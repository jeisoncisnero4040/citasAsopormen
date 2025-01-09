import React from "react";
import '../../styles/ReassingCitas/DataCitaSelected.css'

class DataCitaSelected extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            citaMarkHowUnavaiable: false,
        };
    }

    componentDidUpdate(prevProps) {
        // Cambia `someProp` por la prop correcta que deseas comparar
        if (prevProps.cita !== this.props.cita) {
            this.setState({ citaMarkHowUnavaiable: false });
        }
    }

    renderCheckBoxToUnactivateCita = () => {
        return (
            <div>
                <label htmlFor="checkbox-inactivate">Inactivar Cita</label>
                <input
                    type="checkbox"
                    id="checkbox-inactivate"
                    checked={this.state.citaMarkHowUnavaiable}
                    onChange={this.handleCheckboxChange}
                />
            </div>
        );
    }

    handleCheckboxChange = (event) => {
        const isChecked = event.target.checked;
        this.setState({ citaMarkHowUnavaiable: isChecked }, () => 
            this.props.ChangueCitaCheckedToUnavaiable(isChecked)
        );
    }

    render() {
        const { cita } = this.props;
        const isCitaEmpty = !cita || (typeof cita === 'object' && Object.keys(cita).length === 0);

        return (
            <div className="data-cita-selected-container">
                {isCitaEmpty ? (
                    <p>No se ha seleccionado ninguna cita.</p>
                ) : (
                    <div>
                        <div className="title-data-cita-selected"><h2>Cita Seleccionada</h2></div>
                        <p className="reduced-line-height"><strong>Profesional:</strong> {cita.profesional}</p>
                        <p className="reduced-line-height"><strong>Cliente:</strong> {cita.cliente}</p>
                        <p className="reduced-line-height"><strong>Observacion:</strong> {cita.nombre_plantilla_observacion}</p>
                        <p className="reduced-line-height"><strong>Copago:</strong> {cita.copago}</p>
                        <p className="reduced-line-height"><strong>Fecha:</strong> {cita.fecha}</p>
                        <p className="reduced-line-height"><strong>Razon:</strong> {cita.razon}</p>
                        <p className="reduced-line-height"><strong>Sede:</strong> {cita.sede}</p>
                        <p className="reduced-line-height"><strong>Dirección:</strong> {cita.direccion_cita}</p>
                        <p className="reduced-line-height"><strong>Disponibles:</strong> {cita.cantidad-cita.reasignadas}</p>
                        <p className="reduced-line-height"><strong>Medio Cancelación:</strong> {cita.medio_cancelacion || 'No especificada'}</p>
                        <div className="data-cita-selected-checkbox-inactivate-cita">
                            {this.renderCheckBoxToUnactivateCita()}
                        </div>
                    </div>
                )}
            </div>
        );
    }
}

export default DataCitaSelected;
