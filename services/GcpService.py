import os
from google.oauth2 import service_account
from flask import session, request, url_for
from googleapiclient.discovery import build
from googleapiclient.http import MediaFileUpload
from google.oauth2 import service_account

class GcpService:
    SERVICE_ACCOUNT_FILE = "credentials.json"  
    SCOPES = ["https://www.googleapis.com/auth/drive.file"]

    @staticmethod
    def authenticate_service_account():
        """Autentica usando la cuenta de servicio"""
        credentials = service_account.Credentials.from_service_account_file(
            GcpService.SERVICE_ACCOUNT_FILE, scopes=GcpService.SCOPES
        )
        return credentials

    def upload_pdf(file_path, folder_id=None):
        """
        Sube un archivo PDF a Google Drive con cuenta de servicio y retorna la URL pública.

        :param file_path: Ruta del archivo a subir.
        :param folder_id: (Opcional) ID de la carpeta donde se guardará el archivo.
        :return: URL pública del archivo en Drive.
        """
        credentials = GcpService.authenticate_service_account()
        drive_service = build("drive", "v3", credentials=credentials)

        file_metadata = {"name": os.path.basename(file_path), "mimeType": "application/pdf"}
        if folder_id:
            file_metadata["parents"] = [folder_id]

        media = MediaFileUpload(file_path, mimetype="application/pdf")

        file = drive_service.files().create(body=file_metadata, media_body=media, fields="id").execute()
        file_id = file.get("id")

         
        drive_service.permissions().create(
            fileId=file_id,
            body={"type": "anyone", "role": "reader"},
        ).execute()

         
        file_url = f"https://drive.google.com/file/d/{file_id}/view?usp=sharing"
        return file_url
