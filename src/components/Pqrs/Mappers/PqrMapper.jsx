class PqrMapper {
    static PqrFromFormToRequest = (object) => {
      if (object.id_tipo) delete object.id_tipo;
      if (object.macromotivo_id) delete object.macromotivo_id;
      if (object.general_id) delete object.general_id;
      if (object.especifico_id) delete object.especifico_id;
  
      return object;
    };
    static notifyPqrsToAreaMapper(pqrs,user){
        pqrs['user']=user?.usuario;
        if(pqrs.acciones) delete pqrs.acciones;
        return pqrs;
    }
  }
  
  export default PqrMapper;
  