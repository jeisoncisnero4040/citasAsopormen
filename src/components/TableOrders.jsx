import React, { Component } from "react";
import '../styles/TableOrders.css';
import Constans from '../js/Constans.jsx';
import axios from "axios";
import Warning from "./Warning.jsx";

class TableOrders extends Component {
    constructor(props) {
        super(props);
        this.state = {
            authorizations: [],
            authorizationData: [],
            dataExport:{},
            selectedRow: null, 
            errorMessage: '',
            warningIsOpen: false,
        };
    }

    componentDidUpdate(prevProps) {
        if (prevProps.historyNumber !== this.props.historyNumber) {
            this.fetchAuthorizations();
        }
    }


    fetchAuthorizations = () => {
        const url = `${Constans.apiUrl()}clients/get_authorizations/${this.props.historyNumber}/`;
        axios.get(url)
            .then(response => {
                this.handleAuthorizationsSuccess(response.data.data);
            })
            .catch(error => {
                this.setState({
                    authorizations: [],
                    authorizationData: []
                });
                this.handleAuthorizationsError(error);
            });
    }

    fetchDataAuthorization = (n_autoriza) => {
        const url = `${Constans.apiUrl()}clients/get_authorization_data/${n_autoriza}/`;
        axios.get(url)
            .then(response => {
                this.handleAuthorizationDataSuccess(response.data.data);
            })
            .catch(() => {
                this.handleAuthorizationsError("Ha ocurrido un error inesperado al hacer la solicitud");
            });
    }
    fetchCounNumCitas = (n_autoriza, tiempo) => {
        const url = `${Constans.apiUrl()}citas/get_num_citas/${n_autoriza}/${tiempo}/`;
        return axios.get(url)  
            .then(response => {
                return response.data.data;
            })
            .catch(() => {
                this.handleAuthorizationsError("Ha ocurrido un error inesperado al hacer la solicitud");
                 
            });
    }

    handleAuthorizationsSuccess = (data) => {
        this.setState({
            authorizations: Array.isArray(data) ? data : [],
        });
        if (data.length > 0) {
            const n_autoriza = data[0].n_autoriza;
            this.fetchDataAuthorization(n_autoriza);
        }
    }

    handleAuthorizationDataSuccess = (data) => {
        this.setState({
            authorizationData: Array.isArray(data) ? data : [],
        });
    }

    handleAuthorizationsError = (error) => {
        if (error.response) {
            const errorData = error.response.data;
            this.setState({
                errorMessage: errorData.error ? JSON.stringify(errorData.error) : 'Error al hacer la petición',
                warningIsOpen: true,
            });
        }
    }
    handleRowClick = (rowData) => {
        this.setState({ 
            selectedRow: rowData,
            dataExport: {
                n_autoriza: rowData.n_autoriza.trim(), 
                tiempo: rowData.tiempo.trim(),
                procedim: rowData.procedim.trim(),
                cantidad: rowData.cantidad.trim()
            }
        }, () => {
            this.fetchCounNumCitas(this.state.dataExport.n_autoriza, this.state.dataExport.tiempo)
            .then(numCitas => {
                this.props.getCounterCitas(numCitas);
            })
            .catch(error => {
                this.handleAuthorizationsError("Error al obtener el número de citas");
            });
            this.props.getAuthorizationInfo(this.state.dataExport);
        });
    }
    

    renderAuthorizations = () => {
        return this.state.authorizations.map((authorization, index) => (
            <option value={authorization.n_autoriza} key={index}>
                {authorization.n_autoriza + ' -||- ' + authorization.observa}
            </option>
        ));
    }

    renderAuthorizationData = () => {
        return this.state.authorizationData.map((data, index) => (
            <tr
                key={index}
                className={this.state.selectedRow === data ? "selected" : ""}
                onClick={() => this.handleRowClick(data,this.state.authorizationData)}
            >
                <td>{data.tiempo}</td>
                <td>{data.procedim}</td>
                <td>{data.cantidad}</td>
            </tr>
        ));
    }


    render() {
        return (
            <div className="table-container">
                <div className="select-orden-4">
                    <label>Seleccionar Orden {'de '+this.props.nameClient}</label>
                    <select onChange={e => this.fetchDataAuthorization(e.target.value)}>
                        {this.renderAuthorizations()}
                    </select>
                </div>
                <div className="table-orders">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            {this.renderAuthorizationData()}
                        </tbody>
                    </table>
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

export default TableOrders;
