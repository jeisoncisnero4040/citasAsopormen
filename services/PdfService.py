import fpdf
from utils.DateManager import DateManager

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
                pdf.image(image_path, x=10, y=8, w=30)   
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
        pdf.cell(200, 10, "Listado de Citas Programadas", ln=True, align="C", border=0)
        
        pdf.ln(5)  # Espaciado entre título y cliente

        # Información del cliente debajo del título
        pdf.set_font("Arial", "", 15)
        pdf.cell(200, 10, f"Cliente: {cliente}".encode("latin-1", "replace").decode("latin-1"), ln=True, align="L")
        
        pdf.ln(10)  # Espacio extra antes de continuar con el contenido
        



    @staticmethod
    def __makeTable(pdf, citas):
        pdf.set_font("Arial", "B", 8)

        headers = ["Día","Fecha", "Profesional", "Dirección", "Duración", "Procedimiento"]
        col_widths = [20,40, 40, 40, 15, 30]  # Ancho de cada columna

        # 🔹 Dibujar encabezados
        for i, header in enumerate(headers):
            pdf.cell(col_widths[i], 10, header, border=1, align="C")
        pdf.ln()

        pdf.set_font("Arial", "", 9)

        for cita in citas:
            cita['dia'] = DateManager.GetDayNameFromDate(cita['start'])
            cita['start']=DateManager.GetFullDateInText(cita['start'])

            y_start = pdf.get_y()  # Posición inicial Y de la fila
            x_start = pdf.get_x()  # Posición inicial X
            cell_heights = []  # Lista de alturas de celdas

            # 🔹 PRIMER PASO: Determinar la altura máxima de la fila
            row_texts = []  # Guardamos los textos procesados
            for i, key in enumerate(["dia","start", "profesional", "direcion", "duracion", "procedimiento"]):
                text = cita.get(key, "N/A").title() if key != "duracion" else cita.get("duracion", "N/A") + " Minutos"

                # Obtener el ancho del texto y calcular cuántas líneas ocupará
                text_width = pdf.get_string_width(text)
                num_lines = max(1, int(text_width / col_widths[i]) + 1)  # Asegurar al menos 1 línea
                estimated_height = num_lines * 5  # Cada línea ocupa ~5 puntos

                cell_heights.append(estimated_height)
                row_texts.append(text)  # Guardamos el texto formateado

            max_height = max(cell_heights)  # Altura máxima de la fila

            # 🔹 SEGUNDO PASO: Dibujar todas las celdas con la misma altura
            for i, text in enumerate(row_texts):
                pdf.set_xy(x_start + sum(col_widths[:i]), y_start)  # Restaurar posición inicial

                # Dibujamos un rectángulo de la altura máxima para mantener alineación
                pdf.cell(col_widths[i], max_height, "", border=1)

                # Ahora escribimos el texto con MultiCell dentro del rectángulo
                pdf.set_xy(x_start + sum(col_widths[:i]), y_start)  # Restaurar posición
                pdf.multi_cell(col_widths[i], 5, text, border=0)

            # 🔹 Moverse a la siguiente línea usando la altura máxima
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
            pdf.cell(200, 10, "Tenga en cuenta las siguientes recomendaciones para las citas tipo Valoración:", ln=True, align="L")
            pdf.set_x(pdf.get_x() + 10)  # 🔹 Sangría
            pdf.multi_cell(190, 5, observaciones['valoracion'], align="L")
            pdf.ln(5)  # Espacio entre secciones

        if observaciones.get('terapia'):
            pdf.cell(200, 10, "Tenga en cuenta las siguientes recomendaciones para las citas tipo Terapia:", ln=True, align="L")
            pdf.set_x(pdf.get_x() + 10)  # 🔹 Sangría
            pdf.multi_cell(190, 5, observaciones['terapia'], align="L")
