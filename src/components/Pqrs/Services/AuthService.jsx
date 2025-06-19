import ResponseManager from "../Utils/ResponseManager";

export default class AuthService {
    constructor(apiRequestManager) {
        this.apiRequestManager = apiRequestManager;
    }

    async login(dataToLogin){
        try {
            const data = await this.apiRequestManager.postMethod('login',dataToLogin);
            const user=data.data.data.user;
            const token= data.data.data.token;
            localStorage.setItem('authToken',token);
            return ResponseManager.success(user, false);
        } catch (error) {
            return ResponseManager.error(error.message || error);
        }
    }
    
    async changePassword(dataToChangePasword){
        try {
            const data = await this.apiRequestManager.postMethod('change-password',dataToChangePasword);
            return ResponseManager.success(data.data.data, true);
        } catch (error) {
            return ResponseManager.error(error.message || error);
        }
    }
    async forgotPassword(forgot){
        try {
            const data = await this.apiRequestManager.postMethod('forgot-password',forgot);
            return ResponseManager.success(data.data.data, true);
        } catch (error) {
            return ResponseManager.error(error.message || error);
        }
    }

}
