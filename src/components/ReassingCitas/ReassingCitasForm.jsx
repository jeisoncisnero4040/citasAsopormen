import React from "react";
import UpWarning from "./upWarning.jsx";
import AlertSchedule from "../AlertSchedule";
import ApiRequestManager from "../../util/ApiRequestMamager";
import CalendarProfesionalReadOnly from "./CalendarProfesionaReadOnly"; 
import "../../styles/ReassingCitas/ReassingCitasForm.css";
import CreateCitaForm from "../CreateCitaForm";
import ProfesionalSchedule from "../ProfesionalSchedule";
import ProfesionalSearch from "./ProfesionalSearch";
import CalerdarProfesionalSelecteable from "./CalendarProfesionalSlecteable";
import TableCitasCanceled from "./TableCitasCanceled";
import DataCitaSelected from "./DataCitaSelected";
import Constants from '../../js/Constans.jsx';

class ReassingCitasForm extends React.Component {
    requestManager = new ApiRequestManager();

    constructor(props) {
        super(props);
        this.state = {
            searchQuery: "",
            selectedOption: "",

            profesional: {}, 
            profesional_2: {}, 
            
            profesional_calendar: [], 
            profesional_calendar_2: [], 

            profesional_schedule:[],
            profesional_schedule_2: [], 
            selectedIds:[],

            schedule:{},
            cita:{},

            showClientCalendar: false, 
            showScheduleProfesional:false,
            errorMessage: '',
            warningIsOpen: false,
    
            alertMessage: '',
            AlertIsOpen: false,
            canContinue: true,
            showTwoCalendars:true,
    
            loading: false,
            citaWasChecjedToUnavaible:false,
            reasing:false

        };
    }
    setLoading=(newState)=>{

        this.setState({loading:newState})
    }

    getUpdateCalendarPro = (calendarUpdated) => {
        this.setState({
            profesional_calendar: calendarUpdated
        });
    };
    getUpdateCalendarPro_2 = (calendarUpdated) => {
        this.setState({
            profesional_calendar_2: calendarUpdated
        });
    };

    getUpdateProfesional=(profesionalUpdated)=>{
        this.setState({
            profesional:profesionalUpdated
        })
    }
    getUpdateProfesional_2=(profesionalUpdated)=>{
        this.setState({
            profesional_2:profesionalUpdated
        })
    }
    
    getUpdateSchedulePro = (scheduleUpdate) => {
        this.setState({ profesional_schedule: scheduleUpdate });
    };
    getUpdateSchedulePro_2 = (scheduleUpdate) => {
        this.setState({ profesional_schedule_2: scheduleUpdate });
    };
    getUpdateIdsSelected=(idsSelectedIds)=>{
        this.setState({selectedIds:idsSelectedIds});
    }
    ChangueCitaCheckedToUnavaiable=()=>{
        this.setState({citaWasChecjedToUnavaible:!this.state.citaWasChecjedToUnavaible})
    }
    CloseScheduleProfesional=()=>{
        this.setState({showScheduleProfesional:false})
    }
    
    ChangeToSchedule = () => {
        this.setState({showScheduleProfesional: !this.state.showScheduleProfesional });
    };
    ChangeForm=()=>{
        this.setState({showTwoCalendars: !this.state.showTwoCalendars})
    }

    getScheduleCitas=(updatedSchedule)=>{
        this.setState({ schedule: updatedSchedule });
    }
    getUpdateCita=(updatedCita)=>{
         
        this.setState({cita:updatedCita,
                        citaWasChecjedToUnavaible:false
        })
    }
    showProfesionalSchedule = () => {
        return this.state.showScheduleProfesional ? (
            <ProfesionalSchedule
                events={this.state.profesional_schedule_2}
                ChangeToSchedule={this.ChangeToSchedule}
                profesional={this.state.profesional.name}
            />
        ) : null;
    };


    //dinamics components_session

    renderTwoProfesionalsForm = () => {
        return (
            <div className="wrapper">
                <div className="insert-profesional-form">
                    <p>Selecciona el profesional a modificar citas</p>
                    <ProfesionalSearch
                        getUpdateCalendarPro={this.getUpdateCalendarPro}
                        getUpdateSchedulePro={this.getUpdateSchedulePro}
                        getUpdateProfesional={this.getUpdateProfesional}
                        CloseScheduleProfesional={this.CloseScheduleProfesional}
                    /> 
                    <CalerdarProfesionalSelecteable
                        events={this.state.profesional_calendar}  
                        nameProfesional={this.state.profesional.name}  
                        getUpdateCalendarPro={this.getUpdateCalendarPro}
                        getUpdateSchedulePro={this.getUpdateSchedulePro}
                        cedulaProfesional={this.state.profesional.cedula ? this.state.profesional.cedula.trim() : ''}
                        getUpdateIdsSelected={this.getUpdateIdsSelected}
                    />   
                </div>
    
                <div className="insert-profesional-form">
                    <p>Selecciona el profesional a agregar citas</p>
                    <ProfesionalSearch
                        getUpdateCalendarPro={this.getUpdateCalendarPro_2}  
                        getUpdateSchedulePro={this.getUpdateSchedulePro_2}  
                        getUpdateProfesional={this.getUpdateProfesional_2}  
                        CloseScheduleProfesional={this.CloseScheduleProfesional}
                    /> 
                    <CalendarProfesionalReadOnly
                        events={this.state.profesional_calendar_2}  
                        nameProfesional={this.state.profesional_2.name}  
                        getUpdateCalendarPro={this.getUpdateCalendarPro_2}
                        getUpdateSchedulePro={this.getUpdateSchedulePro_2}
                        cedulaProfesional={this.state.profesional_2.cedula ? this.state.profesional_2.cedula.trim() : ''}
                        ChangeToSchedule={this.ChangeToSchedule}
                    /> 
                </div>
            </div>
        );
    };
    renderTableCitasCanceled = () => {
        return (
            <div className="wrapper">
                <div className="insert-citas-canceled-available-table">
                    < TableCitasCanceled getUpdateCita={this.getUpdateCita}/>
                </div>
                <div className="insert-citas-canceled-avaible-show-info-cita-selected">
                    < DataCitaSelected cita={this.state.cita}
                    ChangueCitaCheckedToUnavaiable={this.ChangueCitaCheckedToUnavaiable}/>
                </div>
            </div>
        );
    };
    renderformCreateCitasAndSeconFormPro=()=>{
        return (
            <div className="wrapper">
                <div className="forrm-for-searh-profesional">
                    <p>Selecciona el profesional a agregar citas</p>
                    <ProfesionalSearch
                        getUpdateCalendarPro={this.getUpdateCalendarPro_2}  
                        getUpdateSchedulePro={this.getUpdateSchedulePro_2}  
                        getUpdateProfesional={this.getUpdateProfesional_2}  
                        CloseScheduleProfesional={this.CloseScheduleProfesional}
                    /> 

                    <CreateCitaForm 
                        getScheduleCitas={this.getScheduleCitas}
                        numCitas={this.state.cita.reasignadas}
                    /> 
                </div>
                <div className="insert-citas-canceled-avaible-show-info-cita-selected">
                    <CalendarProfesionalReadOnly
                        events={this.state.profesional_calendar_2}  
                        nameProfesional={this.state.profesional_2.name}  
                        getUpdateCalendarPro={this.getUpdateCalendarPro_2}
                        getUpdateSchedulePro={this.getUpdateSchedulePro_2}
                        cedulaProfesional={this.state.profesional_2.cedula ? this.state.profesional_2.cedula.trim() : ''}
                        ChangeToSchedule={this.ChangeToSchedule}
                    /> 
                </div>
            </div>
        );
    }
    _openErrorAlert = (error) => {
        this.setState({
            errorMessage: error,
            warningIsOpen: true
        });
    };


    handleReassignCitasButton = () => {
        
        const { citaWasChecjedToUnavaible } = this.state;
        this.setState({loading:true});
        if (citaWasChecjedToUnavaible) {
            const body = this._inactivateCitaBody();
            const url=`${Constants.apiUrl()}citas/Unactivate_cita_canceled`;
            this.setLoading(true);
            this.requestManager.postMethod(url,body)
                .then(response=>{
                    this.setState({citaWasChecjedToUnavaible:false},
                        ()=>this._removeCitasInSessionStorage())
                    
                }).catch(error=>{
                    
                    this._openAlert(error)
                }).finally(()=>this.setLoading(false))
            
            
        } else {
            const messageAlert = this._checkSchedule();
            if (messageAlert) {
                this._openAlert(messageAlert);
                return;
            }
            const bodyRequest = this._getBodyRequests();
            const url = this._buildUrl();
            this._sendCitasToSave(bodyRequest, url);
        }
    }

    _inactivateCitaBody = () => {
        return { id: this.state.cita.id };
    }

    _openAlert = (messageAlert) => {
        this.setState({
            canContinue: false,
            alertMessage: messageAlert,
            AlertIsOpen: true,
        });
    }

    onAccept = () => {
        this.setState({
            canContinue: true,
            AlertIsOpen: false
        }, () => {
            const bodyRequest = this.getBodyRequests();
            const url = this.buildUrl();
            this.sendCitasToSave(bodyRequest, url);
        });
    }

    _checkSchedule = () => {
        if (this.state.showTwoCalendars) {
            return null;
        }
        const weekdays = this.state.schedule.weekDays;
        const startDate = new Date(this.state.schedule.startDate);
        const durationInHours = this.state.duration / 60;
        const durationCita = this.state.schedule.sessionsNum * durationInHours;

        const hourStart = startDate.getHours();
        const hourFinish = hourStart + durationCita;

        let alertMessage = 'Alerta\n';
        if (weekdays.includes("domingo")) {
            alertMessage += 'Has seleccionado el día domingo que no es día laborable.\n';
        }

        if (weekdays.includes("sabado") && (hourStart >= 12 || hourFinish >= 12)) {
            alertMessage += 'El horario seleccionado es el sábado después de las 12pm.\n';
        }

        if (hourStart < 5) {
            alertMessage += "El horario seleccionado es antes de las 5am.\n";
        } else if (hourFinish > 19) {
            alertMessage += "El horario seleccionado es después de las 7pm.\n";
        }

        if (alertMessage !== 'Alerta\n') {
            alertMessage += '¿Seguro que deseas continuar?';
            return alertMessage.trim();
        }
        return null;
    }

    _getBodyRequests = () => {
        if (this.state.showTwoCalendars) {
            return {
                'ced_usu': this.props.user.cedula,
                'registro': this.props.user.usuario.trim(),
                'cedprof': this.state.profesional_2.cedula,
                'ids': this.state.selectedIds
            };
        }
        const startDate = new Date(this.state.schedule.startDate);
        startDate.setHours(startDate.getHours() - 5);
        
        return {
            'ced_usu': this.props.user.cedula,
            'registro': this.props.user.usuario.trim(),

            'profesional':this.state.cita.profesional,
            'cedprof': this.state.profesional_2.cedula,

            'sede': this.state.cita.cod??'001',
            'direccion_cita':this.state.cita.direccion_cita,

            'nro_hist': this.state.cita.nro_hist,
            'codent': this.state.cita.codent,
            'codent2': this.state.cita.codent2,
            'client_number_cel':this.state.cita.celular,
            'clientName':this.state.cita.cliente,

            'n_autoriza':this.state.cita.autoriz,
            'procedim':this.state.cita.procedim,
            'tiempo':this.state.cita.tiempo,

            'procedipro': this.state.cita.procedimiento,
            'recordatorio_wsp':true,
            'notication_orden_programed':false,
            'duration_session':this.state.cita.duracion,  

            'regobserva': this.state.schedule.observationId,
            'start_date': startDate.toISOString(),
            'week_days': this.state.schedule.weekDays,
            'num_citas': this.state.schedule.numCitas,
            'copago':this.state.schedule.copago??'No aplica',
            'num_sessions': this.state.schedule.sessionsNum,

            'all_sessions':this.state.cita.cantidad,
            'saved_sessions':this.state.cita.reasignadas,

            'id':this.state.cita.id

        };
    }

    _buildUrl = () => {
        return this.state.showTwoCalendars?`${Constants.apiUrl()}citas/change_profesional`:`${Constants.apiUrl()}citas/create_citas`;
    };

    _sendCitasToSave = (body, url) => {
        this.requestManager.postMethod(url, body)
            .then(response => {
                this._apiResponseManager();
            })
            .catch(error => {
             this._openErrorAlert(error);
            })
           .finally(
              ()=>this.setState({loading:false})
            );
    }
    
    
    _apiResponseManager=()=>{
        if(this.state.showTwoCalendars){
            this.setState({
                profesional_calendar:[],
                profesional_calendar_2:[]
            })
        }else{
            this._removeCitasInSessionStorage();
        }
    }
    rendeChangeFormButton=()=>{
        return(
            <p onClick={this.ChangeForm}>Cambiar Formulario</p>  
        )
    }
    _removeCitasInSessionStorage = () => {
        sessionStorage.removeItem('citasCanceled');
        this.setState({
            showTwoCalendars: true,
            citaWasCheckedToUnavailable: false
        });
    };
    

    render() {
        return (
            <div className="reassing-citas-form">
                <div className="reassing-citas-form-header">
                    <p>REASIGNADOR DE CITAS</p>   
                </div>
                <div className="reassing-citas-change-form">
                    { this.rendeChangeFormButton() } 
                </div>
                <form>
                    <div className="reassing-citas-form-body">
                        <div className="insert-citas-canceled-avaiables-or-profesionals-form">
                            {this.state.showTwoCalendars?this.renderTwoProfesionalsForm():this.renderTableCitasCanceled()}
                        </div>
                        <div className="search-profesional-by-name-and-insert-calendar">
                            {!this.state.showTwoCalendars&&!this.state.citaWasChecjedToUnavaible?this.renderformCreateCitasAndSeconFormPro():null}
                        </div>
                        <div className="insert-schedule-profesional-selected">
                            {this.showProfesionalSchedule()}
                        </div>
                        <div className="reassing-citas-form-footer">
                            <a onClick={() => this.handleReassignCitasButton()}>
                                {this.state.loading?'Cargando...':this.state.citaWasChecjedToUnavaible ? 'Inactivar Cita' :
                                (this.state.reasing? 'Reasignando...' : 'Reasignar')}
                            </a>
                        </div>
                    </div>
                </form>

                <UpWarning
                    isOpen={this.state.warningIsOpen}
                    onClose={() => this.setState({ warningIsOpen: false })}
                    errorMessage={this.state.errorMessage}
                /> 

                <AlertSchedule
                    isOpen={this.state.AlertIsOpen}
                    onClose={() => this.setState({ AlertIsOpen: false })}
                    alertMessage={this.state.alertMessage}
                    onAccept={this.onAccept}
                />
            </div>
        );
    }
}

export default ReassingCitasForm;
