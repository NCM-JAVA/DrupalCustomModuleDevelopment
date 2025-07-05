(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.employeePerformanceChart = {
    attach: function (context, settings) {
      // Check if canvas is in the current context
      if ($('#employeeChart', context).length === 0) {
        return;
      }

      // Get data from drupalSettings
      const dataSettings = drupalSettings.employeePerformance || {};
      const ctx = document.getElementById('employeeChart').getContext('2d');

      // Create Chart.js bar chart
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: dataSettings.labels || [],
          datasets: [{
            label: 'Performance Score',
            data: dataSettings.data || [],
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true,
              max: 100
            }
          }
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);