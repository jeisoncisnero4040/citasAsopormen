from flask import Flask, jsonify
from exception.BadRequestException import BadRequestException
from exception.NotFoundException import NoFoundException
from exception.InternalServerErrorException import InternalServerErrorException
from utils.ResponseManager import ResponseManager

class ExceptionHandler:
    def __init__(self, app: Flask):
        self.app = app
        self.register_handlers()

    def register_handlers(self):
        
        @self.app.errorhandler(BadRequestException)
        def handle_bad_request_exception(error):
            response = ResponseManager.BadRequest(error.getMessage())
            return jsonify(response), error.getStatus()

        
        @self.app.errorhandler(InternalServerErrorException)
        def handle_internal_server_exception(error):
            response = ResponseManager.serverError(error.getMessage())   
            return jsonify(response), error.getStatus()
        
        @self.app.errorhandler(NoFoundException)
        def handle_not_found_exception(error):
            response = ResponseManager.NotFound(error.getMessage())   
            return jsonify(response), error.getStatus()

         
         





