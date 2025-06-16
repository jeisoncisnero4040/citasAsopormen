import {
  Bar,
  Line,
  Pie,
  Doughnut,
} from "react-chartjs-2";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  ArcElement,
  Tooltip,
  Legend,
} from "chart.js";
import zoomPlugin from 'chartjs-plugin-zoom';

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  PointElement,
  LineElement,
  ArcElement,
  Tooltip,
  Legend,
  zoomPlugin 
);

export class Plot {
  constructor(type, data, options = {},ref=null) {
    this.type = type;
    this.data = data;
    this.options = options;
    this.ref=ref
  }

  display() {
    const commonProps = {
      data: this.data,
      options: this.options,
      ...(this.ref ? { ref: this.ref } : {}),
    };

    switch (this.type) {
      case "bar":
        return <Bar {...commonProps} />;
      case "line":
        return <Line {...commonProps} />;
      case "pie":
        return <Pie {...commonProps} />;
      case "doughnut":
        return <Doughnut {...commonProps} />;
      default:
        throw new Error(`Unsupported chart type: ${this.type}`);
    }
  }
    
  toImage() {                                                     
      return this.ref.current.toBase64Image();
  }

}
