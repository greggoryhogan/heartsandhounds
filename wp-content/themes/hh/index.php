<?php get_header(); ?>
<?php 
if ( have_posts() ) {
	while ( have_posts() ) {
    	the_post(); 
		echo '<div class="container">';
			echo '<h1 class="pt-4 mb-3 m-0">'.get_the_title().'</h1>';
			the_content();
		echo '</div>';
	} // end while
} // end if
?>
<?php get_footer(); ?>