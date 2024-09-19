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
import ApiRequestManager from '../util/ApiRequestMamager.js';



Modal.setAppElement('#root');
pdfMake.vfs = pdfFonts.pdfMake.vfs;
class ClientCalendar extends Component {
    requestManager=new ApiRequestManager();
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
            showError:false,
            showAgreeButtons:false,
            warningsAccepted:false,
            idSelected:'',
            actionType:'',
            showLabelObservations:false,
            cancelObservations:'',
            working:false,
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
        this.setState({ loading: true });

    
        this.requestManager.postMethod(url, body)
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
   
        this.setState({
            errorMessage: error,
            warningIsOpen: true,
        });
       
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

    insertCitasInTable = () => {
        return (
            <tbody>
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
                            <td>{event.profesional ? event.profesional : 'N/A'}</td>
                            <td>{event.procedimiento ? event.procedimiento : 'N/A'}</td>
                            <td className='iconos-cita-cli'>
                                {(this.state.working && this.state.idSelected === event.id) ? (
                                    <p className='search'>Trabajando en ello</p>
                                ) : (
                                    <>
                                        <a onClick={() => this.handleDeleteClick(event.id, event.tiempo)}>
                                            <img className='icono-citas-cli' src={eliminar} alt="eliminar" />
                                        </a>
                                        <a onClick={() => this.handleCancelClick(event.id, event.tiempo)}>
                                            <img className='icono-citas-cli' src={cancelar} alt="cancelar" />
                                        </a>
                                        <a onClick={() => this.createPdfByCitaId(event.id)}>
                                            <img className='icono-citas-cli' src={pdf} alt="pdf" />
                                        </a>
                                    </>
                                )}
                            </td>

                        </tr>
                    );
                })}
            </tbody>
        );
    };
    createPdfByCitaId = async (id) => {
        this.setState({idSelected:id})
        try {
            const dataCita = await this.getCitaById(id);
            this.showPdfInBrowser(dataCita);
        } catch (error) {

            this.setState({
                error: error,
                showError: true
            }, () => {
                setTimeout(() => {
                    this.setState({ showError: false });
                }, 3000); 
            });
        } finally{this.setState({idSelected:''})}
    }
    
    getCitaById = async (id) => {
        
        const url = `${Constants.apiUrl()}citas/${id}`;
        this.setState({working:true});
        try {
            const response = await this.requestManager.getMethod(url);
            return response.data.data[0];
        } catch (error) {
           
            throw new error;
        }finally{
            this.setState({working:false});
        }
    }
    
    showPdfInBrowser = (data) => {
        const estado = data.asistida === '1'
            ? 'Asistida'
            : data.cancelada === '1'
            ? 'Cancelada'
            : data.no_asistida === '1'
            ? 'No Asistida'
            : 'Programada';
    
        const docDefinition = {
            pageSize: {
                width: 396,  
                height: 612  
            },
            pageMargins: [20, 20, 20, 20],
            content: [
                { text: 'Info cita ' + data.id, alignment: 'center', style: 'header' },
                { text: 'Detalle de Cita:', style: 'subheader' },
                { text: ' ', margin: [0, 8] },
                { text: '• Fecha: ' + data.fecha, style: 'text' },
                { text: '• Hora: ' + data.hora, style: 'text' },
                { text: '• Duración: ' + data.duracion + ' Mins', style: 'text' },
                { text: '• Estado: ' + estado, style: 'text' },
                { text: '• Usuario: ' + data.usuario, style: 'text' },
                { text: '• Profesional: ' + data.profesional, style: 'text' },
                { text: '• Orden: ' + data.orden, style: 'text' },
                { text: '• Procedimiento: ' + data.procedimiento, style: 'text' },
                { text: '• Observaciones: ' + (data.observaciones || 'No registra observaciones'), style: 'text' },
                { text: '• Dirección: ' + data.direcion, style: 'text' },
                { text: '• Fecha de Asignación: ' + data.hora_asignacion, style: 'text' }
            ],
            styles: {
                header: {
                    fontSize: 18,
                    bold: true,
                },
                subheader: {
                    fontSize: 14,
                    bold: true,
                },
                text: {
                    fontSize: 12,
                }
            }
        };
    
        pdfMake.createPdf(docDefinition).open();
    }
    handleDeleteClick= (id, tiempo) =>{
        this.setState({
            tiempoCitaSelected:tiempo,
            idSelected:id,
            actionType:'delete'
        }
        )

        this.openAgreeButtons()
    }
    deleteCitaById=(id)=> {
        const url = `${Constants.apiUrl()}citas/${id}`;
        this.setState({working:true})
        this.requestManager.deleteMethod(url)
            .then(response => {
                this.closeModal();
                const newFilteredEvents=this.state.eventDetails.filter(event=>event.id !==id);
                this.props.getCalendarClient(
                    this.props.events.filter(event => event.id !== id),
                    this.state.tiempoCitaSelected
                );

                 
                this.setState({ tiempoCitaSelected: '',warningsAccepted:false,idSelected:'',actionType:''});
                if (newFilteredEvents.length > 0){
                    this.openModal(newFilteredEvents,this.state.selectedday);
                }
            })
            .catch(error => {
                this.setState({
                    error: error,
                    showError: true,
                    idSelected:'',
                    actionType:''
                }, () => {
                    setTimeout(() => {
                        this.setState({ showError: false });
                    }, 3000); 
                });
            }).finally(this.setState({working:true}));
    }

    handleCancelClick=(id,tiempo)=>{
            this.setState({
                tiempoCitaSelected:tiempo,
                idSelected:id,
                actionType:'cancel'
            }
            )
    
            this.openLabelObservations()
    }
    cancelCitaById = (id) => {
        const body={
            'realizar':this.state.cancelObservations,
            'id':id
        }
        const url = `${Constants.apiUrl()}citas/cancel_cita`;
        this.setState({working:true});
        this.requestManager.postMethod(url,body)
            .then(response => {
                this.closeModal();
                const newFilteredEvents = this.state.eventDetails.map(event => {
                    if (event.id === id) {
                        event.cancelada = '1'; 
                    }
                    return event;
                });
                this.props.getCalendarClient(
                    this.props.events.map(event => {
                        if (event.id === id) {
                            event.cancelada = '1'; 
                        }
                        return event;
                    }),
                    this.state.tiempoCitaSelected
                );
                this.openModal(newFilteredEvents,this.state.selectedday);
                this.setState({ tiempoCitaSelected: '', idSelected: '',actionType:'',showLabelObservations:false });
            })
            .catch(error => {
                this.setState({
                    showLabelObservations:false,
                    error: error,
                    showError: true,
                    idSelected:'',
                    actionType:''
                }, () => {
                    setTimeout(() => {
                        this.setState({ showError: false });
                    }, 3000);
                });
            }).finally(this.setState({working:false}));
    }
    
    openAgreeButtons = () => {
        this.setState({ showLabelObservations:false,showAgreeButtons: true });
    }
    
    closeAgreeButtons = () => {
        this.setState({ showAgreeButtons: false });
    }
    openLabelObservations=()=>{
        this.setState({showAgreeButtons: false ,showLabelObservations:true})
    }
    closeLabelObservations=()=>{
        this.setState({showLabelObservations:false})
    }
    
    acceptWarnings = () => {
        if (this.state.actionType==='delete') {
            this.deleteCitaById(this.state.idSelected);
        } else if (this.state.actionType==='cancel') {
            this.cancelCitaById(this.state.idSelected); 
        }
        this.closeAgreeButtons(); 
    }
    insertAgreeField = () => {
        return (
            <div className='agree'>
                <p>Esta acción es irreversible, ¿estás seguro de que deseas continuar?</p>
                <div className='agree-buttons'>
                    <button className='agree-button' onClick={this.closeAgreeButtons}>No</button>
                    <button className='agree-button' onClick={this.acceptWarnings}>Sí</button>
                </div>
            </div>
        );
    }
    handleChangeObservations=(event)=>{
        this.setState({ cancelObservations: event.target.value });
    }
    inserTCancelcitaId = () => {
        return (
            <div className='agree'> 
                <div className="observations-cancel">
                    <label>Observaciones</label>
                    <textarea
                        placeholder="Inserte observaciones a las citas"
                        onChange={this.handleChangeObservations}  
                    />
                </div>
                
                <div className="agree-buttons">
                    <button className="agree-button" onClick={this.closeLabelObservations}>
                        No
                    </button>
                    <button className="agree-button" onClick={this.acceptWarnings}>
                        Cancelar
                    </button>
                </div>
            </div>
        );
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
                    className="modal-client"
                    overlayClassName="overlay-client"
                >
                    <div className="modal-content-client">
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

                                {this.insertCitasInTable()} 

                        </table>
                        <button className="close-button" onClick={this.closeModal}>Cerrar</button>
                        <p>{this.state.showError?this.state.error:null}</p>
                        {this.state.showAgreeButtons?this.insertAgreeField():null}
                        {this.state.showLabelObservations?this.inserTCancelcitaId():null}

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