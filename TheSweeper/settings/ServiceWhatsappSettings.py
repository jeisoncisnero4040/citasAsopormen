import os
from dotenv import load_dotenv

 

load_dotenv()

URL_SERVICE_WHATSAPP=os.getenv("WHATSAPP_SERVICE_URL")
WHATSAPP_ADMIND_NUMBER=os.getenv("WHATSAPP_ADMIND_NUMBER")