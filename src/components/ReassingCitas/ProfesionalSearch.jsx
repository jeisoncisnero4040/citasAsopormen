import React, { Component } from 'react';
import Constans from "../../js/Constans";
import Warning from '../Warning';
import ApiRequestManager from "../../util/ApiRequestMamager";

class ProfesionalSearch extends Component {
    requestManager = new ApiRequestManager();

    constructor(props) {
        super(props);
        this.state = {
            searchQuery: '',
            profesional: {
                name: '',
                speciality: '',
                cedula: ''
            },
            profesional_list: [],
            selectedOption: '',
            loading: false,
            errorMessage: '',
            warningIsOpen: false,
        };
    }

    handleKeyDownInInputLabelSearchProfesional = (event) => {
        if (event.key === 'Enter') {
            this.handleSearchProfesional();
        }
    };

    handleSearchProfesional = () => {
        const url = this.generateApiUrl(this.state.searchQuery);
        this.toggleLoading(true);

        this.requestManager.getMethod(url)
            .then(this.handleSuccessfulResponse)
            .catch(this.handleError)
            .finally(() => {
                this.toggleLoading(false);
            });
    };

    generateApiUrl = (searchQuery) => {
        return `${Constans.apiUrl()}get_profesionals/${searchQuery}`;
    };

    setProfesionalData = (profesional) => {
        const selectedEcc = (profesional.ecc || '').trim();

        this.setState({
            profesional: {
                name: (profesional.enombre || '').trim(),
                speciality: (profesional.nombre || '').trim(),
                cedula: selectedEcc
            },
            selectedOption: selectedEcc,
            profesional_calendar: [],
            profesionalSchedule: [],

        },()=>{
            this.props.getUpdateCalendarPro(this.state.profesional_calendar);
            this.props.getUpdateSchedulePro(this.state.profesionalSchedule);
            this.props.getUpdateProfesional(this.state.profesional);
            this.props.CloseScheduleProfesional()
        });
    };

    handleSuccessfulResponse = (data) => {
        const profesionalList = data.data.data;

        if (profesionalList.length > 0) {
            const firstProfesional = profesionalList[0];
            this.setProfesionalData(firstProfesional);
            this.setState({ profesional_list: profesionalList });
        } else {
            this.setState({ profesional_list: profesionalList });
        }
    };

    handleError = (error) => {
        this.openErrorAlert(error);
    };

    toggleLoading = (isLoading) => {
        this.setState({ loading: isLoading });
    };

    handleSelectChangeProfesional = (event) => {
        const selectedEcc = event.target.value;
        this.setState({ selectedOption: selectedEcc });

        const selectedProfesional = this.state.profesional_list.find(profesional => profesional.ecc.trim() === selectedEcc);

        if (selectedProfesional) {
            this.setProfesionalData(selectedProfesional);
        }
    };

    renderProfesionales = () => {
        if (this.state.profesional_list.length > 0) {
            return this.state.profesional_list.map((profesional, index) => (
                <option key={index} value={profesional.ecc.trim()}>
                    {profesional.enombre.trim()}
                </option>
            ));
        } else {
            return <option>No se encontraron profesionales</option>;
        }
    };

    handleInputLabelSearchProfesional = (event) => {
        this.setState({ searchQuery: event.target.value });
    };

    openErrorAlert = (error) => {
        this.setState({
            errorMessage: error,
            warningIsOpen: true
        });
    };

    render() {
        const { searchQuery, profesional, loading } = this.state;

        return (
            <div>
                <div className="get-all-data-pro">
                    <div>
                        <div className="input-name-pro">
                            <div className="input-name-profesional">
                                <label htmlFor="search-profesional">Buscar Profesional</label>
                                <input
                                    id="search-profesional"
                                    type="search"
                                    placeholder="Buscar por nombre"
                                    value={searchQuery}
                                    onChange={this.handleInputLabelSearchProfesional}
                                    onKeyDown={this.handleKeyDownInInputLabelSearchProfesional}
                                />
                            </div>

                            <div className="select-profesional">
                                <label htmlFor="select-profesional">Seleccionar Profesional</label>
                                <select
                                    id="select-profesional"
                                    name="select-profesional"
                                    value={this.state.selectedOption}
                                    onChange={this.handleSelectChangeProfesional}
                                >
                                    {this.renderProfesionales()}
                                </select>
                            </div>
                        </div>

                        {/* Información del Profesional */}
                        <div className="info-profesional">
                            <div className="read-info-p">
                                <label htmlFor="profesional-name">Nombre:</label>
                                <input
                                    id="profesional-name"
                                    type="text"
                                    value={loading ? 'Buscando...' : profesional?.name || ''}
                                    readOnly
                                />
                            </div>

                            <div className="read-info-p">
                                <label htmlFor="profesional-speciality">Especialidad:</label>
                                <input
                                    id="profesional-speciality"
                                    type="text"
                                    value={loading ? 'Buscando...' : profesional?.speciality || ''}
                                    readOnly
                                />
                            </div>
                            <Warning
                                isOpen={this.state.warningIsOpen}
                                onClose={() => this.setState({ warningIsOpen: false })}
                                errorMessage={this.state.errorMessage}
                            /> 
                        </div>
                    </div>
                </div>


            </div>
        );
    }
}

export default ProfesionalSearch;
