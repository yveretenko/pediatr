let chart, chart_data=[];

$(document).ready(function(){
    $.ajax({
        url: '/admin/appointments/graph_data/'
    }).done(function(data){
        data=JSON.parse(data);

        $.each(data, function(index, value){
            chart_data.push([Date.parse(index), value]);
        });

        $('input[name="group_by"]:checked').trigger('change')
    });

    $('input[name="group_by"]').change(function(){
        let unit_type=$(this).val();
        let limit=$(this).data('limit');

        let sliced_data=chart_data;
        if (limit==='last_year')
        {
            sliced_data=[];

            let last_year = new Date();
            last_year.setFullYear(last_year.getFullYear()-1);

            for (let i=0; i<chart_data.length; i++)
                if (chart_data[i][0]>=last_year.getTime())
                    sliced_data.push(chart_data[i]);
        }

        let options = {
            title: false,
            credits: {
                enabled: false
            },
            chart: {
                renderTo: 'graph',
                type: 'column'
            },
            xAxis: {
                ordinal: true,
                type: 'datetime',
                labels: {
                    align: 'right',
                    rotation: -90
                }
            },
            yAxis: {
                title: false
            },
            tooltip: {
                enabled:false
            },
            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.0f}',
                        color: '#555555'
                    },
                    dataGrouping: {
                        enabled: true,
                        forced: true,
                        units: [
                            [unit_type, [1]]
                        ]
                    }
                }
            },
            series: [{
                gapSize: 0,
                name: 'Записи',
                data: sliced_data,
                color: '#17A2B8'
            }]
        };

        Highcharts.setOptions({
            lang: {
                shortMonths: ['січ', 'лют', 'бер', 'кві', 'тра', 'чер', 'лип', 'сер', 'вер', 'жов', 'лис', 'гру'],
            },
        });

        if (chart)
            chart.destroy();

        chart = new Highcharts.Chart(options);
    });
});