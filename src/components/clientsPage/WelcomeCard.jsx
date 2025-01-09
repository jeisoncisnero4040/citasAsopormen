import React from "react";
import '../../styles/clientsPage/WelcomeCard.css'

class WelcomeCard extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      date: "",
      isOpen: false,  
    };
  }

  setDate = (newDate) => {
    this.setState({
      date: newDate,
    });
  };

  formatDate = (date) => {
    const meses = [
      "enero",
      "febrero",
      "marzo",
      "abril",
      "mayo",
      "junio",
      "julio",
      "agosto",
      "septiembre",
      "octubre",
      "noviembre",
      "diciembre",
    ];

    const dia = date.getDate();
    const mes = meses[date.getMonth()];
    const anio = date.getFullYear();

    return `${dia} de ${mes} de ${anio}`;
  };

  componentDidMount() {
    const now = new Date();
    const dateString = this.formatDate(now);
    this.setDate(dateString);
  }

  
  toggleDropdown = () => {
    this.setState((prevState) => ({
      isOpen: !prevState.isOpen,
    }));
  };
  logout=()=>{
    localStorage.removeItem('authToken');
    localStorage.removeItem('userCitas')
    window.location.href = '/clinico/clientes';
  }

  renderDropdown = () => {
    return (
      <div className="dropdown-card-welcome">
        <button className="dropdown-button" onClick={this.toggleDropdown}>
          {this.props.nameClient ?? 'Opciones'}
        </button>
        {this.state.isOpen && (
          <ul className="dropdown-menu-client">
            <li onClick={this.logout}>Cerrar sesión</li>
          </ul>
        )}
      </div>
    );
  };

  render() {
    return (
      <div className="wrapper-card-welcome">
        <div className="date">
            <p>{this.state.date}</p>
        </div>
        <div className="name-client-and-dropdown">
            {this.renderDropdown()}
        </div>
      </div>
    );
  }
}

export default WelcomeCard;
