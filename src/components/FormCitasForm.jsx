import React, { Component } from "react";
import '../styles/selectProfesional.css';
import '../styles/SelectOrder.css';
import Constants from '../js/Constans.jsx';
import ProfesionalCalendar from "./ProfesionalCalendar.jsx";
import ClientCalendar from "./ClienCalendar.jsx";
import SelectDataClient from "./SelectDataClient.jsx";
import Warning from "./Warning";
import TableOrders from "./TableOrders.jsx";
import Procedures from "./Procedures.jsx";
import SelectCentral from "./SelectCentral.jsx"
import CreateCitaForm from "./CreateCitaForm.jsx";
import AlertSchedule from "./AlertSchedule.jsx";
import ApiRequestManager from "../util/ApiRequestMamager.js";
import ProfesionalSchedule from "./ProfesionalSchedule.jsx";



class FormCitasForm extends Component {
    requestManager = new ApiRequestManager();
    constructor(props) {
        
        super(props);
        this.state = {
            Authorization:{},
            AuthorizationcounterCitas:'',
            schedule:{},
            central:{
                'nombre':'',
                'direccion':'',
                'cod':'',
            },
            procedure:{},
            client:{},
            profesional_calendar: [],
            profesional_schedule: [],

            client_calendar:[],
            profesional_list: [],
            profesional: {
                name: "",
                speciality: "",
                cedula: '',
            },

            historyNumber:'',
            
            loading: false,
            sendingCitas:false,
            showClientCalendar:false,
            showScheduleProfesional:false,
            searchQuery: "",
            selectedOption: "",
            errorMessage:'',
            warningIsOpen:false,

            alertMessage: '', 
            AlertIsOpen:false,
            canContinue:true

        };
    }

    handleKeyDown = (event) => {
        if (event.key === 'Enter') {
            this.setState({ loading: true });
    
            const url = `${Constants.apiUrl()}get_profesionals/${this.state.searchQuery}`;
             
    
            this.requestManager.getMethod(url)
                .then(data => {
                    const profesionalList = data.data.data;
                    if (profesionalList.length > 0) {
                        const firstProfesional = profesionalList[0];
                        const selectedEcc = (firstProfesional.ecc || '').trim();
    
                        this.setState({
                            profesional_list: profesionalList,
                            profesional: {
                                name: (firstProfesional.enombre || '').trim(),
                                speciality: (firstProfesional.nombre || '').trim(),
                                cedula: selectedEcc
                            },
                            selectedOption: selectedEcc,
                            profesional_calendar:[],
                            ProfesionalSchedule:[],
                            showClientCalendar:false
                        });
                    } else {
                        this.setState({ profesional_list: profesionalList });
                    }
                })
                .catch(error=> {
                    
                    this.setState({
                        errorMessage: error ? error : 'Error al hacer la petición',
                        warningIsOpen: true,
                    });
                })
                .finally(() => {
                    this.setState({ loading: false });
                });
        }
    }
    renderProfesionalForm = () => {
        const { showScheduleProfesional, searchQuery, selectedOption, loading, profesional } = this.state;
    
        return (
            <div className="get-all-data-pro">
                <div>
                    
                    <div className="input-name-pro">
                        <div className="input-name-profesional">
                            <label htmlFor="search-profesional">Buscar Profesional</label>
                            <input
                                id="search-profesional"
                                type="search"
                                placeholder="Buscar por nombre"
                                value={searchQuery}
                                onChange={this.handleInputChange}
                                onKeyDown={this.handleKeyDown}
                            />
                        </div>

                        <div className="select-profesional">
                            <label htmlFor="select-profesional">Seleccionar Profesional</label>
                            <select
                                id="select-profesional"
                                name="select-profesional"
                                value={selectedOption}
                                onChange={this.handleSelectChange}
                            >
                                {this.renderProfesionales()}
                            </select>
                        </div>
                    </div>

                    {/* Información del Profesional */}
                    <div className="info-profesional">
                        <div className="read-info-p">
                            <label htmlFor="profesional-name">Nombre:</label>
                            <input
                                id="profesional-name"
                                type="text"
                                value={loading ? 'Buscando...' : profesional?.name || ''}
                                readOnly
                            />
                        </div>

                        <div className="read-info-p">
                            <label htmlFor="profesional-speciality">Especialidad:</label>
                            <input
                                id="profesional-speciality"
                                type="text"
                                value={loading ? 'Buscando...' : profesional?.speciality || ''}
                                readOnly
                            />
                        </div>
                    </div>
                </div>
                
            </div>
        );
    };
    
    
    
    handleInputChange = (event) => {
        this.setState({ searchQuery: event.target.value });
    }
    handleSelectChange = (event) => {
        const selectedEcc = event.target.value;
        this.setState({ selectedOption: selectedEcc });
        const selectedProfesional = this.state.profesional_list.find(profesional => profesional.ecc.trim() === selectedEcc);
        if (selectedProfesional) {
            this.setState({
                profesional: {
                    name: selectedProfesional.enombre.trim(),
                    speciality: selectedProfesional.nombre.trim(),
                    cedula: selectedProfesional.ecc.trim(),
                    
                },
                profesional_calendar:[],
                ProfesionalSchedule:[],
                showScheduleProfesional:false
            });

        }
    }
    //felete later
    getCalendarProfesional = (cedulaProfesional) => {
        this.setState({ loading: true });

        const url = `${Constants.apiUrl()}get_profesional_calendar/${cedulaProfesional}`;

        this.requestManager.getMethod(url)
            .then(response => {
                
                this.setState({ profesional_calendar: response.data.data });
                     
               
            })
            .catch(error => {
                    this.setState({
                        errorMessage: error ? error : 'Error al hacer la petición',
                        warningIsOpen: true,
                    });
                }
            )
            .finally(() => {
                this.setState({ loading: false });
            });
    }

    getCalendarClient=(calendarUpdated,tiempo=null)=>{
        this.setState({
            client_calendar:calendarUpdated
        },()=>this.updateCounterCitas(tiempo))
    }
    ChangeToSchedule=()=>{
        this.setState({showScheduleProfesional:!this.state.showScheduleProfesional})
    }

    
    getUpdateCalendarPro = (calendarUpdated, tiempo=null) => {

        this.setState({
            profesional_calendar: calendarUpdated
        }, tiempo?() => this.updateCounterCitas(tiempo):null);
    }
    getUpdateSchedulePro=(scheduleUpdate)=>{
        this.setState({profesional_schedule:scheduleUpdate})
    }
    
    updateCounterCitas = (tiempo) => {
        if (typeof tiempo === 'object' && tiempo !== null) {  
            if (tiempo[this.state.Authorization.tiempo]) {
                this.setState(prevState => ({
                    AuthorizationcounterCitas: prevState.AuthorizationcounterCitas - tiempo[this.state.Authorization.tiempo]
                }));
            }
     
        } else {
            if (tiempo === this.state.Authorization.tiempo) {
                this.setState(prevState => ({
                    AuthorizationcounterCitas: prevState.AuthorizationcounterCitas - 1
                }));
            }
        }
    }
    
    
    changeCalendar=()=>{
        this.setState(
            {showClientCalendar:!this.state.showClientCalendar}
        )
    }
    renderProfesionales() {
        if (this.state.profesional_list.length > 0) {
            return this.state.profesional_list.map((profesional, index) => (
                <option key={index} value={profesional.ecc.trim()}>
                    {profesional.enombre.trim()}
                </option>
            ));
        } else {
            return <option>No se encontraron profesionales</option>;
        }
    }

    getProcedureName=(updatedProcedure)=>{
        this.setState({ procedure: updatedProcedure });
    }
    getClienInfo = (updatedClient) => {
        this.setState({ 
            client: updatedClient,
            historyNumber:updatedClient.codigo,
            client_calendar:[],
            Authorization:{},
            AuthorizationcounterCitas:''

         });

    }
    getCentralInfo=(UpadtedCentral)=>{
        this.setState({ central: 
            {
                nombre:UpadtedCentral.nombre,
                direccion:UpadtedCentral.direccion,
                cod:UpadtedCentral.cod
            }
         });
    }
    
    getAuthorizationInfo = (updatedAuthorization) => {
        this.setState({ 
            Authorization: updatedAuthorization 
        });
    }
    
    getCounterCitas=(numCitasUpdated)=>{
        this.setState(
            {AuthorizationcounterCitas:numCitasUpdated})
    }
    getScheduleCitas=(updatedSchedule)=>{
        this.setState({ schedule: updatedSchedule });
    }

    checkSchedule = () => {
        const weekdays = this.state.schedule.weekDays;
    
        let startDate = new Date(this.state.schedule.startDate);
        startDate.setHours(startDate.getHours());
        let startDateInColombia = startDate;
        
        const duration = this.state.procedure.duraccion;
        const numSession = this.state.schedule.sessionsNum;
        
        const durationInHours = duration / 60;
        const durationCita = numSession * durationInHours;
        
        const hourStart = startDateInColombia.getHours();
        const hourFinish = hourStart + durationCita;
    
        let alertMessage = 'Alerta\n';
        if (weekdays.includes("domingo")) {
            alertMessage += 'Has seleccionado el día domingo que no es día laborable.\n';
        }
    
        if (weekdays.includes("sabado") && (hourStart >= 12 || hourFinish >= 12)) {
            alertMessage += 'El horario seleccionado es el sábado después de las 12pm. \n';
        }
    
        if (hourStart < 5) {
            alertMessage += "El horario seleccionado es antes de las 5am.\n";
        } else if (hourFinish > 19) {
            alertMessage += "El horario seleccionado es después de las 7pm.\n";
        }
    
        if (alertMessage !== 'Alerta\n') {
            alertMessage += '¿Seguro que deseas continuar?';
            return alertMessage.trim();
        } else {
            return null;
        }
    }
    
    getBodyRequests = () => {
        let startDate = new Date(this.state.schedule.startDate);
        startDate.setHours(startDate.getHours()-5);
        let startDateInColombia = startDate;
        
        return {

            'ced_usu': this.props.user.cedula,
            'registro': this.props.user.usuario.trim(),
            
            'profesional':this.state.profesional.name.trim(),
            'cedprof': this.state.profesional.cedula,

            'sede': this.state.central.cod,
            'direccion_cita':this.state.central.direccion,
            
            'nro_hist': this.state.client.codigo,
            'codent': this.state.client.cod_entidad,
            'codent2': this.state.client.convenio,
            'client_number_cel':this.state.client.cel,
            'clientName':this.state.client.nombre,

            'n_autoriza':this.state.Authorization.n_autoriza,
            'procedim':this.state.Authorization.procedim,
            'tiempo':this.state.Authorization.tiempo,

            'procedipro': this.state.procedure.nombre,
            'recordatorio_wsp':this.state.procedure.recordatorio_whatsapp,
            'notication_orden_programed':this.state.procedure.sendWhatsappConfirmation||false,
            'duration_session':this.state.procedure.duraccion,  

            'regobserva': this.state.schedule.observationId,
            'copago':this.state.schedule.copago??'No aplica',
            'start_date': startDateInColombia.toISOString(),
            'week_days': this.state.schedule.weekDays,
            'num_citas': this.state.schedule.numCitas,
            'num_sessions': this.state.schedule.sessionsNum,
            
            
            'all_sessions':this.state.Authorization.cantidad,
            'saved_sessions':this.state.AuthorizationcounterCitas


        };
    }
    sendCitas = (body) => {
        const url = `${Constants.apiUrl()}citas/create_citas`;
        this.setState({
            sendingCitas: true
        });
    
        this.requestManager.postMethod(url, body)
            .then(response => {
                this.setState({
                    AuthorizationcounterCitas:this.state.AuthorizationcounterCitas+response.data.data
                },()=>this.getCalendarProfesional(body['cedprof'])) 
            })
            .catch(error => {


                    this.setState({
                        errorMessage: error,
                        warningIsOpen: true,
                    });

            })
            .finally(() => {
                this.setState({
                    sendingCitas: false
                });
            });
    };
    
    driveSendCitasRequest = () => {
        const alertMessage = this.checkSchedule();
    
        if (alertMessage) {
            this.setState({
                canContinue: false,
                alertMessage: alertMessage,
                AlertIsOpen: true,
            });
            return;   
        }
    
         
        const body = this.getBodyRequests();

        this.sendCitas(body);
    }

    onAccept = () => {
        this.setState({
            canContinue: true,
            AlertIsOpen: false
        }, () => {
             
            const body = this.getBodyRequests();
            this.sendCitas(body);
        });
    }
    render() {
        return (
            <div className="form-citas-1">
                <div className="header-form-citas">
                    <p>PROGRAMADOR DE CITAS</p>
                </div>
                <form action="">
                    <div className="body-form-citas">
                        <div className="select-data">
                            <div className="data-profesional">

                                {this.renderProfesionalForm()}
                                 

                                <div className="calendar-small-screen">
                                    <a onClick={this.changeCalendar}>
                                        {this.state.showClientCalendar ? "Mostrar calendario Profesional" : "Mostrar calendario cliente"}
                                    </a>
                                    {this.state.showClientCalendar ? (
                                        <ClientCalendar nameClient={this.state.client.nombre} codigo={this.state.client.codigo} events={this.state.client_calendar} getCalendarClient={this.getCalendarClient} />
                                    ) : (
                                        <ProfesionalCalendar events={this.state.profesional_calendar} nameProfesional={this.state.profesional.name}  getUpdateCalendarPro={this.getUpdateCalendarPro} getUpdateSchedulePro={this.getUpdateSchedulePro} cedulaProfesional={this.state.profesional.cedula.trim()}  ChangeToSchedule={this.ChangeToSchedule}/>
                                    )}
                                </div>
                                <div className="get-client-info">
                                    
                                    <SelectDataClient getClienInfo={this.getClienInfo}/>
                                </div>
                            </div>
                            <div className="calendario-profesional">
                                <a onClick={this.changeCalendar}>
                                    {this.state.showClientCalendar ? "Mostrar calendario Profesional" : "Mostrar calendario cliente"}
                                </a>
                                {this.state.showClientCalendar ? (
                                    <ClientCalendar nameClient={this.state.client.nombre} codigo={this.state.client.codigo} events={this.state.client_calendar} getCalendarClient={this.getCalendarClient} />
                                ) : (
                                    <ProfesionalCalendar events={this.state.profesional_calendar} nameProfesional={this.state.profesional.name}  getUpdateCalendarPro={this.getUpdateCalendarPro} getUpdateSchedulePro={this.getUpdateSchedulePro} cedulaProfesional={this.state.profesional.cedula.trim()}  ChangeToSchedule={this.ChangeToSchedule}/>
                                )}
                            </div>

                        </div>
                        <div className="select-orden">
                            {this.state.showScheduleProfesional ? (
                                <ProfesionalSchedule events={this.state.profesional_schedule} ChangeToSchedule={this.ChangeToSchedule} profesional={this.state.profesional} />
                            ) : (
                                <>
                                    <div className="data-order">
                                        <div className="get-order">
                                            <TableOrders 
                                                authorization={this.state.Authorization}
                                                historyNumber={this.state.historyNumber}
                                                nameClient={this.state.client.nombre}
                                                getAuthorizationInfo={this.getAuthorizationInfo}
                                                getCounterCitas={this.getCounterCitas}
                                            />
                                        </div>
                                        <div className="get-order">
                                            <Procedures getProcedureName={this.getProcedureName}/> 
                                        </div>
                                    </div>

                                    <div className="create-cita">
                                        <SelectCentral getCentralInfo={this.getCentralInfo}/> 
                                        <CreateCitaForm 
                                            getScheduleCitas={this.getScheduleCitas}
                                            numCitas={this.state.AuthorizationcounterCitas}
                                        /> 
                                    </div>
                                </>
                            )}
                        </div>

                        <div className="send-citas">
                            {this.state.sendingCitas?<a onClick={null}>Creando Citas...</a>:<a onClick={this.driveSendCitasRequest }>enviar citas</a>}
                        </div>
 
                        
                    </div>
                </form>
                <div>
                    <Warning
                        isOpen={this.state.warningIsOpen}
                        onClose={() => this.setState({ warningIsOpen: false })}
                        errorMessage={this.state.errorMessage}
                    /> 
                </div>
                <div>
                    <AlertSchedule
                        isOpen={this.state.AlertIsOpen}
                        onClose={() => this.setState({ AlertIsOpen: false })}
                        alertMessage={this.state.alertMessage}
                        onAccept={this.onAccept}
                         
                    />
                </div>
            </div>
        );
    }
}

export default FormCitasForm;
