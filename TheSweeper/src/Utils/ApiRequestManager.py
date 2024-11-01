import requests
import time
from settings.ServiceWhatsappSettings import URL_SERVICE_WHATSAPP
from src.Utils.JwtGenerator import JwtGenerator

class ApiRequestManager:
    URL = URL_SERVICE_WHATSAPP

    def __init__(self):
        self.a = 0.3
        self.max_retries = 4
        
    
    def postMethod(self, body: dict, endpoint: str, cont=0) -> int:
        url: str = f"{self.URL}{endpoint}"
        token:str=self.__get_token(body)
        headers = {'Authorization': token}
        response = requests.post(url=url, json=body, headers=headers)
        print(response.json(),response.status_code)
        if response.status_code == 500 and cont < self.max_retries:
            cont += 1
            time.sleep(self.a**2)
            return self.postMethod(body=body, endpoint=endpoint, cont=cont)
        
        if cont >= self.max_retries:
            raise Exception("Connection with WhatsApp service failed after multiple retries")

        data = response.status_code
        return data
    def __get_token(self,payload:dict)->str:
        id:int =payload.get('id')
        jwt_generator=JwtGenerator(id=id)
        jwt=jwt_generator.jwt()
        del jwt_generator
        return 'Bearer '+jwt
    
