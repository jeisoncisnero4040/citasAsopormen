import React, { useEffect } from "react";
import "../Styles/Alert.css";

const Alert = ({ error, message = "Ocurrió un error inesperado", onClose }) => {
  useEffect(() => {
    const timer = setTimeout(() => {
      onClose();
    }, 3000);  

    return () => clearTimeout(timer); 
  }, [onClose]);

  return (
    <div className={`alert-container ${error ? "error" : "success"}`}>
      <p>{message}</p>
      <button className={error ? "error-btn" : "success-btn"} onClick={onClose}>
        Aceptar
      </button>
    </div>
  );
};

export default Alert;

