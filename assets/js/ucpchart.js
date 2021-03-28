"use strict";

var statistics_chart = document.getElementById("myChart").getContext('2d');
var isdays, isrefarr, isearnarr;

$.ajax({
    type: "GET",
    url: 'datachart.php',
    dataType: 'json',
    cache: false,
    success: function (data) {
        isdays = data['days'];
        isrefarr = data['dref'];
        isearnarr = data['dern'];

        var myChart = new Chart(statistics_chart, {
            type: 'line',
            data: {
                labels: isdays,
                datasets: [{
                        label: 'Referral',
                        data: isrefarr,
                        borderWidth: 3,
                        borderColor: '#3ABAF4',
                        backgroundColor: 'transparent',
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3ABAF4',
                        pointRadius: 5,
                        yAxisID: 'y-axis-1'
                    }, {
                        label: 'Earning',
                        data: isearnarr,
                        borderWidth: 3,
                        borderColor: '#47C363',
                        backgroundColor: 'transparent',
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#47C363',
                        pointRadius: 5,
                        yAxisID: 'y-axis-2'
                    }]
            },
            options: {
                responsive: true,
                legend: {
                    display: true,
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Referral & Earning'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    borderColor: 'rgba(178,190,195,.7)',
                    borderWidth: 2,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    yAxes: [{
                            ticks: {
                                stepSize: 10
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false,
                            },
                            position: 'left',
                            id: 'y-axis-1',
                        }, {
                            ticks: {
                                stepSize: 250
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false,
                            },
                            position: 'right',
                            id: 'y-axis-2',
                        }],
                    xAxes: [{
                            gridLines: {
                                color: '#fbfbfb',
                                lineWidth: 2
                            }
                        }]
                },
            },
            plugins: [{
                    beforeInit: function (chart) {
                        chart.data.labels.forEach(function (e, i, a) {
                            if (/\n/.test(e)) {
                                a[i] = e.split(/\n/)
                            }
                        })
                    }
                }]
        });

    }
});
