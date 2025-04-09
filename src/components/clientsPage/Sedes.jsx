import React, { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import WelcomeCard from "./WelcomeCard";
import ContacsAsopormen from "./ContacsAsopormen";
import Footer from "./Footer";
import CardSede from "./CardSede";
 
import "../../styles/clientsPage/UpadateUser.css"

const Sedes = () => {
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
                <WelcomeCard client={client} title={"NUESTRAS SEDES"}/>
            </div>
            <div className="citas-clients">
                <div className="update-client-wrapper">
                    <CardSede />
                    
                </div>
            </div>
            <div className="footer">
                < Footer />
            </div>
        </div>
    );
};

export default Sedes;