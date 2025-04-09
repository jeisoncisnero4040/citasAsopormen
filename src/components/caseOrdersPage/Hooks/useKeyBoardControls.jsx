import { useEffect } from "react";

const useKeyboardControls = (onNext, onPrev, setViewerVisible, viewerVisible,displayReject) => {
  useEffect(() => {
    const handleKeyPress = (event) => {
      if (!viewerVisible &&!displayReject && (event.key === "d" || event.key === "ArrowRight")) {
        onNext();  
      } else if (!viewerVisible &&!displayReject && (event.key === "a" || event.key === "ArrowLeft")) {
        onPrev();  
      } else if (!viewerVisible && event.key === "Enter") {
        setViewerVisible(true);  
      } else if (viewerVisible && event.key === "Escape") {
        setViewerVisible(false);  
      }
    };

    window.addEventListener("keydown", handleKeyPress);
    
    return () => {
      window.removeEventListener("keydown", handleKeyPress);
    };
  }, [onNext, onPrev, setViewerVisible, viewerVisible,displayReject]);

};

export default useKeyboardControls;
