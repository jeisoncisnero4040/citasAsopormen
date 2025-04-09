import React, { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";

import WelcomeCard from "./WelcomeCard";
import NavbarClients from "./NavbarClients";
import "../../styles/clientsPage/ClientPage.css";
import Carrusel from "./Carrusel"
import ContacsAsopormen from "./ContacsAsopormen";
import Footer from "./Footer";


function ClientsPage() {
  const location = useLocation();
  
  const navigate = useNavigate();
  const client = location.state || {};

  useEffect(() => {
    if (!client) {
      navigate("/clientes");
    }
  }, [client, navigate]);


  return (
    <div className="container-citas-client">
      
      <div className="welcome-client">
        <ContacsAsopormen />
        <WelcomeCard client={client} title={"PORTAL DE USUARIOS"}/>
      </div>

      <div className="body">
        <div className="carousel">
          <Carrusel/>
        </div>
        <div className="navbar-clients-page">
            < NavbarClients client={client}/>
        </div>
        
      </div>
      <div className="footer">
        < Footer />
      </div>
    </div>
  );
}

export default ClientsPage;
