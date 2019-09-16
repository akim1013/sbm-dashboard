$(document).ready(function(){
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
