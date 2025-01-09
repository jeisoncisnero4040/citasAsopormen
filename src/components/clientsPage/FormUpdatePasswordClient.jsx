import React from "react";
import Warning from "../Warning";
import ApiRequestManagerClient from "../../util/ApiRequestManagerClient";
import Constants from "../../js/Constans" ;
import Succes from "../Succes";


//este componente comparte el css con el formulario de login-clientes
class FormUpdatePasswordClient extends React.Component {
    requestManager=new ApiRequestManagerClient()
    constructor(props) {
        super(props);
        this.state = {
            newPassword: "",
            confirmationNewPassword: "",
            error: "",
            warningIsOpen: false,
            clientCodigo: "",
            loading: false, 
            SuccesIsOpen:false,
            SuccesMsm:''
        };
    }
    componentDidMount=()=>{
        this._setClientCod(this.props.codigo);
    }

    componentDidUpdate(prevProps) {
        if (prevProps.codigo !== this.props.codigo) {
            this._setClientCod(this.props.codigo);
        }
    }
    _setLoading=(newState)=>{
        this.setState({loading:newState})
    }
    _setError = (newError) => {
        this.setState({ error: newError });
    };

    _setSuccesMsm=(newMesage)=>{
        this.setState({ SuccesMsm: newMesage });
    }
    _setSuccesIsOpen=(newStatus)=>{
        this.setState({SuccesIsOpen: newStatus });
    }


    _setWarningIsOpen = (newState) => {
        this.setState({ warningIsOpen: newState });
    };

    _setPassword = (newPassword) => {
        this.setState({ newPassword: newPassword });
    };

    _setPasswordConfirmation = (newPassword) => {
        this.setState({ confirmationNewPassword: newPassword });
    };

    _setClientCod = (newCod) => {
        this.setState({ clientCodigo: newCod });
    };

    handleNewPassword = (event) => {
        this._setPassword(event.target.value);
    };

    handleNewPasswordConfirmation = (event) => {
        this._setPasswordConfirmation(event.target.value);
    };

    _checkPasswordAreEquals = () => {
        return this.state.newPassword === this.state.confirmationNewPassword;
    };
    _checkLengthNewPassword=()=>{
        return this.state.newPassword.length>=6
    }

    _openModal = (error) => {
        this._setError(error);
        this._setWarningIsOpen(true);
    };

    _openModalSucces = (message) => {
        this._setSuccesMsm(message);
        this._setSuccesIsOpen(true);
    };
    _getUrl=()=>{
        return `${Constants.apiUrl()}clients/update_password`
    }
    logout=()=>{
        localStorage.removeItem('authToken');
        localStorage.removeItem('userCitas');
        window.location.href = '/clinico/clientes';
    }
    _createPayloaad=()=>{
        return {
            'password':this.state.newPassword,
            'clientIdentity':this.state.clientCodigo
        }
    }
    _fetchRequestToApi=(body,url)=>{
        this._setLoading(true)
        this.requestManager.postMethod(url,body)
            .then(response=>{
                this.logout()
            })
            .catch(error=>{
                this._openModal(error)
            })
            .finally(this._setLoading(false))
    }

    handleSubmit = (event) => {
        event.preventDefault();
        if(!this._checkLengthNewPassword()){
            this._openModal("La contraseña nueva es muy corta");
            return;
        }
        if (!this._checkPasswordAreEquals()) {
            this._openModal("Las contraseñas no coinciden.");
            return;
        }
        let body=this._createPayloaad()
        let url=this._getUrl()
        this._fetchRequestToApi(body,url)


    };

    render() {
        const {
            newPassword,
            confirmationNewPassword,
            warningIsOpen,
            error,
            loading,
            SuccesMsm,
        } = this.state;

        return (
            <div className="container-login-clients">
                <div className="login-clients">
                    <div className="login-clients-header">
                        <p>Portal de usuarios</p>
                    </div>

                    <div className="login-clients-labels-and-footer">
                        <p className="subtitle">Actualizar Contraseña</p>

                        <form onSubmit={this.handleSubmit}>
                            {/* Campo para contraseña */}
                            <div className="login-clients-insert-data">
                                <label htmlFor="new-password">Nueva Contraseña</label>
                                <input
                                    type="password"
                                    id="new-password"
                                    placeholder="Ingrese su nueva contraseña"
                                    value={newPassword}
                                    onChange={this.handleNewPassword}
                                    required
                                />
                            </div>

                            <div className="login-clients-insert-data">
                                <label htmlFor="confirm-password">
                                    Confirme Nueva Contraseña
                                </label>
                                <input
                                    type="password"
                                    id="confirm-password"
                                    placeholder="Favor Confirmar nueva contraseña"
                                    value={confirmationNewPassword}
                                    onChange={this.handleNewPasswordConfirmation}
                                    required
                                />
                            </div>

                            <div className="login-clients-button-login">
                                <button type="submit" disabled={loading}>
                                    {loading ? "Cargando..." : "Continuar"}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div>
                    <Warning
                        isOpen={warningIsOpen}
                        onClose={() => this._setWarningIsOpen(false)}
                        errorMessage={error}
                    />
                </div>
                <div>
                    <Succes
                        isOpen={this.state.SuccesIsOpen}
                        onClose={() => this._setSuccesIsOpen(false)}
                        errorMessage={SuccesMsm}
                    />
                </div>
            </div>
        );
    }
}

export default FormUpdatePasswordClient;
