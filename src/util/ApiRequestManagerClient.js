import ApiRequestManager from "./ApiRequestMamager";

class ApiRequestManagerClient extends ApiRequestManager{
    handleAuthError = (error) => {
        if(!error.response){
            throw  "Uppps al parecer estamos teniendo un problema con la red"
        }
        if (error.response && error.response.status === 401) {
            window.location.href = '/clinico/clientes';
            throw error.response.data.message;
        } else {
            throw error.response.data.error?error.response.data.error:'error al hacer la peticion'  
        }
    }
}
export default ApiRequestManagerClient;