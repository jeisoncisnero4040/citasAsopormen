import { useState, useEffect } from "react";

const useCarousel = (items, dataObject) => {
  const [currentIndex, setCurrentIndex] = useState(0);
  const itemsValids = items.filter(item => dataObject[item.key]);
  const nextSlide = () => {
    setCurrentIndex(prev => (prev + 1) % itemsValids.length);
  };

  const prevSlide = () => {
    setCurrentIndex(prev => (prev - 1 + itemsValids.length) % itemsValids.length);
  };

  
  const currentItem = itemsValids[currentIndex] || {};
  const value = dataObject[currentItem.key] || null;

  
  useEffect(() => {
    if (!value && itemsValids.length > 1) {
      nextSlide();
    }
  }, [currentIndex]);

  return {
    currentItem,
    value,
    nextSlide,
    prevSlide,
    currentIndex,
    totalSlides: itemsValids.length,
  };
};

export default useCarousel;
