<?php get_header(); ?>
<?php 
/*if ( have_posts() ) {
	while ( have_posts() ) {
    	the_post(); 
		the_content();
	} // end while
}*/ // end if
echo '<div class="container-xl">';
	echo '<div class="row align-items-md-center  position-relative">';
		echo '<div class="col-9 col-sm-8 col-md-7">';
			echo '<h1 class="pt-4 mb-3 m-0 treat-text">Helping <span>Shelter Dogs</span> one treat at a time</h1>';
		echo '</div>';
		echo '<div class="col-4 offset-md-0 col-md-5 mt-4 mt-md-0 bone-container">';
			echo '<img src="'.THEME_URI .'/assets/img/accents/bone-xl.png" />';
		echo '</div>';
	echo '</div>';
echo '</div>';
echo '<div class="container-xl">';
	echo '<div class="mt-2 mt-md-5 pb-md-5 pb-0 mb-5">';
		
		echo '<div class="row flex-md-row align-items-center">';
			echo '<div class="col-md-7">';
				echo '<h2>At Hearts and Hounds, <br>we believe every dog deserves<br> love, comfort, and a forever home.</h2>';
				echo '<p>That&rsquo;s why we&rsquo;ve combined two simple ideas—community kindness and canine compassion—into one mission. Through neighborhood dog treat boxes, we offer a small joy to every pup that passes by, while shining a light on dogs still waiting for a family of their own.</p>';
				echo '<a href="/about" class="button button-large button-red">Learn about the mission</a>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

	
echo '<div class="bg-brown pt-md-5 pb-md-5 pb-2 pt-3">';
	echo '<div class="container-xl pt-3 pb-3">';
		echo '<div class="row flex-column flex-md-row align-items-md-center">';

			echo '<div class="col-md-4 col-8 pb-3 pb-md-0 offset-md-0 offset-2">';
				echo '<img src="'.THEME_URI .'/assets/img/accents/treatbox-drawing-shadows.png" loading="lazy" />';
			echo '</div>';

			echo '<div class="col-md-7 offset-md-1 text-center">';
				
				echo '<h2>Each treat box is more than a snack stop—<br>it&rsquo;s a story station.</h2>';
				echo '<p>Alongside the biscuits and bones, you&rsquo;ll find information about donation pages, adoption listings, and local shelters in need. Our goal is to make helping shelter dogs as easy as grabbing a treat for your own. Whether you donate, share, or simply take a moment to learn about the dogs featured, every act of kindness helps.</p>';

				//echo '<h2>Not everyone can adopt, but everyone can care.</h2>';
				//echo '<p>We believe awareness creates change. By placing our treat boxes in everyday places—front yards, parks, sidewalks—we keep the conversation going. Shelter dogs are not forgotten; they&rsquo;re part of the community, too. Our boxes invite neighbors to think about rescue in a low-pressure, feel-good way.</p>';

			echo '</div>';
			
		echo '</div>';
	echo '</div>';
		

		
		//echo '<p><strong>Hearts and Hounds is completely grassroots and community-powered.</strong> There&rsquo;s no big company behind us—just people who love dogs and believe in doing good. We partner with shelters, and we design each sign and sticker with purpose. Donations go straight to rescue organizations, and every featured dog has a real story and a real need.</p>';
	
echo '</div>';


	echo '<div class="container-md text-center mt-5">';

			echo '<h2>Together, we can turn daily walks into small acts of rescue.</h2>';
			echo '<p>Not everyone can adopt, but everyone can care. By registering your existing treat box, you can help others learn how to to support shelter animals in their community, and give shelters the extra support they need to run successfully. Invite your neighbors to think about rescue in a low-pressure, feel-good way.</p>';
			echo '<p>With every wagging tail and every shared link, we help build awareness, spark compassion, and maybe -just maybe- bring a few more dogs home.</p>';
			
			echo '<a href="/my-account/treat-boxes/" class="button button-large button-red">Register Your Treat Box</a>';
	echo '</div>';

?>
<?php get_footer(); ?>