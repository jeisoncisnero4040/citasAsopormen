import React, { useState } from "react";
import PqrService from "../Services/PqrService";
import ApiRequestsManagerService from "../Services/ApiRequestsManagerService";
import Alert from "./Alert.jsx"
import TableHistoryPqrs from "./TableHistoryPqrs";
import { plotService } from "../Services/PlotService";
import '../Styles/Plots.css';
import { ExcelService } from "../Services/ExcelService.jsx";
import { PqrsConstants } from "../Constans/PqrsConstans.jsx";
import { useRef } from "react";

const DashboarDataPqrs = () => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(false);
    const [from, setFrom] = useState('');
    const [to, setTo] = useState('');
    const [from_tendencie, setFrom_tendencie] = useState('');
    const [to_tendencie, setTo_tendencie] = useState('');
    const [alertData, setAlertData] = useState({ show: false, error: false, message: "" });

    const pqrsService=new PqrService(new ApiRequestsManagerService());
    const columnsToTable=PqrService.dataToTableHistory;
    const chartRef = useRef(null);

    const handleDateChange = (e) => {
        const { name, value } = e.target;
        if (name === "from") setFrom(value);
        if (name === "to") setTo(value);
        if (name === "from_tendencie") setFrom_tendencie(value);
        if (name === "to_tendencie") setTo_tendencie(value);

    };
    const responseManager = (response, setItem,messageSucces=null) => {
        if (response.message === "success") {
            if(setItem !== null){
                setItem(response.data);
            }
        if(response.alertable==true){
            showSuccess(messageSucces)
        }
    } else if (response.message === "error") {
        showError(response.error||"error desconcido")
    }
    };

    const getData=async (e)=>{
        setLoading(true);
        e.preventDefault()
        const response=await pqrsService.getAllDataPqrs(from,to,from_tendencie,to_tendencie);
        responseManager(response,setData)
        setLoading(false)
    }

    const showSuccess = (msm) => {
        setAlertData({ show: true, error: false, message:msm  });
       
    };
    const showError = (error) => {
        setAlertData({ show: true, error: true, message:error });
    };
    const piePlotPqrsBytype = data.infoTypes ? plotService.plotPie(data.infoTypes,"") : null;
    const linePLotPqrsTypoVsTime=data.infoTypesByDate?plotService.plotLinePorTipo(data.infoTypesByDate):null;
    const piePlotPqrsByCanal=data.infoCanals?plotService.plotPie(data.infoCanals,""):null;
    const LinePlotPqrsCanalVsTime=data.infoCanalsByDate?plotService.plotLinePorTipo(data.infoCanalsByDate):null;
    const PiePlotPqrsBySede=data.infoSedes?plotService.plotPie(data.infoSedes,""):null;
    const linePlotPqrsBySede=data.infoSedeByDate?plotService.plotLinePorTipo(data.infoSedeByDate):null;
    const barPqrsBySedeVsClients=data.infoPorcentsPqrsBySede?plotService.plotBar(data.infoPorcentsPqrsBySede,"sede",["pqrs", "clientes","porcentaje"],{ pqrs: "PQRS", clientes: "Usuarios" },{ pqrs: "#FF6384", clientes: "#36A2EB",porcentaje:"#FFCE56" }):null;
    const LineTendenciePqr=data.infoPqrsTendencie?plotService.plotLinePorTipo(data.infoPqrsTendencie):null;
    const LineTendenciaPqrsByUsers=data.infoPqrsTendencieByUsers?plotService.plotLinePorTipo(data.infoPqrsTendencieByUsers,3,0.5):null;
    const pqrsTypeVsSede = data.infoPqrsvsSedes? plotService.plotBar(data.infoPqrsvsSedes,"sede",PqrsConstants.datasetFields,PqrsConstants.labelMap,PqrsConstants.colorMap,10,1): null;
    const PqrsByArea=data.infoPqrsbyArea?plotService.plotPie(data.infoPqrsbyArea,''):null;
    const PqrsVsSedesVsAreas=data.infoPqrsVsSedesVsAreas?plotService.plotBar(data.infoPqrsVsSedesVsAreas,"sede",PqrsConstants.datasetFieldsAreas,PqrsConstants.labelMapAreas,PqrsConstants.colorMap,10,1):null;
    const pqrsVsAreasVsSedes=data.infoPqrsVsAreasVsSedes?plotService.plotBar(data.infoPqrsVsAreasVsSedes,"area",PqrsConstants.datasetFieldServices,PqrsConstants.labelMapServices,PqrsConstants.colorMap,10,1):null;
    const lineTendenciePqrAreas=data.tendenciePqrsByAreas?plotService.plotLinePorTipo(data.tendenciePqrsByAreas,30,1):null;
    const piePqrsByCharacter=data.infoPqrsByCharacter?plotService.plotPie(data.infoPqrsByCharacter,""):null;
    const pqrsVsCharactersVsAreas = data.infoPqrsVsCharacterVsArea? plotService.plotBar(data.infoPqrsVsCharacterVsArea,"caracteristica", PqrsConstants.datasetFieldsAreas, PqrsConstants.labelMapAreas, PqrsConstants.colorMap,7,1): null;
    const piePqrsByCause=data.infoPqrsByCause?plotService.plotPie(data.infoPqrsByCause,""):null;
    const lineTendencieResponsePqrs=data.tendenceRangeTimeResponsePqrs?plotService.plotLinePorTipo(data.tendenceRangeTimeResponsePqrs,10,0.5):null;
    const lineTendencieResponsePqrsOnTime=data.tendenciaPqrsOnTime?plotService.plotLinePorTipo(data.tendenciaPqrsOnTime,110,5):null;
    const pieTimeResponseByArea=data.dataResponseTimeByArea?plotService.plotPie(data.dataResponseTimeByArea,""):null;

    const handleDownload = (data, filename = "informe.xlsx") => {
        ExcelService.exportToExcel(data, filename);
        };
    const handleExport = (e) => {
        e.preventDefault()
        const imagen1 = pieTimeResponseByArea.toImage(chartRef);
        console.log(imagen1);
    }
    
    return (
        <div >
            <div className="header-form-citas">
                <h2 className="title-new-pqr-page">INFORMES E HISTORIAL</h2>
            </div>
            <p className="text-subtitles-form-informes">Informe PQRS gestionados:</p>
            {/* Primer formulario */}
            <div className="form-get-informe">
            {/* Rango para PQRS */}
            <fieldset className="form-section">
                <legend>Rango de fechas para PQRS</legend>
                <div className="inputs-date-form-informes">
                <label>
                    Desde:
                    <input type="date" name="from" value={from} onChange={handleDateChange} />
                </label>
                <label>
                    Hasta:
                    <input type="date" name="to" value={to} onChange={handleDateChange} />
                </label>
                </div>
            </fieldset>

            {/* Rango para tendencias */}
            <fieldset className="form-section">
                <legend>Rango de fechas para Tendencia de PQRS</legend>
                <div className="inputs-date-form-informes">
                <label>
                    Desde:
                    <input type="date" name="from_tendencie" value={from_tendencie} onChange={handleDateChange} />
                </label>
                <label>
                    Hasta:
                    <input type="date" name="to_tendencie" value={to_tendencie} onChange={handleDateChange} />
                </label>
                </div>
            </fieldset>

            <div className="buttons-tools-form-informes">
                <button onClick={getData}>{loading ? 'Buscando...' : 'Buscar'}</button>
                {data.history && (
                <button onClick={() => handleDownload(data.history, `Informer_pqrs_${from}-${to}.xlsx`)}>Excel</button>
                )}
            </div>
            </div>

            {data.history && (
            <>
                <p className="sibtitle-dashboar-pqrs">Historial de PQRS</p>
                <TableHistoryPqrs data={data.history} columns={columnsToTable} />
            </>
            )}
            <div style={{ display: 'flex', flexWrap: 'wrap', width: '100%' }}>
                <div className="chart-container">
                    {piePlotPqrsBytype && (
                    <>
                        <p className="sibtitle-dashboar-pqrs">Gráfica PQRS por Tipo</p>
                        {piePlotPqrsBytype.display()}
                        <button onClick={() => handleDownload(data.infoTypes, `tipos_pqrs_${from}-${to}.xlsx`)}>Excel</button>
                    </>
                    )}
                </div>
                <div className="chart-container">
                    {linePLotPqrsTypoVsTime && (
                    <>
                        <p className="sibtitle-dashboar-pqrs">Tendencia de Tipos de PQRS en el Tiempo</p>
                        {linePLotPqrsTypoVsTime.display()}
                        <button onClick={() => handleDownload(data.infoTypesByDate, `tipos_pqrs_por_tiempo_${from}-${to}.xlsx`)}>Excel</button>
                    </>
                    )}
                </div>
            </div>

            <div style={{ display: 'flex', flexWrap: 'wrap', width: '100%' }}>
            <div className="chart-container">
                {piePlotPqrsByCanal && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Gráfica PQRS por Canal de Atención</p>
                    {piePlotPqrsByCanal.display()}
                    <button onClick={() => handleDownload(data.infoCanals, `canal_pqrs_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            <div className="chart-container">
                {LinePlotPqrsCanalVsTime && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Tendencia de PQRS por Canal en el Tiempo</p>
                    {LinePlotPqrsCanalVsTime.display()}
                    <button onClick={() => handleDownload(data.infoCanalsByDate, `canal_pqrs_tiempo_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            </div>

            <div style={{ display: 'flex', flexWrap: 'wrap', width: '100%' }}>
            <div className="chart-container">
                {PiePlotPqrsBySede && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Gráfica PQRS por Sede</p>
                    {PiePlotPqrsBySede.display()}
                    <button onClick={() => handleDownload(data.infoSedes, `sede_pqrs_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            <div className="chart-container">
                {linePlotPqrsBySede && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Tendencia de PQRS por Sede en el Tiempo</p>
                    {linePlotPqrsBySede.display()}
                    <button onClick={() => handleDownload(data.infoSedeByDate, `sede_pqrs_tiempo_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            </div>

            <div className="chart-container">
                {barPqrsBySedeVsClients && (
                    <>
                    <p className="sibtitle-dashboar-pqrs">Comparativa: PQRS vs Clientes por Sede</p>
                    {barPqrsBySedeVsClients.display()}
                    <button onClick={() => handleDownload(data.infoPorcentsPqrsBySede, `pqrs_vs_clientes_por_sede_${from}-${to}.xlsx`)}>Excel</button>
                    </>
                )}
            </div>

            <div className="chart-container-2">
                {LineTendenciePqr && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Gráfica Tendencia Pqr's</p>
                    {LineTendenciePqr.display()}
                    <button onClick={() => handleDownload(data.infoPqrsTendencie, `tendencie_pqr_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            <div className="chart-container-2">
                {LineTendenciaPqrsByUsers && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Tendencia de % PQRS por Usuarios atendidos</p>
                    {LineTendenciaPqrsByUsers.display()}
                    <button onClick={() => handleDownload(data.infoPqrsTendencieByUsers, `tendencia_porcentaje_pqrs_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            <div className="chart-container-2">
                {pqrsTypeVsSede && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Frecuencia tipo de pqrs por sede</p>
                    {pqrsTypeVsSede.display()}
                    <button onClick={() => handleDownload(data.infoPqrsvsSedes, `tendencia_porcentaje_pqrs_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            <div style={{ display: 'flex', flexWrap: 'wrap', width: '100%' }}>
                <div className="chart-container">
                    {PqrsByArea && (
                    <>
                        <p className="sibtitle-dashboar-pqrs">Gráfica PQRS por Area</p>
                        {PqrsByArea .display()}
                        <button onClick={() => handleDownload(data.infoPqrsbyArea, `pqrs_por_${from}-${to}.xlsx`)}>Excel</button>
                    </>
                    )}
                </div>
                <div className="chart-container">
                    {PqrsVsSedesVsAreas && (
                    <>
                        <p className="sibtitle-dashboar-pqrs">Distribucion de Pqrs por Areas en Sedes</p>
                        {PqrsVsSedesVsAreas.display()}
                        <button onClick={() => handleDownload(data.infoPqrsVsSedesVsAreas, `pqrs_vs-areas_vs_sedes_${from}-${to}.xlsx`)}>Excel</button>
                    </>
                    )}
                </div>
            </div>
            <div className="chart-container-2">
                {pqrsVsAreasVsSedes && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Frecuencia de Pqrs Por servicio en sedes</p>
                    {pqrsVsAreasVsSedes.display()}
                    <button onClick={() => handleDownload(data.infoPqrsVsAreasVsSedes, `pqrs_por_servicio_por_sede_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>

            <div className="chart-container-2">
                {lineTendenciePqrAreas && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Frecuencia de Pqrs Por servicio en sedes</p>
                    {lineTendenciePqrAreas.display()}
                    <button onClick={() => handleDownload(data.tendenciePqrsByAreas, `tendencie_pqrs_servicios_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>


            <div style={{ display: 'flex', flexWrap: 'wrap', width: '100%' }}>
                <div className="chart-container">
                    {piePqrsByCharacter && (
                    <>
                        <p className="sibtitle-dashboar-pqrs">Gráfica Distribución Pqrs por Caracteristica</p>
                        {piePqrsByCharacter.display()}
                        <button onClick={() => handleDownload(data.infoPqrsByCharacter, `pqrs_por_caracteristica_${from}-${to}.xlsx`)}>Excel</button>
                    </>
                    )}
                </div>
                <div className="chart-container">
                    {pqrsVsCharactersVsAreas && (
                    <>
                        <p className="sibtitle-dashboar-pqrs">Distribucion de Pqrs por Caracteristica Por Area</p>
                        {pqrsVsCharactersVsAreas.display()}
                        <button onClick={() => handleDownload(data.infoPqrsVsCharacterVsArea, `pqrs_cararacteristica_vs_area_vs_tiempo_${from}-${to}.xlsx`)}>Excel</button>
                    </>
                    )}
                </div>
            </div>
            <div style={{ display: 'flex', flexWrap: 'wrap', width: '100%' }}>
                <div className="chart-container">
                    {piePqrsByCause && (
                    <>
                        <p className="sibtitle-dashboar-pqrs">Frecuencia de Pqrs Por Causa</p>
                        {piePqrsByCause.display()}
                        <button onClick={() => handleDownload(data.tendenciePqrsByAreas, `info_pqrs_por_causa_${from}-${to}.xlsx`)}>Excel</button>
                    </>
                    )}
                </div>
                <div className="chart-container">
                    
                </div>
            </div>
            <div className="chart-container-2">
                {lineTendencieResponsePqrs && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Tendencia Tiempo de Respuesta Pqrs En días</p>
                    {lineTendencieResponsePqrs.display()}
                    <button onClick={() => handleDownload(data.tendenciePqrsByAreas, `tendencia_tiempos_respuesta_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            <div className="chart-container-2">
                {lineTendencieResponsePqrsOnTime && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Tendencia Porcentaje de Respuesta exitosa a pqrs</p>
                    {lineTendencieResponsePqrsOnTime.display()}
                    <button onClick={() => handleDownload(data.tendenciePqrsByAreas, `tendencia_porcentaje_respuesta_exitosa_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            <div className="chart-container-3">
                {pieTimeResponseByArea && (
                <>
                    <p className="sibtitle-dashboar-pqrs">Tiempo de Respuesta por area en Horas</p>
                    {pieTimeResponseByArea.display(chartRef)}
                    <button onClick={() => handleDownload(data.tendenciePqrsByAreas, `tiempo_respuesta_areas_en horas_${from}-${to}.xlsx`)}>Excel</button>
                </>
                )}
            </div>
            <div className="send-citas">
                <button  className='button-send-pqr' onClick={(e)=>handleExport(e)}>{loading?"CARGANDO...":"EXPORTAR A PDF"}</button>
            </div>

            {alertData.show && (
                <Alert 
                error={alertData.error} 
                message={alertData.message} 
                onClose={() => setAlertData({ ...alertData, show: false })}
                />
            )}
        </div>
    );
};

export default DashboarDataPqrs;
