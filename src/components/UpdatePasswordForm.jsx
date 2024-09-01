import React, { Component } from 'react';
import logo from "../assets/logo.png";
import Warning from './Warning';
import axios from 'axios';
import Constans from '../js/Constans';
import '../styles/formUpdatePassword.css'

class UpdatePasswordForm extends Component {
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
        axios.post(url, {
            cedula,
            oldPassword,
            newPassword
        })
        .then(response => {
            this.setState({
                errorMessage: 'Contraseña reestablecida correctamente',
                warningIsOpen: true
            });
            window.location.href = '/';
        })
        .catch(error => {
            if (error.response) {
                const errorData = error.response.data;
                this.setState({
                    errorMessage: errorData.error ? errorData.error : 'Error al hacer la petición',
                    warningIsOpen: true
                });
            }
        });
    }
     

    render() {
        const { oldPassword, newPassword, newPasswordConfirmation, error, loading,cedula } = this.state;

        return (
             
            <div className="update-container-recover">
                <img src={logo} alt="Logo" className="logo" />
                <p className="title">Cambiar Contraseña</p>
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