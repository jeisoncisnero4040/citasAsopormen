
class InternalServerErrorException(Exception):
    def __init__(self,status=500,error="error desconocido"):
        self.status=status
        self.error=error

    def getMessage(self):
        return (self.error)

    def getStatus(self):
        return self.status
    