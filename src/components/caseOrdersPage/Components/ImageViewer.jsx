import React from "react";
import Viewer from "react-viewer";

const ImageViewer = ({ visible, imageUrl, closeViewer }) => {
    return (
        <Viewer
            visible={visible}
            onClose={closeViewer}
            images={[{ src: imageUrl, alt: "Imagen" }]}
            zoomable={true}  
            scalable={true}  
            rotatable={true} 
            downloadable={true}  
            noClose={true} 
            noNavbar={true}  
        />
    );
};

export default ImageViewer;
