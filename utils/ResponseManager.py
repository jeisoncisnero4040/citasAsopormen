class ResponseManager:
    @staticmethod
    def success(data)->dict:
        return {
            "mesagge":"succes",
            "status":"200",
            "data":data
        }
    
    @staticmethod
    def created (data)->dict:
        return {
            "mesagge":"succes",
            "status":"201",
            "data":data
        }
    
    @staticmethod
    def BadRequest(error)->dict:
        return {
            "mesagge":"failed",
            "error":error,
            "status":"400",
            "data":[]
        }    
    @staticmethod
    def NotFound(error)->dict:
        return {
            "mesagge":"failed",
            "error":error,
            "status":"404",
            "data":[]
        } 
    @staticmethod
    def serverError(error)->dict:
        return {
            "mesagge":"failed",
            "error":error,
            "status":"500",
            "data":[]
        }   