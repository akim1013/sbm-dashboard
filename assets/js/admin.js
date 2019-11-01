// User id used to delete user
let userId = -1;
let api_path = '/';
let all_data = [];
// Get user lists on admin side
let user_array = [];
let edit_user = (id) => {
    let user = user_array.filter(item => item.id == id)[0];
    $('.shop_multiselect').empty();
    $('.loader').removeClass('hide');
    $.ajax({
        url: api_path + 'auth/shop',
        method: 'post',
        data: {db: user.database},
        success: function(res){
            let response = JSON.parse(res);
            if(response.status == 'success'){
                $('.loader').addClass('hide');
                let select = $('<select name="shop[]" class="mr-sm-3 form-control form-control shop_multiselect" multiple="multiple"></select>');
                for(let item of response.data){
                    let option = '';
                    for(let _item of JSON.parse(user.shop_name)){
                        if(item.description == _item){
                            option = '<option value="' + item.description + '" selected>' + item.description + '</option>';
                            break;
                        }
                        option = '<option value="' + item.description + '">' + item.description + '</option>';
                    }
                    select.append(option);
                }
                $('#edit_user input[name="name"]').val(user.name);
                $('input[name="user_id"]').val(id);
                $('.shop_multiselect').append(select);
                select.multiselect({
                    buttonWidth: (window.width > 512) ? '223px' : '100%',
                    buttonClass: 'multishop-btn',
                    includeSelectAllOption: true,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
            }
        }
    });
}
let getUsers = () => {
    $.ajax({
        url: api_path + 'auth/users',
        method: 'post',
        success: function(res){
            let response = JSON.parse(res);
            if(response.status == 'success'){
                let shop = 'All';
                let users = response.data;
                user_array = [...response.data];
                for(let user of users){
                    shop = '';
                    if(user.shop_name.indexOf('"') > 0){
                        if(JSON.parse(user.shop_name).length > 1){
                            shop = JSON.parse(user.shop_name).length.toString() + ' shops';
                        }else{
                            shop = JSON.parse(user.shop_name).toString();
                        }
                    }
                     $('.user-table tbody').append(
                         '<tr><td>' + user.name +
                         '</td><td>' + user.database +
                         '</td><td>' + shop +
                         '</td><td><span onclick="edit_user(' + user.id + ')" class="edit_user" style="margin-right: 10px" user_id="' + user.id + '" data-toggle="modal" data-target="#edit-user"><i class="fa fa-edit"></i></span><span data-toggle="modal" data-target="#confirm-delete" user_id="' + user.id + '" class="delete_user"><i class="fa fa-remove"></i></span></td></tr>'
                     );
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
        url: api_path + 'auth/db',
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
    $('#shop_multiselect').empty();
    $.ajax({
        url: api_path + 'auth/shop',
        method: 'post',
        data: {db: db},
        success: function(res){
            $('.loader').addClass('hide');
            let response = JSON.parse(res);
            if(response.status == 'success'){
                let select = $('<select name="shop[]" class="mr-sm-3 form-control form-control shop_multiselect" multiple="multiple"></select>');
                for(let item of response.data){
                    select.append('<option value="' + item.description + '">' + item.description + '</option>');
                }
                $('#shop_multiselect').append(select);
                select.multiselect({
                    buttonWidth: (window.width > 512) ? '223px' : '100%',
                    buttonClass: 'multishop-btn',
                    includeSelectAllOption: true,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
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
    //$('.loader').removeClass('hide');
    e.stopPropagation();
    e.preventDefault();
    $.ajax({
        url: api_path + 'auth/register',
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
                    window.location.reload();
                }, 3500);
            }
        }
    });
})

// Delete confirm modal
$('.confirm-delete').click(function(){
    $.ajax({
        url: api_path + 'auth/delete',
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
        url: api_path + 'auth/logout',
        method: 'post',
        success: function(res){
            $('.loader').addClass('hide');
            window.location.reload();
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
$('#edit-user').submit(function(e){
    e.preventDefault();
    e.stopPropagation();
    let data = {
        id: $('input[name="user_id"]').val(),
        name: $('#edit-user input[name="name"]').val(),
        shop: $('#edit-user select').val()
    }
    
    $.ajax({
        url: api_path + 'auth/update',
        method: 'post',
        data: data,
        success: function(res){
            console.log(res);
        }
    });
})
// Init page
$(document).ready(function(){
    $('#shop_select').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true
    });
    getUsers();
    getDB();
})
