import React, { Component } from 'react';
import logo from "../assets/logo.png";
import Warning from './Warning';
import Constans from '../js/Constans';
import '../styles/formUpdatePassword.css'
import ApiRequestManager from '../util/ApiRequestMamager';

class UpdatePasswordForm extends Component {
    ApiRequestManager=new ApiRequestManager();
    constructor(props) {
        super(props);
        this.state = {
            oldPassword: '',
            newPassword: '',
            newPasswordConfirmation: '',
            cedula:'',
            error: '',
            loading: false,
            errorMessage: '',
            warningIsOpen: false
        };
    }
    handleCedula=(event)=>{
        this.setState({ cedula: event.target.value });
    }

    handleChange = (event) => {
        this.setState({ [event.target.id]: event.target.value });
    }

    handleSubmit = (event) => {
        event.preventDefault();
        const { oldPassword, newPassword, newPasswordConfirmation,cedula } = this.state;
    
        if (newPassword !== newPasswordConfirmation) {
            this.setState({ 
                errorMessage: 'Las nuevas contraseñas no coinciden',
                warningIsOpen: true
            });
            return;
        }
    
        const url = `${Constans.apiUrl()}update_password`;
        this.ApiRequestManager.postMethod(url, {
            cedula,
            oldPassword,
            newPassword
        })
        .then(response => {
            window.location.href = '/';
        })
        .catch(error => {
            this.setState({
                errorMessage: error,
                warningIsOpen: true
            });
        
        });
    }
     

    render() {
        const { oldPassword, newPassword, newPasswordConfirmation, error, loading,cedula } = this.state;

        return (
             
            <div className="login-container">
                    <div className="login-image">
                        <img src={logo} alt="Logo" className="logo" />
                        <div className="clinic-asopormen">
                            <strong>Actualizar</strong>
                            <p>Contraseña</p>
                        </div>
                    </div>
                 
                <form onSubmit={this.handleSubmit}>
                    <div className="form-group">
                        <input
                            type="text"
                            id="cedula"
                            value={cedula}
                            onChange={this.handleCedula}
                            placeholder="Ingrese su identificacion"
                            required
                        />
                    </div>
                    <div className="form-group">
                        <input
                            type="password"
                            id="oldPassword"
                            value={oldPassword}
                            onChange={this.handleChange}
                            placeholder="Ingrese su contraseña actual"
                            required
                        />
                    </div>
                    <div className="form-group">
                        <input
                            type="password"
                            id="newPassword"
                            value={newPassword}
                            onChange={this.handleChange}
                            placeholder="Ingrese su nueva contraseña"
                            required
                        />
                    </div>
                    <div className="form-group">
                        <input
                            type="password"
                            id="newPasswordConfirmation"
                            value={newPasswordConfirmation}
                            onChange={this.handleChange}
                            placeholder="Confirme su nueva contraseña"
                            required
                        />
                    </div>

                    {error && <p className="error">{error}</p>}
                    <button type="submit" disabled={loading}>
                        {loading ? "Cargando..." : "Enviar"}
                    </button>
                </form>
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

export default UpdatePasswordForm;