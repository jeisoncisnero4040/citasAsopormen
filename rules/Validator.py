from marshmallow import ValidationError
from exception.BadRequestException import BadRequestException
from rules.CitasValidator import CitasValidator
from rules.CitasSchema import CitaSchema

class Validator:
    @staticmethod
    def validate_citas_to_pdf(data):
        schema = CitasValidator()
        try:
            return schema.load(data)
        except ValidationError as err:
            raise BadRequestException(error=err.messages)

    @staticmethod
    def validate_citas_content(citas):
        schema = CitaSchema(many=True)
        try:
            return schema.load(citas)
        except ValidationError as err:
            raise BadRequestException(error=err.messages)
