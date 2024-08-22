import React, { Component } from "react";
import axios from "axios";  
import Constants from '../js/Constans.jsx';
import Warning from "./Warning";

class Procedures extends Component {  
    constructor(props) {
        super(props);
        this.state = {
            procedures: [],  
            errorMessage: '',
            warningIsOpen: false
        };
    }

    componentDidMount() {
        const urlForProcedures = `${Constants.apiUrl()}get_procedures`;
        axios.get(urlForProcedures)
            .then(response => {
                this.setState({
                    procedures: response.data.data,
                }, () => {
                    if (this.state.procedures.length > 0) {
                        this.props.getProcedureName(this.state.procedures[0].nombre);
                    }
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

    handleProcedureChange = (event) => {
        this.props.getProcedureName(event.target.value);
    }

    renderProcedures = () => {
        if (this.state.procedures.length > 0) {
            return this.state.procedures.map((procedure, index) => (
                <option key={index} value={procedure.nombre.trim()}>
                    {procedure.nombre.trim()}  
                </option>
            ));
        } else {
            return <option>No se encontraron procedimientos</option>;  
        }
    }

    render() {
        return (
            <div className="procedures-container">
                <label>Seleccionar procedimiento interno</label>
                <select onChange={this.handleProcedureChange}>  
                    {this.renderProcedures()}  
                </select>
                <div>
                    <Warning
                        isOpen={this.state.warningIsOpen}
                        onClose={() => this.setState({ warningIsOpen: false })}
                        errorMessage={this.state.errorMessage}
                    />
                </div>
            </div>
        );
    }
}

export default Procedures;
