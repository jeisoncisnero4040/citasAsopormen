import React, { Component } from "react";
import "../../../src/styles/clientsPage/Carrusel.css";
import imagen1 from "../../../src/assets/image.carousel.1.png";
import imagen2 from "../../../src/assets/image.corousel.2.jpg";
import imagen3 from "../../../src/assets/image.corousel.3.jpg";
import imagen4 from "../../../src/assets/image.carousel.4.jpg";

class Carrusel extends Component {
  constructor(props) {
    super(props);
    this.state = {
      currentIndex: 0,
      images: [],
      contTime: 0,
      mision:"Somos una entidad sin ánimo de lucro que presta servicios de salud, educación, recreación y deporte, contribuyendo al mejoramiento de la calidad de vida y garantizado los derechos a las personas con y sin discapacidad a través de la habilitación, rehabilitación y protección integral para la inclusión social con excelencia."
    };
    this.timer = null;
  }

  componentDidMount() {
    const randomImages = [imagen2, imagen3,imagen4];
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
    if (this.state.contTime === 5) {
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
        <div className="carousel-content">
          <div className="carousel-images">
            {images.length > 0 && (
              <img
                className="img-carrousel-clients-page"
                src={images[currentIndex]}
                alt={`Imagen ${currentIndex + 1}`}
              />
            )}
          </div>
          <div className="carousel-text-container">
            <p>{this.state.mision}</p>
          </div>
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

