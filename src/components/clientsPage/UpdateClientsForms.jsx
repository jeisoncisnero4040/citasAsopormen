import React from "react";
import FormUpdateEmailClient from "./FormUpdateEmailClient";
import FormUpdateNumberCelClient from "./FormUpdateNumberCelClient";
import FormUpdatePasswordClient from "./FormUpdatePasswordClient";
import "../../styles/clientsPage/UpdateClientForms.css";

class UpdateClientsForms extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            codeClient: ""
        };
    }

    componentDidMount() {
         
        this._setCodClient(this.props.codigo.trim());
    }

    _setCodClient = (newCod) => {
        this.setState({ codeClient: newCod });
    };

    render() {
        return ( 
            <div className="update-client-forms">
                <div className="form-to-update-cleint">
                    <FormUpdatePasswordClient codigo={this.state.codeClient} />
                </div>
                <div className="form-to-update-cleint">
                    <FormUpdateEmailClient codigo={this.state.codeClient} />
                </div>
                <div className="form-to-update-cleint">
                    <FormUpdateNumberCelClient codigo={this.state.codeClient} />
                </div>

                
                
            </div>
        );
    }
}

export default UpdateClientsForms;
