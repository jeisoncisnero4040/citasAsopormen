import React, { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import WelcomeCard from "./WelcomeCard";
import ContacsAsopormen from "./ContacsAsopormen";
import Footer from "./Footer";
import UpdateClientsForms from "./UpdateClientsForms";
import "../../styles/clientsPage/UpadateUser.css"

const UpdateUser = () => {
    const location = useLocation();
    const initialClient = location.state || {};  

    const [client, setClient] = useState(initialClient);

    useEffect(() => {
        setClient(initialClient);
    }, [initialClient]);
    

    return (
        <div className="container-citas-client">
            <div className="welcome-client">
                <ContacsAsopormen />
                <WelcomeCard client={client} title={"ACTUALIZAR DATOS"}/>
            </div>
            <div className="citas-clients">
                <div className="update-client-wrapper">
                     <UpdateClientsForms codigo={client.codigo.trim()} />
                    
                </div>
            </div>
            <div className="footer">
                < Footer />
            </div>
        </div>
    );
};

export default UpdateUser;
