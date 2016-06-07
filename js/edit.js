$(function (){
    $('div.article_list ul').sortable({
     delay: 250,
     start: function() {
         
     },
     stop: function() {
        var pos = getPositions($(this)),
        query = {
            'articles_menu':'',
            'articles_pos':pos
        },
        handler = function (){};
        handler.get_elem = $(this);
        query_ajax(query, handler);
    
    }

    });  
});

var hdrs = getHolders();

function getPositions($elem){
    var str_pos = '';
    $.each($elem.children(), function (key, value){
        str_pos += '||' + key + $(value).find('b').html();
    });
    return str_pos;
}
function select2hidden(){
    validateSelects();
    $('select').on('change', function(){
        validateSelect($(this));
    });
}
function validateSelect(elem){
    var name = elem.attr('name');
    $('input[name="' + name + '"]').val(elem.val());
    if($('select[name="article_func"] option:selected').val() === 1){
        $('select[name="article_img_place"] option[value="0"]').attr("selected", "selected");
        validateImgPl();
        
    }
}

function validateSelects(){
    var selectNames = ['article_func', 'secondary_to', 'article_dest', 'article_img_place'];
    for (var i = 0; i < selectNames.length; i++){
        validateSelect($('select[name="'+ selectNames[i] + '"]'));
    }
}

function imgName(){
    $('input[type="file"]').on('click', function(){
         $('input[name="name"]').val('');
    });
}



$(function() {
    var txt = $('#change'),     
    hiddenDiv = $('.hiddendiv');

    txt.on('keyup focus', function() {
        validateText(txt, hiddenDiv);

    });
        

});

function createHiddenDivTA(){
    var txt = $('#change');    
    if(txt){
        var hiddenDiv = $(document.createElement('div'));
        txt.addClass('noscroll');
        hiddenDiv.addClass('hiddendiv');
        $('div#changeWrap form').append(hiddenDiv);
        validateText(txt);
    }
}

function validateText(txt){
    var content_ta = txt.val().replace(/^<p>/g, ''),
        hiddenDiv = $('div.hiddendiv');
    
    content_ta = content_ta.replace(/<\/p>/g, '');
    content_ta = content_ta.replace(/<p>/g, '\n\n');
    content_ta = content_ta.replace(/<br>/g, '\n');
    if(content_ta.length > 0){
        var content_hidden = content_ta.replace(/(\n\n)/g, '</p><p>');
        content_hidden = content_hidden.replace(/\n/g, '<br>');
        content_hidden = '<p>' + content_hidden + '</p>';
        txt.html(content_ta);
        $('#v_content').html(content_hidden);
        $('#hidden_change').html(content_hidden);
        hiddenDiv.html(content_hidden);
    }
    txt.css('height', hiddenDiv.height());
}

$(function() {
    var inp_title = $("input[name='article_title']");
    
        inp_title.on('click focus blur keyup', function() {
            validateTitle(inp_title);

        });
    

});
function validateTitle(inp_title){
    var content = inp_title.val();
    $("#v_h3").html(content);
}

function switchImgPlace(){
    validateImgPl(true);
    $('select[name="article_img_place"]').on('change', function(){
        validateImgPl();
    });
}
function validateImgPl(){  
    var place = $('select[name="article_img_place"] option:selected').val();
        var className = 'article article-';
        switch(place){
            case 'top':
                className += 'top';
                break;
            case 'left':
                className += 'left';
                break;
            case 'right':
                className += 'right';
                break;
            default:
                className += 'none';  
            
        }
        className += ' clearfix'
        if(place !== 'none'){
            $('#view_article').removeClass();
            $('#view_article').addClass(className);

            if(hdrs['ph'].full_name($('input[name="article_img_name"]').val()));
            valid_art_img(hdrs['ph'].full_path());
        }
        else{
            valid_art_img(false);
        }
}


function put(elem){
    var src = $(elem).attr('src'),
        alt = $(elem).attr('alt');

    hdrs['ph'].full_path(src);
    if($('select[name="article_img_place"]').val() !== 'none') {
        $('input[name="article_img_name"]').val(hdrs['ph'].full_name());
        valid_art_img(src);
    }

    $('input[name="name"]').val(hdrs['ph'].name());
    $('input[name="old_name"]').val(hdrs['ph'].full_name());
    $('input[name="alt"]').val(alt);
}

function valid_art_img(src){
    if(src){
        $('#article_img').attr('src', src);
        $('#article_img').show();
    }
    else{
        $('#article_img').hide();

    }
}

$(function (){
    var message = $('span.message');
    if(message.length > 0){
        int = setTimeout(function (){
            message.slideUp();
        }, 10000);
        
    }
});

