import React, { Component } from 'react';
import FullCalendar from '@fullcalendar/react';  
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import Modal from 'react-modal';  
import '../styles/CalerdarProfesional.css';
import eliminar from '../assets/eliminar.png'
import axios from 'axios';
import Constants from '../js/Constans.jsx';

 
Modal.setAppElement('#root');

class ProfesionalCalendar extends Component {
    constructor(props) {
        super(props);
    }
    state = {
        modalIsOpen: false,
        eventDetails: [],
        selectedday: new Date().toISOString().split('T')[0],
        warningIsOpen:false,
        warningMessage:'',
        alertIsOpen:false,
        alertMessage:'',
        canContine:false,
        idToDelete:'',
        tiempoCitaSelected:'',
        error:'',
        showError:false
    }

    deleteCitaById=(id)=> {

        const url = `${Constants.apiUrl()}citas/${id}/`;
        axios.delete(url)
            .then(response => {
                this.closeModal();
                const newFilteredEvents=this.state.eventDetails.filter(event=>event.id !==id);
                this.props.getUpdateCalendarPro(
                    this.props.events.filter(event => event.id !== id),
                    this.state.tiempoCitaSelected
                );

                 
                this.setState({ tiempoCitaSelected: '' });
                if (newFilteredEvents.length > 0){
                    this.openModal(newFilteredEvents,this.state.selectedday);
                }
                

            })
            .catch(error => {
                if (error.response) {
                    const errorData = error.response.data;
                    this.setState({
                        error: errorData.error ? errorData.error : 'Error al hacer la petición',
                        showError: true
                    }, () => {
                         
                        setTimeout(() => {
                            this.setState({ showError: false });
                        }, 1000); 
                    });
                }
            });
    }
    openModal = (filteredEvents,eventDate) => {
        this.setState({
            modalIsOpen: true,
            eventDetails: filteredEvents,
            selectedDay:eventDate
        });
    }
    closeModal = () => {
        this.setState({ modalIsOpen: false });
    }
    filterCitasByDay = (info) => {
        const { start } = info.event;
        const eventDate = new Date(start);



        const filteredEvents = this.props.events.filter(event => {
            const eventDay = new Date(event.start);
            return eventDay.getDate() === eventDate.getDate() &&
                   eventDay.getMonth() === eventDate.getMonth() &&
                   eventDay.getFullYear() === eventDate.getFullYear();
        });
        this.setState(
            {selectedday:eventDate.toISOString().split('T')[0]},
            ()=>this.openModal(filteredEvents,eventDate)
        )

        
    }
    handleDeleteClick = (id, tiempo) => {
         
        this.setState({ tiempoCitaSelected: tiempo }, () => {
            this.deleteCitaById(id);
        });
    };
    deleteCitasByday = () => {
        const citasDeletables = this.filterDeletablesCitas();
        const citasNotDeletables = this.filterCitasNotDeletables(citasDeletables);
        const citasByTiempo = this.getCitasByTiempo(citasDeletables);

        this.deleteCitas(citasDeletables,citasNotDeletables,citasByTiempo);
    }
    filterDeletablesCitas = () => {
        return this.state.eventDetails.filter(cita => 
            cita.cancelada === '0' && 
            cita.asistida === '0' && 
            cita.no_asistida === '0'
        );
    }
    
    filterCitasNotDeletables = (citasDeletables) => {
        return this.state.eventDetails.filter(cita => 
            !citasDeletables.includes(cita)
        );
    }
    
    getCitasByTiempo = (citasDeletables) => {
        let citasByTiempo = {};
    
        citasDeletables.forEach(citaDeletable => {
            const tiempo = citaDeletable.tiempo; 
            citasByTiempo[tiempo] ? citasByTiempo[tiempo] += 1 : citasByTiempo[tiempo] = 1;
        });
    
        return citasByTiempo;
    }
    
    deleteCitas = (citasDeletables, citasNotDeletables, citasByTiempo) => {
         
        const url = `${Constants.apiUrl()}citas/delete_all_citas`;
    
        axios.post(url, {
            'profesional_identity': this.props.cedulaProfesional,
            'day': this.state.selectedday
        })
        .then(response => {

            this.closeModal();

            this.props.getUpdateCalendarPro(
               this.props.events.filter(cita => 
                   !citasDeletables.some(deletable => deletable.id === cita.id)
                ), 
                citasByTiempo
            );

    
            if (citasNotDeletables.length > 0) {

                this.openModal(citasNotDeletables, this.state.selectedday);
            }
        })
        .catch(error => {
            if (error.response) {
                const errorData = error.response.data;
                this.setState({
                    errorMessage: errorData.error ? errorData.error : 'Error al hacer la petición',
                    warningIsOpen: true,
                });
            }
        });
    }


 
    render() {
        const { modalIsOpen, eventDetails } = this.state;

        return (
            <div className="calendar-container">
                <p>{this.props.nameProfesional}</p>
                <FullCalendar
                    plugins={[dayGridPlugin, interactionPlugin]}
                    events={this.props.events}
                    eventClick={this.filterCitasByDay}  
                    locale="es"
                />
                <div>
                    <Modal
                        isOpen={modalIsOpen}
                        onRequestClose={this.closeModal}
                        contentLabel="Event Details"
                        className="modal"
                        overlayClassName="overlay"
                    >
                        <div className="modal-content">
                            <div className='header-t-p'>
                                <h4>{`Estado de horario de ${this.props.nameProfesional}`}</h4>
                                <a onClick={this.deleteCitasByday}>
                                    <img className='delete-citas' src={eliminar} alt="eliminar" />
                                </a>
                                 
                             </div>  
                            
                            
                            <table>
                                <thead>
                                    <tr>
                                        <th className='date'>Hora</th>
                                        <th>Paciente</th>
                                        <th>Procedimiento</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {eventDetails.map((event, index) => {
                                        
                                        const [paciente, procedimiento] = event.title.split('-');  

                                        return (
                                            <tr key={index} id={event.id}>
                                                <td>
                                                    {`${new Date(event.start).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${new Date(event.end).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`}
                                                </td>
                                                <td>{paciente ? paciente.trim() : 'N/A'}</td>
                                                <td>{procedimiento ? procedimiento.trim() : 'N/A'}</td>
                                                <td className='iconos'>
                                                    <a onClick={() => this.handleDeleteClick(event.id,event.tiempo)}>
                                                        <img className='icono-table' src={eliminar} alt="eliminar" />
                                                    </a>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                            <button className="close-button" onClick={this.closeModal}>Cerrar</button>
                                
                            <p>{this.state.showError?this.state.error:null}</p>

                                    
                            
                        </div>
                    </Modal>
                </div>
            </div>
        );
    }
}

export default ProfesionalCalendar;
