import React, { useState,useEffect } from 'react';
import '../Styles/AnswerPqrsForm.css';
import PqrService from '../Services/PqrService';
import ApiRequestsManagerService from '../Services/ApiRequestsManagerService';
import { useParams } from 'react-router-dom';
import Alert from "./Alert.jsx"
import PqrsModal from './PqrsModal';
import { FaEye, FaEyeSlash } from 'react-icons/fa';
import PqrsUnavailable from './PqrsUnavailable.jsx';

const AnswerPqrsForm = () => {
  const [implementedActions, setImplementedActions] = useState([{ description: '', responsible: '', evidence: null }]);
  const [pqrsResponse, setPqrsResponse] = useState({});
  const [alertData, setAlertData] = useState({ show: false, error: false, message: "" });
  const [loading, setLoading]=useState(false);
  const [pqrs, setPqrs] = useState({});
  const [displayPqrs,setDisplayPqrs]=useState(false);
  const [pqrsUnAvaible,setPqrsUnAvaible]=useState(false);
  const pqrsService=new PqrService(new ApiRequestsManagerService())
  const { 'id-encoded': idEncoded } = useParams();

  
  useEffect(() => {
    fetchPqrs(idEncoded);
  }, [idEncoded]);

  const fetchPqrs = async (idEncoded) => {
    const response = await pqrsService.getPqrsByIdEncoded(idEncoded);
    if (response.message === "success") {
      setPqrs(response.data);
    } else if (response.message === "error") {
      setPqrsUnAvaible(true)

    }
  };


  const handleActionChange = (index, field, value) => {
    const updatedActions = [...implementedActions];
    updatedActions[index][field] = value;
    setImplementedActions(updatedActions);
  };

  const isActionComplete = (action) =>
    action.description.trim() !== '' &&
    action.responsible.trim() !== '' ;

  const addNewAction = () => {
    setImplementedActions([
      ...implementedActions,
      { description: '', responsible: '', evidence: null }
    ]);
  };

  const removeLastAction = () => {
    if (implementedActions.length > 1) {
      setImplementedActions(implementedActions.slice(0, -1));
    }
  };

  const handleFormChange = (e) => {
    const { name, value } = e.target;
    const updatedResponse = {
      ...pqrsResponse,
      [name]: value
    };
    setPqrsResponse(updatedResponse);
  };

  const handleSubmit =async (e) => {
    setLoading(true);
    e.preventDefault();

    const formData = new FormData();
  
    formData.append('usuario_respuesta_area', pqrsResponse.usuario || '');
    formData.append('respuesta', pqrsResponse.respuesta || '');
    formData.append('causas', pqrsResponse.causas || '');
    formData.append('id',idEncoded);
    
    implementedActions.forEach((action, index) => {
      formData.append(`actions[${index}][descripcion]`, action.description);
      formData.append(`actions[${index}][persona_responsable]`, action.responsible);
      formData.append(`actions[${index}][evidence]`, action.evidence); 
    });
    const response = await pqrsService.sendPqrsAnswerArea(formData);
    responseManager(response,null,"Respuesta de pqrs guardada correctamente");
    setLoading(false)
    await sleep(3000);
    setPqrsUnAvaible(true)


  };
  const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));
  const responseManager = (response, setItem,messageSucces=null) => {
    if (response.message === "success") {
      if(setItem !== null){
        setItem(response.data);
      }
      if(response.alertable==true){
        showSuccess(messageSucces)
      }
    } else if (response.message === "error") {
      showError(response.error||"error desconcido")

    }
  };
  const showSuccess = (msm) => {
    setAlertData({ show: true, error: false, message:msm  });
   
  };
  const showError = (error) => {
      setAlertData({ show: true, error: true, message:error });
  };
  if(pqrsUnAvaible){
    return (
      <PqrsUnavailable/>
    )
  }

  return (
    <div className='index-clientsPage'>

      <div className='body-answer-pqrs-form'>
        <form className='form-answer-pqrs' onSubmit={handleSubmit}>
        <p className='subtitle-form-answer-pqr-2'>Responder PQRS</p>
          <a
            href="#"
            role="button"
            style={{ display: 'flex', marginTop: '0.5rem', cursor: 'pointer', color: '#0066cc',marginBottom:'3%',gap:'5px' }}
            onClick={(e) => {
              e.preventDefault();
              setDisplayPqrs(true);
            }}
          >
              <FaEye />
              <span>VerPqrs</span>
          </a>
          
          <div className='input-form-answer-pqrs'>
            <label>Nombre:</label>
            <input
              type="text"
              name="usuario"
              placeholder='Por favor ingresa tu nombre'
              required
              onChange={handleFormChange}
            />
          </div>

          <div className='input-form-answer-pqrs'>
            <label>Respuesta:</label>
            <textarea
              name="respuesta"
              placeholder='Por favor ingresa la respuesta que envías en usuario emisor'
              required
              onChange={handleFormChange}
            ></textarea>
          </div>

          <div className='input-form-answer-pqrs'>
            <label>Identificación de fallas:</label>
            <textarea
              name="causas"
              placeholder='Ingresa una identificación y/o análisis de la causa'
              required
              onChange={handleFormChange}
            ></textarea>
          </div>

          <div className='actions-implementeds-pqrs'>
            <h4>Acciones Implementadas</h4>

            {implementedActions.map((action, index) => (
              <div key={index} className='accion-item'>
                <div className='input-form-answer-pqrs'>
                  <label>Especificación de la implementación:</label>
                  <textarea
                    value={action.description}
                    onChange={e => handleActionChange(index, 'description', e.target.value)}
                    required
                  />
                </div>

                <div className='input-form-answer-pqrs'>
                  <label>¿Quién la hizo?</label>
                  <input
                    type="text"
                    value={action.responsible}
                    onChange={e => handleActionChange(index, 'responsible', e.target.value)}
                    required
                  />
                </div>

                <div className='input-form-answer-pqrs'>
                  <label>Adjuntar evidencia:</label>
                  <input
                    type="file"
                    onChange={e => handleActionChange(index, 'evidence', e.target.files[0])}
                    required
                  />
                </div>
              </div>
            ))}

            {isActionComplete(implementedActions[implementedActions.length - 1]) && (
              <a
                href="#"
                role="button"
                className='link-add-input'
                onClick={(e) => {
                  e.preventDefault();
                  addNewAction();
                }}
              >
                + Agregar otra acción implementada
              </a>
            )}

            {implementedActions.length > 1 && (
              <a
                href="#"
                role="button"
                className='link-delete-input'
                onClick={(e) => {
                  e.preventDefault();
                  removeLastAction();
                }}
              >
                - Eliminar última acción
              </a>
            )}
          </div>

          <button className='button-send-anwer-pqrs' type="submit" onClick={(e)=>handleSubmit(e)}>{loading?'Respondiendo':'Enviar'}</button>
        </form>
      </div>
      {displayPqrs && (
            <div className="info-pqrs-selected">
              <PqrsModal pqrs={pqrs} onClose={setDisplayPqrs}/>
            </div>
      )}
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
const formatearCampo = (campo) =>
  campo
    .replace(/_/g, " ")
    .replace(/\b\w/g, (l) => l.toUpperCase());
export default AnswerPqrsForm;
