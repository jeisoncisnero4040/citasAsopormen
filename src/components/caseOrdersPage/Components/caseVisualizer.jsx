import React, { useEffect, useState } from "react";
import TableOrderAvaibles from "../Components/TableOrderAvaibles";
import OrderCase from "../Services/OrderCase";
import CarouselDataOrder from "./CarouselDataOrder";
import RecjetCaseForm from "./RejectCaseForm";
import Alert from "./Alert";

const CaseVisualizer = ({ user }) => {
    const [cases, setCases] = useState([]);
    const [caseSelected, setCaseSelected] = useState(null);
    const [displayOrder, setDisplayOrder] = useState(false);
    const [displayRejectOrder,setDisplayRejectOrder]=useState(false);
    const [alertData, setAlertData] = useState({ show: false, error: false, message: "" });
    const [loading,setLoading]=useState(false)
    const columnsToTable = OrderCase.dataToTable;
    const usuario = user?.usuario?.trim() || "";

    const orderService = new OrderCase();

    useEffect(() => {
        const fetchData = async () => {
            const fetchedCases = await orderService.fetchCases();
            responseManager(fetchedCases);
            
        };
        fetchData();
    }, []);

    const viewOrder = (order) => {
        setCaseSelected(order);
        setDisplayOrder(true);
    };

    const closeVisualizer = () => {
        setDisplayOrder(false);
        setDisplayRejectOrder(false)
    };
    const displayRejectCase=()=>{
        setDisplayRejectOrder(true)
    }
    const hideRejectCaseForm=()=>{
        setDisplayRejectOrder(false);
    }

    const closeOrder = async (order) => {
        setLoading(true)
        const response=await orderService.closeOrder(order.id, usuario,cases);
        responseManager(response);
    };
    const acceptCase=async ()=>{
        setLoading(true)
        const response=await orderService.accettCase(caseSelected.id, usuario,cases);
        closeVisualizer()
        responseManager(response);
    }
    const rejectCase=async ( razon)=>{
        setLoading(true)
        const response=await orderService.rejectCase(caseSelected.id, usuario,razon,cases);
        closeVisualizer()
        responseManager(response);
    }
    const showSuccess = (msm) => {
        setAlertData({ show: true, error: false, message:msm  });
       
    };
    
    const showError = (error) => {
        setAlertData({ show: true, error: true, message:error });
    };
    const responseManager = (response) => {
        setLoading(false);
    
        if (response.success === true) {
            setCases(response.data);
            showSuccess(response.message);
        } else if (response.success === false) {
            setCases(response.data);
            showError(response.error);
        } else {
            setCases(response.data);
        }
    };
    
    
    return (
        <div>
            {
            <TableOrderAvaibles
                data={cases}
                columns={columnsToTable}
                viewOrder={viewOrder}
                closeOrder={closeOrder}
                loading={loading}
            />}
            {displayOrder && caseSelected && (
                <div style={{ display: "block" }}>
                    <CarouselDataOrder 
                        order={OrderCase.mapOrder(caseSelected)} 
                        closeVisualizer={closeVisualizer} 
                        accepCase={acceptCase}
                        displayRejectOrder={displayRejectCase}
                        displayReject={displayRejectOrder}
                        loading={loading}
                    />
                </div>
            )}
            {displayOrder && caseSelected && displayRejectOrder && (
                <div>
                    <RecjetCaseForm rejectCase={rejectCase} hideRejectCaseForm={hideRejectCaseForm} loading={loading}/>
                </div>
            )}
            
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
    );
};

export default CaseVisualizer;
