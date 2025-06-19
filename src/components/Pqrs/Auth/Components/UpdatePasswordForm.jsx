import React, { useState, useCallback, useMemo } from 'react';
import '../../../../styles/formUpdatePassword.css';
import Alert from '../../Components/Alert';
import { useNavigate } from 'react-router-dom';
import AuthService from '../../Services/AuthService';
import ApiRequestsManagerService from '../../Services/ApiRequestsManagerService';

const UpdatePasswordForm = () => {
    const [cedula, setCedula] = useState('');
    const [oldPassword, setOldPassword] = useState('');
    const [newPassword, setNewPassword] = useState('');
    const [newPasswordConfirmation, setNewPasswordConfirmation] = useState('');
    const [loading, setLoading] = useState(false);
    const [alertData, setAlertData] = useState({ show: false, error: false, message: '' });

    const navigate = useNavigate();
    const authService = useMemo(() => new AuthService(new ApiRequestsManagerService()), []);

    const handleCedula = useCallback((e) => setCedula(e.target.value), []);

    const handleChange = useCallback((e) => {
        const { id, value } = e.target;
        if (id === 'oldPassword') setOldPassword(value);
        else if (id === 'newPassword') setNewPassword(value);
        else if (id === 'newPasswordConfirmation') setNewPasswordConfirmation(value);
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        if (newPassword !== newPasswordConfirmation) {
            return showError("Las contraseñas nuevas no coinciden");
        }

        setLoading(true);
        const response = await authService.changePassword({ cedula, oldPassword, newPassword });
        setLoading(false);
        responseManager(response, "Contraseña actualizada correctamente");
    };

    const responseManager = (response, messageSuccess = null) => {
        if (response?.message === "success") {
            showSuccess(messageSuccess);
            setTimeout(() => navigate('/'), 1000);
        } else if (response?.message === "error") {
            showError(response.error || "Error desconocido");
        }
    };

    const showSuccess = (msg) => setAlertData({ show: true, error: false, message: msg });
    const showError = (msg) => setAlertData({ show: true, error: true, message: msg });

    return (
        <div className="login-container">
            <div className="login-image">
                <img src="https://res.cloudinary.com/dxalvdckk/image/upload/v1747435854/descarga_ztjs3h.png" alt="Logo" className="logo" />
                <div className="clinic-asopormen">
                    <strong>Actualizar</strong>
                    <p>Contraseña</p>
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
                        type="password"
                        id="oldPassword"
                        value={oldPassword}
                        onChange={handleChange}
                        placeholder="Ingrese su contraseña actual"
                        required
                    />
                </div>
                <div className="form-group">
                    <input
                        type="password"
                        id="newPassword"
                        value={newPassword}
                        onChange={handleChange}
                        placeholder="Ingrese su nueva contraseña"
                        required
                    />
                </div>
                <div className="form-group">
                    <input
                        type="password"
                        id="newPasswordConfirmation"
                        value={newPasswordConfirmation}
                        onChange={handleChange}
                        placeholder="Confirme su nueva contraseña"
                        required
                    />
                </div>
                <button type="submit" disabled={loading}>
                    {loading ? 'Cargando...' : 'Enviar'}
                </button>
            </form>
            <div>
                {alertData.show && (
                <Alert
                    error={alertData.error}
                    message={alertData.message}
                    onClose={() => setAlertData((prev) => ({ ...prev, show: false }))}
                />
            )}
            </div>

        </div>
    );
};

export default UpdatePasswordForm;
