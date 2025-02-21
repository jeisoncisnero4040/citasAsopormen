from marshmallow import Schema, fields
from rules.CitasSchema import CitaSchema
from rules.ObservacionesSchema import ObservacionesSchema

class CitasValidator(Schema):
    cliente = fields.String(required=True, error_messages={'error': 'El cliente es obligatorio'})
    citas = fields.List(fields.Nested(CitaSchema), required=True, validate=lambda x: len(x) > 0, error_messages={'error': 'Debe haber al menos una cita'})
    observaciones=fields.List(fields.Nested(ObservacionesSchema),required=False)