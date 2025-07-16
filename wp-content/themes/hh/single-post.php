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
					$shelter_output[] = '<a href="' . $shelter_link . '" target="_blank" class="button me-2 shelter-link">' . $shelter_name . '</a>';
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
						//awards
						$awards = maybe_unserialize(get_post_meta($post_id,'treatbox_awards',true));
						//print_r($awards);
						if(!is_array($awards)) {
							$awards = array();
						}				
						if(!empty($awards)) {
							echo '<div class="d-flex flex-wrap treatbox-page-awards mt-4">';
								//foreach($awards as $award) {
								$counts = array(500, 100, 50, 25, 10, 1);
								foreach($counts as $count) {
									if(isset($awards[$count.'_page_visits'])) {
										echo '<div class="d-flex align-items-center text-center award-container w-100">';
											echo '<div class="award brown"></div>';
											if($count == 1) {
												echo '<div class="award-text brown"><span class="count">'.$count.'+</span> Treat Box Visit</div>';
											} else {
												echo '<div class="award-text brown"><span class="count">'.$count.'+</span> Treat Box Visits</div>';
											}
											/*if(absint($awards[$count.'_page_visits']) > 1) {
												echo '<div class="award-count"> <span class="x">x</span> '.$awards[$count.'_page_visits'].'</div>';
											}*/
											
										echo '</div>';
										break;
									}
								}
								foreach($counts as $count) {
									if(isset($awards[$count.'_comments'])) {
										echo '<div class="d-flex align-items-center text-center award-container w-100">';
											echo '<div class="award tan"></div>';
											if($count == 1) {
												echo '<div class="award-text tan"><span class="count">'.$count.'+</span> Comment</div>';
											} else {
												echo '<div class="award-text tan"><span class="count">'.$count.'+</span> Comments</div>';
											}
											/*if(absint($awards[$count.'_shelter_visits']) > 1) {
												echo '<div class="award-count"> <span class="x">x</span> '.$awards[$count.'_shelter_visits'].'</div>';
											}*/
											
										echo '</div>';
										break;
									}
								}
								foreach($counts as $count) {
									if(isset($awards[$count.'_shelter_visits'])) {
										echo '<div class="d-flex align-items-center text-center award-container w-100">';
											echo '<div class="award red"></div>';
											if($count == 1) {
												echo '<div class="award-text red"><span class="count">'.$count.'+</span> Shelter Visit</div>';
											} else {
												echo '<div class="award-text red"><span class="count">'.$count.'+</span> Shelter Visits</div>';
											}
											/*if(absint($awards[$count.'_shelter_visits']) > 1) {
												echo '<div class="award-count"> <span class="x">x</span> '.$awards[$count.'_shelter_visits'].'</div>';
											}*/
											
										echo '</div>';
										break;
									}
								}
							echo '</div>';
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
						echo '<p class="mb-0 p-0"><strong>Do you have a treat box?</strong> Sign up for a free Hearts &amp; Hounds account to support your local shelter too!</p>';
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