import React from "react";
import Modal from 'react-modal';
import '../../styles/clientsPage/CancelCitaModal.css'
import ApiRequestManagerClient from "../../util/ApiRequestManagerClient";
import Constants from "../../js/Constans";

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
        let body=this._createPayload();
        let url=this._getUrl()
        this._fetchRequest(url=url,body=body);

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
    render() {
        const { isOpen, onClose } = this.props;
        const { razon } = this.state;

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
                        transform: 'translate(-50%, -50%)',
                        padding: '20px',
                        borderRadius: '10px',
                        zIndex: 10000,
                    },
                    overlay: {
                        backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    },
                }}
            >
                <div className="form-cancel-cita">
                    <h2 className="cancel-cita-title">Cancelar Cita</h2>
                    <div className="label-to-get-razon">
                        <label className="razon">
                            Razón para cancelar la cita:
                        </label>
                        <textarea
                            id="razon"
                            value={razon}
                            onChange={this.handleRazonChange}
                            rows="4"
                            placeholder="Escribe aquí la razón para cancelar la cita..."
                        ></textarea>

                    </div>
                    <div className="button-cancel-cita-modal">
                        <button onClick={this.handleSubmit}>Salir</button>
                        <button onClick={this.cancelCita}>Cancelar Cita</button>
                    </div>
                    <div className="insert-error-canceling-citas">
                        <p>{this.state.error??this.state.error}</p>
                    </div>
                </div>
            </Modal>
        );
    }
}

export default CancelCitaModal;
