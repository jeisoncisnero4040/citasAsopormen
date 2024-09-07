import React, { Component } from 'react';
import FullCalendar from '@fullcalendar/react';  
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import '../styles/CalerdarProfesional.css';
import '../styles/ClientCalendar.css';
import Constants from '../js/Constans.jsx';
import Warning from './Warning.jsx';
import buscar from "../assets/buscar.jpg";
import pdf from "../assets/pdf.webp";
import eliminar from "../assets/eliminar.png";
import cancelar from "../assets/cancelar.png"
import axios from 'axios';
import Modal from 'react-modal';  
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';



Modal.setAppElement('#root');
pdfMake.vfs = pdfFonts.pdfMake.vfs;
class ClientCalendar extends Component {
    constructor(props) {
        super(props);
        const today = new Date().toISOString().split('T')[0]; 
        this.state = {
            startDate: today,
            endDate: today,
            errorMessage: '',
            warningIsOpen: false,
            modalIsOpen: false,
            eventDetails: [],
            loading:false,
            tiempoCitaSelected:'',
            error:'',
            showError:false
        };
    }
    handleStartDateChange = (event) => {
        this.setState({ startDate: event.target.value });
    };

    handleEndDateChange = (event) => {
        this.setState({ endDate: event.target.value });
    };

    getScheduleClient = () => {
        const body = this.getBody();
        this.fetchDataAuthorization(body);
    }

    getBody = () => {
        return {
            codigo: this.props.codigo,
            startDate: this.state.startDate,
            endDate: this.state.endDate
        };
    }
    fetchDataAuthorization = (body) => {
        const url = `${Constants.apiUrl()}citas/get_citas_client`;
        alert(JSON.stringify(body));
    
        this.setState({ loading: true });
    
        axios.post(url, body)
            .then(response => {
                this.handleClientCalendarSuccess(response.data.data);
            })
            .catch(error => {
                this.handleAuthorizationError(error);
            })
            .finally(() => {
                this.setState({ loading: false }); 
            });
    }
    

    handleClientCalendarSuccess = (calendar) => {
        this.setState(
            ()=>this.props.getCalendarClient(calendar)
        );
    }

    handleAuthorizationError = (error) => {
        if (error.response) {
            const errorData = error.response.data;
            this.setState({
                errorMessage: errorData.error ? JSON.stringify(errorData.error) : 'Error al hacer la petición',
                warningIsOpen: true,
            });
        }
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

    openModal = (filteredEvents) => {
        console.log(filteredEvents)
        this.setState({
            modalIsOpen: true,
            eventDetails: filteredEvents
        });
    }

    closeModal = () => {
        this.setState({ modalIsOpen: false });
    }

    generatePDF = (data,name) => {
        const docDefinition = {
            content: [
                { text: 'Reporte '+ name, style: 'header',alignment: 'center' },
    
                 
                { text: 'Detalle de Citas', style: 'subheader' },
    
                 
                { text: ' ', margin: [0, 10] },

                {
                    table: {
                        headerRows: 1,  
                        body: [
                            ['Fecha Inicio', 'Duración', 'Profesional', 'Procedimiento', 'Observaciones', 'estado'],
                            ...data.map(cita => [
                                { text: new Date(cita.start).toLocaleString([], {
                                    day: '2-digit',
                                    month: 'numeric',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }), style: 'tableBody' },
                                { text: cita.duracion + ' mins', style: 'tableBody' },
                                { text: cita.profesional, style: 'tableBody' },
                                { text: cita.procedimiento, style: 'tableBody' },
                                { text: cita.observaciones || 'N/A', style: 'tableBody' },
                                { text: cita.asistida === '1' ? 'Asistida' : (cita.cancelada === '1' ? 'Cancelada' : (cita.no_asistida === '1' ? 'No Asistió' : 'Programada')), style: 'tableBody' }
                            ])
                        ]
                    },
                    layout: 'lightHorizontalLines',  
                }
            ],
            styles: {
                header: {
                    fontSize: 18,
                    bold: true,
                    margin: [0, 0, 0, 10]
                },
                subheader: {
                    fontSize: 14,
                    bold: true,
                    margin: [0, 10, 0, 10]
                },
                tableBody: {
                    fontSize: 10
                }
            }
        };
        pdfMake.createPdf(docDefinition).open();
    };
    handleDeleteClick(id,tiempo){
        this.setState(
            {
                tiempoCitaSelected:tiempo,

            },()=>this.deleteCitaById(id)
        )
    }
    deleteCitaById=(id)=> {

        const url = `${Constants.apiUrl()}citas/${id}/`;
        axios.delete(url)
            .then(response => {
                this.closeModal();
                const newFilteredEvents=this.state.eventDetails.filter(event=>event.id !==id);
                this.props.getCalendarClient(
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
                    <div className="get-schedule-client">
                        {this.state.loading?<img src={buscar} alt="buscar" />:<a onClick={this.getScheduleClient}><img src={buscar} alt="buscar" /></a>}
                    </div>
                    <div className='get-pdf'>
                        {this.state.loading ? (
                            <p className='search'>Buscando...</p>
                        ) : (
                            this.props.events.length > 0 ? (
                                <a onClick={() => this.generatePDF(this.props.events, this.props.nameClient)}><img src={pdf} alt="pdf" /></a>
                            ) : (
                                null
                            )
                        )}
                    </div>
                    
                </div>
                
                <FullCalendar
                    plugins={[dayGridPlugin, interactionPlugin]}
                    events={this.props.events}
                    eventClick={this.handleEventClick}  
                    locale="es"
                />
                <Modal
                    isOpen={this.state.modalIsOpen}
                    onRequestClose={this.closeModal}
                    contentLabel="Event Details"
                    className="modal"
                    overlayClassName="overlay"
                >
                    <div className="modal-content">
                        <h4>{`Estado de horario de ${this.props.nameClient}`}</h4>
                        <table>
                            <thead>
                                <tr>
                                    <th className="date">Hora</th>
                                    <th>Profesional</th>
                                    <th>Procedimiento</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {this.state.eventDetails.map((event, index) => {
                                    return (
                                        <tr key={index}>
                                            <td>
                                                {`${new Date(event.start).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} - ${new Date(event.end).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`}
                                            </td>
                                            <td>{event.profesional ? event.profesional : 'N/A'}</td>
                                            <td>{event.procedimiento ? event.procedimiento : 'N/A'}</td>
                                            <td className='iconos-cita-cli'>
                                                <a onClick={() => this.handleDeleteClick(event.id,event.tiempo)}>
                                                    <img className='icono-citas-cli' src={eliminar} alt="eliminar" />
                                                </a>
                                                <a onClick={null}>
                                                    <img className='icono-citas-cli' src={cancelar} alt="cancelar" />
                                                </a>
                                                <a onClick={null}>
                                                    <img className='icono-citas-cli' src={pdf} alt="pdf" />
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