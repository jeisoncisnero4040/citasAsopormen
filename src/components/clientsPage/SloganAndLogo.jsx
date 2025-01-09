import React from "react";
import '../../styles/clientsPage/SloganAndLogo.css'

class SloganAndLogo extends React.Component{
    render(){
        return(
            <div className="slogan-logo">
                <div className="slogan">
                    <h2 className="portal">Portal de usuarios  </h2>
                    
                </div>
                <div className="logo-1">
                    <h1 className="slogan-text">Asopormen, Nuestra Razón de Ser!!!</h1>
                    <img src="https://asopormen.org.co/storage/ASO.550-2-1536x1536.png" />
                </div>
            </div>
        )
    }
}
export default SloganAndLogo;