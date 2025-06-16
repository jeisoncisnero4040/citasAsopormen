import { Plot } from "../../Informes/Utils/Plot";

export class PlotterService {
  static getOptions = (scala = 4000, stepSize =100, enableScales = true) => {
    return {
      responsive: true,
      plugins: {
        legend: { position: "top" },
        zoom: {
          zoom: {
            wheel: { enabled: true },
            pinch: { enabled: true },
            mode: "xy",
          },
          pan: {
            enabled: true,
            mode: "xy",
          },
        },
      },
      ...(enableScales && {
        scales: {
          y: {
            beginAtZero: true,
            min: 0,
            ...(scala !== undefined && scala !== null ? { max: scala } : {}),
            ticks: {
              stepSize: stepSize,
            },
            title: {
              display: true,
              text: "Valor",
            },
          },
          x: {
            title: {
              display: true,
              text: "Mes",
            },
          },
        },
      }),
    };
  };


    static plotLine(data, yField, xField, groupField, scala = 100, stepSize = 5, ref = null) {
    const xValues = [...new Set(data.map(item => item[xField]))].sort();
    const groups = [...new Set(data.map(item => item[groupField]))];
    
    const datasets = groups.map((group,index) => {
        const values = xValues.map(x => {
        const record = data.find(item => item[xField] === x && item[groupField] === group);
        const raw = record?.[yField];
        const num = typeof raw === "string" ? parseFloat(raw) : raw;
        return typeof num === "number" && !isNaN(num) ? num : 0;
        });

        return {
        label: group,
        data: values,
        fill: false,
        borderColor: PlotterService.getRandomColor(index+1),
        tension: 0.4,
        pointRadius: 4,
        };
    });

    const chartData = {
        labels: xValues,
        datasets,
    };

    return new Plot("line", chartData, PlotterService.getOptions(scala, stepSize), ref);
    }

static getRandomColor(indice) {
  const palette = {
    1: "#e6194b",  // rojo fuerte
    2: "#3cb44b",  // verde brillante
    3: "#ffe119",  // amarillo
    4: "#0082c8",  // azul vivo
    5: "#f58231",  // naranja fuerte
    6: "#911eb4",  // púrpura fuerte
    7: "#46f0f0",  // cian
    8: "#f032e6",  // fucsia
    9: "#d2f53c",  // lima
    10: "#fabebe", // rosa claro
    11: "#008080", // verde azulado
    12: "#e6beff", // lavanda
    13: "#aa6e28", // marrón medio
    14: "#fffac8", // beige claro
    15: "#800000", // marrón oscuro
    16: "#aaffc3", // verde menta
    17: "#808000", // oliva
    18: "#ffd8b1", // durazno claro
    19: "#000080", // azul marino
    20: "#808080"  // gris medio
  };

  return palette[indice];
}

}
