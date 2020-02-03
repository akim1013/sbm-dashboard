let api_path = '/';
let weeks = ['First week', 'Second week', 'Third week', 'Forth week', 'Fifth week'];
let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

let site_lang = getCookie('sbm_language');
$(document).ready(() => {
    let isMobile = false;

    $('.shop-name').text('All shops');
    let storage_all_shop = '';

    // Date Range Change
    let start = moment().subtract(2, 'days');
    let end = moment().subtract(2, 'days');

    let today = moment();
    let yesterday = moment().subtract(1, 'days');

    let start_of_this_week = moment().startOf('week');
    let end_of_this_week = moment().endOf('week');

    let start_of_last_week = moment().subtract(7, 'days').startOf('week');
    let end_of_last_week = moment().subtract(7, 'days').endOf('week');

    let start_of_this_month = moment().startOf('month');
    let end_of_this_month = moment().endOf('month');

    let start_of_last_month = moment().subtract(1, 'months').startOf('month');
    let end_of_last_month = moment().subtract(1, 'months').endOf('month');

    let start_of_this_year = moment().startOf('year');
    let end_of_this_year = moment().endOf('year');

    let start_of_last_year = moment().subtract(1, 'years').startOf('year');
    let end_of_last_year = moment().subtract(1, 'years').endOf('year');

    const formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    })

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

    let detail_comparison   = [];

    let article_detail      = [];
    let discount_detail      = [];
    let payment_detail      = [];
    let article_group_array = [];

    let sorted_netsale      = []; // For storing sorted netsale value

    let pc_checked          = false; // Present control info checked or not
    let operators           = [];
    let stored_operators    = [];
    let pc_filter           = {};   // Present control filter object
    let operator_rate       = [];
    let shop_operator_data;


    let first_ajax;
    let second_ajax = [];
    let selected = 'ov'; // Selected Overall View else dc, stands for Detail comparison, pc present control
    let localStorage_changed = true;
    let detail_comparison_data = {
        start: start_of_this_week.format('YYYY-MM-DD'),
        end: end_of_this_week.format('YYYY-MM-DD'),
        last_start: start_of_last_week.format('YYYY-MM-DD'),
        last_end: end_of_last_week.format('YYYY-MM-DD'),
        shop_name: localStorage.getItem('_shop_name')
    };
    if(window.innerWidth < 575){
        isMobile = true;
    }else{
        isMobile = false;
    }

    let dateFromDay = (year, day) => {
        let date = new Date(year, 0); // initialize a date in `year-01-01`
        return new Date(date.setDate(day)); // add the number of days
    }
    let getRandomColor = () => {
        let letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    } // Generate random color
    let show_monthly_bar = () => {
        $('#monthly_sale_bar').removeClass('hide');
        $('#monthly_transaction_bar').removeClass('hide');
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

    let getRanks = (value) => {
        let sorted = value.slice().sort((a,b) => {return b-a})
        let ranked = value.slice().map((v) => { return sorted.indexOf(v)+1 });
        return ranked;
    }

    let find_shop_name = (id) => {
        for(let item of shops){
            if(id == item.id) return item.name;
        }
    }
    let find_shop_id = (name) => {
        for(let item of shops){
            if(name == item.name) return item.id;
        }
    }

    let formatDate = (date) => {
        let monthNames = [
            "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"
        ];
        let day = date.getDate();
        let monthIndex = date.getMonth();
        let year = date.getFullYear();

        return year + '-' + monthNames[monthIndex] + '-' + day;
    } // Parse randomly given date value to YYYY-MM-DD format

    let downloadCSV = (csv, filename) => {
        let csvFile;
        let downloadLink;

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

    let add_shop_list = (shop_lists) => {
        storage_all_shop = localStorage.getItem('shop_name');
        localStorage.setItem('_shop_name', localStorage.getItem('shop_name'));
        $('#all-shops').empty();
        $('.payment_shop_list').empty();
        $('.monthly_shop_list').empty();
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
            $('.payment_shop_list').append('<option>' + shop.description + '</option>');
            $('.monthly_shop_list').append('<option>' + shop.description + '</option>');
            $('.yearly_shop_list').append('<option>' + shop.description + '</option>');
        }
        for(let shop of shop_lists.sale){
            _shops.push({
                id: shop.shop_id,
                name: find_shop_name(shop.shop_id),
                value: find_shop_name(shop.shop_id)
            });
        }
    }
    let flat_process = (data) => {

        netsale             = [];
        promotion           = [];
        discount            = [];
        transaction_count   = [];
        tip                 = [];
        tax                 = [];
        average_bill        = [];
        _netsale             = 0;
        _promotion           = 0;
        _discount            = 0;
        _transaction_count   = 0;
        _tip                 = 0;
        _tax                 = 0;
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
        for(let tx of data.tax){
            tax.push({
                shop: find_shop_name(tx.shop_id),
                value: tx.tax
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
        for(let item of tax){
            _tax += item.value ? parseFloat(item.value) : 0;
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
    let display_flat_data = () =>{
        $("._netsale").text(process_price(_netsale));
        $("._discount").text(process_price(_discount));
        $("._average_bill").text(process_price(_average_bill ? _average_bill : 0));
        $("._transaction_count").text(_transaction_count);
        $("._tip").text(process_price(_tip));
        $("._tax").text(process_price(_tax));
        $("._promotion").text(process_price(_promotion));
    }
    let display_flat_data_single = () => {

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
    let process_price = (val) => {
        // Converts string to price format || 34503 -> 34.5k
        let value = parseFloat(val);
        if(Math.abs(value) > 1000 * 1000){
            return (value / 1000).toFixed(2) + ' m';
        }else if(Math.abs(value) > 1000){
            return (value / 1000).toFixed(2) + ' k';
        }else{
            return value.toFixed(2);
        }
    }
    let process_price_secondary = (val) => {
        // Converts string to price format || 34503 -> $34,503.00
        let value = parseFloat(val);
        return formatter.format(value);
    }
    let process_percent = (val, ref) => {
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
    let process_one_value = (val, ref) => {
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
            if(sorted_netsale.length > 1){
                sale_comparison_reverse();
            }
            sale_comparison_table(sorted_netsale);

            let turnover_ranks = getRanks(temp_turnover);
            let transaction_ranks = getRanks(temp_transaction);
            Highcharts.chart('sale_comparison_bar', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: lang[site_lang]['hc_comparison_by_shops']
                },
                xAxis: {
                    categories: temp_shop, // Error, Distorted shop names
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: lang[site_lang]['hc_sales'] + ' [USD]'
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
                    name: lang[site_lang]['hc_turnover'],
                    data: temp_turnover
                },{
                    name: lang[site_lang]['hc_transaction'],
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
    let monthly_growth_process = (data, id) => {
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
                let _date = [];
                let _date_checker = [];
                let _data = [];
                let _s_data = [];
                for(let item of data.daily_sale){
                    if(_s == find_shop_name(item.shop_id)){
                        _date.push(formatDate(new Date(item.transaction_date.date)));
                    }
                }
                _date_checker = x_sale.map((item) => (_date.indexOf(item) > -1 ? 1 : 0));
                for(let item of data.daily_sale){
                    if(_s == find_shop_name(item.shop_id)){
                        _s_data.push(item);
                    }
                }
                let idx = 0;
                for(let i = 0; i < _date_checker.length; i++){
                    if(_date_checker[i] == 1){
                        _data.push(parseFloat(_s_data[idx].netsale));
                        idx ++;
                    }else{
                        _data.push(0);
                    }
                }
                series_sale.push({
                    name: _s,
                    data: _data
                })
            }
            for(let _s of s_transaction){
                let _date = [];
                let _date_checker = [];
                let _data = [];
                let _s_data = [];
                for(let item of data.daily_transaction){
                    if(_s == find_shop_name(item.shop_id)){
                        _date.push(formatDate(new Date(item.transaction_date.date)));
                    }
                }
                _date_checker = x_sale.map((item) => (_date.indexOf(item) > -1 ? 1 : 0));
                for(let item of data.daily_transaction){
                    if(_s == find_shop_name(item.shop_id)){
                        _s_data.push(item);
                    }
                }
                let idx = 0;
                for(let i = 0; i < _date_checker.length; i++){
                    if(_date_checker[i] == 1){
                        _data.push(parseFloat(_s_data[idx].transaction_count));
                        idx ++;
                    }else{
                        _data.push(0);
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
                    text: lang[site_lang]['hc_comparison_30_days_1']
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
                    text: lang[site_lang]['hc_comparison_30_days_2']
                },
                xAxis: {
                    categories: x_transaction
                },
                yAxis: {
                    title: {
                        text: lang[site_lang]['hc_transaction_count']
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

    let date_range_set = (st, ed) => {
        second_ajax = [];
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
    }               // Priority 0
    let get_dashboard_data = (start, end) => {
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
    }       // Priority 1
    let get_daily_turnover = () => {
        let data = {
            start: moment().subtract(7, 'days').format('YYYY-MM-DD'),
            end: moment().format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('_shop_name'),
            length: JSON.parse(localStorage.getItem('_shop_name')).length
        }
        if($("#yt_comparison").highcharts()){
            $("#yt_comparison").highcharts().destroy();
        }
        if($("#w_comparison").highcharts()){
            $("#w_comparison").highcharts().destroy();
        }
        if($("#wl_comparison").highcharts()){
            $("#wl_comparison").highcharts().destroy();
        }
        if($("#m_comparison").highcharts()){
            $("#m_comparison").highcharts().destroy();
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
                    let d_series = [];
                    let d_temp_shop = '';
                    if(JSON.parse(localStorage.getItem('_shop_name')).length > 3){
                        for(let item of d_data){
                            d_last_7.push(parseFloat(item.netsale));
                            d_w_7.push(moment(dateFromDay(moment().format('Y'), item.d)).format('ddd'));
                        }
                        d_series.push({
                            name: 'Turnover',
                            data: d_last_7
                        })
                        d_yesterday.push(parseFloat(d_data[d_data.length - 2].netsale));
                        d_today.push(parseFloat(d_data[d_data.length - 1].netsale));
                    }else{
                        let _date = [];
                        let _date_ = '';
                        let _s = [];
                        let _s_ = '';
                        for(let item of d_data){
                            if((_date_ != item.d) && (_date.indexOf(item.d) < 0)){
                                _date.push(item.d);
                                _date_ = item.d;
                            }
                            if((_s_ != item.shop_name) && (_s.indexOf(item.shop_name) < 0)){
                                _s.push(item.shop_name);
                                _s_ = item.shop_name;
                            }
                        }
                        let _y = 0;
                        let _t = 0;
                        for(let item of _date){
                            d_w_7.push(moment(dateFromDay(moment().format('Y'), item)).format('ddd'));
                        }
                        for(let i = _s.length * 4; i < _s.length * 5; i++){
                            _y += parseFloat(d_data[i].netsale);
                        }
                        for(let i = _s.length * 5; i < _s.length * 6; i++){
                            _t += parseFloat(d_data[i].netsale);
                        }
                        d_yesterday.push(_y);
                        d_today.push(_t);
                        for(let item of _s){
                            let _values = [];
                            for(let _item of d_data){
                                if(item == _item.shop_name){
                                    _values.push(parseFloat(_item.netsale));
                                }
                            }
                            d_series.push({
                                name: item,
                                data: _values
                            })
                        }
                    }

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
                            name: lang[site_lang]['hc_yesterday'],
                            data: d_yesterday

                        }, {
                            name: lang[site_lang]['hc_today'],
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
                            text: lang[site_lang]['hc_last_7_days'],
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
                        series: d_series
                    });
                }
            }
        });
    }   // Priority 2
    let get_weekly_turnover = () => {
        let data = {
            start: (new Date().getFullYear().toString() + '-' + (new Date().getMonth() + 1).toString() + '-01'),
            end: moment().format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('_shop_name'),
            length: JSON.parse(localStorage.getItem('_shop_name')).length
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
                    let w_days = [...weeks];
                    let w_sale = [0, 0, 0, 0, 0];
                    let w_series = [];
                    if(JSON.parse(localStorage.getItem('_shop_name')).length > 3){
                        let idx = 0;
                        for(let item of w_data){
                            if(item.w < 5){
                                w_sale[item.w - 1] = parseFloat(item.netsale);
                            }else{
                                w_sale[Math.ceil(moment().day("Monday").week(item.w).date() / 7) - 1] = parseFloat(item.netsale);
                            }
                        }
                        w_series.push({
                            name: 'Turnover',
                            data: w_sale
                        });
                    }else{
                        let _s = [];
                        let _s_ = '';
                        let idx = 0;
                        for(let item of w_data){
                            if((_s_ != item.shop_name) && (_s.indexOf(item.shop_name) < 0)){
                                _s.push(item.shop_name);
                                _s_ = item.shop_name;
                            }
                        }
                        for(let item of _s){
                             let _values = [0, 0, 0, 0, 0];
                            for(let _item of w_data){
                                if(item == _item.shop_name){
                                    if(_item.w < 5){
                                        _values[_item.w - 1] = parseFloat(_item.netsale);
                                    }else{
                                        _values[Math.ceil(moment().day("Monday").week(_item.w).date() / 7) - 1] = parseFloat(_item.netsale);
                                    }
                                }
                            }
                            w_series.push({
                                name: item,
                                data: [..._values]
                            })
                        }
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
                            text: lang[site_lang]['hc_this_month_turnover'],
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
                        series: w_series
                    });
                }
            }
        });
    } // Priority 3
    let get_monthly_turnover = () => {
        let data = {
            start: (new Date().getFullYear().toString() + '-01-01'),
            end: moment().format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('_shop_name'),
            length: JSON.parse(localStorage.getItem('_shop_name')).length
        }
        $.ajax({
            url: api_path + 'home/monthly_turnover',
            method: 'post',
            data: data,
            success: function(res){
                get_daily_data(end)
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    $('#turnover_detail').removeClass('hide');
                    let m_data = response.data.monthly_turnover;

                    let m_label = [...months];
                    let m_sale = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    let m_series = [];
                    if(JSON.parse(localStorage.getItem('_shop_name')).length > 3){
                        let idx = 0;
                        for(let item of m_data){
                            m_sale[item.m - 1] = parseFloat(item.netsale);
                        }
                        m_series.push({
                            name: 'Turnover',
                            data: m_sale
                        })
                    }else{
                        let _s = [];
                        let _s_ = '';
                        let idx = 0;
                        for(let item of m_data){
                            if((_s_ != item.shop_name) && (_s.indexOf(item.shop_name) < 0)){
                                _s.push(item.shop_name);
                                _s_ = item.shop_name;
                            }
                        }
                        for(let item of _s){
                            let _values = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                            for(let _item of m_data){
                                if(item == _item.shop_name){
                                    _values[_item.m - 1] = parseFloat(_item.netsale);
                                }
                            }
                            m_series.push({
                                name: item,
                                data: [..._values]
                            })
                        }
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
                            text: lang[site_lang]['hc_this_year_turnover'],
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
                        series: m_series
                    });
                }
            }
        });} // Priority 4 || Issue fixed for disordered display || 12/13
    let get_yearly_turnover = () => {
        let data = {
            shop_name: localStorage.getItem('_shop_name'),
            length: JSON.parse(localStorage.getItem('_shop_name')).length
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
    } // Priority INF
    let get_daily_data = (date) => {

        if(second_ajax.length == 0){
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
                    $('.loader').addClass('hide');
                    let response = JSON.parse(res);
                    second_ajax = response;
                    if(response.status == 'success'){
                        monthly_growth_process(response.data, 0);
                    }
                }
            });
        }
    } // Priority 5

    let operator_table_render = () => {
        // Show operators by shop checked
        $('.operator_multiselect').empty();
        let p_operator = shop_operator_data.operators;
        let shop_tags = document.querySelectorAll('.shop_multiselect input');
        let s_ids = [];
        for(let item of shop_tags){
            if(item.checked){
                s_ids.push(item.getAttribute('value'));
            }
        }
        let dup_remove = -1;
        for(let item of p_operator){
            if((s_ids.indexOf(item.shop_id.toString()) > -1) && (dup_remove != item.id)){
                $('.operator_multiselect').append('<div class="row"><div class="col-md-8"><div class="i-checks"><input type="checkbox" value="'
                    + item.id + '" class="checkbox-template operator_check"><label>'
                    + item.code + ':' + item.description + '</label></div></div><div class="col-md-4"><input type="text" placeholder="$/hr" class="form-control form-control-sm op_rate hide" style="height: 23px"></div></div>');
            }
            dup_remove = item.id;
        }
    }
    let render_shop_operator_table = () => {
        $('.shop_multiselect').empty();
        let p_shops = shop_operator_data.shops;
        for(let item of p_shops){
            $('.shop_multiselect').append('<div class="i-checks"><input checked type="checkbox" value="' + item.id + '" class="s_operator_check checkbox-template"><label>' + item.description + '</label></div>');
        }
        operator_table_render();
    }
    let get_operator_info = () => {
        let data = {
            shop_name: localStorage.getItem('_shop_name'),
            length: JSON.parse(localStorage.getItem('_shop_name')).length
        }
        $.ajax({
            url: api_path + 'home/operator_info',
            method: 'post',
            data: data,
            success: function(res){
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    pc_checked = true;

                    shop_operator_data = response.data;
                    render_shop_operator_table();
                }
            }
        });
    }
    let presence_date_change = (st, ed) => {
        $('.presence_date').text(st.format('MMM DD, YYYY') + ' ~ ' + ed.format('MMM DD, YYYY'));
        pc_filter.start = st.format('YYYY-MM-DD');
        pc_filter.end = ed.format('YYYY-MM-DD');
    }
    let payment_date_range_set = (st, ed) => {
        $('#payment_date_range').text(st.format('MMM DD, YYYY') + ' ~ ' + ed.format('MMM DD, YYYY'));
        $('#payment_date_range').attr('start', st.format('YYYY-MM-DD'));
        $('#payment_date_range').attr('end', ed.format('YYYY-MM-DD'));
    }
    let monthly_date_range_set = (st, ed) => {
        $('#monthly_date_range').text(st.format('MMM DD, YYYY') + ' ~ ' + ed.format('MMM DD, YYYY'));
        $('#monthly_date_range').attr('start', st.format('YYYY-MM-DD'));
        $('#monthly_date_range').attr('end', ed.format('YYYY-MM-DD'));
    }
    let yearly_date_range_set = (st, ed) => {
        $('#yearly_date_range').text(st.format('MMM DD, YYYY') + ' ~ ' + ed.format('MMM DD, YYYY'));
        $('#yearly_date_range').attr('start', st.format('YYYY-MM-DD'));
        $('#yearly_date_range').attr('end', ed.format('YYYY-MM-DD'));
    }
    let set_start_date = (st, ed) => {
        $('.start_date').text(st.format('MMM DD, YYYY') + ' ~ ' + ed.format('MMM DD, YYYY'));
        detail_comparison_data.last_start = st.format('YYYY-MM-DD');
        detail_comparison_data.last_end = ed.format('YYYY-MM-DD');
    }
    let set_end_date = (st, ed) => {
        $('.end_date').text(st.format('MMM DD, YYYY') + ' ~ ' + ed.format('MMM DD, YYYY'));
        detail_comparison_data.start = st.format('YYYY-MM-DD');
        detail_comparison_data.end = ed.format('YYYY-MM-DD');
    }

    let presence_table_render = () => {
        $('.presence_operators tbody').empty();
        for(let item of operators){
            let total_hours = 0;
            let total_charge = 0;
            for(let i = 0; i < item.in_timestamp.length; i++){
                total_hours += (new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600);
                total_charge += (((new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600)) < 8) ? (((new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600)) * item.rate) : (8 * item.rate + ((new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600) - 8) * item.rate * 1.5);
                $('.presence_operators tbody').append('<tr><td>'
                    + item.code + ' : ' + item.name + '</td><td>'
                    + item.in_timestamp[i] + '</td><td>'
                    + item.out_timestamp[i] + '</td><td>'
                    + ((new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600)).toFixed(2) + '</td><td>'
                    + ((((new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600)) > 8) ? ((new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600) - 8).toFixed(2) : 0) + '</td><td>'
                    + item.rate.toString() + ' $/hr' + '</td><td>'
                    + ((((new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600)) < 8) ? (((new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600)) * item.rate).toFixed(2) + '$' : (8 * item.rate + ((new Date(item.out_timestamp[i]) - new Date(item.in_timestamp[i])) / (1000 * 3600) - 8) * item.rate * 1.5).toFixed(2) + '$') + '</td><td>'
                    + '<span style="margin-left: 10px" class="adjust_operator" oid="' + item.id + '" tid="' + i + '" data-toggle="modal" data-target="#adjustment"><i class="fa fa-edit"></i></span>' + '</td><td>'
                    + item.history[i] + '</td></tr>');
            }
            $('.presence_operators tbody').append('<tr class="operator_total" style="color: #eaeaea; font-size: 13"><td colspan="2">'
                + item.code + ' : ' + item.name + '</td><td></td><td>'
                + total_hours.toFixed(2) + '</td><td>'
                + ((total_hours > 40) ? (total_hours - 40).toFixed(2) : 0) + '</td><td></td><td>'
                + total_charge.toFixed(2) + '$' + '</td><td></td><td></td><td></td></tr>');
        }
    }

    let article_group_table_render = () => {
        let price = 0;
        let qty = 0;
        let last_week_price = 0;
        let last_week_qty = 0;
        let group_price = 0;
        let table = $('#comparison_detail table tbody');
        table.empty();
        table.append('<tr class="title"><td colspan="9">' + lang[site_lang]['lb_sales'] + '<td><tr>');
        for(let i = 0; i < article_detail.length; i++){
            if((article_detail[i].amount == 0) && (article_detail[i].last_week_amount == 0)){
                continue;
            }
            price += parseFloat(article_detail[i].price);
            qty += (article_detail[i].amount);
            group_price += parseFloat(article_detail[i].price);
            last_week_price += parseFloat(article_detail[i].last_week_price);
            last_week_qty += (article_detail[i].last_week_amount);
        }
        for(let item of article_group_array){
            let group_qty = 0;
            let group_amount = 0;
            let group_percent = 0;
            let last_week_group_qty = 0;
            let last_week_group_amount = 0;
            let last_week_group_percent = 0;
            //table.append('<tr class="sub-title" style="cursor: pointer;"><td colspan="9">' + item.group_name + '<td><tr>');
            let sub_item = "";
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
                    sub_item += ('<tr class="sub-items sub_' + item.group_name.replace(/[^A-Z0-9]/ig, "") + ' hide"><td> ' + _item.article_name
                        + '</td><td>' + _item.last_week_amount
                        + '</td><td>' + parseFloat(_item.last_week_price).toFixed(2)
                        + '</td><td>' + (parseFloat(_item.last_week_price) / parseFloat(item.last_group_price) * 100).toFixed(3) + '%'
                        + '</td><td>' + _item.amount
                        + '</td><td>' + parseFloat(_item.price).toFixed(2)
                        + '</td><td>' + (parseFloat(_item.price) / parseFloat(item.group_price) * 100).toFixed(3) + '%'
                        + '</td><td>' + (_item.amount - _item.last_week_amount).toString()
                        + '</td><td>' + (parseFloat(_item.price) - parseFloat(_item.last_week_price)).toFixed(2)
                        + '</td></tr>')
                }
            }
            sub_item += '<tr class="sub-items sub_' + item.group_name.replace(/[^A-Z0-9]/ig, "") + ' hide"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
            table.append('<tr class="sub-total group_' + item.group_name.replace(/[^A-Z0-9]/ig, "") + '" style="cursor: pointer"><td style="color: #55a">' + item.group_name
                + '</td><td>' + last_week_group_qty
                + '</td><td>' + last_week_group_amount.toFixed(2)
                + '</td><td>' + last_week_group_percent.toFixed(3) + '%'
                + '</td><td>' + group_qty
                + '</td><td>' + group_amount.toFixed(2)
                + '</td><td>' + group_percent.toFixed(3) + '%'
                + '</td><td>' + (group_qty - last_week_group_qty).toString()
                + '</td><td>' + (group_amount - last_week_group_amount).toFixed(2)
                + '</td></tr>');
            table.append(sub_item);
        }
        table.append('<tr class="total"><td>' + lang[site_lang]['lb_sales_total'] + '</td><td>' + last_week_qty.toString() + '</td><td>' + process_price(last_week_price) + '</td><td></td><td>' + qty.toString() + '</td><td>' + process_price(price) + '</td><td></td><td>'
         + (qty - last_week_qty).toString() + '</td><td>' + process_price(price - last_week_price) + '</td></tr>');
         // Discount details
         table.append('<tr class="title"><td colspan="9">' + lang[site_lang]['lb_discount'] + '<td><tr>');
         let this_week_discount_qty = 0;
         let this_week_discount_amount = 0;
         let last_week_discount_qty = 0;
         let last_week_discount_amount = 0;
         let discount_v_qty = 0;
         let discount_v_amount = 0;
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
         table.append('<tr class="total"><td>' + lang[site_lang]['lb_discount_total'] + '</td><td>' + last_week_discount_qty.toString() + '</td><td>' + process_price(last_week_discount_amount) + '</td><td></td><td>' + this_week_discount_qty.toString() + '</td><td>' + process_price(this_week_discount_amount)
         + '</td><td></td><td>' + (this_week_discount_qty - last_week_discount_qty).toString() + '</td><td>' + process_price(this_week_discount_amount - last_week_discount_amount) + '</td></tr>')

         // Payment details
         table.append('<tr class="title"><td colspan="9">' + lang[site_lang]['lb_payment'] + '<td><tr>');
         let this_week_payment_qty = 0;
         let this_week_payment_amount = 0;
         let last_week_payment_qty = 0;
         let last_week_payment_amount = 0;
         let payment_v_qty = 0;
         let payment_v_amount = 0;
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
         table.append('<tr class="total"><td>' + lang[site_lang]['lb_payment_total'] + '</td><td>' + last_week_payment_qty.toString() + '</td><td>' + process_price(last_week_payment_amount) + '</td><td></td><td>' + this_week_payment_qty.toString() + '</td><td>' + process_price(this_week_payment_amount)
         + '</td><td></td><td>' + (this_week_payment_qty - last_week_payment_qty).toString() + '</td><td>' + process_price(this_week_payment_amount - last_week_payment_amount) + '</td></tr>')
    }
    let article_group_sort = (idx, dir) => {

        switch(idx){
            case '1':
                if(dir == 'd'){
                    article_group_array.sort((a, b) => (parseFloat(a.last_group_amount) > parseFloat(b.last_group_amount)) ? -1 : ((parseFloat(b.last_group_amount) > parseFloat(a.last_group_amount)) ? 1 : 0));
                    discount_detail.sort((a, b) => (parseFloat(a.last_week_quantity) > parseFloat(b.last_week_quantity)) ? -1 : ((parseFloat(b.last_week_quantity) > parseFloat(a.last_week_quantity)) ? 1 : 0));
                    payment_detail.sort((a, b) => (parseFloat(a.last_week_qty) > parseFloat(b.last_week_qty)) ? -1 : ((parseFloat(b.last_week_qty) > parseFloat(a.last_week_qty)) ? 1 : 0));
                }else{
                    article_group_array.sort((a, b) => (parseFloat(a.last_group_amount) > parseFloat(b.last_group_amount)) ? 1 : ((parseFloat(b.last_group_amount) > parseFloat(a.last_group_amount)) ? -1 : 0));
                    discount_detail.sort((a, b) => (parseFloat(a.last_week_quantity) > parseFloat(b.last_week_quantity)) ? 1 : ((parseFloat(b.last_week_quantity) > parseFloat(a.last_week_quantity)) ? -1 : 0));
                    payment_detail.sort((a, b) => (parseFloat(a.last_week_qty) > parseFloat(b.last_week_qty)) ? 1 : ((parseFloat(b.last_week_qty) > parseFloat(a.last_week_qty)) ? -1 : 0));
                }
                article_group_table_render();
                break;
            case '2':
                if(dir == 'd'){
                    article_group_array.sort((a, b) => (parseFloat(a.last_group_price) > parseFloat(b.last_group_price)) ? -1 : ((parseFloat(b.last_group_price) > parseFloat(a.last_group_price)) ? 1 : 0));
                    discount_detail.sort((a, b) => (parseFloat(a.last_week_amount) > parseFloat(b.last_week_amount)) ? -1 : ((parseFloat(b.last_week_amount) > parseFloat(a.last_week_amount)) ? 1 : 0));
                    payment_detail.sort((a, b) => (parseFloat(a.last_week_amount) > parseFloat(b.last_week_amount)) ? -1 : ((parseFloat(b.last_week_amount) > parseFloat(a.last_week_amount)) ? 1 : 0));
                }else{
                    article_group_array.sort((a, b) => (parseFloat(a.last_group_price) > parseFloat(b.last_group_price)) ? 1 : ((parseFloat(b.last_group_price) > parseFloat(a.last_group_price)) ? -1 : 0));
                    discount_detail.sort((a, b) => (parseFloat(a.last_week_amount) > parseFloat(b.last_week_amount)) ? 1 : ((parseFloat(b.last_week_amount) > parseFloat(a.last_week_amount)) ? -1 : 0));
                    payment_detail.sort((a, b) => (parseFloat(a.last_week_amount) > parseFloat(b.last_week_amount)) ? 1 : ((parseFloat(b.last_week_amount) > parseFloat(a.last_week_amount)) ? -1 : 0));
                }
                article_group_table_render();
                break;
            case '3':
                if(dir == 'd'){
                    article_group_array.sort((a, b) => (parseFloat(a.group_amount) > parseFloat(b.group_amount)) ? -1 : ((parseFloat(b.group_amount) > parseFloat(a.group_amount)) ? 1 : 0));
                    discount_detail.sort((a, b) => (parseFloat(a.this_week_quantity) > parseFloat(b.this_week_quantity)) ? -1 : ((parseFloat(b.this_week_quantity) > parseFloat(a.this_week_quantity)) ? 1 : 0));
                    payment_detail.sort((a, b) => (parseFloat(a.this_week_qty) > parseFloat(b.this_week_qty)) ? -1 : ((parseFloat(b.this_week_qty) > parseFloat(a.this_week_qty)) ? 1 : 0));
                }else{
                    article_group_array.sort((a, b) => (parseFloat(a.group_amount) > parseFloat(b.group_amount)) ? 1 : ((parseFloat(b.group_amount) > parseFloat(a.group_amount)) ? -1 : 0));
                    discount_detail.sort((a, b) => (parseFloat(a.this_week_quantity) > parseFloat(b.this_week_quantity)) ? 1 : ((parseFloat(b.this_week_quantity) > parseFloat(a.this_week_quantity)) ? -1 : 0));
                    payment_detail.sort((a, b) => (parseFloat(a.this_week_qty) > parseFloat(b.this_week_qty)) ? 1 : ((parseFloat(b.this_week_qty) > parseFloat(a.this_week_qty)) ? -1 : 0));
                }
                article_group_table_render();
                break;
            case '4':
                if(dir == 'd'){
                    article_group_array.sort((a, b) => (parseFloat(a.group_price) > parseFloat(b.group_price)) ? -1 : ((parseFloat(b.group_price) > parseFloat(a.group_price)) ? 1 : 0));
                    discount_detail.sort((a, b) => (parseFloat(a.this_week_amount) > parseFloat(b.this_week_amount)) ? -1 : ((parseFloat(b.this_week_amount) > parseFloat(a.this_week_amount)) ? 1 : 0));
                    payment_detail.sort((a, b) => (parseFloat(a.this_week_amount) > parseFloat(b.this_week_amount)) ? -1 : ((parseFloat(b.this_week_amount) > parseFloat(a.this_week_amount)) ? 1 : 0));
                }else{
                    article_group_array.sort((a, b) => (parseFloat(a.group_price) > parseFloat(b.group_price)) ? 1 : ((parseFloat(b.group_price) > parseFloat(a.group_price)) ? -1 : 0));
                    discount_detail.sort((a, b) => (parseFloat(a.this_week_amount) > parseFloat(b.this_week_amount)) ? 1 : ((parseFloat(b.this_week_amount) > parseFloat(a.this_week_amount)) ? -1 : 0));
                    payment_detail.sort((a, b) => (parseFloat(a.this_week_amount) > parseFloat(b.this_week_amount)) ? 1 : ((parseFloat(b.this_week_amount) > parseFloat(a.this_week_amount)) ? -1 : 0));
                }
                article_group_table_render();
                break;
            case '5':
                if(dir == 'd'){
                    article_group_array.sort((a, b) => (parseFloat(a.v_qty) > parseFloat(b.v_qty)) ? -1 : ((parseFloat(b.v_qty) > parseFloat(a.v_qty)) ? 1 : 0));
                    discount_detail.sort((a, b) => ((parseFloat(a.last_week_quantity)-parseFloat(a.this_week_quantity)) > (parseFloat(b.last_week_quantity)-parseFloat(b.this_week_quantity))) ? -1 : ((parseFloat(b.last_week_quantity)-parseFloat(b.this_week_quantity)) > (parseFloat(a.last_week_quantity)-parseFloat(a.this_week_quantity)) ? 1 : 0));
                    payment_detail.sort((a, b) => ((parseFloat(a.last_week_qty)-parseFloat(a.this_week_qty)) > (parseFloat(b.last_week_qty)-parseFloat(b.this_week_qty))) ? -1 : ((parseFloat(b.last_week_qty)-parseFloat(b.this_week_qty)) > (parseFloat(a.last_week_qty)-parseFloat(a.this_week_qty)) ? 1 : 0));
                }else{
                    article_group_array.sort((a, b) => (parseFloat(a.v_qty) > parseFloat(b.v_qty)) ? 1 : ((parseFloat(b.v_qty) > parseFloat(a.v_qty)) ? -1 : 0));
                    discount_detail.sort((a, b) => ((parseFloat(a.last_week_quantity)-parseFloat(a.this_week_quantity)) > (parseFloat(b.last_week_quantity)-parseFloat(b.this_week_quantity))) ? 1 : ((parseFloat(b.last_week_quantity)-parseFloat(b.this_week_quantity)) > (parseFloat(a.last_week_quantity)-parseFloat(a.this_week_quantity)) ? -1 : 0));
                    payment_detail.sort((a, b) => ((parseFloat(a.last_week_qty)-parseFloat(a.this_week_qty)) > (parseFloat(b.last_week_qty)-parseFloat(b.this_week_qty))) ? 1 : ((parseFloat(b.last_week_qty)-parseFloat(b.this_week_qty)) > (parseFloat(a.last_week_qty)-parseFloat(a.this_week_qty)) ? -1 : 0));
                }
                article_group_table_render();
                break;
            case '6':
                if(dir == 'd'){
                    article_group_array.sort((a, b) => (parseFloat(a.v_amount) > parseFloat(b.v_amount)) ? -1 : ((parseFloat(b.v_amount) > parseFloat(a.v_amount)) ? 1 : 0));
                    discount_detail.sort((a, b) => ((parseFloat(a.last_week_amount)-parseFloat(a.this_week_amount)) > (parseFloat(b.last_week_amount)-parseFloat(b.this_week_amount))) ? -1 : ((parseFloat(b.last_week_amount)-parseFloat(b.this_week_amount)) > (parseFloat(a.last_week_amount)-parseFloat(a.this_week_amount)) ? 1 : 0));
                    payment_detail.sort((a, b) => ((parseFloat(a.last_week_amount)-parseFloat(a.this_week_amount)) > (parseFloat(b.last_week_amount)-parseFloat(b.this_week_amount))) ? -1 : ((parseFloat(b.last_week_amount)-parseFloat(b.this_week_amount)) > (parseFloat(a.last_week_amount)-parseFloat(a.this_week_amount)) ? 1 : 0));
                }else{
                    article_group_array.sort((a, b) => (parseFloat(a.v_amount) > parseFloat(b.v_amount)) ? 1 : ((parseFloat(b.v_amount) > parseFloat(a.v_amount)) ? -1 : 0));
                    discount_detail.sort((a, b) => ((parseFloat(a.last_week_amount)-parseFloat(a.this_week_amount)) > (parseFloat(b.last_week_amount)-parseFloat(b.this_week_amount))) ? 1 : ((parseFloat(b.last_week_amount)-parseFloat(b.this_week_amount)) > (parseFloat(a.last_week_amount)-parseFloat(a.this_week_amount)) ? -1 : 0));
                    payment_detail.sort((a, b) => ((parseFloat(a.last_week_amount)-parseFloat(a.this_week_amount)) > (parseFloat(b.last_week_amount)-parseFloat(b.this_week_amount))) ? 1 : ((parseFloat(b.last_week_amount)-parseFloat(b.this_week_amount)) > (parseFloat(a.last_week_amount)-parseFloat(a.this_week_amount)) ? -1 : 0));
                }
                article_group_table_render();
                break;
            default:
        }
    }
    $('.article_sort').click(function(){
        article_group_sort($(this).attr('sort_attr'), $(this).attr('sort_dir'));
        if($(this).attr('sort_dir') == 'd'){
            $(this).attr('sort_dir', 'a');
        }else{
            $(this).attr('sort_dir', 'd');
        }
    })
    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'sans-serif'
            }
        }
    });

    date_range_set(start, end); // Initiate app
    presence_date_change(start_of_last_week, end_of_last_week); // Presence date range set

    /* Action hooks */
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
    $('#payment_date_range').daterangepicker({
        startDate: start_of_last_month,
        endDate: end_of_last_month,
        ranges: {
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, payment_date_range_set);
    $('#monthly_date_range').daterangepicker({
        startDate: start_of_last_month,
        endDate: end_of_last_month,
        ranges: {
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, monthly_date_range_set);
    $('#yearly_date_range').daterangepicker({
        startDate: start_of_last_year,
        endDate: end_of_last_year,
        ranges: {
           'This Year': [moment().startOf('year'), moment().endOf('year')],
           'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        }
    }, yearly_date_range_set);
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
            if(JSON.parse(localStorage.getItem('_shop_name')).length <=3){
                $('.shop_article_detail').removeClass('hide');
            }else{
                $('.shop_article_detail').addClass('hide');
            }
            let data = {
                start: start.format('YYYY-MM-DD'),
                end: end.format('YYYY-MM-DD'),
                shop_name: localStorage.getItem('shop_name')
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
                            text: lang[site_lang]['hc_total_payment_details'],
                        },
                        xAxis: {
                            categories: p_description,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: lang[site_lang]['hc_price'] + ' [USD]'
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
                            name: lang[site_lang]['hc_payment_type'],
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
                            text: lang[site_lang]['hc_payment_by_articles']
                        },
                        xAxis: {
                            categories: d_group,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: lang[site_lang]['hc_price'] + ' [USD]'
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
                            name: lang[site_lang]['hc_article'],
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
                shop_name: localStorage.getItem('shop_name')
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
                            text: lang[site_lang]['hc_transaction_count_by_hours']
                        },
                        xAxis: {
                            categories: d_hours,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: lang[site_lang]['hc_transaction_count']
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
                            name: lang[site_lang]['hc_transaction_count'],
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
    $('.shop_article_detail').click(() => {
        let data = {
            start: start.format('YYYY-MM-DD'),
            end: end.format('YYYY-MM-DD'),
            shop_name: localStorage.getItem('_shop_name')
        }
        $('.shop_article_detail_box').empty();
        $('.loader').removeClass('hide');
        $.ajax({
            url: api_path + 'home/article_detail',
            method: 'post',
            data: data,
            success: function(res){
                $('.loader').addClass('hide');
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    let s_n = []; // Shop name list
                    let article_data = response.data.article_detail;
                    let filtered_article_data = []; // Limited to 20
                    let _temp_shop_name = '';
                    let article_series = [];

                    for(let item of article_data){
                        if(_temp_shop_name != item.shop_name){
                            s_n.push(item.shop_name);
                            _temp_shop_name = item.shop_name;
                        }
                    }
                    for(let s of s_n){
                        let count = 0;
                        for(let item of article_data){
                            if(s == item.shop_name){
                                if(count < 20){
                                    count++;
                                    filtered_article_data.push(item);
                                }
                            }
                        }
                    }

                    for(let s of s_n){
                        let article_pie = [];
                        let article_x = [];
                        let article_y = [];
                        for(let item of filtered_article_data){
                            if(s == item.shop_name){
                                article_pie.push({
                                    name: item.article_name,
                                    y: parseFloat(item.price)
                                });
                                article_x.push(item.article_name);
                                article_y.push(parseFloat(item.price));
                            }
                        }
                        let div1 = $('<div class="col-lg-4"></div>');
                        let div2 = $('<div class="col-lg-8"></div>');
                        let table = $('<table class="table table-sm table-striped"></table>');
                        let line_div = $('<div id="line_chart_' + s.replace(/\s/g, '') + '"></div>')
                        let pie_div = $('<div id="pie_chart_' + s.replace(/\s/g, '') + '"></div>');
                        // Render table
                        let thead = $('<thead><tr><th>Article</th><th>Total price</th></tr><thead>');
                        let tbody = $('<tbody></tbody>');
                        for(let item of filtered_article_data){
                            if(s == item.shop_name){
                                tbody.append('<tr><td>' + item.article_name + '</td><td>' + item.price + '</td></tr>')
                            }
                        }
                        div1.append('<h3 style="margin: 20px;">' + s + ' - ' + lang[site_lang]['hc_top_20_articles'] + '</h3>');

                        $('.shop_article_detail_box').append(div1.append(table.append(thead).append(tbody)));
                        // Render Pie chart
                        $('.shop_article_detail_box').append(div2);
                        div2.append(pie_div);
                        Highcharts.chart('pie_chart_' + s.replace(/\s/g, ''), {
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {
                                text: s + lang[site_lang]['hc_article_sale_details']
                            },
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        enabled: true,
                                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                                    }
                                }
                            },
                            series: [{
                                name: 'Articles',
                                colorByPoint: true,
                                data: article_pie
                            }]
                        });
                        // Render Line chart
                        div2.append(line_div);
                        Highcharts.chart('line_chart_' + s.replace(/\s/g, ''), {
                            chart: {
                                type: 'column'
                            },
                            title: {
                                text: s + lang[site_lang]['hc_article_sale_details']
                            },
                            subtitle: {
                                text: lang[site_lang]['hc_top_20_articles']
                            },
                            xAxis: {
                                categories: article_x,
                                crosshair: true
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Price'
                                }
                            },
                            tooltip: {
                                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                    '<td style="padding:0"><b>${point.y:.1f}</b></td></tr>',
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
                                name: s,
                                data: article_y
                            }]
                        });
                    }
                }
            }
        })
    })
    //sale_comparison_reverse
    $('.turnover_comparison_sort').click(function(){
        sale_comparison_reverse();
    })
    $('.transaction_comparison_sort').click(function(){
        transaction_comparison_reverse();
    })
    // Single shop select
    $('#all-shops').delegate('.single-shop', 'click', function(){
        localStorage_changed = true;
        if(selected == 'ov'){
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
            get_daily_turnover();
            localStorage_changed = false;
        }else if(selected == 'dc'){
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
                }else{
                    $('.shop-name').text(_shop_name.toString());
                }
                localStorage.setItem('_shop_name', JSON.stringify(_shop_name));
            }else{
                $('.single-shop').removeClass('selected');
                _shop_name = JSON.parse(storage_all_shop);
                $('.shop-name').text('All shops');
                localStorage.setItem('_shop_name', JSON.stringify(_shop_name));
            }
            $('#detail_comparison').trigger('click');
            localStorage_changed = false;
        }
        else{

        }
    })
    $('#refresh').click(function(){
        window.location.reload();
    })
    $('#overall_view').click(function(){
        $('.page-dashboard').removeClass('hide');
        $('.page-present').addClass('hide');
        $('.page-comparison').addClass('hide');
        $('.page-payment').addClass('hide');
        $('.page-monthly').addClass('hide');
        $('.page-yearly').addClass('hide');
        $('.list-unstyled li').removeClass('active');
        $(this).parent().addClass('active');
        selected = 'ov';
    })

    $('.start_date').daterangepicker({
        startDate: start_of_last_week,
        endDate: end_of_last_week
    }, set_start_date);
    $('.end_date').daterangepicker({
        startDate: start_of_this_week,
        endDate: end_of_this_week
    }, set_end_date);
    $('#detail_comparison').click(function(){
        selected = 'dc';
        $('.page-dashboard').addClass('hide');
        $('.page-present').addClass('hide');
        $('.page-comparison').removeClass('hide');
        $('.page-payment').addClass('hide');
        $('.page-monthly').addClass('hide');
        $('.page-yearly').addClass('hide');
        $('.list-unstyled li').removeClass('active');
        $(this).parent().addClass('active');
    })
    $('#operator_present').click(function(){
        selected = 'pc';
        $('.page-dashboard').addClass('hide');
        $('.page-comparison').addClass('hide');
        $('.page-present').removeClass('hide');
        $('.page-payment').addClass('hide');
        $('.page-monthly').addClass('hide');
        $('.page-yearly').addClass('hide');
        $('.list-unstyled li').removeClass('active');
        $(this).parent().addClass('active');
        if(!pc_checked){
            get_operator_info();
        }
    })
    $('#payment_view').click(function(){
        selected = 'pv';
        $('.page-dashboard').addClass('hide');
        $('.page-present').addClass('hide');
        $('.page-comparison').addClass('hide');
        $('.page-payment').removeClass('hide');
        $('.page-monthly').addClass('hide');
        $('.page-yearly').addClass('hide');
        $('.list-unstyled li').removeClass('active');
        $(this).parent().addClass('active');
        payment_date_range_set(start_of_last_month, end_of_last_month);
    })
    $('#month_view').click(function(){
        selected = 'mv';
        $('.page-dashboard').addClass('hide');
        $('.page-present').addClass('hide');
        $('.page-comparison').addClass('hide');
        $('.page-payment').addClass('hide');
        $('.page-monthly').removeClass('hide');
        $('.page-yearly').addClass('hide');
        $('.list-unstyled li').removeClass('active');
        $(this).parent().addClass('active');
        monthly_date_range_set(start_of_last_month, end_of_last_month);
    })
    $('#year_view').click(function(){
        selected = 'mv';
        $('.page-dashboard').addClass('hide');
        $('.page-present').addClass('hide');
        $('.page-comparison').addClass('hide');
        $('.page-payment').addClass('hide');
        $('.page-monthly').addClass('hide');
        $('.page-yearly').removeClass('hide');
        $('.list-unstyled li').removeClass('active');
        $(this).parent().addClass('active');
        yearly_date_range_set(start_of_last_year, end_of_last_year);
    })
    $("#apply_filter").click(function(){
        let table = $('#comparison_detail table tbody');
        table.empty();
        $('.loader').removeClass('hide');
        detail_comparison_data.shop_name = localStorage.getItem('_shop_name');
        $.ajax({
            url: api_path + 'home/get_comparison_detailed_data',
            method: 'post',
            data: detail_comparison_data,
            success: function(res){
                $('.loader').addClass('hide');
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    detail_comparison = response;
                    detail_comparison.shops = localStorage.getItem('_shop_name');

                    $('#export_csv').removeClass('disabled');
                    article_detail = [...response.data.article_detail];
                    discount_detail = [...response.data.discount_detail];
                    payment_detail = [...response.data.payment_detail];
                    // Article detail

                    let article_group = '';
                    article_group_array = [];
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
                        let group_amount = 0;
                        let last_group_amount = 0;
                        let v_qty = 0;
                        let v_amount = 0;
                        for(let _item of article_detail){
                            if(item.group_name == _item.group_name){
                                group_price += parseFloat(_item.price);
                                group_amount += parseInt(_item.amount);
                                last_group_price += parseFloat(_item.last_week_price);
                                last_group_amount += parseInt(_item.last_week_amount);
                                v_qty += (parseInt(_item.amount) - parseInt(_item.last_week_amount));
                                v_amount += (parseFloat(_item.price) - parseFloat(_item.last_week_price));
                            }
                        }
                        item.group_price = group_price;
                        item.last_group_price = last_group_price;
                        item.group_amount = group_amount;
                        item.last_group_amount = last_group_amount;
                        item.v_qty = v_qty;
                        item.v_amount = v_amount;
                    }
                    article_group_table_render();
                }
            }
        });
    })
    $('table').delegate('.sub-total', 'click', function(){
        if($(this).hasClass('_active')){
            $(this).removeClass('_active');
            $('.sub_' + $(this).attr('class').split(" ")[1].split('_')[1]).addClass('hide');
            return;
        }else{
            $(this).addClass('_active');
            $('.sub_' + $(this).attr('class').split(" ")[1].split('_')[1]).removeClass('hide');
        }
    })
    $('#export_csv').click(function(){
        if(!$(this).hasClass('disabled')){
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
        }
    })
    $("#export_xls").click(function(e){
        let tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
        let textRange; let j=0;
        tab = document.getElementById('detail_comparison_table'); // id of table

        for(j = 0 ; j < tab.rows.length ; j++)
        {
            tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
        }
        tab_text=tab_text+"</table>";
        let a = document.createElement('a');
        a.href = 'data:application/vnd.ms-excel,' +  encodeURIComponent(tab_text);
        a.download =  + new Date() + '.xls';
        a.click();
        e.preventDefault();
    })

    $('.presence_date').daterangepicker({
        startDate: start_of_last_week,
        endDate: end_of_last_week,
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
    }, presence_date_change);

    $('.shop_multiselect').delegate('.s_operator_check', 'click', function(){
        operator_table_render();
    })
    $('.payment_view_apply').click(function(){
        let data = {
            start: $('#payment_date_range').attr('start'),
            end: $('#payment_date_range').attr('end'),
            shop_name: $('.payment_shop_list').val()
        }
        $('.loader').removeClass('hide');
        $.ajax({
            url: api_path + 'home/get_payment_view',
            method: 'post',
            data: data,
            success: function(res){
                $('.loader').addClass('hide');
                $('.payment_view_export').removeClass('disabled');
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    let p_netsale = response.data.p_netsale;
                    let p_tax = response.data.p_tax;
                    let p_detail = response.data.p_detail;
                    // Adding required headers
                    let p_types = [];
                    for(let item of p_detail){
                        if(p_types.indexOf(item.payment_description) == -1){
                            p_types.push(item.payment_description);
                        }
                    }
                    $('.payment_table_header').empty();
                    $('.payment_table_header').append('<th width=' + (100 / (4 + p_types.length)) + '%>Date</th><th width=' + (100 / (4 + p_types.length)) + '%>Gross sale</th><th width=' + (100 / (4 + p_types.length)) + '%>Tax</th><th width=' + (100 / (4 + p_types.length)) + '%>Net sale</th>');
                    for(let item of p_types){
                        $('.payment_table_header').append(`<th width=${100 / (4 + p_types.length)}%>${item}</th>`);
                    }
                    // Adding to tbody
                    $('#payment_table tbody').empty();
                    let _date_array = [];
                    for(let item of p_netsale){
                        _date_array.push(item.d);
                    }
                    for(let item of _date_array){
                        let tbody_array = [];
                        tbody_array.push($('#payment_date_range').attr('start').split('-')[0] + '-' + $('#payment_date_range').attr('start').split('-')[1] + '-' + item.toString()); // Date field
                        let __netsale = 0;
                        let __tax = 0;
                        for(let _item of p_netsale){
                            if(item == _item.d){
                                __netsale = parseFloat(_item.netsale);
                            }
                        }
                        for(let _item of p_tax){
                            if(item == _item.d){
                                __tax = parseFloat(_item.tax);
                            }
                        }
                        tbody_array.push(__netsale + __tax); // Gross sale
                        tbody_array.push(__tax); // Tax
                        tbody_array.push(__netsale); // Net sale
                        let _temp_payment = [];
                        for(let _item of p_detail){
                            if(item == _item.d){
                                _temp_payment.push({
                                    type: _item.payment_description,
                                    amount: _item.amount
                                })
                            }
                        }
                        for(let _item of p_types){
                            let found = 0;
                            for(let __item of _temp_payment){
                                if(__item.type == _item){
                                    found = 1;
                                }
                            }
                            if(found == 0){
                                tbody_array.push(0);
                            }else{
                                for(let __item of _temp_payment){
                                    if(__item.type == _item){
                                        tbody_array.push(__item.amount);
                                    }
                                }
                            }
                        }
                        let tr = $('<tr></tr>');
                        for(let _item of tbody_array){
                            tr.append(`<td>${_item}</td>`)
                        }
                        $('#payment_table tbody').append(tr);
                    }
                }
            }
        });
    })
    $('.payment_view_export').click(function(){
        if(!$(this).hasClass('disabled')){
            let filename = localStorage.getItem('user_name') + ' (' + moment().format('YYYY-MM-DD HH:mm:ss') + ') ' + '.csv';
            let csv = [];
            let rows = document.querySelectorAll("#payment_table tr");

            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");

                for (let j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);

                csv.push(row.join(","));
            }
            // Download CSV file
            downloadCSV(csv.join("\n"), filename);
        }
    })
    $('.monthly_view_apply').click(function(){
        let data = {
            start: $('#monthly_date_range').attr('start'),
            end: $('#monthly_date_range').attr('end'),
            shop_name: $('.monthly_shop_list').val()
        }
        $('.loader').removeClass('hide');
        $.ajax({
            url: api_path + 'home/get_monthly_view',
            method: 'post',
            data: data,
            success: function(res){
                $('.loader').addClass('hide');
                $('.monthly_view_export').removeClass('disabled');
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    $('#monthly_table tbody').empty();
                    // Define the templates
                    let week = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    let projected = [4800, 3200, 3200, 3200, 3200, 3400, 4500];

                    // Date series
                    let date_array = [];
                    for(let item of response.data.m_sale){
                        // Format is YYYY-MM-DD || 2019-12-08
                        date_array.push(item.y + '-' + item.m + '-' + item.d);
                    }
                    // Seed the table
                    let ac_projected = 0;
                    let ac_sale = 0;
                    let ac_netsale = 0;
                    let ac_count = 0;
                    let ac_cup = 0;
                    let ac_ac = 0;
                    let ac_drink = 0;
                    for(let date of date_array){
                        let tr_array = [];
                        tr_array.push(date);
                        for(let item of response.data.m_sale){
                            if((item.y == date.split('-')[0]) && (item.m == date.split('-')[1]) && (item.d == date.split('-')[2])){
                                tr_array.push(week[parseInt(item.w) - 1]);
                            }
                        }
                        tr_array.push('#Temp!');
                        for(let item of response.data.m_sale){
                            if((item.y == date.split('-')[0]) && (item.m == date.split('-')[1]) && (item.d == date.split('-')[2])){
                                ac_projected += projected[parseInt(item.w) - 1];
                                ac_sale += parseFloat(item.sale);
                                ac_netsale += parseFloat(item.netsale);
                                tr_array.push(process_price_secondary(projected[parseInt(item.w) - 1]));
                                tr_array.push(process_price_secondary(ac_projected));
                                tr_array.push(((parseFloat(item.sale) / projected[parseInt(item.w) - 1]) * 100).toFixed(2) + '%');
                                tr_array.push(process_price_secondary(item.sale));
                                tr_array.push(process_price_secondary(ac_sale));
                                tr_array.push(process_price_secondary(item.netsale));
                                tr_array.push(process_price_secondary(ac_netsale));
                            }
                        }
                        for(let item of response.data.m_count){
                            if((item.y == date.split('-')[0]) && (item.m == date.split('-')[1]) && (item.d == date.split('-')[2])){
                                ac_count += item.transaction_count;
                                tr_array.push(item.transaction_count);
                                tr_array.push(ac_count);
                            }
                        }
                        for(let item of response.data.m_cup){
                            if((item.y == date.split('-')[0]) && (item.m == date.split('-')[1]) && (item.d == date.split('-')[2])){
                                ac_cup += item.cups;
                                tr_array.push(item.cups);
                                tr_array.push(ac_cup);
                            }
                        }
                        for(let item of response.data.m_ac){
                            if((item.y == date.split('-')[0]) && (item.m == date.split('-')[1]) && (item.d == date.split('-')[2])){
                                ac_ac += parseFloat(item.ac);
                                tr_array.push(process_price_secondary(item.ac));
                                tr_array.push(process_price_secondary(ac_ac));
                            }
                        }
                        for(let item of response.data.m_drink){
                            if((item.y == date.split('-')[0]) && (item.m == date.split('-')[1]) && (item.d == date.split('-')[2])){
                                ac_drink += parseFloat(item.drinks);
                                tr_array.push(process_price_secondary(item.drinks));
                                tr_array.push(process_price_secondary(ac_drink));
                            }
                        }

                        // Render the table
                        let tr_tag = $('<tr></tr>');
                        for(let item of tr_array){
                            tr_tag.append(`<td>${item}</td>`);
                        }
                        tr_tag.append('<td></td>');
                        $('#monthly_table tbody').append(tr_tag);
                    }
                }
            }
        });
    })
    $('.monthly_view_export').click(function(){
        if(!$(this).hasClass('disabled')){
            let filename = localStorage.getItem('user_name') + ' (' + moment().format('YYYY-MM-DD HH:mm:ss') + ') ' + '.csv';
            let csv = [];
            let rows = document.querySelectorAll("#monthly_table tr");

            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");

                for (let j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);

                csv.push(row.join(","));
            }
            // Download CSV file
            downloadCSV(csv.join("\n"), filename);
        }
    })
    $('.yearly_view_apply').click(function(){
        let data = {
            start: $('#yearly_date_range').attr('start'),
            end: $('#yearly_date_range').attr('end'),
            shop_name: $('.yearly_shop_list').val()
        }
        $('.loader').removeClass('hide');
        $.ajax({
            url: api_path + 'home/get_yearly_view',
            method: 'post',
            data: data,
            success: function(res){
                $('.loader').addClass('hide');
                $('.yearly_view_export').removeClass('disabled');
                let response = JSON.parse(res);
                if(response.status == 'success'){

                    $('#yearly_table tbody').empty();

                    // Sale
                    let td_array = [];
                    let total_value = 0;
                    td_array.push('Monthly sales');

                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseFloat(item.netsale);
                                td_array[i + 1] = process_price_secondary(item.netsale);
                            }
                        }
                    }

                    td_array.push(process_price_secondary(total_value));
                    let tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Daily average
                    td_array = [];
                    total_value = 0;
                    td_array.push('Daily average');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseFloat(item.netsale) / (new Date(item.y, item.m, 0).getDate());
                                td_array[i + 1] = (process_price_secondary(parseFloat(item.netsale) / (new Date(item.y, item.m, 0).getDate())));
                            }
                        }
                    }

                    td_array.push(process_price_secondary(total_value / 12));
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Deliver amount
                    td_array = [];
                    total_value = 0;
                    td_array.push('Deliver amount');
                    for(let i = 0; i < 12; i++){
                        td_array.push(0);
                    }
                    td_array.push(process_price_secondary(total_value / 12));
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Deliver amount percent
                    td_array = [];
                    total_value = 0;
                    td_array.push('Deliver amount percent');
                    for(let i = 0; i < 12; i++){
                        td_array.push('0%');
                    }
                    td_array.push('0%');
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Dine in items
                    td_array = [];
                    total_value = 0;
                    td_array.push('Dine in items');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    for(let item of response.data.y_dinein_count){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseInt(item.dinein_count);
                                td_array[i + 1] = (item.dinein_count);
                            }
                        }
                    }

                    td_array.push(total_value);
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Dine in amount
                    td_array = [];
                    total_value = 0;
                    td_array.push('Dine in Amount');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    for(let item of response.data.y_dinein_amount){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseFloat(item.dinein_amount);
                                td_array[i + 1] = (process_price_secondary(item.dinein_amount));
                            }
                        }
                    }

                    td_array.push(process_price_secondary(total_value));
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Dine in percent
                    td_array = [];
                    total_value = 0;
                    td_array.push('Dine in Percent');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    let idx = 0;
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseFloat(response.data.y_dinein_amount[idx].dinein_amount) / parseFloat(response.data.y_sale[idx].netsale);
                                td_array[i + 1] = (((parseFloat(response.data.y_dinein_amount[idx].dinein_amount) / parseFloat(response.data.y_sale[idx].netsale)) * 100).toFixed(2) + '%');
                            }
                        }
                        idx ++;
                    }

                    td_array.push((total_value * 100).toFixed(2) + '%');
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // To go count
                    td_array = [];
                    total_value = 0;
                    td_array.push('To go items');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    idx = 0;
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseInt(response.data.y_togo_count[idx].togo_count);
                                td_array[i + 1] = (response.data.y_togo_count[idx].togo_count);
                            }
                        }
                        idx++;
                    }

                    td_array.push(total_value);
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // To go amount
                    td_array = [];
                    total_value = 0;
                    td_array.push('To go Amount');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    idx = 0;
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseFloat(response.data.y_togo_amount[idx].togo_amount);
                                td_array[i + 1] = (process_price_secondary(response.data.y_togo_amount[idx].togo_amount));
                            }
                        }
                        idx++;
                    }
                    td_array.push(process_price_secondary(total_value));
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // To go percent
                    td_array = [];
                    total_value = 0;
                    td_array.push('To go Percent');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    idx = 0;
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseFloat(response.data.y_togo_amount[idx].togo_amount) / parseFloat(response.data.y_sale[idx].netsale);
                                td_array[i + 1] = (((parseFloat(response.data.y_togo_amount[idx].togo_amount) / parseFloat(response.data.y_sale[idx].netsale)) * 100).toFixed(2) + '%');
                            }
                        }
                        idx++;
                    }

                    td_array.push((total_value * 100).toFixed(2) + '%');
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Monthly transaction count
                    td_array = [];
                    total_value = 0;
                    td_array.push('Monthly transaction tickets');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    idx = 0;
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseInt(response.data.y_transaction_count[idx].transaction_count);
                                td_array[i + 1] = (response.data.y_transaction_count[idx].transaction_count);
                            }
                        }
                        idx++;
                    }
                    td_array.push(total_value);
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Monthly item count
                    td_array = [];
                    total_value = 0;
                    td_array.push('Monthly items');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    idx = 0;
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseInt(response.data.y_article_count[idx].article_count);
                                td_array[i + 1] = (response.data.y_article_count[idx].article_count);
                            }
                        }
                        idx++;
                    }
                    td_array.push(total_value);
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Price per ticker
                    td_array = [];
                    total_value = 0;
                    td_array.push('Unit price/per tickers');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    idx = 0;
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseFloat(response.data.y_sale[idx].netsale) / parseFloat(response.data.y_transaction_count[idx].transaction_count);
                                td_array[i + 1] = (process_price_secondary(parseFloat(response.data.y_sale[idx].netsale) / parseFloat(response.data.y_transaction_count[idx].transaction_count)));
                            }
                        }
                        idx++;
                    }
                    td_array.push(process_price_secondary(total_value / 12));
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);

                    // Price per article
                    td_array = [];
                    total_value = 0;
                    td_array.push('Unit price/per items');
                    for(let i = 0; i < 12; i ++){
                        td_array.push(0);
                    }
                    idx = 0;
                    for(let item of response.data.y_sale){
                        for(let i = 0; i < 12; i++){
                            if(item.m == (i + 1)){
                                total_value += parseFloat(response.data.y_sale[idx].netsale) / parseFloat(response.data.y_article_count[idx].article_count);
                                td_array[i + 1] = (process_price_secondary(parseFloat(response.data.y_sale[idx].netsale) / parseFloat(response.data.y_article_count[idx].article_count)));
                            }
                        }
                        idx++;
                    }
                    td_array.push(process_price_secondary(total_value / 12));
                    tr = $('<tr></tr>');
                    for(let item of td_array){
                        tr.append('<td>' + item + '</td>');
                    }
                    $('#yearly_table tbody').append(tr);
                }

            }
        })
        //$('.loader').removeClass('hide');
    })
    // Temp modify
    $('#monthly_table tbody').delegate('td:nth-child(3)', 'click', function(){
        let value = $(this).text();
        let input = $('<input class="temp_input form-control form-control-sm"/>');
        $(this).empty();
        $(this).append(input);
        input.val(value);
        input.focus();
    })
    $('#monthly_table tbody').delegate('.temp_input', 'focusout', function(){
        let value = $(this).val();
        $(this).parent().text(value);
        $(this).remove();
    })

    // Projected sales modify
    let update_projected_ac = () => {
        let projected_tds = $('#monthly_table tbody td:nth-child(4)');
        let projected_ac_tds = $('#monthly_table tbody td:nth-child(5)');

        let projected_ac_values = [];

        let ac_value = 0;
        for(let item of projected_tds){
            ac_value += Number(item.innerText.replace(/[^0-9.-]+/g,""));
            projected_ac_values.push(process_price_secondary(ac_value));
        }
        let idx = 0;
        for(let item of projected_ac_tds){
            item.innerText = (projected_ac_values[idx]);
            idx++;
        }
    }
    let update_sales_percent = () => {
        let projected_tds = $('#monthly_table tbody td:nth-child(4)');
        let percent_tds = $('#monthly_table tbody td:nth-child(6)');
        let sales_tds = $('#monthly_table tbody td:nth-child(7)');

        let length = projected_tds.length;
        for(let i = 0; i < length; i++){
            percent_tds[i].innerText = (Number(sales_tds[i].innerText.replace(/[^0-9.-]+/g,"")) / Number(projected_tds[i].innerText.replace(/[^0-9.-]+/g,"")) * 100).toFixed(2) + '%';
        }
    }
    $('#monthly_table tbody').delegate('td:nth-child(4)', 'click', function(){
        let value = Number($(this).text().replace(/[^0-9.-]+/g,""));
        let input = $('<input class="projected_input form-control form-control-sm"/>');
        $(this).empty();
        $(this).append(input);
        input.val(value);
        input.focus();
    })
    $('#monthly_table tbody').delegate('.projected_input', 'keyup', function(e){
        if(e.originalEvent.keyCode == 13){
            $(this).focusout();
        }
    })
    $('#monthly_table tbody').delegate('.projected_input', 'focusout', function(){
        let value = $(this).val();
        $(this).parent().text(process_price_secondary(value));
        $(this).remove();
        update_projected_ac();
        update_sales_percent();
    })

    $(".operator_filter").click(function(){
        let o_shops_dom         = $('.shop_multiselect input:checked'); // Filter, checked shop doms
        let o_tills_dom         = $('.till_multiselect input:checked'); // Filter, checked till doms
        let o_operators_dom     = $('.operator_multiselect input:checked'); // Filter, checked operator doms
        let o_shops         = [];
        let o_tills         = [];
        let o_operators     = [];

        let rate_validation = true;

        operator_rate       = [];

        for(let item of o_shops_dom){
            o_shops.push(item.value);
        }
        for(let item of o_tills_dom){
            o_tills.push(item.value);
        }
        for(let item of o_operators_dom){
            if(item.parentElement.parentElement.parentElement.querySelector('.op_rate').value){}else{
                rate_validation = false;
            }
        }
        if(rate_validation){

            for(let item of o_operators_dom){
                o_operators.push(item.value);
                operator_rate.push({
                    id: item.value,
                    rate: item.parentElement.parentElement.parentElement.querySelector('.op_rate').value
                });
            }
            pc_filter.shops = JSON.stringify(o_shops);
            pc_filter.tills = JSON.stringify(o_tills);
            pc_filter.operators = JSON.stringify(o_operators);
            $('.loader').removeClass('hide');
            $.ajax({
                url: api_path + 'home/operator_presence',
                method: 'post',
                data: pc_filter,
                success: function(res){
                    $('.loader').addClass('hide');
                    let response = JSON.parse(res);
                    if(response.status == 'success'){
                        $('.export_presence_data').removeClass('disabled');
                        $('.save_presence_data').removeClass('disabled');
                        operators = [];
                        let o_id = -1;
                        // Get operators
                        for(let item of response.data.presence){
                            if(o_id != item.operator_id){
                                o_id = item.operator_id;
                                operators.push({
                                    id: o_id,
                                    name: item.operator_name,
                                    code: item.operator_code,
                                    till_id: item.till_id,
                                    shop_id: item.shop_id
                                });
                            }
                        }
                        // Get timestamp for each operator
                        for(let item of operators){
                            item.in_timestamp = [];
                            item.out_timestamp = [];
                            item.rate          = 0;
                            for(let _item of response.data.presence){
                                if(item.id == _item.operator_id){
                                    if(_item.o_type == 1){
                                        item.in_timestamp.push(_item.t_stamp.date.split('.')[0]);
                                    }else{
                                        item.out_timestamp.push(_item.t_stamp.date.split('.')[0]);
                                    }
                                }
                            }
                            // Apply rate to the operators array
                            for(let _item of operator_rate){
                                if(parseInt(_item.id) == parseInt(item.id)){
                                    item.rate = parseInt(_item.rate);
                                }
                            }
                        }
                        // Reorder the timestamp to be valid
                        for(let item of operators){
                            let in_length = item.in_timestamp.length;
                            let out_length = item.out_timestamp.length;
                            if(in_length != out_length){
                                // console.log('----------------------');
                                // console.log(item.in_timestamp);
                                // console.log(item.out_timestamp);
                                if(in_length < out_length){
                                    // Fill gaps in in_timestamp
                                    for(let i = 0; i < item.in_timestamp.length; i++){
                                        if(new Date(item.in_timestamp[i]) > new Date(item.out_timestamp[i])){
                                            item.in_timestamp.splice(i, 0, item.out_timestamp[i]);
                                        }
                                    }
                                    if(item.in_timestamp.length != item.out_timestamp.length){
                                        for(let i = item.in_timestamp.length - 1; i < item.out_timestamp.length - 1; i++){
                                            item.in_timestamp.splice(i + 1, 0, item.out_timestamp[i + 1]);
                                        }
                                    }
                                }else if(in_length > out_length){
                                    // Fill gaps in out_timestamp
                                    for(let i = 0; i < item.out_timestamp.length; i++){
                                        if(new Date(item.in_timestamp[i]) > new Date(item.out_timestamp[i])){
                                            item.out_timestamp.splice(i, 0, item.in_timestamp[i]);
                                        }
                                    }
                                    if(item.in_timestamp.length != item.out_timestamp.length){
                                        for(let i = item.out_timestamp.length - 1; i < item.in_timestamp.length - 1; i++){
                                            item.out_timestamp.splice(i + 1, 0, item.in_timestamp[i + 1]);
                                        }
                                    }
                                }else{}
                                // console.log('*************************');
                                // console.log(item.in_timestamp);
                                // console.log(item.out_timestamp);
                            }
                        }
                        // Set history data
                        for(let item of operators){
                            item.history = [];
                            for(let i = 0; i < item.in_timestamp.length; i++){
                                item.history.push('Not adjusted');
                            }
                        }
                        // Draw table
                        presence_table_render();
                    }
                }
            });
        }else{
            $.toast({
                heading: 'Rate is missing',
                text: 'You have to input the rate of selected operators',
                showHideTransition: 'slide',
                icon: 'error',
                position: 'top-right'
            })
        }
    })
    $('tbody').delegate('.adjust_operator', 'click', function(){
        let o_id = $(this).attr('oid');
        let t_id = $(this).attr('tid');
        let operator = {};
        for(let item of operators){
            if(item.id == o_id){
                operator = item;
            }
        }
        // Reset timepicker
        $('.original_in_timestamp').timepicker('destroy');
        $('.original_out_timestamp').timepicker('destroy');
        $('.new_in_timestamp').timepicker('destroy');
        $('.new_out_timestamp').timepicker('destroy');
        $('.adjust_reason').val('');
        $('#adjustment .operator-name').text(operator.code + ':' + operator.name);

        $('.original_in_timestamp').timepicker({
            timeFormat: 'HH:mm:ss',
            interval: 10,
            defaultTime: operator.in_timestamp[t_id].split(' ')[1],
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            change: function(time){
                $(this).attr('value', moment(time).format('HH:mm:ss'));
            }
        })
        $('.original_out_timestamp').timepicker({
            timeFormat: 'HH:mm:ss',
            interval: 10,
            defaultTime: operator.out_timestamp[t_id].split(' ')[1],
            dynamic: false,
            minTime: operator.in_timestamp[t_id].split(' ')[1],
            maxTime: operator.out_timestamp[t_id].split(' ')[1],
            dropdown: true,
            scrollbar: true,
            change: function(time){
                $(this).attr('value', moment(time).format('HH:mm:ss'));
            }
        })
        $('.new_in_timestamp').timepicker({
            timeFormat: 'HH:mm:ss',
            interval: 10,
            defaultTime: operator.in_timestamp[t_id].split(' ')[1],
            dynamic: false,
            minTime: operator.in_timestamp[t_id].split(' ')[1],
            dropdown: true,
            scrollbar: true,
            change: function(time){
                $(this).attr('value', moment(time).format('HH:mm:ss'));
            }
        })
        $('.new_out_timestamp').timepicker({
            timeFormat: 'HH:mm:ss',
            interval: 10,
            defaultTime: operator.out_timestamp[t_id].split(' ')[1],
            dynamic: false,
            minTime: operator.in_timestamp[t_id].split(' ')[1],
            dropdown: true,
            scrollbar: true,
            change: function(time){
                $(this).attr('value', moment(time).format('HH:mm:ss'));
            }
        })
        $('#adjustment').attr('t_id', t_id);
        $('#adjustment').attr('o_id', o_id);
    })
    $('.adjust_done').click(function(){
        let t_id = $('#adjustment').attr('t_id');
        let o_id = $('#adjustment').attr('o_id');
        let operator = {};
        for(let item of operators){
            if(o_id == item.id){
                operator = item;
            }
        }
        // Validation
        if((new Date(operator.in_timestamp[t_id].split(' ')[0] + ' ' + $('.original_in_timestamp').val()) < new Date(operator.out_timestamp[t_id].split(' ')[0] + ' ' + $('.original_out_timestamp').val()))
            && (new Date(operator.in_timestamp[t_id].split(' ')[0] + ' ' + $('.new_in_timestamp').val()) < new Date(operator.out_timestamp[t_id].split(' ')[0] + ' ' + $('.new_out_timestamp').val()))
            && ($('.adjust_reason').val() != '')
        ){
            let temp_in_timestamp = [];
            let temp_out_timestamp = [];
            let temp_history = [];

            operator.in_timestamp[t_id] = operator.in_timestamp[t_id].split(' ')[0] + ' ' + $('.new_in_timestamp').val();
            operator.out_timestamp[t_id] = operator.out_timestamp[t_id].split(' ')[0] + ' ' + $('.new_out_timestamp').val();
            operator.history[t_id] = localStorage.getItem('user_name') + " : (" + moment().format('YYYY-MM-DD HH:mm:ss') + ") : " +  $('.adjust_reason').val();
            operator.in_timestamp.splice(t_id, 0, operator.in_timestamp[t_id].split(' ')[0] + ' ' + $('.original_in_timestamp').val());
            operator.out_timestamp.splice(t_id, 0, operator.out_timestamp[t_id].split(' ')[0] + ' ' + $('.original_out_timestamp').val());
            operator.history.splice(t_id, 0, localStorage.getItem('user_name') + " : (" + moment().format('YYYY-MM-DD HH:mm:ss') + ") : " +  $('.adjust_reason').val());
            presence_table_render();
            $('#adjustment').modal('hide');
        }else{
            $.toast({
                heading: 'Input error',
                text: 'Please input valid time range and make sure you have filled the adjustment reason',
                showHideTransition: 'slide',
                icon: 'error',
                position: 'top-right'
            })
            return false;
        }
    })
    $('.export_presence_data').click(function(){
        if(!$(this).hasClass('disabled')){
            let filename = localStorage.getItem('user_name') + ' (' + moment().format('YYYY-MM-DD HH:mm:ss') + ') ' + '.csv';
            let csv = [];
            let rows = document.querySelectorAll(".presence_operators tr");

            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");

                for (let j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText);

                csv.push(row.join(","));
            }
            // Download CSV file
            downloadCSV(csv.join("\n"), filename);
        }
    })
    $('.save_presence_data').click(function(){
        if(!$(this).hasClass('disabled')){
            $('.loader').removeClass('hide');
            $.ajax({
                url: api_path + 'home/save_present_data',
                method: 'post',
                data: {
                    operators: JSON.stringify(operators),
                    manager: localStorage.getItem('user_name'),
                    date: moment().format('YYYY-MM-DD HH:mm:ss')
                },
                success: function(res){
                    $('.loader').addClass('hide');
                    let response = JSON.parse(res);
                    if(response.status == 'success'){
                        $.toast({
                            heading: 'Save success',
                            text: 'Presence of operators data successfully stored in database',
                            showHideTransition: 'slide',
                            icon: 'success',
                            position: 'top-right'
                        })
                    }else{
                        $.toast({
                            heading: 'Error',
                            text: 'Database has error right now, please try again later',
                            showHideTransition: 'slide',
                            icon: 'error',
                            position: 'top-right'
                        })
                    }
                }
            });
        }
    })
    $('.load_presence_data').click(function(){
        $('.loaded_presence_data_table tbody').empty();
        $('.loader').removeClass('hide');
        $.ajax({
            url: api_path + 'home/load_present_data',
            method: 'post',
            success: function(res){
                $('.loader').addClass('hide');
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    $('.export_presence_data').removeClass('disabled');
                    $('.save_presence_data').removeClass('disabled');
                    stored_operators = response.data;
                    for(let item of stored_operators){
                        $('.loaded_presence_data_table tbody').append('<tr><td>'
                            + item.manager + '</td><td>'
                            + item.date + '</td><td>'
                            + '<span style="margin-right: 10px" class="check_data" did="' + item.id + '"><i class="fa fa-eye"></i></span>'
                            + '<span style="margin-right: 10px" class="download_data" did="' + item.id + '"><i class="fa fa-download"></i></span>'
                            + '<span style="margin-right: 10px" class="delete_data" did="' + item.id + '"><i class="fa fa-remove"></i></span>'
                            + '</td></tr>');
                    }
                }
            }
        });
    })
    // See stored presence data
    $('tbody').delegate('.check_data', 'click', function(){
        for(let item of stored_operators){
            if(item.id == $(this).attr('did')){
                operators = JSON.parse(item.operators);
            }
        }
        presence_table_render();
        $('#presence_loaded_data_modal').modal('hide');
    })
    // Download stored presence data
    $('tbody').delegate('.download_data', 'click', function(){
        let manager = '';
        let date = '';
        for(let item of stored_operators){
            if(item.id == $(this).attr('did')){
                operators = JSON.parse(item.operators);
                manager = item.manager;
                date = item.date;
            }
        }
        presence_table_render();
        let filename = manager + ' (' + date + ') ' + '.csv';
        let csv = [];
        let rows = document.querySelectorAll(".presence_operators tr");

        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");

            for (let j = 0; j < cols.length; j++)
                row.push(cols[j].innerText);

            csv.push(row.join(","));
        }
        // Download CSV file
        downloadCSV(csv.join("\n"), filename);
    })
    // Delete stored presence data
    $('tbody').delegate('.delete_data', 'click', function(){
        let data = {
            id: $(this).attr('did')
        }
        $('.loader').removeClass('hide');
        $.ajax({
            url: api_path + 'home/delete_present_data',
            method: 'post',
            data: data,
            success: function(res){
                $('.loader').addClass('hide');
                let response = JSON.parse(res);
                if(response.status == 'success'){
                    $.toast({
                        heading: 'Delete presence success',
                        text: 'Presence of operators data successfully deleted in database',
                        showHideTransition: 'slide',
                        icon: 'success',
                        position: 'top-right'
                    })
                    $('.loaded_presence_data_table tbody').empty();
                    let temp_stored_operators = [];
                    for(let item of stored_operators){
                        if(item.id != data.id){
                            temp_stored_operators.push(item);
                        }
                    }
                    stored_operators = [...temp_stored_operators];
                    for(let item of stored_operators){
                        $('.loaded_presence_data_table tbody').append('<tr><td>'
                            + item.manager + '</td><td>'
                            + item.date + '</td><td>'
                            + '<span style="margin-right: 10px" class="check_data" did="' + item.id + '"><i class="fa fa-eye"></i></span>'
                            + '<span style="margin-right: 10px" class="download_data" did="' + item.id + '"><i class="fa fa-download"></i></span>'
                            + '<span style="margin-right: 10px" class="delete_data" did="' + item.id + '"><i class="fa fa-remove"></i></span>'
                            + '</td></tr>');
                    }
                }
            }
        });
    })
    // Display popover for help
    $('.popover_hover').popover({
        container: 'body',
        trigger: 'hover'
    })

    // Operater rate mask to numeric
    $('.col-md-4').delegate('.op_rate', 'keypress keyup blur', function(e){
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
            e.preventDefault();
        }
    })

    // Check operators will show the rate Input
    $('.operator_multiselect').delegate('.operator_check', 'click', function(){
        if($(this).is(':checked')){
            $(this)[0].parentElement.parentElement.parentElement.querySelector('.op_rate').classList.remove('hide');
        }else{
            $(this)[0].parentElement.parentElement.parentElement.querySelector('.op_rate').classList.add('hide');
        }
    })

    // Check all or uncheck all
    $('.check_toggle').click(function(){
        let tags = $(this)[0].parentElement.querySelectorAll('input');
        let checked_all = true;
        for(let item of tags){
            if(item.checked == false){
                checked_all = false;
            }
        }
        if(checked_all){
            for(let item of tags){
                item.checked = false;
                if(item.parentElement.parentElement.parentElement.querySelector('.op_rate')){
                    item.parentElement.parentElement.parentElement.querySelector('.op_rate').classList.add('hide');
                }else{
                    operator_table_render();
                }
            }
        }else{
            for(let item of tags){
                item.checked = true;
                if(item.parentElement.parentElement.parentElement.querySelector('.op_rate')){
                    item.parentElement.parentElement.parentElement.querySelector('.op_rate').classList.remove('hide');
                }else{
                    operator_table_render();
                }
            }
        }
    })
})
