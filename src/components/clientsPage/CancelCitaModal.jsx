import React from "react";
import Modal from 'react-modal';
import '../../styles/clientsPage/CancelCitaModal.css'
import ApiRequestManagerClient from "../../util/ApiRequestManagerClient";
import Constants from "../../js/Constans";
import { FaTimes} from "react-icons/fa";

Modal.setAppElement('#root');

class CancelCitaModal extends React.Component {
    requestManager=new ApiRequestManagerClient()
    constructor(props) {
        super(props);
        this.state = {
            razon: '', 
            cita: {},  
            error:''
        };
    }
    

    componentDidMount() {
        this.setCitaToCancel(this.props.cita);
    }

    setCitaToCancel = (newCita) => {
        this.setState({ cita: newCita });
    };
    setError=(newError)=>{
        this.setState({error:newError})
    }

    handleRazonChange = (event) => {
        this.setState({ razon: event.target.value });
    };

    handleSubmit = () => {
        this.props.onClose(); 
    };


    cancelCita=()=>{
        if(!this.state.razon){
            this.setError("La razon por la cual se cancela la cita es requerida");
            return;
        }
        if(!this._isCitaOnTime()){
            this.setError("La cita que desea cancelar ya no esta disponible para esta acción");
            return;
        }
        let body=this._createPayload();
        let url=this._getUrl();
        this._fetchRequest(url=url,body=body);
        this.props.onClose();

    }
    _getUrl=()=>{
        return `${Constants.apiUrl()}citas/cancel_all_sessions_cita`
    }
    _createPayload = () => {
        const { cita, razon } = this.state;
        
        return {
            ids: cita.ids,
            fecha_cita: cita.start.replace('T',' ').slice(0,16),
            razon: razon,
            meanCancel:'web',
        };
    }
    _fetchRequest=(url,body)=>{
        this.requestManager.postMethod(url,body)
            .then(response=>{
                this.props.citaCanceled();
                this.props.onClose();
            }).catch(error=>{
                this.setError(error)
            })

    }
    _isCitaOnTime = () => {
        const now = new Date();
        const dateCita = new Date(this.state.cita.start);
        const diferenciaMs = dateCita - now;
        const diferenciaHoras = diferenciaMs / (1000 * 60 * 60);
        return diferenciaHoras > 6;
    };
    
    render() {
        const { isOpen, onClose } = this.props;
        const { razon, error } = this.state;

        return (
            <Modal
                isOpen={isOpen}
                onRequestClose={onClose}
                style={{
                    content: {
                        top: '50%',
                        left: '50%',
                        right: 'auto',
                        bottom: 'auto',
                        marginRight: '-50%',
                        transform: 'translate(-50%, -40%)',
                        padding: '20px',
                        borderRadius: '10px',
                        zIndex: 10000,
                    },
                    overlay: {
                        backgroundColor: 'rgba(0, 0, 0, 0.3)',
                    },
                }}
            >
                <div className="form-cancel-cita">
                    <div className="title-and-close-button">
                        <p>CANCELACIÓN DE CITA</p>
                        <a onClick={this.handleSubmit}><FaTimes /></a>
                    </div>
                    <div className="info-cancel-cita">
                        <p className="subtittle">IMPORTANTE:</p>
                        <p className="text-info-to-cancel-cita">
                            • Si no cumple con las condiciones establecidas para cancelaciones,
                            la cita se registrará como asistida.
                        </p>
                        <p className="text-info-to-cancel-cita">
                            • Si cancela por motivos de salud, deberá presentar un soporte médico
                            de manera presencial para realizar la modificación correspondiente.
                        </p>
                    </div>
                    <div className="label-to-get-razon">
                        <label className="razon">Razón:</label>
                        <textarea
                            id="razon"
                            value={razon}
                            onChange={this.handleRazonChange}
                            rows="6"
                            placeholder="Escribe aquí la razón para cancelar la cita..."
                        ></textarea>
                    </div>
                    <div className="button-cancel-cita-modal">
                        <button onClick={this.cancelCita}>Continuar</button>
                    </div>
                    <div className="insert-error-canceling-citas">
                        <p>{error}</p>
                    </div>
                </div>
            </Modal>
        );
    }


}

export default CancelCitaModal;

