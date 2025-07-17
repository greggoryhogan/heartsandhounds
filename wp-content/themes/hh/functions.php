<?php

define('HH_URL',get_bloginfo('url'));
define( 'THEME_DIR', get_template_directory() );
define( 'THEME_URI', get_template_directory_uri() );

//include_once(THEME_DIR.'/includes/flms.php');
//include_once(THEME_DIR.'/includes/mailchimp.php');
include_once(THEME_DIR.'/includes/woocommerce.php');

function register_hh_scripts() {
	//Theme version
	$version = wp_get_theme()->get('Version');
	//Deliver minified css for staging/prod
	$hh_dir = HH_URL.'/wp-content/themes/hh';
	//Main Stylesheet
	wp_enqueue_style( 'hh-main', $hh_dir.'/assets/css/main.css', false, $version, 'all' );
	wp_enqueue_script( 'hh-main', $hh_dir.'/assets/js/main.js', array('jquery'), $version, true);
	wp_localize_script( 'hh-main', 'hh_main', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'current_user_id' => get_current_user_id(),
		'post_id' => get_the_ID(),
		'user_id' => get_current_user_id(),
	) );
	wp_enqueue_script( 'henry-bootstrap', HH_URL.'/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', array(), '5.2', true );
	if ( is_account_page() ) {
        wp_enqueue_editor();
    }
	if (is_singular('post')) {
        // Enqueue the default comment scripts
        if (comments_open() || get_comments_number()) {
            wp_enqueue_script('comment-reply');  // This is the WordPress script for handling comment replies
        }
    }
}
add_action( 'wp_enqueue_scripts', 'register_hh_scripts' );

function theme_specific_login_style() {
	$version = wp_get_theme()->get('Version');
	//Deliver minified css for staging/prod
	$hh_dir = HH_URL.'/wp-content/themes/hh';
	wp_enqueue_style( 'hh-main', $hh_dir.'/assets/css/login.css', false, $version, 'all' );
}
add_action( 'login_enqueue_scripts', 'theme_specific_login_style' ); 

add_action('wp_head','hh_early_head_customization',5);
function hh_early_head_customization() { 
	?>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<!--<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">-->
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0 viewport-fit=cover">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-title" content="hh Repeating Arms">
	<meta name="theme-color" content="#191102" />

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-JBQLB3JCCK"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'G-JBQLB3JCCK');
	</script>

	<?php 
}

add_theme_support( 'title-tag','menus', 'editor-color-palette' );
register_nav_menu( 'menu-left',		__( 'Main Menu Left', 'hh' ) );
register_nav_menu( 'menu-right',  __( 'Main Menu Right', 'hh' ) );
register_nav_menu( 'footer-menu',	__( 'Footer Menu', 'hh' ) );

/* ---------------------------------------------------------------------------
 * Main Menu
 * --------------------------------------------------------------------------- */
function hh_wp_nav_menu( $location = 'main-menu', $depth = 4, $extra_class = '' ) {	
	$args = array( 
		'container' 		=> 'false',
		'menu_class'		=> 'menu d-flex align-items-center '.$extra_class, 
		'theme_location'	=> $location,
		'depth' 			=> $depth,
		'echo' 				=> false,
	);
	echo wp_nav_menu( $args ); 
}

function add_last_nav_item($items, $args) {
	if(!function_exists('get_flms_search_form')) {
		return $items;
	}
    // just to show you how to dump it out - remove that line afterwards ofcs
	//var_export($args);
    // If this is the menu you are looking for, add search form
    if (isset($args->theme_location) && $args->theme_location === 'main-menu') {
        $items .= '<li class="hh-search"><a href="/courses/" class="toggle-course-search">Search</a>' . get_flms_search_form(false, 'Search for courses') . '</li>';
    }
    return $items;
}
//add_filter( 'wp_nav_menu_items', 'add_last_nav_item', 10, 2);

add_filter( 'wp_setup_nav_menu_item','my_item_setup' );
function my_item_setup($item) {
	if(is_array($item->classes)) {
		if(in_array('hh-my-account-link', $item->classes)) {
			if(get_current_user_id() == 0) {
				$item->title = 'Log In';
			}
		}
	}
    return $item;
}

add_filter( 'nav_menu_link_attributes','add_woo_data_to_link', 10, 3 );
function add_woo_data_to_link($atts, $item, $args) {
	if(is_array($item->classes)) {
		if(in_array('hh-cart', $item->classes)) {
			if ( class_exists( 'woocommerce' ) ) {
				$cart_count = is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : '';
				$atts['data-cart-count'] = $cart_count;
			}
		}
	}
    return $atts;
}

//add_action( 'login_enqueue_scripts', 'hh_login_branding' );
function hh_login_branding() { ?>
	<style type="text/css">
		.wp-core-ui .button, .wp-core-ui .button-secondary {
			color: #191102!important;
		}
		body.login div#login h1 a {
			background-image: url('<?php echo THEME_URI; ?>/logo.png');
			background-size: contain;
			width: 100%;
		}
		.login form .input, .login form input[type=checkbox], .login input[type=text] {
			font-family: 'Arial';
		}
		.submit .button {
			background: #fff!important;
			box-shadow: none!important;
			text-shadow: none!important;
			font-family: 'Arial';
			color: #191102!important;
			letter-spacing: 0px;
			border: 1px solid #191102!important;
		}
		.submit .button:hover,
		.submit .button:focus,
		.submit .button:active {
			background-color: #191102!important;
			color: #fff!important;
		}
		body.login {
			background: #fff;
		}
		.login form {
			border-color: #191102!important;
			border-radius: 3px;
		}
		input[type="color"], input[type="date"], input[type="datetime-local"], input[type="datetime"], input[type="email"], input[type="month"], input[type="number"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="time"], input[type="url"], input[type="week"], select, textarea {
			border-color: #191102!important;
			border-radius: 3px;
		}
		.message {
			border-color: #ffb300 !important;
			background: #ffb300 !important;
			box-shadow: none !important;
			border-radius: 3px;
		}
		#nav, #backtoblog {text-align: center!important;}
		a {color: #191102!important; text-decoration: none;}
		a:hover,
		a:hover,
		a:active {
			color: #191102!important;
			text-decoration: underline!important;
		}
	</style><?php 
} 

add_filter( 'login_headerurl', 'hh_loginlogo_url' );
function hh_loginlogo_url($url) {
     return get_bloginfo('url');
}

/** Add dropdown actions to menu */
function menu_set_dropdown( $menu_items, $args ) {
    $last_top = 0;
    foreach ( $menu_items as $key => $obj ) {
		if(isset($obj->classes)) {
			if(in_array('my-account', $obj->classes)) {
				if(get_current_user_id() > 0) {
					$obj->title = 'My Account';
				}
			}
		}
        // it is a top lv item?
        if ( 0 == $obj->menu_item_parent ) {
            // set the key of the parent
            $last_top = $key;
        } else {
			if(strpos($menu_items[$last_top]->title,'menu-toggle') == false) {
				$menu_items[$last_top]->classes['has-submenu'] = 'has-submenu';
				$menu_items[$last_top]->title .= '<span class="menu-toggle">';
			}
        }
    }
    return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'menu_set_dropdown', 10, 2 );

if(!function_exists('is_staging')) {
	function is_staging() {
		if(defined('IS_STAGING')) {
			return IS_STAGING;
		}
		return false;
	}
}

function wp_engine_manual_cache_flush() {
	// Don't cause a fatal if there is no WpeCommon class
	if ( ! class_exists( 'WpeCommon' ) ) {
		return 'This is not a WP Engine environment. ';
	}

	if ( method_exists('WpeCommon', 'purge_memcached' ) ) {
		\WpeCommon::purge_memcached();
	}

	if ( method_exists('WpeCommon', 'clear_maxcdn_cache' ) ) {
		\WpeCommon::clear_maxcdn_cache();
	}

	if ( method_exists('WpeCommon', 'purge_varnish_cache' ) ) {
		\WpeCommon::purge_varnish_cache();
	}
	
	global $wp_object_cache;
	// Check for valid cache. Sometimes this is broken -- we don't know why! -- and it crashes when we flush.
	// If there's no cache, we don't need to flush anyway.
	$error = ' WP Engine caches cleared.';

	if ( $wp_object_cache && is_object( $wp_object_cache ) ) {
		try {
			wp_cache_flush();
		} catch ( \Exception $ex ) {
			$error = "Warning: error flushing WordPress object cache: " . $ex->getMessage();
		}
	}

	return $error;
}

add_filter('wp_link_query_args', 'disable_link_searching');
function disable_link_searching($args) {
	if ( is_admin() && wp_doing_ajax() ) {
		$args['post_type'] = false;
	}
	return $args;
}
add_filter('gettext', 'translate_text', 10);
add_filter('ngettext', 'translate_text');
function translate_text($translated_text) {
	if($translated_text == 'Paste URL or type to search') {
		$translated_text = 'Enter a url';
	} 
	if ( $translated_text === 'One response to %s' ) {
        return 'One tail wag about %s';
    }
	if ( $translated_text === '%1$s responses to %2$s' ) {
        return '%1$s tail wags about %2$s';
    }
	if ( $translated_text === '%1$s response to %2$s' ) {
        return '%1$s tail wag %2$s';
    }
	if($translated_text === 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.') {
		return 'From your account dashboard you can manage your <a href="'.trailingslashit(get_bloginfo('url')).'my-account/treat-boxes/">treat boxes</a>, view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.';
	}
	if($translated_text == 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.') {
		return 'From your account dashboard you can manage your <a href="'.trailingslashit(get_bloginfo('url')).'my-account/treat-boxes/">treat boxes</a>, view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.';
	}
	return $translated_text;
}

add_action('wp_login', function ($user_login, $user) {
    //Set a login timestamp so we can query users who have never logged in
    update_user_meta($user->ID, 'last_login', current_time('mysql'));
}, 10, 2);

//keep non-admins out
add_action('admin_init', function () {
    // Only run on actual admin pages (not AJAX or admin-ajax.php)
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        if (!current_user_can('administrator')) {
            wp_redirect(home_url('/my-account/'));
            exit;
        }
    }
});
//hide toolbar
add_filter('show_admin_bar', function ($show) {
    return current_user_can('administrator');
});

function custom_comment_form_defaults($defaults) {
    $defaults['title_reply'] = 'Share Your Thoughts or Treat Box Stories';  // Change the comment box title
    $defaults['comment_notes_after'] = '<p class="comment-notes">Have a treat box story to share? Share any stories, feedback, or a wagging tail moment from your visit. Your thoughts make a difference!</p>';  // Add custom text below the comment form
    return $defaults;
}
add_filter('comment_form_defaults', 'custom_comment_form_defaults');

function remove_comment_avatars($args) {
    $args['avatar_size'] = 0;  // Set avatar size to 0 to prevent avatars from being displayed
    return $args;
}
add_filter('comment_form_defaults', 'remove_comment_avatars');  // Removes avatars in the comment form
add_filter('get_avatar', '__return_false');  // Disables avatars everywhere in comments

function remove_comment_author_link($author_link) {
    return get_comment_author();  // This returns just the author's name without the link
}
add_filter('get_comment_author_link', 'remove_comment_author_link');


add_filter('comment_form_default_fields', 'website_remove');
function website_remove($fields)
{
   if(isset($fields['url']))
   unset($fields['url']);
   return $fields;
}
remove_action( 'set_comment_cookies', 'wp_set_comment_cookies' );

function hearts_and_hounds_custom_comment_form_order( $fields ) {
    // Reorder the fields: name, email, comment
    $new_order = [];

    if ( isset( $fields['author'] ) ) {
        $new_order['author'] = $fields['author'];
    }
    if ( isset( $fields['email'] ) ) {
        $new_order['email'] = $fields['email'];
    }
    if ( isset( $fields['comment'] ) ) {
        $new_order['comment'] = $fields['comment'];
    }

    return $new_order;
}
add_filter( 'comment_form_fields', 'hearts_and_hounds_custom_comment_form_order' );

function hearts_and_hounds_comment_form_notes( $defaults ) {
    $defaults['comment_notes_before'] = '<p class="comment-notes">Have a treat box story to share? Share any stories, feedback, or a wagging tail moment from your visit. Your thoughts make a difference!</p>';
    $defaults['comment_notes_after'] = '<small class="d-block"><span class="required">*</span> Required</small>'; // Remove from below the textarea
	$defaults['comment_notes_after'] = '';
    return $defaults;
}
add_filter( 'comment_form_defaults', 'hearts_and_hounds_comment_form_notes' );

function hearts_and_hounds_comment_field_descriptions( $fields ) {
    if ( isset( $fields['author'] ) ) {
        $fields['author'] = '<p class="comment-form-author">
            <label for="author">Name <span class="required">*</span></label>
            <input id="author" name="author" type="text" required />
            <small class="form-text text-muted">Please enter your first name or nickname.</small>
        </p>';
    }

    if ( isset( $fields['email'] ) ) {
        $fields['email'] = '<p class="comment-form-email">
            <label for="email">Email <span class="required">*</span></label>
            <input id="email" name="email" type="email" required />
            <small class="form-text text-muted">Your email won&rsquo;t be shown publicly.</small>
        </p>';
    }

    return $fields;
}
add_filter( 'comment_form_fields', 'hearts_and_hounds_comment_field_descriptions' );

add_filter('template_redirect',function() {
	if ( is_404() ) {
		global $wp_query;
		if(is_array($wp_query->query)) {
			if(isset($wp_query->query['name'])) {
				$path = str_replace('box','',str_replace('box-', '',sanitize_text_field( $wp_query->query['name'] )));
				$query = new WP_Query( array(
					'post_type' => 'post',
					'meta_query' => array(
						array(
							'key' => 'box_number',
							'value' => $path,
							'compare' => '='
						)
					)
				));
				if($query->have_posts()) {
					 while($query->have_posts()) {
						$query->the_post();
						wp_redirect(get_permalink(get_the_ID()));
						exit;
					 }
				}

			}
		}
	}
	
});

//Add box to post title - Now handled through rankmath
/*add_filter( 'document_title_parts', 'custom_modify_document_title_parts' );
function custom_modify_document_title_parts( $title ) {
    if ( is_singular( 'post' ) ) {
        $title['title'] = $title['title'] ."'s Box";
    }
    return $title;
}*/