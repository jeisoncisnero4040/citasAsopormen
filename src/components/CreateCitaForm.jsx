import React, { Component } from "react";
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';
import '../styles/CreateCitaForm.css';

class CreateCitaForm extends Component {
    constructor(props) {
        super(props);
        this.state = {
            dataSchedule: {
                startDate: new Date(),
                weekDays: [],
                sessionsNum: 1,
                observations: '',
                numCitas: '',
            },
            dropdownOpen: false
        };
    }

    componentDidMount() {
        this.props.getScheduleCitas(this.state.dataSchedule);
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
                observations: event.target.value
            }
        }),() => {
            this.props.getScheduleCitas(this.state.dataSchedule);
        });
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
                    <label>Observaciones</label>
                    <textarea
                        placeholder="Inserte observaciones a las citas"
                        onChange={this.handleObservations}
                    />
                </div>
            </div>
        );
    }
}

export default CreateCitaForm;
