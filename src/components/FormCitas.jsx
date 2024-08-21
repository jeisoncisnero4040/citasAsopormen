import React from "react";
import { useLocation } from 'react-router-dom';
import '../styles/formCitas.css';
import NavbarCitas from "./NavbarCitas";
import FormCitasForm from "./FormCitasForm";
 

function FormCitas() {
    const userData = useLocation().state || {};

    return (
        <div className="container">
            <div className="navbar">
                <NavbarCitas userName={userData.usuario} />
            </div>
            <div className="form-citas">
                <FormCitasForm user={userData}/>
            </div>
        </div>
    );
}

export default FormCitas;
