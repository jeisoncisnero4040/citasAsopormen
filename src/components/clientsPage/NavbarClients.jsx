// src/components/NavbarClients.js
import React from "react";
import { useNavigate,useLocation } from "react-router-dom";
import "../../styles/clientsPage/Navbar.css";
import citas from "../../assets/image.navbar.clients.cancel.cita.png";
import sedes from "../../assets/image.navbar.clients.asopormen.sedes.png";
import update from "../../assets/image.navbar.clients.update.info.png";
import history from "../../assets/image.navbar.clients.historial.png"

const NavbarClients = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const client = location.state || {};

  return (
    <div className="container-navbar-client">
      <div className="container-navbar-citas-item">
        <a onClick={() => navigate("citas",{state:client})}>
          <img src={citas} alt="citas" />
          <p>CANCELAR CITA</p>
        </a>
      </div>
      <div className="container-navbar-citas-item">
        <a onClick={() => navigate("historial",{state:client})}>
          <img src={history} alt="history" />
          <p>HISTORIAL</p>
        </a>
      </div>
      <div className="container-navbar-citas-item">
        <a onClick={() => navigate("actualizar-datos",{state:client})}>
          <img src={update} alt="update" />
          <p>ACTUALIZAR DATOS</p>
        </a>
      </div>
      <div className="container-navbar-citas-item">
        <a onClick={() => navigate("nuestras-sedes",{state:client})}>
          <img src={sedes} alt="sedes" />
          <p>NUESTRAS SEDES</p>
        </a>
      </div>
    </div>
  );
};

export default NavbarClients;
