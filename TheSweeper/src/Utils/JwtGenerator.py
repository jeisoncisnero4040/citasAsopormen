import jwt
import datetime
import pytz
from settings.AppSettings import SECRET_KEY, TIMEZONE, JWT_TTL

class JwtGenerator:
    def __init__(self, id: int):
        self.id = id
        self.time_zone = pytz.timezone(TIMEZONE)

    def jwt(self):
        now = datetime.datetime.now(self.time_zone)
        payload = {
            "sub": self.id,
            "iat": now,  
            "exp": now + datetime.timedelta(minutes=int(JWT_TTL)),  
            "admin": True,  
        }
        return jwt.encode(payload=payload, key=SECRET_KEY, algorithm="HS256")
