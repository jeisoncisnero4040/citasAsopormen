from flask import Flask, jsonify, request
from fpdf import FPDF  
from utils.ResponseManager import ResponseManager
from services.GcpService import GcpService
from exception.InternalServerErrorException import InternalServerErrorException
from ExceptionHandler import ExceptionHandler
from rules.Validator import Validator
from config.contans import APP_SECRET_KEY, FOLDER_ID
from services.PdfService import PdfService


# Configuración inicial
app = Flask(__name__)
app.secret_key = APP_SECRET_KEY
exception = ExceptionHandler(app=app)
credentials = GcpService.authenticate_service_account()

@app.route("/upload-list-citas/pdf", methods=["GET"])
def upload_list_citas_pdf():
    if not credentials:
        raise InternalServerErrorException(error="Fallo al autenticar")

    data = request.get_json()
    Validator.validate_citas_to_pdf(data)

    file_name = PdfService.makeCitasPdf(data)  
    file_url = GcpService.upload_pdf(file_path=file_name, folder_id=FOLDER_ID)

    return jsonify(ResponseManager.success(file_url))


if __name__ == "__main__":
    app.run(debug=True, port=5000)
