// src/components/HomePage.js
import React from "react";
import LoginForm from "./LoginForm";
import "../styles/app.css";

const HomePage = () => {
  return (
    <>
      <div className="left"></div>
      <div className="login-form">
        <LoginForm />
      </div>
      <div className="right"></div>
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
