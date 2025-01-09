// src/components/NavbarClients.js
import React from "react";
import "../../styles/clientsPage/Navbar.css";

class NavbarClients extends React.Component {
  constructor(props) {
    super(props);
  }

  // Maneja la selección de la opción
  handleOptionSelect = (option) => {
    const { onOptionSelect } = this.props;
    if (onOptionSelect) {
      onOptionSelect(option);
    }
  };

  render() {
    const { selectedOption } = this.props;

    return (
      <div className="container-navbar-client">

        <div className="navbar-client">
          <div
            className={`redirect-to-citas-component ${
              selectedOption === "citas" ? "active" : ""
            }`}
            onClick={() => this.handleOptionSelect("start")}
          >
            <a className="navbar-client" >
              Inicio
            </a>
          </div>
         
         <div
           className={`redirect-to-citas-component ${
             selectedOption === "citas" ? "active" : ""
           }`}
           onClick={() => this.handleOptionSelect("citas")}
         >
           <a className="navbar-client" >
             Citas
           </a>
         </div>
 
          
         <div
           className={`redirect-to-userdatas-update " : ""
           }`}
           onClick={() => this.handleOptionSelect("actualizar")}
         >
           <a className="navbar-client" >
             Actualizar datos
           </a>
         </div>
       </div>
      </div>

    );
  }
}

export default NavbarClients;
