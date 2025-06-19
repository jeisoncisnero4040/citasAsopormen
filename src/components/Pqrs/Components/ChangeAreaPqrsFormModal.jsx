import React, { useState,useEffect} from "react";
import PqrService from "../Services/PqrService";
import ApiRequestsManagerService from "../Services/ApiRequestsManagerService";
import Alert from "./Alert.jsx"
import { FaTimes} from "react-icons/fa";



const ChangeAreaPqrsForm =({ pqrs, onClose,updateAreaPqrs,loading })=>{
    const[utility,setUtility]=useState([]);
    const[newData,setNewData]=useState({});
    
    const [alertData, setAlertData] = useState({ show: false, error: false, message: "" });

    const pqrsService=new PqrService(new ApiRequestsManagerService());


    useEffect(() => {
        fetchUtilities();
    }, []);
    const fetchUtilities=async ()=>{
        const response = await pqrsService.getUtility();
        responseManager(response, setUtility);
    }

    const responseManager = (response, setItem,messageSucces=null) => {
        if (response.message === "success") {
          setItem(response.data);
          if(response.alertable==true){
            showSuccess(messageSucces)
          }
        } else if (response.message === "error") {
          showError(response.error||"error desconcido")
        }
      };
      const handleInputPqrsChange = (e) => {
        const { name, value } = e.target;
      
        setNewData((prevData) => ({
          ...prevData,
          [name]: value
        }));
      };
    const renderOptionUtility = (type) => {
        const types = PqrService.filterUtility(utility, type);
        return types.map((type) => (
        <option key={type.id} value={type.id}>
            {type.nombre}
        </option>
        ));
    };
    const showSuccess = (msm) => {
        setAlertData({ show: true, error: false, message:msm  });
        
    };
    const showError = (error) => {
        setAlertData({ show: true, error: true, message:error });
    };

    return (
        <div  className="modal-overlay">
            <div className="modal-content-pqrs">
                <form>
                    <div className="title-and-close-button">
                        <p>Actualizar Area Pqrs</p>
                        <a onClick={()=>onClose(false)}><FaTimes /></a>
                    </div>
                    <div className="select-pqr">
                        <label style={{fontSize:'18px'}}>Area:</label>
                        <select
                        name="area_id"
                        value={newData.area_id|| ""}
                        onChange={handleInputPqrsChange}
                        required
                        >
                        <option value="">Seleccione la area</option>
                        {renderOptionUtility("area")}
                        </select>
                    </div>
                    <div className="select-pqr">
                        <label style={{fontSize:'18px'}}>Area:</label>
                        <select
                        name="sede_id"
                        value={newData.sede_id || ""}
                        onChange={handleInputPqrsChange}
                        required
                        >
                        <option value="">Seleccione la sede</option>
                        {renderOptionUtility("sede")}
                        </select>
                    </div>
                    <button type="submit" disabled={!newData.area_id || !newData.sede_id} onClick={(e)=>updateAreaPqrs(e,pqrs,newData)}>{loading?'Cargando':'Actualizar'}</button>
                </form>
                {alertData.show && (
                    <div>
                        <Alert 
                            error={alertData.error} 
                            message={alertData.message} 
                            onClose={() => setAlertData({ ...alertData, show: false })}
                        />
                    </div>
                )}

            </div>
            
        </div>
    )
    

}
export default ChangeAreaPqrsForm;