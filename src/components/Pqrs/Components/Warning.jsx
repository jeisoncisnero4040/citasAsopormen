import React from "react";
import "../Styles/Warning.css"; // Asegúrate de crear este archivo

const Warning = ({ isOpen, onClose, errorMessage }) => {
  if (!isOpen) return null;

  return (
    <div className="warning-overlay">
      <div className="warning-modal">
        <h2 className="warning-title">Error</h2>
        <p className="warning-message">{errorMessage}</p>
        <button className="warning-close" onClick={onClose}>
          Cerrar
        </button>
      </div>
    </div>
  );
};

export default Warning;
