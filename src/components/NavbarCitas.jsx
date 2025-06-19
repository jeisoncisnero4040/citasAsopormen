import React, { useState } from "react";
import '../styles/NabvarCitas.css';

import { useNavigate } from 'react-router-dom';

const NavbarCitas = ({user}) => {   

    const [isUserCardOpen, setIsUserCardOpen] = useState(false);
    const [isQualityCardOpen,setIsQualityCardOpen]=useState(false);
    const navigate = useNavigate();
    

    const redirectToCreatepQRfORM=()=>{
        navigate('/pqrs/crear',{ state: user })
    }

    const redirectToPqrsPage=()=>{
        navigate('/pqrs',{ state: user })
    }

    const handleLogout = () => {
        localStorage.removeItem('authToken');
        navigate('/');
    };



    const toggleUserCard = () => {
        setIsUserCardOpen(prev => !prev);
    };
    const toggleQualityCard=()=>{
        setIsQualityCardOpen(prev=>!prev);
    }
    const redirectToRecoverPassword = () => {
        navigate('/update_password');
    };

    const redirectToInformesPqrs=()=>{
        navigate('/pqrs/informes',{ state: user });
    }
    return (
        <div className="subnavbar">
            <img src={"https://res.cloudinary.com/dxalvdckk/image/upload/v1747435854/descarga_ztjs3h.png"} alt="asopormen" />
            
            <div className="card">
                <div className="card-header" onClick={toggleQualityCard}>
                    <button className="card-btn">Calidad</button>
                </div>
                {isQualityCardOpen && (
                    <div className="card-body">
                        <a onClick={redirectToPqrsPage}>Pqrs</a>
                        <a onClick={redirectToCreatepQRfORM}>Crear Pqr</a>
                        <a onClick={redirectToInformesPqrs}>Informes</a>
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
