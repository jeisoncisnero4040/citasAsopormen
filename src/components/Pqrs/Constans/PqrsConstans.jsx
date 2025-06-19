export class PqrsConstants {
  static datasetFields = [
    "QUEJA",
    "PETICION",
    "RECLAMO",
    "SUGERENCIA",
    "FELICITACION",
    "SOLICITUD",
    "SOLICITUD_DE_CITAS"
  ];

  static labelMap = {
    QUEJA: "Quejas",
    PETICION: "Peticiones",
    RECLAMO: "Reclamos",
    SUGERENCIA: "Sugerencias",
    FELICITACION: "Felicitaciones",
    SOLICITUD: "Solicitudes",
    SOLICITUD_DE_CITAS: "Citas"
  };

  static datasetFieldsAreas = [
    "ADMINISTRATIVO_ABA",
    "CENTRAL_DE_CITAS",
    "DIRECCION_SUBDIRECCION",
    "ADMINISTRATIVO_EDUCACION",
    "EXPERIENCIA_CLIENTE",
    "FACTURACION",
    "SEGURIDAD",
    "LOGISTICA",
    "ADMINISTRATIVO_OTRO",
    "ADMISIONISTA",
    "ADMINISTRATIVO_PROYECCION",
    "ADMINISTRATIVO_REHABILITACION",
    "RESPUESTA_TELEFONICA",
    "ASISTENCIAL_ABA",
    "ASISTENCIAL_EDUCACION",
    "ASISTENCIAL_PROYECCION",
    "ASISTENCIAL_REHABILITACION",
    "SOLICITUD"
  ];

  static labelMapAreas = {
    ADMINISTRATIVO_ABA: "Adm. ABA",
    CENTRAL_DE_CITAS: "Central de Citas",
    DIRECCION_SUBDIRECCION: "Dirección/Subdirección",
    ADMINISTRATIVO_EDUCACION: "Adm. Educación",
    EXPERIENCIA_CLIENTE: "Experiencia Cliente",
    FACTURACION: "Facturación",
    SEGURIDAD: "Seguridad",
    LOGISTICA: "Logística",
    ADMINISTRATIVO_OTRO: "Adm. Otro",
    ADMISIONISTA: "Admisionista",
    ADMINISTRATIVO_PROYECCION: "Adm. Proyección",
    ADMINISTRATIVO_REHABILITACION: "Adm. Rehabilitación",
    RESPUESTA_TELEFONICA: "Respuesta Telefónica",
    ASISTENCIAL_ABA: "Asist. ABA",
    ASISTENCIAL_EDUCACION: "Asist. Educación",
    ASISTENCIAL_PROYECCION: "Asist. Proyección",
    ASISTENCIAL_REHABILITACION: "Asist. Rehabilitación",
    SOLICITUD: "Solicitud"
  };

  static palette = [
    "#005082", "#4380ad", "#3a97ff", "#8bb7ff",
    "#d4f0ff", "#FAE105", "#ECDB00", "#FFF69D"
  ];
    static datasetFieldServices = [
    "SEDE_CENTRAL",
    "COLEGIO_CLL_42",
    "SEDE_BOLARQUI",
    "SEDE_SAN_GIL"
    ];

    static labelMapServices = {
    SEDE_CENTRAL: "Sede Central",
    COLEGIO_CLL_42: "Colegio Cll 42",
    SEDE_BOLARQUI: "Sede Bolarqui",
    SEDE_SAN_GIL: "Sede San Gil"
    };

    static datasetFieldsCharacteristics = [
    "ACCESIBILIDAD",
    "CONTINUIDAD",
    "OPORTUNIDAD",
    "PERTINENCIA",
    "SATISFACCION",
    "SEGURIDAD",
    "SOLICITUD_INFO_DOCUMENTO"
  ];
  static labelMapCharacteristics = {
    ACCESIBILIDAD: "Accesibilidad",
    CONTINUIDAD: "Continuidad",
    OPORTUNIDAD: "Oportunidad",
    PERTINENCIA: "Pertinencia",
    SATISFACCION: "Satisfacción",
    SEGURIDAD: "Seguridad",
    SOLICITUD_INFO_DOCUMENTO: "Solicitud de Información o Documento"
  };

  static get colorMap() {
    return this.datasetFields.reduce((acc, field, index) => {
      acc[field] = this.palette[index % this.palette.length];
      return acc;
    }, {});
  }
}
