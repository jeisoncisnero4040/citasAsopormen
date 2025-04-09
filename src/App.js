// src/App.js
import React from "react";
import { Route, Routes } from "react-router-dom";
import HomePage from "./components/HomePage";
import FormCitas from "./components/FormCitas";
import ProtectedRoute from "./util/ProtectedRout";
import ProtectedRouteClient from "./util/ProtecteRouteClient";
import RecoverPasswordPage from "./components/RecoverPasswordPage";
import UpdatePasswordPage from "./components/UpdatePassword";
import "./styles/app.css";
import ReassingCitasPage from "./components/ReassingCitas/ReassingCitasPage";
import IndexClients from "./components/clientsPage/indexClients";
import RequestPasswordClients from "./components/clientsPage/RequestPasswordClients";
import ClientsPage from "./components/clientsPage/ClientsPage";
import HistoryChatbotPage from "./components/HistoyChatbot/HistoryChatbotPage";
import UsuariosCitas from "./components/clientsPage/UsuariosCitas";
import UpdateUser from "./components/clientsPage/UpdateUser";
import Sedes from "./components/clientsPage/Sedes";
import UsuarioHistory from "./components/clientsPage/UsuarioHistory";
import IndexOrdersCase from "./components/caseOrdersPage/Components/index";


function App() {
  return (
    
      <div className="app-container">
        <Routes>
          
          <Route path="/recover_password" element={<RecoverPasswordPage/>} />
          <Route path="/" element={<HomePage />} />
          <Route path="/clientes" element={<IndexClients/>} />
          <Route path="/solicitar_contraseña" element={<RequestPasswordClients/>} />
          
          <Route element={<ProtectedRoute/> }>
              <Route path="/history_chatbot" element={<HistoryChatbotPage/>}/> 
              <Route path="/formcitas" element={<FormCitas />} />
              <Route path="/update_password" element={<UpdatePasswordPage/>}/> 
              <Route path="/reasignador_citas" element={<ReassingCitasPage/>}/> 
              <Route path="/orders" element={<IndexOrdersCase/>}/> 

          </Route>
          <Route element={<ProtectedRouteClient/> }>
              <Route path="/clientes_citas" element={<ClientsPage/>}/>
              <Route path="/clientes_citas/citas" element={<UsuariosCitas/>}/>  
              <Route path="/clientes_citas/actualizar-datos" element={<UpdateUser/>}/>  
              <Route path="/clientes_citas/nuestras-sedes" element={< Sedes />}/> 
              <Route path="/clientes_citas/historial" element={<UsuarioHistory  />}/>   
              
          </Route>
          
           
          
        </Routes>
      </div>
            
  );
}

export default App;
