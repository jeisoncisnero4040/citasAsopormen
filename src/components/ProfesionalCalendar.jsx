import React, { Component } from 'react';
import FullCalendar from '@fullcalendar/react';  
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import Modal from 'react-modal';  
import '../styles/CalerdarProfesional.css';
import eliminar from '../assets/eliminar.png'
import ApiRequestManager from '../util/ApiRequestMamager.js';
import Constants from '../js/Constans.jsx';
import buscar from "../assets/buscar.jpg";
import Warning from './Warning.jsx';

 
Modal.setAppElement('#root');

class ProfesionalCalendar extends Component {
    requestManager=new ApiRequestManager();
    
    constructor(props) {
        super(props);
    
    const today = new Date().toISOString().split('T')[0]; 
    this.state = {
        startDate: today,
        endDate: today,
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
        showError:false,
        showAgreeButtons:false,
        idSelected:'',
        loading:false
    }
    }
    handleStartDateChange = (event) => {
        this.setState({ startDate: event.target.value });
    };

    handleEndDateChange = (event) => {
        this.setState({ endDate: event.target.value });
    };
    getBody = () => {
        return {
            'cedula': this.props.cedulaProfesional,
            'startDate': this.state.startDate,
            'endDate': this.state.endDate
        };
    }
    
    getCalendarProfesional = () => {
        const body = this.getBody();
        this.setState({ loading: true });
        const url = `${Constants.apiUrl()}citas/get_citas_profesional`;
    
        this.requestManager.postMethod(url, body)
            .then(response => {
                this.setState(
                    () => this.props.getUpdateCalendarPro(response.data.data)
                );
            })
            .catch(error => {
                this.setState({
                    errorMessage: error,
                    warningIsOpen: true
                });
            })
            .finally(() => { 
                this.setState({ loading: false });
            });
    }
    insertCitasInTable = () => {
        return (
            <tbody className='body-table'>
                {this.state.eventDetails.map((event, index) => {
                    
                    const estado = event.asistida === '1'
                        ? 'Asistida'
                        : event.cancelada === '1'
                        ? 'Cancelada'
                        : event.no_asistida === '1'
                        ? 'NoAsistio'
                        : 'Programada';
    
                    return (
                        <tr className={estado} key={index}>
                             
                            <td>
                                {`${new Date(event.start).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${new Date(event.end).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`}
                            </td> 
                            <td>{event.usuario ? event.usuario : 'N/A'}</td>
                            <td>{event.procedimiento ? event.procedimiento : 'N/A'}</td>
                            <td className='iconos-cita-cli'>
                                 
                                <a onClick={() => this.handleDeleteClick(event.id,event.tiempo)}>
                                    <img className='icono-citas-cli' src={eliminar} alt="eliminar" />
                                </a>
                            </td>
                        </tr>
                    );
                })}
            </tbody>
        );
    };


    deleteCitaById=(id)=> {

        const url = `${Constants.apiUrl()}citas/${id}`;
        this.requestManager.deleteMethod(url)
            .then(response => {
                this.closeModal();
                const newFilteredEvents=this.state.eventDetails.filter(event=>event.id !==id);
                this.props.getUpdateCalendarPro(
                    this.props.events.filter(event => event.id !== id),
                    this.state.tiempoCitaSelected
                );

                 
                this.setState({ tiempoCitaSelected: '',idSelected:'' });
                if (newFilteredEvents.length > 0){
                    this.openModal(newFilteredEvents,this.state.selectedday);
                }
                

            })
            .catch(error => {
                if (error) {
                    
                    this.setState({
                        error: error,
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
    
        this.requestManager.postMethod(url, {
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
            this.setState({
                errorMessage: error,
                warningIsOpen: true,
            });
        });
    }
    handleDeleteAllCitasClick=()=>{
        this.openAgreeButtons()
    }

    handleDeleteClick= (id, tiempo) =>{
        this.setState({
            tiempoCitaSelected:tiempo,
            idSelected:id
        }
        )
        this.openAgreeButtons()
        

    }
    openAgreeButtons = () => {
        this.setState({ showAgreeButtons: true });
    }
    
    closeAgreeButtons = () => {
        this.setState({ showAgreeButtons: false });
    }
    
    acceptWarnings = () => {
        
        if (this.state.tiempoCitaSelected) {
            this.deleteCitaById(this.state.idSelected);
        } else {
            this.deleteCitasByday(); 
        }
        this.closeAgreeButtons(); 
    }
    insertAgreeField = (id, tiempo) => {
        return (
            <div className='agree'>
                <p>Esta acción es irreversible, ¿estás seguro de que deseas continuar?</p>
                <div className='agree-buttons'>
                    <button className='agree-button' onClick={this.closeAgreeButtons}>No</button>
                    <button className='agree-button' onClick={() => this.acceptWarnings(id, tiempo)}>Sí</button>
                </div>
            </div>
        );
    }


 
    render() {
        const { modalIsOpen, eventDetails } = this.state;

        return (
            <div className="calendar-container">
                <p>{this.props.nameProfesional}</p>
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
                    <div className="get-schedule-client">
                        {this.state.loading?<img src={buscar} alt="buscar" />:<a onClick={this.getCalendarProfesional}><img src={buscar} alt="buscar" /></a>}
                    </div>
                    <div className='get-pdf'>
                        {this.state.loading ? (
                            <p className='search'>Buscando...</p>
                        ) :null}
                    </div>
                    
                </div>
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
                                <a onClick={this.handleDeleteAllCitasClick}>
                                    <img className='delete-citas' src={eliminar} alt="eliminar" />
                                </a>
                                 
                             </div>  
                            
                            <div className='table-pro'>
                                <table>
                                    <thead >
                                        <tr>
                                            <th className='date'>Hora</th>
                                            <th>Paciente</th>
                                            <th>Procedimiento</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>

                                        {this.insertCitasInTable()} 

                                    
                                </table>
                            </div>
                            
                            <button className="close-button" onClick={this.closeModal}>Cerrar</button>  
                            <p className='p-error'>{this.state.showError?this.state.error:null}</p>
                            {this.state.showAgreeButtons?this.insertAgreeField():null}
                        </div>
                    </Modal>
                </div>
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

export default ProfesionalCalendar;