import React, { useState } from "react";
import '../styles/NabvarCitas.css';
import logo from "../assets/logo.png";
import { useNavigate } from 'react-router-dom';

const NavbarCitas = ({user}) => {   
    const [isOpcionesCardOpen, setIsOpcionesCardOpen] = useState(false);
    const [isUserCardOpen, setIsUserCardOpen] = useState(false);
    const navigate = useNavigate();
    
    const whileAddFunction = () => {
        navigate('/formcitas', { state: user });
    }
    const redirectToRecoverReassingCitas= () => {
        navigate('/reasignador_citas', { state: user });
    }
    const redirectToChatBotHistory=()=>{
        navigate('/history_chatbot',{ state:user });
    }
    const  redirectToOrdesrsCase=()=>{
        navigate('/orders',{ state:user });
    }
    const  redirectToInformes=()=>{
        navigate('/informes',{ state:user });
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
                        <a onClick={whileAddFunction}>Cargar Agenda</a>
                        <a onClick={redirectToRecoverReassingCitas}>Reasignar Citas</a>
                        <a onClick={redirectToChatBotHistory}>Historial Chat</a>
                        {/*<a onClick={redirectToOrdesrsCase}>Casos de Ordenes</a>*/}
                        <a onClick={redirectToInformes}>Informes</a>
                    </div>
                )}
            </div>
            <div className="card">
                <div className="card-header" onClick={toggleUserCard}>
                    <button className="card-btn">{user.usuario}</button>
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
