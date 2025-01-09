import React from "react";
import '../../styles/ReassingCitas/TableCitasCanceled.css';
import ApiRequestManager from "../../util/ApiRequestMamager";
import Constans from "../../js/Constans";
import Warning from "../Warning";

class TableCitasCanceled extends React.Component {
    requestManager = new ApiRequestManager();
    constructor(props) {
        super(props);
        this.state = {
            citasCanceled: [],
            citasFilteredByCentral: [],
            centrals: [],
            citaSelected: {},
            columsTable: ['cliente', 'profesional', 'fecha', 'procedimiento'],
            pagination: 10,
            page: 0,
            citasOrdenedByDate:false,
            loading:false,

            warningIsOpen:false,
            errorMessage:''

        };
    }

    componentDidMount = async () => {
        try {
            this.setState({loading:true})
            const citasCanceledList = await this._getCitasCanceled();
            const CentrarOffice = this.filterCitasByCentralOffice(citasCanceledList);
            this.setState({
                citasCanceled: citasCanceledList,
                citasFilteredByCentral: citasCanceledList,
                centrals: CentrarOffice,
            });
        } catch (error) {
            this._openError(error);
        }finally{
            this.setState({loading:false})
        }
    };
    
    _getCitasCanceled = async () => {
        try {
            const storedCitas = sessionStorage.getItem('citasCanceled');
            if (storedCitas) {
                return JSON.parse(storedCitas);
            } else {
                const url = `${Constans.apiUrl()}citas/get_citas_canceled`;
                const response = await this.requestManager.getMethod(url);
                const citas = response.data.data; 
                sessionStorage.setItem('citasCanceled', JSON.stringify(citas)); 
                return citas;
            }
        } catch (error) {
            throw error  
        }
    };

    
    filterCitasByCentralOffice = (citasCanceledList) => {
        const uniqueSedes = new Set(citasCanceledList.map(cita => cita.sede));
        return Array.from(uniqueSedes);
    };
    

    handleSedeChange = (event) => {
        const selectedSede = event.target.value;
        const filteredCitas = selectedSede 
            ? this.state.citasCanceled.filter(cita => cita.sede === selectedSede) 
            : this.state.citasCanceled;

        this.setState({ citasFilteredByCentral: filteredCitas, page: 0 });  
    };
    renderTableBody = () => {
        const startPageActual = this.state.page * this.state.pagination; 
        const endPageActual = startPageActual + this.state.pagination;  
    
        if (this.state.citasFilteredByCentral.length !== 0) {
            const citasToRender = this.state.citasFilteredByCentral.slice(startPageActual, endPageActual);
            return (
                <tbody>
                    {citasToRender.map((cita, index) => (
                        <tr 
                            key={cita.id} 
                            className={(cita.id===this.state.citaSelected.id)?
                                        'cita-rendered-selected':
                                        (index % 2 === 0 ? 
                                        'cita-rendered-pair' : 
                                        'cita-rendered-odd')} 
                            onClick={() => this.updateCitaSelected(cita)}
                        >
                            {this.state.columsTable.map((column) => (
                                <td className="td-table-citas-canceled" key={column}>
                                    {cita[column] !== undefined ? cita[column] : '-'}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            );
        } else {
            return (
                <tbody>
                    <tr>
                        <td colSpan={this.state.columsTable.length}>{this.state.loading?'Buscando':'No hay citas disponibles'}</td>
                    </tr>
                </tbody>
            );
        }
    };
    

    renderTableHead = () => {
        return (
            <thead className="table-citas_canceled-header">
                <tr>
                    {this.state.columsTable.map((column, index) => (
                        <th key={index} className="table-citas_canceled-row">
                            {column !== 'fecha' ? (
                                column
                            ) : (
                                <a onClick={()=>this._ordenedCitas()}>
                                    {column} 
                                </a>
                            )}
                        </th>
                    ))}
                </tr>
            </thead>
        );
    };
    _ordenedCitas=()=>{
        const citasOrdened=this.state.citasOrdenedByDate?this._orddenedCitasByName():this._ordenedCitasByDate();
        this.setState({
            citasFilteredByCentral:citasOrdened,
            citasOrdenedByDate:!this.state.citasOrdenedByDate
        })
    }
    _orddenedCitasByName = () => {
        
        const sortedCitas = this.state.citasFilteredByCentral.sort((a, b) => {
            const nameA = a.cliente.toLowerCase();
            const nameB = b.cliente.toLowerCase(); 
            if (nameA < nameB) return -1; 
            if (nameA > nameB) return 1; 
            return 0; 
        });
        
        return sortedCitas;
    }
    _ordenedCitasByDate = () => {
         
        const sortedCitas = this.state.citasFilteredByCentral.sort((a, b) => {
            const dateA = new Date(a.fecha);  
            const dateB = new Date(b.fecha);  
            return dateA - dateB;  
        });
        
        return sortedCitas; 
    }
    



    renderFilterBySedeDropdown = () => {
        return (
            <div>
                <select id="sede-select" onChange={this.handleSedeChange} className="select-central-of-table-citas-canceled">
                    <option value="">Seleccionar sede</option>
                    {this.state.centrals.map((sede, index) => (
                        <option key={index} value={sede}>
                            {sede}
                        </option>
                    ))}
                </select>
            </div>
        );
    };
    updateCitaSelected=(cita)=>{
        this.setState({citaSelected:cita},
            ()=>this.props.getUpdateCita(cita)
        )
    }
    renderPaginationInfo = () => {
        const actualPage = this.state.page + 1;  
        const totalItems = this.state.citasFilteredByCentral.length;
        const allPages = Math.ceil(totalItems / this.state.pagination);  
    
        return (
            <span>Página {actualPage} de {allPages}</span>
        );
    };
    renderButtonsNavigation=()=> {
        const totalItems = this.state.citasFilteredByCentral.length;
        const allPages = Math.ceil(totalItems / this.state.pagination);  
        return (
            <div className="wrapper-navigation-buttons">
                {this.state.page > 0 ? 
                    <button className='button-navigation-table-active' type="button" onClick={this._goToBackPage}>anterior</button> :
                    <button className='button-navigation-table-unactive' type="button" disabled>anterior</button>}
                {this.state.page < allPages - 1 ? 
                    <button className='button-navigation-table-active' type="button" onClick={this._goToNextPage}>siguiente</button> :
                    <button className='button-navigation-table-unactive' type="button" disabled>siguiente</button>}
            </div>
        );
    };
    
    _goToNextPage = () => {
        this.setState(prevState => ({ page: prevState.page + 1 }));
    }
    
    _goToBackPage = () => {
        this.setState(prevState => ({ page: prevState.page - 1 }));
    }
    _openError=(error)=>{
        this.setState({
            warningIsOpen:true,
            errorMessage:error
        })
    }
    
    render() {
        return (
            <div className="table-citas_avaiables-container">
                <div className="title-table-citas-canceled-avaibles"><h2>Citas Canceladas</h2></div>
                <div className="table-citas-canceled-navigation">
                    <div className="filter-sede-dropdown">
                        {this.renderFilterBySedeDropdown()}
                    </div>
                </div>
                <table className="table-citas_canceled">
                    {this.renderTableHead()}
                    {this.renderTableBody()}
                </table>
                <div className="navigation-footer-table-citas-canceled">
                    <div className="info-pagination-table-citas-canceled">
                        {this.renderPaginationInfo()}
                    </div>
                    <div className="button-navigation-table-citas-canceled">
                        {this.renderButtonsNavigation()}
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

export default TableCitasCanceled;
