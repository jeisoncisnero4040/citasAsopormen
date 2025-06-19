import { useLocation, useNavigate } from 'react-router-dom'; 
import NavbarCitas from "../../NavbarCitas"; 
import DashboarDataPqrs from './DashboardDataPqrs';
import { useEffect } from 'react';

function IndexInformesPqrs() {
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
                <DashboarDataPqrs />
            </div>
        </div>
    );
}

export default IndexInformesPqrs;