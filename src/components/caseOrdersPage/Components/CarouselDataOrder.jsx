import React, { useState } from "react";
import useCarousel from "../Hooks/useCarousel";
import useKeyboardControls from "../Hooks/useKeyBoardControls";
import "../Styles/CarouselDataOrder.css";
import ImageViewer from "./ImageViewer";
import OrderCase from "../Services/OrderCase";

const CarouselDataOrder = ({ order, closeVisualizer, accepCase, displayRejectOrder,displayReject,loading }) => {
    const [viewerVisible, setViewerVisible] = useState(false);
  const {
    currentItem,
    value,
    nextSlide,
    prevSlide,
    currentIndex,
    totalSlides,
  } = useCarousel(OrderCase.keysdisplayed, order);

  useKeyboardControls(nextSlide, prevSlide,setViewerVisible,viewerVisible,displayReject);

  // Estado para manejar el visor de imágenes


  return (
    <div className="modal-overlay" onClick={closeVisualizer}>
      <div className="modal-visualizer-case" onClick={(e) => e.stopPropagation()}>
        <h3>{currentItem?.title || "Sin título"}</h3>

        <div className="carousel-container">
          {currentItem.url ? (
            value ? (
              <img
                src={value}
                alt={currentItem.title || "Imagen"}
                className="carousel-image"
                onClick={() => setViewerVisible(true)} 
              />
            ) : (
              <p>Imagen no disponible</p>
            )
          ) : currentItem.title === "Caso en PDF" ? (
            value ? (
              <a href={value} target="_blank" rel="noopener noreferrer">
                Ver documento PDF
              </a>
            ) : (
              <p>PDF no disponible</p>
            )
          ) : (
            value?.split("\n").map((line, index) => (
              <p key={index}>{line}</p>
            ))
          )}

          {/* Botones flotantes para navegación */}
          <button className="nav-arrow left" onClick={prevSlide}>&#9665;</button>
          <button className="nav-arrow right" onClick={nextSlide}>&#9655;</button>
        </div>
        <div className="navigate-carosel-display-cases">
          <span>{currentIndex + 1} / {totalSlides} </span>
        </div>
        <div className="modal-footer">
          {!OrderCase.isOrderAccepted(order) ? (
              <button className="reject-btn" onClick={displayRejectOrder}>Rechazar caso</button>
          ) : (
              <button className="no-selectable" onClick={null}>Rechazar caso</button>
          )}

          {!OrderCase.isOrderAccepted(order) ? (
              <button className="accept-btn" onClick={() => accepCase(order)}>
                  {loading ? 'Cargando' : 'Aceptar Caso'}
              </button>
          ) : (
              <button className="no-selectable" onClick={null}>Aceptar Caso</button>
          )}

          <button className="close-btn" onClick={closeVisualizer}>Cerrar</button>
      </div>
      </div>

      {/* Visor de imágenes */}
      <ImageViewer
        visible={viewerVisible}
        imageUrl={value}
        closeViewer={() => setViewerVisible(false)}
      />
    </div>
  );
};

export default CarouselDataOrder;

