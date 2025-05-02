<!DOCTYPE html>
<?php if (!defined('ABSPATH')) exit; ?>
<html <?php language_attributes(); ?>>
<head>
<?php wp_head(); ?>
</head>
<body <?php body_class('bootstrap-wrapper'); ?>>
<header class="site-header">
    <div class="container">
        <div class="row">
			<div class="col-md-6 col-10 logo-container">
                <a href="/" title="Home" class="home-link text-reset text-decoration-none position-relative d-flex align-items-center">
                    <img src="<?php echo THEME_URI; ?>/assets/img/pup-cropped.png" class="" alt="Beacon Hill Financial Educators Logo"/>
                    <h1>Hearts<span>&amp; Hounds</span></h1>
                </a>
            </div>
            <!--<div class="col-sm-6 col-md-7 col-lg-7 col-4 d-mobile"></div>
            <div class="col-2 d-mobile">
                <div class="mobile-nav">
                    <button class="nav-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>-->
			<div class="col-md-6 col-2 site-navigation"><?php hh_wp_nav_menu(); ?></div>
		</div>
    </div>
    <nav class="d-mobile">
        <div class="container">
            <?php //hh_wp_nav_menu(); ?>
        </div>
    </nav>
</header>
<main role="main" id="Main">
    