from datetime import timedelta,date
from settings.AppSettings import MODE,WHATSAPP_PRUEBAS
import pandas as pd

class CitasMapper:

    @staticmethod
    def map(citas: list) -> list:
        citas_mapped = []
        for cita in citas:
            
            cita_cleaned = {key: (value.strip() if isinstance(value, str) else value) 
                            for key, value in cita.items()}
            
           
            day = cita_cleaned.get('fecha')
            hour = cita_cleaned.get('hora')
            duracion=cita_cleaned.get('duracion')
            
           
            hour_in_integer = CitasMapper.__map_hour_to_24_format(hour)
            minute_in_integer = CitasMapper.__get_minute(hour)
            
            
            day_with_hour = day + timedelta(hours=hour_in_integer, minutes=minute_in_integer)
            end_date=day_with_hour+ timedelta(minutes=int(duracion))

            day_with_hour_str = day_with_hour.strftime('%Y-%m-%d %H:%M')
            end_date_as_str=end_date.strftime('%Y-%m-%d %H:%M')
           
            cita_cleaned['date'] = day_with_hour_str
            cita_cleaned['end_date']=end_date_as_str

            del cita_cleaned['fecha']
            del cita_cleaned['hora']
            del cita_cleaned['duracion']
            
            if MODE=='LOCAL':
 
                cita_cleaned['telephone_number']=WHATSAPP_PRUEBAS

            citas_mapped.append(cita_cleaned)
        return citas_mapped

    @staticmethod
    def groupCitasBySession(citas:list)->list:
        if not citas:
            return []
        citas_gruped:pd.DataFrame=CitasMapper.__citas_gruped_by_sessions(citas)
        dict_citas_groped=citas_gruped.to_dict(orient='records')
        citas_ready_for_send:list=[]
        
        for cita in dict_citas_groped:
            range_time_cita_in_str: str = cita.get('session_time_range')
            range_time: list = range_time_cita_in_str.split(' - ')

            start_time = pd.to_datetime(range_time[0])  
            end_time = pd.to_datetime(range_time[1]) 

            
            start_hour: str = start_time.strftime('%Y-%m-%d %H:%M') 
            end_hour: str = end_time.strftime('%Y-%m-%d %H:%M') 

            cita.update({'date': start_hour})
            cita.update({'end_date': end_hour})
            del cita['id']
            del cita['session_time_range']
            citas_ready_for_send.append(cita)
        
        return citas_ready_for_send

    @staticmethod
    def __map_hour_to_24_format(hour: str):
         
        is_am = hour[-2:] == 'AM'
        hour_value = int(hour.split(':')[0])
        
         
        if is_am:
            return 0 if hour_value == 12 else hour_value
        else:
            return 12 if hour_value == 12 else hour_value + 12

    @staticmethod
    def __citas_gruped_by_sessions(citas: list) -> pd.DataFrame:
        
        df = pd.DataFrame(citas)
        df_clean = CitasMapper.__delete_citas_duplicates(df)

        df_clean['date'] = pd.to_datetime(df_clean['date'])
        df_clean['end_date'] = pd.to_datetime(df_clean['end_date'])
        
       
        df_clean_and_ordered = df_clean.sort_values(by=['date', 'end_date'], ascending=True)
        
        
        grouped = df_clean_and_ordered.groupby(
            ['procedim', 'direction', 'observations', 'client', 'telephone_number', 'profesional']
        )

       
        result_list = []

        
        for _, group in grouped:
           
            group = group.reset_index(drop=True)
            session_ids = []
            session_time_ranges = []  
            
            
            current_session_id = [group.loc[0, 'id']]
            start_time = group.loc[0, 'date'] 
            
            for i in range(1, len(group)):
                if group.loc[i, 'date'] == group.loc[i - 1, 'end_date']: 
                    current_session_id.append(group.loc[i, 'id'])
                else:
                    session_ids.extend([f"{'|||'.join(map(str, current_session_id))}"] * len(current_session_id))
                    session_time_ranges.extend([f"{start_time} - {group.loc[i-1, 'end_date']}"] * len(current_session_id))
                    
                    current_session_id = [group.loc[i, 'id']]
                    start_time = group.loc[i, 'date']  # Actualizar la hora de inicio de la nueva sesión
            
            
            session_ids.extend([f"{'|||'.join(map(str, current_session_id))}"] * len(current_session_id))
            session_time_ranges.extend([f"{start_time} - {group.loc[len(group) - 1, 'end_date']}"] * len(current_session_id))
            
            
            group['session_ids'] = session_ids
            group['session_time_range'] = session_time_ranges
            result_list.append(group)

        # Concatenar todos los grupos nuevamente en un solo DataFrame
        final_df = pd.concat(result_list)
        return CitasMapper.__get_citas_by_group_sessions(final_df)
        

    @staticmethod
    def __delete_citas_duplicates(citas_in_df:pd.DataFrame):
        df_not_duplicates = citas_in_df.drop_duplicates(subset=citas_in_df.columns.difference(['id']))
        return df_not_duplicates

    @staticmethod
    def __get_citas_by_group_sessions(citas_group_sessions:pd.DataFrame)->pd.DataFrame:
        Citas_ready_in_df=citas_group_sessions.drop_duplicates(subset=citas_group_sessions.columns.difference(['id','date','end_date']))
        return Citas_ready_in_df

    

    
    @staticmethod
    def __get_minute(hour: str):
         
        return int(hour[3:5])


    
