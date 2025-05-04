<?php get_header(); ?>
<?php 
if ( have_posts() ) {
	while ( have_posts() ) {
    	the_post(); 
		echo '<div class="container">';
			echo '<div class="text-center">';
				echo '<h1 class="pt-4 pb-2 mb-4 m-0 border-bottom">'.get_the_title().'</h1>';
			echo '</div>';
			the_content();
		echo '</div>';
	} // end while
} // end if
?>
<?php get_footer(); ?>