import { Plot } from "../Utils/Plot";

export class plotService {
  static getOptions = (scala = 50, stepSize = 5, enableScales = true) => {
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

  static plotPie(data, label) {
    const sedeLabels = data.map((item) => item.nombre.trim());
    const sedeCounts = data.map((item) => parseInt(item.pqrs, 10));

    const pieChartData = {
      labels: sedeLabels,
      datasets: [
        {
          label: label,
          data: sedeCounts,
          backgroundColor: sedeLabels.map(() => plotService.getRandomColor()),
          borderWidth: 1,
        },
      ],
    };

    // No escalas para pie chart
    return new Plot("pie", pieChartData, plotService.getOptions(undefined, undefined, false));
  }

  static plotLinePorTipo(data, scala = 50, stepSize = 5) {
    const fechas = [...new Set(data.map((item) => item.mes))].sort();
    const tipos = [...new Set(data.map((item) => item.nombre))];

    const datasets = tipos.map((tipo) => {
      const valores = fechas.map((fecha) => {
        const registro = data.find((item) => item.mes === fecha && item.nombre === tipo);
        return registro ? parseFloat(registro.pqrs) : 0;
      });

      return {
        label: tipo,
        data: valores,
        fill: false,
        borderColor: plotService.getRandomColor(),
        tension: 0,
        pointRadius: 5,
      };
    });

    const chartData = {
      labels: fechas,
      datasets,
    };

    return new Plot("line", chartData, plotService.getOptions(scala, stepSize));
  }

  static plotBar(data, labelField, datasetFields, labelMap = {}, colors = {},scala,stepSize) {
    const labels = data.map((item) => item[labelField]?.trim?.() ?? item[labelField]);

    const datasets = datasetFields
      .filter((field) => {
        return data.some((item) => parseInt(item[field], 10) > 0);
      })
      .map((field) => ({
        label: labelMap[field] || field,
        data: data.map((item) => parseInt(item[field], 10) || 0),
        backgroundColor: colors[field] || plotService.getRandomColor(scala, stepSize),
      }));

    const chartData = {
      labels,
      datasets,
    };

    return new Plot("bar", chartData, plotService.getOptions(scala,stepSize));
  }



  static getRandomColor() {
    const palette = [
      "#005082", "#4380ad", "#3a97ff", "#8bb7ff",
      "#d4f0ff", "#FAE105", "#ECDB00", "#FFF69D",

    ];
    return palette[Math.floor(Math.random() * palette.length)];
  }
}
