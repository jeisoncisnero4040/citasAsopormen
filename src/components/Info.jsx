import React, { Component } from "react";
import Modal from 'react-modal';

Modal.setAppElement('#root');

class Info extends Component {
    constructor(props) {
        super(props);
    }
    
    renderMarginTopInPercent = () => {
        return "50%";
    };

    render() {
        const { isOpen, onClose, info, title } = this.props; 
        
        return (
            <Modal
                isOpen={isOpen}
                onRequestClose={onClose}
                style={{
                    content: {
                        top: this.renderMarginTopInPercent(),
                        left: '50%',
                        right: 'auto',
                        bottom: 'auto',
                        marginRight: '-50%',
                        transform: 'translate(-50%, -50%)',
                        padding: '20px',
                        borderRadius: '10px',
                        zIndex: '10000',
                        maxWidth:'1000px'
                    },
                    overlay: {
                        backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    },
                }}
            >
                <h2>{title}</h2>
                <pre>{info}</pre>
                <button onClick={onClose}>Cerrar</button>
            </Modal>
        );
    }
}

export default Info;

