import React, { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import WelcomeCard from "./WelcomeCard";
import ContacsAsopormen from "./ContacsAsopormen";
import Footer from "./Footer";
import ClientCitas from "./ClientCitas";

import "../../styles/clientsPage/UsuariosCitas.css"


const UsuariosCitas = () => {
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
                <WelcomeCard client={client} title={"CANCELAR CITA"}/>
            </div>
            <div className="citas-clients">
                <div className="citas-client-wrapper">
                     
                    < ClientCitas codigo={client.codigo.trim()} subtitle={"CITAS PROGRAMADAS"}/>
                </div>
            </div>
            <div className="footer">
                < Footer />
            </div>
        </div>
    );
};

export default UsuariosCitas;
