import ResponseManager from "../Utils/ResponseManager"

export class InformesService{
    constructor(apiRequestManager){
        this.apiRequestManager=apiRequestManager
    }
    async getNewClientsInforme(to,from){
        const endpoint="informes/new-clients"
        const params=`?from=${encodeURIComponent(to)}&to=${encodeURIComponent(from)}`
        try{
            const response=await this.apiRequestManager.getMethod(`${endpoint}${params}`)
            return ResponseManager.success(response.data.data, false);
        }catch (error) {
            return ResponseManager.error(error.message || error);
        }
        


    }
    async getClientsWhitOutAppoiments(to,from){
        const endpoint="informes/clients-appoiments-not-foud"
        const params=`?from=${encodeURIComponent(to)}&to=${encodeURIComponent(from)}`
        try{
            const response=await this.apiRequestManager.getMethod(`${endpoint}${params}`)
            return ResponseManager.success(response.data.data, false);
        }catch (error) {
            return ResponseManager.error(error.message || error);
        }
        


    }
   
}