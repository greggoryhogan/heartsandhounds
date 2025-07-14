<?php get_header(); ?>
<?php 
if ( have_posts() ) {
	while ( have_posts() ) {
    	the_post(); 
		$post_id = get_the_ID();
		$box = get_post_meta($post_id,'box_number',true);
		$shelters = get_post_meta( get_the_ID(), 'shelters', true );
		$location = get_post_meta($post_id,'location',true);	
		$shelter_output = array();
		if ( ! empty( $shelters ) && is_array( $shelters ) ) {
							
			foreach ( $shelters as $shelter ) {
				// Display shelter name and link (if available)
				$shelter_name = esc_html( $shelter['name'] );
				$shelter_link = esc_url( $shelter['link'] );
				if($shelter_link != '' && $shelter_name != '') {
					$shelter_output[] = '<a href="' . $shelter_link . '" target="_blank" class="button me-2">' . $shelter_name . '&nbsp;&nbsp;&rarr;</a>';
				}
			}
		}
		echo '<div class="container-xl pt-4 box-details">';
			echo '<div class="row">';
				echo '<div class="col-12 col-md-4">';
					echo '<div class="box-info">';
						echo '<h1 class="pt-0 pb-1 mt-0 mb-1 m-0 border-bottom">'.get_the_title().'&rsquo;s Box</h1>';
						echo '<h2 class="pt-0 mb-0">Box #'.$box.'</h2>';
						
						if($location != '') {
							echo '<h3 class="mb-0 pt-1 mt-0">'.$location.'</h3>';
						}
						
						if(!empty($shelter_output)) {
							echo '<h3 class="mb-0 pt-1 mt-0">Shelters we support:</h3>';
							echo '<div class="d-flex flex-wrap">'.implode(' ',$shelter_output).'</div>';
						
						}
					echo '</div>';	
				echo '</div>';	
				echo '<div class="col-12 col-md-8 pt-3 pt-md-0 box-member-content">';
					the_content();
				echo '</div>';
			echo '</div>';

			if ( comments_open() || get_comments_number() ) :
				echo '<div class="row">';
					echo '<div class="col-12 col-md-8 offset-md-4 offset-0 pt-3 mt-md-3 mt-2 pt-md-5">';
						echo '<div class="bg-white rounded p-3 comments-section">';
							comments_template();  // This loads the default WordPress comment template
						echo '</div>';
					echo '</div>';
				echo '</div>';
			endif;

			if(!empty($shelter_output)) {
				echo '<div class="row">';
					echo '<div class="col-12 col-md-8 offset-md-4 offset-0 pt-3 mt-md-0 mt-2 pt-md-4">';
						echo '<div class="d-block d-md-nones bg-white p-3 rounded  pt-3">';
							echo '<p class="mb-0 p-0"><strong>Every bit helps.</strong> Consider donating to one of these shelters if you&rsquo;re able.</p>';
							//echo '<h3 class="mb-0 pt-1 mt-0">Shelters we support:</h3>';
							echo '<div class="d-flex flex-wrap">'.implode(' ',$shelter_output).'</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}

			echo '<div class="row">';
				echo '<div class="col-12 col-md-8 offset-md-4 offset-0 pt-3 mt-md-0 mt-2 pt-md-4">';
					echo '<div class="d-block d-md-nones bg-white p-3 rounded  pt-3">';
						echo '<p class="mb-0 p-0"><strong>Do you have a treat box?</strong> Sign up for a free Hearts &amp; Hounds to support your local shelter too!</p>';
						//echo '<h3 class="mb-0 pt-1 mt-0">Shelters we support:</h3>';
						echo '<a href="' . trailingslashit(get_bloginfo('url')) . 'my-account/" class="button me-2">Register or Sign Up</a>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	} // end while
} // end if
?>
<?php get_footer(); ?>