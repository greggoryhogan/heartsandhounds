<?php get_header(); ?>
<?php 
if ( have_posts() ) {
	while ( have_posts() ) {
    	the_post(); 
		$post_id = get_the_ID();
		$box = get_post_meta($post_id,'box_number',true);
		echo '<div class="container pt-4 box-details">';
			echo '<div class="row">';
				echo '<div class="col-12 col-md-4">';
					echo '<div class="box-info">';
						echo '<h1 class="pt-0 pb-1 mt-0 mb-1 m-0 border-bottom">'.get_the_title().'</h1>';
						echo '<h2 class="pt-0 mb-0">Box #'.$box.'</h2>';
						$location = get_post_meta($post_id,'location',true);	
						if($location != '') {
							echo '<h3 class="mb-0 pt-1 mt-0">'.$location.'</h3>';
						}
						$shelters = get_post_meta( get_the_ID(), 'shelters', true );
			
						// Check if there are shelters associated with this box
						if ( ! empty( $shelters ) && is_array( $shelters ) ) {
							$shelter_output = array();
							foreach ( $shelters as $shelter ) {
								// Display shelter name and link (if available)
								$shelter_name = esc_html( $shelter['name'] );
								$shelter_link = esc_url( $shelter['link'] );
								if($shelter_link != '' && $shelter_name != '') {
									$shelter_output[] = '<a href="' . $shelter_link . '" target="_blank" class="button me-2 mb-2">' . $shelter_name . '</a>';
								}
							}
							if(!empty($shelter_output)) {
								echo '<h3 class="mb-0 pt-1 mt-0">Shelters we support:</h3>';
								echo '<div class="d-flex">'.implode(' ',$shelter_output).'</div>';
							}
						}
					echo '</div>';	
				echo '</div>';	
				echo '<div class="col-12 col-md-8 pt-3 pt-md-0">';
					the_content();

					
					
				echo '</div>';
			echo '</div>';
			
		echo '</div>';
	} // end while
} // end if
?>
<?php get_footer(); ?>