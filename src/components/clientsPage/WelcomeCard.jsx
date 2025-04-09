import { useState } from "react";
import { FaHome } from "react-icons/fa";
import "../../styles/clientsPage/WelcomeCard.css";
import { useNavigate } from "react-router-dom";

function WelcomeCard({ client,title }) {
    const [isOpen, setIsOpen] = useState(false);

    const toggleDropdown = () => {
        setIsOpen(!isOpen);
    };
    const navigate = useNavigate();

    const _redirectToHome=()=>{
      navigate("/clientes_citas", { state:client});
    }

    const logout = () => {
        localStorage.removeItem("authToken");
        localStorage.removeItem("userCitas");
        localStorage.removeItem('userCitasHistory');
        window.location.href = "/clinico/clientes";
    };

    return (
        <div className="wrapper-card-welcome">
            <a className="redirect-to-home" onClick={()=>_redirectToHome()}><FaHome className="home-icon" /></a>
            <p>{title}</p>
            <div className="name-client-and-dropdown">
                <div className="dropdown-card-welcome">
                    <button className="dropdown-button" onClick={toggleDropdown}>
                        {client.nombre ?? "Opciones"}
                    </button>
                    {isOpen && (
                        <ul className="dropdown-menu-client">
                            <li onClick={logout}>Cerrar sesión</li>
                        </ul>
                    )}
                </div>
            </div>
        </div>
    );
}

export default WelcomeCard;

