import React, { useEffect, useState } from "react";
import ApiRequestsManagerService from "../Services/ApiRequestsManagerService";
import PqrService from "../Services/PqrService";
import '../Styles/CreatepqrForm.css'
import Alert from "./Alert.jsx"

const CreatepqrForm = ({ user }) => {
  const [reasonsPqr, setReasonsPqr] = useState([]);
  const [utilityPqr, setUtilityPqr] = useState([]);
  const [pqrCreated,setPqrCreated]=useState([])
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({});
  const [alertData, setAlertData] = useState({ show: false, error: false, message: "" });
  const [pdfAdjunt,setPafAdjunt]=useState(null);


  const pqrService = new PqrService(new ApiRequestsManagerService());

  useEffect(() => {
    const fetchReasonsAndUtilities = async () => {
      setLoading(true);
      const responseReason = await pqrService.getReasonsPqr();
      responseManager(responseReason, setReasonsPqr);
      const responseUtilities = await pqrService.getUtility();
      responseManager(responseUtilities, setUtilityPqr);
      setLoading(false);
    };

    fetchReasonsAndUtilities();
  }, []);

  const responseManager = (response, setItem,messageSucces=null) => {
    if (response.message === "success") {
      setItem(response.data);
      if(response.alertable==true){
        showSuccess(messageSucces)
      }
    } else if (response.message === "error") {
      showError(response.error||"error desconcido")
    }
  };
  const handleInputPqrsChange = (e) => {
    const { name, value } = e.target;
    setFormData(prevFormData => {
      const hierarchy = [
        "macromotivo_id",
        "general_id",
        "especifico_id",
        "id_tipo",
        "motivo_id"
      ];
  
      const index = hierarchy.indexOf(name);
      const updatedFormData = { ...prevFormData };
  
      updatedFormData[name] = value;
  
      if (index !== -1) {
        for (let i = index + 1; i < hierarchy.length; i++) {
          updatedFormData[hierarchy[i]] = "";
        }
      }
  
      return updatedFormData;
    });
  };

  const renderOptionUtility = (type) => {
    const types = PqrService.filterUtility(utilityPqr, type);
    return types.map((type) => (
      <option key={type.id} value={type.id}>
        {type.nombre}
      </option>
    ));
  };

  const renderOptionReasons = (parentId, level) => {
    const reasons = PqrService.filterReason(parentId, level, reasonsPqr);
    return reasons.map((reason) => (
      <option key={reason.id_motivo} value={reason.id_motivo}>
        {reason.nombre}
      </option>
    ));
  };
  const handleButtonSendPqr = async (e) => {
      e.preventDefault();
      setLoading(true)
      const dataToSend = new FormData();
      Object.entries(formData).forEach(([key, value]) => {
        const excludedKeys = ['id_tipo', 'macromotivo_id', 'general_id', 'especifico_id'];
        if (!excludedKeys.includes(key)) {
          dataToSend.append(key, value);
        }
      });
      if (pdfAdjunt) {
        dataToSend.append("pdfInfo", pdfAdjunt); 
      }

      const response = await pqrService.createnewPqr(dataToSend);
      setLoading(false)
      responseManager(response, setPqrCreated, "Pqr Creado con éxito");
  };
  const showSuccess = (msm) => {
    setAlertData({ show: true, error: false, message:msm  });
   
};
  const showError = (error) => {
      setAlertData({ show: true, error: true, message:error });
  };

  const handlePdfAdjunt=(file)=>{
      setPafAdjunt(file)
  }

  return (
    <div className="pqrs-form-container">
      <div className="header-form-citas">
        <h2 className="title-new-pqr-page">FORMULARIO CREAR PQRS</h2>
      </div>
      <form className="form-create-pqr">
        <div className="contacs-pqr">
          <h2 >Informacion de Usuario</h2>
          {/* Usuario y contacto */}
          <div className="select-pqr">
            <label>Tipo de Usuario</label>
            <select
              name="tipo_usuario"
              value={formData.tipo_usuario || ""}
              onChange={handleInputPqrsChange}
              required
            >
              <option value="">Seleccione el tipo de usuario</option>
                {renderOptionUtility("usuario")}
            </select>
          </div>

          <div className="input-pqr">
            <label>Nombre de Usuario</label>
            <input
              type="text"
              name="nombre_usuario"
              value={formData.nombre_usuario || ""}
              onChange={handleInputPqrsChange}
              placeholder={loading?"cargado...":"Favor ingresa el nombre de el usuario"}
              required
            />
          </div>
          
          <div className="input-pqr">
            <label>Nombre de remitente</label>
            <input
              type="text"
              name="nombre_quien_registra"
              value={formData.nombre_quien_registra || ""}
              onChange={handleInputPqrsChange}
              placeholder={loading?"cargado...":"Favor ingresa el nombre de el usuario remitente"}
              required
            />
          </div>

          <div className="input-pqr">
            <label>Identificación Usuario</label>
            <input
              type="text"
              name="identificacion_usuario"
              value={formData.identificacion_usuario || ""}
              onChange={handleInputPqrsChange}
              placeholder={loading?"cargado...":"Favor ingresa la identificacion del usuario"}
              required
            />
          </div>

          <div className="input-pqr">
            <label>Celular</label>
            <input
              type="text"
              name="celular_usuario"
              value={formData.celular_usuario || ""}
              onChange={handleInputPqrsChange}
              placeholder={loading?"cargado...":"Favor ingresa un numero de contacto de usuario"}
              required
            />
          </div>

          <div className="input-pqr">
            <label>Email</label>
            <input
              type="email"
              name="email_usuario"
              value={formData.email_usuario || ""}
              onChange={handleInputPqrsChange}
              placeholder="Ingrese la dirección de email del usuario"
              required
            />
          </div>
        </div>

        <div className="config-pqr">
          <h2>Informacion pqr</h2>
          {/* Configuración del PQR */}
          <div className="select-pqr">
            <label>Tipo de PQR</label>
            <select
              name="tipo_id"
              value={formData.tipo_id || ""}
              onChange={handleInputPqrsChange}
              required
            >
              <option value="">Seleccione el tipo de PQR</option>
              {renderOptionUtility("tipo")}
            </select>
          </div>

          <div className="select-pqr">
            <label>Medio de PQR Recibido</label>
            <select
              name="canal_id"
              value={formData.canal_id || ""}
              onChange={handleInputPqrsChange}
              required
            >
              <option value="">Seleccione el medio de PQR</option>
              {renderOptionUtility("canal")}
            </select>
          </div>

          <div className="select-pqr">
            <label>Características SOGCS</label>
            <select
              name="sogcs_id"
              value={formData.sogcs_id || ""}
              onChange={handleInputPqrsChange}
              required
            >
              <option value="">Seleccione Característica SOGCS</option>
              {renderOptionUtility("caracteristica")}
            </select>
          </div>

          <div className="select-pqr">
            <label>Sede</label>
            <select
              name="sede_id"
              value={formData.sede_id || ""}
              onChange={handleInputPqrsChange}
              required
            >
              <option value="">Seleccione la sede</option>
              {renderOptionUtility("sede")}
            </select>
          </div>
          <div className="select-pqr">
            <label>Area</label>
            <select
              name="area_id"
              value={formData.area_id || ""}
              onChange={handleInputPqrsChange}
              required
            >
              <option value="">Seleccione la area</option>
              {renderOptionUtility("area")}
            </select>
          </div>
        </div>

        <div className="reasons_pqr">
          <h2>Motivo Pqr</h2>
          {/* Razones PQR */}
          <div className="select-pqr">
            <label>Macromotivo</label>
            <select
              name="macromotivo_id"
              value={formData.macromotivo_id || ""}
              onChange={handleInputPqrsChange}
              required
            >
              <option value="">Seleccione macromotivo</option>
              {renderOptionReasons(null, "macromotivo")}
            </select>
          </div>

          <div className="select-pqr">
            {PqrService.ReasonPqrsSelectAvaible(formData, "general") && (
              <>
                <label>Motivo General</label>
                <select
                  name="general_id"
                  value={formData.general_id || ""}
                  onChange={handleInputPqrsChange}
                  required
                >
                  <option value="">Seleccione motivo general</option>
                  {renderOptionReasons(formData.macromotivo_id, "general")}
                </select>
              </>
            )}
          </div>

          <div className="select-pqr">
            {PqrService.ReasonPqrsSelectAvaible(formData, "especifico") && (
              <>
                <label>Motivo Específico</label>
                <select
                  name="especifico_id"
                  value={formData.especifico_id || ""}
                  onChange={handleInputPqrsChange}
                  required
                >
                  <option value="">Seleccione motivo específico</option>
                  {renderOptionReasons(formData.general_id, "especifico")}
                </select>
              </>
            )}
          </div>

          <div className="select-pqr">
            {PqrService.ReasonPqrsSelectAvaible(formData, "tipo") && (
              <>
                <label>Tipo de Motivo</label>
                <select
                  name="id_tipo"
                  value={formData.id_tipo || ""}
                  onChange={handleInputPqrsChange}
                  required
                >
                  <option value="">Seleccione tipo de motivo</option>
                  {renderOptionReasons(formData.especifico_id, "tipo")}
                </select>
              </>
            )}
          </div>

          <div className="select-pqr">
            {PqrService.ReasonPqrsSelectAvaible(formData, "causa") && (
              <>
                <label>Causa de Motivo</label>
                <select
                  name="motivo_id"
                  value={formData.motivo_id || ""}
                  onChange={handleInputPqrsChange}
                  required
                >
                  <option value="">Seleccione causa de motivo</option>
                  {renderOptionReasons(formData.id_tipo, "causa")}
                </select>
              </>
            )}
          </div>
        </div>
        <div className="descripcion-pqrs">
          <div className="input-pqr">
            <label>Descripción</label>
            <textarea
              name="descripcion"
              value={formData.descripcion || ""}
              onChange={handleInputPqrsChange}
              placeholder={loading?"cargado...":"Ingresa la Informacion detallada del PQR"}
              rows={10}
              required
            />
          </div>
        </div>
        <div className="reasons_pqr">
            <div className='input-form-answer-pqrs'>
              <label>Adjuntar pdf  'opcional'</label>
              <input
                type="file"
                onChange={e => handlePdfAdjunt( e.target.files[0])}
              />
            </div>
        </div>


        <div className="send-citas">
            <button  className='button-send-pqr' onClick={(e) => handleButtonSendPqr(e)}>{loading?"CARGANDO...":"CREAR PQR"}</button>
        </div>
      </form>
      {alertData.show && (
        <div>
            <Alert 
                error={alertData.error} 
                message={alertData.message} 
                onClose={() => setAlertData({ ...alertData, show: false })}
            />
        </div>
      )}

    
    </div>
  );
};

export default CreatepqrForm;
