import React, { useState } from "react";
import { InformesService } from "../Services/InformesService";
import ApiRequestsManagerService from "../Services/ApiRequestManagerService";
import { ExcelService } from "../Services/ExcelService";
import '../Styles/InformesFactory.css';
import Warning from "../../Warning"

const InformesFactory = () => {
  const informesService = new InformesService(new ApiRequestsManagerService());

  // Formulario 1
  const [from1, setFrom1] = useState("");
  const [to1, setTo1] = useState("");
  const [data1, setData1] = useState(null);
  const [errorMessage,setErrorMessage]=useState("");
  const [warningIsOpen,setWarnigIsOpen]=useState(false)

  // Formulario 2
  const [from2, setFrom2] = useState("");
  const [to2, setTo2] = useState("");
  const [data2, setData2] = useState(null);

  const [loading, setLoading] = useState(false);

  const handleSearch1 = async () => {
    setLoading(true);
    const response = await informesService.getNewClientsInforme(from1, to1);
    setLoading(false);

    if (response.error) {
      openWarning(response.error)
    } else {
      setData1(response.data || []);
    }
  };

  const handleSearch2 = async () => {
    setLoading(true);
    const response = await informesService.getClientsWhitOutAppoiments(from2, to2);
    setLoading(false);

    if (response.error) {
      openWarning(response.error)
    } else {
      setData2(response.data || []);
    }
  };

  const handleDownload = (data, filename = "informe.xlsx") => {
    ExcelService.exportToExcel(data, filename);
  };
  const openWarning=(error)=>{
      setErrorMessage(error);
      setWarnigIsOpen(true)
  }

  return (
    <div className="container-informes">
      <div className="header-form-citas">
        <p>INFORMES</p>
      </div>

      {/* Primer formulario */}
      <div className="form-get-informe">
        <div className="inputs-date-form-informes">
          <input type="date" value={from1} onChange={(e) => setFrom1(e.target.value)} />
          <input type="date" value={to1} onChange={(e) => setTo1(e.target.value)} />
        </div>

        <div className="buttons-tools-form-informes">
          <button onClick={handleSearch1}>{loading ? 'Buscando' : 'Buscar'}</button>
          {data1 && data1.length > 0 && (
            <button onClick={() => handleDownload(data1, "informe_nuevos_clientes.xlsx")}>Descargar</button>
          )}
        </div>
      </div>

      {/* Segundo formulario */}
      <div className="form-get-informe">
        <div className="inputs-date-form-informes">
          <input type="date" value={from2} onChange={(e) => setFrom2(e.target.value)} />
          <input type="date" value={to2} onChange={(e) => setTo2(e.target.value)} />
        </div>

        <div className="buttons-tools-form-informes">
          <button onClick={handleSearch2}>{loading ? 'Buscando' : 'Buscar'}</button>
          {data2 && data2.length > 0 && (
            <button onClick={() => handleDownload(data2, "informe_clientes_sin_citas.xlsx")}>Descargar</button>
          )}
        </div>
      </div>
          <Warning
            isOpen={warningIsOpen}
            onClose={() => setWarnigIsOpen(false)}
            errorMessage={errorMessage}
        /> 
    </div>
  );
};

export default InformesFactory;
