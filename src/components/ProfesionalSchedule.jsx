import React from 'react';
import { Calendar, momentLocalizer } from 'react-big-calendar';
import moment from 'moment';
import 'react-big-calendar/lib/css/react-big-calendar.css';  
import '../styles/ProfesionalSchedule.css';
import horario from "../assets/horario.png";
import ampliar from "../assets/ampliar.png";
import 'moment/locale/es'

moment.locale('es');
const localizer = momentLocalizer(moment);

class ProfesionalSchedule extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            profesional_calendar: [],
            sizeCalendar: 400
        };
    }

    componentDidMount() {
        if (this.props.events && Array.isArray(this.props.events)) {
            const mappedEvents = this.props.events.map(event => ({
                ...event,
                start: new Date(event.start),
                end: new Date(event.end)
            }));

            this.setState({ profesional_calendar: mappedEvents });
        }
    }

    componentDidUpdate(prevProps) {
        if (prevProps.events !== this.props.events) {
            const mappedEvents = this.props.events.map(event => ({
                ...event,
                start: new Date(event.start),
                end: new Date(event.end)
            }));

            this.setState({ profesional_calendar: mappedEvents });
        }
    }

    ChangeToSchedule = () => {
        this.props.ChangeToSchedule();
    }

    ChangeSizeCalendar = () => {
        this.setState({ sizeCalendar: (this.state.sizeCalendar === 400 ? 'auto' : 400) });
    }

    renderFooterCalendar = () => {
        return (
            this.state.sizeCalendar === 'auto' ? (
                <div className='schedule-profesional-footer'>
                    <div className='calendar-iconos'>
                        <a onClick={() => this.ChangeToSchedule()}>
                            <img src={horario} alt="horario" />
                        </a>
                        <a onClick={() => this.ChangeSizeCalendar()}>
                            <img src={ampliar} alt="ampliar" />
                        </a>
                    </div>
                </div>
            ) : null
        );
    }

    // Función para asignar estilos personalizados a los eventos usando el color de la API
    eventStyleGetter = (event, start, end, isSelected) => {
        const backgroundColor = event.color || '#3174ad';  // Usar el color del evento, o un valor por defecto

        return {
            style: {
                backgroundColor,
                color: 'white',  // Color del texto
                borderRadius: '5px',
                border: 'none'
            }
        };
    }

    render() {
        const messages = {
            next: "Siguiente",
            previous: "Anterior",
            today: "Hoy",
            month: "Mes",
            week: "Semana",
            day: "Día",
            agenda: "Agenda",
            date: "Fecha",
            time: "Hora",
            event: "Evento",
            noEventsInRange: "No hay eventos en este rango",
            allDay: "Todo el día",
        };

        return (
            <div className='profesional-schedule'>
                <div className='schedule-profesional-header'>
                    <p>{"Horario de " + this.props.profesional}</p>
                    <div className='calendar-iconos'>
                        <a onClick={() => this.ChangeToSchedule()}>
                            <img src={horario} alt="horario" />
                        </a>
                        <a onClick={() => this.ChangeSizeCalendar()}>
                            <img src={ampliar} alt="ampliar" />
                        </a>
                    </div>
                </div>
                <Calendar
                    localizer={localizer}
                    events={this.state.profesional_calendar}
                    startAccessor="start"
                    endAccessor="end"
                    defaultView="week"
                    views={['week', 'day']}
                    step={15}
                    min={new Date(2024, 8, 28, 6, 0)}
                    max={new Date(2024, 8, 28, 19, 0)}
                    messages={messages}
                    style={{ height: this.state.sizeCalendar, width: '100%' }}
                    eventPropGetter={this.eventStyleGetter}  // Usar el estilo personalizado
                />
                {this.renderFooterCalendar()}
            </div>
        );
    }
}

export default ProfesionalSchedule;
