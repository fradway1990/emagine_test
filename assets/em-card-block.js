(function($){
    $('body').on('click','.em_card_dots li',function(){
        let target = $(this).attr('data-for');
        target = $(target);
        let dot_index = $(this).index();
        let card_width = $(this).parent().parent().find('.em_card_slide').width();
        $(this).parent().find('li').removeClass('selected');
        $(this).addClass('selected');
        target.parent().animate({
            scrollLeft: dot_index * card_width
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