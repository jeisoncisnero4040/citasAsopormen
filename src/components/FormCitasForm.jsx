import React, { Component } from "react";
import '../styles/selectProfesional.css';
import '../styles/SelectOrder.css';
import Constants from '../js/Constans.jsx';
import ProfesionalCalendar from "./ProfesionalCalendar.jsx";
import axios from 'axios';
import SelectDataClient from "./SelectDataClient.jsx";
import Warning from "./Warning";
import TableOrders from "./TableOrders.jsx";
import Procedures from "./Procedures.jsx";
import SelectCentral from "./SelectCentral.jsx"

class FormCitasForm extends Component {
    constructor(props) {
        super(props);
        this.state = {
            central:{
                'nombre':'',
                'direccion':''
            },
            procedure:[],
            client:{},
            profesional_calendar: [],
            profesional_list: [],
            profesional: {
                name: "",
                speciality: "",
                cedula: '',
            },
            
            loading: false,
            searchQuery: "",
            selectedOption: "",
            errorMessage:'',
            warningIsOpen:false
        };
    }
    componentDidMount() {
        const urlForCentrals = `${Constants.apiUrl()}get_centrals_office`;
 

        axios.get(urlForCentrals)
            .then(response => {
                this.setState({
                    centrals: response.data.data,
                });
            })
            .catch(error => {
                this.setState({
                    error: 'Hubo un error al cargar los datos',
                    loading: false
                });
            });
    }

    handleKeyDown = (event) => {
        if (event.key === 'Enter') {
            this.setState({ loading: true });

            const url = `${Constants.apiUrl()}get_profesionals/${this.state.searchQuery}/`;
            axios.get(url)
                .then(response => {
                    if (response.status === 200) {
                        const profesionalList = response.data.data;
                        if (profesionalList.length > 0) {
                            const firstProfesional = profesionalList[0];
                            const selectedEcc = firstProfesional.ecc.trim();

                            this.setState({
                                profesional_list: profesionalList,
                                profesional: {
                                    name: firstProfesional.enombre.trim(),
                                    speciality: firstProfesional.nombre.trim(),
                                    cedula: selectedEcc
                                },
                                selectedOption: selectedEcc
                            }, () => {
                                this.getCalendarProfesional(selectedEcc);
                            });
                        } else {
                            this.setState({ profesional_list: profesionalList });
                        }
                    }
                })
                .catch(error => {
                    if (error.response ) {
                        const errorData = error.response.data;
                        this.setState({
                            errorMessage: errorData.error ? errorData.error : 'Error al hacer la petición',
                            warningIsOpen: true,
                        });
                         
                    } 
                })
                .finally(() => {
                    this.setState({ loading: false });
                });
        }
    }

    handleInputChange = (event) => {
        this.setState({ searchQuery: event.target.value });
    }

    handleSelectChange = (event) => {
        const selectedEcc = event.target.value;
        this.setState({ selectedOption: selectedEcc });
        const selectedProfesional = this.state.profesional_list.find(profesional => profesional.ecc.trim() === selectedEcc);
        if (selectedProfesional) {
            this.setState({
                profesional: {
                    name: selectedProfesional.enombre.trim(),
                    speciality: selectedProfesional.nombre.trim(),
                    cedula: selectedProfesional.ecc.trim()
                }
            });
            this.getCalendarProfesional(selectedEcc);
        }
    }

    getCalendarProfesional = (cedulaProfesional) => {
        this.setState({ loading: true });

        const url = `${Constants.apiUrl()}get_profesional_calendar/${cedulaProfesional}/`;

        axios.get(url)
            .then(response => {
                if (response.status === 200) {
                    this.setState({ profesional_calendar: response.data.data });
                     
                } else {
                    alert(`Error: No se encontró el calendario`);
                }
            })
            .catch(() => {
                alert(`Error: No se pudo obtener el calendario`);
            })
            .finally(() => {
                this.setState({ loading: false });
            });
    }

    renderProfesionales() {
        if (this.state.profesional_list.length > 0) {
            return this.state.profesional_list.map((profesional, index) => (
                <option key={index} value={profesional.ecc.trim()}>
                    {profesional.enombre.trim()}
                </option>
            ));
        } else {
            return <option>No se encontraron profesionales</option>;
        }
    }

    getProcedureName=(updatedProcedure)=>{
        this.setState({ procedure: updatedProcedure });
    }
    getClienInfo = (updatedClient) => {
        this.setState({ client: updatedClient });
    }
    getCentralInfo=(UpadtedCentral)=>{
        this.setState({ central: 
            {
                nombre:UpadtedCentral.nombre,
                direccion:UpadtedCentral.direccion,
            }
         });
    }

 
    render() {
        return (
            <div className="form-citas-1">
                <div className="header-form-citas">
                    <p>PROGRAMADOR DE CITAS</p>
                </div>
                <form action="">
                    <div className="body-form-citas">
                        <div className="select-data">
                            <div className="data-profesional">
                                <div className="get-all-data-pro">
                                    <div className="input-name-pro">
                                        <div className="input-name-profesional">
                                            <label>Buscar Profesional</label>
                                            <input 
                                                type="text"
                                                placeholder="Buscar por nombre"
                                                value={this.state.searchQuery} 
                                                onChange={this.handleInputChange} 
                                                onKeyDown={this.handleKeyDown} 
                                            />
                                        </div>
                                        <div className="select-profesional">
                                            <label>Seleccionar Profesional</label>
                                            <select 
                                                name="select-profesional" 
                                                value={this.state.selectedOption}
                                                onChange={this.handleSelectChange}
                                            >
                                                {this.renderProfesionales()}
                                            </select>
                                        </div>
                                </div>
                                    <div className="info-profesional">
                                        <div className="read-info-p">
                                            <label>Nombre:</label>
                                            <input
                                                type="text"
                                                placeholder={this.state.loading ? 'Buscando...' : this.state.profesional.name}
                                                readOnly
                                            />
                                        </div>
                                        <div className="read-info-p">
                                            <label>Especialidad:</label>
                                            <input
                                                type="text"
                                                placeholder={this.state.loading ? 'Buscando...' : this.state.profesional.speciality}
                                                readOnly
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div className="calendar-small-screen">
                                    <ProfesionalCalendar events={this.state.profesional_calendar} nameProfesional={this.state.profesional.name}/> 
                                </div>
                                <div className="get-client-info">
                                    
                                    <SelectDataClient getClienInfo={this.getClienInfo}/>
                                </div>
                            </div>
                            <div className="calendario-profesional">
                                <ProfesionalCalendar events={this.state.profesional_calendar} nameProfesional={this.state.profesional.name}/> 
                            </div>
                        </div>
                        <div className="select-orden">
                            <div className="data-order">
                                <div className="get-order">
                                    <TableOrders/>
                                </div>
                                <div className="get-order">
                                    <Procedures getProcedureName={this.getProcedureName}/> 
                                </div>
                                <div className="get-order">
                                    <SelectCentral getCentralInfo={this.getCentralInfo}/> 
                                </div>
                            </div>
                            <div className="create-cita">
                                <p>{JSON.stringify(this.state.procedure)}</p>
                                <p>{JSON.stringify(this.state.central)}</p>
                            </div>
                        </div>
 
                        
                    </div>
                </form>
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

export default FormCitasForm;
