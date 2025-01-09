import React from 'react';
import { Calendar, momentLocalizer } from 'react-big-calendar';
import moment from 'moment';
import 'react-big-calendar/lib/css/react-big-calendar.css';  
import '../styles/ProfesionalSchedule.css';
import horario from "../assets/horario.png";
import ampliar from "../assets/ampliar.png";
import 'moment/locale/es'
import ApiRequestManager from '../util/ApiRequestMamager';
import Warning from './Warning';
import Constants from '../js/Constans.jsx';

moment.locale('es');
const localizer = momentLocalizer(moment);

class ProfesionalSchedule extends React.Component {
    requestManager=new ApiRequestManager()
    constructor(props) {
        super(props);
        this.state = {
            profesional_calendar: [],
            sizeCalendar: 400,
            disponibilitySchedule:[],
            loading:false,
            warningIsOpen:false,
            errorMessage:'',
            showScheduleProfesional:true
        };
    }

    componentDidMount() {
        if (this.props.events && Array.isArray(this.props.events)) {
            const mappedEvents = this.props.events.map(event => {
                const filteredEvent = Object.keys(event).reduce((acc, key) => {
                     
                    if (['start', 'end', 'title', 'color'].includes(key)) {
                        acc[key] = event[key];
                    }
                    return acc;
                }, {});

                filteredEvent.start = new Date(filteredEvent.start);
                filteredEvent.end = new Date(filteredEvent.end);
                return filteredEvent;
            });
    
            this.setState({ profesional_calendar: mappedEvents });
        }
    }
    
    componentDidUpdate(prevProps) {
        if (prevProps.events !== this.props.events) {
            const mappedEvents = this.props.events.map(event => {
                const filteredEvent = Object.keys(event).reduce((acc, key) => {
                    
                    if (['start', 'end', 'title', 'color'].includes(key)) {
                        acc[key] = event[key];
                    }
                    return acc;
                }, {});
                 
                filteredEvent.start = new Date(filteredEvent.start);
                filteredEvent.end = new Date(filteredEvent.end);
                return filteredEvent;
            });
    
            this.setState({ profesional_calendar: mappedEvents });
        }
    }
    
    setDisponibilitySchedule = (newSchedule) => {
        this.setState({ disponibilitySchedule: newSchedule });
      };
    
    setLoading = (isLoading) => {
        this.setState({ loading: isLoading });
    };

    setWarningIsOpen = (isOpen) => {
        this.setState({ warningIsOpen: isOpen });
    };

    setErrorMessage = (message) => {
        this.setState({ errorMessage: message });
    };

    setShowScheduleProfesional = (isVisible) => {
        this.setState({ showScheduleProfesional: isVisible });
    };
    

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
    getDisponibility = () => {
        
        const body = this.makeBodyToGetDisponibility();
        const url = `${Constants.apiUrl()}clients/get_forbiden_blocks`;
        
    
        this.setLoading(true)
        this.requestManager.postMethod(url, body) 
            .then(response => {
                const scheduleMapped=this.mapDisponibilitySchedule(response.data.data)
                console.log(response.data.data)
                this.setDisponibilitySchedule(scheduleMapped);
                
                
                this.setState(prevState => ({
                    profesional_calendar: prevState.profesional_calendar.concat(scheduleMapped)
                }));
            })
            .catch(error => {
                this._openWarning(error);
            })
            .finally(() => {
                this.setLoading(false)
            });
    };

    makeBodyToGetDisponibility = () => {
        const { profesional_calendar } = this.state;
        
        
        if (profesional_calendar.length === 0) {
            return {};
        }
    
        const lenProfesional_calendar = profesional_calendar.length;
        const start = profesional_calendar[0].start; 
        const end = profesional_calendar[lenProfesional_calendar - 1].end;
    
        return {
            "start": start,
            "end": end,
            "cedula": this.props.profesional.cedula
        };
    };
    mapDisponibilitySchedule = (schedule) => {
        alert('aqui');
        return schedule.map(event => ({
            ...event,  
            start: new Date(event.start),  
            end: new Date(event.end) 
        }));
    };
    
    _openWarning=(error)=>{
        this.setWarningIsOpen(true)
        this.setErrorMessage(error)
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
                    <p>{"Horario de " + this.props.profesional.name}</p>
                    <div className='calendar-iconos'>
                        {/**{this.state.loading?<p>Cargando...</p>:<a onClick={()=>this.getDisponibility()}>Mostrar bloques no disponibles </a>}**/}
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
                    eventPropGetter={this.eventStyleGetter} 
                />
                {this.renderFooterCalendar()}
                <div>
                    <Warning
                        isOpen={this.state.warningIsOpen}
                        onClose={() => this.setWarningIsOpen(false)}
                        errorMessage={this.state.errorMessage}
                    /> 
                </div>
            </div>
        );
    }
}

export default ProfesionalSchedule;
