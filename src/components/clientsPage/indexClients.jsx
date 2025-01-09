import React from "react";
import '../../styles/clientsPage/indexClients.css'
import LoginClients from "./LoginClients";
import Footer from "./Footer";

class IndexClients extends React.Component {

    renderForm = () => {
        return <div className="wrapper"><LoginClients/></div>; 
    };


    render() {
        return (
            <div className="index-clientsPage">
                <div className="index-clientsPage-body">
                    <div className="index-clientsPage-left"></div>
                    <div className="index-clientsPage-center">
                        {this.renderForm()}
                    </div>
                    <div className="index-clientsPage-rifth"></div>
                </div>
                <div className="index-clientsPage-footer">
                     < Footer />
                </div>
            </div>
        );
    }
}

export default IndexClients;
