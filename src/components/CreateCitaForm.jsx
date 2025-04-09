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
                weekDays: {},
                observationId: '',
                numWeeks: '',
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
    handleStartDateChangeByDay=(hour,day)=>{
        let updatedWeekDays = { ...this.state.dataSchedule.weekDays }; 
        updatedWeekDays[day].startHour=hour;
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                weekDays: updatedWeekDays
            }
        }), () => {
            this.props.getScheduleCitas(this.state.dataSchedule);
             
        });
    }

    handleWeekDaysChange = (event) => {
        
        const { value, checked } = event.target;
        let updatedWeekDays = { ...this.state.dataSchedule.weekDays }; 

    
        if (checked) {
             
            updatedWeekDays[value] = { sessions: 1, startHour: '06:00 AM' };  
        } else {
            delete updatedWeekDays[value];  
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
    
         
        const sortedEntries = Object.entries(weekDays).sort(([keyA], [keyB]) => {
            return weekDaysOrder.indexOf(keyA) - weekDaysOrder.indexOf(keyB);
        });
    
         
        return Object.fromEntries(sortedEntries);
    };
    

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
    renderListHours = () => {
        const hoursList = this._generateTimeIntervals();
        return hoursList.map((hour) => (
            <option key={hour} value={hour}>
                {hour}
            </option>
        ));
    };
    _generateTimeIntervals=()=> {
        const startTime = new Date(0, 0, 0, 6, 0); 
        const endTime = new Date(0, 0, 0, 19, 0);  
        const intervals = [];
        
        while (startTime <= endTime) {
            const hours = startTime.getHours();
            const minutes = startTime.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
    
            // Formatear horas y minutos con ceros delante
            const formattedHours = (hours % 12 === 0 ? 12 : hours % 12).toString().padStart(2, '0');
            const formattedMinutes = minutes.toString().padStart(2, '0');
    
            intervals.push(`${formattedHours}:${formattedMinutes} ${ampm}`);
            startTime.setMinutes(startTime.getMinutes() + 15); 
        }
    
        return intervals;
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
    
    handleNumSessionByDay = (event,day) => {
        let updatedWeekDays = { ...this.state.dataSchedule.weekDays }; 
        updatedWeekDays[day].sessions=event.target.value
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                weekDays: updatedWeekDays
            }
        }), () => {
            this.props.getScheduleCitas(this.state.dataSchedule);
        });
    }
    handleStartHourByDay = (event,day) => {
        let updatedWeekDays = { ...this.state.dataSchedule.weekDays }; 
        updatedWeekDays[day].startHour=event.target.value
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                weekDays: updatedWeekDays
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
    handleNumWeeks=(event)=>{
        this.setState(prevState => ({
            dataSchedule: {
                ...prevState.dataSchedule,
                numWeeks: event.target.value
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
    _getSessionByWeek = () => {
        let updatedWeekDays = { ...this.state.dataSchedule.weekDays }; 
        let suma = 0;
        for (const day in updatedWeekDays) {
            suma = suma + parseInt(updatedWeekDays[day].sessions); 
        }
        return suma;
    }
    renderNumSessionsSelect = (day) => {
          

         
        return this.state.dataSchedule.weekDays[day] ? (
            <div className="select-numtype-sessions">
                <select
                    value={this.state.dataSchedule.weekDays[day].sessions}
                    onChange={(e) => this.handleNumSessionByDay(e, day)}
                >
                    {this.renderListNumSessions()}
                </select>
            </div>
        ) : null;
    };
    renderStartHourSelect = (day) => {
          

         
        return this.state.dataSchedule.weekDays[day] ? (
            <div className="select-numtype-sessions">
                <select
                    value={this.state.dataSchedule.weekDays[day].startHour}
                    onChange={(e) => this.handleStartHourByDay(e, day)}
                >
                    {this.renderListHours()}
                </select>
            </div>
        ) : null;
    };
    
    
    
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

                </div>
                <div className="get-days">
                    <label>Seleccione días</label>
                    <div className="custom-dropdown">
                        <button type="button" onClick={this.toggleDropdown}>
                            {this.state.dropdownOpen ? 'Cerrar días' : 'Abrir días'}
                        </button>
                        {this.state.dropdownOpen && (
                        <div className="dropdown-menu">
                            <div className="header-dropdown-selec-days">
                                <div className="header-dropdown-days">Dias</div>
                                <div>sessiones </div>
                                <div>inicio</div>
                            </div>
                            {['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'].map((day) => (
                                <div className="option-day-to-create-schedule" key={day}>
                                    <div className="dropdown-select-days-list-days">
                                        <input
                                            type="checkbox"
                                            id={day}
                                            value={day}
                                            checked={this.state.dataSchedule.weekDays[day]}
                                            onChange={this.handleWeekDaysChange}
                                        />
                                        <label htmlFor={day}>
                                            {day.charAt(0).toUpperCase() + day.slice(1)}
                                        </label>
                                    </div>
                                        {this.renderNumSessionsSelect(day)}
                                        {this.renderStartHourSelect(day)}


                                </div>
                            ))}
                            
                        </div>
                    )}

                    </div>
                </div>
                <div className="get-schedule-cita">
                    <div className="select-numtype-sessions">
                        <label>Semanas</label>
                        <input 
                            placeholder="Numero Semanas"
                            autocomplete="off" 
                            type="text" 
                            onChange={this.handleNumWeeks}
                        
                        />
                    </div>
                    <div className="show-num-sessions">
                        <label>num Semanas:</label>
                        <input
                            type="text"
                            placeholder={this._getSessionByWeek()}
                            readOnly
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