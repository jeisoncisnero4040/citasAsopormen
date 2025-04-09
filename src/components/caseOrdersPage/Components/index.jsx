import React, { useEffect } from "react";
import { useLocation, useNavigate } from 'react-router-dom'; 
import NavbarCitas from "../../NavbarCitas"; 
import CaseVisualizer from "./caseVisualizer";


function IndexOrdersCase() {
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
                <CaseVisualizer user={user}/>
            </div>
        </div>
    );
}

export default IndexOrdersCase;