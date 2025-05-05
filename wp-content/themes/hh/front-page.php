<?php get_header(); ?>
<?php 
/*if ( have_posts() ) {
	while ( have_posts() ) {
    	the_post(); 
		the_content();
	} // end while
}*/ // end if
echo '<div class="container">';
	echo '<div class="row align-items-md-center  position-relative">';
		echo '<div class="col-9 col-sm-8 col-md-7">';
			echo '<h1 class="pt-4 mb-3 m-0 treat-text">Helping Shelter <span>Dogs</span> one treat at a time</h1>';
		echo '</div>';
		echo '<div class="col-4 offset-md-0 col-md-5 mt-4 mt-md-0 bone-container">';
			echo '<img src="'.THEME_URI .'/assets/img/accents/bone-xl.png" />';
		echo '</div>';
	echo '</div>';
	echo '<div class="mt-2 mt-md-5">';
		echo '<p><strong>At Hearts and Hounds, we believe every dog deserves love, comfort, and a forever home.</strong> That&rsquo;s why we&rsquo;ve combined two simple ideas—community kindness and canine compassion—into one mission. Through neighborhood dog treat boxes, we offer a small joy to every pup that passes by, while shining a light on dogs still waiting for a family of their own.</p>';

		echo '<p><strong>Each treat box is more than a snack stop—it&rsquo;s a story station.</strong> Alongside the biscuits and bones, you&rsquo;ll find information and QR codes that link directly to donation pages, adoption listings, and local shelters in need. Our goal is to make helping shelter dogs as easy as grabbing a treat for your own. Whether you donate, share, or simply take a moment to learn about the dogs featured, every act of kindness helps.</p>';

		echo '<p><strong>We believe awareness creates change. Not everyone can adopt, but everyone can care.</strong> By placing our treat boxes in everyday places—front yards, parks, sidewalks—we keep the conversation going. Shelter dogs are not forgotten; they&rsquo;re part of the community, too. Our boxes invite neighbors to think about rescue in a low-pressure, feel-good way.</p>';

		//echo '<p><strong>Hearts and Hounds is completely grassroots and community-powered.</strong> There&rsquo;s no big company behind us—just people who love dogs and believe in doing good. We partner with shelters, and we design each sign and sticker with purpose. Donations go straight to rescue organizations, and every featured dog has a real story and a real need.</p>';

		echo '<p><strong>Together, we can turn daily walks into small acts of rescue.</strong> With every wagging tail and every shared link, we help build awareness, spark compassion, and maybe—just maybe—bring a few more dogs home.</p>';
	echo '</div>';
echo '</div>';
?>
<?php get_footer(); ?>