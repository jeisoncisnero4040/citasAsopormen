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
            info:'',
            title:'',
            password:''
        };
    }

    _setEmail = (newEmail) => {
        this.setState({ email: newEmail }); 
    };
    _setPassword=(newPassword)=>{
        this.setState({password:newPassword})
    }
    handlepassword=(event)=>{
        this._setPassword(event.target.value)
    }

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
                this._openModalSucces("CAMBIO DE CORREO","Email Actualizado Con Éxito")
                this._setEmail('')
            })
            .catch(error=>{
                this._openModalSucces("Error",error)
            })
            .finally(()=>this._setLoading(false))
    }

    render() {
        const { email, error, loading,info,title,password } = this.state;

        return (
            <div className="container-form-update-client">
                
                <div className="form-update-clients-labels-and-footer">
                    <p className="subtittle-form-update-client">EMAIL</p>

                    <form onSubmit={this.handleSubmit}>
                        <div className="form-update-clients-insert-data">
                            <label htmlFor="email">
                                Contraseña
                            </label>
                            <input
                                type="password"
                                id="password"
                                placeholder="Favor ingresar contraseña"
                                value={password}
                                onChange={this.handlepassword}
                                required
                            />
                        </div>
                        <div className="form-update-clients-insert-data">
                            <label htmlFor="email">
                                Nuevo Correo
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

export default FormUpdateEmailClient;
