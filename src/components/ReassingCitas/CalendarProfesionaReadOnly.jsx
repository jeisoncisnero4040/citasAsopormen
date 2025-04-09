import ProfesionalCalendar from "../ProfesionalCalendar";
import '../../styles/CalerdarProfesional.css';


class CalerdarProfesionalReadOnly extends ProfesionalCalendar {
    
    insertButtonDeleteAllCitasDay = () => {
        return (
            null
        );
    };
    _renderButtons = (id, tiempo) => {
        return (
            <div><p>No hay opciones</p></div>
        );
    };
}
export default CalerdarProfesionalReadOnly;
