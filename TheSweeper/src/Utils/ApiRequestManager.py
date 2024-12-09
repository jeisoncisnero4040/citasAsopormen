import requests
import time
from settings.ServiceWhatsappSettings import URL_SERVICE_WHATSAPP, WHATSAPP_ADMIND_NUMBER
from src.Utils.JwtGenerator import JwtGenerator
import datetime


class ApiRequestManager:
    URL = URL_SERVICE_WHATSAPP

    def __init__(self):
        self.retry_delay = 0.3  
        self.max_retries = 4   

    def post_method(self, body: dict, endpoint: str) -> int:
        url: str = f"{self.URL}{endpoint}"
        token: str = self.__get_token(body)
        headers = {'Authorization': token}

        
        for attempt in range(1, self.max_retries + 1):
            try:
                response = requests.post(url=url, json=body, headers=headers)
                print(f"Attempt {attempt}: Response {response.json()}, Status Code: {response.status_code}")
                
                if response.status_code == 500:
                    if attempt == self.max_retries:
                        raise Exception("Max retries reached for endpoint")
                    time.sleep(self.retry_delay * attempt)  
                    continue  
                return response.status_code
            except requests.exceptions.RequestException as e:
                
                if attempt == self.max_retries:
                    raise Exception("Failed to connect after multiple retries") from e

    def __get_token(self, payload: dict) -> str:
       
        jwt_generator = JwtGenerator(id=payload.get('id'))
        return 'Bearer ' + jwt_generator.jwt() 
