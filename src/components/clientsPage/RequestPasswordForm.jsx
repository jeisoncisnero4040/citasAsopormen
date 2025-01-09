import React from "react";
import Constants from '../../js/Constans';
import ApiRequestManager from "../../util/ApiRequestMamager.js";
import Warning from "../Warning";
import logo from '../../../src/assets/logo.png'

class RequestPasswordForm extends React.Component {
    requestManager = new ApiRequestManager();
    constructor(props) {
        super(props);
        this.state = {
            identityNumber: "",
            selectedEmailAsReceptor: false,
            selectedMobileAsReceptor: false,
            loading: false,
            warningIsOpen: false,
            errorMessage: "",
        };
        
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
            this.openWarning("No se ha seleccionado ningún método de envío");
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
                // Manejar la respuesta aquí si es necesario
            })
            .catch((error) => {
                this.openWarning(error);
            })
            .finally(() => {
                this.setState({ loading: false });
            });
    };

    openWarning = (error) => {
        this.setState({
            errorMessage: error,
            warningIsOpen: true,
        });
    };
    
    redirectToIndexPage=()=>{
        window.location.href = '/clinico/clientes';
    }

    render() {
        return (
            <div className="container-login-clients">
                <div className="header-and-logo">
                    <img src={logo} alt="logo" />
                </div>

                <div className="login-clients">
                    <div className="login-clients-header">
                        <p>Solicitar Contraseña</p>
                    </div>

                    <div className="login-clients-labels-and-footer">
                        <p className="subtitle">Solicitar Contraseña</p>

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
                                <button type="submit">{this.state.loading ? 'Solicitando' : 'Continuar'}</button>
                            </div>
                        </form>
                        <div className="login-clients-request-password">
                        <a onClick={this.redirectToIndexPage} className="request-password">Volver</a>
                    </div>
                    </div>
                </div>

                <div>
                    <Warning
                        isOpen={this.state.warningIsOpen}
                        onClose={() => this.setState({ warningIsOpen: false })}
                        errorMessage={this.state.errorMessage}
                    />
                </div>
            </div>
        );
    }
}

export default (RequestPasswordForm);

