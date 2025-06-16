import React, { useState } from "react";
import { InformesService } from "../Services/InformesService";
import ApiRequestsManagerService from "../Services/ApiRequestManagerService";
import { ExcelService } from "../Services/ExcelService";
import { PlotterService } from "../../cloneSchedulePage/Components/PlotterService";
import '../Styles/InformesFactory.css';
import Warning from "../../Warning";
import { useRef } from "react";

const InformesFactory = () => {
  const informesService = new InformesService(new ApiRequestsManagerService());

  // Formulario 1
  const [from1, setFrom1] = useState("");
  const [to1, setTo1] = useState("");
  const [data1, setData1] = useState(null);

  // Formulario 2
  const [from2, setFrom2] = useState("");
  const [to2, setTo2] = useState("");
  const [data2, setData2] = useState(null);

  // Formulario 3 - Citas por entidad
  const [from3, setFrom3] = useState("");
  const [to3, setTo3] = useState("");
  const [data3, setData3] = useState(null);
  const [chart3, setChart3] = useState(null);
  const refChart3=useRef(null)

  // Estado general
  const [loading, setLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState("");
  const [warningIsOpen, setWarnigIsOpen] = useState(false);

  const handleSearch1 = async () => {
    setLoading(true);
    const response = await informesService.getNewClientsInforme(from1, to1);
    setLoading(false);

    if (response.error) {
      openWarning(response.error);
    } else {
      setData1(response.data || []);
    }
  };

  const handleSearch2 = async () => {
    setLoading(true);
    const response = await informesService.getClientsWhitOutAppoiments(from2, to2);
    setLoading(false);

    if (response.error) {
      openWarning(response.error);
    } else {
      setData2(response.data || []);
    }
  };

  const handleSearch3 = async () => {
    setLoading(true);
    const response = await informesService.getAppoimentsByEntity(from3, to3);
    setLoading(false);

    if (response.error) {
      openWarning(response.error);
      return;
    } 

    const result = response.data || [];
    setData3(result);
    const chart = PlotterService.plotLine(result, "citas", "fecha", "entidad",null);
    setChart3(chart);
    
  };

  const handleDownload = (data, filename = "informe.xlsx") => {
    ExcelService.exportToExcel(data, filename);
  };

  const openWarning = (error) => {
    setErrorMessage(error);
    setWarnigIsOpen(true);
  };

  return (
    <div className="container-informes">
      <div className="header-form-citas">
        <p>INFORMES</p>
      </div>

      {/* Informe 1 */}
      <p className="text-subtitles-form-informes">Informe Paciente nuevos y sus citas programadas:</p>
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

      {/* Informe 2 */}
      <p className="text-subtitles-form-informes">Pacientes Nuevos sin programación de Citas:</p>
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

      {/* Informe 3: Citas por entidad */}
      <p className="text-subtitles-form-informes">Citas por entidad:</p>
      <div className="form-get-informe">
        <div className="inputs-date-form-informes">
          <input type="date" value={from3} onChange={(e) => setFrom3(e.target.value)} />
          <input type="date" value={to3} onChange={(e) => setTo3(e.target.value)} />
        </div>
        <div className="buttons-tools-form-informes">
          <button onClick={handleSearch3}>{loading ? 'Buscando' : 'Buscar'}</button>
          {data3 && data3.length > 0 && (
            <button onClick={() => handleDownload(data3, "citas-por-entidad.xlsx")}>Descargar</button>
          )}
        </div>
      </div>
      <div className="plots-container">
        {chart3 && (
          <div >
            {chart3.display()}
          </div>
        )}
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
