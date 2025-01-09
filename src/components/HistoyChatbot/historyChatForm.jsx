import React from "react";
import SelectNumberClient from "./SelectNumberClient";
import TableHistory from "./TableHistory";
import SearchMessage from "./SearchMessage"
import '../../styles/HistoryChatBot/historyChatForm.css';

class HistoryChatForm extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            historyChat: [],
            historyChatFiltered:[]
        };
    }

    setHistoryChatBotClient = (newHistory) =>{
        this.setState({ historyChat: newHistory,
                        historyChatFiltered:[]
        });
    };
    setHistoryChatBotFiltered=(newChatFiltered)=>{
        this.setState({historyChatFiltered:newChatFiltered})
    }
    renderTable = () => {
        const { historyChatFiltered, historyChat } = this.state;
    
        if (historyChatFiltered.length > 0) {
            return <TableHistory historyChat={historyChatFiltered} />;
        } else if (historyChat.length > 0) {
            return <TableHistory historyChat={historyChat} />;
        } else {
            return <div>No hay historial</div>;
        }
    };
    
    render() {
        return (
            <div className="history-chat-bot-container">
                <div className="select-data-client">
                    <SelectNumberClient setHistoryChatBotClient={this.setHistoryChatBotClient} />
                </div>
                <div className="search-message">
                    <SearchMessage historyChat={this.state.historyChat} 
                                    setHistoryChatBotClient={this.setHistoryChatBotFiltered} />
                </div>
                <div className="table-history-chatbot">
                    {this.renderTable()}
                </div>
            </div>
        );
    }
}

export default HistoryChatForm;
