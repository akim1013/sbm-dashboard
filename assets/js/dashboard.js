
$(document).ready(function(){
    var shops               = [];
    var grossale            = [];
    var netsale             = [];
    var realsale            = [];
    var vat                 = [];
    var tax                 = [];
    var promotion           = [];
    var discount            = [];
    var transaction_count   = [];
    var tip                 = [];
    var average_bill        = [];
    // Total values
    var _grossale            = 0;
    var _netsale             = 0;
    var _realsale            = 0;
    var _vat                 = 0;
    var _tax                 = 0;
    var _promotion           = 0;
    var _discount            = 0;
    var _transaction_count   = 0;
    var _tip                 = 0;
    var _average_bill        = 0;
    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    function find_shop_name(id){
        for(var item of shops){
            if(id == item.id) return item.name;
        }
    }
    function add_shop_list(shop_lists){
        $('#all-shops').empty();
        shops = [];
        $('#all-shops').append($('<li>').append($('<a class="">Overall view</a>')));
        for(var shop of shop_lists){
            shops.push({
                id: shop.id,
                name: shop.description,
                value: shop.description
            });
            $('#all-shops').append($('<li>').append($('<a class="" shopId=' + shop.id + '>' + shop.description + '</a>')));
        }
    }
    function flat_process(data){
        grossale            = [];
        netsale             = [];
        realsale            = [];
        vat                 = [];
        tax                 = [];
        promotion           = [];
        discount            = [];
        transaction_count   = [];
        tip                 = [];
        average_bill        = [];
        _grossale            = 0;
        _netsale             = 0;
        _realsale            = 0;
        _vat                 = 0;
        _tax                 = 0;
        _promotion           = 0;
        _discount            = 0;
        _transaction_count   = 0;
        _tip                 = 0;
        _average_bill        = 0;
        for(let sale of data.sale){
            grossale.push({
                shop: find_shop_name(sale.shop_id),
                value: sale.grossale
            });
            netsale.push({
                shop: find_shop_name(sale.shop_id),
                value: sale.netsale
            });
            realsale.push({
                shop: find_shop_name(sale.shop_id),
                value: sale.realsale
            });
            tax.push({
                shop: find_shop_name(sale.shop_id),
                value: sale.tax
            });
            vat.push({
                shop: find_shop_name(sale.shop_id),
                value: sale.vat
            });
            discount.push({
                shop: find_shop_name(sale.shop_id),
                value: sale.discount
            });
        }
        for(let prom of data.promotion){
            promotion.push({
                shop: find_shop_name(prom.shop_id),
                value: prom.promotion
            });
        }
        for(let ti of data.tip){
            tip.push({
                shop: find_shop_name(ti.shop_id),
                value: ti.tip
            });
        }
        for(let transaction of data.transaction){
            transaction_count.push({
                shop: find_shop_name(transaction.shop_id),
                value: transaction.transaction_count
            });
            average_bill.push({
                shop: find_shop_name(transaction.shop_id),
                value: transaction.average_bill,
                tc: transaction.transaction_count
            });
        }
        for(let item of grossale){
            _grossale += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of netsale){
            _netsale += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of realsale){
            _realsale += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of discount){
            _discount += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of vat){
            _vat += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of tax){
            _tax += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of tip){
            _tip += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of promotion){
            _promotion += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of transaction_count){
            _transaction_count += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of average_bill){
            _average_bill += item.value ? parseFloat(item.value) * parseFloat(item.tc) : 0;
        }
        _average_bill = _average_bill / _transaction_count;
        display_flat_data();
    }
    function display_flat_data(){
        $("._grossale").text(process_price(_grossale));
        $("._netsale").text(process_price(_netsale));
        $("._realsale").text(process_price(_realsale));
        $("._vat").text(process_price(_vat));
        $("._tax").text(process_price(_tax));
        $("._discount").text(process_price(_discount));
        $("._average_bill").text(process_price(_average_bill));
        $("._transaction_count").text(_transaction_count);
        $("._tip").text(process_price(_tip));
        $("._promotion").text(process_price(_promotion));
    }
    function process_price(val){

        let value = parseFloat(val);
        if(Math.abs(value) > 1000){
            return (value / 1000).toFixed(2) + ' k';
        }else{
            return value.toFixed(2);
        }
    }
    function process_percent(val, ref){
        let ret = [];
        for(let item of val){
            ret.push({
                name: item.shop,
                y: item.value / ref * 100,
                v: item.value
            });
        }
        return ret;
    }
    function process_one_value(val, ref){
        let ret = [];
        for(let item of val){
            if(ref == 1){
                ret.push(parseFloat(item.value));
            }else{
                ret.push(item.value);
            }
        }
        return ret;
    }
    function comparison_chart_process(){
        Highcharts.chart('sale_comparison_pie', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Gros sale comparison of the shops'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.v:.1f}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.v:.1f}',
                        connectorColor: 'silver'
                    }
                }
            },
            series: [{
                name: 'Share',
                data: process_percent(grossale, _grossale)
            }]
        });
        Highcharts.chart('transaction_comparison_pie', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Transaction count comparison of the shops'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.v}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.v}',
                        connectorColor: 'silver'
                    }
                }
            },
            series: [{
                name: 'Share',
                data: process_percent(transaction_count, _transaction_count)
            }]
        });
        Highcharts.chart('sale_comparison_bar', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Sales comparison'
            },
            xAxis: {
                categories: process_one_value(shops, 0),
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Sales [USD]'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Gros sale',
                data: process_one_value(grossale, 1)
            }, {
                name: 'Net sale',
                data: process_one_value(netsale, 1)
            }, {
                name: 'Real sale',
                data: process_one_value(realsale, 1)
            }, {
                name: 'VAT',
                data: process_one_value(vat, 1)
            }, {
                name: 'Tax',
                data: process_one_value(tax, 1)
            }]
        });
        Highcharts.chart('weekly_growth_line', {

            title: {
                text: 'Weekly growth'
            },

            subtitle: {
                text: 'Total sales growth in last 7 days'
            },

            yAxis: {
                title: {
                    text: 'Sales [USD]'
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    pointStart: 2010
                }
            },

            series: [{
                name: 'Installation',
                data: [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
            }, {
                name: 'Manufacturing',
                data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
            }, {
                name: 'Sales & Distribution',
                data: [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
            }, {
                name: 'Project Development',
                data: [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
            }, {
                name: 'Other',
                data: [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
            }],

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
        Highcharts.chart('monthly_growth_line', {

            title: {
                text: 'Weekly growth'
            },

            subtitle: {
                text: 'Total sales growth in last 7 days'
            },

            yAxis: {
                title: {
                    text: 'Sales [USD]'
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    pointStart: 2010
                }
            },

            series: [{
                name: 'Installation',
                data: [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
            }, {
                name: 'Manufacturing',
                data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
            }, {
                name: 'Sales & Distribution',
                data: [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
            }, {
                name: 'Project Development',
                data: [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
            }, {
                name: 'Other',
                data: [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
            }],

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
        Highcharts.chart('yearly_growth_line', {

            title: {
                text: 'Weekly growth'
            },

            subtitle: {
                text: 'Total sales growth in last 7 days'
            },

            yAxis: {
                title: {
                    text: 'Sales [USD]'
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    pointStart: 2010
                }
            },

            series: [{
                name: 'Installation',
                data: [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
            }, {
                name: 'Manufacturing',
                data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
            }, {
                name: 'Sales & Distribution',
                data: [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
            }, {
                name: 'Project Development',
                data: [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
            }, {
                name: 'Other',
                data: [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
            }],

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
    function get_dashboard_data(start, end){
        $('.loader').removeClass('hide');
        var date = {
            start: start.format('YYYY-MM-DD'),
            end: end.format('YYYY-MM-DD')
        }
        $.ajax({
            url: '/home/dashboard',
            method: 'post',
            data: date,
            success: function(res){
                $('.loader').addClass('hide');
                var response = JSON.parse(res);
                console.log(response)
                if(response.status == 'success'){
                    add_shop_list(response.data.shops);
                    flat_process(response.data);
                    comparison_chart_process();
                    get_daily_data(start, end);
                }
            }
        });
    }
    function get_daily_data(start, end){
        var date = {
            start: start.format('YYYY-MM-DD'),
            end: end.format('YYYY-MM-DD')
        }
        $.ajax({
            url: '/home/daily',
            method: 'post',
            data: date,
            success: function(res){
                var response = JSON.parse(res);
                console.log(response);
            }
        });
    }
    // Date Range Change
    var start = moment().subtract(2, 'days');
    var end = moment().subtract(2, 'days');

    function date_range_set(start, end) {
        if($("#sale_comparison_pie").highcharts()){
            $("#sale_comparison_pie").highcharts().destroy();
        }
        if($("#transaction_comparison_pie").highcharts()){
            $("#transaction_comparison_pie").highcharts().destroy();
        }
        if($("#sale_comparison_bar").highcharts()){
            $("#sale_comparison_bar").highcharts().destroy();
        }
        $('#reportrange span').html(start.format('M.D.Y') + ' ~ ' + end.format('M.D.Y'));
        get_dashboard_data(start, end);
    }
    date_range_set(start, end);

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'This Year': [moment().startOf('year'), moment().endOf('year')],
           'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        }
    }, date_range_set);
    // Logout User
    $('.logout').click(function(e){
        $('.loader').removeClass('hide');
        e.stopPropagation();
        e.preventDefault();
        $.ajax({
            url: '/auth/logout',
            method: 'post',
            success: function(res){
                $('.loader').addClass('hide');
                window.location.assign('/');
            }
        });
    })
})
