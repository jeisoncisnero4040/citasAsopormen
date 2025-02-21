from marshmallow import Schema, fields, ValidationError


class CitaSchema(Schema):
    start = fields.String(required=True, error_messages={'error': 'Las citas deben tener fecha'})
    profesional = fields.String(required=True, error_messages={'error': 'Las citas deben tener un profesional asignado'})
    duracion=fields.String(required=True, error_messages={'error': 'Debe especificar la duracion de la cita'})
    procedimiento=fields.String(required=True, error_messages={'error': 'Debe especificar el procedimiento'})
    direcion=fields.String(required=True, error_messages={'error': 'Debe especificar la direcion'})
    copago=fields.String(required=False, allow_none=True,error_messages={'error': 'error al cargar copago'})
    observaciones=fields.String(required=False,allow_none=True, error_messages={'error': 'error al cargar observaciones'})