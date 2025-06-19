import { useState,useEffect } from "react";
import PdfService from "../Services/PdfService";
import { FaTimes} from "react-icons/fa";
import { FaEye } from 'react-icons/fa';



const AnswerPqrsToClientFormModal=({pqrs,onClose,loading,sendAnswertToClient})=>{
    const [AnswerBody,setAnswerBody]=useState({});
    const [filesToAdjunt,setFilesToAdjunt]=useState([]);
    const isParticularUser = AnswerBody.typeUser?.toLowerCase() === 'particular';
    useEffect(() => {
        if(pqrs) {
          const startBody={
            'answerArea':pqrs.respuesta,
            'user':pqrs.nombre_usuario,
            'userRegister':pqrs.nombre_quien_registra,
            'typeUser':pqrs.tipo_usuario,
            'userCed':pqrs.identificacion_usuario
          }
          setAnswerBody(startBody);
        }
      }, [pqrs]);
    const handleInputPqrsChange = (e) => {
      const { name, value } = e.target;
      setAnswerBody((prevBody) => ({
        ...prevBody,
        [name]: value
      }));
    };
    const openPdf=()=>{
        const pdfService=new PdfService(pqrs,AnswerBody);
        pdfService.openPdfInBrowser();
    }
    const answerPqrs = async (e) => {
      e.preventDefault();
      const pdfService = new PdfService(pqrs, AnswerBody);
      const formData = await pdfService.buildPdfFormData();

      filesToAdjunt.forEach((file) => {
        if (file) {
          formData.append("files_adjunt[]", file);
        }
      });

      sendAnswertToClient(formData);
    };
    const addFileInput = (e) => {
      e.preventDefault()
      setFilesToAdjunt([...filesToAdjunt, null]);
    };

  const handleFileChange = (index, file) => {
    const updatedFiles = [...filesToAdjunt];
    updatedFiles[index] = file;
    setFilesToAdjunt(updatedFiles);
  };
  const deleteFile = (e) => {
    e.preventDefault()
    const filesUpdated = filesToAdjunt.slice(0, filesToAdjunt.length - 1);
    setFilesToAdjunt(filesUpdated);
  };
  const isLastFileEmpty = () => {
    const lastFile = filesToAdjunt[filesToAdjunt.length - 1];
    return !lastFile; 
  }

  const hasFiles = () => {
    return filesToAdjunt.length > 0;
  }
    return (
      <div className="modal-overlay">
        <div className="modal-content-pqrs">

          {/* Título y botón de cerrar */}
          <div className="title-and-close-button">
            <p>Responder  Pqrs</p>
            <a onClick={() => onClose(false)}>
              <FaTimes />
            </a>
          </div>

          {/* Enlace para ver PDF */}
          <a
            href="#"
            role="button"
            className="link-add-input"
            onClick={(e) => {
              e.preventDefault();
              openPdf();
            }}
          >
            <FaEye />
            <span>Ver Pdf</span>
          </a>

          {/* Nombre de persona a enviar */}
          <div className="input-pqr">
            <label>Nombre de persona a enviar</label>
            <input
              type="text"
              name="userRegister"
              value={AnswerBody.userRegister || ""}
              onChange={handleInputPqrsChange}
              placeholder={loading ? "cargado..." : "Ingresa o modifica el nombre el solicitante"}
              required
            />
          </div>

          {/* Campos condicionales para usuarios no particulares */}
          {!isParticularUser && (
            <>
              <div className="input-pqr">
                <label>Cargo de receptor</label>
                <input
                  type="text"
                  name="post"
                  value={AnswerBody.post || ""}
                  onChange={handleInputPqrsChange}
                  placeholder={loading ? "cargado..." : "Ingresael cargo de quin recibe"}
                  required
                />
              </div>

              <div className="input-pqr">
                <label>Area de la Institucion receptora</label>
                <input
                  type="text"
                  name="areaEps"
                  value={AnswerBody.areaEps || ""}
                  onChange={handleInputPqrsChange}
                  placeholder={loading ? "cargado..." : "Ingresa la área de la Eps"}
                  required
                />
              </div>

              <div className="input-pqr">
                <label>Eps</label>
                <input
                  type="text"
                  name="typeUser"
                  value={AnswerBody.typeUser || ""}
                  onChange={handleInputPqrsChange}
                  placeholder={loading ? "cargado..." : "Ingresa la Eps"}
                  required
                />
              </div>
            </>
          )}

          {/* Motivo de Documento */}
          <div className="input-pqr">
            <label>Motivo de Documento</label>
            <input
              type="text"
              name="motive"
              value={AnswerBody.motive || ""}
              onChange={handleInputPqrsChange}
              placeholder={loading ? "cargado..." : "Ingresa el motivo de Respuesta"}
              required
            />
          </div>

          {/* Cuerpo de Respuesta PQRS */}
          <div className="input-pqr">
            <label>Cuerpo de Respuesta Pqrs</label>
            <textarea
              rows={10}
              name="answerArea"
              value={AnswerBody.answerArea || ""}
              onChange={handleInputPqrsChange}
              placeholder={loading ? "cargado..." : "Ingresa o modifica el cuerpo de la respuesta"}
              required
            />
          </div>

          {/* Archivos adjuntos */}
          <div className="input-form-answer-pqrs">
            <a href="#" onClick={(e) => addFileInput(e)} className="link-add-button">Agregar archivo adjunto</a>

            {filesToAdjunt.map((file, index) => (
              <div key={index} style={{ marginBottom: '0.5rem' }}>
                <input
                  type="file"
                  onChange={(e) => handleFileChange(index, e.target.files[0])}
                  required
                />
              </div>
            ))}
          <div style={{display:'flex',gap:'50px'}}>
            {!isLastFileEmpty() && (
              <a
                href="#"
                onClick={(e) => addFileInput(e)}
                className="link-add-input"
              >
                Agregar archivo adjunto
              </a>
            )}

            {hasFiles() && (
              <a
                href="#"
                onClick={(e) => deleteFile(e)}
                className="link-delete-input"
              >
                Eliminar último archivo
              </a>
            )}

          </div>

          </div>

          {/* Botón para enviar la respuesta */}
          <button onClick={(e) => answerPqrs(e)}>
            {loading ? 'Respondiendo' : 'Responder'}
          </button>

        </div>
      </div>
    );

}
export default AnswerPqrsToClientFormModal;