import React, { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import WelcomeCard from "./WelcomeCard";
import ContacsAsopormen from "./ContacsAsopormen";
import Footer from "./Footer";
import CitasHistory from "./CitasHistory";
import "../../styles/clientsPage/UsuariosCitas.css"

const UsuarioHistory = () => {
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
                <WelcomeCard client={client} title={"HISTORIAL"}/>
            </div>
            <div className="citas-clients">
                <div className="citas-client-wrapper">
                    < CitasHistory codigo={client.codigo.trim()} subtitle={"HISTORIAL DE CITAS"}/> 
                    
                </div>
            </div>
            <div className="footer">
                < Footer />
            </div>
        </div>
    );
};

export default UsuarioHistory;