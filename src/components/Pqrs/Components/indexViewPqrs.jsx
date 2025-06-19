import React, { useEffect } from "react";
import { useLocation, useNavigate } from 'react-router-dom'; 
import NavbarCitas from "../../NavbarCitas"; 
import PqrsVisualizer from "./PqrsVisualizer";
import '../Styles/IndexPqrs.css'


function IndexViewPqrs() {
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
                <PqrsVisualizer user={user}/>
            </div>
        </div>
    );
}

export default IndexViewPqrs;