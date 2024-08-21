import { Navigate,Outlet } from "react-router-dom";

const ProtectedRoute=()=>{
    const token=localStorage.getItem('authToken');
    const redirectPack='/'
    if (!token){
        return <Navigate to={redirectPack} replace/> 

    }
    return <Outlet /> 



}
export default ProtectedRoute;