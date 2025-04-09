import React, { useState } from "react";
import "../Styles/RejectCaseForm.css"; // Asegúrate de importar los estilos

const RejectCaseForm = ({ rejectCase, hideRejectCaseForm ,loading}) => {
    const [razonToReject, setRazonToReject] = useState("");

    const handleChangeRazon = (event) => {
        setRazonToReject(event.target.value);
    };

    return (
        <div className="modal-overlay" onClick={hideRejectCaseForm}>
            <div className="modal-reject-case" onClick={(e) => e.stopPropagation()}>
                <h2>Rechazar Caso de Orden</h2>
                <p>Ingresa una razón detallada por la cual rechazas el caso</p>

                <div className="input-group">
                    <label htmlFor="razon">Razón:</label>
                    <textarea
                        id="razon"
                        value={razonToReject}
                        onChange={handleChangeRazon}
                        rows="6"
                        placeholder="Escribe aquí la razón por la cual rechazas el caso..."
                    ></textarea>
                </div>

                <div className="modal-buttons">
                    <button className="btn cancel" onClick={hideRejectCaseForm}>Salir</button>
                    <button className="btn reject" onClick={() => rejectCase(razonToReject)}>{loading?'Cargando':'Rechazar'}</button>
                </div>
            </div>
        </div>
    );
};

export default RejectCaseForm;
