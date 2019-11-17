let api_path = '/';
let weeks = ['First week', 'Second week', 'Third week', 'Forth week', 'Fifth week'];
let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$(document).ready(() => {
    let isMobile = false;

    $('.shop-name').text('All shops');
    let storage_all_shop = '';
    // Date Range Change
    let start = moment().subtract(1, 'days');
    let end = moment().subtract(1, 'days');
    let _shop_name          = [];
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

    let daily_turnover      = []; // Yesterday, 2 days ago, last week, this week, last 7 days || 15 days of turnover data groupby day
    let weekly_turnover     = []; // Weekly turnover for this month and last month
    let monthly_turnover    = []; // Monthly turnover for this year
    let yearly_turnover     = []; // Yearly turnover


    let sorted_netsale      = []; // For storing sorted netsale value

    let first_ajax;
    let second_ajax;

    if(window.innerWidth < 575){
        isMobile = true;
    }else{
        isMobile = false;
    }
    let dateFromDay = (year, day) => {
        let date = new Date(year, 0); // initialize a date in `year-01-01`
        return new Date(date.setDate(day)); // add the number of days
    }
    // Generate random color
    let getRandomColor = () => {
        let letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    let show_comparison_charts = () => {
        $('#comparison_bar').removeClass('hide');
        $('#comparison_none').addClass('hide');
    }
    let hide_comparison_charts = () => {
        $('#comparison_bar').addClass('hide');
        $('#comparison_none').removeClass('hide');
        $('#comparison_none h3').text('There are no transactions from ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    }

    let show_monthly_bar = () => {
        $('#monthly_sale_bar').removeClass('hide');
        $('#monthly_transaction_bar').removeClass('hide');
    }

    let hide_detail_charts = () => {
        if(!$('#transaction_detail').hasClass('hide')){
            $('#transaction_detail').addClass('hide');
        }
        if(!$('#sale_detail').hasClass('hide')){
            $('#sale_detail').addClass('hide');
        }
        if($("#transaction_detail_line").highcharts()){
            $("#transaction_detail_line").highcharts().destroy();
        }
        if($("#sale_detail_line").highcharts()){
            $("#sale_detail_line").highcharts().destroy();
        }
        if($("#payment_detail_line").highcharts()){
            $("#payment_detail_line").highcharts().destroy();
        }
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
        storage_all_shop = localStorage.getItem('shop_name');
        localStorage.setItem('_shop_name', localStorage.getItem('shop_name'));
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
    function display_flat_data_single(){

        let __netsale = 0;
        let __discount = 0;
        let __average_bill = 0;
        let __transaction_count = 0;
        let __tip = 0;
        let __promotion = 0;
        for(let item of netsale){
            for(let name of _shop_name){
                if(item.shop == name){
                    __netsale += parseFloat(item.value ? item.value : 0);
                }
            }
        }
        for(let item of discount){
            for(let name of _shop_name){
                if(item.shop == name){
                    __discount += parseFloat(item.value ? item.value : 0);
                }
            }
        }
        for(let item of average_bill){
            //if(item.shop == name) __average_bill = item.value;
        }
        for(let item of transaction_count){
            for(let name of _shop_name){
                if(item.shop == name){
                    __transaction_count += parseFloat(item.value ? item.value : 0);
                }
            }
        }
        for(let item of tip){
            for(let name of _shop_name){
                if(item.shop == name){
                    __tip += parseFloat(item.value ? item.value : 0);
                }
            }
        }
        for(let item of promotion){
            for(let name of _shop_name){
                if(item.shop == name){
                    __promotion += parseFloat(item.value ? item.value : 0);
                }
            }
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
        if(Math.abs(value) > 1000 * 1000){
            return (value / 1000).toFixed(2) + ' m';
        }else if(Math.abs(value) > 1000){
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
    let comparison_chart_process = () => {
        if((netsale.length == 0) && (transaction_count.length == 0)){
            hide_comparison_charts();
        }else{
            show_comparison_charts();
            let _sorted_netsale = [ ...netsale];
            for(let item of _sorted_netsale){
                for(let _item of transaction_count){
                    if(item.shop == _item.shop){
                        item.transaction = _item.value;
                    }
                }
            }
            sorted_netsale = [..._sorted_netsale];

            let temp_shop = [];
            let temp_turnover = [];
            let temp_transaction = [];

            for(let item of sorted_netsale){
                temp_shop.push(item.shop);
                temp_turnover.push(parseFloat(item.value));
                temp_transaction.push(item.transaction);
            }
            sale_comparison_reverse();
            sale_comparison_table(sorted_netsale);



            let turnover_ranks = getRanks(temp_turnover);
            let transaction_ranks = getRanks(temp_transaction);
            Highcharts.chart('sale_comparison_bar', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Turnover and transaction comparison by shops'
                },
                xAxis: {
                    categories: temp_shop, // Error, Distorted shop names
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
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
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
                                if(this.colorIndex == 0){
                                    return turnover_ranks[this.point.index]
                                }else{
                                    return transaction_ranks[this.point.index]
                                }
                            }
                        }
                    }
                },
                series: [{
                    name: 'Turnover',
                    data: temp_turnover
                },{
                    name: 'Transaction',
                    data: temp_transaction
                }]
            });
        }
    }
    let sale_comparison_reverse = () => {
        if(parseFloat(sorted_netsale[0].value) < parseFloat(sorted_netsale[1].value)){
            sorted_netsale.sort((a,b) => (parseFloat(a.value) > parseFloat(b.value)) ? -1 : ((parseFloat(b.value) > parseFloat(a.value)) ? 1 : 0));
        }else{
            sorted_netsale.sort((a,b) => (parseFloat(a.value) > parseFloat(b.value)) ? 1 : ((parseFloat(b.value) > parseFloat(a.value)) ? -1 : 0));
        }
        sale_comparison_table(sorted_netsale);
    }
    let transaction_comparison_reverse = () => {
        if(parseInt(sorted_netsale[0].transaction) < parseInt(sorted_netsale[1].transaction)){
            sorted_netsale.sort((a,b) => (parseInt(a.transaction) > parseInt(b.transaction)) ? -1 : ((parseInt(b.transaction) > parseInt(a.transaction)) ? 1 : 0));
        }else{
            sorted_netsale.sort((a,b) => (parseInt(a.transaction) > parseInt(b.transaction)) ? 1 : ((parseInt(b.transaction) > parseInt(a.transaction)) ? -1 : 0));
        }
        sale_comparison_table(sorted_netsale);
    }
    let sale_comparison_table = (val) => {
        let idx = 0;
        $('.sale_comparison_table').empty();
        for(let item of val){
            idx ++;
            let tr;
            if(idx <= 3){
                tr = $('<tr class="top3"></tr>');
            }else if(idx > 3 && idx <=5 ){
                tr = $('<tr class="top5"></tr>');
            }else{
                tr = $('<tr></tr>');
            }
            tr.append($('<td>' + idx + '</td>'));
            tr.append($('<td>' + item.shop + '</td>'));
            tr.append($('<td>' + item.value + '</td>'));
            tr.append($('<td>' + item.transaction + '</td>'));
            $('.sale_comparison_table').append(tr);
            if(idx == 10) break;
        }
    }
    function monthly_growth_process(data, id){
        if(data.length != 0){
            show_monthly_bar();
            let x_sale              = []; // x Axis date
            let s_sale              = []; // Shop names
            let series_sale         = []; // Series of data

            let x_transaction       = [];
            let s_transaction       = [];
            let series_transaction  = [];

            _shop_id = [];

            for(let item of _shop_name){
                _shop_id.push(find_shop_id(item));
            }
            if($("#monthly_growth_line").highcharts()){
                $("#monthly_growth_line").highcharts().destroy();
            }
            if($("#monthly_transaction_line").highcharts()){
                $("#monthly_transaction_line").highcharts().destroy();
            }
            x_sale = [];
            s_sale = [];
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
                    for(let _id of _shop_id){
                        if(s_sale.indexOf(find_shop_name(_id)) < 0){
                            s_sale.push(find_shop_name(_id));
                        }
                    }
                }
            }
            x_sale.sort((a, b) => {
                return new Date(a) - new Date(b)
            });
            x_transaction = [];
            s_transaction = [];
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
                    for(let _id of _shop_id){
                        if(s_transaction.indexOf(find_shop_name(_id)) < 0){
                            s_transaction.push(find_shop_name(_id));
                        }
                    }
                }
            }
            x_transaction.sort((a, b) => {
                return new Date(a) - new Date(b)
            });
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
                    text: 'Turnover comparison in last 30 days'
                },
                xAxis: {
                    categories: x_sale
                },
                yAxis: {
                    title: {
                        text: 'USD'
                    }
                },
                legend: {
                    enabled: !isMobile
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
                    text: 'Transaction count comparison in last 30 days'
                },
                xAxis: {
                    categories: x_transaction
                },
                yAxis: {
                    title: {
                        text: 'Transaction count'
                    }
                },
                legend: {
                    enabled: !isMobile
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
    }
    function get_dashboard_data(start, end){
        $('.loader').removeClass('hide');
        let data = {
            start: start.format('YYYY-MM-DD'),
            end: end.format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('shop_name')
        }
        $.ajax({
            url: api_path + 'home/dashboard',
            method: 'post',
            data: data,
            success: function(res){
                $('.loader').addClass('hide');
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    add_shop_list(response.data);
                    flat_process(response.data);
                    comparison_chart_process();
                    get_daily_turnover();
                    //get_daily_data(end);
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
            url: api_path + 'home/daily',
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
    /////////////////////////////////////////////////////////////////////////
    let get_daily_turnover = () => {
        let data = {
            start: moment().subtract(7, 'days').format('YYYY-MM-DD'),
            end: moment().format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('shop_name')
        }
        $.ajax({
            url: api_path + 'home/daily_turnover',
            method: 'post',
            data: data,
            success: function(res){
                get_weekly_turnover();
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    let d_data = response.data.daily_turnover;
                    let d_today     = [];
                    let d_yesterday = [];
                    let d_last_7    = [];
                    let d_w_7       = []; // Week
                    for(let item of d_data){
                        d_last_7.push(parseFloat(item.netsale));
                        d_w_7.push(moment(dateFromDay(moment().format('Y'), item.d)).format('ddd'));
                    }

                    d_yesterday.push(parseFloat(d_data[d_data.length - 2].netsale));
                    d_today.push(parseFloat(d_data[d_data.length - 1].netsale));

                    let percent = ((d_today[0] - d_yesterday[0]) / d_yesterday[0]) * 100;
                    $('.yt_val').text(process_price(d_yesterday[0]));
                    $('.t_val').text(process_price(d_today[0]));
                    $('.t_growth_percent').text((percent > 0) ? '+' + percent.toFixed(2) + ' %' : percent.toFixed(2) + ' %');
                    $('#turnover_detail').removeClass('hide');
                    Highcharts.chart('yt_comparison', {
                        chart: {
                            height: 200,
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: {
                            categories: [
                                ''
                            ],
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: ''
                            }
                        },
                        legend: {
                            enabled: true,
                            itemStyle: {
                                'fontSize': '10px'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="font-size:10px; color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0;font-size:10px;"><b>{point.y:.1f} $</b></td></tr>',
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
                            name: 'Yesterday',
                            data: d_yesterday

                        }, {
                            name: 'Today',
                            data: d_today

                        }]
                    });
                    Highcharts.chart('w_comparison', {
                        chart: {
                            height: 200,
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        subtitle: {
                            text: 'Last 7 days'
                        },
                        xAxis: {
                            categories: d_w_7,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: ''
                            }
                        },
                        legend: {
                            enabled: false,
                            itemStyle: {
                                'fontSize': '10px'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="font-size:10px; color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0;font-size:10px;"><b>{point.y:.1f} $</b></td></tr>',
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
                            name: 'Turnover',
                            data: d_last_7
                        }]
                    });
                }
            }
        });
    }
    let get_weekly_turnover = () => {
        let data = {
            start: (new Date().getFullYear().toString() + '-' + (new Date().getMonth() + 1).toString() + '-01'),
            end: moment().format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('shop_name')
        }
        $.ajax({
            url: api_path + 'home/weekly_turnover',
            method: 'post',
            data: data,
            success: function(res){
                get_monthly_turnover();
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    $('#turnover_detail').removeClass('hide');
                    let w_data = response.data.weekly_turnover;
                    let w_days = [];
                    let w_sale = [];
                    let idx = 0;
                    for(let item of w_data){
                        w_days.push(weeks[idx]);
                        w_sale.push(parseFloat(item.netsale));
                        idx ++;
                    }
                    for(let i = 0; i < 5 - w_data.length; i++){
                        w_days.push(weeks[idx + i]);
                        w_sale.push(0);
                    }
                    Highcharts.chart('wl_comparison', {
                        chart: {
                            height: 200,
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        subtitle: {
                            text: 'This month\'s turnover'
                        },
                        xAxis: {
                            categories: w_days,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: ''
                            }
                        },
                        legend: {
                            enabled: false,
                            itemStyle: {
                                'fontSize': '10px'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="font-size:10px; color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0;font-size:10px;"><b>{point.y:.1f} $</b></td></tr>',
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
                            name: 'Turnover',
                            data: w_sale
                        }]
                    });
                }
            }
        });
    }
    let get_monthly_turnover = () => {
        let data = {
            start: (new Date().getFullYear().toString() + '-01-01'),
            end: moment().format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('shop_name')
        }
        $.ajax({
            url: api_path + 'home/monthly_turnover',
            method: 'post',
            data: data,
            success: function(res){
                //get_yearly_turnover();
                get_daily_data(end)
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    $('#turnover_detail').removeClass('hide');
                    let m_data = response.data.monthly_turnover;
                    let m_label = [];
                    let m_sale = [];
                    let idx = 0;
                    for(let item of m_data){
                        m_label.push(months[idx]);
                        m_sale.push(parseFloat(item.netsale));
                        idx ++;
                    }
                    for(let i = 0; i < 12 - m_data.length; i++){
                        m_label.push(months[idx + i]);
                        m_sale.push(0);
                    }
                    Highcharts.chart('m_comparison', {
                        chart: {
                            height: 200,
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        subtitle: {
                            text: 'This year\'s turnover'
                        },
                        xAxis: {
                            categories: m_label,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: ''
                            }
                        },
                        legend: {
                            enabled: false,
                            itemStyle: {
                                'fontSize': '10px'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="font-size:10px; color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0;font-size:10px;"><b>{point.y:.1f} $</b></td></tr>',
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
                            name: 'Turnover',
                            data: m_sale
                        }]
                    });
                }
            }
        });
    }
    let get_yearly_turnover = () => {
        let data = {
            shop_name: localStorage.getItem('shop_name')
        }
        $.ajax({
            url: api_path + 'home/yearly_turnover',
            method: 'post',
            data: data,
            success: function(res){
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    $('#turnover_detail').removeClass('hide');

                }
            }
        });
    }

    function date_range_set(st, ed) {
        start = st;
        end = ed;
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
            url: api_path + 'auth/logout',
            method: 'post',
            success: function(res){
                $('.loader').addClass('hide');
                window.location.assign(api_path);
            }
        });
    })

    // Details section
    $('._netsale').click(() => {
        if($('#sale_detail').hasClass('hide')){
            let data = {
                start: start.format('YYYY-MM-DD'),
                end: end.format('YYYY-MM-DD'),
                shop_name: localStorage.getItem('_shop_name')
            }
            $('.loader').removeClass('hide');
            // Payment detail
            $.ajax({
                url: api_path + 'home/payment',
                method: 'post',
                data: data,
                success: function(res){
                    let response = JSON.parse(res);
                    let p_description = []; // payment description
                    let p_amount = []; // amount of money
                    let p_detail = [];
                    for(let item of response.data.payment_detail){
                        p_description.push(item.pd);
                        p_amount.push(parseFloat(item.amount));
                        p_detail.push({
                            name: item.pd,
                            value: parseFloat(item.amount)
                        })
                    }

                    // Draw pie chart for payment details
                    Highcharts.chart('payment_detail_line', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Payment details'
                        },
                        xAxis: {
                            categories: p_description,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Price [USD]'
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
                        series: [{
                            name: 'Payment type',
                            data: p_amount,
                            dataLabels: {
                                enabled: true,
                                color: '#FFFFFF',
                                align: 'center',
                                format: '{point.y:.1f}', // one decimal
                                style: {
                                    fontSize: '7px',
                                    fontFamily: 'Verdana, sans-serif'
                                }
                            }
                        }]
                    });
                }
            })
            // Sale detail
            $.ajax({
                url: api_path + 'home/sale_detail',
                method: 'post',
                data: data,
                success: function(res){
                    $('#sale_detail').removeClass('hide');
                    $('.loader').addClass('hide');
                    let response = JSON.parse(res);
                    let d_price = [];
                    let d_group = [];
                    for(let item of response.data.sale_detail){
                        d_price.push(parseFloat(item.price));
                        d_group.push(item.group_name);
                    }
                    Highcharts.chart('sale_detail_line', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Turnover details by items'
                        },
                        xAxis: {
                            categories: d_group,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Price [USD]'
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
                        series: [{
                            name: 'Product',
                            data: d_price,
                            dataLabels: {
                                enabled: true,
                                color: '#FFFFFF',
                                align: 'center',
                                format: '{point.y:.1f}', // one decimal
                                style: {
                                    fontSize: '7px',
                                    fontFamily: 'Verdana, sans-serif'
                                }
                            }
                        }]
                    });
                }
            });
        }else{
            $('#sale_detail').addClass('hide')
        }
    })
    $('._transaction_count').click(() => {
        if($('#transaction_detail').hasClass('hide')){
            let data = {
                start: end.format('YYYY-MM-DD'),
                end: end.format('YYYY-MM-DD'),
                shop_name: localStorage.getItem('_shop_name')
            }
            $('.loader').removeClass('hide');
            $.ajax({
                url: api_path + 'home/transaction_detail',
                method: 'post',
                data: data,
                success: function(res){
                    $('.loader').addClass('hide');
                    $('#transaction_detail').removeClass('hide');
                    let response = JSON.parse(res);
                    let d_hours = [];
                    let d_count = [];
                    for(let i = 0; i < 24; i++){
                        d_hours.push(i);
                        let flag = false;
                        for(let item of response.data.transaction_detail){
                            if(item.h == i){
                                d_count.push(item.transaction_count);
                                flag = true;
                            }
                        }
                        if(!flag){
                            d_count.push(0);
                        }
                    }
                    Highcharts.chart('transaction_detail_line', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Transaction count detail by hours'
                        },
                        xAxis: {
                            categories: d_hours,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Number of transactions'
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
                        series: [{
                            name: 'Transaction count',
                            data: d_count,
                            dataLabels: {
                                enabled: true,
                                color: '#FFFFFF',
                                align: 'center',
                                style: {
                                    fontSize: '9px',
                                    fontFamily: 'Verdana, sans-serif'
                                }
                            }
                        }]
                    });
                }
            });
        }else{
            $('#transaction_detail').addClass('hide')
        }
    })
    //sale_comparison_reverse
    $('.turnover_comparison_sort').click(function(){
        sale_comparison_reverse();
    })
    $('.transaction_comparison_sort').click(function(){
        transaction_comparison_reverse();
    })
    $('#all-shops').delegate('.single-shop', 'click', function(){

        hide_detail_charts();
        let shop_id = $(this)[0].getAttribute('shopId');
        if(shop_id != 0){
            $("#comparison_bar").hide();

            if($(this).hasClass('selected')){
                $(this).removeClass('selected');
                _shop_name = _shop_name.filter((item) => {
                    return item != find_shop_name(shop_id);
                })
            }else{
                if(_shop_name.length > 2){
                    // Give a alert that you can select only 3 distinct shops at a time
                    //_shop_name.push(find_shop_name(shop_id));
                    _shop_name = [];
                    $('.single-shop').removeClass('selected');
                }
                $(this).addClass('selected');
                _shop_name.push(find_shop_name(shop_id));
            }
            if(_shop_name.length == 0){
                $('.single-shop').removeClass('selected');
                _shop_name = JSON.parse(storage_all_shop);
                $('.shop-name').text('All shops');
                if((netsale.length != 0) && (transaction_count.length != 0)){
                    show_comparison_charts();
                }
                monthly_growth_process(second_ajax.data, 0);
                $("#comparison_bar").show();
            }else{
                $('.shop-name').text(_shop_name.toString());
                monthly_growth_process(second_ajax.data, -1);
            }
            localStorage.setItem('_shop_name', JSON.stringify(_shop_name));
        }else{
            $('.single-shop').removeClass('selected');
            _shop_name = JSON.parse(storage_all_shop);
            $('.shop-name').text('All shops');
            localStorage.setItem('_shop_name', JSON.stringify(_shop_name));
            if((netsale.length != 0) && (transaction_count.length != 0)){
                show_comparison_charts();
            }
            display_flat_data();
            monthly_growth_process(second_ajax.data, 0);
        }
    })
    $('#refresh').click(function(){
        window.location.reload();
    })
    $('#overall_view').click(function(){
        $('.page-dashboard').removeClass('hide');
        $('.page-comparison').addClass('hide');
        $('.list-unstyled li').removeClass('active');
        $(this).parent().addClass('active');
    })
    $('#detail_comparison').click(function(){
        $('.page-dashboard').addClass('hide');
        $('.page-comparison').removeClass('hide');
        $('.list-unstyled li').removeClass('active');
        $(this).parent().addClass('active');
        let data = {
            start: moment().startOf('week').format('YYYY-MM-DD'),
            end: moment().format('YYYY-MM-DD'),
            last_start: moment().subtract(7, 'days').startOf('week').format('YYYY-MM-DD'),
            last_end: moment().subtract(7, 'days').endOf('week').format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('_shop_name')
        }
        $('.end_date').text(moment().startOf('week').format('MMM DD, YYYY') + ' ~ ' + moment().format('MMM DD, YYYY'));
        $('.start_date').text(moment().subtract(7, 'days').startOf('week').format('MMM DD, YYYY') + ' ~ ' + moment().subtract(7, 'days').endOf('week').format('MMM DD, YYYY'));
        let table = $('#comparison_detail table tbody');
        table.empty();
        $.ajax({
            url: api_path + 'home/get_comparison_detailed_data',
            method: 'post',
            data: data,
            success: function(res){
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    $('.export-data').removeClass('hide');
                    let article_detail = response.data.article_detail;
                    let discount_detail = response.data.discount_detail;
                    let payment_detail = response.data.payment_detail;
                    // Article detail
                    table.append('<tr class="title"><td colspan="9">Sales<td><tr>');
                    let article_group = '';
                    let article_group_array = [];
                    let price = 0;
                    let qty = 0;
                    let last_week_price = 0;
                    let last_week_qty = 0;
                    let group_price = 0;
                    for(let i = 0; i < article_detail.length; i++){
                        if((article_detail[i].amount == 0) && (article_detail[i].last_week_amount == 0)){
                            continue;
                        }
                        price += parseFloat(article_detail[i].price);
                        qty += (article_detail[i].amount);
                        group_price += parseFloat(article_detail[i].price);
                        last_week_price += parseFloat(article_detail[i].last_week_price);
                        last_week_qty += (article_detail[i].last_week_amount);
                        if(article_group != article_detail[i].group_name){
                            article_group = article_detail[i].group_name;
                            article_group_array.push({
                                group_name: article_group,
                            });
                        }
                    }
                    for(let item of article_group_array){
                        let group_price = 0;
                        let last_group_price = 0;
                        for(let _item of article_detail){
                            if(item.group_name == _item.group_name){
                                group_price += parseFloat(_item.price);
                                last_group_price += parseFloat(_item.last_week_price);
                            }
                        }
                        item.group_price = group_price;
                        item.last_group_price = last_group_price;
                    }
                    for(let item of article_group_array){
                        let group_qty = 0;
                        let group_amount = 0;
                        let group_percent = 0;
                        let last_week_group_qty = 0;
                        let last_week_group_amount = 0;
                        let last_week_group_percent = 0;
                        table.append('<tr class="sub-title"><td colspan="9">' + item.group_name + '<td><tr>');
                        for(let _item of article_detail){
                            if(item.group_name == _item.group_name){
                                if((_item.amount == 0) && (_item.last_week_amount == 0)){
                                    continue;
                                }
                                group_qty += _item.amount;
                                group_amount += parseFloat(_item.price);
                                group_percent += parseFloat(_item.price) / parseFloat(price) * 100;
                                last_week_group_qty += _item.last_week_amount;
                                last_week_group_amount += parseFloat(_item.last_week_price);
                                last_week_group_percent += parseFloat(_item.last_week_price) / parseFloat(last_week_price) * 100;
                                table.append('<tr><td>' + _item.article_name + '</td><td>' + _item.last_week_amount + '</td><td>' + parseFloat(_item.last_week_price).toFixed(2) + '</td><td>' + (parseFloat(_item.last_week_price) / parseFloat(item.last_group_price) * 100).toFixed(3) + '%' + '</td><td>' + _item.amount + '</td><td>' + parseFloat(_item.price).toFixed(2) + '</td><td>' + (parseFloat(_item.price) / parseFloat(item.group_price) * 100).toFixed(3) + '%' + '</td><td>'
                                + (_item.amount - _item.last_week_amount).toString() + '</td><td>' + (parseFloat(_item.price) - parseFloat(_item.last_week_price)).toFixed(2) + '</td></tr>')
                            }
                        }
                        table.append('<tr class="sub-total"><td></td><td>' + last_week_group_qty + '</td><td>' + last_week_group_amount.toFixed(2) + '</td><td>' + last_week_group_percent.toFixed(3) + '%' + '</td><td>' + group_qty + '</td><td>' + group_amount.toFixed(2) + '</td><td>' + group_percent.toFixed(3) + '%' + '</td><td></td><td></td></tr>');
                    }
                    table.append('<tr class="total"><td>Total sales</td><td>' + last_week_qty.toString() + '</td><td>' + process_price(last_week_price) + '</td><td></td><td>' + qty.toString() + '</td><td>' + process_price(price) + '</td><td></td><td>'
                     + (qty - last_week_qty).toString() + '</td><td>' + process_price(price - last_week_price) + '</td></tr>');

                    // Discount details
                    table.append('<tr class="title"><td colspan="9">Discount<td><tr>');
                    let this_week_discount_qty = 0;
                    let this_week_discount_amount = 0;
                    let last_week_discount_qty = 0;
                    let last_week_discount_amount = 0;
                    for(let item of discount_detail){
                        if((item.this_week_quantity == '0') && (item.last_week_quantity == '0')){
                            continue;
                        }
                        this_week_discount_qty += parseInt(item.this_week_quantity);
                        this_week_discount_amount += parseFloat(item.this_week_amount);
                        last_week_discount_qty += parseInt(item.last_week_quantity);
                        last_week_discount_amount += parseFloat(item.last_week_amount);
                        table.append('<tr><td>' + item.discount_description + '</td><td>' + item.last_week_quantity + '</td><td>' + process_price(item.last_week_amount) + '</td><td></td><td>' + item.this_week_quantity + '</td><td>' + process_price(item.this_week_amount)
                        + '</td><td></td><td>' + (parseInt(item.this_week_quantity) - parseInt(item.last_week_quantity)).toString() + '</td><td>' + process_price(parseFloat(item.this_week_amount) - parseFloat(item.last_week_amount)) + '</td></tr>');
                    }
                    table.append('<tr class="total"><td>Discount total</td><td>' + last_week_discount_qty.toString() + '</td><td>' + process_price(last_week_discount_amount) + '</td><td></td><td>' + this_week_discount_qty.toString() + '</td><td>' + process_price(this_week_discount_amount)
                    + '</td><td></td><td>' + (this_week_discount_qty - last_week_discount_qty).toString() + '</td><td>' + process_price(this_week_discount_amount - last_week_discount_amount) + '</td></tr>')

                    // Payment details
                    table.append('<tr class="title"><td colspan="9">Payment<td><tr>');
                    let this_week_payment_qty = 0;
                    let this_week_payment_amount = 0;
                    let last_week_payment_qty = 0;
                    let last_week_payment_amount = 0;
                    for(let item of payment_detail){
                        if((item.this_week_qty == '0') && (item.last_week_qty == '0')){
                            continue;
                        }
                        this_week_payment_qty += parseInt(item.this_week_qty);
                        this_week_payment_amount += parseFloat(item.this_week_amount);
                        last_week_payment_qty += parseInt(item.last_week_qty);
                        last_week_payment_amount += parseFloat(item.last_week_amount);
                        table.append('<tr><td>' + item.description + '</td><td>' + item.last_week_qty + '</td><td>' + process_price(item.last_week_amount) + '</td><td></td><td>' + item.this_week_qty + '</td><td>' + process_price(item.this_week_amount)
                        + '</td><td></td><td>' + (parseInt(item.this_week_qty) - parseInt(item.last_week_qty)).toString() + '</td><td>' + process_price(parseFloat(item.this_week_amount) - parseFloat(item.last_week_amount)) + '</td></tr>');
                    }
                    table.append('<tr class="total"><td>Payment total</td><td>' + last_week_payment_qty.toString() + '</td><td>' + process_price(last_week_payment_amount) + '</td><td></td><td>' + this_week_payment_qty.toString() + '</td><td>' + process_price(this_week_payment_amount)
                    + '</td><td></td><td>' + (this_week_payment_qty - last_week_payment_qty).toString() + '</td><td>' + process_price(this_week_payment_amount - last_week_payment_amount) + '</td></tr>')
                }
            }
        });
    })
    let downloadCSV = (csv, filename) => {
        var csvFile;
        var downloadLink;

        // CSV file
        csvFile = new Blob([csv], {type: "text/csv"});

        // Download link
        downloadLink = document.createElement("a");

        // File name
        downloadLink.download = filename;

        // Create a link to the file
        downloadLink.href = window.URL.createObjectURL(csvFile);

        // Hide download link
        downloadLink.style.display = "none";

        // Add the link to DOM
        document.body.appendChild(downloadLink);

        // Click download link
        downloadLink.click();
    }
    $('#export_csv').click(function(){
        let filename = + new Date() + '.csv';
        let csv = [];
        let rows = document.querySelectorAll("#detail_comparison_table tr");

        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");

            for (let j = 0; j < cols.length; j++)
                row.push(cols[j].innerText);

            csv.push(row.join(","));
        }
        // Download CSV file
        downloadCSV(csv.join("\n"), filename);
    })
    $("#export_xls").click(function(e){
        var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
        var textRange; var j=0;
        tab = document.getElementById('detail_comparison_table'); // id of table

        for(j = 0 ; j < tab.rows.length ; j++)
        {
            tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
        }
        tab_text=tab_text+"</table>";
        var a = document.createElement('a');
        a.href = 'data:application/vnd.ms-excel,' +  encodeURIComponent(tab_text);
        a.download =  + new Date() + '.xls';
        a.click();
        e.preventDefault();
    })
})
