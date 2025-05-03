</main>
<footer class="footer pt-4 pb-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-2 offset-md-5 col-4 offset-4 mb-sm-3 mb-2">
                <a href="/" title="Home" class="logo" title="Visit Hearts & Hounds homepage">
                    <img src="<?php echo THEME_URI; ?>/assets/img/logo-circle.png" alt="Hearts & Hounds logo" class="d-block" />
                </a>
            </div>
            <div class="col-md-12 col-12">
                <div class="d-flex flex-md-row flex-column align-items-center nav-container justify-content-center">
                    <?php hh_wp_nav_menu('menu-left', 2, 'd-flex flex-sm-row flex-column'); ?>
                    <?php hh_wp_nav_menu('menu-right', 2, 'd-flex flex-sm-row flex-column'); ?>
                </div>
            </div>
        </div>
        <div class="text-center mt-2">
            <a href="mailto:outreach@heartsandhounds.org?subject=I want to help!" title="email us">outreach@heartsandhounds.org</a>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>