$(document).ready(function(){
    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    function draw_pie_chat(dom, data){
        let label = [];
        let value = [];
        let color = [];
        let bgcolor = [];
        let border = [];
        for(let item of data){
            label.push(item.shop);
            value.push(parseFloat(item.value));
            color.push(getRandomColor());
            bgcolor.push(getRandomColor());
            border.push(0);
        }
        var myPieChart = new Chart(dom, {
            type: 'doughnut',
            options: {
                cutoutPercentage: 90,
                legend: {
                    display: true
                }
            },
            data: {
                labels: label,
                datasets: [
                    {
                        data: value,
                        borderWidth: border,
                        backgroundColor: color,
                        hoverBackgroundColor: bgcolor
                    }]
            }
        });
    }
    var shops = [];
    function find_shop_name(id){
        for(var item of shops){
            if(id == item.id) return item.name;
        }
    }
    function add_shop_list(shop_lists){
        $('#all-shops').empty();
        for(var shop of shop_lists){
            shops.push({
                id: shop.id,
                name: shop.description
            });
            $('#all-shops').append($('<li>').append($('<a href="#" shopId=' + shop.id + '>' + shop.description + '</a>')));
        }
    }

    function total_process(data){
        var turnover = 0;
        var discount = 0;
        var promotion = 0;
        var transactions = 0;
        var average_bill = 0;

        for(var to of data.turnover){
            turnover += parseFloat(to.TurnOver);
        }
        for(var dc of data.discount){
            discount += parseFloat(dc.Discount);
        }
        for(var pr of data.promotion){
            promotion += parseFloat(pr.Promotion);
        }
        for(var tr of data.transactions){
            transactions += parseInt(tr.Transactions);
        }
        for(var ab of data.average_bill){
            average_bill += parseFloat(ab.average_bill);
        }
        $('.turnover').text((turnover / 1000).toFixed(2) + ' k');
        $('.discount').text((discount / 1000).toFixed(2) + ' k');
        $('.promotion').text((promotion / 1000).toFixed(2) + ' k');
        $('.transactions').text((transactions / 1000).toFixed(2) + ' k');
        $('.average_bill').text(average_bill.toFixed(2));
        $('.discount_percent').text(((discount / turnover) * 100).toFixed(2) + ' %');
        $('.promotion_percent').text(((promotion / turnover) * 100).toFixed(2) + ' %');
    }
    function comparison_process(data){
        var sales = [];
        var transactions = [];
        var discount = [];

        for(var to of data.turnover){
            sales.push({
                shop: find_shop_name(to.ShopId),
                value: to.TurnOver
            });
        }
        for(var to of data.transactions){
            transactions.push({
                shop: find_shop_name(to.ShopId),
                value: to.Transactions
            });
        }
        for(var to of data.discount){
            discount.push({
                shop: find_shop_name(to.ShopId),
                value: to.Discount
            });
        }
        draw_pie_chat($('#pieChartHome1'), sales);
        draw_pie_chat($('#pieChartHome2'), transactions);
        draw_pie_chat($('#pieChartHome3'), discount);
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
                    total_process(response.data);
                    comparison_process(response.data);
                }
            }
        });
    }

    // Date Range Change
    var start = moment().subtract(3, 'year');
    var end = moment();

    function date_range_set(start, end) {
        $('#reportrange span').html(start.format('M.D.Y') + ' ~ ' + end.format('M.D.Y'));

        get_dashboard_data(start, end);
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, date_range_set);

    date_range_set(start, end);


    // Register User
    $('#register-form').submit(function(e){
        $('.loader').removeClass('hide');
        e.stopPropagation();
        e.preventDefault();
        $.ajax({
            url: '/auth/register',
            method: 'post',
            data: $(this).serialize(),
            success: function(res){
                $('.loader').addClass('hide');
                var response = JSON.parse(res);
                if(response.status == 'failed'){
                    $.toast({
                        heading: 'Error',
                        text: response.msg,
                        showHideTransition: 'fade',
                        icon: 'error',
                        position: 'top-right'
                    })
                }else{
                    $.toast({
                        heading: 'Success',
                        text: response.msg,
                        showHideTransition: 'slide',
                        icon: 'success',
                        position: 'top-right'
                    })
                    setTimeout(function(){
                        window.location.assign('/');
                    }, 3500);
                }
            }
        });
    })
    // Login User
    $('#login-form').submit(function(e){
        $('.loader').removeClass('hide');
        e.stopPropagation();
        e.preventDefault();
        $.ajax({
            url: '/auth/login',
            method: 'post',
            data: $(this).serialize(),
            success: function(res){
                $('.loader').addClass('hide');
                var response = JSON.parse(res);
                if(response.status == 'failed'){
                    $.toast({
                        heading: 'Error',
                        text: response.msg,
                        showHideTransition: 'fade',
                        icon: 'error',
                        position: 'top-right'
                    })
                }else{
                    $.toast({
                        heading: 'Success',
                        text: response.msg,
                        showHideTransition: 'slide',
                        icon: 'success',
                        position: 'top-right'
                    })
                    setTimeout(function(){
                        window.location.assign('/');
                    }, 3500);
                }
            }
        });
    })

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
