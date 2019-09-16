$(document).ready(function(){
    $('#register-form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();
        $.ajax({
            url: '/register/add',
            method: 'post',
            data: $(this).serialize(),
            success: function(res){
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
                        window.location.assign('/login');
                    }, 3500);
                }
            }
        });
    })

    $('#login-form').submit(function(e){
        e.stopPropagation();
        e.preventDefault();
        $.ajax({
            url: '/login/auth',
            method: 'post',
            data: $(this).serialize(),
            success: function(res){
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

})
