import React from "react";
import "../../styles/clientsPage/CardSede.css"

class CardSede extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            sedes : [
                {
                    nombre: "SEDE PRINCIPAL",
                    direccion: "Carrera 27 No. 42-52, Bucaramanga – Santander",
                    img_url:"https://asopormen.org.co/storage/elementor/thumbs/SEDE-PRINCIPAL-q7nx9d4jt2djtvwz4f3wy6he0hdsyipudzto3pk9bc.webp",
                    maps_url: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.376992288244!2d-73.119!3d7.119!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e683f8c14c01e59%3A0x8e134b0e134b!2sCarrera%2027%20No.%2042-52%2C%20Bucaramanga%2C%20Santander!5e0!3m2!1ses!2sco!4v1699999999999"
                },

                {
                    nombre: "SEDE ABA TOMATIS",
                    direccion: "Carrera 26 # 41-13 Sotomayor, Bucaramanga – Santander",
                    img_url:"https://asopormen.org.co/storage/elementor/thumbs/SEDE-ABA-TOMATIS-2023-q7nx9fy2dkhespsvnybsnnrrsmzwlm11eds4jjg2so.webp",
                    maps_url: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.376992288244!2d-73.120!3d7.120!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e683f8c14c01e59%3A0x8e134b0e134b!2sCarrera%2026%20No.%2041-13%2C%20Bucaramanga%2C%20Santander!5e0!3m2!1ses!2sco!4v1699999999999"
                },
                {
                    nombre: "SEDE EDUCACIÓN",
                    direccion: "Calle 42 No. 27-28, Bucaramanga – Santander",
                    img_url:"https://asopormen.org.co/storage/elementor/thumbs/EDTH-PROYECCION-q7nx9f086qg4h3u8tfx6360b794jdwxb294n29hgyw.webp",
                    maps_url: "//www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7918.136735269297!2d-73.115599!3d7.118076!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e683fdee1220f29%3A0xa35a4056444d1f29!2sAsopormen!5e0!3m2!1ses!2sus!4v1738710356053!5m2!1ses!2sus"
                },
                {
                    nombre: "SEDE BOLARQUI",
                    direccion: "Calle 53 # 27-33, Bucaramanga – Santander",
                    img_url:"https://asopormen.org.co/storage/elementor/thumbs/SEDE-BOLARQUI-q7nx9gvwkeip4briigqf85j8e0v9tb4rqifm0teomg.webp",
                    maps_url: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7918.236099569369!2d-73.114157!3d7.112316!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e683fdd1d236a2b%3A0x59b7948a35e4e2b9!2sCl.%2053%20%2329-26%2C%20Sotomayor%2C%20Bucaramanga%2C%20Santander%2C%20Colombia!5e0!3m2!1ses!2sus!4v1738710443005!5m2!1ses!2sus"
                },
                {
                    nombre: "SEDE SAN GIL",
                    direccion: "Carrera 17 No. 35-08, San Gil – Santander",
                    img_url:"https://asopormen.org.co/storage/elementor/thumbs/SEDE-SAN-GIL-q7nx9e2dzweu5hvlyxijio8ulv9667tkq4h5kziv54.webp",
                    maps_url: "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7927.48898242793!2d-73.141363!3d6.553907!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e69c6d0a932b827%3A0x9eaa068f802bce7f!2sCra.%2017%20%2335-19%2C%20San%20Gil%2C%20Santander%2C%20Colombia!5e0!3m2!1ses!2sus!4v1738710500726!5m2!1ses!2sus"
                }
            ]
        };
    }

    render() {
        return (
            <div className="card-sedes-container">
                {this.state.sedes.map((sede, index) => (
                    <div key={index} className="card-sede">
                        <div className="card-sede-header">
                            <p className="card-sede-header-name-sede">{sede.nombre}</p>
                            <p className="card-sede-header-direction-sede">{sede.direccion}</p>
                        </div>
                        <div className="card-sede-body">
                            <div className="card-sede-body-img">
                                <img src={sede.img_url} alt={sede.nombre} className="sede-img" />
                            </div>
                            <div className="card-sede-body-map">
                                <iframe
                                    src={sede.maps_url}
                                    className="sede-map"
                                    allowFullScreen=""
                                    loading="lazy"
                                    referrerPolicy="no-referrer-when-downgrade"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        );
    }
}

export default CardSede;

