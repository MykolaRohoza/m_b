$(function() {
    $('.full_container').hide();
    $(window).scroll(function (){
        getScroll();
    });
    $('.exercises_container').sortable({
        delay: 250,
        start: function() {
        },
        stop: function() {
            
        }
        
    });  
    
    setDraggable(); 
    $('input[name="add_ex"]').on('click', function (){
        var ex_name = $('input[name="new_ex"]').val().trim();
        addNewEx(ex_name);
        addNewExGetAll(ex_name);
    });
    $('input[name="exercise"]').on('click', function (){ 
        save_exercises($(this));
    });
    
    $('div.more').on('click', function (){
        show_users_info($(this));
        getScroll($(this));
    });

});
// todo
function getScroll($elem){
    var max_height = $('div#contacts_container').height(),
    $pusher = $('#pusher'),
    scroll = $(window).scrollTop(),
    offered_height = $pusher.parent().height() - $pusher.height()+ scroll;
    if($elem && false){
        $pusher.animate({
            height: $pusher.parent().position().top - $elem.parent().position().top
        }, 300);            
    }
    
    if(max_height > offered_height || $pusher.height() > offered_height){
    }
        $pusher.height(scroll);
}

function  span2changeble(elem){
    var $span = $(elem),
        content = $span.html(),
        width = $span.width(),
        id = ($span.attr('id'))?$span.attr('id'):'0_',
        container = $span.parent(),
        index = container.children('span').index(elem) - 1,
        data = getDataNames(elem); 
    $span.remove();
    addChangeble(width, content, data['data_name'], data['id_data'],
        data['data_cont'], id, container, index, data['data_cont'] === 'id_role');
    
    container.siblings('div.full_container').slideDown('fast');
}
function getDataNames(elem){
    var $elem = $(elem),
    container = $elem.parent(),
    index = container.parent().children('h4').index(container);
    var dataNamesHolder = [
        {'data_name' : 'role_menu',
        'id_data' : '',
        'data_cont' : 'id_role'},
        
        {'data_name' : 'contacts_menu',
        'id_data' : 'id_info',
        'data_cont' : 'contact'},
        
        {'data_name' : 'diagnosis_menu',
        'id_data' : '',
        'data_cont' : 'diagnosis'}
    ];
    
    return dataNamesHolder[index];
}
function getSelectInner ($elem, data_name, id_data, data_cont, id, handler){
        var id_user = getIdUserByElem($elem),
        query = {'id_user': id_user};
        if(id_data.length > 0) query[id_data] = id.split('_')[0];

        query[data_name] = '';
        query[data_cont] = $elem.val();

        query_ajax(query, handler);
}

function addChangeble(width, content, data_name, id_data, data_cont, id, container, index, is_select){
    var $new_elem;
    if(!is_select){
        $new_elem = $('<input type="text" value="' + content + '">');
    }
    else{
        
        $new_elem = $('<select></select>'); 
        var select_handler =  function (result){
                $.each(result, function (key, value){
                var selected = '';
                    if(content.trim() === value.trim()) {
                        selected = 'selected="selected"';
                    }
                    $('<option value="' + key + '" ' + selected + ' >' + value + '</option>').appendTo($new_elem);
                });
            };
            select_handler.get_elem = $new_elem;
        save_ (container.children()[0], data_name, id_data, data_cont, id, select_handler);
        
    }
    
    $new_elem.insertAfter(container.children()[index]);
    
    var handler = function (result){
        
        input2span($new_elem, result, id_data, data_cont, container, index);
        handler.get_elem = function (){
            return $new_elem;
        };
       
    };
    handler.get_elem = $new_elem;
    
    $new_elem.width(width + 10);
    if($new_elem[0].tagName.toLowerCase() === 'select'){
        $new_elem.bind('change', function(){
            save_($(this), data_name, id_data, data_cont, id, handler); 
        });
    }
    if($new_elem[0].tagName.toLowerCase() === 'input'){
        $new_elem.bind("blur keyup e", function(e){
            if(e.keyCode === undefined || e.keyCode === 13) 
                save_($(this), data_name, id_data, data_cont, id, handler); 
            
        });
    }

    return $new_elem;

}


function save_ (elem, data_name, id_data, data_cont, id, handler){
        
    var $elem = $(elem),
        id_user = getIdUserByElem($elem),
        query = {'id_user': id_user};
    if(id_data.length > 0) query[id_data] = id.split('_')[0];
    
    query[data_name] = '';
    query[data_cont] = $elem.val();
    
    query_ajax(query, handler);
}

function  input2span(elem, response, id, content, container, index){
    
    elem.remove();
    if(response[content].length > 0) {
        var elem_id;
        if(response[id]){
            elem_id =  'id="' + response[id] + '_' + response[content].replace(/\s/g, '') + '"';
        }
        var $new_elem = $('<span ' + elem_id + '></span>');
        
        $new_elem.insertAfter(container.children()[index]);
        
        $new_elem.html(response[content]).bind("dblclick", function(){
                    span2changeble($(this));
                });
        var next = $new_elem.next();
        if(next.length > 0){
            if(next.html() !== '.' && next.html() !== ', ' ){
                $('<span>, </span>').insertAfter($new_elem);
            }
        }
        else{
            $('<span>.</span>').insertAfter($new_elem);
        }
    }
    else{
        var useless = $(container.children('span')[index + 1]);
        if(useless.html() === '.' || useless.html() === ', '){
            useless.remove();
        }
    }
}


function query_ajax(obj, handler){
    var query  = '';
    $.each(obj, function (key, value){
        if(query.length !== 0) query += '&';
        query += key + '=' + value;
    });
    $.ajax({
        type: 'POST',
        url: '/resp/' + query,
        data: query,
        beforeSend: function (){
            if(handler.get_elem){
                handler.get_elem.css('cursor', 'progress');
                handler.get_elem.attr('disabled', 'disabled');
            }
            
        },
        success: function(data){
            var result = JSON.parse(data);
            if(result) {
                handler.get_elem.css('cursor', 'auto');
                handler.get_elem.removeAttr('disabled');
                handler(result);
            }
            else{

            }


        }
    }); 
}

function  new_contact(elem){   
    addChangeble('20%', '', 'contacts_menu', 'id_info', 'contact', '', $(elem).parent(), 0);

}
function  new_diagnosis(elem){
    addChangeble('20%', '', 'diagnosis_menu', '', 'diagnosis', '', $(elem).parent(), 0);
}

function getIdUserByElem(elem){
    return elem.parent().siblings('div.full_container').children('form').children('input[name="id_user"]').val();
}
function  show_users_info(elem){
    
    var $elem = $(elem),
        cont = $elem.siblings('.full_container');
    if(cont.is(':hidden')){
        cont.slideDown("slow");
        $elem.children('span').removeClass();
        $elem.children('span').addClass('glyphicon glyphicon-arrow-up');
    }
    else{
        cont.slideUp("slow");
        $elem.children('span').removeClass();
        $elem.children('span').addClass('glyphicon glyphicon-arrow-down');
    }
}

function  save_exercises(elem){
    var id_user =  elem.siblings('input[name="id_user"]').val();
    var cont = elem.parent('form').siblings('.exercises_container'); 

    var exercises = cont.children('.exercise'), glue = '==||##', user_ex = glue;
    for (var i = 0; i < exercises.length; i++){
        var exercise = $(exercises[i]);
        user_ex += glue + exercise.children('input[name="id_exercise"]').val()  
                + glue + exercise.children('span.ex').html()  
                + glue + exercise.children('span.counts').html()
                + glue + exercise.children('span.repeat').html();
    }
    var query = 'id_user=' + id_user + '&add_user_ex=' +  user_ex;

    $.ajax({
        type: 'POST',
        url: '/resp/' + query,
        data: query,
        start: function (){
            
        },
        success: function(data){
            var result = JSON.parse(data);
            if(result) {
                cont.empty();
                for (var i = 0; i < result.length; i++){    
                    createExercise(cont, result[i]['id'], result[i]['ex'], result[i]['count'], result[i]['repeat']);
                }
            }
            else{

            }
        }
    });
    
}

function onStopDrag(){

    $('.exercises_container div.ui-draggable div').remove();
    var cont = $('.exercises_container div.ui-draggable').removeAttr('class');
    cont.addClass('exercise').append(
                            '<span class="counts">30</span>'
                            + '<div class="pd_btn plus" onclick="plus_counts(this)"></div>'
                            + '<div class="pd_btn deg" onclick="deg_counts(this)"></div>'
                            + '<span> X </span>'
                            + '<span class="repeat">2</span>'
                            + '<div class="pd_btn plus" onclick="plus_rep(this)"></div>'
                            + '<div class="pd_btn deg" onclick="deg_rep(this)"></div>'
                        );

}
function addNewEx(str, id_exercise){
    if(str !== undefined && str.trim().length > 0 ){
        // Добавить проверку на номер
        id_exercise = (id_exercise === undefined)?0:id_exercise;
        $('div.container_add_ex').append(
                '<div class="exercise ui-draggable">'
                + '<input type="hidden" name="id_exercise"  value="' + id_exercise + '">'
                + '<span class="ex">' + str + '</span>'
                + '<div class="pd_btn deg" onclick="deg_ex(this)"></div>'
                + '</div>'
        );
        
    }
}
function addNewExGetAll(str, id_exercise){
    if(str !== undefined && str.trim().length > 0){
    id_exercise = (id_exercise === undefined)?0:id_exercise;
    var query = 'add_new_ex=' + str;
    $.ajax({
        type: 'POST',
        url: '/resp/' + query,
        data: query,
        success: function(data){
            var result = JSON.parse(data);
            if(result) {
                $('div.container_add_ex').remove();
                $('#exercise_bank').append('<div class="container_add_ex">');
                $.each(result, function(id_exercise, str) {
                    addNewEx(str, id_exercise);
                });
                setDraggable();
            }
            else{

            }


        }
    });
    }
}
function plus_counts(elem){
    var span = $(elem).siblings('span.counts');
    var val = parseInt(span.html());
    val++;
    span.html(val);
}
function deg_counts(elem){
    var span = $(elem).siblings('span.counts');
    var val = parseInt(span.html());
    val--;
    if(val >= 0){
        span.html(val);
    }
    else{
        span.html(0);
    }
}
function plus_rep(elem){
    elem = $(elem);
    var span = elem.siblings('span.repeat');
    var val = parseInt(span.html());
    val++;
    span.html(val);
}
function deg_rep(elem){
    elem = $(elem);
    var span = elem.siblings('span.repeat');
    var val = parseInt(span.html());
    val--;
    if(val >= 0){
        span.html(val);
    }
    else{
        elem.parent().remove();
    }
}

function deg_ex(elem){
    var jElem = $(elem);
    var id_exercise = jElem.siblings('input[name="id_exercise"]').val();
    if(id_exercise !== undefined && id_exercise > 0){
        var query = 'del_ex=&id_exercise=' + id_exercise;
        $('div.exercise input[value="' + id_exercise  + '"]').parent().remove();
        $.ajax({
            type: 'POST',
            url: '/resp/' + query,
            data: query,
            success: function(data){
                var result = JSON.parse(data);
                if(result) {
                    $('div.container_add_ex').remove();
                    $('#exercise_bank').append('<div class="container_add_ex">');
                    $.each(result, function(id_exercise, str) {
                        addNewEx(str, id_exercise);
                    });
                    setDraggable();
                }
                else{

                }


            }
        });
    }
}


function setDraggable(){
    $('.container_add_ex .exercise').draggable({
        connectToSortable: '.exercises_container',
        helper: "clone",
        stop: function() {   
            onStopDrag();
        }
    });
}
function createExercise(elem, id, ex, count, repeat){
    elem.append(
                '<div class="exercise" style="display: inline-block;">'
                + '<input type="hidden" value="' + id + '" name="id_exercise">'   
                + '<span class="ex">' + ex + '</span>'
                + '<span class="counts">' + count + '</span>'
                + '<div class="pd_btn plus" onclick="plus_counts(this)"></div>'
                + '<div class="pd_btn deg" onclick="deg_counts(this)"></div>'
                + '<span> X </span>'
                + '<span class="repeat">' + repeat + '</span>'
                + '<div class="pd_btn plus" onclick="plus_rep(this)"></div>'
                + '<div class="pd_btn deg" onclick="deg_rep(this)"></div>'
                + '</div>'
            );
}



