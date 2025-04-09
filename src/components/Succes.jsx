import Warning from "./Warning";
import Modal from 'react-modal';
import "../styles/Succes.css"


Modal.setAppElement('#root');
class Succes extends Warning{
    render() {
         
        const { isOpen, onClose, info,title } = this.props;
        
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
                        borderRadius: '30px',
                        width:'400px',
                        zIndex: 10000,
                        background:'#ffffff',
                        boxShadow:'0px 4px 10px rgba(0, 0, 0, 0.5)'
                    },
                    overlay: {
                        backgroundColor: 'rgba(0, 0, 0, 0.5)',
                    },
                }}
            >   
                <div className="container-succes">
                    <p className="title-succes-modal">{title}</p>
                    <p className="subtitle-succes-modal">{info}</p>
                    <button className="button-succes-modal" onClick={onClose}>Continuar</button>
                </div>

            </Modal>
        );
    }

}
export default Succes;