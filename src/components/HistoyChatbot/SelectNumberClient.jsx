import Constans from "../../js/Constans";
import SelectDataClient from "../SelectDataClient";


class SelectNumberClient extends  SelectDataClient{
    constructor(props) {
        super(props);
        this.state = {
            client: {
                codigo: "",
                nombre: "",
            },
            dataClient: {},
            clientList:[],
            MessageList: [],
            searchQuery: "",
            selectedOption: "",
            loading: false
        };
    }
    _getAllInfoClient=(codigo)=>{
        this.getClientInfo(codigo);
        this.getClientHistory(codigo);
    }
    getClientHistory = (codigo) => {
        
        this.setState({ loading: true });
        const url = `${Constans.apiUrl()}clients/history_chat_bot/${codigo}`;
        this.requestManager.getMethod(url)
        .then(response => {
            
            const historyChatbot = response.data.data;
            this.setState({MessageList: historyChatbot });
            
            this.handleClietHistory(historyChatbot);  
        })
        .catch(error => {  
            this.setState({
                errorMessage: error,
                warningIsOpen: true,
                
            },()=>this.handleClietHistory([]));  
        })
        .finally(() => {
            this.setState({ loading: false });
        });
    }

    handleClietHistory = (history) => {

        this.props.setHistoryChatBotClient(history);
    }
    handleClientSelection = (selectedClient) => {
        return;
    }

}
export default SelectNumberClient;