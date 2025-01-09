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
            warningIsOpen: false,
            stringToSearch:'',
            isSearch:false
        };
    }
    handleProcedureToSearch=(event)=>{
        this.setState(
            {
                stringToSearch:event.target.value
            }
        )
    }
    GetProcedure=(event)=>{
        if(event.key==='Enter'){
            this.setState({isSearch:true})
            this.serachProcedureByQuery()


        }
    }
    serachProcedureByQuery=()=>{
        const urlForProcedures = `${Constants.apiUrl()}get_procedures/${this.state.stringToSearch}`;
        axios.get(urlForProcedures)
            .then(response => {
                
                this.setState({
                    procedures: response.data.data,
                    procedureSelected: response.data.data[0],
                    isSearch:false,
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
                        isSearch:false,
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
    handleChangeInCheckBoxWhatsappNotification = () => {
        this.setState(prevState => ({
            procedureSelected: {
                ...prevState.procedureSelected,
                sendWhatsappConfirmation: !prevState.procedureSelected.sendWhatsappConfirmation
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
                <div className="search-select-procedure">
                    <div className="input-name-profesional">
                        <label>Buscar Procedimiento</label>
                        <input 
                            type="search"
                            placeholder="Buscar procedimiento"
                            value={this.state.searchQuery} 
                            onChange={this.handleProcedureToSearch} 
                            onKeyDown={this.GetProcedure} 
                        />
                    </div>
                    <div className="input-name-profesional">
                        <label>Seleccionar procedimiento interno</label>
                        <select onChange={this.handleProcedureChange}>  
                            {this.renderProcedures()}  
                        </select>
                    </div>

                </div>
                <div className="info-procedure">
                    <div className="read-info-p">
                        <label>Nombre:</label>
                        <input
                            type="text"
                            placeholder={this.state.isSearch? 'Buscando...' : this.state.procedureSelected.nombre}
                            readOnly
                        />
                    </div>
                    <div className="read-info-p">
                        <label>Duracion:</label>
                        <input
                            type="text"
                            placeholder={this.state.isSearch ? 'Buscando...' : (this.state.procedureSelected.duraccion?this.state.procedureSelected.duraccion+' minutos':null)}
                            readOnly
                        />
                    </div>

                </div>
                <div className="checboxs-remember-whtsapp">
                    <label>
                        <input
                            type="checkbox"
                            checked={this.state.procedureSelected.recordatorio_whatsapp || false}
                            onChange={this.handleChangeInCheckBoxWhatsapp}
                        />
                        Recordatorio whatsapp

                    </label>
                    <label>
                    <input
                            type="checkbox"
                            checked={this.state.procedureSelected.sendWhatsappConfirmation || false}
                            onChange={this.handleChangeInCheckBoxWhatsappNotification}
                        />
                        Enviar confirmación de programación
                    </label>
                </div>
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
