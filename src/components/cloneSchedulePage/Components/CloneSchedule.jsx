import React, { useState } from "react";
import ProfesionalCalendar from "../../ProfesionalCalendar";
import ProfesionalSearch from "../../ReassingCitas/ProfesionalSearch";
import "../Styles/CloneSchedule.css"; 
import Warning from "../../Warning";
import { ApiRequestsManagerService } from "../Services/ApiRequestsManagerService";
import {CitasService} from "../Services/CitasService"

const CloneSchedule = ({ user }) => {
  const [newCalendar, setNewCalendar] = useState([]);
  const [from, setFrom] = useState("");
  const [to, setTo] = useState("");
  const [start, setStart] = useState("");
  const [profesional, setProfesional] = useState({});
  const [profesionalCalendar, setProfersionalCalendar] = useState([]);
  const [openCalendar, setOpenCalendar] = useState(false);
  const [loading,setLoading]=useState(false);
  const [errorMessage,setErrorMessage]=useState("");
  const [warningIsOpen,setWarnigIsOpen]=useState(false)

  const citasService= new CitasService(new ApiRequestsManagerService())

  const cloneCalendar=async(e)=>{
    e.preventDefault()
    setLoading(true);
    const payload=buildPayload();
    const response = await citasService.cloneCalendarProfesional(payload,newCalendar);
    setLoading(false);

    if (response.error) {
      openWarning(response.error)
    } else {
      setNewCalendar(response.data || []);
    }
  }
   const buildPayload=()=>{
        return {
        from: from,
        to: to,
        start: start,
        cedula: profesional?.cedula?.trim()??'',
        usuario: user.usuario,
        cedula_usuario: user.cedula
        }
   }
    const openWarning=(error)=>{
      setErrorMessage(error);
      setWarnigIsOpen(true)
    }
  
  return (
    <div className="clone-calendar-container">
        <div className="header-form-citas">
            <p>Replicar Horario</p>
        </div>
        <div className="clone-calendar-content">
            <div className="clone-calendar-content-left-section">
            <div className="clone-calendar-profesional-search-profesional">
                <ProfesionalSearch
                getUpdateCalendarPro={setNewCalendar}
                getUpdateSchedulePro={setProfersionalCalendar}
                getUpdateProfesional={setProfesional}
                CloseScheduleProfesional={setOpenCalendar}
                />
            </div>
            <div className="clone-calendar-profesional-inputs-date">
                <div className="clone-calendar-profesional-input-date">
                <label>Desde</label>
                <input
                    type="date"
                    value={from}
                    onChange={(e) => setFrom(e.target.value)}
                />
                </div>
                <div className="clone-calendar-profesional-input-date">
                <label>Hasta</label>
                <input
                    type="date"
                    value={to}
                    onChange={(e) => setTo(e.target.value)}
                />
                </div>
                <div className="clone-calendar-profesional-input-date">
                <label>Clonar a partir de</label>
                <input
                    type="date"
                    value={start}
                    onChange={(e) => setStart(e.target.value)}
                />
                </div>
            </div>
            </div>

            <div className="clone-calendar-content-right-section">
            <ProfesionalCalendar
                events={newCalendar}
                nameProfesional={profesional.name}
                getUpdateCalendarPro={setNewCalendar}
                getUpdateSchedulePro={setProfersionalCalendar}
                cedulaProfesional={profesional.cedula}
                ChangeToSchedule={setOpenCalendar}
                usuario={user.usuario}
            />
            </div>
        </div>
        <div className="reassing-citas-form-footer">
            <a onClick={(e) => cloneCalendar(e)}>
                {loading?'Cargando...':'Replicar Horario' }
            </a>
        </div>
              
        <Warning
            isOpen={warningIsOpen}
            onClose={() => setWarnigIsOpen(false)}
            errorMessage={errorMessage}
        /> 
        
    </div>
  );
};

export default CloneSchedule;
