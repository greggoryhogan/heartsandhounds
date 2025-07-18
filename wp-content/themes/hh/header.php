<!DOCTYPE html>
<?php if (!defined('ABSPATH')) exit; ?>
<html <?php language_attributes(); ?>>
<head>
<?php wp_head(); ?>
</head>
<body <?php body_class('bootstrap-wrapper'); ?>>
<header class="site-header pt-3" id="navbar" >
    <div class="container-xl">
        <div class="row">
            <div class="col-md-5 d-none d-lg-block">
                <?php hh_wp_nav_menu('menu-left', 2, 'justify-content-end menu-left'); ?>
            </div>
            <div class="col-lg-2 col-3">
                <a href="/" class="logo" title="Hearts & Hounds homepage">
                    <img src="<?php echo THEME_URI; ?>/assets/img/logo-circle.png" alt="Hearts & Hounds logo"/>
                </a>
            </div>
            <div class="col-md-5  d-none d-lg-block">
                <?php hh_wp_nav_menu('menu-right',2,'menu-right'); ?>
            </div>
            <div class="col-9 col-lg-10 d-flex d-lg-none align-items-center justify-content-end">
                <input type="checkbox" id="menu" />
                <label for="menu" class="hamburger" data-bs-toggle="collapse" href="#mobile-nav" role="button" aria-expanded="false" aria-controls="mobile-nav">
                    <div></div>
                </label>
            </div>
		</div>
    </div>
    <div class="collapse container-xl" id="mobile-nav">
        <div class="d-flex flex-column d-lg-none">
            <?php hh_wp_nav_menu('menu-left', 2, 'd-flex flex-column'); ?>
            <?php hh_wp_nav_menu('menu-right', 2, 'd-flex flex-column'); ?>
        </div>
    </div>
    
</header>
<main role="main" id="main">
    