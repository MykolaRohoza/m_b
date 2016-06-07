
window.onload=function(){
    scroll();
    $("a.fancyimage").fancybox(); 
    var adv = $('div.advertise h3');
    if(adv.length > 0){
        adv.siblings('p').hide();
        showRebate();
    }
    var sw = $('#carousel-example-generic');
    if(sw.length > 0){
        swipe(['#carousel-example-generic']);
    }
    var inp = $("input[name='article_title']");
    if(inp.length > 0){
        switchImgPlace();
        createHiddenDivTA();
        validateTitle(inp);
        imgName();
        select2hidden();
    }
    reg();
    navbarCollapse();

};



/**
 * показать/спрятать акции 
 *
*/
function showRebate(){
    var div = $('div.advertise h3');
    div.on('click', function(){
        div.siblings('p').slideUp("slow");
        if($(this).siblings('p').is(':hidden')){
            $(this).siblings('p').slideDown("slow");
        }
       
    });
    
    div.siblings('p').on('click', function(){
        $(this).slideUp("slow");
    });

}   
/**
 * 
 * @param {type} arr массив итентификаторов всех необходимых каруселей, ненужные примеры лучше удалить 
 * 
 */

function swipe(arr){

    var query = arr.join(', ');
    $(query).hammer().on('swipeleft', function(){
  			$(this).carousel('next'); 
                       
  		});
    $(query).hammer().on('swiperight', function(){
            $(this).carousel('prev');
           
    });

}
/*
 * 
 * @param obj - массив для передачи данных $POST
 * @param function handler - функция будет вызвана по резудьтату
 * @param handler.get_elem - при наличии данного свойства перед отправкой на 
 * данный элемент будет поставлено - недоступен + измениться курсор по успеху вернет обратно
 * 
 */
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
            console.log(data);
            var result = JSON.parse(data);
                handler.get_elem.css('cursor', 'auto');
                handler.get_elem.removeAttr('disabled');
            if(result) {
                handler(result);
            }
            else{

            }


        },
        error: function(){
            handler.get_elem.css('cursor', 'auto');
            handler.get_elem.removeAttr('disabled');
 
        }
    }); 
}



/**
 * 
 * прячет навбар при вызове
 *
*/
function navbarCollapse(){
    var $holder = $('div.nav-holder'),
        $nav = $('nav.navbar'),
        $toggle = $('button.navbar-toggle');
    $toggle.on('click', function (){
        if($holder.is(':hidden')){
            $holder.show("slide", { direction: "left" }, 300);
            $nav.show("slide", { direction: "left" }, 300);
            $toggle.animate({left: 200}, 300);
        }
        else{
            $holder.hide("slide", { direction: "left" }, 300);
            $nav.hide("slide", { direction: "left" }, 300);
            $toggle.animate({left: 0}, 300);
            
        }
    });
    

}
function removeElem(elem){
    elem.remove();
}


function scroll(){
         $(window).scroll(function () {
            if ($(this).scrollTop() > 10) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#back-to-top').click(function () {
            $('#back-to-top').tooltip('hide');
            $('body,html').animate({
                scrollTop: 0
            }, 200);
            return false;
        });
 
        $('#back-to-top').tooltip('show');
}