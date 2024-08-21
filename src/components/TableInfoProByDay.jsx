import React from 'react';
import { useLocation } from 'react-router-dom';

const TableInfoProByDay = () => {
    const location = useLocation();
    const { filteredEvents } = location.state || {}; 
    console.log(filteredEvents) 

    return (
        <div>
            <h1>Eventos del Día</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    {filteredEvents && filteredEvents.length > 0 ? (
                        filteredEvents.map((event, index) => (
                            <tr key={index}>
                                <td>{event.nombre}</td>
                                <td>{new Date(event.date).toLocaleDateString()}</td>
                            </tr>
                        ))
                    ) : (
                        <tr>
                            <td colSpan="2">No hay eventos para mostrar</td>
                        </tr>
                    )}
                </tbody>
            </table>
        </div>
    );
};

export default TableInfoProByDay;

