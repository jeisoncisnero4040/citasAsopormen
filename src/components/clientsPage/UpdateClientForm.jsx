import React from "react";
import '../../styles/clientsPage/UpdateClientForm.css'
import FormUpdatePasswordClient from "./FormUpdatePasswordClient";
import FormUpdateEmailClient from "./FormUpdateEmailClient";
import FormUpdateNumberCelClient from "./FormUpdateNumberCelClient";

class UpdateClientForm extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      clientCod: "",
      formSelected: "password",
    };
  }

  componentDidMount = () => {
    this.setCodigoClient(this.props.codigo);
  };

  setCodigoClient = (newCod) => {
    this.setState({ clientCod: newCod });
  };

  setSelectedForm = (form) => {
    this.setState({ formSelected: form });
  };

  renderForm = () => {
    if (this.state.formSelected === "password") {
      return (
            <FormUpdatePasswordClient codigo={this.state.clientCod} />
      );
    }
    if (this.state.formSelected === "email") {
      return (<FormUpdateEmailClient codigo={this.state.clientCod} />);
    }
    if (this.state.formSelected === "telephoneNumber") {
      return (<FormUpdateNumberCelClient codigo={this.state.clientCod}/>);
    }
  };

  render() {
    return (
      <div className="container-update-client">
        <div className="navbar-update-client">
          <a className={this.state.formSelected==='password'?
                        'navbar-update-client-selected':
                        'navbar-update-client-option'} 
                        onClick={() => this.setSelectedForm("password")}>
            Actualizar Contraseña
          </a>
          <a className={this.state.formSelected==='email'?
                        'navbar-update-client-selected':
                        'navbar-update-client-option'} 
                        onClick={() => this.setSelectedForm("email")}>
            Actualizar Email
          </a>
          <a className={this.state.formSelected==='telephoneNumber'?
                        'navbar-update-client-selected':
                        'navbar-update-client-option'} 
                        onClick={() => this.setSelectedForm("telephoneNumber")}>
            Actualizar Número de Teléfono
          </a>
        </div>
        <div className="form-content">{this.renderForm()}</div>
      </div>
    );
  }
}

export default UpdateClientForm;
