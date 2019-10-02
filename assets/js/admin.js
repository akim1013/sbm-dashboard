let userId = -1;
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
function getUsers(){
    $.ajax({
        url: '/auth/users',
        method: 'post',
        success: function(res){
            let response = JSON.parse(res);
            if(response.status == 'success'){
                let users = response.data;
                for(let user of users){
                     $('.user-table tbody').append('<tr><td>' + user.name + '</td><td>' + user.email + '</td><td>' + user.database + '</td><td><button data-toggle="modal" data-target="#confirm-delete" user_id="' + user.id + '" class="delete_user btn btn-primary">Delete</button></td></tr>');
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
}
function getDB(){
    $.ajax({
        url: '/auth/db',
        method: 'post',
        success: function(res){
            $('select[name="database"]').empty();
            let response = JSON.parse(res);
            if(response.status == 'success'){
                for(let item of response.data){
                    $('select[name="database"]').append('<option value="' + item.name + '">' + item.name + '</option>');
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
}
// Register user
$('#new_user').submit(function(e){
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
                    $('.user-table tbody').empty();
                    getUsers();
                }, 3500);
            }
        }
    });
})
$('.user-table tbody').delegate('.delete_user', 'click', function(){
    userId = $(this)[0].getAttribute('user_id');
})
$('.confirm-delete').click(function(){
    $.ajax({
        url: '/auth/delete',
        method: 'post',
        data: {
            id: userId
        },
        success: function(res){
            $('.user-table tbody').empty();
            getUsers();
        }
    });
})
$(document).ready(function(){
    getUsers();
    getDB();
})
