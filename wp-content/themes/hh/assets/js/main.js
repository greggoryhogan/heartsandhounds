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

    $(document).on('click','.shelter-link',function(e) {
      var link = $(this).attr('href');
      var shelter = $(this).text();
      $.ajax({
            url: hh_main.ajax_url,
            method: 'POST',
            data: {
                action: 'update_treatbox_link_count',
                link : link,
                shelter : shelter,
                post_id : hh_main.post_id,
                user_id : hh_main.user_id
            },
            success: (response) => {
                
            },
            error: (response)=>{
        
            }
        });
    });

    const $tooltip = $('<div class="tooltip"></div>').appendTo('body');
  let isTouch = false;

  function showTooltip($el, pageX, pageY) {
    const text = $el.data('tooltip');
    $tooltip.text(text).fadeIn(150).css({
      top: pageY + 10,
      left: pageX + 10
    });
  }

  function hideTooltip() {
    $tooltip.hide();
  }

  // Desktop hover
  $('.award').on('mouseenter', function(e) {
    if (isTouch) return;
    showTooltip($(this), e.pageX, e.pageY);
  }).on('mousemove', function(e) {
    if (isTouch) return;
    $tooltip.css({
      top: e.pageY + 10,
      left: e.pageX + 10
    });
  }).on('mouseleave', function() {
    if (isTouch) return;
    hideTooltip();
  });

  // Mobile tap
  $('.award').on('touchstart click', function(e) {
    isTouch = true;
    const $el = $(this);
    const offset = $el.offset();

    showTooltip($el, offset.left + $el.outerWidth() / 2, offset.top + $el.outerHeight());

    // Hide after 2 seconds or if tapped again
    setTimeout(hideTooltip, 2000);
    e.stopPropagation(); // Prevent event from bubbling
  });

  // Tap anywhere else to close
  $(document).on('touchstart click', function(e) {
    if (!$(e.target).closest('.award').length) {
      hideTooltip();
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