// src/components/HomePage.js
import React from "react";
import LoginForm from "./Pqrs/Auth/Components/LoginForm";
import "../styles/app.css";

const HomePage = () => {
  return (
    <>

      <div className="index-clientsPage">
        <LoginForm />
      </div>

      <div className="footer-container">
            <strong>Copyright &copy; 2024
                <a target="_blank" href="https://asopormen.org.co"> Instituto Asopormen</a>.</strong> Todos los derechos
                     reservados.
            <b>Versión</b> 1.0
      </div>
    </>
  );
};

export default HomePage;   
