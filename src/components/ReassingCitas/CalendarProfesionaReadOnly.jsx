import ProfesionalCalendar from "../ProfesionalCalendar";
import '../../styles/CalerdarProfesional.css';


class CalerdarProfesionalReadOnly extends ProfesionalCalendar {
    
    insertButtonDeleteAllCitasDay = () => {
        return (
            <div><p></p></div>
        );
    };
    renderDeleteButton = (id, tiempo) => {
        return (
            <div><p>No hay opciones</p></div>
        );
    };
}
export default CalerdarProfesionalReadOnly;
