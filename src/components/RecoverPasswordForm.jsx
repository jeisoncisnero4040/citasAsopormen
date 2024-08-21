import React, { Component } from 'react';
import logo from "../assets/logo.png";
 


class RecoverPasswordForm extends Component {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            cedula: '',
            password: '',
            error: '',
            loading: false
        };

 
    }

 
    render() {
        const { email, cedula,error, loading } = this.state;

        return (
            <div className="login-container-recover">
                <img src={logo} alt="Logo" className="logo" />
                <p className="title">perdí mi contraseña</p>
                <p>Recuperar</p>
                <form  >
                    <div className="form-group">
                    <input
                        type="text"  
                        id="cedula"
                        value={cedula}  
                        placeholder="Ingrese tu identificación"
                         
                        required
                    />
                    </div>
                    <div className="form-group">
                    <input
                        type="text"
                        id="email"
                        value={email}
 
                        placeholder="Ingrese la contraseña"
                        required
                    />
                    </div>

                    {this.state.error && <p className="error">{this.state.error}</p>}
                    <button type="submit" disabled={loading}>
                    {loading ? "Cargando..." : "enviar"}
                    </button>
                    
                </form>
    </div>
        );
    }
}

export default RecoverPasswordForm;
