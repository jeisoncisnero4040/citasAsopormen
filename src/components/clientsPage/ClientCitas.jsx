import React from "react";
import ApiRequestManagerClient from "../../util/ApiRequestManagerClient";
import Warning from "../Warning";
import Constants from "../../js/Constans";
import CardCita from "./CardCita";
import '../../styles/clientsPage/ClientCitas.css';
import LoadingPage from "./LoadingPage";

// Alerta: la API retorna las citas como un array de array
// Muy importante hacer llamada .flat() cuando se haga la petición
class ClientCitas extends React.Component {
    requestManager = new ApiRequestManagerClient();

    constructor(props) {
        super(props);
        this.state = {
            citasClient: [],
            loading: false,
            warningIsOpen: false,
            errorMessage: "",
        };
    }

    componentDidMount() {
        if (!this.props.codigo) {
            this.setState({
                warningIsOpen: true,
                errorMessage: "El código del cliente no está definido.",
            });
            return;
        }
        if (!localStorage.getItem('userCitas')) {
            this.fetchCitas();
        } else {
            this.setCitas(JSON.parse(localStorage.getItem('userCitas')));
        }
    }

    fetchCitas = () => {
        const url = `${Constants.apiUrl()}citas/get_citas_client/${this.props.codigo}`;
        this.setState({ loading: true });

        this.requestManager
            .getMethod(url)
            .then((response) => {
                this.setCitas(response.data.data.flat() || [], true);
            })
            .catch((error) => {
                this.setState({
                    warningIsOpen: true,
                    errorMessage: error.message || "Error desconocido al obtener citas.",
                });
            })
            .finally(() => {
                this.setState({ loading: false });
            });
    };

    setCitas = (newCitas, saveInCache = false) => {
        this.setState({ citasClient: newCitas });
        if (saveInCache) {
            localStorage.setItem('userCitas', JSON.stringify(newCitas));
        }
    };

    markCitaAsCancelada = (ids) => {
        const updatedCitas = this.state.citasClient.map((cita) => {
            if (cita.ids === ids) {
                return { ...cita, cancelada: '1' };
            }
            return cita;
        });

        this.setCitas(updatedCitas, true);
    };

    renderCitas = () => {
        const { citasClient } = this.state;

        if (citasClient.length === 0) {
          return (
            <div className="citas-client-no-found">
                <p className="no-citas">El cliente no registras Citas</p>
            </div>)
        }

        return (
            <div className="wrapper-citas-client">
                {citasClient.map((cita) => (
                    <div key={cita.id} className="cita-card">
                        <CardCita cita={cita} citaCanceled={this.markCitaAsCancelada} />
                    </div>
                ))}
            </div>
        );
    };
    renderLoading=()=>{
      return (
        <div className="wrapper-citas-client">
            <LoadingPage/>
        </div>
      )
    }
    render() {
        const { loading, warningIsOpen, errorMessage } = this.state;

        return (
            <div>
                {loading ? (
                    this.renderLoading()
                ) : (
                    this.renderCitas()
                )}
                <Warning
                    isOpen={warningIsOpen}
                    onClose={() => this.setState({ warningIsOpen: false })}
                    errorMessage={errorMessage}
                />
            </div>
        );
    }
}

export default ClientCitas;
