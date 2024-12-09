import datetime
import time
import pytz
import pandas as pd
from src.Utils.DatabaseConnection import DatabaseConnection
from src.Utils.CitasMapper import CitasMapper
from src.Services.WhatsappService import WhatsappService
from ..Utils.Plotter import Plotter
from settings.AppSettings import TIMEZONE
from src.Services.EmailService import EmailService


class CitasService:
    def __init__(self, db_connection: DatabaseConnection, whatsapp_service: WhatsappService,email_service=EmailService):
        """
        Servicio para manejar operaciones relacionadas con citas, notificaciones y análisis.
        """

        self.db_connection = db_connection
        self.whatsapp_service = whatsapp_service
        self.email_service=email_service
        self.max_retries: int = 3
        self.retry_delay: float = 0.3
        self.timezone = pytz.timezone(TIMEZONE)

    def send_message_confirmation(self) -> bool:
        """
        Envía confirmaciones de citas a través de WhatsApp y marca las citas como notificadas. Si hay un error, envía una notificación por correo.
        """
        start_sweep=time.asctime()
        if not self.db_connection.connection():
            self.whatsapp_service.sendFailedConnection("Error al conectar a la base de datos")
            return False

        citas_unmapped = self.__get_citas_sendeables()
        citas_mapped = self.__map_citas(citas_unmapped)
        citas_grouped = self.__group_citas(citas_mapped)
        citas_grouped_with_observations=self.__getObservationCita(citas_grouped)
        

        ids_notified=''
        telephones_notified = set()
        has_error = False
        for cita in citas_grouped_with_observations:
            telephone = cita.get('telephone_number')
            if telephone not in telephones_notified:
                telephones_notified.add(telephone)
                """if  self.__send_message_confirmation(cita=cita):
                    session_ids = cita.get('session_ids')
                    ids_notified+='|||'+session_ids
                else:
                    has_error = True"""
                
            time.sleep(2)
        else:
            num_Citas_notified=len(telephones_notified)
            telephones_notified.clear()  
            end_sweep=time.asctime()
            #self.__mark_cita_as_wsp_sending_today(ids_notified)

        if has_error:
            self.__sendErrorByEmail()

        return {
                "hora_finalizacion":  datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                "num_citas_notificadas": num_Citas_notified,
                "tiempo_duracion": f"Duracion del barrido : {end_sweep-start_sweep}"}


    def create_informe_citas_notified(self):
        """
        Genera un informe en Excel de las citas notificadas y crea gráficos relacionados.
        """
        if not self.db_connection.connection():
            self.whatsapp_service.sendFailedConnection("Error al conectar a la base de datos")
            return

        citas_notified = self.__get_citas_notified()
        mapped_citas = CitasMapper.map(citas_notified)
        df = pd.DataFrame(mapped_citas)

        df.to_excel("INFORME_CITAS_CANCELADAS.xlsx", index=False)
        Plotter.generatePlotCitasNotified(df)

    def update_citas_for_next_day(self, count: int = 0):
        """
        Resetea el estado de notificación de citas para el día siguiente.
        """
        query = "UPDATE citas SET wsp_enviado_hoy = '0' WHERE wsp_enviado_hoy = '1'"
        self.__execute_query(query, count)

    def __get_citas_sendeables(self) -> list:
        """
        Obtiene las citas enviables a través de WhatsApp.
        """
        start, end = self.__get_date_range()
        query = """
            SELECT 
                ci.id,
                ci.fecha,
                ci.hora,
                ci.procedim,
                ci.direccion_cita AS direction,
                ci.regobserva AS observation_id,
                cli.nombre AS client,
                ci.copago,
                cli.cel AS telephone_number,
                em.enombre as profesional,
                pro.duraccion as duracion
            FROM 
                citas ci
            INNER JOIN cliente cli ON cli.codigo LIKE '%' + ci.nro_hist + '%'
            INNER JOIN procedipro pro ON pro.nombre = ci.procedipro
            INNER JOIN emplea em ON em.ecc = ci.cedprof
            WHERE 
                ci.recordatorio_wsp = '1'
                AND ci.confirma != '1'
                AND ci.cancelada != '1'
                AND ci.na != '1'
                AND ci.asistio != '1'
                AND ci.wsp_enviado_hoy != '1'
                AND ci.fecha BETWEEN 
                    CONVERT(smalldatetime, ?, 120) 
                    AND CONVERT(smalldatetime, ?, 120)
            ORDER BY 
                ci.hora ASC;
        """
        return self.__fetch_query_results(query, (start, end))

    def __map_citas(self, unmapped_citas: list) -> list:
        return CitasMapper.map(unmapped_citas)

    def __group_citas(self, ungropued_citas: list) -> list:
        return CitasMapper.groupCitasBySession(ungropued_citas)

    def __send_message_confirmation(self, cita: dict) -> bool:
        """
        Envía una confirmación de cita por WhatsApp y actualiza su estado en la base de datos.
        """
        response = self.whatsapp_service.sendMessageConfirmation(cita)
        return response==200

    def __mark_cita_as_wsp_sending_today(self, ids: str, count: int = 0):
        """
        Marca las citas como notificadas hoy en la base de datos.
        """
        ids_concat = ids.replace('|||', ', ')
        if(ids_concat.strip()):
            query = f"UPDATE citas SET wsp_enviado_hoy = '1' WHERE id IN ({ids_concat})"
            self.__execute_query(query, count) 

    def __get_citas_notified(self) -> list:
        """
        Obtiene las citas que han sido notificadas.
        """
        query = """
            SELECT 
                ci.id,
                ci.fecha,
                ci.hora,
                ci.autoriz,
                ci.procedipro,
                ci.cancelada,
                ci.confirma,
                cli.nombre AS paciente,
                cli.cel AS telephone_number,
                em.enombre as profesional,
                se.nombre AS sede,
                pro.duraccion as duracion
            FROM 
                citas ci
            INNER JOIN cliente cli ON cli.codigo LIKE '%' + ci.nro_hist + '%'
            INNER JOIN sede se ON se.cod = ci.sede
            INNER JOIN emplea em ON em.ecc = ci.cedprof
            INNER JOIN procedipro pro ON pro.nombre = ci.procedipro
            WHERE 
                ci.wsp_enviado_hoy = '1'
                AND ci.fecha > GETDATE()
                AND ci.autoriz != ''
        """
        return self.__fetch_query_results(query)

    def __fetch_query_results(self, query: str, params: tuple = ()) -> list:
        """
        Ejecuta una consulta SQL y retorna los resultados.
        """
        try:
            connection = self.db_connection.connection()
            with connection:
                cursor = connection.cursor()
                cursor.execute(query, params)
                results = cursor.fetchall()

                if not results:
                    return []

                column_names = [column[0] for column in cursor.description]
                return [dict(zip(column_names, row)) for row in results]
        except Exception as e:
            self.whatsapp_service.sendFailedConnection(e)
            return []

    def __execute_query(self, query: str, count: int = 0):
        """
        Ejecuta una consulta SQL de escritura con manejo de reintentos.
        """
        try:
            connection = self.db_connection.connection()
            with connection:
                cursor = connection.cursor()
                cursor.execute(query) 
                connection.commit()
        except Exception as e:
            if count < self.max_retries:
                time.sleep(self.retry_delay * (count + 1))
                self.__execute_query(query, count + 1)
            else:
                self.whatsapp_service.sendFailedConnection(e)

    def __get_date_range(self):
        """
        Calcula el rango de fechas para las consultas de citas.
        """
        start = datetime.datetime.now(self.timezone) + datetime.timedelta(days=1)
        start = start.replace(hour=0, minute=0, second=0, microsecond=0)
        end = start + datetime.timedelta(days=2)
        return start, end
    def __sendErrorByEmail(self):
        return self.email_service.sendEmailFailedWhatsapSystemFailed()
    
    def __getObservationCita(self, citas_gruped):
        observaCitasIds: set = set()
        observaCitasIds.update(int(cita['observation_id']) for cita in citas_gruped)
        observationAndContent=self.__getAllObservation(observaCitasIds)
        for cita in citas_gruped:
            copago:str=cita.get('copago')if cita.get('copago') else 'No aplica'
            observationId:int=int(cita['observation_id'])
            observationTemplate:str=observationAndContent.get(observationId)
            observations=observationTemplate.replace("{{}}",copago)
            cita['observations']=observations

            del cita['observation_id']
            del cita['copago']
        return citas_gruped
        
    def __getAllObservation(self, ids):
        placeholders = ",".join(["?"] * len(ids)) 
        query = f"""
            SELECT oc.id, oc.contenido 
            FROM observa_citas oc 
            WHERE oc.id IN ({placeholders})
        """
        try:
            connection = self.db_connection.connection()
            with connection:
                cursor = connection.cursor()
                cursor.execute(query, tuple(ids)  )
                results = cursor.fetchall()

                if not results:
                    return {}

                
                return {row[0]: row[1] for row in results}  

        except Exception as e:
            print(e)
            return {}


        

