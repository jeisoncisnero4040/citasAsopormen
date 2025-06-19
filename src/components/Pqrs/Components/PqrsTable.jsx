import useDynamicTable from "../../../hooks/useDynamicTable";
import "../Styles/PqrsTable.css";


const PqrsTable = ({ data, columns, showPqrs, showPqrsAnswer,notifyArea, notifyingId,displayChangeAreaPqrsForm,displaySendAnswerToClient,closePqrs }) => {
  const {
    data: tableData,
    sortBy,
    setFilterQuery,
    currentPage,
    setCurrentPage,
    totalPages,
  } = useDynamicTable(data, 20, null);

  const renderButtons = (pqrs) => {
    const isLoadingThis = notifyingId === pqrs.id;
  
    if (isLoadingThis) {
      return <div className="loading-button-pqrs">Cargando...</div>;
    }
  
    switch (pqrs.estado) {
      case 'activa':
        return (
          <button onClick={() => notifyArea(pqrs)} className="button-table-pqrs">
            Notificar al área
          </button>
        );
      case 'en area':
        return(
          <p style={{ margin: 0, padding: 0 }}>Esperando Respuesta</p>
        )
      case 'area respondido':
        return (
          <div style={{display:`flex`}}>
            <button onClick={()=>displaySendAnswerToClient(pqrs)} className="button-area-respondido">
                Notificar usuario
            </button>
            <button  onClick={()=>showPqrsAnswer(pqrs)}className="button-area-respondido">
                ver respuesta
            </button>
            <button  onClick={()=>displayChangeAreaPqrsForm(pqrs)}className="button-area-respondido">
                cambiar area
            </button>
          </div>

        );
      case 'respondido':
        return (
          <button onClick={() => closePqrs(pqrs)} className="button-respondido">
              cerrar
          </button>
        )

  
      default:
        return null;
    }
  };
  
  if (data.length === 0) {
    return <div>Cargando datos...</div>;
  }

  return (
    <div>
      <input
        type="text"
        placeholder="Buscar..."
        onChange={(e) => setFilterQuery(e.target.value)}
      />
      <table className="pqrs-table">
        <thead>
          <tr>
            {columns.map((col) => (
              <th key={col.key} onClick={() => col.sortable && sortBy(col.key)}>
                {col.label} {col.sortable ? "↕" : ""}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {tableData.map((row, index) => (
            <tr key={index}>
              {columns.map((col) => (
                <td
                  className={`row-table-pqrs-${row.estado.replace(" ", "-")}`}
                  key={col.key}
                  onClick={
                    col.key !== "acciones" ? () => showPqrs(row) : undefined
                  }
                >
                  {col.key !== "acciones" ? row[col.key] : renderButtons(row)}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
      <div className="button-navigate-table-orders">
        <button onClick={() => setCurrentPage((prev) => Math.max(prev - 1, 1))}>
          Anterior
        </button>
        <span>{`Página ${currentPage} de ${totalPages}`}</span>
        <button
          onClick={() =>
            setCurrentPage((prev) => Math.min(prev + 1, totalPages))
          }
        >
          Siguiente
        </button>
      </div>
    </div>
  );
};

export default PqrsTable;
