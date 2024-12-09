import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.mime.application import MIMEApplication
from settings.AppSettings import EMAIL_SENDER, EMAIL_CITAS, EMAIL_SENDER_PASSWORD,EMAIL_SISTEMAS
from src.Utils.TemplateGenerator import TemplateGenerator
import os

class EmailService:
    def __init__(self):
        self.sender: str = EMAIL_SENDER
        self.sender_password: str = EMAIL_SENDER_PASSWORD
        self.to: str = EMAIL_CITAS
        self.to_sistemas=EMAIL_SISTEMAS

    def send_correo(self) -> None:
         
        html_content = TemplateGenerator.daily_informe_template()

         
        msg = MIMEMultipart()
        msg['Subject'] = "Informe Diario - Análisis Chatbot"
        msg['From'] = self.sender
        msg['To'] = self.to

         
        msg.attach(MIMEText(html_content, "html"))

        if os.path.exists("analisis.html"):
            with open("analisis.html", "rb") as archivo_html:
                part_html = MIMEApplication(archivo_html.read(), _subtype="html")
                part_html.add_header('Content-Disposition', 'attachment', filename="Analisis_cuenta.html")
                msg.attach(part_html)

        if os.path.exists("INFORME_CITAS_CANCELADAS.xlsx"):
            with open("INFORME_CITAS_CANCELADAS.xlsx", "rb") as archivo_excel:
                part_excel = MIMEApplication(archivo_excel.read(), _subtype="octet-stream")
                part_excel.add_header('Content-Disposition', 'attachment', filename="Analisis_cuenta.xlsx")
                msg.attach(part_excel)

            
        server = smtplib.SMTP('smtp.gmail.com', 587)
        server.starttls()
        server.login(self.sender, self.sender_password)
        server.sendmail(self.sender, self.to, msg.as_string())
        server.quit()

    def sendEmailFailedWhatsapSystemFailed(self):
        html_content = TemplateGenerator.error_whatsapp_service()
        msg = MIMEMultipart()
        msg['Subject'] = "Notificacio error chatbot asopormen"
        msg['From'] = self.sender
        msg['To'] = self.to
        msg.attach(MIMEText(html_content, "html"))
        server = smtplib.SMTP('smtp.gmail.com', 587)
        server.starttls()
        server.login(self.sender, self.sender_password)
        server.sendmail(self.sender, self.to_sistemas, msg.as_string())
        server.quit()