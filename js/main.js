
window.onload=function(){
    scroll();
    $("a.fancyimage").fancybox({
        'opacity'       : true,
        'overlayShow'   : true,
        'transitionIn'  : 'elastic',
        'titlePosition' : 'over',
        'overlayColor'      : '#000',
        'overlayOpacity'    : 0.5,
        'padding'           : 1,
        'showCloseButton'  : true,    // отображения кнопки закрытия

        'enableKeyboardNav' : true,
        'enableEscapeButton' : true
    }); 
    
        $("a[rel=img_group]").fancybox({
        'opacity'       : true,
	'overlayShow'   : true,
        'transitionIn'  : 'elastic',
        'titlePosition' : 'over', 
        'overlayColor'      : '#000',
        'overlayOpacity'    : 0.5,
        'padding'           : 7,
        'showCloseButton'  : true    // отображения кнопки закрытия
//        'titleFormat'       : function(title, currentArray, currentIndex, currentOpts) {
//            return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' 
//                    + currentArray.length + (title.length ? ' &nbsp; ' + title : 'gfggf') + '</span>';
//	}

        });

	     
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
   navbarLife();

};

function  navbarLife(){
    $('.nav > li > a ').on('mouseenter', function (){
        $(this).animate({
            borderColor:"rgba(0, 0, 0, 1)",
            borderRadius: '10px',
            color:'rgba(108, 123, 139, 1)',
            backgroundColor:'rgba(238, 238, 238, 1)'
            
        }, 500);
    });
    $('.nav > li > a ').on('mouseleave', function (){
        var bgColor = ($(this).is('a.active'))?'rgba(202, 225, 255, 1)':"transparent",
        tColor = ($(this).is('a.active'))?'rgba(108, 123, 139, 1)':'rgba(255, 255, 255, 1)';
 
        $(this).animate({
            borderColor:"transparent",
            borderRadius: '0px',
            color: tColor,
            backgroundColor: bgColor
        }, 500);
    });
}

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
            //console.log(data);
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
 * прячет навбар от кнопки
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