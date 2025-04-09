import React from "react";
import IndexClients from "./indexClients";
import RequestPasswordForm from "./RequestPasswordForm";

class RequestPasswordClients extends IndexClients{
    renderForm = () => {
        return <div className="wrapper-login">
            < RequestPasswordForm/>
        </div>; 
    };

}
export default RequestPasswordClients;