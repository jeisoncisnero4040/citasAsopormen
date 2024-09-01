import React from "react";
import Modal from 'react-modal';
import PropTypes from 'prop-types';
import alert from "../assets/alerta.png";
import '../styles/alertSchedule.css'

Modal.setAppElement('#root');

class AlertSchedule extends React.Component {
    acceptAlert = () => {
        const { onAccept } = this.props;
        onAccept();
    }

    render() {
        const { isOpen, onClose, alertMessage } = this.props;

        return (
            <Modal
                isOpen={isOpen}
                onRequestClose={onClose}
                style={{
                    content: {
                        top: '50%',
                        left: '50%',
                        width:'800px',
                        right: '500px',
                        bottom: 'auto',
                        marginRight: '-50%',
                        transform: 'translate(-50%, -50%)',
                        padding: '20px',
                        borderRadius: '10px',
                        zIndex: 10000
                    },
                    overlay: {
                        backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    },
                     
                }}
            >
                <div  className="alert">
                    <img src={alert} alt="alert-image" />
                </div>
                <p>{alertMessage}</p>
                <div className="bootons">
                    <button onClick={onClose}>Cerrar</button>
                    <button onClick={this.acceptAlert}>Seguro!!</button>   
                </div>
                
                
            </Modal>
        );
    }
}

 
 
export default AlertSchedule;
