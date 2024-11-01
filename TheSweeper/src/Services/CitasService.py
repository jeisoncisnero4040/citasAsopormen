from src.Utils.DatabaseConnection import DatabaseConnection
from src.Utils.CitasMapper import CitasMapper
from src.Services.WhatsappService import WhatsappService
from settings.AppSettings import TIMEZONE
import datetime
import pytz
import time

class CitasService:
    
    def __init__(self, db_connection: DatabaseConnection, whatsapp_service: WhatsappService):
        self.db_connection = db_connection 
        self.whatsapp_service = whatsapp_service
        self.max_retries = 3
        self.a = 0.3
    
    def send_message_confirmation(self):
        if not self.db_connection.connection():
            self.whatsapp_service.sendFailedConnection("Error al conectar a la base de datos")
            return 
        self.timezone = pytz.timezone(TIMEZONE)
        self.now = datetime.datetime.now(self.timezone)
        
        citas_unmapped = self.__get_citas_sendeables()
        citas_mapped = self.__map_citas(citas_unmapped)
        citas_grouped_by_sessions=self.__group_citas(citas_mapped)
        print(citas_grouped_by_sessions)
        telephones_notified:set=set()
        for cita in citas_grouped_by_sessions:
            telephone:str=cita.get('telephone_number')
            if telephone not in telephones_notified:
                telephones_notified.add(telephone) 
                print(cita)
                self.__send_message_confirmation(cita=cita)
                time.sleep(5)
        else:
            telephones_notified.clear()
        
        #if self.now.hour >= 17:
            #self.__update_citas_for_next_day()

    def __get_citas_sendeables(self):
        timezone = pytz.timezone(TIMEZONE)
        start = datetime.datetime.now(timezone) + datetime.timedelta(days=1)
        start = start.replace(hour=0, minute=0, second=0, microsecond=0)
        end = start + datetime.timedelta(days=2)
        query = """
            SELECT 
                ci.id,
                ci.fecha,
                ci.hora,
                ci.procedim,
                ci.direccion_cita AS direction,
                ci.regobserva AS observations,
                cli.nombre AS client,
                cli.cel AS telephone_number,
                em.enombre as profesional,
                pro.duraccion as duracion
            FROM 
                citas ci
            INNER JOIN 
                cliente cli ON cli.codigo LIKE '%' + ci.nro_hist + '%'
            INNER JOIN 
                procedipro pro ON pro.nombre = ci.procedipro
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

        try:
            connection = self.db_connection.connection()   
            with connection:
                cursor = connection.cursor()
                cursor.execute(query, (start, end))   
                citas_sendeables = cursor.fetchall()

                if not citas_sendeables:
                    
                    return []
                 
                column_names = [column[0] for column in cursor.description]
                citas_sendeables_mapped = [dict(zip(column_names, cita)) for cita in citas_sendeables]

                return citas_sendeables_mapped
        except Exception as e:
            self.whatsapp_service.sendFailedConnection(f"Error al acceder la base de datos : {e}")
            return []

    def __map_citas(self, unmapped_citas: list) -> list:
        return  CitasMapper.map(unmapped_citas)
    
    def __group_citas(self,ungropued_citas:list)->list:
        return CitasMapper.groupCitasBySession(ungropued_citas)

    
    def __send_message_confirmation(self, cita: dict):
        response = self.whatsapp_service.sendMessageConfirmation(cita)
        if response == 200:
            ids:str= cita.get('session_ids')
            self.__mark_cita_as_wsp_sending_today(ids=ids)
            
    def __mark_cita_as_wsp_sending_today(self, ids: str, count=0):
        ids_concat = ids.replace('|||', ', ') 
        query = f"UPDATE citas SET wsp_enviado_hoy = '1' WHERE id IN ({ids_concat})"
        try:
            connection = self.db_connection.connection()
            with connection:
                cursor = connection.cursor()
                cursor.execute(query) 
                connection.commit()
        except Exception as e:
            if count >= self.max_retries:
                # Llamar a fallback o enviar una notificación de fallo

                return
            time.sleep(self.a * (count + 1))
            self.__mark_cita_as_wsp_sending_today(id=id, count=count+1)

    def __update_citas_for_next_day(self,count=0):
        query = """UPDATE citas SET wsp_enviado_hoy = '0' WHERE wsp_enviado_hoy = '1'"""
        try:
            connection = self.db_connection.connection()
            with connection:
                cursor = connection.cursor()
                cursor.execute(query)   
                connection.commit()
        except Exception as e:

            if count >= self.max_retries:
                return
            time.sleep(self.a * (count + 1))
            self.__mark_cita_as_wsp_sending_today(id=id, count=count+1)
    

