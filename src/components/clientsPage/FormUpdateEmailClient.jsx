import React from "react"; 
import FormUpdatePasswordClient from "./FormUpdatePasswordClient";
import Warning from "../Warning";
import Constants from "../../js/Constans" ;
import Succes from "../Succes"

class FormUpdateEmailClient extends FormUpdatePasswordClient {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            error: "",
            warningIsOpen: false,
            clientCodigo: "",
            loading: false, 
            SuccesIsOpen:false,
            SuccesMsm:''
        };
    }

    _setEmail = (newEmail) => {
        this.setState({ email: newEmail }); 
    };

    handleNewEmail = (event) => {
        this._setEmail(event.target.value);
    };

    _getUrl = () => {
        return `${Constants.apiUrl()}clients/update`; 
    };

    _createPayload = () => {
        return {
            'email': this.state.email,
            'clientCod': this.state.clientCodigo
        };
    };

    handleSubmit = (event) => {
        event.preventDefault();
        let body = this._createPayload();
        let url = this._getUrl();
        this._fetchRequestToApi(body, url);  
    };
    _fetchRequestToApi=(body,url)=>{
        this._setLoading(true)
        this.requestManager.postMethod(url,body)
            .then(response=>{
                this._openModalSucces("Email actualizado correctamente")
                this._setEmail('')
            })
            .catch(error=>{
                this._openModal(error)
            })
            .finally(this._setLoading(false))
    }

    render() {
        const { email, error, loading,SuccesMsm } = this.state;

        return (
            <div className="container-login-clients">
                <div className="login-clients">
                    <div className="login-clients-header">
                        <p>Portal de usuarios</p>
                    </div>

                    <div className="login-clients-labels-and-footer">
                        <p className="subtitle">Actualizar Email</p>

                        <form onSubmit={this.handleSubmit}>
                            <div className="login-clients-insert-data">
                                <label htmlFor="email">
                                    Ingrese Su Nuevo Email
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    placeholder="Favor Confirmar su  nuevo correo electronico"
                                    value={email}
                                    onChange={this.handleNewEmail}
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
                        isOpen={this.state.warningIsOpen}
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

export default FormUpdateEmailClient;
