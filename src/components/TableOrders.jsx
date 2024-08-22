import React,{Component} from "react";
import '../styles/TableOrders.css'

class TableOrders extends Component{
    render(){
        return (
            <div className="table-container">
                 
                <div className="select-orden-4">
                     
                     
                        <label>Seleccionar Orden</label>
                        <select >
                            
                        </select>
                     
                </div>
                <div className="table-orders">
                <table class="table  table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>971000</td>
                        <td>Terapia Física</td>
                        <td>20</td>
                        </tr>
                        <tr>
                        <td>977500</td>
                        <td>Terapia Lenguaje</td>
                        <td>20</td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                 
                <div></div>
            </div>
        )
    }
}

export default TableOrders;