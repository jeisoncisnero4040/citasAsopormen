from settings.DataBaseSetting import DATABASE_CONNECTION_STRING
 

import pyodbc
import time

class DatabaseConnection:
    def __init__(self):
        
        self.max_retries = 3
        self.a = 0.3

    def connection(self, cont=0):
        try:
            connection = pyodbc.connect(DATABASE_CONNECTION_STRING)
             
            return connection
        except Exception as e:
            if cont < self.max_retries:
                time.sleep(self.a**2)
                return self.connection(cont=cont + 1)
            else:
                None
                


