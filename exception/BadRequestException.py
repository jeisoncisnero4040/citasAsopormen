
class BadRequestException(Exception):
    def __init__(self,status=400,error="bad request"):
        self.status=status
        self.error=error

    def getMessage(self):
        return (self.error)

    def getStatus(self):
        return self.status
    