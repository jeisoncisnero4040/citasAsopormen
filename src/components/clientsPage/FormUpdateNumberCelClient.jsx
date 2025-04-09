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
            password:'',
            loading: false, 
            SuccesIsOpen:false,
            info:'',
            title:''
        };
    }

    _setTelephoneNumber = (newTelephoneNumber) => {
        this.setState({ telephoneNumber: newTelephoneNumber }); 
    };
    _setPassword=(newPassword)=>{
        this.setState({password:newPassword})
    }

    handleNewTelephoneNumber = (event) => {
        this._setTelephoneNumber(event.target.value);
    };
    handlepassword=(event)=>{
        this._setPassword(event.target.value)
    }

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
            'clientCod': this.state.clientCodigo,
            'password':this.state.password
        };
    };

    handleSubmit = (event) => {
        event.preventDefault();
        if (!this._validateTelephoneNumber()){
            this._openModalSucces("Error","El Número Ingresado No Es Valido")
            return
        }
        let body = this._createPayload();
        let url = this._getUrl();
        this._setLoading(true)
        this._fetchRequestToApi(body, url);  
    };
    _fetchRequestToApi=(body,url)=>{
        
        this.requestManager.postMethod(url,body)
            .then(response=>{
                this._setPassword('');
                this._setPasswordConfirmation("");
                this._openModalSucces("CAMBIO DE TELÉFONO","Celular Actualizado Con Éxito");
            })
            .catch(error=>{
                this._openModalSucces("Error",error)
            })
            .finally(() => this._setLoading(false))
    }

    render() {
        const { telephoneNumber,password, error, loading,info,title } = this.state;

        return (
            <div className="container-form-update-client">
                 
                <div className="form-update-clients-labels-and-footer">
                    <p className="subtittle-form-update-client">CELULAR</p>

                    <form onSubmit={this.handleSubmit}>
                        <div className="form-update-clients-insert-data">
                            <label htmlFor="telephoneNumber">
                                Contraseña
                            </label>
                            <input
                                type="password"
                                id="password"
                                placeholder="Favor ingresar contraseña"
                                value={password}
                                onChange={this.handleNewPassword}
                                required
                            />
                        </div>
                        <div className="form-update-clients-insert-data">
                            <label htmlFor="telephoneNumber">
                                Nuevo Número
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

                        <div className="update-clients-button-update">
                            <button type="submit" disabled={loading}>
                                {loading ? "Cargando..." : "Continuar"}
                            </button>
                        </div>
                    </form>
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
                        info={info}
                        title={title}
                    />
                </div>
            </div>
        );
    }
}

export default FormUpdateNumberCelClient;
