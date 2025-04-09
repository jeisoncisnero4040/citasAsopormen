import React from "react";
import { FaFacebookF, FaInstagram, FaTwitter, FaYoutube, FaLinkedinIn,FaPhone } from "react-icons/fa"; 
import '../../styles/clientsPage/ContacsAsopormen.css';

class ContacsAsopormen extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return (
      <div className="wrapper-contacs-asopormen">
        <div className="net-asopormen">
          <p>Síguenos en nuestras redes sociales</p>
          <div className="social-icons">
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
        <div className="number-asopormen">
          <FaPhone className="phone-icon" />
          <div className="number-cel-asopormen">
                <p>Linea Única De Citas</p>
                <p>3166922054</p>
          </div>
        </div>
      </div>
    );
  }
}

export default ContacsAsopormen;
