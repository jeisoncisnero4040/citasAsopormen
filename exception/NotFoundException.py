from .BadRequestException import BadRequestException 

class NoFoundException(BadRequestException):
    def __init__(self,status=404,error="recurso no encontrado"):
        self.status=status
        self.error=error

    def getMessage(self):
        return (self.error)

    def getStatus(self):
        return self.status
    