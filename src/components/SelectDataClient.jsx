import React, { Component } from 'react';
import Constants from '../js/Constans.jsx';
import '../styles/SelectDataClient.css';
import Warning from "./Warning";
import ApiRequestManager from '../util/ApiRequestMamager.js';

class SelectDataClient extends Component {
    requestManager=new ApiRequestManager();
    constructor(props) {
        super(props);
        this.state = {
            client: {
                codigo: "",
                nombre: "",
            },
            dataClient: {},
            clientList: [],
            searchQuery: "",
            selectedOption: "",
            loading: false
        };
    }

    handleKeyDown = (event) => {
        if (event.key === 'Enter') {
            this.setState({ loading: true });

            const url = `${Constants.apiUrl()}get_clients/${this.state.searchQuery}/`;
            this.requestManager.getMethod(url)
                .then(response => {
                     
                        const clientList = response.data.data;
 
                        if (clientList.length > 0) {
                            const firstClient = clientList[0];
                            this.setState({
                                clientList: clientList,
                                client: {
                                    nombre: firstClient.nombre.trim(),
                                    codigo: firstClient.codigo.trim()
                                },
                                loading: false
                            });
                            
                             
                            this.getClientInfo(firstClient.codigo);
                        } else {
                            this.setState({ clientList: [], loading: false });
                             
                        }
                     
                })
                .catch(error => {
                    this.setState({
                        errorMessage: error ,
                        warningIsOpen: true,
                        loading:false
                    });
                });
        }
    }
    renderClients() {
        if (this.state.clientList.length > 0) {
            return this.state.clientList.map((client, index) => (
                <option key={index} value={client.codigo}>
                    {client.nombre}
                </option>
            ));
        } else {
            return <option>No se encontraron clientes</option>;
        }
    }

    handleInputChange = (event) => {
        this.setState({ searchQuery: event.target.value });
    }

    handleSelectChange = (event) => {
        const selectedCodigo = event.target.value;
        const selectedClient = this.state.clientList.find(client => client.codigo === selectedCodigo);
        if (selectedClient) {
            this.setState({
                client: {
                    nombre: selectedClient.nombre.trim(),
                    codigo: selectedClient.codigo.trim(),
                },
                selectedOption: selectedCodigo
            }, () => {
                this.getClientInfo(selectedCodigo);
            });
        }
    }

    getClientInfo = (codigo) => {
        this.setState({ loading: true });

        const url = `${Constants.apiUrl()}client_info`;

 
        this.requestManager.postMethod(url, {
            'historyId': codigo
        })
        .then(response => {
            const clientData = response.data.data[0];
            const trimmedClientData = this.trimObjectValues(clientData);
            this.setState({ dataClient: trimmedClientData });
            this.handleClientSelection(trimmedClientData);  
        })
        .catch(error => {  
            this.setState({
                errorMessage: error,
                warningIsOpen: true,
            });  
        })
        .finally(() => {
            this.setState({ loading: false });
        });
    }

    handleClientSelection = (selectedClient) => {
        this.props.getClienInfo(selectedClient);
    }

    //pasar a un pakete de utils
    trimObjectValues = (obj) => {
        return Object.keys(obj).reduce((acc, key) => {
            const value = obj[key];
            acc[key] = typeof value === 'string' ? value.trim() : value;
            return acc;
        }, {});
    };
    
    render() {
        return (
            <div className="secet-data-client">
                <div className='input-name-client'>
                    <div className="input-name-profesional">
                        <label>Buscar Cliente</label>
                        <input 
                            type="search"
                            placeholder="Buscar por nombre"
                            value={this.state.searchQuery} 
                            onChange={this.handleInputChange} 
                            onKeyDown={this.handleKeyDown} 
                        />
                    </div>
                    <div className="select-profesional">
                        <label>Seleccionar Cliente</label>
                        <select 
                            name="select-profesional" 
                            value={this.state.selectedOption}
                            onChange={this.handleSelectChange}
                        >
                            {this.renderClients()}
                        </select>
                    </div>
                </div>
                <div className='show-data-client-3'>
                    <div className="read-info-p">
                            <label>Nombre</label>
                            <input 
                                type="text"
                                placeholder={this.state.loading ? 'Buscando...' : this.state.dataClient.nombre}
                                readOnly
                            />
                    </div>
                    <div className="read-info-p">
                        <label>Numero de hístoria</label>
                        <input
                            type="text"
                            placeholder={this.state.loading ? 'Buscando...' : this.state.dataClient.codigo}
                            readOnly
                        />
                    </div>
                    <div className="read-info-p">
                        <label>Direccion</label>
                        <input 
                            type="text"
                            placeholder={this.state.loading ? 'Buscando...' :this.state.dataClient.direcc }
                                readOnly
                        />
                    </div>


                </div>

                <div className='show-data-client-3'>
                    <div className="read-info-p">
                        <label>Número de cedula</label>
                        <input
                            type="text"
                            placeholder={this.state.loading ? 'Buscando...' : this.state.dataClient.nit_cli}
                            readOnly
                        />
                    </div>
                    <div className="read-info-p">
                        <label>Entidad</label>
                        <input 
                            type="text"
                            placeholder={this.state.loading ? 'Buscando...' : this.state.dataClient.entidad}
                                readOnly
                        />
                    </div>
                    <div className="read-info-p">
                        <label>Contacto</label>
                        <input
                            type="text"
                            placeholder={this.state.loading ? 'Buscando...' : this.state.dataClient.cel}
                            readOnly
                        />
                    </div>


                </div>

                <div className='show-data-client-4' >
                    <div className="read-info-p">
                        <label>Barrio</label>
                        <input
                            type="text"
                            placeholder={this.state.loading ? 'Buscando...' : this.state.dataClient.barrio}
                            readOnly
                        />
                    </div>
                    <div className="read-info-p">
                        <label>Municipio</label>
                        <input
                            type="text"
                            placeholder={this.state.loading ? 'Buscando...' : this.state.dataClient.municipio}
                            readOnly
                        />
                    </div>
                    <div className='sex-and-age'>
                        <div className="read-info-p">
                            <label>Sexo</label>
                            <input
                                type="text"
                                placeholder={this.state.loading ? 'Buscando...' : this.state.dataClient.sexo}
                                readOnly
                            />
                        </div>
                        <div className="read-info-p">
                            <label>edad</label>
                            <input
                                type="text"
                                placeholder={this.state.loading ? 'Buscando...' : this.state.dataClient.f_nacio}
                                readOnly
                            />
                        </div>
                    </div>

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

export default SelectDataClient;
