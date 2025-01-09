import React, { useEffect } from "react";
import { useLocation, useNavigate } from 'react-router-dom'; 
import NavbarCitas from "../NavbarCitas"; 
import ReassingCitasForm from "./ReassingCitasForm";

function ReassingCitasPage() {
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
                <ReassingCitasForm user={user} />
            </div>
        </div>
    );
}

export default ReassingCitasPage;
