export default class ResponseManager {
    static success(data, isAlertable) {
      return {
        message: 'success',
        alertable: isAlertable,
        data: data
      };
    }
  
    static error(error) {
      return {
        message: 'error',
        alertable: true,
        error: error
      };
    }
  }
  