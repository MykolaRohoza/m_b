
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





/**
 * 
 * прячет навбар при вызове
 *
*/
function navbarCollapse(){


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