import React from "react";
import useDynamicTable from "../Hooks/useDynamicTable";
import OrderCase from "../Services/OrderCase";
import '../Styles/TableOrderAvaibles.css'

const TableOrderAvaibles = ({ data, columns, viewOrder, closeOrder,loading}) => {
  const {
    data: tableData,
    sortBy,
    setFilterQuery,
    currentPage,
    setCurrentPage,
    totalPages
  } = useDynamicTable(data, 5, null);


  const renderButtons = (order) => {
    const caseIsAccepted = OrderCase.isOrderAccepted(order);
    return (
      <div className="buttos-option-case">
        {caseIsAccepted && <button className='close-case-order'onClick={() => closeOrder(order)}>{loading?'Cargando..':'Cerrar'}</button>}
        <button className='view-case-order' onClick={() => viewOrder(order)}>Ver</button>
      </div>
    );
  };

  return (
    <div className="table-cases-container">
      <input
        className="input-search-case"
        type="text"
        placeholder="Buscar..."
        onChange={(e) => setFilterQuery(e.target.value)}
      />
      <table className="table-orders-case">
        <thead >
          <tr className="header-table-case-order">
            {columns.map((col) => (
              <th key={col.key} onClick={() => col.sortable && sortBy(col.key)}>
                {col.label} {col.sortable ? "↕" : ""}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {tableData.map((row, index) => (
            <tr className={OrderCase.isOrderAccepted(row)?'accepted':'in-process'} key={index}>
              {columns.map((col) => (
                <td key={col.key}>
                  {col.key !== "acciones" ? row[col.key] : renderButtons(row)}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
      <div className="metadata tabla">
          <p>Orden ya aceptada</p>
          <p>Orden únicamente cargada</p>
      </div>
            <div className="button-navigate-table-orders">
        <button  onClick={() => setCurrentPage((prev) => Math.max(prev - 1, 1))}>
          Anterior
        </button>
        <span>{`Página ${currentPage} de ${totalPages}`}</span>
        <button
          onClick={() => setCurrentPage((prev) => Math.min(prev + 1, totalPages))}
        >
          Siguiente
        </button>
      </div>
    </div>
  );
};

export default TableOrderAvaibles;

