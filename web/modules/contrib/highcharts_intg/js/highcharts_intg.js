/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */
(function ($) {
    let basepath = drupalSettings.path.baseUrl;
    let chartDataURL = basepath + 'highcharts_intg/getdata/';
    var fid;

    $('.file-id').each(function (key, obj) {
        fid = $(this).attr('fid');
        modchartDataURL = chartDataURL + fid;
        $.getJSON(modchartDataURL, function (data) {
            chartRender(data);
        });
    });

    function chartRender(data) {
        Highcharts.chart('container' + data.fid, {
            chart: {
                type: data.chart_type
            },
            title: {
                text: '',
                align: 'left'
            },
            subtitle: {
                text: '',
                align: 'left'
            },
            yAxis: {
                title: {
                    text: data.y_axis.name
                },
                categories: data.y_axis.values
            },
            xAxis: {
                title: {
                    text: data.x_axis.name
                },
                categories: data.x_axis.values
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: FALSE
                    },
                    pointStart: 0
                },
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: TRUE
                    },
                    groupPadding: 0.1
                }
            },
            series: data.co_ord,
            responsive: {
                rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
            }
        });
    }
})(jQuery);
