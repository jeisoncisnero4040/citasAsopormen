import "../Styles/PqrsModal.css"; 

const PqrsModal = ({ pqrs, onClose }) => {
  const camposExcluidos = ["id", "acciones", "respuesta", "causa", "usuario_respuesta_area", "fecha_respuesta"];

  return (
    <div className="modal-overlay">
      <div className="modal-content-pqrs">
        <h2>Detalle del PQRS</h2>
        {Object.entries(pqrs).map(([key, value]) => {
          if (camposExcluidos.includes(key) || value === null || value === "") return null;

          if (typeof value === "object") return null;

          if (!key.includes("url")) {
            return (
              <div key={key} className="modal-item-pqrs">
                <p>{formatearCampo(key)}:</p>
                <strong>{value}</strong>
              </div>
            );
          }

          return (
            <div key={key} className="modal-item-pqrs">
              <p>{formatearCampo(key)}:</p>
              <a href={value} target="_blank" rel="noopener noreferrer">Ir a la URL</a>
            </div>
          );
        })}
        <button className="close-button" onClick={() => onClose(false)}>Cerrar</button>
      </div>
    </div>
  );
};

const formatearCampo = (campo) =>
  campo
    .replace(/_/g, " ")
    .replace(/\b\w/g, (l) => l.toUpperCase());

export default PqrsModal;
