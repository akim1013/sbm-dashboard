// User id used to delete user
let userId = -1;

// Get user lists on admin side
let getUsers = () => {
    $.ajax({
        url: '/auth/users',
        method: 'post',
        success: function(res){
            let response = JSON.parse(res);
            if(response.status == 'success'){
                let shop = 'All';
                let users = response.data;
                for(let user of users){
                    shop = 'All';
                    if(user.shop_name != '0'){
                        shop = user.shop_name;
                    }
                     $('.user-table tbody').append('<tr><td>' + user.name + '</td><td>' + user.email + '</td><td>' + user.database + '</td><td>' + shop + '</td><td><button class="edit_user btn btn-primary" style="margin-right: 10px">Edit</button><button data-toggle="modal" data-target="#confirm-delete" user_id="' + user.id + '" class="delete_user btn btn-primary">Delete</button></td></tr>');
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

// Get database lists
let getDB = () => {
    $('.loader').removeClass('hide');
    $.ajax({
        url: '/auth/db',
        method: 'post',
        success: function(res){
            $('.loader').addClass('hide');
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

// Get shop lists of a database
let getShop = (db) => {
    $('.loader').removeClass('hide');
    $.ajax({
        url: '/auth/shop',
        method: 'post',
        data: {db: db},
        success: function(res){
            $('.loader').addClass('hide');
            $('select[name="shop"]').empty();
            let response = JSON.parse(res);
            if(response.status == 'success'){
                $('select[name="shop"]').append('<option value="0">All shops</option>');
                for(let item of response.data){
                    $('select[name="shop"]').append('<option value="' + item.description + '">' + item.description + '</option>');
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

// Register a user
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

// Delete confirm modal
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

// Admin logout action
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

// Select database when admin is to add a user
$('select[name="database"]').change(function(){
    getShop($(this).val());
})

// Delete user pass user ID
$('.user-table tbody').delegate('.delete_user', 'click', function(){
    userId = $(this)[0].getAttribute('user_id');
})

// Init page
$(document).ready(function(){
    getUsers();
    getDB();
})
