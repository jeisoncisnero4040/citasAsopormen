import React from "react";
import '../../styles/clientsPage/LoadingPage.css'
class LoadingPage extends React.Component{
    render(){
        return(
            <div classname="container-loading">
                <div class="wheel-container">
                    <div class="wheel"></div>
                </div>
                <div>
                    <p  className="text-loading">Cargando</p>
                </div>
            
            </div>
        )
    }
}
export default LoadingPage;