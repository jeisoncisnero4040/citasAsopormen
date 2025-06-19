import useDynamicTable from "../../../hooks/useDynamicTable";
import "../Styles/PqrsTable.css";
import { useState } from "react";


const TableHistoryPqrs= ({ data, columns}) => {

  const [displayMode,setDisplayMode]=useState("status")

  const {
    data: tableData,
    sortBy,
    setFilterQuery,
    currentPage,
    setCurrentPage,
    totalPages,
  } = useDynamicTable(data, 20, null);
  
  const renderUrlEvidencie = (pqrs) => {
  return (
      <a href={pqrs.url_respuesta} target="_blank" rel="noopener noreferrer">
          ver respuesta
      </a>
  );
  };
  const handleDisplayModeChange = (e) => {
    setDisplayMode(e.target.value);
  };
  const getModeCssRoeTable=(row)=>{
    
    return displayMode==='status'?`row-table-pqrs-${row.estado.replace(" ", "-")}`:`row-table-pqrs-${row.tiempo_cumplido}`
    
  }

  return (
    <div>
      <div>
        <input
        style={{ width: '300px' }}
        type="text"
        placeholder="Buscar..."
        onChange={(e) => setFilterQuery(e.target.value)}

        
      />
        <label> Ver por: </label>
        <select
          name="displayMode"
          value={displayMode}
          onChange={handleDisplayModeChange}
        >
          <option value="status">Estado</option>
           <option value="onTime">cerrado a tiempo</option>
        </select>

      </div>

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
                  className={getModeCssRoeTable(row)}
                  key={col.key}
                >
                  {col.key !== "url_respuesta" ? row[col.key] :renderUrlEvidencie(row)}
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

export default TableHistoryPqrs;