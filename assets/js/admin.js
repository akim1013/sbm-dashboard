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
$(document).ready(function(){
    $.ajax({
        url: '/auth/users',
        method: 'post',
        success: function(res){
            let response = JSON.parse(res);
            if(response.status == 'success'){
                $.toast({
                    heading: response.status,
                    text: response.msg,
                    showHideTransition: 'slide',
                    icon: 'success',
                    position: 'top-right'
                })
                let users = response.data;
                for(let user of users){
                     $('.user-table tbody').append('<tr><td></td><td>' + user.name + '</td><td>' + user.email + '</td><td>' + user.database + '</td><td>Action buttons</td></tr>');
                }
            }else{
                $.toast({
                    heading: response.status,
                    text: response.msg,
                    showHideTransition: 'slide',
                    icon: 'error',
                    position: 'top-right'
                })
            }
        }
    });
})
