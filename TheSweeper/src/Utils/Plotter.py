import plotly.express as px
from plotly.subplots import make_subplots
import pandas as pd
import plotly.graph_objects as go

class Plotter:

    @staticmethod
    def generatePlotCitasNotified(citasNotified: pd.DataFrame):
        if citasNotified.empty:
            return None


        figure = make_subplots(
            rows=2, cols=2,
            column_widths=[0.5, 0.5],
            row_heights=[0.5, 0.5],
            specs=[[{"type": "pie"}, {"type": "pie"}],
                   [{"type": "pie"}, None]],
            subplot_titles=("Porcentaje citas Canceladas, Confirmadas, y Sin Confirmar",
                            "Porcentaje citas Canceladas por Sede", 
                            "Porcentaje citas Canceladas por Procedimiento")
        )

        
        df1 = citasNotified['confirma'].value_counts().reset_index()
        df1.columns = ['Estado', 'Conteo']
        pie1 = px.pie(df1, values='Conteo', names='Estado', title="Estado de Confirmación")
        figure.add_trace(pie1.data[0], row=1, col=1)

        df2 = citasNotified[citasNotified['cancelada'] == '1']['sede'].value_counts().reset_index()
        df2.columns = ['Sede', 'Conteo']
        pie2 = px.pie(df2, values='Conteo', names='Sede', title="Citas Canceladas por Sede")
        figure.add_trace(pie2.data[0], row=1, col=2)

        df3 = citasNotified[citasNotified['cancelada'] == '1']['procedipro'].value_counts().reset_index()
        df3.columns = ['Procedimiento', 'Conteo']
        pie3 = px.pie(df3, values='Conteo', names='Procedimiento', title="Citas Canceladas por Procedimiento")
        figure.add_trace(pie3.data[0], row=2, col=1)

        
        figure.update_layout(
            title_text="Análisis de Citas Notificadas",
            title_x=0.5,  
            title_y=0.5,  
            showlegend=True,  
            height=800, 
            margin=dict(l=50, r=50, t=50, b=50), 
            plot_bgcolor='rgba(0,0,0,0)',  
            paper_bgcolor='lightgrey',  
            title_font=dict(
                family="Arial",  
                size=35,  
                color="#1b0dd3",   
            )
        )

        
        figure.write_html('analisis.html', auto_open=False)
