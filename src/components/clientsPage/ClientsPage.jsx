import React, { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import ClientCitas from "./ClientCitas";
import WelcomeCard from "./WelcomeCard";
import SloganAndLogo from "./SloganAndLogo";
import NavbarClients from "./NavbarClients";
import "../../styles/clientsPage/ClientPage.css";
import UpdateClientForm from "./UpdateClientForm";
import Carrusel from "./Carrusel"


function ClientsPage() {
  const location = useLocation();
  
  const navigate = useNavigate();
  const client = location.state || {};


  const [selectedOption, setSelectedOption] = useState("start");

  useEffect(() => {
    if (!client) {
      navigate("/clientes");
    }
  }, [client, navigate]);


  return (
    <div className="container-citas-client">
     
      <div className="welcome-client">
        <WelcomeCard nameClient={client.nombre} />
      </div>

      <div className="logo-and-slogan-asopormen">
        <SloganAndLogo />
      </div>

      {/* Navbar */}
      <div className="navbar-client-page">
        <NavbarClients
            selectedOption={selectedOption}
            onOptionSelect={setSelectedOption}
            />
      </div>

       
      <div className="citas-client-cards-or-update-pasword-form">
        {selectedOption === "start" && (
          <div>
             <Carrusel/> 
          </div>
        )}
        {selectedOption === "citas" && (
          <div>
             <ClientCitas codigo={client.codigo}/> 
          </div>
        )}
        {selectedOption === "actualizar" && (
          <div className="wrapper-citas-client">
              <UpdateClientForm codigo={client.codigo}/>
          </div>
        )}
      </div> 
    </div>
  );
}

export default ClientsPage;
