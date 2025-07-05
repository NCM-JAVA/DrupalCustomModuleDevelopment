(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.csvChartDisplay = {
    attach: function (context, settings) {
      // Only once per attach, on the #csvChart canvas
      const canvas = once('csvChartBehavior', '#csvChart', context);
      if (!canvas.length) {
        return;
      }

      console.log("csv_chart_display.js loaded");

      const chartData = drupalSettings.csvChartData || {};
      if (!chartData.labels || !chartData.values) {
        console.log("No chart data available in drupalSettings.");
        return;
      }

      const ctx = canvas[0].getContext('2d');

      // Destroy existing chart instance if exists to avoid duplicates
      if (canvas[0].chartInstance) {
        canvas[0].chartInstance.destroy();
      }

      canvas[0].chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: chartData.labels,
          datasets: [{
            label: 'CSV Data',
            data: chartData.values,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);