import React, { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import logo from "../assets/logo.png";
import "../styles/LoginForm.css";
import Constants from '../js/Constans.jsx';
import Warning from "./Warning";

const LoginForm = () => {
  const [cedula, setCedula] = useState("");  
  const [password, setPassword] = useState("");
  const [role, setRole] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [warningIsOpen, setWarningIsOpen] = useState(false);

  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");

    try {
      const url = `${Constants.apiUrl()}login`;
      const response = await axios.post(url, {
        cedula,
        password,
      });

      const token = response?.data?.access_token;
      const userData = response?.data?.data;
      
      localStorage.setItem('authToken', token);
      navigate('/formcitas', { state: userData });
      
    } catch (error) {
      if (error.response) {
        const errorData = error.response.data;
        setErrorMessage(errorData.error ? errorData.error : 'Error al hacer la petición');
        setWarningIsOpen(true);
      } 
    } finally {
      setLoading(false);
    }
  };

  const redirectToRecoverPassword = () => {
    navigate('/recover_password');
  }

  return (
    <div className="login-container">
      <div className="login-image">
          <img src={logo} alt="Logo" className="logo" />
          <div className="clinic-asopormen">
              <strong>Clínico</strong>
              <p>Asopormen</p>
          </div>
      </div>
      <p>Ingreso de usuarios</p>
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
        {error && <p className="error">{error}</p>}
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
