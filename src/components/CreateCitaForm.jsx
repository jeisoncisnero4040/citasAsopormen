import React, { Component } from "react";
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';
import '../styles/CreateCitaForm.css';
import ApiRequestManager from "../util/ApiRequestMamager";
import Constans from "../js/Constans";
import Warning from "./Warning";
import Info from "./Info";

class CreateCitaForm extends Component {
    requestManeger=new ApiRequestManager();
    constructor(props) {
        super(props);
        this.state = {
            dataSchedule: {
                startDate: new Date(),
                weekDays: [],
                sessionsNum: 1,
                observationId: '',
                numCitas: '',
                copago:'',
            },
            observations:[],
            dropdownOpen: false,
            observationContent:'',

            errorMessage: '',
            warningIsOpen: false,

            info: '',
            title: '',
            infoIsOpen: false,

        };
    }

    componentDidMount() {
        this.props.getScheduleCitas(this.state.dataSchedule);
        const url = `${Constans.apiUrl()}observa_citas`
        this.requestManeger.getMethod(url)
            .then(response=>{
                this.setObservations(response.data.data);
            })
            .catch(error=>{
                this._openErrorAlert(error)
            })
    }
    
    setObservations=(newObservations)=>{
        this.setState({
            observations:newObservations
        })
    }
    setObservationContent=(newOnservationContent)=>{
        this.setState({
            observationContent:newOnservationContent
        })
    }

    handleStartDateChange = (date) => {
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                startDate: date
            }
        }), () => {
            this.props.getScheduleCitas(this.state.dataSchedule);
        });
    }
    

    handleWeekDaysChange = (event) => {
        const { value, checked } = event.target;
        let updatedWeekDays = [...this.state.dataSchedule.weekDays];
    
        if (checked) {
            updatedWeekDays.push(value);
        } else {
            updatedWeekDays = updatedWeekDays.filter(day => day !== value);
        }
    
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                weekDays: this.sortWeekDays(updatedWeekDays)
            }
        }), () => {
            this.props.getScheduleCitas(this.state.dataSchedule);
        });
    }
    sortWeekDays = (weekDays) => {
        const weekDaysOrder = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        
        return weekDays.sort((a, b) => {
            return weekDaysOrder.indexOf(a) - weekDaysOrder.indexOf(b);
        });
    }

    toggleDropdown = () => {
        this.setState(prevState => ({
            dropdownOpen: !prevState.dropdownOpen
        }));
    }

    renderListNumSessions() {
        return Array.from({ length: 8 }, (_, i) => {
            const numeroSessions = i + 1;
            return (
                <option key={numeroSessions} value={numeroSessions}>
                    {numeroSessions}
                </option>
            );
        });
    }
    renderObservations = () => {
        if (this.state.observations.length > 0) {
            return (
                <>
                    <option value="">Seleccionar Observacion</option> 
                    {this.state.observations.map((observation, index) => (
                        <option key={index} value={observation.id}>
                            {observation.nombre.trim()}
                        </option>
                    ))}
                </>
            );
        } else {
            return <option>No se encontraron profesionales</option>;
        }
    };
    
    handleNumSessionByDay = (event) => {
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                sessionsNum: event.target.value
            }
        }), () => {
            this.props.getScheduleCitas(this.state.dataSchedule);
        });
    }

    handleObservations = (event) => {
        
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                observationId: event.target.value
            }
        }), () => {
            this.props.getScheduleCitas(this.state.dataSchedule);
            if (this.state.dataSchedule.observationId !== '') {
                this.getObservationContent(this.state.dataSchedule.observationId);
            }else{
                this.state.observationContent=''
            }
        });
    }
    
    handleCopago=(event)=>{
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                copago: event.target.value
            }
        }),() => {
            this.props.getScheduleCitas(this.state.dataSchedule);
        });
    }
    getObservationContent=(nameObservation)=>{
        const url=`${Constans.apiUrl()}observation/get_observation/${nameObservation}`
        this.requestManeger.getMethod(url)
            .then(response=>{
                this.setObservationContent(response.data.data.contenido)
            })
            .catch(error=>{
                this._openErrorAlert(error)
            })
    }
    handleNumCitas=(event)=>{
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                numCitas: event.target.value
            }
        }),() => {
            this.props.getScheduleCitas(this.state.dataSchedule);
        });
    }
    _openErrorAlert = (error) => {
        this.setState({
            errorMessage: error,
            warningIsOpen: true
        });
    };
    _openInfo = (info, title) => {
        this.setState({
            info,
            title,
            infoIsOpen: true,  
        });
    };
    renderObservationBotton = () => {
        return (
            <a onClick={this._showObservation}>Ver Observaciones</a>
        );
    }
    _showObservation=()=>{
         
        const observationTemplate=this.state.observationContent;
        const copago=this.state.dataSchedule.copago?this.state.dataSchedule.copago:'No aplica';
        const observation=observationTemplate.replace('{{}}',copago)
        
        this._openInfo(observation)
    }
    render() {
        return (
            <div className="select-data-cita-container">
                <div className="get-schedule-cita">
                    <div className="get-date-start">
                        <label>Fecha inicial</label>
                        <DatePicker
                            selected={this.state.dataSchedule.startDate}  
                            onChange={this.handleStartDateChange}  
                            showTimeSelect
                            timeFormat="HH:mm"
                            timeIntervals={15}
                            dateFormat="MMMM d, yyyy h:mm aa"
                            timeCaption="Hora"
                        />
                    </div>
                    <div className="get-days">
                        <label>Seleccione días</label>
                        <div className="custom-dropdown">
                            <button type="button" onClick={this.toggleDropdown}>
                                {this.state.dropdownOpen ? 'Cerrar días' : 'Abrir días'}
                            </button>
                            {this.state.dropdownOpen && (
                                <div className="dropdown-menu">
                                    {['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'].map(day => (
                                        <div key={day}>
                                            <input
                                                type="checkbox"
                                                id={day}
                                                value={day}
                                                checked={this.state.dataSchedule.weekDays.includes(day)}
                                                onChange={this.handleWeekDaysChange}
                                            />
                                            <label htmlFor={day}>{day.charAt(0).toUpperCase() + day.slice(1)}</label>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
                <div className="get-schedule-cita">
                    <div className="select-numtype-sessions">
                        <label>Sesiones:</label>
                        <select onChange={this.handleNumSessionByDay}>
                            {this.renderListNumSessions()}
                        </select>
                    </div>
                    <div className="select-numtype-sessions">
                        <label>Dias</label>
                        <input 
                            placeholder="Numero días"
                            autocomplete="off" 
                            type="text" 
                            onChange={this.handleNumCitas}
                        
                        />
                    </div>
                    <div className="show-num-sessions">
                        <label>Asignadas:</label>
                        <input
                            type="text"
                            placeholder={this.props.numCitas}
                            readOnly
                        />
                    </div>


                </div>
                <div className="observations">
                    <label>Elige la Observacion:</label>
                        <select onChange={this.handleObservations}>
                            
                            {this.renderObservations()}
                        </select>

                    <label>Copago</label>
                    <input 
                        placeholder="ingrese copago"
                        autocomplete="off" 
                        type="text" 
                        onChange={this.handleCopago}
                    
                    />
                    {this.state.observationContent? this.renderObservationBotton():null}

                    
                </div>
                <div>
                    <Warning
                        isOpen={this.state.warningIsOpen}
                        onClose={() => this.setState({ warningIsOpen: false })}
                        errorMessage={this.state.errorMessage}
                    /> 
                </div>
                <div>
                    <Info
                        isOpen={this.state.infoIsOpen}
                        onClose={() => this.setState({ infoIsOpen: false })}
                        info={this.state.info}
                        title={this.state.title}
                    />

                </div>
            </div>
        );
    }
}

export default CreateCitaForm;
