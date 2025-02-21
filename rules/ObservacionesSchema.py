from marshmallow import Schema, fields

class ObservacionesSchema(Schema):
    terapia=fields.String(required=False,allow_none=True)
    valoracion=fields.String(required=False,allow_none=True)