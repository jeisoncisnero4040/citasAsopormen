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
import horario from "../assets/horario.png";
import reiniciar from '../assets/restart.cita.png'
import metadata from '../assets/image.info.png'
 
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
            loading:false,
            deletingCitas:false,
            restartingCitas:false,

            modalMetadataCitasIsOpen:false,
            metadataCita:{}
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
                    () => this.props.getUpdateCalendarPro(response.data.data.calendar),
                    () => this.props.getUpdateSchedulePro(response.data.data.schedule)
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
                            <td>{event.fecha.split(' ')[0]}</td>
                            <td>{event.autorizacion}</td>
                            <td>{event.usuario ? event.usuario : 'N/A'}</td>
                            <td>{event.procedimiento ? event.procedimiento : 'N/A'}</td>
                            <td className='iconos-cita-cli'>
                                {this._renderButtons(event.id, event.tiempo,estado,event.usuario)}
                            </td>
                        </tr>
                    );
                })}
            </tbody>
        );
    };
 


    deleteCitaById=(id,cliente)=> {

        const url = `${Constants.apiUrl()}citas/${id}?usuario=${encodeURIComponent(this.props.usuario)}&profesional=${encodeURIComponent(this.props.nameProfesional)}`;
        this.setState({deletingCitas:true})
        this.requestManager.deleteMethod(url)
            .then(response => {
                this.closeModal();
                const newFilteredEvents=this.state.eventDetails.filter(event=>event.id !==id);
                this.props.getUpdateCalendarPro(
                    this.props.events.filter(event => event.id !== id),
                    this.state.tiempoCitaSelected,
                    'delete'
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
            }).finally(this.setState({deletingCitas:false}));
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
     
        const sortedFilteredEvents = filteredEvents.sort((a, b) => {
            return new Date(a.start) - new Date(b.start);   
        });
     
        this.setState(
            { selectedday: eventDate.toISOString().split('T')[0] },
            () => this.openModal(sortedFilteredEvents, eventDate)
        );
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
            cita.no_asistida === '0'&&
            cita.autorizacion!=="");
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
                citasByTiempo,
                'delete'
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
        })
        .finally(this.setState({deletingCitas:false}));
    }


    handleDeleteClick= (id, tiempo) =>{
        this.setState({
            tiempoCitaSelected:tiempo,
            idSelected:id
        }
        )
        this.openAgreeButtons(id)
        

    }
    _handleRestartCita = (id) => {
        
        const url = `${Constants.apiUrl()}citas/restart/${id}`;
        this.setState({restartingCitas:true})
        this.requestManager.postMethod(url)
            .then(response => {
                this.closeModal();
                const newFilteredEvents = this.state.eventDetails.map(event => {
                    if (event.id === id) {
                        return { ...event, cancelada: '0', no_asistida: '0', asistida: '0' };
                    }
                    return event;
                });
            
                this.props.getUpdateCalendarPro(
                    this.props.events.map(event => {
                        if (event.id === id) {
                            return { ...event, cancelada: '0', no_asistida: '0', asistida: '0' };
                        }
                        return event;
                    }),
                    this.state.tiempoCitaSelected  
                );
            
                this.setState({ tiempoCitaSelected: '', idSelected: '' });
            
                if (newFilteredEvents.length > 0) {
                    this.openModal(newFilteredEvents, this.state.selectedday);
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
            }).finally(this.setState({deletingCitas:false}));

    };
    
    openAgreeButtons = () => {
        this.setState({ showAgreeButtons: true });
    }
    
    closeAgreeButtons = () => {
        this.setState({ showAgreeButtons: false });
    }
    
    acceptWarnings = () => {
        this.deleteCitaById(this.state.idSelected);
        this.closeAgreeButtons(); 
    }
    ChangeToSchedule=()=>{
        this.props.ChangeToSchedule()
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

    
    _renderButtons = (id, tiempo, estado,cliente) => {
        const { deletingCitas, idSelected } = this.state;
    
        if (deletingCitas && idSelected === id) {
            return <p className='search'>Eliminando</p>;
        } else {
            return (
                <div>
                    <a onClick={() =>this._showMetaDataInfoCitaById(id)}>
                        <img className='icono-citas-cli' src={metadata} alt="reiniciar" />
                    </a>
                    {estado === 'Programada' ? (
                        <a onClick={() => this.handleDeleteClick(id, tiempo,cliente)}>
                            <img className='icono-citas-cli' src={eliminar} alt="eliminar" />
                        </a>
                    ) : (
                        <a onClick={() => this._handleRestartCita(id)}>
                            <img className='icono-citas-cli' src={reiniciar} alt="reiniciar" />
                        </a>
                    )}
                </div>
            );
        }
    };
    _showMetaDataInfoCitaById=(id)=>{
        const cita=this.state.eventDetails.filter(event=>event.id===id);
        this._openModalMetadataCita(cita[0])
    }

    insertButtonShowSchedule=()=>{
        return(
            <a onClick={() => this.ChangeToSchedule()}><img src={horario} alt="horario" /></a>
        )
    }
    _openModalMetadataCita=(dataCita)=>{
        this.setState(
            {
                modalMetadataCitasIsOpen:true,
                metadataCita:dataCita
            }
        )
    }
    _closeModalMetadataCita = () => {
        this.setState({
            modalMetadataCitasIsOpen: false,
        });
    };

    

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
                        ) : (
                            this.props.events.length > 0 ? (
                                this.insertButtonShowSchedule()
                            ) : (
                                null
                            )
                        )}
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
                             </div>  
                            
                            <div className='table-pro'>
                                <table>
                                    <thead >
                                        <tr>
                                            <th>Hora</th>
                                            <th>Fecha</th>
                                            <th>Autorizacion</th>
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
                    
                <div className={`modal-info-cita-${this.state.modalMetadataCitasIsOpen ? 'active' : 'inactive'}`}>
                    <h2>INFO CITA {this.state.metadataCita?.id ?? "N/A"}</h2>
                    <p>Fecha de registro: {this.state.metadataCita?.dateSave ?? "No disponible"}</p>
                    <p>Nombre de quien registra: {this.state.metadataCita?.registro ? String(this.state.metadataCita.registro).replace(/\./g, " ") : "No disponible"}</p>
                    <button onClick={() => this._closeModalMetadataCita()} type="button">
                        Cerrar
                    </button>
                </div>
            </div>
        );
    }
}

export default ProfesionalCalendar;
