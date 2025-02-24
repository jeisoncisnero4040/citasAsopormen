from datetime import datetime
from exception.InternalServerErrorException import InternalServerErrorException

class DateManager:
    DAYS = {
        0: 'Lunes',
        1: 'Martes',
        2: 'Miércoles',
        3: 'Jueves',
        4: 'Viernes',
        5: 'Sábado',
        6: 'Domingo'
    }

    @staticmethod
    def GetDayNameFromDate(date_str):
        try:

            date_obj =  datetime.strptime(date_str, "%Y-%m-%dT%H:%M:%S%z").date()
            day_week = date_obj.weekday() 
            return DateManager.DAYS.get(day_week, "Día inválido") 

        except Exception as e:
            raise InternalServerErrorException(error=str(e))
    @staticmethod
    def GetFullDateInText(date_str):
        """
        Retorna la fecha en el formato: 'DD/MM/YYYY HH:MM AM/PM'
        """
        try:
            # Convertir string a datetime
            date_obj = datetime.strptime(date_str, "%Y-%m-%dT%H:%M:%S%z").date()


            # Formatear la fecha en 'DD/MM/YYYY HH:MM AM/PM'
            formatted_date = date_obj.strftime("%d/%m/%Y %I:%M %p")

            return formatted_date

        except Exception as e:
            raise InternalServerErrorException(error=str(e))





