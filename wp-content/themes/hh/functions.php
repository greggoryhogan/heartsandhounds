<?php

define('HH_URL',get_bloginfo('url'));
define( 'THEME_DIR', get_template_directory() );
define( 'THEME_URI', get_template_directory_uri() );

//include_once(THEME_DIR.'/includes/flms.php');
//include_once(THEME_DIR.'/includes/mailchimp.php');
//include_once(THEME_DIR.'/includes/woocommerce.php');

function register_hh_scripts() {
	//Theme version
	$version = wp_get_theme()->get('Version');
	//Deliver minified css for staging/prod
	$hh_dir = HH_URL.'/wp-content/themes/hh';
	//Main Stylesheet
	wp_enqueue_style( 'hh-main', $hh_dir.'/assets/css/main.css', false, $version, 'all' );
	wp_enqueue_script( 'hh-main', $hh_dir.'/assets/js/main.js', array('jquery'), $version, true);
	wp_enqueue_script( 'henry-bootstrap', HH_URL.'/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', array(), '5.2', true );
}
add_action( 'wp_enqueue_scripts', 'register_hh_scripts' );

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
	<meta name="theme-color" content="##191102" />
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

//add_filter( 'nav_menu_link_attributes','add_woo_data_to_link', 10, 3 );
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

add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3; // 3 products per row
	}
}