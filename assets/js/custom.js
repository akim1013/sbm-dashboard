let site_lang = getCookie('sbm_language');
$(document).ready(function(){

    $('.lang_auth_heading').text(lang[site_lang]['auth_heading']);
    $('.lang_auth_subtitle').text(lang[site_lang]['auth_subtitle_1'] + '\n' + lang[site_lang]['auth_subtitle_2']);
    $('.lang_auth_username').text(lang[site_lang]['auth_username']);
    $('.lang_auth_password').text(lang[site_lang]['auth_password']);
    $('.lang_auth_login').text(lang[site_lang]['auth_login']);
})

$('#login-form').submit(function(e){
    $('.loader').removeClass('hide');
    e.stopPropagation();
    e.preventDefault();
    let data = {
        name: $('input[name="name"]').val(),
        password: $('input[name="password"]').val(),
        language: site_lang
    }
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
                    if(response.shop_name != 'admin'){
                        localStorage.setItem('shop_name', response.shop_name);
                        localStorage.setItem('user_name', response.user_name);
                    }
                    window.location.reload();
                }, 3000);
            }
        }
    });
})
