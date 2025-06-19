import React,{useEffect,useState} from "react";
import PqrService from "../Services/PqrService";
import ApiRequestsManagerService from "../Services/ApiRequestsManagerService";
import Alert from "./Alert";
import PqrsTable from "./PqrsTable";
import "../Styles/PqrsVisualizer.css"
import PqrsModal from "./PqrsModal";
import AnswerPqrsVisualizer from "./AnswerPqrsVisualizerModal";
import ChangeAreaPqrsForm from "./ChangeAreaPqrsFormModal";
import AnswerPqrsToClientFormModal from "./AnswerPqrsToClientFormModal";



const PqrsVisualizer=({user})=>{
    const[loading,setLoading]=useState(false);
    const[pqrs,setPqrs]=useState([]);
    const [alertData, setAlertData] = useState({ show: false, error: false, message: "" });
    const [displayPqrs,setDisplayPqrs]=useState(false);
    const [displayAnswerPqrs,setDisplayAnswerPqrs]=useState(false)
    const [displayAnswerPqrsClient,setDisplayAnswerPqrsClient]=useState(false);
    const [pqrsSelected,setPqrsSelected]=useState([]);
    const [displayChangeAreaPqrsForm,setDisplayChangeAreaPqrsForm]=useState(false);
    const pqrsService=new PqrService(new ApiRequestsManagerService());
    const columnsToTable=PqrService.dataToTable;
    const [notifyingId, setNotifyingId] = useState(null);
    

    useEffect(()=>{
        fetchPqrs()
    },[]);

    const fetchPqrs=async ()=>{
        setLoading(true)
        const pqrsResponse=await pqrsService.getPqrs()
        responseManager(pqrsResponse,setPqrs)
        setLoading(false)
    }
    const showSuccess = (msm) => {
        setAlertData({ show: true, error: false, message:msm  });
       
    };
    const showError = (error) => {
        setAlertData({ show: true, error: true, message:error });
    };
    
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
    const showPqrs = (row) => {
        setPqrsSelected(row);
        setDisplayPqrs(true);
    };
    const showPqrsAnswer=(row)=>{
        setPqrsSelected(row);
        setDisplayAnswerPqrs(true)
    }
    const NotifyPqrs= async(row)=>{
      setNotifyingId(row.id);
      setLoading(true);
      setPqrsSelected(row);
      const pqrsResponse=await pqrsService.notifyPqrsToArea(row,user,pqrs)
      responseManager(pqrsResponse,setPqrs,"Pqrs notificado correctamente")
      setPqrsSelected([])
      setLoading(false)
      setNotifyingId(null);

    }
    const displayChangeAreaForm=(row)=>{
      setPqrsSelected(row);
      setDisplayChangeAreaPqrsForm(true)
    }
    const updateAreaPqrs=async(e,pqrsSelected,newData)=>{
      e.preventDefault();
      setLoading(true)
      const response=await  pqrsService.changueAreaPqrs(pqrsSelected,newData,pqrs);
      responseManager(response,setPqrs,"Pqrs Actulizado Correctamente");
      setLoading(false)
    }
    const displaySendAnswerToClient=(row)=>{
      setPqrsSelected(row);
      setDisplayAnswerPqrsClient(true)
    }
    const sendAnswertToClient=async (answer)=>{
        setLoading(true)
        const response=await  pqrsService.sendPqrsAnswerClient(answer,pqrsSelected,pqrs);
        responseManager(response,setPqrs,"Pqrs Actulizado Correctamente");
        setLoading(false)
    }
    const closePqrs=async(row)=>{
        setLoading(true);
        setPqrsSelected(row);
        const response=await pqrsService.closePqrs(pqrsSelected,pqrs)
        responseManager(response,setPqrs,"Pqrs cerrado Correctamente")
        setLoading(false)
    }
    
      return (
        <div className="pqrs-visualizer-container">
          <div className="header-form-citas">
            <p>Vizualizador Pqrs</p>
          </div>
      
          <div className="pqrs-visualizer-table">
            <PqrsTable
              data={pqrs}
              columns={columnsToTable}
              showPqrs={showPqrs}
              showPqrsAnswer={showPqrsAnswer}
              notifyArea={NotifyPqrs}
              loading={loading}
              notifyingId={notifyingId}
              displayChangeAreaPqrsForm={displayChangeAreaForm}
              displaySendAnswerToClient={displaySendAnswerToClient}
              closePqrs={closePqrs}
              
            />
          </div>
      
          {displayPqrs && (
            <div className="info-pqrs-selected">
              <PqrsModal pqrs={pqrsSelected} onClose={setDisplayPqrs}/>
            </div>
          )}
          {displayAnswerPqrs && (
            <div className="info-pqrs-selected">
              <AnswerPqrsVisualizer pqrs={pqrsSelected} onClose={setDisplayAnswerPqrs}/>
            </div>
          )}

          {alertData.show && (
            <Alert 
              error={alertData.error} 
              message={alertData.message} 
              onClose={() => setAlertData({ ...alertData, show: false })}
            />
          )}
          {
            displayChangeAreaPqrsForm&&(
              <ChangeAreaPqrsForm pqrs={pqrsSelected} 
                onClose={setDisplayChangeAreaPqrsForm} 
                updateAreaPqrs={updateAreaPqrs}
                loading={loading}
              />
            )
          }
          {
            displayAnswerPqrsClient&&(
              <AnswerPqrsToClientFormModal
                  onClose={setDisplayAnswerPqrsClient}
                  pqrs={pqrsSelected}
                  loading={loading}
                  sendAnswertToClient={sendAnswertToClient}
              />
            )
          }
        </div>
      );
}
export default PqrsVisualizer;