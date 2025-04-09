import React from "react";
import Modal from 'react-modal';

Modal.setAppElement('#root');

class SuccessWithLink extends React.Component {
    render() {
        const { isOpen, onClose, url } = this.props;

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
                        zIndex: '10000',
                        textAlign: 'center',
                    },
                    overlay: {
                        backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    },
                }}
            >
                <h2>¡Hecho!</h2>
                <p>
                    La notificación fue entregada al usuario con éxito. Si quieres ver el contenido del documento adjunto, 
                    <a href={url} target="_blank" rel="noopener noreferrer"> pulsa aquí</a>.
                </p>
                <button onClick={onClose} style={{ marginTop: '10px', padding: '8px 15px',textDecoration:'none',color:'blue',fontWeight:'800' }}>
                    Cerrar
                </button>
            </Modal>
        );
    }
}

export default SuccessWithLink;
