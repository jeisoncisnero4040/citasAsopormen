import React, { Component } from 'react';
import FullCalendar from '@fullcalendar/react';  
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import '../styles/CalerdarProfesional.css';
import '../styles/ClientCalendar.css';
import Constants from '../js/Constans.jsx';
import Warning from './Warning.jsx';
import axios from 'axios';


class ClientCalendar extends Component {
    constructor(props) {
        super(props);
        const today = new Date().toISOString().split('T')[0]; 
        this.state = {
            startDate: today,
            endDate: today,
            calendar: {},
            errorMessage: '',
            warningIsOpen: false,
        };
    }

    handleStartDateChange = (event) => {
        this.setState({ startDate: event.target.value });
    };

    handleEndDateChange = (event) => {
        this.setState({ endDate: event.target.value });
    };
    getScheduleClient=()=>{
        
        const body=this.getBody();
        this.fetchDataAuthorization(body)


    }

    getBody=()=>{
        return {
            'codigo':this.props.codigo,
            'startDate':this.state.startDate,
            'endDate':this.state.endDate
        }
    }

    fetchDataAuthorization = (body) => {
        const url = `${Constants.apiUrl()}citas/get_citas_client`;
        alert(JSON.stringify(body))
        axios.post(url,body)
            .then(response => {
                this.handleClientCalendarSucces(response.data.data);
            })
            .catch((error) => {
                this.handleAuthorizationsError(error);
            });
    }
    handleClientCalendarSucces=(calendar)=>{
        this.setState(
            {calendar:calendar}
        )
    }
    handleAuthorizationsError = (error) => {
        if (error.response) {
            const errorData = error.response.data;
            this.setState({
                errorMessage: errorData.error ? JSON.stringify(errorData.error) : 'Error al hacer la petición',
                warningIsOpen: true,
            });
        }
    }

    render() {
        return (
            <div className="calendar-container">
                <p>{this.props.nameClient}</p>
                <div className="inputs-date">
                    <div className="get-date">
                        <label>Fecha inicial</label>
                        <input
                            type="date"
                            value={this.state.startDate}
                            onChange={this.handleStartDateChange}
                        />
                    </div>
                    <div className="get-date">
                        <label>Fecha final</label>
                        <input
                            type="date"
                            value={this.state.endDate}
                            onChange={this.handleEndDateChange}
                        />
                    </div>
                    <div className='get-schedule-client'>
                        <a onClick={this.getScheduleClient}>obtener horario</a>
                    </div>
                </div>
                <FullCalendar
                    plugins={[dayGridPlugin, interactionPlugin]}
                    events={this.state.calendar}
                    locale="es"
                />
                <div>
                    <Warning
                        isOpen={this.state.warningIsOpen}
                        onClose={() => this.setState({ warningIsOpen: false })}
                        errorMessage={this.state.errorMessage}
                    />
                </div>
            </div>
        );
    }
}

export default ClientCalendar;
