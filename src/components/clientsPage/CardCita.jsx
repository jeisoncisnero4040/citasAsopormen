import React from "react";
import '../../styles/clientsPage/CardCita.css';
import profesional from '../../assets/1021799.png';
import reloj from '../../assets/reloj.png';
import direcion from '../../assets/direcion.png';
import cancelar_2 from '../../assets/cancelar_2.png';
import CancelCitaModal from "./CancelCitaModal";

class CardCita extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            cita: {},  
            isModalOpen: false,
        };
    }

    // Este método puede ser llamado cuando las props cambian
    static getDerivedStateFromProps(nextProps, nextState) {
        if (nextProps.cita !== nextState.cita) {
            return { cita: nextProps.cita };
        }
        return null;
    }
    markCitaAsCancelada = () => {
        this.setState((prevState) => ({
            cita: {
                ...prevState.cita,
                cancelada: '1',   
            }
        }), () => {
            
            this.props.citaCanceled(this.state.cita.ids);  
        });
    };
    renderOptions = () => {
        const statusCita = this._getStatusCita(this.state.cita);
        if (statusCita === 'programada') {
            return (
                <div className="wrapper-button-cancel">
                    <a className="camcel-cita" onClick={this.openModal}>
                        <img src={cancelar_2} alt="" />
                        <p className="text-cancelar">Cancelar</p>
                        <p className="text-cancelar-small">cita</p>
                    </a>
                </div>
            );
        }
        return null;
    };
    

    // Método para obtener el estado de la cita
    _getStatusCita = (cita) => {
        if (!cita) return "Sin estado";
        if (cita.asistida === "1") return "asistida";
        if (cita.cancelada === "1") return "cancelada";
        if (cita.no_asistida === "1") return "no asistida";
        return "programada";
    };

    _getStartDateCita = (cita) => {
        if (!cita || !cita.start) return null;
        const start = new Date(cita.start);
        return isNaN(start) ? null : start;
    };

    _getDayName = (fecha) => {
        if (!fecha) return "Fecha inválida";
        return new Intl.DateTimeFormat("es-ES", { weekday: "long" }).format(fecha);
    };

    _getMinuteStart = (cita) => {
        if (!cita.start) return "";
        const date = new Date(cita.start);
        let hour = date.getHours();
        const minute = date.getMinutes();
        const period = hour < 12 ? "AM" : "PM";
        hour = hour % 12 || 12;
        return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')} ${period}`;
    };

    _getDurationCita = (cita) => {
        if (!cita || !cita.ids) return 0;
        const numSessionsCita = cita.ids.split('|||').length;
        return numSessionsCita * parseInt(cita.duracion, 10);
    };

    _getDirectionCita = (cita) => {
        if (!cita || !cita.direcion) return "Indefinida";  
        let wordArray = cita.direcion.split(' ');  
        let textTitle = wordArray
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())  
            .join(' ');  
        return textTitle;
    };
    

    openModal = () => {
        this.setState({ isModalOpen: true });
    };

    closeModal = () => {
        this.setState({ isModalOpen: false });
    };

    render() {
        const { cita, isModalOpen } = this.state;
        const statusCita = this._getStatusCita(cita); 
        const startDate = this._getStartDateCita(cita); 
        const dayName = startDate ? this._getDayName(startDate) : "Fecha inválida";
        const hourStart = this._getMinuteStart(cita);
        const duration = this._getDurationCita(cita);
        const direction = this._getDirectionCita(cita);


        return (
            <div className="cita-card-wrapper">
                <div className={`info-date-card-${statusCita.replace(/ /g, "-")}`}>
                    <div className="wrapper-status">
                        <p className="status-cita">{statusCita}</p>
                    </div>
                    <br />
                    <p className="day-name">{dayName}</p>
                    <p className="fecha">{startDate ? startDate.toLocaleDateString() : "No disponible"}</p>
                    <p className="hour">{hourStart}</p>
                </div>

                <div className="info-cita-card">
                    <div className="static-info-cita">
                        <div className="name-profesional-cita">
                            <img src={profesional} alt="" />
                            <div className="name-profesional-cita-and-procedim">
                                <p className="profesional-name">{cita.profesional}</p>
                                <p className="procedim">{cita.procedimiento}</p>
                            </div>
                        </div>
                        <hr />
                        <div className="name-profesional-cita">
                            <img src={direcion} alt="" />
                            <p className="direccion-cita">Dirección: {direction}</p>
                        </div>
                        <div className="name-profesional-cita">
                            <img src={reloj} alt="" />
                            <p className="duracion-cita">Duración: {duration} Minutos</p>
                        </div>
                    </div>
                    <div className="vertical-line"></div>
                    <div className="options">
                        {this.renderOptions()}
                    </div>

                </div>

                {/* Modal */}
                <CancelCitaModal
                    isOpen={isModalOpen}
                    onClose={this.closeModal}
                    citaCanceled={this.markCitaAsCancelada}
                    cita={cita}
                />
            </div>
        );
    }
}

export default CardCita;
