import os
from dotenv import load_dotenv


load_dotenv()


FOLDER_ID = os.getenv('FOLDER_ID')
APP_SECRET_KEY= os.getenv('APP_SECRET')
IMAGE_ASOPORMEN_URL=os.getenv('LOGO_ASOPORMEN')

