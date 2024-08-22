import React, { Component } from "react";
import axios from "axios";  
import Constants from '../js/Constans.jsx';
import Warning from "./Warning";
import '../styles/Centrals.css';

class Procedures extends Component {  
    constructor(props) {
        super(props);
        this.state = {
            offices: [],  
            officeSelected: {
                nombre: '',
                direccion: '',
                cod: ''
            },
            errorMessage: '',
            warningIsOpen: false
        };
    }

    componentDidMount() {
        const url = `${Constants.apiUrl()}get_centrals_office`;
        axios.get(url)
            .then(response => {
                this.setState({
                    offices: response.data.data,
                    officeSelected: {
                        nombre: response.data.data[0].nombre.trim(),
                        direccion: response.data.data[0].direccion.trim(),
                        cod: response.data.data[0].cod.trim()
                    }
                }, () => {
                    this.props.getCentralInfo(this.state.officeSelected);
                });
            })
            .catch(error => {
                if (error.response) {
                    const errorData = error.response.data;
                    this.setState({
                        errorMessage: errorData.error ? errorData.error : 'Error al hacer la petición',
                        warningIsOpen: true,
                    });
                } 
            });
    }

    changeSelectedCentral = (event) => {
        const cod = event.target.value;
        const central = this.state.offices.find(c => c.cod === cod);
        
        if (central) {
            this.setState({
                officeSelected: {
                    nombre: central.nombre.trim(),
                    direccion: central.direccion.trim(),
                    cod: central.cod.trim()
                }
            }, () => {
                this.props.getCentralInfo(this.state.officeSelected);
            });
        }
    }

    changeDirecctionCentralSelected = (event) => {
        const newDirection = event.target.value;
        this.setState((prevState) => ({
            officeSelected: {
                ...prevState.officeSelected,
                direccion: newDirection
            }
        }), () => {
            this.props.getCentralInfo(this.state.officeSelected );
        });
    }

    rendercentrals = () => {
        return this.state.offices.length > 0 ? (
            this.state.offices.map((central, index) => (
                <option key={index} value={central.cod}>
                    {central.nombre.trim()}  
                </option>
            ))
        ) : (
            <option>No se encontraron oficinas</option>
        );
    }

    render() {
        return (
            <div className="central-container">
                <div className="select-central">
                    <label>Seleccionar oficina</label>
                    <select onChange={this.changeSelectedCentral}>  
                        {this.rendercentrals()}  
                    </select>
                </div>
                <div className="show-data-central">
                    <div className="name-central">
                        <label> Nombre Sede</label>
                        <input type="text" 
                            placeholder={this.state.officeSelected.nombre}
                            readOnly
                        />
                    </div>
                    <div className="direccion-central">
                        <label> Dirección Sede</label>
                        <input type="text" 
                            value={this.state.officeSelected.direccion}
                            onChange={this.changeDirecctionCentralSelected}
                        />
                    </div>
                    <div>
                        <Warning
                            isOpen={this.state.warningIsOpen}
                            onClose={() => this.setState({ warningIsOpen: false })}
                            errorMessage={this.state.errorMessage}
                        />
                    </div>
                </div>
            </div>
        );
    }
}

export default Procedures;
