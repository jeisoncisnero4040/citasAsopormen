import React, { Component } from 'react';
import FullCalendar from '@fullcalendar/react';  
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import Modal from 'react-modal';  
import '../styles/CalerdarProfesional.css';

 
Modal.setAppElement('#root');

class ProfesionalCalendar extends Component {
    constructor(props) {
        super(props);
    }
    state = {
        modalIsOpen: false,
        eventDetails: []
    }

 
    openModal = (filteredEvents) => {
        this.setState({
            modalIsOpen: true,
            eventDetails: filteredEvents
        });
    }

 
    closeModal = () => {
        this.setState({ modalIsOpen: false });
    }

    handleEventClick = (info) => {
        const { start } = info.event;
        const eventDate = new Date(start);

        const filteredEvents = this.props.events.filter(event => {
            const eventDay = new Date(event.start);
            return eventDay.getDate() === eventDate.getDate() &&
                   eventDay.getMonth() === eventDate.getMonth() &&
                   eventDay.getFullYear() === eventDate.getFullYear();
        });

        this.openModal(filteredEvents); 
    }

    render() {
        const { modalIsOpen, eventDetails } = this.state;

        return (
            <div className="calendar-container">
                <p>{this.props.nameProfesional}</p>
                <FullCalendar
                    plugins={[dayGridPlugin, interactionPlugin]}
                    events={this.props.events}
                    eventClick={this.handleEventClick}  
                    locale="es"
                />

                <Modal
                    isOpen={modalIsOpen}
                    onRequestClose={this.closeModal}
                    contentLabel="Event Details"
                    className="modal"
                    overlayClassName="overlay"
                >
                    <div className="modal-content">
                        <h4>{`Estado de horario de ${this.props.nameProfesional}`}</h4>

                        
                        <table>
                            <thead>
                                <tr>
                                    <th className='date'>Hora</th>
                                    <th>Paciente</th>
                                    <th>Procedimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                {eventDetails.map((event, index) => {
                                    
                                    const [paciente, procedimiento] = event.title.split('-');  

                                    return (
                                        <tr key={index}>
                                            <td>
                                                {`${new Date(event.start).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${new Date(event.end).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`}
                                            </td>
                                            <td>{paciente ? paciente.trim() : 'N/A'}</td>
                                            <td>{procedimiento ? procedimiento.trim() : 'N/A'}</td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                        <button className="close-button" onClick={this.closeModal}>Cerrar</button>
                    </div>
                </Modal>
            </div>
        );
    }
}

export default ProfesionalCalendar;
