import React, { Component } from "react";
import '../../../src/styles/clientsPage/Carrusel.css';
import imagen1 from '../../../src/assets/asopormenLogo.png';
import imagen2 from '../../../src/assets/asopormen.Instalaciones.jpg';
import imagen3 from '../../../src/assets/asopormenPricipal.webp';

class Carrusel extends Component {
  constructor(props) {
    super(props);
    this.state = {
      currentIndex: 0,
      images: [],
      contTime: 0,
    };
    this.timer = null;  
  }

  componentDidMount() {
    const randomImages = [imagen1, imagen2, imagen3];
    this.setState({ images: randomImages });
    this.startCounter();
  }

  componentWillUnmount() {
    this.clearCounter();  
  }

  startCounter = () => {
    this.clearCounter();  
    this.timer = setInterval(this.secondsCounter, 1000);  
  };

  clearCounter = () => {
    if (this.timer) {
      clearInterval(this.timer);
      this.timer = null;
    }
  };

  resetCounter = () => {
    this.setState({ contTime: 0 });
    this.startCounter(); 
  };

  secondsCounter = () => {
    if (this.state.contTime === 3) {
      this.nextSlide();  
      return;
    }
    this.setState((prevState) => ({ contTime: prevState.contTime + 1 }));
  };

  prevSlide = () => {
    this.setState(
      (prevState) => ({
        currentIndex:
          prevState.currentIndex === 0
            ? this.state.images.length - 1
            : prevState.currentIndex - 1,
      }),
      this.resetCounter  
    );
  };

  nextSlide = () => {
    this.setState(
      (prevState) => ({
        currentIndex:
          prevState.currentIndex === this.state.images.length - 1
            ? 0
            : prevState.currentIndex + 1,
      }),
      this.resetCounter  
    );
  };

  render() {
    const { images, currentIndex } = this.state;

    return (
      <div className="carousel-index-client">
        <div className="button-slider">
          <button className="button-carousel-clients" onClick={this.prevSlide}>
            ❮
          </button>
        </div>
        <div className="carousel-images">
          {images.length > 0 && (
            <img
              className="img-carrousel-clients-page"
              src={images[currentIndex]}
              alt={`Imagen ${currentIndex + 1}`}
            />
          )}
        </div>
        <div className="button-slider">
          <button className="button-carousel-clients" onClick={this.nextSlide}>
            ❯
          </button>
        </div>
      </div>
    );
  }
}

export default Carrusel;
