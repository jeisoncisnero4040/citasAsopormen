import React from "react";
import '../../styles/clientsPage/indexClients.css'
import LoginClients from "./LoginClients";
import Footer from "./Footer";

class IndexClients extends React.Component {

    renderForm = () => {
        return <div className="wrapper-login">
            < LoginClients/>
        </div>; 
    };


    render() {
        return (
            <div className="index-clientsPage">
                {this.renderForm()}
            </div>
        );
    }
}

export default IndexClients;
