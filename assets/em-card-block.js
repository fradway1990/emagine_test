(function($){
    let scroll_timer;
    let jump_selection = false;
    document.addEventListener('scroll', function (event) {
        clearTimeout(scroll_timer);
        scroll_timer  = setTimeout(function(){
            if(typeof event.target.classList !== 'undefined' && event.target.classList.contains('em_card_slide_show_wrap')){
                let dots = $(event.target).parent().find('.em_card_dots');
                let scroll_pos = $(event.target).scrollLeft();
                let children = $(event.target).children('.em_card_slide');
                let slideshow_left = $(event.target).offset().left; 
                for(var i = 0; i < children.length; i++){
                        if(Math.abs($(children[i]).position().left) < 1){
                            children.removeClass('current_slide');
                            $(children[i]).addClass('current_slide');
                            let child_id = $(children[i]).attr('id');
                            console.log(dots);
                            dots.children('li').removeClass('selected');
                            $('#'+child_id+'_dot').addClass('selected');
                            break;
                        }
                }
            }
        },66);
    },true);

    $('body').on('click','.em_card_dots li',function(){
        let target = $(this).attr('data-for');
        target = $(target);
        console.log(target.position().left);
        $(this).parent().find('li').removeClass('selected');
        $(this).addClass('selected');
        target.parent().animate({
            scrollLeft: target.position().left
          }, 50);
        target.parent().children('.em_card_slide').removeClass('current_slide');
        target.addClass('current_slide');
    });

    $('body').on('click','.em_card_button',function(){
        
        let slideshow = $(this).parent().find('.em_card_slide_show_wrap');
        let scrollPos = slideshow.scrollLeft();
        let card_width = slideshow.find('.em_card_slide').width();
        let scroll_amt = scrollPos + card_width;
        let current_slide = slideshow.find('.current_slide');
        let dots = $(this).parent().find('.em_card_dots');

        if(this.classList.contains('prev')){
            scroll_amt = scrollPos - card_width;
        }
        slideshow.animate({
            scrollLeft: scroll_amt
        }, 50);
        current_slide.removeClass('current_slide');
        let new_current_slide = current_slide.next('.em_card_slide');
        if(this.classList.contains('prev')){
            new_current_slide = current_slide.prev('.em_card_slide');
        }
        new_current_slide.addClass('current_slide');
        new_current_slide_id = $(new_current_slide).attr('id');

        dots.children('li').removeClass('selected');
        $('#'+new_current_slide_id+'_dot').addClass('selected');
    });

    
}(jQuery));