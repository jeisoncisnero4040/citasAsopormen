import React, { Component } from "react";
import axios from "axios";  
import Constants from '../js/Constans.jsx';
import Warning from "./Warning";
import '../styles/Procedures.css'

class Procedures extends Component {  
    constructor(props) {
        super(props);
        this.state = {
            procedures: [],
            procedureSelected: {},  
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
                    procedureSelected: response.data.data[0]
                }, () => {
                    this.props.getProcedureName(this.state.procedureSelected)
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
        const nameProcedure = event.target.value;
        const newProcedureSelected = this.state.procedures.find(procedure => procedure.nombre.trim() === nameProcedure);
        this.setState({ procedureSelected: newProcedureSelected }, () => {
            this.props.getProcedureName(this.state.procedureSelected);
        });
    }

    handleChangeInCheckBoxWhatsapp = () => {
        this.setState(prevState => ({
            procedureSelected: {
                ...prevState.procedureSelected,
                recordatorio_whatsapp: !prevState.procedureSelected.recordatorio_whatsapp
            }
        }), () => {
            this.props.getProcedureName(this.state.procedureSelected);
        });
    }

    renderProcedures = () => {
        if (this.state.procedures.length > 0) {
            return this.state.procedures.map((procedure, index) => (
                <option key={index} value={procedure.nombre.trim()}>
                    {`${procedure.nombre.trim()} - ${procedure.duraccion} mins`}
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

                <label>
                    <input
                        type="checkbox"
                        checked={this.state.procedureSelected.recordatorio_whatsapp || false}
                        onChange={this.handleChangeInCheckBoxWhatsapp}
                    />
                    Recordatorio whatsapp
                </label>

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
