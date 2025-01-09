import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import logo from '../../../src/assets/logo.png';
import '../../styles/clientsPage/LoginClients.css';
import Warning from "../Warning";
import ApiRequestManager from "../../util/ApiRequestMamager.js";
import Constants from '../../js/Constans';

const LoginClients = () => {

    const [document, setDocument] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [warningIsOpen, setWarningIsOpen] = useState(false);

    const navigate = useNavigate();
    const requestManager = new ApiRequestManager();

    const changeLoading = (isLoading) => {
        setLoading(isLoading);
    };

    const handleInputDocument = (event) => {
        setDocument(event.target.value);
    }

    const handleInputPassword = (event) => {
        setPassword(event.target.value);
    }

    const handleSubmit = (event) => {
        event.preventDefault();
        const body = _buildPayload();
        const url = `${Constants.apiUrl()}login_client`;
        _sendRequestToApi(url, body);
    }

    const _buildPayload = () => {
        return {
            'cedula': document,
            'password': password
        };
    }

    const _sendRequestToApi = (url, body) => {
        changeLoading(true);

        requestManager
            .postMethod(url, body)
            .then((response) => {
                const token = response.data.access_token;
                const userdata = response.data.data;
                _setTokenInBrowser(token);
                _redirectToClientsPage(userdata);
            })
            .catch((error) => {
                openWarning(error);
            })
            .finally(() => {
                changeLoading(false);
            });
    };

    const _setTokenInBrowser = (token) => {
        localStorage.setItem('authToken', token);
    }

    const _redirectToClientsPage = (client) => {
        navigate("/clientes_citas", { state:client});
    };
    

    const redirectToRequestPasswordPage = () => {
        window.location.href = '/clinico/solicitar_contraseña';
    }

    const openWarning = (error) => {
        setErrorMessage(error);
        setWarningIsOpen(true);
    }

    return (
        <div className="container-login-clients">
            <div className="header-and-logo">
                <img src={logo} alt="logo" />
            </div>

            <div className="login-clients">
                <div className="login-clients-header">
                    <p>Portal de usuarios</p>
                </div>

                <div className="login-clients-labels-and-footer">
                    <p className="subtitle">Iniciar sesión</p>

                    <form onSubmit={handleSubmit}>
                        {/* Campo para número de documento */}
                        <div className="login-clients-insert-data">
                            <label htmlFor="user-data">Número de Documento</label>
                            <input
                                type="text"
                                id="user-data"
                                placeholder="Ingrese su número de documento"
                                value={document}
                                onChange={handleInputDocument}
                                required
                            />
                        </div>

                        {/* Campo para contraseña */}
                        <div className="login-clients-insert-data">
                            <label htmlFor="user-password">Contraseña</label>
                            <input
                                type="password"
                                id="user-password"
                                placeholder="Ingrese su contraseña"
                                value={password}
                                onChange={handleInputPassword}
                                required
                            />
                        </div>

                        <div className="login-clients-button-login">
                            <button type="submit">{loading ? 'Cargando' : 'Continuar'}</button>
                        </div>
                    </form>

                    {/* Enlace para solicitar contraseña */}
                    <div className="login-clients-request-password">
                        <a onClick={redirectToRequestPasswordPage} className="request-password">Solicitar contraseña</a>
                    </div>
                </div>
            </div>

            <div>
                <Warning
                    isOpen={warningIsOpen}
                    onClose={() => setWarningIsOpen(false)}
                    errorMessage={errorMessage}
                />
            </div>
        </div>
    );
}

export default LoginClients;

