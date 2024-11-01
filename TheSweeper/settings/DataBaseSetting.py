import os
from dotenv import load_dotenv

 

load_dotenv()
DATABASE_ENGINE = os.getenv("DATABASE_ENGINE")
DATABASE_URL = os.getenv("DATABASE_URL")
DATABASE_USER = os.getenv("DATABASE_USER")
DATABASE_PORT = os.getenv("DATABASE_PORT")
DATABASE_PASSWORD = os.getenv("DATABASE_PASSWORD")
DATABASE_NAME = os.getenv("DATABASE_NAME")


DATABASE_CONNECTION_STRING = (
    f'DRIVER={{ODBC Driver 17 for SQL Server}};'
    f'SERVER={DATABASE_URL};'
    f'DATABASE={DATABASE_NAME};'
    f'UID={DATABASE_USER};'
    f'PWD={DATABASE_PASSWORD}'
)