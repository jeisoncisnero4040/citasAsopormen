import React, { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import "../../../../styles/LoginForm.css"
import Warning from "../../../Pqrs/Components/Warning.jsx";
import AuthService from "../../Services/AuthService.jsx";
import ApiRequestsManagerService from "../../Services/ApiRequestsManagerService.jsx";

const LoginForm = () => {
  const [cedula, setCedula] = useState("");  
  const [password, setPassword] = useState("");
  const [role, setRole] = useState("");
  const [loading, setLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [warningIsOpen, setWarningIsOpen] = useState(false);

  const navigate = useNavigate();
  const authService=new AuthService(new ApiRequestsManagerService())
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    const response= await authService.login({cedula,password,})
    setLoading(false);
    if (response.message === "success") {
        const user=response.data;
        navigate('/pqrs', { state: user }); 
    }else if (response.message === "error") {
        showError(response.error||"error desconcido")
    }
  }
    const showError=(error)=>{
        setErrorMessage(error);
        setWarningIsOpen(true);
    }
  const redirectToRecoverPassword = () => {
    navigate('/recover_password');
  }
  return (
    <div className="login-container">
      <div className="login-image">
          <img src='https://res.cloudinary.com/dxalvdckk/image/upload/v1747435854/descarga_ztjs3h.png'alt="Logo" className="logo" />
          <div className="clinic-asopormen">
              <strong className="title">HelpDesk</strong>
              <p className="subtitle">Asopormen</p>
          </div>
      </div>
      <p>Ingreso de personal</p>
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <input
            type="text"
            id="cedula"
            value={cedula}
            placeholder="Ingrese tu identificación"
            onChange={(e) => setCedula(e.target.value)}
            required
          />
        </div>
        <div className="form-group">
          <input
            type="password"
            id="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            placeholder="Ingrese la contraseña"
            required
          />
        </div>
        <div className="form-group">
          <select
            id="role"
            value={role}
            onChange={(e) => setRole(e.target.value)}
          >
            <option value="">Seleccionar rol</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
        </div>
        <a onClick={redirectToRecoverPassword}>Olvidé mis datos de acceso</a>
        <button type="submit" disabled={loading}>
          {loading ? "Cargando..." : "Iniciar Sesión"}
        </button>
        
      </form>

        <Warning
          isOpen={warningIsOpen}
          onClose={() => setWarningIsOpen(false)}
          errorMessage={errorMessage}
      />

    </div>
  );
};

export default LoginForm;
