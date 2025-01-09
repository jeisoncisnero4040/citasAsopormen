import React from "react";

class SearchMessage extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            paramSearch: '',
            historyMessagesList: [],
            historyMessagesListFiltered: [],
        };
    }

    componentDidMount() {
         
        this.setHistoryMessageList(this.props.historyChat);
    }

    componentDidUpdate(prevProps) {
         
        if (prevProps.historyChat !== this.props.historyChat) {
            this.setHistoryMessageList(this.props.historyChat);
        }
    }

    setHistoryMessageList = (newHistory) => {
        this.setState({
            historyMessagesList: newHistory,
            historyMessagesListFiltered: newHistory,  
        });
    };

    setParamSearch = (newParam) => {
        this.setState({ paramSearch: newParam });
    };

    handleParamToSearch = (event) => {
        const newParam = event.target.value;
        this.setParamSearch(newParam);
        this._searchMsm(newParam);
    };

    _searchMsm = (paramSearch) => {
        if (paramSearch === '') {
            
             
            this.setState({ historyMessagesListFiltered: this.state.historyMessagesList });

        } else {
            
            const filteredMessages = this.state.historyMessagesList.filter((message) =>
                message.mensaje.toLowerCase().includes(paramSearch.toLowerCase())
            );
            this.setState({ historyMessagesListFiltered: filteredMessages });
        }

         
        this.props.setHistoryChatBotClient(this.state.historyMessagesListFiltered);
    };

    render() {
        return (
            <div className="container-search-message">
                <input
                    type="text"
                    value={this.state.paramSearch}  
                    onChange={this.handleParamToSearch}
                    placeholder="Buscar mensaje"
                    readOnly={this.state.historyMessagesList.length === 0 && this.state.paramSearch === ''}
                />
            </div>
        );
    }
}

export default SearchMessage;
