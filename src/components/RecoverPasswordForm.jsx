import React, { Component } from 'react';
import logo from "../assets/logo.png";
import axios from 'axios';
import Warning from './Warning';
import Constants from '../js/Constans.jsx';
 




class RecoverPasswordForm extends Component {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            cedula: '',
            error: '',
            loading: false,
            warningIsOpen: false,
            messageSuccess: ''
        };
    }

    handleCedula = (event) => {
        this.setState({ cedula: event.target.value });
    }

    handleEmail = (event) => {
        this.setState({ email: event.target.value });
    }

    retrievePassword = (event) => {
        event.preventDefault(); 

        const body = {
            'cedula': this.state.cedula,
            'email': this.state.email
        };

        const url = `${Constants.apiUrl()}recover_password`;

        this.setState({ loading: true });

        axios.post(url, body)
            .then(response => {
                this.setState({
                    messageSuccess: "Contraseña actualizada correctamente",
                    warningIsOpen: false 
                });


                setTimeout(() => {
                    this.setState({ messageSuccess: '', cedula: '', email: '' });
                }, 2000);
            })
            .catch(error => {
                this.setState({
                    error: error.response?.data?.error || 'Error al recuperar la contraseña',
                    warningIsOpen: true
                });
            })
            .finally(() => {
                this.setState({ loading: false });
            });
    }

    render() {
        return (
            <div className="login-container">
                    <div className="login-image">
                        <img src={logo} alt="Logo" className="logo" />
                        <div className="clinic-asopormen">
                            <strong>Recuperar</strong>
                            <p>Contraseña</p>
                        </div>
                    </div>
                
                <form onSubmit={this.retrievePassword}>
                    <div className="form-group">
                        <input
                            type="text"
                            id="cedula"
                            value={this.state.cedula}
                            onChange={this.handleCedula}
                            placeholder="Ingrese su identificación"
                            required
                        />
                    </div>
                    <div className="form-group">
                        <input
                            type="email"
                            id="email"
                            value={this.state.email}
                            onChange={this.handleEmail}
                            placeholder="Ingrese su correo electrónico"
                            required
                        />
                    </div>

                    <button type="submit">
                        {this.state.loading ? "Cargando..." : "Enviar"}
                    </button>
                </form>

                {this.state.messageSuccess && <p className="success-message">{this.state.messageSuccess}</p>}

                <Warning
                    isOpen={this.state.warningIsOpen}
                    onClose={() => this.setState({ warningIsOpen: false })}
                    errorMessage={this.state.error}
                />
            </div>
        );
    }
}

export default RecoverPasswordForm;
