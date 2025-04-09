import React from "react";
import '../../styles/clientsPage/footer.css';
import logo from '../../assets/logo.clientes.png';
import { FaPhoneAlt, FaMapMarkerAlt, FaEnvelope, FaFacebookF, FaInstagram, FaTwitter, FaYoutube, FaLinkedinIn } from "react-icons/fa"; // Agregado los íconos de redes sociales

class Footer extends React.Component {
    render() {
        return (
            <div className="footer-clients-page-container">
                <div className="header-footer"></div>
                <div className="footer-info-asopormen">

                    <div className="footer-logo-and-slogan">
                        <img src={logo} alt="logo" />
                        <div className="text-footer-slogan">
                            <div className="asopormen-slogan-1">
                                <p className="asopormen">Asopormen, </p>
                                <p>Nuestra</p>
                            </div>
                            <p>Razón de ser</p>
                        </div>
                    </div>
                    <div className="footer-contacts">
                        <div className="contact">
                            <FaMapMarkerAlt className="icono"/>
                            <div>
                                <p>Carrera 27 No.42-52 - Bucaramanga</p>
                                <p>Santander, Colombia</p>
                            </div>
                        </div>
                        <div className="contact">
                            <FaPhoneAlt className="icono" />
                            <p>607 68529932</p>
                        </div>
                        <div className="contact ">
                            <FaEnvelope className="icono" />
                            <p>Subdireccionejecutiva@asopormen.org.co</p>
                        </div>
                    </div>
                    <div className="footer-social-nets">
                        <p>Síguenos en nuestras redes sociales</p>
                        <div className="social-icons-footer">
                            <a href="https://facebook.com/asopormen/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                                <FaFacebookF />
                            </a>
                            <a href="https://instagram.com/asopormen/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                <FaInstagram />
                            </a>
                            <a href="https://twitter.com/asopormen/" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                                <FaTwitter />
                            </a>
                            <a href="https://youtube.com/@asopormen/" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                                <FaYoutube />
                            </a>
                            <a href="https://linkedin.com/company/asopormen/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                                <FaLinkedinIn />
                            </a>
                        </div>
                        
                    </div>
                </div>
                <div className="footer">
                    <p>&copy;2025 - TODOS LOS DERECHOS SON RESERVADOS</p>
                    <p>ASOPORMEN</p>
                </div>
            </div>
        );
    }
}

export default Footer;
