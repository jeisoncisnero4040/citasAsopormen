
import React, { useEffect } from "react";
import { useLocation, useNavigate } from 'react-router-dom'; 
import NavbarCitas from "../../NavbarCitas"; 
import CreatepqrForm from "./CreatepqrForm";


function IndexCreatePqrs() {
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
                <CreatepqrForm user={user}/>
            </div>
        </div>
    );
}

export default IndexCreatePqrs;