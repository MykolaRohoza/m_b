function reg (){
    $('input[name="user_name"]').on('keyup change',function(){
        validateName($(this));
    });
    
    $('input[name="user_second_name"]').on('keyup change',function(){
       validateSecName($(this));
     });

    $('input[name="login"][required=""]').on('keyup change',function(){
        validateLogin($(this));
    });

    $('input[name="telephone"]').on('keyup change',function(){
       validateTelephone($(this));
    });

    $('input[name="password"][required=""], input[name="confirm_password"]').on('keyup change',function(){
        validatePassword($('input[name="password"][required=""]'), $('input[name="confirm_password"]'));
    });

//    
    $('input[name="regestration"]').on('click', function (){
        registrate();
        //registrate_test();
    });
    

}
function registrate_test(){ 
    var query = 'registration_test='
    + '&' + 'login' + '=' + 'iyaki@rambler.ru';
    $.ajax({
        type: 'POST',
        url: '/resp/' + query,
        data: query,
        success: function(data){

            var result = JSON.parse(data);
            if(result) {
                result = 'на вашу почту высланы инструкции по активации';
            }
            else{
                 result = 'системная ошибка попробуйте позже';
            }
            showMessage($('input[name="regestration"]'), result);

        }
    });
     timer();

}

function registrate(){ 
    if(validate()){
        var query = getQuery();

        $.ajax({
            type: 'POST',
            url: '/resp/' + query,
            data: query,
            success: function(data){

                var result = JSON.parse(data);
                if(result) {
                    switch(result){
                        case(-1):
                            result = 'Этот логин уже есть в базе';
                        break;
                        case(-2):
                            result = 'Этот телефон уже есть в базе';
                        break;
                        default :
                            result = 'на вашу почту высланы инструкции по активации';
                            timer();
                    }
                }
                else{
                     result = 'системная ошибка попробуйте позже';
                }
                showMessage($('input[name="regestration"]'), result);

            }
        });
    }

    
}
function timer(){
    var time = 15, int;

    function time_for_sent(){
        time--;
        var message = 'На вашу почту высланы инструкции по активации, выслать новый код можно будет через ' + time + ' секунд';
        switch(time){
            case 1:
                message += 'у';
                break;
            case 2,3:
                message += 'ы';
                break;
        }
        showMessage($('input[name="regestration"]'), message);
        $('input[name="regestration"]').attr('disabled', 'disabled');
        if (time <= 0){
           clearInterval(int);
            $('input[name="regestration"]').removeAttr('disabled');
            showMessage($('input[name="regestration"]'), 1);
        }
    }
    int = setInterval(time_for_sent, 1000);
}
function check(val, param, elem){
    var contact;
            if(param === 'telephone') contact = 'телефон';
            if(param === 'login') contact = 'логин';
    $.ajax({
        type: 'POST',
        url: '/resp/check=1&' + param + '=' + val + '',
        data: 'check=1&' + param + '=' + val,
        success: function(data){ 
            var result = JSON.parse(data);
            if(result) {
                showMessage(elem, 'Данный' + ' ' + contact + ' ' + 'уже есть в базе');
            }

        }
    }); 

}


function validateName(elem){
    var str = elem.val(), message;
    str = str.replace(/\s*$/, '').replace(/^\s*/, '');
    if(/^[a-zA-Zа-яА-Я]{3,20}$/.test(str)){

        message = 1;
    }
    else {
         message ='проверьте правильность набора';
        if(str.length > 20) message = 'Максимальная длина имени 20';
        if(str.length < 3) message = 'Минимальная длина имени 3';
        if(/[^a-zA-Zа-яА-Я]/g.test(str)) message = 'Имя может содержать только буквы';
    }
    showMessage(elem, message);
    return message;
}

function validateSecName(elem){
    var str = elem.val(), message;

    str = str.replace(/\s*$/, '').replace(/^\s*/, '');
    if(/^[a-zA-Zа-яА-Я\-]{3,20}$/.test(str)){
        message = 1;
    }
    else {
        if(str.length > 20) message = 'Максимальная длина фамилии 20';
        if(str.length < 3) message = 'Минимальная длина фамилии 3';
        if(/[^a-zA-Zа-яА-Я]/g.test(str)) message = 'Фамилия может содержать только буквы';
    }
        showMessage(elem, message);
        return message;
}
function validateLogin(elem, withOutCheck){
    var str = elem.val(), message;
    str = str.replace(/\s*$/, '').replace(/^\s*/, '');
    if(/^[\d\w]{3,15}@[\d\w]{2,10}\.[\d\w]{2,10}(\.[\d\w]{2,10})?$/.test(str)){
        if(withOutCheck || check(str, 'login', elem)) message = 1;
        else message = 0;
    }
    else {
         message ='проверьте правильность набора';
        if(str.length > 20) message = 'Максимальная длина логина 20';
        if(str.length < 5) message = 'Минимальная длина логина 5';

        
    }

    showMessage(elem, message);
     return message;
}


function validateTelephone(elem, withOutCheck){
    var str = elem.val(), message;

    str = str.replace(/\+|\-|\(|\)|\s/g, '');
    str = str.replace(/^(38)/, '');
    if(/^[\d]{10,15}$/.test(str)){
        if(withOutCheck || check(str, 'telephone', elem)) message = 1;
        else message = 0;
    }
    else {
         message ='проверьте правильность набора';
        if(/^[^\d]$/.test(str)) message = 'Телефон может содержать только цифры пробелы и () -+';
        if(str.length < 10) message = 'Минимальная длина телефона 10';
        if(str.length > 15) message = 'Максимальная длина телефона 15';


    }
    showMessage(elem, message);
     return message;
}
function validatePassword(elem1, elem2){
    var pass = elem1.val(),
        dbl_pass = elem2.val(),
        message;

    if(/^[\d\w]{5,20}$/.test(pass)){
        if(pass !== dbl_pass){
            message = 'пароли не совпадают';
        }
         else  message = 1;
    }
    else {
        message ='проверьте правильность набора';
        if(pass.length > 20) message = 'Максимальная длина пароля 20';
        if(pass.length < 5) message = 'Минимальная длина пароля 5';
        if(!message) message ='Пароль может содержать только латинские буквы и цифры';
    }
    showMessage(elem1, message);
     return message;
}
function validate(){
    var message = validateName( $('input[name="user_name"]')) +
        validateSecName($('input[name="user_second_name"]')) +
        validateLogin($('input[name="login"][required=""]'), true) + 
        validateTelephone($('input[name="telephone"]'), true) +
        validatePassword($('input[name="password"][required=""]'), $('input[name="confirm_password"]')) ;

    if(message == 5) {
            return true;
    }
    else {
        false;
    }
  
}
function getQuery(){

    var query = 'registration='
    + '&' + "user_name" + '=' + $('input[name="user_name"]').val()
    + '&' + 'user_second_name' + '=' + $('input[name="user_second_name"]').val()
    + '&' + 'login' + '=' + $('input[name="login"][required=""]').val()  
    + '&' +  'telephone' + '=' + $('input[name="telephone"]').val() 
    + '&' + 'password' + '=' + $('input[name="password"][required=""]').val();
    return query;
}

function showMessage(elem, message){

    var parent = elem.parent();
    parent.children('span[class="message"]').remove();
    parent.children('br:first').remove();
    if(message !==1 && message !==0) {
        parent.prepend('<span class="message" style="color:red">' + message + '</span> <br>');
    }

}


