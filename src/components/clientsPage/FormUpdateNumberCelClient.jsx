import React from "react"; 
import FormUpdatePasswordClient from "./FormUpdatePasswordClient";
import Warning from "../Warning";
import Constants from "../../js/Constans" ;
import Succes from "../Succes";

class FormUpdateNumberCelClient extends FormUpdatePasswordClient {
    constructor(props) {
        super(props);
        this.state = {
            telephoneNumber: '',
            error: "",
            warningIsOpen: false,
            clientCodigo: "",
            loading: false, 
            SuccesIsOpen:false,
            SuccesMsm:''
        };
    }

    _setTelephoneNumber = (newTelephoneNumber) => {
        this.setState({ telephoneNumber: newTelephoneNumber }); 
    };

    handleNewTelephoneNumber = (event) => {
        this._setTelephoneNumber(event.target.value);
    };
    _validateTelephoneNumber=()=>{
        const regex = /^3\d{9}$/; 
        return regex.test(this.state.telephoneNumber)
        
    }

    _getUrl = () => {
        return `${Constants.apiUrl()}clients/update`; 
    };

    _createPayload = () => {
        return {
            'cel': this.state.telephoneNumber,
            'clientCod': this.state.clientCodigo
        };
    };

    handleSubmit = (event) => {
        event.preventDefault();
        if (!this._validateTelephoneNumber()){
            this._openModal("el numero ingresado no es valido")
            return
        }
        let body = this._createPayload();
        let url = this._getUrl();
        this._fetchRequestToApi(body, url);  
    };
    _fetchRequestToApi=(body,url)=>{
        this._setLoading(true)
        this.requestManager.postMethod(url,body)
            .then(response=>{
                this._setPassword('');
                this._setPasswordConfirmation("");
                this._openModalSucces("Contraseña actualizada correctamente");
            })
            .catch(error=>{
                this._openModal(error)
            })
            .finally(this._setLoading(false))
    }

    render() {
        const { telephoneNumber, error, loading, SuccesMsm } = this.state;

        return (
            <div className="container-login-clients">
                <div className="login-clients">
                    <div className="login-clients-header">
                        <p>Portal de usuarios</p>
                    </div>

                    <div className="login-clients-labels-and-footer">
                        <p className="subtitle">Actualizar Contacto Celular</p>

                        <form onSubmit={this.handleSubmit}>
                            <div className="login-clients-insert-data">
                                <label htmlFor="telephoneNumber">
                                    Ingrese Su Nuevo Numero de Contacto
                                </label>
                                <input
                                    type="text"
                                    id="telephoneNumber"
                                    placeholder="Favor ingresar numero de contacto"
                                    value={telephoneNumber}
                                    onChange={this.handleNewTelephoneNumber}
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

export default FormUpdateNumberCelClient;
