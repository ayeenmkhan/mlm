"use strict";

var statistics_chart = document.getElementById("myChart").getContext('2d');
var isdays, isrefarr, isearnarr;

$.ajax({
    type: "GET",
    url: 'getdatachart.php',
    dataType: 'json',
    cache: false,
    success: function (data) {
        isdays = data['islbel'];
        isrefarr = data['isdat1'];
        isearnarr = data['isdat2'];

        var myChart = new Chart(statistics_chart, {
            type: 'line',
            data: {
                labels: isdays,
                datasets: [{
                        label: 'Registered',
                        data: isrefarr,
                        borderWidth: 3,
                        borderColor: '#3ABAF4',
                        backgroundColor: 'transparent',
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3ABAF4',
                        pointRadius: 5,
                        yAxisID: 'y-axis-1'
                    }, {
                        label: 'Incoming',
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
                    text: 'Registered & Incoming'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, .8)',
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

var statistics_chart1 = document.getElementById("myChart1").getContext('2d');

$.ajax({
    type: "GET",
    url: 'getdatachart.php',
    data: {dchart: '1'},
    dataType: 'json',
    cache: false,
    success: function (data) {
        isdays = data['islbel'];
        isrefarr = data['isdat1'];
        isearnarr = data['isdat2'];

        var myChart1 = new Chart(statistics_chart1, {
            type: 'line',
            data: {
                labels: isdays,
                datasets: [{
                        label: 'Registered',
                        data: isrefarr,
                        borderWidth: 2,
                        borderColor: '#3ABAF4',
                        backgroundColor: 'rgba(58, 186, 244, .3)',
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3ABAF4',
                        pointRadius: 3,
                        yAxisID: 'y-axis-1',
                    }, {
                        label: 'Member',
                        data: isearnarr,
                        borderWidth: 2,
                        borderColor: '#47C363',
                        backgroundColor: 'rgba(71, 195, 99, .3)',
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#47C363',
                        pointRadius: 3,
                        yAxisID: 'y-axis-2',
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
                    text: 'Registered & Member'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, .8)',
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
                                stepSize: 50
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false,
                            },
                            position: 'left',
                            id: 'y-axis-1',
                        }, {
                            ticks: {
                                stepSize: 10
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

var statistics_chart2 = document.getElementById("myChart2").getContext('2d');

$.ajax({
    type: "GET",
    url: 'getdatachart.php',
    data: {dchart: '2'},
    dataType: 'json',
    cache: false,
    success: function (data) {
        isdays = data['islbel'];
        isrefarr = data['isdat1'];
        isearnarr = data['isdat2'];

        var myChart2 = new Chart(statistics_chart2, {
            type: 'line',
            data: {
                labels: isdays,
                datasets: [{
                        label: 'Incoming',
                        data: isrefarr,
                        borderWidth: 2,
                        borderColor: '#3ABAF4',
                        backgroundColor: 'rgba(58, 186, 244, .3)',
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3ABAF4',
                        pointRadius: 3,
                        yAxisID: 'y-axis-1'
                    }, {
                        label: 'Withdraw',
                        data: isearnarr,
                        borderWidth: 2,
                        borderColor: '#47C363',
                        backgroundColor: 'rgba(71, 195, 99, .3)',
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#47C363',
                        pointRadius: 3,
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
                    text: 'Incoming & Withdraw'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, .8)',
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
                                stepSize: 250
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false,
                            },
                            position: 'left',
                            id: 'y-axis-1',
                        }, {
                            ticks: {
                                stepSize: 100
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

var statistics_chart3 = document.getElementById("myChart3").getContext('2d');

$.ajax({
    type: "GET",
    url: 'getdatachart.php',
    data: {dchart: '3'},
    dataType: 'json',
    cache: false,
    success: function (data) {
        isdays = data['islbel'];
        isrefarr = data['isdat1'];

        var myChart3 = new Chart(statistics_chart3, {
            type: 'doughnut',
            data: {
                labels: isdays,
                datasets: [{
                        label: 'Country',
                        data: isrefarr,
                        backgroundColor: [
                            '#C1C8F9',
                            '#F2C1F9',
                            '#C1F9D6',
                            '#F9F2C1',
                            '#C8F9C1',
                            '#F9C1C8',
                            '#C1F9F2',
                            '#D6C1F9',
                            '#C1E4F9',
                        ],
                        borderWidth: 2,
                        borderColor: '#3ABAF4',
                    }]
            },
            options: {
                responsive: true,
                legend: {
                    display: false,
                    position: 'bottom',
                },
                title: {
                    display: true,
                    position: 'top',
                    text: 'Member by Country'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, .8)',
                    borderColor: 'rgba(103, 119, 239, .7)',
                    borderWidth: 2,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });

    }
});
