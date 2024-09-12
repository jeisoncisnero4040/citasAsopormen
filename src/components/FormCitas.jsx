import React, { useEffect } from "react";
import { useLocation, useNavigate } from 'react-router-dom';
import '../styles/formCitas.css';
import NavbarCitas from "./NavbarCitas";
import FormCitasForm from "./FormCitasForm";

function FormCitas() {
    const location = useLocation();
    const navigate = useNavigate();   
    const userData = location.state || {};

    useEffect(() => {
        if (!userData || !userData.usuario) {
             
            navigate('/');   
        }
    }, [userData, navigate]);

    return (
        <div className="container">
            <div className="navbar">
                <NavbarCitas userName={userData.usuario} />
            </div>
            <div className="form-citas">
                <FormCitasForm user={userData} />
            </div>
        </div>
    );
}

export default FormCitas;
