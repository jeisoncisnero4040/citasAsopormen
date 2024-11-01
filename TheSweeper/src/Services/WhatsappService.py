from settings.ServiceWhatsappSettings import WHATSAPP_ADMIND_NUMBER
import datetime
from src.Utils.ApiRequestManager import ApiRequestManager

class WhatsappService:
    def __init__(self,request_manager:ApiRequestManager) -> None:
        self.requests_manager=request_manager


    def sendFailedConnection(self, error: Exception):
        body = self.__createPayload(error)
        self.requests_manager.postMethod(body=body, endpoint='whatsapp/failed')
    
    def sendMessageConfirmation(self, dataMessage:dict):
        return self.requests_manager.postMethod(body=dataMessage,endpoint='whatsapp/start_chat')

    def __createPayload(self, error: Exception) -> dict:
        return {
            'telephone_number': WHATSAPP_ADMIND_NUMBER,
            'origin': str(error),
            'date': datetime.datetime.now().strftime('%Y-%m-%d %H:%M')
        }
