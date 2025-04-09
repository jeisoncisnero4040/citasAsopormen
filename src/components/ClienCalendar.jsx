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
import cancelar from "../assets/cancelar_2.png"
import Modal from 'react-modal';  
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import ApiRequestManager from '../util/ApiRequestMamager.js';
import logo from'../assets/logo.png'


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
            tiemposCitasSelected:[],
            error:'',
            showError:false,
            showAgreeButtons:false,
            warningsAccepted:false,
            idSelected:'',
            idsSelected:[],
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
    handleCheckboxChange = (id) => {
        this.setState(prevState => {
            const { idsSelected } = prevState;
    
            
            if (idsSelected.includes(id)) {
                return { idsSelected: idsSelected.filter(existingId => existingId !== id) };
            } else {
                return { idsSelected: [...idsSelected, id] };
            }
        });
    };
    

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
        this.setState({selectedday:start,idsSelected:[]});

        const filteredEvents = this.props.events.filter(event => {
            const eventDay = new Date(event.start);
            return eventDay.getDate() === eventDate.getDate() &&
                   eventDay.getMonth() === eventDate.getMonth() &&
                   eventDay.getFullYear() === eventDate.getFullYear();
        });
        const sortedFilteredEvents = filteredEvents.sort((a, b) => {
            return new Date(a.start) - new Date(b.start);   
        });

        this.openModal(sortedFilteredEvents); 
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
    _getDaySemana = (date) => {
        const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const fecha = new Date(date);
        return dias[fecha.getDay()];
    };
    generatePDF = (data,name) => {
        const sortedFilteredEvents = [...data].sort((a, b) => {
            return new Date(a.start) - new Date(b.start);   
        });
        const docDefinition = {
            content: [
                { text: 'Reporte '+ name, style: 'header',alignment: 'center' },
    
                 
                { text: 'Detalle de Citas', style: 'subheader' },
    
                 
                { text: ' ', margin: [0, 10] },

                {
                    table: {
                        headerRows: 1,  
                        body: [
                            ['dia','Fecha Inicio', 'Duración', 'Profesional', 'Procedimiento', 'estado'],
                            ...sortedFilteredEvents.map(cita => [
                                { text: this._getDaySemana(cita.start), style: 'tableBody' },
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

    renderCancelSessionCitasButton=()=>{
        
        if(this.state.idsSelected.length>0){
            

            return (
                <a style={{ display: 'flex',alignItems:'center' }} onClick={() => this.handleCancelClick()} >
                    <p>Cancelar</p>
                    <img className='icono-citas-cli' src={cancelar} alt="cancelar" />
                </a>
            )
        }
    }

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
                            <td>{event.fecha.split(' ')[0]}</td>
                            <td>{event.profesional ? event.profesional : 'N/A'}</td>
                            <td>{event.autorizacion}</td>
                            <td>{event.procedimiento ? event.procedipro : 'N/A'}</td>
                            <td className='iconos-cita-cli'>
                                {(this.state.working && this.state.idSelected === event.id) ? (
                                    <p className='search'>Trabajando en ello</p>
                                ) : (
                                    <>
                                        {this.renderCheckboxCitaSelected(event.id,estado)}
                                        <a onClick={() => this.handleDeleteClick(event.id, event.tiempo)}>
                                            <img className='icono-citas-cli' src={eliminar} alt="eliminar" />
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
    renderCheckboxCitaSelected = (id,estado) => {
        if(estado==='Programada'){
            return (
                <div key={id} className="checkbox">
                    <input
                        type="checkbox"
                        id={`checkbox-${id}`} 
                        onChange={() => this.handleCheckboxChange(id)} 
                        checked={this.state.idsSelected.includes(id)} 
                    />

                </div>
            );
        }else{
            return;
        }
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
    

    _formatDate = (date) => {
        const formattedDate =new Date(date)
        const meses = [
          "enero",
          "febrero",
          "marzo",
          "abril",
          "mayo",
          "junio",
          "julio",
          "agosto",
          "septiembre",
          "octubre",
          "noviembre",
          "diciembre",
        ];
    
        const dia = formattedDate.getDate();
        const mes = meses[formattedDate.getMonth()];
        const anio = formattedDate.getFullYear();
    
        return `${dia} de ${mes} de ${anio}`;
      };
   
  showPdfInBrowser = async (data) => {
    const estado = data.asistida === '1'
      ? 'Asistida'
      : data.cancelada === '1'
      ? 'Cancelada'
      : data.no_asistida === '1'
      ? 'No Asistida'
      : 'Programada';
    const observations = data.observaciones
      ? data.observaciones.replace('{{}}', data.copago||'No aplica')
      : 'No registra Observaciones';
    
    const date=this._formatDate(data.fecha)
    
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
        { text: '• Fecha: ' + date, style: 'text' },
        { text: '• Hora: ' + data.hora, style: 'text' },
        { text: '• Duración: ' + data.duracion + ' Mins', style: 'text' },
        { text: '• Estado: ' + estado, style: 'text' },
        { text: '• Usuario: ' + data.usuario, style: 'text' },
        { text: '• Profesional: ' + data.profesional, style: 'text' },
        { text: '• Orden: ' + data.orden, style: 'text' },
        { text: '• Procedimiento: ' + data.procedimiento, style: 'text' },
        { text: '• Dirección: ' + data.direcion, style: 'text' },
        { text: '• Observaciones: ', style: 'text', },
        { text: observations, style:'observations', preserveLeadingSpaces: true }
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
          fontSize: 10,
        },
        observations: {
          fontSize: 9,
          margin: [22, 5, 22, 5]
        },
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
        const url = `${Constants.apiUrl()}citas/${id}?usuario=${encodeURIComponent(this.props.usuario)}&cliente=${encodeURIComponent(this.props.nameClient)}`;
        this.setState({working:true})
        this.requestManager.deleteMethod(url)
            .then(response => {
                this.closeModal();
                const newFilteredEvents=this.state.eventDetails.filter(event=>event.id !==id);
                this.props.getCalendarClient(
                    this.props.events.filter(event => event.id !== id),
                    this.state.tiempoCitaSelected,
                    'delete'
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

    handleCancelClick=()=>{
            this.setState({
                actionType:'cancel'
            }
            )
    
            this.openLabelObservations()
    }
    cancelCitaById = (ids) => {
       
        const exampleCita=this.state.eventDetails.filter(even=>even.id===ids[0])
        const date=exampleCita[0].start.replace('T',' ').slice(0,16);
        

        const body = {
            razon: this.state.cancelObservations,
            ids: ids.join('|||'),
            meanCancel:'mc',
            fecha_cita:date,
            usuario:this.props.usuario,
            cliente:this.props.nameClient,
        };
    
        const url = `${Constants.apiUrl()}citas/cancel_all_sessions_cita`;
        this.setState({ working: true });
    
        this.requestManager.postMethod(url, body)
            .then(response => {
                this.closeModal();
    
                const tiemposCanceled = this.getCitasByTiempo(ids);
    
                const updatedEvents = this.props.events.map(event => {
                    if (ids.includes(event.id)) {
                        return { ...event, cancelada: '1' };
                    }
                    return event;
                });
    
                this.props.getCalendarClient(updatedEvents, tiemposCanceled);
                //this.openModal(sortedFilteredEvents, this.state.selectedDay);
    
                this.setState({
                    tiempoCitaSelected: '',
                    idsSelected: [],
                    actionType: '',
                    showLabelObservations: false,
                });
            })
            .catch(error => {
                this.setState(
                    {
                        showLabelObservations: false,
                        error: error,
                        showError: true,
                        idSelected: '',
                        actionType: '',
                    },
                    () => {
                        setTimeout(() => {
                            this.setState({ showError: false });
                        }, 3000);
                    }
                );
            })
            .finally(() => {
                this.setState({ working: false });
            });
    };
    
    getCitasByTiempo = (idsCanceled) => {
        const { eventDetails } = this.state;
        let citasByTiempo = {};
    
        idsCanceled.forEach(id => {
            const event = eventDetails.find(event => event.id === id);
            if (event && event.tiempo) {
                citasByTiempo[event.tiempo] = (citasByTiempo[event.tiempo] || 0) + 1;
            }
        });
    
        return citasByTiempo;
    };

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
            this.cancelCitaById(this.state.idsSelected); 
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
                        <div className='render-client-name-and-cancel-buton'>
                            <h4>{`Estado de horario de ${this.props.nameClient}`}</h4>
                            {this.renderCancelSessionCitasButton()}
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Fecha</th>
                                    <th>Profesional</th>
                                    <th>Autorizacion</th>
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