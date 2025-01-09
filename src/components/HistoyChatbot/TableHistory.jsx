import TableCitasCanceled from "../ReassingCitas/TableCitasCanceled";
import Info from "../Info";
import Warning from "../Warning";

class TableHistory extends TableCitasCanceled{

    constructor(props) {
        super(props);
        this.state = {
            messages: [],
            messagesFileteredByActions: [],
            actions: [],
            columsTable: ['fecha', 'mensaje', 'accion', 'estado'],
            pagination: 10,
            page: 0,
            info: '',
            title: '',
            infoIsOpen: false,

        };
    }

    componentDidMount = () => {


        const ActionsMessage = this._filterActiosMessage(this.props.historyChat);
        this.setState({
            messages: this.props.historyChat,
            messagesFileteredByActions: this.props.historyChat,
            actions: ActionsMessage,
        });
       
    };
    componentDidUpdate(prevProps) {
        if (prevProps.historyChat !== this.props.historyChat) {

            const ActionsMessage = this._filterActiosMessage(this.props.historyChat);
            this.setState({
                messages: this.props.historyChat,
                messagesFileteredByActions: this.props.historyChat,
                actions: ActionsMessage,
                page:0
            });
        }
    }
    
    _filterActiosMessage = (messages) => {
        const uniqueMsmActions = new Set(messages.map(msm=> msm.accion));
        return Array.from(uniqueMsmActions);
    };
    

    handleActionChange = (event) => {
        const selectedAction = event.target.value;
        const filteredMessages = selectedAction
            ? this.state.messages.filter(msm => msm.accion ===selectedAction) 
            : this.state.messages;

        this.setState({ messagesFileteredByActions:filteredMessages, page: 0 });  
    };
    renderTableBody = () => {
        const startPageActual = this.state.page * this.state.pagination; 
        const endPageActual = startPageActual + this.state.pagination;  
    
        if (this.state.messagesFileteredByActions.length !== 0) {
            const messagesToRender = this.state.messagesFileteredByActions.slice(startPageActual, endPageActual);
            return (
                <tbody>
                    {messagesToRender.map((msm, index) => (
                        <tr 
                            key={msm.id} 
                            className={index % 2 === 0 ? 'cita-rendered-pair':'cita-rendered-odd'} 
                            onClick={() => this._showMessageContent(msm)}
                        >
                            {this.state.columsTable.map((column) => (
                                <td className="td-table-citas-canceled" key={column}>
                                    {msm[column] !== undefined ? msm[column] : '-'}
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
                        <td colSpan={this.state.columsTable.length}>{this.state.loading?'Buscando':'No hay Historial de chat'}</td>
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
                            {column.charAt(0).toUpperCase()+column.slice(1).toLowerCase()}
                        </th>
                    ))}
                </tr>
            </thead>
        );
    };

    renderFilterBySedeDropdown = () => {
        return (
            <div>
                <select id="sede-select" onChange={this.handleActionChange} className="select-central-of-table-citas-canceled">
                    <option value="">Seleccionar Accion</option>
                    {this.state.actions.map((sede, index) => (
                        <option key={index} value={sede}>
                            {sede}
                        </option>
                    ))}
                </select>
            </div>
        );
    };
    _showMessageContent=(message)=>{
        this._openInfo(message.mensaje)
    }
    renderPaginationInfo = () => {
        const actualPage = this.state.page + 1;  
        const totalItems = this.state.messagesFileteredByActions.length;

        const allPages = Math.ceil(totalItems / this.state.pagination);  
    
        return (
            <span>Página {actualPage} de {allPages}</span>
        );
    };
    renderButtonsNavigation=()=> {
        const totalItems = this.state.messagesFileteredByActions.length;
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
    _openInfo = (info, title) => {
        this.setState({
            info,
            title,
            infoIsOpen: true,  
        });
    };
    
    render() {
        return (
            <div className="table-citas_avaiables-container">
                <div className="title-table-citas-canceled-avaibles"><h2>Historial de Chat</h2></div>
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
                <div>
                    <Info
                        isOpen={this.state.infoIsOpen}
                        onClose={() => this.setState({ infoIsOpen: false })}
                        info={this.state.info}
                        title={this.state.title}
                    />

                </div>
            </div>
        );
    }
}
    

export default TableHistory;