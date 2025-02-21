from BadRequestException import BadRequestException

class UnautorizateErrorException(BadRequestException):
    def __init__(self,status=403,error="No autorizado para esta accion"):
        super().__init__(status,error)