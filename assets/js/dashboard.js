
$(document).ready(() => {
    let shops               = [];   // Shop lists
    let _shops              = [];   // Available shop lists
    let netsale             = [];   // Netsale values of shops
    let promotion           = [];   // Promotion values of shops
    let discount            = [];   // Discount values of shops
    let transaction_count   = [];   // Transaction count values of shops
    let tip                 = [];   // Tip values of shops
    let average_bill        = [];   // Average bill values of shops
    // Total values
    let _netsale             = 0;
    let _realsale            = 0;
    let _promotion           = 0;
    let _discount            = 0;
    let _transaction_count   = 0;
    let _tip                 = 0;
    let _average_bill        = 0;

    let first_ajax;
    let second_ajax;

    // Generate random color
    let getRandomColor = () => {
        let letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    function getRanks(value){
        let sorted = value.slice().sort((a,b) => {return b-a})
        let ranked = value.slice().map((v) => { return sorted.indexOf(v)+1 });
        return ranked;
    }
    function find_shop_name(id){
        for(let item of shops){
            if(id == item.id) return item.name;
        }
    }
    function find_shop_id(name){
        for(let item of shops){
            if(name == item.name) return item.id;
        }
    }
    function add_shop_list(shop_lists){
        $('#all-shops').empty();
        shops   = [];
        _shops  = [];
        $('#all-shops').append($('<li>').append($('<a class="single-shop" style="cursor: pointer" shopId="0">Overall view</a>')));
        for(let shop of shop_lists.shops){
            shops.push({
                id: shop.id,
                name: shop.description,
                value: shop.description
            });
            $('#all-shops').append($('<li>').append($('<a class="single-shop" style="cursor: pointer" shopId=' + shop.id + '>' + (shop.description) + '</a>')));
        }
        for(let shop of shop_lists.sale){
            _shops.push({
                id: shop.shop_id,
                name: find_shop_name(shop.shop_id),
                value: find_shop_name(shop.shop_id)
            });
        }
    }
    function flat_process(data){
        netsale             = [];
        promotion           = [];
        discount            = [];
        transaction_count   = [];
        tip                 = [];
        average_bill        = [];
        _netsale             = 0;
        _promotion           = 0;
        _discount            = 0;
        _transaction_count   = 0;
        _tip                 = 0;
        _average_bill        = 0;
        for(let sale of data.sale){
            netsale.push({
                shop: find_shop_name(sale.shop_id),
                value: sale.netsale
            });
        }
        for(let disc of data.discount){
            discount.push({
                shop: find_shop_name(disc.shop_id),
                value: disc.discount
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
        for(let item of netsale){
            _netsale += item.value ? parseFloat(item.value) : 0;
        }
        for(let item of discount){
            _discount += item.value ? parseFloat(item.value) : 0;
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
        $("._netsale").text(process_price(_netsale));
        $("._discount").text(process_price(_discount));
        $("._average_bill").text(process_price(_average_bill ? _average_bill : 0));
        $("._transaction_count").text(_transaction_count);
        $("._tip").text(process_price(_tip));
        $("._promotion").text(process_price(_promotion));
    }
    function display_flat_data_single(shop){
        let __netsale = 0;
        let __discount = 0;
        let __average_bill = 0;
        let __transaction_count = 0;
        let __tip = 0;
        let __promotion = 0;

        for(let item of netsale){
            if(item.shop == shop) __netsale = item.value;
        }
        for(let item of discount){
            if(item.shop == shop) __discount = item.value;
        }
        for(let item of average_bill){
            if(item.shop == shop) __average_bill = item.value;
        }
        for(let item of transaction_count){
            if(item.shop == shop) __transaction_count = item.value;
        }
        for(let item of tip){
            if(item.shop == shop) __tip = item.value;
        }
        for(let item of promotion){
            if(item.shop == shop) __promotion = item.value;
        }
        $("._netsale").text(process_price(__netsale));
        $("._discount").text(process_price(__discount));
        $("._average_bill").text(process_price(__average_bill));
        $("._transaction_count").text(__transaction_count);
        $("._tip").text(process_price(__tip));
        $("._promotion").text(process_price(__promotion));
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
        let ranks = getRanks(process_one_value(netsale, 1));
        Highcharts.chart('sale_comparison_pie', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Net sale comparison of the shops'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f} %</b>'
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
                name: 'Net sale',
                data: process_percent(netsale, _netsale)
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
                pointFormat: '{series.name}: <b>{point.percentage:.1f} %</b>'
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
                name: 'Transaction count',
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
                categories: process_one_value(_shops, 0), // Error, Distorted shop names
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
                },
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return ranks[this.point.index]
                        }
                    }
                }
            },
            series: [{
                name: 'Net sale',
                data: process_one_value(netsale, 1)
            }]
        });
    }
    function monthly_growth_process(data, id){

        let x_sale              = []; // x Axis date
        let s_sale              = []; // Shop names
        let series_sale         = []; // Series of data

        let x_transaction       = [];
        let s_transaction       = [];
        let series_transaction  = [];
        if($("#monthly_growth_line").highcharts()){
            $("#monthly_growth_line").highcharts().destroy();
        }
        if($("#monthly_transaction_line").highcharts()){
            $("#monthly_transaction_line").highcharts().destroy();
        }
        for(let item of data.daily_sale){
            let date = new Date(item.transaction_date.date);
            let shop = find_shop_name(item.shop_id);
            if(x_sale.indexOf(formatDate(date)) < 0){
                x_sale.push(formatDate(date));
            }
            if(id == 0){
                if(s_sale.indexOf(shop) < 0){
                    s_sale.push(shop);
                }
            }else{
                if(s_sale.indexOf(find_shop_name(id)) < 0){
                    s_sale.push(find_shop_name(id));
                }
            }
        }
        for(let item of data.daily_transaction){
            let date = new Date(item.transaction_date.date);
            let shop = find_shop_name(item.shop_id);
            if(x_transaction.indexOf(formatDate(date)) < 0){
                x_transaction.push(formatDate(date));
            }
            if(id == 0){
                if(s_transaction.indexOf(shop) < 0){
                    s_transaction.push(shop);
                }
            }else{
                if(s_transaction.indexOf(find_shop_name(id)) < 0){
                    s_transaction.push(find_shop_name(id));
                }
            }
        }
        for(let _s of s_sale){
            let _data = [];
            for(let item of data.daily_sale){
                if(_s == find_shop_name(item.shop_id)){
                    _data.push(parseFloat(item.netsale));
                }
            }
            series_sale.push({
                name: _s,
                data: _data
            })
        }
        for(let _s of s_transaction){
            let _data = [];
            for(let item of data.daily_transaction){
                if(_s == find_shop_name(item.shop_id)){
                    _data.push(parseFloat(item.transaction_count));
                }
            }
            series_transaction.push({
                name: _s,
                data: _data
            })
        }
        Highcharts.chart('monthly_growth_line', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Sales growth in last 30 days'
            },
            xAxis: {
                categories: x_sale
            },
            yAxis: {
                title: {
                    text: 'USD'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: false
                    },
                    enableMouseTracking: true
                }
            },
            series: series_sale
        });
        Highcharts.chart('monthly_transaction_line', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Transaction count in last 30 days'
            },
            xAxis: {
                categories: x_transaction
            },
            yAxis: {
                title: {
                    text: 'Transaction count'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: false
                    },
                    enableMouseTracking: true
                }
            },
            series: series_transaction
        });
    }
    function get_dashboard_data(start, end){
        $('.loader').removeClass('hide');
        let date = {
            start: start.format('YYYY-MM-DD'),
            end: end.format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('shop_name')
        }
        $.ajax({
            url: '/home/dashboard',
            method: 'post',
            data: date,
            success: function(res){
                $('.loader').addClass('hide');
                let response = JSON.parse(res);
                console.log(response);
                if(response.status == 'success'){
                    add_shop_list(response.data);
                    flat_process(response.data);
                    comparison_chart_process();
                    get_daily_data(end);
                }
            }
        });
    }
    function formatDate(date) {
        let monthNames = [
            "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"
        ];
        let day = date.getDate();
        let monthIndex = date.getMonth();
        let year = date.getFullYear();

        return year + '-' + monthNames[monthIndex] + '-' + day;
    }
    function get_daily_data(date){
        second_ajax = undefined;
        let monthago = new Date(date.format('YYYY-MM-DD'));
        let past_month = monthago.getDate() - 30;
        monthago.setDate(past_month);
        let month = {
            start: formatDate(monthago),
            end: date.format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('shop_name')
        }
        $.ajax({
            url: '/home/daily',
            method: 'post',
            data: month,
            success: function(res){
                let response = JSON.parse(res);
                second_ajax = response;
                if(response.status == 'success'){
                    monthly_growth_process(response.data, 0);
                }
            }
        });
    }
    // Date Range Change
    let start = moment().subtract(2, 'days');
    let end = moment().subtract(2, 'days');

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
        if($("#monthly_growth_line").highcharts()){
            $("#monthly_growth_line").highcharts().destroy();
        }
        if($("#monthly_transaction_line").highcharts()){
            $("#monthly_transaction_line").highcharts().destroy();
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
        localStorage.setItem('shop_name', 'All');
        $.ajax({
            url: '/auth/logout',
            method: 'post',
            success: function(res){
                $('.loader').addClass('hide');
                window.location.assign('/');
            }
        });
    })
    $('#all-shops').delegate('.single-shop', 'click', function(){
        let shop_id = $(this)[0].getAttribute('shopId');
        if(shop_id != 0){
            $("#comparison_pie").hide();
            $("#comparison_bar").hide();
            display_flat_data_single(find_shop_name(shop_id));
            monthly_growth_process(second_ajax.data, shop_id);
        }else{
            $("#comparison_pie").show();
            $("#comparison_bar").show();
            display_flat_data();
            monthly_growth_process(second_ajax.data, 0);
        }
    })
})
