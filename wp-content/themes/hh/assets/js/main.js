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

    var cart_count = parseInt($('.site-header .hh-cart a').attr('data-cart-count'));
    if(cart_count > 0) {
      $('.site-header .hh-cart a').append('<span class="cart-count">'+cart_count+'</span>');
    }
    function updateCartCount() {
      let cartTotal;
      $.ajax({
          url: hh_main.ajax_url,
          method: 'GET',
          async: false,
          data: {
              action: 'get_cart_total',
          },
          success: (response) => {
              cartTotal = response.cart_count;
              if(cartTotal == 0 || cartTotal == '') {
                $('.site-header .hh-cart a .cart-count').remove();
              } else {
                if($('.site-header .hh-cart a .cart-count').length) {
                  $('.site-header .hh-cart a .cart-count').text(cartTotal);
                } else {
                  $('.site-header .hh-cart a').append('<span class="cart-count">'+cartTotal+'</span>');
                }
              }
              
          },
          error: (response)=>{
      
          }
      });
  }

  wp.hooks.addAction(
    'experimental__woocommerce_blocks-cart-set-item-quantity',
    'test',
    ( { product, quantity } ) => {
      const key = product.key;
      const unsubscribe = wp.data.subscribe(() => {
              const isPendingQuantity = wp.data.select('wc/store/cart').isItemPendingQuantity(key);
              if ( ! isPendingQuantity ) {
                  updateCartCount();
              }
          }, 'wc/store/cart');
    }
  );

})(jQuery); // Fully reference jQuery after this point.