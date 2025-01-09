import { Navigate,Outlet } from "react-router-dom";

const ProtectedRouteClient=()=>{
    const token=localStorage.getItem('authToken');
    const redirectPack='/clientes'
    if (!token){
        return <Navigate to={redirectPack} replace/> 

    }
    return <Outlet /> 



}
export default ProtectedRouteClient;