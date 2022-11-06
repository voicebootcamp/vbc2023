(function (document, Joomla) {
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('osm-sales-chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Joomla.getOptions('labels'),
                datasets: [{
                    label: Joomla.JText._('OSM_SALES_INCOME'),
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    data: Joomla.getOptions('sales'),
                    fill: false,
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                        }
                    }]
                },
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        });
    });

    reloadSalesChart = function () {
        var planId = document.getElementById('plan_id').value;
        var ajaxUrl = Joomla.getOptions('ajaxUrl') + '&plan_id=' + planId;

        Joomla.request({
            url: ajaxUrl,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            onSuccess: function (resp) {
                var msg = JSON.parse(resp);
                var ctx = document.getElementById('osm-sales-chart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: msg.labels,
                        datasets: [{
                            label: Joomla.JText._('OSM_SALES_INCOME'),
                            backgroundColor: 'rgb(255, 99, 132)',
                            borderColor: 'rgb(255, 99, 132)',
                            data: msg.sales,
                            fill: false,
                        }]
                    },
                    plugins: [ChartDataLabels],
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                }
                            }]
                        },
                        plugins: {
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                });
            },
            onError: function (error) {
                alert(error.statusText);
            }
        });
    };
})(document, Joomla);