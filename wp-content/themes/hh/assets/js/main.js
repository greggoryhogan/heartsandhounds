(function($) {
    var menu = $('.site-header');
    var content = $('#main');
    var origOffsetY = menu.offset().top;
    
    function scroll() {
        if ($(window).scrollTop() >= 20) {
            $('body').addClass('nav-is-sticky');
            
        } else {
            $('body').removeClass('nav-is-sticky');
        }
    }

    document.onscroll = scroll;

    

})(jQuery); // Fully reference jQuery after this point.