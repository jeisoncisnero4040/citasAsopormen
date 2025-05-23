import React, { useEffect } from "react";
import { useLocation, useNavigate } from 'react-router-dom';
import '../../../styles/formCitas.css';
import NavbarCitas from "../../NavbarCitas";
import InformesFactory from "./InformesFactory";

function IndexInformes() {
    const location = useLocation();
    const navigate = useNavigate();   
    const user = location.state || {};

    useEffect(() => {
        if (!user || !user.usuario) {
             
            navigate('/');   
        }
    }, [user, navigate]);

    return (
        <div className="container">
            <div className="navbar">
                <NavbarCitas user={user} />
            </div>
            <div className="form-citas">
                <InformesFactory />
            </div>
        </div>
    );
}

export default IndexInformes;
