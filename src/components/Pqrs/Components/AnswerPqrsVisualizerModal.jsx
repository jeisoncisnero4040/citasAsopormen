import React, { useState, useEffect } from "react";
import { use } from "react";
import ApiRequestsManagerService from "../Services/ApiRequestsManagerService";
import PqrService from "../Services/PqrService";


const AnswerPqrsVisualizer = ({ pqrs, onClose }) => {
  const [pqrsToShow,setPqrsToShow]=useState({});
  const [pqrsActions,setPqrsActions]=useState([]);
  const [loading,setLoading]=useState(false);
  const includeFields = ["respuesta", "causas", "usuario_respuesta_area", "fecha_respuesta"];
  const pqrsService = new PqrService(new ApiRequestsManagerService());

  useEffect(() => {
    setPqrsToShow(pqrs);
    fetchActionsPqrs(pqrs);
  }, [pqrs]);

  const fetchActionsPqrs = async (pqrs) => {
    setLoading(true);
    const id = pqrs?.id ?? null;
    const response = await pqrsService.getActionsPqrs(id);
    setPqrsActions(response?.data ?? []);
    setLoading(false)
  };
  

  return (
    <div onClick={() => onClose(false)} className="modal-overlay">
      <div className="modal-content-pqrs" onClick={(e) => e.stopPropagation()}>
        <h2>Detalles Respuesta PQRS del área</h2>
  
        {Object.entries(pqrs).map(([key, value]) => {
          if (!includeFields.includes(key) || value === null || value === "") return null;
          return (
            <div key={key} className="modal-item-pqrs">
              <p>{formatearCampo(key)}:</p>
              <strong>{value}</strong>
            </div>
          );
        })}
        
        <h2>Acciones Implementadas</h2>
        {pqrsActions.length > 0 &&
          pqrsActions.map((action, index) => (
            <div key={index} className="action-answer-pqrs">
              <div className="modal-item-pqrs">
                  <p>{`${index+1}) Descripcion`}</p>
                  <strong>{action.descripcion}</strong>
              </div>
              <div className="modal-item-pqrs">
                  <p>Realizado por:</p>
                  <strong>{action.persona_responsable}</strong>
              </div>
              {action.url_evidencia && (
                <>
                  <p>Evidencia:</p>
                  <img src={action.url_evidencia} alt="Evidencia" />
                </>
              )}
            </div>
          ))}
  
        <button onClick={() => onClose(false)}>Cerrar</button>
      </div>
    </div>
  );
  
};

const formatearCampo = (campo) =>
  campo.replace(/_/g, " ").replace(/\b\w/g, (l) => l.toUpperCase());

export default AnswerPqrsVisualizer;
