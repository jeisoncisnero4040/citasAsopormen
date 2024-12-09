from src.Utils.DatabaseConnection import DatabaseConnection
from src.Services.CitasService import CitasService
from src.Services.WhatsappService import WhatsappService
from src.Utils.ApiRequestManager import ApiRequestManager
from src.Utils.sweep_logs import SweepLogs
from src.Services.EmailService import EmailService
from settings.AppSettings import TIMEZONE
import os
import datetime
import pytz


class Sweeper:
    def __init__(self):
         
        self.conexion = DatabaseConnection()
        self.request_manager = ApiRequestManager()
        self.whatsapp_service = WhatsappService(request_manager=self.request_manager)
        self.email_service = EmailService()
        self.citas_service = CitasService(
            db_connection=self.conexion, whatsapp_service=self.whatsapp_service,email_service=self.email_service
        )
        self.timezone = pytz.timezone(TIMEZONE)
        

    def sweep(self):
        if self.__isFirstSweep():
            self.__make_and_send_informe_dayli()

        InfoSweep=self.citas_service.send_message_confirmation()
        SweepLogs.save_log_work(InfoSweep) if InfoSweep
        

    def __isFirstSweep(self) -> bool:
        now = datetime.datetime.now(tz=self.timezone)
        return now.hour < 6

    def __delete_files(self):

        files_to_delete = ["INFORME_CITAS_CANCELADAS.xlsx", "analisis.html"]
        for file in files_to_delete:
            if os.path.exists(file):
                os.remove(file)
    def __make_and_send_informe_dayli(self):
        self.citas_service.create_informe_citas_notified()
        self.email_service.send_correo()
        self.citas_service.update_citas_for_next_day()
        self.__delete_files()
        SweepLogs.save_log_work()
