import React from "react";
import IndexClients from "./indexClients";
import RequestPasswordForm from "./RequestPasswordForm";

class RequestPasswordClients extends IndexClients{
    renderForm = () => {
        return <div className="wrapper"><RequestPasswordForm/></div>; 
    };

}
export default RequestPasswordClients;