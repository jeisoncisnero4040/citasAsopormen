import React from "react";
import Constants from '../../js/Constans';
import ApiRequestManager from "../../util/ApiRequestMamager.js";
import Succes from "../Succes.jsx";

class RequestPasswordForm extends React.Component {
    requestManager = new ApiRequestManager();
    constructor(props) {
        super(props);
        this.state = {
            identityNumber: "",
            selectedEmailAsReceptor: false,
            selectedMobileAsReceptor: false,
            loading: false,
            SuccesIsOpen:false,
            info:"",
            title:"",
        };
        
    }
    _setSuccesMsm=(newMesage)=>{
        this.setState({ info: newMesage });
    }
    _setTitleSucces=(newSucces)=>{
        this.setState({ tittle: newSucces });
    }
    _setSuccesIsOpen=(newStatus)=>{
        this.setState({SuccesIsOpen: newStatus });
    }


    handleInputIdentityNumber = (event) => {
        this.setState({ identityNumber: event.target.value });
    };

    handleCheckboxChange = (type) => {
        if (type === "email") {
            this.setState({
                selectedEmailAsReceptor: true,
                selectedMobileAsReceptor: false,
            });
        } else if (type === "mobile") {
            this.setState({
                selectedEmailAsReceptor: false,
                selectedMobileAsReceptor: true,
            });
        }
    };

    handleSubmit = (event) => {
        event.preventDefault();
        const { selectedEmailAsReceptor, selectedMobileAsReceptor } = this.state;

        if (!selectedEmailAsReceptor && !selectedMobileAsReceptor) {
            this._openModalSucces("ERROR","No se ha seleccionado ningún método de envío");
            return;
        }

        const body = this._buildBodyToRequest();
        const url = `${Constants.apiUrl()}clients/request_password`;
        this._sendRequestToApi(url, body);
    };

    _buildBodyToRequest = () => {
        return {
            clientIdentity: this.state.identityNumber,
            sendPasswordToEmail: this.state.selectedEmailAsReceptor,
            sendPasswordToMobile: this.state.selectedMobileAsReceptor,
        };
    };

    _sendRequestToApi = (url, body) => {
        this.setState({ loading: true });

        this.requestManager
            .postMethod(url, body)
            .then((response) => {
                this._openModalSucces("Contraseña actaulizada".toUpperCase(),"se ha enviado la contraseña nueva al medio seleccionado")
            })
            .catch((error) => {
                this._openModalSucces("Error",error)
            })
            .finally(() => {
                this.setState({ loading: false });
            });
    };

    _openModalSucces = (title,info) => {
        this.setState({
            title:title,
            info:info,
            SuccesIsOpen:true
        })
    };
    
    redirectToIndexPage=()=>{
        window.location.href = '/clinico/clientes';
    }

    render() {
        return (
            <div className="container-login-clients">

                <div className="login-clients">
                    <div className="login-clients-header">
                        <p>Solicitar Contraseña</p>
                    </div>

                    <div className="login-clients-labels-and-footer">
                        <p className="subtitle">Ingrese datos para solicitar contraseña</p>
                        <p className="subtitle">Una vez solicites la contraseña cerifica el canal de envío </p>

                        <form onSubmit={this.handleSubmit}>
                            <div className="login-clients-insert-data">
                                <label htmlFor="user-identity">Número de Identificación</label>
                                <input
                                    type="text"
                                    id="user-identity"
                                    placeholder="Ingrese su número de identificación"
                                    onChange={this.handleInputIdentityNumber}
                                    value={this.state.identityNumber}
                                    required
                                />
                            </div>

                            <div className="login-clients-checkbox-options">
                                <label>
                                    <input
                                        type="checkbox"
                                        checked={this.state.selectedEmailAsReceptor}
                                        onChange={() => this.handleCheckboxChange("email")}
                                    />
                                    Enviar al correo electrónico
                                </label>
                                <label>
                                    <input
                                        type="checkbox"
                                        checked={this.state.selectedMobileAsReceptor}
                                        onChange={() => this.handleCheckboxChange("mobile")}
                                    />
                                    Enviar al celular
                                </label>
                            </div>

                            <div className="login-clients-button-login">
                                <button type="submit">{this.state.loading ? 'Solicitando' : 'Solicitar Contraseña'}</button>
                            </div>
                        </form>
                        <div className="login-clients-request-password">
                        <a onClick={this.redirectToIndexPage} className="request-password">Volver</a>
                    </div>
                    </div>
                </div>

                <div>
                    <Succes
                        isOpen={this.state.SuccesIsOpen}
                        onClose={() => this._setSuccesIsOpen(false)}
                        info={this.state.info}
                        title={this.state.title}
                    />
                </div>
            </div>
        );
    }
}

export default (RequestPasswordForm);

