import os
import json
from datetime import datetime


class SweepLogs:

    log_file = "sweep.logs"

    @staticmethod
    def save_log_work(sweep_data=None):
        """
        Guarda información en el archivo sweep.logs. 
        Si no existe el archivo, lo crea.
        """
        if not os.path.exists(SweepLogs.log_file):
            with open(SweepLogs.log_file, 'w') as file:
                file.write("[]")  

        with open(SweepLogs.log_file, 'r') as file:
            try:
                logs = json.load(file)
            except json.JSONDecodeError:
                logs = []
        log_entry = {
            "fecha": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        }
        if sweep_data:

            log_entry.update({
                "accion": "barrido",
                "hora_finalizacion": sweep_data.get("hora_finalizacion"),
                "num_citas_notificadas": sweep_data.get("num_citas_notificadas"),
                "tiempo_duracion": sweep_data.get("tiempo_duracion"),
            })
        else:

            log_entry["accion"] = "envio_informe"



        logs.append(log_entry)
        with open(SweepLogs.log_file, 'w') as file:
            json.dump(logs, file, indent=4)


