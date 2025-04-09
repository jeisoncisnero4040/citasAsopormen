import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import '../../styles/clientsPage/LoginClients.css';
import ApiRequestManager from "../../util/ApiRequestMamager.js";
import Constants from '../../js/Constans';
import Succes from "../Succes.jsx"

const LoginClients = () => {

    const [document, setDocument] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [info, setInfo] = useState('');
    const [SuccesIsOpen, setSuccesIsOpen] = useState(false);
    const [title, setTitle]=useState("")

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
                _openSucces("ERROR",error);
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

    const _openSucces = (title,error) => {
        setInfo(error);
        setTitle(title);
        setSuccesIsOpen(true);
        
    }


    return (
        <div className="container-login-clients">

            <div className="login-clients">
                <div className="login-clients-header">
                    <p>¡Bienvenido(a) de nuevo!</p>
                </div>

                <div className="login-clients-labels-and-footer">
                    <p className="subtitle">Nos alegra verte de vuelta </p>
                    <p className="subtitle">Ingresa a tu cuenta para entrar a tu cuenta y consultar tus citas</p>

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
                        <div className="login-clients-request-password">
                            <a onClick={redirectToRequestPasswordPage} className="request-password">¿Olvidaste tu contraseña?</a>
                        </div>

                        <div className="login-clients-button-login">
                            <button type="submit">{loading ? 'Cargando' : 'Iniciar sesión'}</button>
                        </div>
                    </form>

                    

                </div>
            </div>

            <div>
            <div>
                <Succes
                        isOpen={SuccesIsOpen}
                        onClose={() => setSuccesIsOpen(false)}
                        info={info}
                        title={title}
                    />
                </div>
            </div>
        </div>
    );
}

export default LoginClients;

