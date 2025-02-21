import fpdf

import os
from exception.InternalServerErrorException import InternalServerErrorException

class PdfService(fpdf.FPDF):  
    @staticmethod
    def makeCitasPdf(data):
        citas = data.get('citas', [])
        cliente = data.get('cliente', 'Cliente Desconocido')  
        observaciones = data.get('observaciones', [])

        pdf = PdfService()
        pdf.set_auto_page_break(auto=True, margin=15)
        pdf.add_page()

         
        PdfService.__buildHeader(pdf, cliente)
        PdfService.__makeTable(pdf, citas)
        PdfService.insertObservaciones(pdf, observaciones[0])

         
        file_path = f"{cliente}_citas.pdf"
        pdf.output(file_path)
        return file_path

    @staticmethod
    def __buildHeader(pdf, cliente):
        image_path = os.path.join("static", "asopologo.png")

        # Verificar si la imagen existe antes de insertarla
        if os.path.exists(image_path):
            try:
                pdf.image(image_path, x=10, y=8, w=30)  # Imagen alineada a la izquierda
            except Exception as e:
                raise InternalServerErrorException(error=str(e))
        else:
            raise InternalServerErrorException(error="Imagen de logo no encontrada")

        # Texto al lado de la imagen (más cerca)
        pdf.set_xy(42, 12)  # Ajustamos la posición más cerca de la imagen
        pdf.set_font("Times", "I", 12)  # Fuente diferente (Times, Itálica)
        pdf.multi_cell(0, 6, "Asopormen\nNuestra razón de ser", align="L")  # Texto en 2 líneas

        # Espacio debajo del header
        pdf.ln(20)  # Aseguramos un espacio adecuado antes del título

        # Título principal centrado
        pdf.set_font("Arial", "B", 20)
        pdf.cell(200, 10, "Listado de Citas", ln=True, align="C", border=0)
        
        pdf.ln(5)  # Espaciado entre título y cliente

        # Información del cliente debajo del título
        pdf.set_font("Arial", "", 15)
        pdf.cell(200, 10, f"Cliente: {cliente}".encode("latin-1", "replace").decode("latin-1"), ln=True, align="L")
        
        pdf.ln(10)  # Espacio extra antes de continuar con el contenido


    @staticmethod
    def __makeTable(pdf, citas):
        pdf.set_font("Arial", "B", 8)

        headers = ["Fecha", "Profesional", "Dirección", "Duración", "Procedimiento"]
        col_widths = [30, 40, 40, 15, 30]  # Ajuste de anchos

        # Imprimir encabezados
        for i, header in enumerate(headers):
            pdf.cell(col_widths[i], 10, header, border=1, align="C")
        pdf.ln()

        pdf.set_font("Arial", "", 9)

        for cita in citas:
            cita['procedimiento']='terapia fisica'
            y_start = pdf.get_y()  # Posición inicial Y
            x_start = pdf.get_x()  # Posición inicial X

            cell_heights = []  # Almacenar altura de cada celda

            
            for i, key in enumerate(["start", "profesional", "direcion", "duracion", "procedimiento"]):
                text = cita.get(key, "N/A").title() if key != "duracion" else cita.get("duracion", "N/A") + ' Minutos'


                
                text_width = pdf.get_string_width(text)
                num_lines = max(1, int(text_width / col_widths[i]) + 1)
                estimated_height = num_lines * 5  # Cada línea ocupa 5 puntos aprox.

                cell_heights.append(estimated_height)

            max_height = max(cell_heights)  # La altura máxima de la fila

            # 🔹 SEGUNDO: Imprimir todas las celdas con la misma altura
            for i, key in enumerate(["start", "profesional", "direcion", "duracion", "procedimiento"]):
                pdf.set_xy(x_start + sum(col_widths[:i]), y_start)  # Ajustar posición

                text = cita.get(key, "N/A").title() if key != "duracion" else cita.get("duracion", "N/A") + ' Minutos'

                # 🔹 Forzar todas las celdas a la misma altura con un MultiCell "fijo"
                pdf.multi_cell(col_widths[i], max_height / (max_height // 5), text, border=1)

                # Ajustar la posición para la siguiente celda
                pdf.set_xy(x_start + sum(col_widths[:i+1]), y_start)

            # 🔹 Moverse a la siguiente línea
            pdf.set_y(y_start + max_height)



    @staticmethod
    def insertObservaciones(pdf, observaciones=None):
        if not observaciones or (not observaciones.get('terapia') and not observaciones.get('valoracion')):
            return

        pdf.ln(10)   
        pdf.set_font("Arial", "B", 12)
        pdf.cell(200, 10, "Observaciones", ln=True, align="L")

        pdf.set_font("Arial", "", 10)

        if observaciones.get('valoracion'):
            pdf.cell(200, 10, "Valoración:", ln=True, align="L")
            pdf.set_x(pdf.get_x() + 10)  # 🔹 Sangría
            pdf.multi_cell(190, 5, observaciones['valoracion'], align="L")
            pdf.ln(5)  # Espacio entre secciones

        if observaciones.get('terapia'):
            pdf.cell(200, 10, "Terapia:", ln=True, align="L")
            pdf.set_x(pdf.get_x() + 10)  # 🔹 Sangría
            pdf.multi_cell(190, 5, observaciones['terapia'], align="L")
