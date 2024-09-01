import React, { Component } from "react";
import '../styles/selectProfesional.css';
import '../styles/SelectOrder.css';
import Constants from '../js/Constans.jsx';
import ProfesionalCalendar from "./ProfesionalCalendar.jsx";
import ClientCalendar from "./ClienCalendar.jsx";
import axios from 'axios';
import SelectDataClient from "./SelectDataClient.jsx";
import Warning from "./Warning";
import TableOrders from "./TableOrders.jsx";
import Procedures from "./Procedures.jsx";
import SelectCentral from "./SelectCentral.jsx"
import CreateCitaForm from "./CreateCitaForm.jsx";
import AlertSchedule from "./AlertSchedule.jsx";



class FormCitasForm extends Component {
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
            profesional_list: [],
            profesional: {
                name: "",
                speciality: "",
                cedula: '',
            },

            historyNumber:'',
            
            loading: false,
            showClientCalendar:false,
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

            const url = `${Constants.apiUrl()}get_profesionals/${this.state.searchQuery}/`;
            axios.get(url)
                .then(response => {
                    if (response.status === 200) {
                        const profesionalList = response.data.data;
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
                                selectedOption: selectedEcc
                            }, () => {
                                this.getCalendarProfesional(selectedEcc);
                            });
                        } else {
                            this.setState({ profesional_list: profesionalList });
                        }
                    }
                })
                .catch(error => {
                    if (error.response ) {
                        const errorData = error.response.data;
                        this.setState({
                            errorMessage: errorData.error ? errorData.error : 'Error al hacer la petición',
                            warningIsOpen: true,
                        });
                         
                    } 
                })
                .finally(() => {
                    this.setState({ loading: false });
                });
        }
    }
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
                    cedula: selectedProfesional.ecc.trim()
                }
            });
            this.getCalendarProfesional(selectedEcc);
        }
    }
    getCalendarProfesional = (cedulaProfesional) => {
        this.setState({ loading: true });

        const url = `${Constants.apiUrl()}get_profesional_calendar/${cedulaProfesional}/`;

        axios.get(url)
            .then(response => {
                if (response.status === 200) {
                    this.setState({ profesional_calendar: response.data.data });
                     
                } else {
                    alert(`Error: No se encontró el calendario`);
                }
            })
            .catch(() => {
                alert(`Error: No se pudo obtener el calendario`);
            })
            .finally(() => {
                this.setState({ loading: false });
            });
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
            historyNumber:updatedClient.codigo
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
        }, () => {
             
            alert(JSON.stringify(this.state.Authorization));
        });
    }
    
    getCounterCitas=(numCitasUpdated)=>{
        this.setState(
            {AuthorizationcounterCitas:numCitasUpdated},
            ()=>alert(JSON.stringify(this.state.AuthorizationcounterCitas))
        )
        
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

        
        let alertMessage = 'ASTAROSHNA\n';
    
        if (weekdays.includes("sabado") && (hourStart>=12 || hourFinish>=12 )) {
            alertMessage += 'El horario seleccionado es el sábado después de las 12pm. \n';
        }
    
        if (weekdays.includes("domingo")) {
            alertMessage += 'Has seleccionado el día domingo que no es día laborable.\n';
        }
    
        if (alertMessage !=='ASTAROSHNA\n' ) {
            alertMessage += '¿seguro que deseas continuar?';
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

            'cedprof': this.state.profesional.cedula,

            'sede': this.state.central.cod,
            'direccion_cita':this.state.central.direccion,
            
            'nro_hist': this.state.client.codigo,
            'codent': this.state.client.cod_entidad,
            'codent2': this.state.client.convenio,

            'n_autoriza':this.state.Authorization.n_autoriza,
            'procedim':this.state.Authorization.procedim,
            'tiempo':this.state.Authorization.tiempo,

            'procedipro': this.state.procedure.nombre,
            'recordatorio_wsp':this.state.procedure.recordatorio_whatsapp,
            'duration_session':this.state.procedure.duraccion,  

            'regobserva': this.state.schedule.observations,
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
        alert(JSON.stringify(body))
        axios.post(url, body)
           .then(response => {
                this.getCalendarProfesional(body['cedprof'])
           })
           .catch(error => {
               if (error.response) {
                   const errorData = error.response.data;
                   this.setState({
                        errorMessage: errorData.error ? JSON.stringify(errorData.error) : 'Error al hacer la petición',
                        warningIsOpen: true,
                   });
              }
            });
    }
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
                                <div className="get-all-data-pro">
                                    <div className="input-name-pro">
                                        <div className="input-name-profesional">
                                            <label>Buscar Profesional</label>
                                            <input 
                                                type="text"
                                                placeholder="Buscar por nombre"
                                                value={this.state.searchQuery} 
                                                onChange={this.handleInputChange} 
                                                onKeyDown={this.handleKeyDown} 
                                            />
                                        </div>
                                        <div className="select-profesional">
                                            <label>Seleccionar Profesional</label>
                                            <select 
                                                name="select-profesional" 
                                                value={this.state.selectedOption}
                                                onChange={this.handleSelectChange}
                                            >
                                                {this.renderProfesionales()}
                                            </select>
                                        </div>
                                </div>
                                    <div className="info-profesional">
                                        <div className="read-info-p">
                                            <label>Nombre:</label>
                                            <input
                                                type="text"
                                                placeholder={this.state.loading ? 'Buscando...' : this.state.profesional.name}
                                                readOnly
                                            />
                                        </div>
                                        <div className="read-info-p">
                                            <label>Especialidad:</label>
                                            <input
                                                type="text"
                                                placeholder={this.state.loading ? 'Buscando...' : this.state.profesional.speciality}
                                                readOnly
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div className="calendar-small-screen">
                                    <ProfesionalCalendar events={this.state.profesional_calendar} nameProfesional={this.state.profesional.name}/> 
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
                                    <ClientCalendar nameClient={this.state.client.nombre} codigo={this.state.client.codigo} />
                                ) : (
                                    <ProfesionalCalendar events={this.state.profesional_calendar} nameProfesional={this.state.profesional.name} />
                                )}
                            </div>

                        </div>
                        <div className="select-orden">
                            <div className="data-order">
                                <div className="get-order">
                                    <TableOrders 
                                        historyNumber={this.state.historyNumber}
                                        nameClient={this.state.client.nombre}
                                        getAuthorizationInfo={this.getAuthorizationInfo}
                                        getCounterCitas={this.getCounterCitas}
                                    />
                                </div>

                                <div className="get-order">
                                    <SelectCentral getCentralInfo={this.getCentralInfo}/> 
                                </div>
                            </div>
                            <div className="create-cita">
                                <Procedures getProcedureName={this.getProcedureName}/> 
                                <CreateCitaForm 
                                    getScheduleCitas={this.getScheduleCitas}
                                    numCitas={this.state.AuthorizationcounterCitas}
                                /> 
                            </div>
                            

                        </div>

                        <div className="send-citas">
                            <a onClick={this.driveSendCitasRequest }>enviar citas</a>
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
