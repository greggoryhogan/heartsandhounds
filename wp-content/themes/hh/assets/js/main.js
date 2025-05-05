(function($) {

    //sticky nav
    function scroll() {
        if ($(window).scrollTop() >= 30) {
            $('body').addClass('nav-is-sticky');
            
        } else {
            $('body').removeClass('nav-is-sticky');
        }
    }
    scroll;
    document.onscroll = scroll;

    //collapse nav when clicking outside it
    document.addEventListener('click', function (event) {
        const nav = document.getElementById('mobile-nav');
        const button = document.querySelector('[data-bs-toggle="collapse"][href="#mobile-nav"]');
    
        // Check if nav is open
        const isNavOpen = nav.classList.contains('show');
    
        // If nav is open, and click is outside both nav and button, close it
        if (
          isNavOpen &&
          !nav.contains(event.target) &&
          !button.contains(event.target)
        ) {
          const collapse = bootstrap.Collapse.getInstance(nav) || new bootstrap.Collapse(nav);
          collapse.hide();
          $( "#menu" ).prop( "checked", false );
        }
      });

    //get current user id
    //hh_main.current_user_id

    //hide links
    $(document).on('wplink-open', function () {
        // Hide internal link search
        $('.query-results, .search-panel, .link-search-wrapper').hide();

        // Disable input and search
        $('.link-search-field').prop('disabled', true).val('');

        // Force the URL field to be empty, so they type it manually
        $('#wp-link-url').attr('placeholder', 'Paste full URL here').val('');
    });

    $(document).on('click','#delete-box',function(e) {
        e.preventDefault();
        if (confirm("Are you sure you want to delete this Treat Box? This cannot be undone")) {
            $('#edit-box-form').append('<input type="hidden" name="delete-box" value="1" />');
            $('#save-box').trigger('click');
        } 
    });
    

})(jQuery); // Fully reference jQuery after this point.