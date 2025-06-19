import ApiRequestsManagerService from "./ApiRequestsManagerService";
import ResponseManager from "../Utils/ResponseManager";
import PqrMapper from "../Mappers/PqrMapper";


export default class PqrService {
  constructor(apiRequestManager) {
    this.apiRequestManager = apiRequestManager;
  }
  static dataToTable = [
    { key: "nombre_usuario", label: "Nombre Usuario", sortable: true },
    { key: "identificacion_usuario", label: "Identificación", sortable: true },
    { key: "tipo_usuario", label: "Tipo de Usuario", sortable: true },
    { key: "tipo_pqr", label: "Tipo PQR", sortable: true },
    { key: "area_servicio", label: "Área de Servicio", sortable: true },
    { key: "sogcs", label: "SOGCS", sortable: true },
    { key: "fecha_creacion", label: "Fecha de Creación", sortable: true },
    { key:"referencia",label: "referencia", sortable:false},
    { key: "acciones", label: "Acciones", sortable: false },

  ];
  static dataToTableHistory = [
    { key: "fecha_creacion", label: "Fecha de Creación", sortable: true },
    { key: "tipo", label: "Tipo PQR", sortable: true },
    { key: "tiempo_respuesta", label: "Tiempo Estimado de Respuesta (Horas)", sortable: true },
    { key: "tiempo_total", label: "Tiempo Total (Horas)", sortable: true },
    { key: "tiempo_en_area", label: "Tiempo en Área (Horas)", sortable: true },
    { key: "sede", label: "Sede", sortable: true },
    { key: "area", label: "Área", sortable: true },
    { key: "url_respuesta", label: "URL Respuesta", sortable: false },
  ];



  async getReasonsPqr() {
    try {
        const data =await  this.apiRequestManager.getMethod('reasons');
        return ResponseManager.success(data.data.data, false);
    } catch (error) {
        return ResponseManager.error(error.message || error);
    }
  }
  async getUtility(){
    try {
        const data = await this.apiRequestManager.getMethod('utils');
        return ResponseManager.success(data.data.data, false);
    } catch (error) {
        return ResponseManager.error(error.message || error);
    }
  }
  async createnewPqr(formdata){
    try {
      const data = await this.apiRequestManager.postMethod('pqrs/add',formdata);
      return ResponseManager.success(data.data.data, true);
  } catch (error) {
      return ResponseManager.error(error.message || error);
  }
  }

  async getPqrs(){
    try {
        const data = await this.apiRequestManager.getMethod('pqrs');
        return ResponseManager.success(data.data.data, false);
    } catch (error) {
        return ResponseManager.error(error.message || error);
    }
  }
  async notifyPqrsToArea(pqrs, user, pqrsList) {
    try{
      const payload = PqrMapper.notifyPqrsToAreaMapper(pqrs, user);
      const response = await this.apiRequestManager.postMethod('pqrs/notify-area', payload);
      const newPqrs = response.data.data[0];
      const updatedList = pqrsList.map(item =>item.id === newPqrs.id ? newPqrs : item);
      return ResponseManager.success(updatedList,true)
    }catch (error) {
      return ResponseManager.error(error.message || error);
    }
  }
  async changueAreaPqrs(pqrs,payload, pqrsList) {
    try{

      const response = await this.apiRequestManager.postMethod(`pqrs/${pqrs.id}/change-area`, payload);
      const newPqrs = response.data.data[0];
      const updatedList = pqrsList.map(item =>item.id === newPqrs.id ? newPqrs : item);
      return ResponseManager.success(updatedList,true)
    }catch (error) {
      return ResponseManager.error(error.message || error);
  }

}
  async sendPqrsAnswerArea(formData){
    try {
        const response = await this.apiRequestManager.postMethod('pqrs/answer-area',formData);
        return ResponseManager.success(response.data.data, true);
    } catch (error) {
        return ResponseManager.error(error.message || error);
    }
  }
  async getActionsPqrs(id){
    try {
      const response = await this.apiRequestManager.getMethod(`pqrs/${id}/actions`);
      return ResponseManager.success(response.data.data, false);
  } catch (error) {
      return ResponseManager.error(error.message || error);
  }
  }
  async getPqrsByIdEncoded(idEncoded){
    try {
      const response = await this.apiRequestManager.getMethod(`pqrs/${idEncoded}/encoded`);
      return ResponseManager.success(response.data.data[0], false);
  } catch (error) {
      return ResponseManager.error(error.message || error);
  }
  }
  async sendPqrsAnswerClient(formData,pqrs,pqrsList){
    try {
        formData.append('user',pqrs.nombre_quien_registra);
        formData.append('to',pqrs.email_usuario);
        formData.append('date',pqrs.fecha_creacion);
        formData.append('id',pqrs.id)
        formData.append('canal',pqrs.canal)
        formData.append('user_type',pqrs.tipo_usuario)
        const response = await this.apiRequestManager.postMethod('pqrs/answer/client',formData);
        const newPqrs = response.data.data[0];
        const updatedList = pqrsList.map(item =>item.id === newPqrs.id ? newPqrs : item);
        return ResponseManager.success(updatedList,true)
    } catch (error) {
      console.log(pqrs.id)
        return ResponseManager.error(error.message || error);
    }
  }
  static filterUtility = (utilities, filterValue) => {
    return utilities.filter(util => util.tipo === filterValue);
  };
  async getAllDataPqrs(from, to,tendencieFrom,tendencieTo) {
      try {
          const query = `from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&from_tendencie=${encodeURIComponent(tendencieFrom)}&to_tendencie=${encodeURIComponent(tendencieTo)}`;
          const response = await this.apiRequestManager.getMethod(`data/pqrs?${query}`);
          return ResponseManager.success(response.data.data, false);
      } catch (error) {
          return ResponseManager.error(error.message || error);
      }
  }
  async closePqrs(pqrs, pqrsList) {
    const id = pqrs?.id ?? null;
    
    try {
      const response = await this.apiRequestManager.postMethod(`pqrs/${id}/close`);
      const newPqrs = response.data.data[0];

      const updatedList = pqrsList.filter(item => item.id !== newPqrs.id);
      
      return ResponseManager.success(updatedList, true);
      
    } catch (error) {
      return ResponseManager.error(error.message || error);
    }
  }
  static filterReason=(parent_id,nivel,reasons)=>{

    if (parent_id){
      return reasons.filter(reason=>reason.id_padre===parent_id && reason.nivel===nivel);
    }
    return reasons.filter(reason=>reason.nivel===nivel);
  }
  static ReasonPqrsSelectAvaible = (formData, nivel) => {
    if (nivel === 'general') {
      return !!formData.macromotivo_id;
    }
    if (nivel === 'especifico') {
      return !!formData.general_id;
    }
    if (nivel === 'tipo') {
      return !!formData.especifico_id;
    }
    if (nivel === 'causa') {
      return !!formData.id_tipo;
    }
    return false;
  };

  
  
}
