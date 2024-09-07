// src/App.js
import React from "react";
import { Route, Routes } from "react-router-dom";
import HomePage from "./components/HomePage";
import FormCitas from "./components/FormCitas";
import ProtectedRoute from "./util/ProtectedRout";
import RecoverPasswordPage from "./components/RecoverPasswordPage";
import UpdatePasswordPage from "./components/UpdatePassword";
import "./styles/app.css";


function App() {
  return (
    
      <div className="app-container">
        <Routes>
          
          <Route path="/recover_password" element={<RecoverPasswordPage/>} />
          <Route path="/" element={<HomePage />} />
          <Route element={<ProtectedRoute/> }>
              <Route path="/formcitas" element={<FormCitas />} />
          </Route>
          <Route path="/update_password" element={<UpdatePasswordPage/>}/> 
          
        </Routes>
      </div>
    
  );
}

export default App;
