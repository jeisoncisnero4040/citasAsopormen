import ResponseManager from "../../Informes/Utils/ResponseManager";

export class CitasService{
    constructor(apiRequestsManager){
        this.apiRequestsManager=apiRequestsManager;
    }
    async cloneCalendarProfesional(requests,currentCalendar){
        const endpoint="citas/clone-calendar"
        try{
            const response=await this.apiRequestsManager.postMethod(endpoint,requests)
            const calendarResponse= response.data.data
            const newCalendar = this.getNewCalendar(currentCalendar,calendarResponse)
            return ResponseManager.success(newCalendar, false);
        }catch (error) {
            return ResponseManager.error(error.message || error);
        }
    }
    getNewCalendar(currentCalendar,newCalendar){
        return [...currentCalendar,...newCalendar]
    }

}