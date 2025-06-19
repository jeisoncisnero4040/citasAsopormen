import React, { useState, useCallback } from 'react';
import '../../../../styles/formUpdatePassword.css';
import Alert from '../../Components/Alert';
import AuthService from '../../Services/AuthService';
import ApiRequestsManagerService from '../../Services/ApiRequestsManagerService';

const RecoverPasswordForm = () => {
    const [cedula, setCedula] = useState('');
    const [email, setEmail] = useState('');
    const [loading, setLoading] = useState(false);
    const [alertData, setAlertData] = useState({ show: false, error: false, message: "" });

    const authService = new AuthService(new ApiRequestsManagerService());

    const handleCedula = useCallback((event) => {
        setCedula(event.target.value);
    }, []);

    const handleEmail = useCallback((event) => {
        setEmail(event.target.value);
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        const response = await authService.forgotPassword({ cedula, email });
        setLoading(false);
        responseManager(response, "Contraseña actualizada correctamente");
    };

    const responseManager = (response, messageSuccess = null) => {
        if (response.message === "success" && response.alertable === true) {
            showSuccess(messageSuccess);
        } else if (response.message === "error") {
            showError(response.error || "Error al recuperar la contraseña");
        }
    };

    const showSuccess = (msm) => {
        setAlertData({ show: true, error: false, message: msm });
    };

    const showError = (error) => {
        setAlertData({ show: true, error: true, message: error });
    };

    return (
        <div className="login-container">
            <div className="login-image">
                <img
                    src="https://res.cloudinary.com/dxalvdckk/image/upload/v1747435854/descarga_ztjs3h.png"
                    alt="Logo"
                    className="logo"
                />
                <div className="clinic-asopormen">
                    <strong className="title">Recuperar</strong>
                    <p className="subtitle">Contraseña</p>
                </div>
            </div>

            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <input
                        type="text"
                        id="cedula"
                        value={cedula}
                        onChange={handleCedula}
                        placeholder="Ingrese su identificación"
                        required
                    />
                </div>
                <div className="form-group">
                    <input
                        type="email"
                        id="email"
                        value={email}
                        onChange={handleEmail}
                        placeholder="Ingrese su correo electrónico"
                        required
                    />
                </div>

                <button type="submit" disabled={loading}>
                    {loading ? 'Cargando...' : 'Enviar'}
                </button>
            </form>

            {alertData.show && (
                <Alert
                    error={alertData.error}
                    message={alertData.message}
                    onClose={() => setAlertData({ ...alertData, show: false })}
                />
            )}
        </div>
    );
};

export default RecoverPasswordForm;
