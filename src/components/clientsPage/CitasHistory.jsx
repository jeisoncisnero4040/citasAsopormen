import ClientCitas from "./ClientCitas";
import Constants from "../../js/Constans";



class CitasHystory extends ClientCitas{
    componentDidMount() {
        if (!this.props.codigo) {
            this.setState({
                warningIsOpen: true,
                errorMessage: "El código del cliente no está definido.",
            });
            return;
        }
        if (!localStorage.getItem('userCitasHistory')) {
            this.fetchCitas();
        } else {
            this.setCitas(JSON.parse(localStorage.getItem('userCitasHistory')));
        }
    }

    _getUrl=()=>{
        return `${Constants.apiUrl()}citas/get_citas_client_history/${this.props.codigo}`
    }

    setCitas = (newCitas, saveInCache = false) => {
        this.setState({ citasClient: newCitas });
        if (saveInCache) {
            localStorage.setItem('userCitasHistory', JSON.stringify(newCitas));
        }
    };
}
export default CitasHystory;