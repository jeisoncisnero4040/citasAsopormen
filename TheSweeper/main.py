from src.Utils.DatabaseConnection import DatabaseConnection
from src.Services.CitasService import CitasService
from src.Services.WhatsappService import WhatsappService
from src.Utils.ApiRequestManager import ApiRequestManager


if __name__=='__main__':
    conexion=DatabaseConnection()
    request_Manager=ApiRequestManager()
    whatsappService=WhatsappService(request_manager=request_Manager)
    citas_service=CitasService(db_connection=conexion,whatsapp_service=whatsappService)
    citas_service.send_message_confirmation()
    del citas_service