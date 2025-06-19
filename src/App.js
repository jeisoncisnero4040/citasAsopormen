// src/App.js
import { Route, Routes } from "react-router-dom";
import HomePage from "./components/HomePage";
import ProtectedRoute from "./util/ProtectedRout";
import RecoverPasswordPage from "./components/Pqrs/Auth/Components/RecoverPasswordPage";
import "./styles/app.css";
import IndexCreatePqrs from "./components/Pqrs/Components/indexCreatePqrs";
import IndexViewPqrs from "./components/Pqrs/Components/indexViewPqrs";
import AnswerPqrsForm from "./components/Pqrs/Components/AnswerPqrsForm";
import IndexInformesPqrs from "./components/Pqrs/Components/IndexInformesPqrs";
import UpdatePasswordPage from "./components/Pqrs/Auth/Components/UpdatePassword";



function App() {
  return (
    
      <div className="app-container">
        <Routes>

          <Route path="/recover_password" element={<RecoverPasswordPage/>} />
          <Route path="/" element={<HomePage />} />
          <Route path="/pqrs/responder/:id-encoded" element={<AnswerPqrsForm/>}/> 
          <Route element={<ProtectedRoute/> }>
              <Route path="/update_password" element={<UpdatePasswordPage/>}/>
              <Route path="/pqrs/crear" element={<IndexCreatePqrs/>}/> 
              <Route path="/pqrs" element={<IndexViewPqrs/>}/> 
              <Route path="/pqrs/informes" element={<IndexInformesPqrs/>}/>
          </Route>
        </Routes>
      </div>
            
  );
}

export default App;
