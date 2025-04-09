import React from "react";
import '../../styles/clientsPage/CardCita.css';
import CancelCitaModal from "./CancelCitaModal";
import { FaUser, FaChild, FaMapMarkerAlt, FaClock } from "react-icons/fa";

class CardCita extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            cita: {},  
            isModalOpen: false,
        };
    }

    componentDidMount() {
        this._setCita(this.props.cita);
    }

    componentDidUpdate(prevProps) {
        if (prevProps.cita !== this.props.cita) {
            this._setCita(this.props.cita);
        }
    }

    _setCita = (newCita = {}) => {
        this.setState({ cita: newCita });
    };
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
                    <a className="camcel-cita" onClick={()=>this.openModal()}>
                        <p className="text-cancelar">Cancelar Cita</p>
                    </a>
                </div>
            );
        }
        else{
            const statusCita=this._getStatusCita(this.state.cita);
             
            return(
                <div className="wrapper-button-unavaiable">
                    <a className="camcel-cita" onClick={null}>
                        <p className="text-cancelar">{this._capitalize(statusCita)}</p>
                    </a>
                </div>
            )
        }
         
    };
    

     
    _getStatusCita = (cita) => {
        if (!cita) return "Sin estado";
        if (cita.asistida === "1") return "asistida";
        if (cita.cancelada === "1") return "cancelada";
        if (cita.no_asistida === "1") return "no-asistida";
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
    _capitalize = (string) => {
        const words = string.split(" ");
        return words.map(word => {
             
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        }).join(" ");  
    };
    

    render() {
        const { cita, isModalOpen } = this.state;
        const statusCita = this._getStatusCita(cita); 
        const startDate = this._getStartDateCita(cita); 
        const dayName = startDate ? this._getDayName(startDate) : "Fecha inválida";
        const hourStart = this._getMinuteStart(cita);
        const duration = this._getDurationCita(cita);
        const direction = this._getDirectionCita(cita);
        const fecha = `${dayName} ${startDate ? startDate.toLocaleDateString() : "No disponible"} - ${hourStart}`;

        return (
            <div className="cita-card-wrapper">

                <div className="info-cita-card">
                    
                <div className={`header-${statusCita}`}>
                    <p className={statusCita}>{fecha.toUpperCase()}</p>
                </div>

                <div className='cita-info-item'>
                    <div className="key-cita-info-item">
                        <FaUser className={`icono-pro-${statusCita}`} />
                        <p className={statusCita}>Profesional:</p>
                    </div>
                    <p>{cita.profesional}</p>
                </div>

                <div className='cita-info-item'>
                    <div className="key-cita-info-item">
                        <FaChild className={`icono-${statusCita}`} />
                        <p className={statusCita}>Cita:</p>
                    </div>
                    <p>{cita.procedimiento}</p>
                </div>

                <div className='cita-info-item'>
                    <div className="key-cita-info-item">
                        <FaMapMarkerAlt className={`icono-${statusCita}`} />
                        <p className={statusCita}>Dirección:</p>
                    </div>
                    <p>{direction}</p>
                </div>

                <div className='cita-info-item'>
                    <div className="key-cita-info-item">
                        <FaClock className={`icono-${statusCita}`} />
                        <p className={statusCita}>Duración:</p>
                    </div>
                    <p>{duration} Minutos</p>
                </div>

                    {/* Modal */}
                    <CancelCitaModal
                        isOpen={isModalOpen}
                        onClose={this.closeModal}
                        citaCanceled={this.markCitaAsCancelada}
                        cita={this.props.cita}
                    />

                </div>
                <div className={`options-${statusCita === 'programada' ? 'programada' : 'no-programada'}`}>
                    {this.renderOptions()}
                </div>


            </div>
        );
    }
}

export default CardCita;
