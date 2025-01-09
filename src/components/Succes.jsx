import Warning from "./Warning";
import Modal from 'react-modal';

Modal.setAppElement('#root');
class Succes extends Warning{
    render() {
         
        const { isOpen, onClose, errorMessage } = this.props;
        
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
                        zIndex: 10000
                    },
                    overlay: {
                        backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    },
                }}
            >
                <h2>Hecho</h2>
                <p>{errorMessage}</p>
                <button onClick={onClose}>Cerrar</button>
            </Modal>
        );
    }

}
export default Succes;