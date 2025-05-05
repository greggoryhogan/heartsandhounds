<?php get_header(); ?>
<?php 

echo '<div class="container-lg">';
	echo '<div class="text-center">';
		echo '<h1 class="pt-4 pb-2 mb-4 m-0 border-bottom">Uh oh! This pup lost the trail.</h1>';
		echo '<p>The page you&rsquo;re looking for wandered off or never existed.</p>';
		echo '<p>Sniff your way back to <a href="'.get_bloginfo('url').'">home</a> and try again!</p>';
	echo '</div>';
	the_content();
echo '</div>';	
?>
<?php get_footer(); ?>