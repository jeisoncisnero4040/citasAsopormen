import ApiService from "./ApiService";

class OrderCase {
    static dataToTable = [
        { key: "id", label: "ID", sortable: true },
        { key: "num_historia", label: "N° Historia", sortable: true },
        { key: "cedula_cliente", label: "Cédula", sortable: true },
        { key: "nombre_cliente", label: "Nombre", sortable: true },
        { key: "celular_cliente", label: "Celular", sortable: true },
        { key: "eps_cliente", label: "EPS", sortable: true },
        { key: "direccion_cliente", label: "Dirección", sortable: false },
        { key: "fecha_creacion", label: "Fecha de Creación", sortable: true },
        { key: "acciones", label: "Acciones", sortable: false }
    ];

    static keysToGroupInOnlyKey = [
        { key: "num_historia", title: "Código" },
        { key: "nombre_cliente", title: "Nombre" },
        { key: "cedula_cliente", title: "Cédula" },
        { key: "celular_cliente", title: "Celular" },
        { key: "eps_cliente", title: "EPS" },
        { key: "direccion_cliente", title: "Dirección" },
        { key: "email_cliente", title: "Email" },
        { key: "codigo_autorizacion", title: "Autorización Código" }
    ];

    static keysdisplayed = [
        { key: "info_user", url: false, title: "Información del Cliente" },
        { key: "url_imagen_cedula1", url: true, title: "Imagen Cédula Frontal" },
        { key: "url_imagen_cedula2", url: true, title: "Imagen Cédula Trasera" },
        { key: "url_imagen_order", url: true, title: "Orden Médica" },
        { key: "url_imagen_historia_1", url: true, title: "Historia Clínica 1" },
        { key: "url_imagen_historia_2", url: true, title: "Historia Clínica 2" },
        { key: "url_imagen_historia_3", url: true, title: "Historia Clínica 3" },
        { key: "url_imagen_historia_4", url: true, title: "Historia Clínica 4" },
        { key: "url_imagen_historia_5", url: true, title: "Historia Clínica 5" },
        { key: "url_imagen_preautoriz", url: true, title: "Pre-autorización" },
        { key: "url_imagen_autoriz", url: true, title: "Imagen de Autorización" },
        { key: "url_case_in_pdf", url: false, title: "Caso en PDF" },
        { key: "descripcion_caso_particular", url: false, title: "Descripción del Caso" },
        { key: "observaciones_caso", url: false, title: "Observaciones" }
    ];

    static isOrderAccepted(order) {
        return order.aceptada === "1";
    }
    

    async fetchCases() {
        try {
            const response = await new ApiService().getMethod("case/all");
            return {data:response.data.data};
        } catch (error) {
            return {
                success: false,
                error: error,
                data:[]
            };
        }
    }
    async closeOrder(id, usuario, cases) {
        const Payload = { 'id': id, 'usuario': usuario };
    
        try {
            const response = await new ApiService().postMethod("case/close", Payload);
            return {
                success: true,
                data: OrderCase.deleteCaseClosedOrRejected(cases, id),
                message:'La orden fue cerrada satisfactoriamente'
            };
        } catch (error) {
            return {
                success: false,
                error: "Error al cerrar el caso: " + error,
                data:cases
            };
        }
    }
    async accettCase(id, usuario, cases) {
        const Payload = { 'id': id, 'usuario': usuario };
    
        try {
            const response = await new ApiService().postMethod("case/accept", Payload);
            return {
                success: true,
                data: OrderCase.updateCaseToAccepted(cases,id),
                message:'orden aceptada'
            };
        } catch (error) {
            return {
                success: false,
                error: "Error al cerrar el caso: " + error,
                data:cases
            };
        }
    }
    async rejectCase(id, usuario, razon,cases) {
        const Payload = { 'id': id, 'usuario': usuario,'observaciones':razon };

    
        try{
            const response = await new ApiService().postMethod("case/reject", Payload);
            return {
                success: true,
                data: OrderCase.deleteCaseClosedOrRejected(cases,id),
                message:'La orden fue Rechazada satisfactoriamente'
            };
        }catch (error) {
            return {
                success: false,
                error: "Error al cerrar el caso: " +error,
                data:cases
            };
        }
    }
    
    
    static deleteCaseClosedOrRejected(cases, id) {
        return cases.filter(caseItem => caseItem.id !== id);
    }
    static updateCaseToAccepted(cases, id) {
        return cases.map(caseItem => 
            caseItem.id === id ? { ...caseItem, aceptada: "1" } : caseItem
        );
    }
    static mapOrder(order) {
        order["info_user"] = OrderCase.getFormattedUserInfo(order);
        return order;
    }



    static getFormattedUserInfo(order) {
        return OrderCase.keysToGroupInOnlyKey
            .map(({ key, title }) => {
                let value = order[key];
                if (key === "num_historia" && !value) {
                    value = "Usuario nuevo";
                }
                return value ? `${title}: ${value}` : null;
            })
            .filter(Boolean)  
            .join("\n");
    }
}

export default OrderCase;
