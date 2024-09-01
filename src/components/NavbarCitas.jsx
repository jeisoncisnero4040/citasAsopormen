import React, { useState } from "react";
import '../styles/NabvarCitas.css';
import logo from "../assets/logo.png";
import { useNavigate } from 'react-router-dom';

const NavbarCitas = ({ userName = '' }) => {   
    const [isOpcionesCardOpen, setIsOpcionesCardOpen] = useState(false);
    const [isUserCardOpen, setIsUserCardOpen] = useState(false);
    const navigate = useNavigate();
    
    const whileAddFunction = () => {
        navigate('/formcitas', { state: { userName } });
    }

    const handleLogout = () => {
        localStorage.removeItem('authToken');
        navigate('/');
    };

    const toggleOpcionesCard = () => {
        setIsOpcionesCardOpen(prev => !prev);
    };

    const toggleUserCard = () => {
        setIsUserCardOpen(prev => !prev);
    };
    const redirectToRecoverPassword = () => {
        navigate('/update_password');
    };
    return (
        <div className="subnavbar">
            <img src={logo} alt="asopormen" />
            
            <div className="card">
                <div className="card-header" onClick={toggleOpcionesCard}>
                    <button className="card-btn">Programador</button>
                </div>
                {isOpcionesCardOpen && (
                    <div className="card-body">
                        <a href={whileAddFunction}>Cargar Agenda</a>
                        <a href={whileAddFunction}>Ver Paciente</a>
                        <a href={whileAddFunction}>Ver Terapeuta</a>
                    </div>
                )}
            </div>
            
            <div className="link">
                <a href={whileAddFunction}>Clinico</a>
            </div>
            
            <div className="card">
                <div className="card-header" onClick={toggleUserCard}>
                    <button className="card-btn">{userName.trim()}</button>
                </div>
                {isUserCardOpen && (
                    <div className="card-body">
                        <a onClick={redirectToRecoverPassword}>Cambiar contraseña</a>
                        <a onClick={handleLogout}>Cerrar sesión</a>
                    </div>
                )}
            </div>
        </div>
    );
};

export default NavbarCitas;
