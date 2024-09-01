import React, { Component } from "react";
 
import '../styles/RecoverPasswordForm.css';
import UpdatePasswordForm from "./UpdatePasswordForm";

class UpdatePasswordPage extends Component{
    render(){
        return(
            
            <>
                <div className="left"></div>
                <div className="login-form">
                    <UpdatePasswordForm/>
                </div>
                <div className="right"></div>
                <div className="footer-container">
                    <strong>Copyright &copy; 2024
                        <a target="_blank" href="https://asopormen.org.co"> Instituto Asopormen</a>.</strong> Todos los derechos
                            reservados.
                    <b>Versión</b> 1.0
                </div>
            </>


        )
    }
}
 
export default UpdatePasswordPage;