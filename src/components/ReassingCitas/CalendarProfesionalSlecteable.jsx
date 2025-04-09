import ProfesionalCalendar from "../ProfesionalCalendar";
import '../../styles/CalerdarProfesional.css';
import '../../styles/ReassingCitas/CalendarProfesionalSelecteable.css'


class CalerdarProfesionalSelecteable extends ProfesionalCalendar {
    constructor(props) {
        super(props);
    
        const today = new Date().toISOString().split('T')[0]; 
        this.state = {
            startDate: today,
            endDate: today,
            modalIsOpen: false,
            eventDetails: [],
            selectedday: new Date().toISOString().split('T')[0],
            warningIsOpen:false,
            warningMessage:'',
            alertIsOpen:false,
            alertMessage:'',
            canContine:false,
            idToDelete:'',
            tiempoCitaSelected:'',
            error:'',
            showError:false,
            showAgreeButtons:false,
            idSelected:'',
            loading:false,
            deletingCitas:false,
            idsSelected:[]
        }
    }
    insertButtonDeleteAllCitasDay = () => {
        return (
            null
        );
    };
    insertButtonShowSchedule=()=>{
        return (
            null
        );
    }
    handleCheckboxChange = (id) => {
        this.setState(prevState => {
            const { idsSelected } = prevState;
    
            
            if (idsSelected.includes(id)) {
                return { idsSelected: idsSelected.filter(existingId => existingId !== id) };
            } else {
                return { idsSelected: [...idsSelected, id] };
            }
        },()=>{
            this.props.getUpdateIdsSelected(this.state.idsSelected)
        });
    };
    _renderButtons= (id, tiempo,estado) => {
        if(estado==='Programada'){
            return (
                <div key={id} className="checkbox">
                    <input
                        type="checkbox"
                        id={`checkbox-${id}`} 
                        onChange={() => this.handleCheckboxChange(id)} 
                        checked={this.state.idsSelected.includes(id)} 
                    />

                </div>
            );
        }else{
            return(
                <p>no hay opciones</p>
            )
        }
    };
}
export default CalerdarProfesionalSelecteable;
