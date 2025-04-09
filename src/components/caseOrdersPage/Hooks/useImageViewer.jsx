import { useState } from "react";

const useImageViewer = () => {
    const [visible, setVisible] = useState(false);
    const [imageUrl, setImageUrl] = useState("");

    const openViewer = (url) => {
        setImageUrl(url);
        setVisible(true);
    };

    const closeViewer = () => {
        setVisible(false);
    };

    return { visible, imageUrl, openViewer, closeViewer };
};

export default useImageViewer;
