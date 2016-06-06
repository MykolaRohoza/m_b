/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function  passwordCheck(){
    $( 'input[name="new_pass_check"]' ).keyup(function() {

        if( $(this).val() === $('input[name="new_pass"]').val()){
            $('span#office_message').html('Пароли совпадают');
            $('span#office_message').removeAttr('style');
            $('span#office_message').css({'color': 'green'});
        }
        else{
            $('span#office_message').html('Пароли не совпадают');
            $('span#office_message').css({'color': 'red'});
        }
    });

    $( 'input[name="new_pass"]' ).keyup(function() {
        if( $(this).val() === $( 'input[name="new_pass_check"]').val()){
        $('span#office_message').html('Пароли совпадают');
        $('span#office_message').removeAttr('style');
        $('span#office_message').css({'color': 'green'});
        }
        else{
            $('span#office_message').html('Пароли не совпадают');
            $('span#office_message').css({'color': 'red'});
        }
    });
    
}