<?php 

add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3; // 3 products per row
	}
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

function custom_add_my_account_endpoints() {
   // Main endpoint
   add_rewrite_endpoint( 'treat-boxes', EP_ROOT | EP_PAGES );

}
add_action( 'init', 'custom_add_my_account_endpoints' );

function custom_my_account_menu_items_ordered( $items ) {
    // Save the original items
    $new_items = [];

    // Add Dashboard first
    if ( isset( $items['dashboard'] ) ) {
        $new_items['dashboard'] = $items['dashboard'];
        unset( $items['dashboard'] );
    }

    // Add My Boxes next
    $new_items['treat-boxes'] = __( 'Treat Boxes', 'your-textdomain' );

    // Add the rest (in original order, minus dashboard)
    foreach ( $items as $key => $item ) {
        if ( $key !== 'treat-boxes' ) {
            $new_items[ $key ] = $item;
        }
    }

    return $new_items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items_ordered' );

function my_boxes_query_vars( $vars ) {
    $vars[] = 'treat-boxes';
    $vars[] = 'box_id';
    return $vars;
}
add_filter( 'query_vars', 'my_boxes_query_vars' );

function custom_my_account_boxes_content() {
    global $wp_query;

    // Get sub-path after "treat-boxes"
    $sub_path = $wp_query->get( 'treat-boxes' );
    $box_info = explode('edit/',$sub_path);
    $box_id = 0;
    if(isset($box_info[1])) {
        $box_id = absint($box_info[1]);
    }
    
    if ( $sub_path === 'new-box' ) {
         new_box_form();
    }  elseif ( $box_id > 0  ) {
        // Show default "My Boxes"
        echo '<h2>Edit</h2>';
        display_edit_box_form($box_id);
    } elseif ( empty( $sub_path ) ) {
        // Show default "My Boxes"
        echo '<h2>Active Treat Boxes</h2>';
        list_user_boxes();
        echo '<a class="button" href="' . esc_url( wc_get_account_endpoint_url( 'treat-boxes' ) . 'new-box/' ) . '">Add New Box</a>';
    } else {
        // Handle unknown subpages if needed
        echo '<p>Page not found.</p>';
    }
}
add_action( 'woocommerce_account_treat-boxes_endpoint', 'custom_my_account_boxes_content', 10 );

function new_box_form() {
    wc_print_notices();
    if ( !isset($_POST['submit_new_box']) ) {
        // Show "Add New Box"
        echo '<h2>Add New Treat Box</h2>';
        
        ?>
        <form method="post" id="add-new-box-form">
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="box_name">Box Name</label>
                <span class="d-block"><em>&ldquo;&rsquo;s Box&rdquo; will be appended on your public page. <br>For example. If you set &lsquo;Doug&rsquo; it will be displayed as &ldquo;Doug&rsquo;s Box&rdquo;</em></span>
                <input type="text" class="input-text" name="box_name" id="box_name" required />
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <?php $hh_box_count = get_next_box_number(); ?>
                <label for="box_number">Box Number</label>
                <span class="d-block"><em>Pick any box number you&rsquo;d like, the default is the next box number available.</em></span>
                <input type="number" class="input-text" name="box_number" id="box_number" required value="<?php echo $hh_box_count; ?>" />
                
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="location">Location</label>
                <span class="d-block"><em>Be as vague or specific as you&rsquo;re comfortable with.</em></span>
                <input type="text" class="input-text" name="location" id="location" />
            </p>

            <div id="shelters-wrapper" class="mt-3">
                <label>Shelters We Support</label>
                <div><em>Add links to the shelters or rescues you&rsquo;d like to support. These will appear on your box&rsquo;s page so visitors can learn more or donate.</em></div>
                <div class="shelter-group">
                    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                        <label>Shelter Name</label>
                        <input type="text" class="input-text" name="shelters[0][name]" />
                    </p>
                    <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
                        <label>Shelter Link</label>
                        <input type="url" class="input-text" name="shelters[0][link]" />
                    </p>
                    <p>
                        <button type="button" class="remove-shelter" style="display: none;">Remove</button>
                    </p>
                    <div class="clear"></div>
                </div>
            </div>

            <p>
                <button type="button" id="add-shelter" class="add-shelter">+ Add Another Shelter</button>
            </p>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="about">Take a moment to share why you started this treat box</label>
                <span class="d-block"><em>Your story will be displayed so visitors can learn more about the inspiration behind it.</em></span>
                <?php
                $default = '<strong>This treat box is part of Hearts and Hounds</strong>, a community project spreading kindness to local pups while raising awareness for shelter dogs in need. Help yourself to a treat for your furry friend, and take a moment to check out the featured rescue nearby. Every tail wag helps share their story!';
                wp_editor(
                    $default, // Initial content
                    'about-your-box', // Editor ID
                    array(
                        'textarea_name' => 'about-your-box',
                        'media_buttons' => false,
                        'textarea_rows' => 6,
                        'quicktags' => false,
                        'teeny' => true, // Simplified editor
                        'tinymce' => array( 
                            'content_css' => HH_URL.'/wp-content/themes/hh/assets/css/editor.css',
                        )
                    )
                ); ?>
            </p>

            
        

            <p>
                <button type="submit" name="submit_new_box" class="button mt-2 mb-4">Publish Treat Box</button>
            </p>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let shelterCount = 1;

                function createShelterGroup(index) {
                    const div = document.createElement('div');
                    div.classList.add('shelter-group');
                    div.innerHTML = `
                        <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                            <input type="text" class="input-text" name="shelters[${index}][name]" />
                        </p>
                        <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
                            <input type="url" class="input-text name="shelters[${index}][link]" />
                        </p>
                        
                            <a class="remove-shelter">Remove</a>
                            <div class="clear"></div>
                        
                    `;
                    return div;
                }

                document.getElementById('add-shelter').addEventListener('click', function () {
                    const wrapper = document.getElementById('shelters-wrapper');
                    const newGroup = createShelterGroup(shelterCount++);
                    wrapper.appendChild(newGroup);
                });

                document.getElementById('shelters-wrapper').addEventListener('click', function (e) {
                    if (e.target && e.target.classList.contains('remove-shelter')) {
                        const group = e.target.closest('.shelter-group');
                        if (group) group.remove();
                    }
                });
            });
        </script>

        <?php 
    }
    echo '<a href="' . esc_url( wc_get_account_endpoint_url( 'treat-boxes' ) ) . '">← Back to All Treat Boxes</a>';
}
function handle_new_box_submission() {
    if ( isset($_POST['submit_new_box']) ) {
        $errors = array();
        $box_name   = sanitize_text_field($_POST['box_name']);
        $box_number = sanitize_text_field($_POST['box_number']);

        $hh_box_count = maybe_unserialize(get_option( 'hh_box_count', array() ));
        if(in_array($box_number, $hh_box_count)) {
            $box_number = get_next_box_number();
            $errors[] = 'The box number you specified is not available. You have been assigned a new box number but can edit your box to set a new number.';
        }
        

        $location   = sanitize_text_field($_POST['location']);
        
        // Allow safe HTML in About field
        $about = wp_kses_post($_POST['about-your-box']);

        // Shelters is a nested array
        $shelters = isset($_POST['shelters']) ? array_map(function($s) {
            return [
                'name' => sanitize_text_field($s['name'] ?? ''),
                'link' => esc_url_raw($s['link'] ?? '')
            ];
        }, $_POST['shelters']) : [];

        // Create custom post (e.g., 'box' post type)
        $post_id = wp_insert_post([
            'post_type'    => 'post',
            'post_title'   => $box_name,
            'post_name' => sanitize_title($box_name),  
            'post_content' => $about, // Store About content here
            'post_status'  => 'publish',
            'post_author'  => get_current_user_id(),
            'meta_input'   => [
                'box_number' => $box_number,
                'location'   => $location,
                'shelters'   => $shelters,
            ]
        ]);

        if ( $post_id ) {
            wp_mail('outreach@heartsandhounds.org','Treatbox Created!','Visit it here - '.get_permalink( $post_id ));
            //update box numbers
            //$lowest_boxget_next_box_number()
            $hh_box_count[] = $box_number;
            update_option('hh_box_count', $hh_box_count);

            wc_add_notice( 'Your treat box has been created! <a href="'.wc_get_account_endpoint_url( 'treat-boxes' ).'">View Your Treat Boxes</a>', 'success' );
            if(!empty($errors)) {
                wc_add_notice( implode('<br>',$errors), 'notice' );
            }
            //wp_redirect( wc_get_account_endpoint_url( 'treat-boxes' ) );
            
            //exit;
        } else {
            wc_add_notice( 'There was an error saving your box.', 'error' );
        }
    } else {

    }
}
add_action( 'woocommerce_account_treat-boxes_endpoint', 'handle_new_box_submission', 5 );

function get_next_box_number() {
    $hh_box_count = maybe_unserialize(get_option( 'hh_box_count', array() ));
    if (empty($hh_box_count)) {
        $lowest_value = 1; // Set to 1 if array is empty
    } else {
        sort($hh_box_count);
        $lowest_value = 1;
        foreach ($hh_box_count as $number) {
            if ($number == $lowest_value) {
                $lowest_value++;
            }
        }
        return $lowest_value;
        
    }
    return $lowest_value;
}

function list_user_boxes() {
    // Get the current user ID
    $user_id = get_current_user_id();
    
    if ( ! $user_id ) {
        echo 'Please log in to view your boxes.';
        return;
    }

    // Query to get 'box' custom post type posts by the current user
    $args = array(
        'post_type'      => 'post',           // Your custom post type
        'posts_per_page' => -1,              // Get all posts
        'post_status'    => 'publish',       // Only published posts
        'author'         => $user_id, 
        'orderby' => 'id',
        'order' => 'asc'       // Filter by the current user
    );

    // The Query
    $query = new WP_Query( $args );

    // Check if there are any posts
    if ( $query->have_posts() ) {
        echo '<div class="mb-1">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $box_id = get_the_ID();
            //echo '<li class="mb-2">';
            $views = 0;
            if(function_exists('pvc_get_post_views')) {
                $views = pvc_get_post_views($box_id);
            }
            echo '<div class="bg-brown pt-3 pb-3 ps-3 pe-3 rounded mb-3">';
                echo '<div class="d-flex flex-column flex-sm-row justify-content-between mb-2 mb-md-1">';
                    echo '<h4 class="mt-0 mb-0 me-2"><a href="' . esc_url( wc_get_account_endpoint_url( 'treat-boxes' ) . 'edit/' . $box_id ) . '" class="text-white text-decoration-none">' . get_the_title() . '</a></h4>';
                    echo '<div class="d-flex mt-1 mt-sm-0">';
                        echo '<a href="' . esc_url( wc_get_account_endpoint_url( 'treat-boxes' ) . 'edit/' . $box_id ) . '" class="rounded ps-2 pe-2 pt-1 pb-1 text-decoration-none treatbox-link me-2">Edit</a>';
                        echo '<a href="' . get_permalink( $box_id ) . '" class="rounded ps-2 pe-2 pt-1 pb-1 text-decoration-none treatbox-link">Visit</a>';
                    echo '</div>';
                echo '</div>';
                //echo '<h5>Visitors</h5>';
                echo '<div class="ms-3">';
                    if($views == 1) {
                        echo $views .' person has visited your treat box page';
                    } else {
                        echo $views .' people have visited your treat box page';
                    }
                    $shelters = get_post_meta( get_the_ID(), 'shelters', true );
                    $shelter_output = array();
                    $total_shelter_visits = 0;
                    if ( ! empty( $shelters ) && is_array( $shelters ) ) {
                        $link_counts = maybe_unserialize(get_post_meta($box_id, 'treatbox_link_counts',true));
                        foreach ( $shelters as $shelter ) {
                            // Display shelter name and link (if available)
                            $shelter_name = esc_html( $shelter['name'] );
                            $shelter_link = sanitize_url( $shelter['link'] );
                            if($shelter_link != '' && $shelter_name != '') {
                                $count = 0;
                                if(isset($link_counts[$shelter_link])) {
                                    $count = $link_counts[$shelter_link];
                                }
                                if($count == 1) {
                                    $shelter_output[] = '<div>'.$count .' person has visited '.$shelter_name.'</div>';
                                } else {
                                    $shelter_output[] = '<div>'.$count .' people have visited '.$shelter_name.'</div>';
                                }
                                $total_shelter_visits += $count;
                            }
                        }
                    }
                    if(!empty($shelter_output)) {
                        
                            echo implode('',$shelter_output);
                            
                        
                    }
                    if($total_shelter_visits > 0 || $views > 0) {
                        echo '<div class="mt-1">You&rsquo;re making a difference, great job!</div>';
                    }
                echo '</div>';
            echo '</div>';
            
            //echo '</li>';
        }
        echo '</div>';
    } else {
        echo '<p>You have not created any boxes yet.</p>';
    }

    // Reset Post Data
    wp_reset_postdata();
}


function display_edit_box_form($box_id) {
    // Get the current user ID
    $user_id = get_current_user_id();

    if ( ! $user_id ) {
        echo 'Please log in to edit your box.';
        return;
    }

    
    if ( $box_id ) {
        $box = get_post( $box_id );
        $permalink = get_permalink($box_id);
        // Check if the box exists and is authored by the current user
        if ( $box && $box->post_author == $user_id ) {
            // Get current post meta for the box
            $box_name   = get_the_title( $box_id );
            $box_number   = get_post_meta($box_id,'box_number',true);
            $location   = get_post_meta( $box_id, 'location', true );
            $about      = get_post_field('post_content',$box_id);
            $shelters   = get_post_meta( $box_id, 'shelters', true );
            $link = str_replace($box->post_name, 'box'.$box_number, $permalink);
            $link2 = 'https://mytreatbox.org/'.$box->post_name;
            $link3 = 'https://mytreatbox.org/box'.$box_number;
            $link4 = 'https://hhbox.org/'.$box->post_name;
            $link5 = 'https://hhbox.org/box'.$box_number;
            echo '<div class="mb-2 d-flex flex-column">
                <div>Direct Link: <a href="'.$permalink.'" title="View your box">'.$permalink.'</a></div>
                <div class="d-flex"><div>Easy Links:&nbsp;&nbsp;</div><div class="d-flex flex-column">
                    <a href="'.$link.'" title="View your box">'.str_replace('https://','',$link).'</a>
                    <a href="'.$link2.'" title="View your box">'.str_replace('https://','',$link2).'</a>
                    <a href="'.$link3.'" title="View your box">'.str_replace('https://','',$link3).'</a>
                    <a href="'.$link4.'" title="View your box">'.str_replace('https://','',$link4).'</a>
                    <a href="'.$link5.'" title="View your box">'.str_replace('https://','',$link5).'</a>
                </div></div>
            </div>';
            // Display the edit form
            ?>
            <form method="POST" id="edit-box-form">
                <input type="hidden" name="box_id" value="<?php echo $box_id; ?>" />
                
                <p class="mt-3 mb-0"><em><strong>Warning:</strong> Changing your box name and/or number will change the links to your page.</em></p>
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="box_name">Box Name</label>
                    <span class="d-block"><em>&ldquo;&rsquo;s Box&rdquo; will be appended on your public page. Currently displayed as &ldquo;<?php echo $box_name; ?>&rsquo;s Box&rdquo;</em></span>
                    <input type="text" class="input-text" name="box_name" value="<?php echo esc_attr( $box_name ); ?>" required>
                </p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <?php $hh_box_count = get_next_box_number(); ?>
                    <label for="box_number">Box Number</label>
                    <span class="d-block"><em>Pick any box number you&rsquo;d like, the default is the next box number available.</em></span>
                    <input type="number" class="input-text" name="box_number" id="box_number" required value="<?php echo absint($box_number); ?>" />
                </p>
                
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="location">Location</label>
                    <span class="d-block"><em>Be as vague or specific as you&rsquo;re comfortable with.</em></span>
                    <input type="text" class="input-text" name="location" value="<?php echo esc_attr( $location ); ?>">
                </p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="shelters">Shelters We Support</label>
                    <span class="d-block"><em>Add links to the shelters or rescues you&rsquo;d like to support. These will appear on your box&rsquo;s page so visitors can learn more or donate.</em></span>
                </p>
                    <div id="shelters-wrapper" class="mt-3">
                        <?php
                        if ( ! empty( $shelters ) && is_array( $shelters ) ) {
                            foreach ( $shelters as $index => $shelter ) {
                                ?>
                                <div class="shelter-group">
                                    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                                        <input type="text" class="input-text" name="shelters[<?php echo $index; ?>][name]" value="<?php echo esc_attr( $shelter['name'] ); ?>" placeholder="Shelter Name">
                                    </p>
                                    <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
                                        <input type="url" class="input-text" name="shelters[<?php echo $index; ?>][link]" value="<?php echo esc_url( $shelter['link'] ); ?>" placeholder="Shelter Link">
                                    </p>
                                    
                                    <a class="remove-shelter">Remove</a>
                                    <div class="clear"></div>
                                </div>
                                <div class="clear"></div>
                                <?php
                            }
                        }
                        ?>
                        
                    </div>
                
                <p>
                    <button type="button" id="add-shelter" class="add-shelter">+ Add Another Shelter</button>
                </p>
                
                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="about">Take a moment to share why you started this treat box</label>
                    <span class="d-block"><em>Your story will be displayed so visitors can learn more about the inspiration behind it.</em></span>
                    <?php
                    wp_editor(
                        $about, // Initial content
                        'about-your-box', // Editor ID
                        array(
                            'textarea_name' => 'about-your-box',
                            'media_buttons' => false,
                            'textarea_rows' => 6,
                            'quicktags' => false,
                            'teeny' => true, // Simplified editor
                            'tinymce' => array( 
                                'content_css' => HH_URL.'/wp-content/themes/hh/assets/css/editor.css',
                            )
                        )
                    );
                    ?>
                </p>
                
                
                <div class="clear"></div>
                
                <button type="submit" class="button mt-2 mb-4" id="save-box" name="update_box">Save Changes</button>
                <div class="mt-2 d-flex flex-md-row flex-column justify-content-between">
                    <?php echo '<a href="' . esc_url( wc_get_account_endpoint_url( 'treat-boxes' ) ) . '">← Back to All Treat Boxes</a>'; ?>
                    <div id="delete-box" class="display-link text-decoration-underline mt-4 mt-md-0">Delete this Treat Box</div>
                </div>
                
            </form>
            <script>
                document.getElementById('add-shelter').addEventListener('click', function() {
                    var wrapper = document.getElementById('shelters-wrapper');
                    var index = wrapper.querySelectorAll('.shelter-group').length;
                    var shelterGroup = document.createElement('div');
                    shelterGroup.classList.add('shelter-group');
                    shelterGroup.innerHTML = `
                        <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
                            <input type="text" class="input-text" name="shelters[${index}][name]" placeholder="Shelter Name">
                        </p>
                        <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
                            <input type="url" class="input-text" name="shelters[${index}][link]" placeholder="Shelter Link">
                        </p>
                        <a class="remove-shelter">Remove</a>
                    <div class="clear"></div>
                    `;
                    wrapper.appendChild(shelterGroup);
                });

                document.getElementById('shelters-wrapper').addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('remove-shelter')) {
                        e.target.closest('.shelter-group').remove();
                    }
                });
            </script>
            <?php
        } else {
            echo 'You do not have permission to edit this box.';
            echo '<a href="' . esc_url( wc_get_account_endpoint_url( 'treat-boxes' ) ) . '">← Back to All Treat Boxes</a>';
        }
    }
    
}

function handle_edit_box_submission() {
    if ( isset( $_POST['update_box'] ) && isset( $_POST['box_id'] ) ) {
        $box_id = intval( $_POST['box_id'] );
        $user_id = get_current_user_id();
        $errors = array();
        // Make sure the user is the author of the box
        $box = get_post( $box_id );
        if ( $box && $box->post_author == $user_id ) {

            if(isset($_POST['delete-box'])) {
                $current_box = get_post_meta($box_id,'box_number',true);
                if($current_box != '') {
                    $hh_box_count = maybe_unserialize(get_option( 'hh_box_count', array() ));
                    if(($key = array_search($current_box, $hh_box_count)) !== false) {
                        unset($hh_box_count[$key]);
                    }    
                    update_option('hh_box_count',$hh_box_count);
                }
                wp_delete_post($box_id, true);
                wc_add_notice('Your box has been permanantly deleted','success');
                wp_redirect( wc_get_account_endpoint_url( 'treat-boxes' ) );
                exit;
            } else {
                // Sanitize inputs
                $box_name = sanitize_text_field( $_POST['box_name'] );
                $location = sanitize_text_field( $_POST['location'] );
                $about    = wp_kses_post( $_POST['about-your-box'] );  // Allow HTML in the About field
                $shelters = isset($_POST['shelters']) ? array_map(function($s) {
                    return [
                        'name' => sanitize_text_field($s['name'] ?? ''),
                        'link' => esc_url_raw($s['link'] ?? '')
                    ];
                }, $_POST['shelters']) : [];

                $current_box = get_post_meta($box_id,'box_number',true);
                $box_number = sanitize_text_field($_POST['box_number']);
                if($current_box != $box_number) {
                    $hh_box_count = maybe_unserialize(get_option( 'hh_box_count', array() ));
                    if(($key = array_search($current_box, $hh_box_count)) !== false) {
                        unset($hh_box_count[$key]);
                    }    
                    if(in_array($box_number, $hh_box_count)) {
                        $box_number = get_next_box_number();
                        $errors[] = 'The box number you specified is not available. You have been assigned a new box number but can edit your box to set a new number.';
                    }
                    $hh_box_count[] = $box_number;
                    update_option('hh_box_count',$hh_box_count);
                }

                // Update the box post
                $post_data = array(
                    'ID'           => $box_id,
                    'post_title'   => $box_name,
                    'post_content' => $about,
                    'post_name' => sanitize_title($box_name),  
                );
                //'post_name' => 'box'.$box_number,
                wp_update_post( $post_data );

                // Update the post meta
                update_post_meta( $box_id, 'location', $location );
                update_post_meta( $box_id, 'box_number', $box_number );
                update_post_meta( $box_id, 'shelters', $shelters );

                //attempt to clear cache
                wp_engine_manual_cache_flush();
                
                // Display success message
                wc_add_notice( 'Your box has been updated!', 'success' );
                if(!empty($errors)) {
                    wc_add_notice( implode('<br>',$errors), 'notice' );
                }
                //wp_redirect( wc_get_account_endpoint_url( 'treat-boxes' ) );
                //exit;
            }
        }
    }
}
add_action( 'template_redirect', 'handle_edit_box_submission' );

function wc_custom_end_point_page_title( $title ) {
	global $wp_query;
  
	$is_endpoint = isset( $wp_query->query_vars['treat-boxes'] );
  
	if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
  
	  $title = __( 'Treat Boxes', 'woocommerce' );
  
	  remove_filter( 'the_title', 'wc_custom_end_point_page_title' );
	}
  
	  return $title;
  }
  add_filter( 'the_title', 'wc_custom_end_point_page_title' );

  add_action( 'woocommerce_before_account_navigation', function() {
    ?>
    <div class="woo-account-select-wrapper">
        <select class="woo-account-select select2">
            <?php
            $current_endpoint = WC()->query->get_current_endpoint();
            foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
                $url = wc_get_account_endpoint_url( $endpoint );
                $is_selected = ( $current_endpoint === $endpoint ) || ( empty( $current_endpoint ) && $endpoint === 'dashboard' );
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_url( $url ),
                    selected( $is_selected, true, false ),
                    esc_html( $label )
                );
            }
            ?>
        </select>
        </div>

    <script>
        jQuery(function($) {
            $('.woo-account-select').selectWoo({
                minimumResultsForSearch: Infinity
            }); // Initializes WooSelect

            $('.woo-account-select').on('change', function () {
                window.location.href = $(this).val();
            });
        });
    </script>
    <?php
} );


add_filter("woocommerce_get_query_vars", function ($vars) {

    foreach (["treat-boxes"] as $e) {
        $vars[$e] = $e;
    }

    return $vars;

});

add_action('wp_ajax_get_cart_total', 'get_cart_total');
add_action('wp_ajax_nopriv_get_cart_total', 'get_cart_total');
function get_cart_total() {
    $cart_count = '';
    if ( class_exists( 'woocommerce' ) ) {
        $cart_count = is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : '';
    }
    wp_send_json(array('cart_count' => $cart_count));
}

add_action('wp_ajax_update_treatbox_link_count', 'update_link_count');
add_action('wp_ajax_nopriv_update_treatbox_link_count', 'update_link_count');
function update_link_count() {
    $post_id = absint($_POST['post_id']);
    $user_id = absint($_POST['user_id']);
    $author = get_post_field( 'post_author', $post_id );
    //skip for people visiting their own pages
    if($author != $user_id) {
        $link = sanitize_url( $_POST['link'] );
        $shelter = trim(sanitize_text_field( stripslashes( $_POST['shelter'] ) ));
        //delete_post_meta($post_id, 'treatbox_link_counts'); //for testing
        $link_counts = maybe_unserialize(get_post_meta($post_id, 'treatbox_link_counts', true));
        if(!is_array($link_counts)) {
            $link_counts = array();
        }
        if(!isset($link_counts[$link])) {
            $link_counts[$link] = 0;
        }
        $link_counts[$link] += 1;
        update_post_meta($post_id,'treatbox_link_counts',$link_counts);

        //awards
        $awards = maybe_unserialize(get_post_meta($post_id,'treatbox_awards',true));
        if(!is_array($awards)) {
            $awards = array();
        }

        if($link_counts[$link] == 1) {
            if(!isset($awards['1_shelter_visits'])) {
                $awards['1_shelter_visits'] = 0;
            }
            $awards['1_shelter_visits'] += 1;
            trigger_reward($post_id, 'shelter_visits', 1, array('shelter' => $shelter));
        } else if($link_counts[$link] == 10) {
            if(!isset($awards['10_shelter_visits'])) {
                $awards['10_shelter_visits'] = 0;
            }
            $awards['10_shelter_visits'] += 1;
            trigger_reward($post_id, 'shelter_visits', 10, array('shelter' => $shelter));
        } else if($link_counts[$link] == 25) {
            if(!isset($awards['25_shelter_visits'])) {
                $awards['25_shelter_visits'] = 0;
            }
            $awards['25_shelter_visits'] += 1;
            trigger_reward($post_id, 'shelter_visits', 25, array('shelter' => $shelter));
        } else if($link_counts[$link] == 50) {
            if(!isset($awards['50_shelter_visits'])) {
                $awards['50_shelter_visits'] = 0;
            }
            $awards['50_shelter_visits'] += 1;
            trigger_reward($post_id, 'shelter_visits', 50, array('shelter' => $shelter));
        } else if($link_counts[$link] == 100) {
            if(!isset($awards['100_shelter_visits'])) {
                $awards['100_shelter_visits'] = 0;
            }
            $awards['100_shelter_visits'] += 1;
            trigger_reward($post_id, 'shelter_visits', 100, array('shelter' => $shelter));
        }
        update_post_meta($post_id,'treatbox_awards',$awards);
    }
    wp_die();
}

//stop authors from incrementing their own post views
add_filter('pvc_run_check_post', 'dont_update_viewcount_for_own_authors', 10, 2);
function dont_update_viewcount_for_own_authors($run, $post_id) {
    $user_id = get_current_user_id();
    $author = get_post_field( 'post_author', $post_id );
    if($author == $user_id) {
        $run = false;
    }
    return $run;
}

add_action('pvc_after_count_visit','add_views_award', 10, 1);
function add_views_award($post_id) {
    //awards
    $awards = maybe_unserialize(get_post_meta($post_id,'treatbox_awards',true));
    if(!is_array($awards)) {
        $awards = array();
    }
    $views = 0;
    if(function_exists('pvc_get_post_views')) {
        $views = pvc_get_post_views($post_id);
    }
    if($views == 1) {
        if(!isset($awards['1_page_visits'])) {
            $awards['1_page_visits'] = 0;
        }
        $awards['1_page_visits'] += 1;
        trigger_reward($post_id, 'page_visits', 1);
    } else if($views == 10) {
        if(!isset($awards['10_page_visits'])) {
            $awards['10_page_visits'] = 0;
        }
        $awards['10_page_visits'] += 1;
        trigger_reward($post_id, 'page_visits', 10);
    } else if($views == 25) {
        if(!isset($awards['25_page_visits'])) {
            $awards['25_page_visits'] = 0;
        }
        $awards['25_page_visits'] += 1;
        trigger_reward($post_id, 'page_visits', 25);
    } else if($views == 50) {
        if(!isset($awards['50_page_visits'])) {
            $awards['50_page_visits'] = 0;
        }
        $awards['50_page_visits'] += 1;
        trigger_reward($post_id, 'page_visits', 50);
    } else if($views == 100) {
        if(!isset($awards['100_page_visits'])) {
            $awards['100_page_visits'] = 0;
        }
        $awards['100_page_visits'] += 1;
        trigger_reward($post_id, 'page_visits', 100);
    } else if($views == 500) {
        if(!isset($awards['500_page_visits'])) {
            $awards['500_page_visits'] = 0;
        }
        $awards['500_page_visits'] += 1;
        trigger_reward($post_id, 'page_visits', 500);
    }
    update_post_meta($post_id,'treatbox_awards',$awards);
}

add_filter('pvc_enqueue_styles','__return_false');

add_shortcode('active_treatboxes', function() {
    $args = array(
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $the_query = new WP_Query($args);
    $return = '';
    if ($the_query->have_posts()) :
        $return .= '<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 mb-4">';
        while ($the_query->have_posts()): $the_query->the_post();
            $return .= '<div class="mb-4">';
                $return .= '<div class="bg-brown pt-3 pb-3 ps-3 pe-3 rounded h-100">';
                    $post_id = get_the_ID();
                    $return .= '<h3 class="h5 pt-0 pb-1 mt-0 mb-1 m-0 border-bottom border-white">'.get_the_title().'&rsquo;s Box</h3>';
                    $box = get_post_meta($post_id,'box_number',true);
                    $location = get_post_meta($post_id,'location',true);	
                    $return .= '<h4 class=" h5 pt-0 mb-0">Box #'.$box.'</h2>';      
                    if($location != '') {
                        $return .= '<h6 class="h6 mb-0 pt-1 mt-0">'.$location.'</h3>';
                    }
                    $return .= '<div class="mt-3">';
                        $return .= '<a href="'.get_permalink().'" title="'.get_the_title().'" class="rounded ps-2 pe-2 pt-1 pb-1 text-decoration-none treatbox-link me-2">Visit</a>';
                    $return .= '</div>';
                $return .= '</div>';
            $return .= '</div>';
        endwhile; 
        $return .= '</div>';
        wp_reset_postdata();
    endif;

    $return .= '<div class="d-block d-md-nones bg-white p-3 rounded  pt-3 mb-2">';
        $return .= '<p class="mb-0 p-0"><strong>Do you have a treat box?</strong> Sign up for a free Hearts &amp; Hounds account to support your local shelter too!</p>';
        $return .= '<a href="' . trailingslashit(get_bloginfo('url')) . 'my-account/" class="button me-2">Register or Sign Up</a>';
    $return .= '</div>';
    return $return;
});

//tratbox updates
add_action( 'woocommerce_edit_account_form', 'hh_add_treat_box_checkbox_to_account' );
function hh_add_treat_box_checkbox_to_account() {
    $user_id = get_current_user_id();
    $checked = get_user_meta( $user_id, 'hh_treat_box_updates', true );
    ?>
    <fieldset>
        <legend>Treat Box Updates</legend>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label>
                <input type="checkbox" name="hh_treat_box_updates" value="1" <?php checked( $checked, '1' ); ?> />
                Send me alerts about badges earned for my treat box page
            </label>
        </p>
    </fieldset>
    <?php
}
add_action( 'woocommerce_save_account_details', 'hh_save_treat_box_checkbox_to_account', 12, 1 );
function hh_save_treat_box_checkbox_to_account( $user_id ) {
    $value = isset($_POST['hh_treat_box_updates']) ? '1' : '0';
    update_user_meta( $user_id, 'hh_treat_box_updates', $value );
}

//automatically opt in users to notifications
add_action( 'user_register', 'hh_set_treat_box_updates_on_registration' );
function hh_set_treat_box_updates_on_registration( $user_id ) {
    update_user_meta( $user_id, 'hh_treat_box_updates', '1' );
}

//count comments
function hh_update_unique_commenter_count( $comment_id ) {
    $comment = get_comment( $comment_id );
    $post_id = $comment->comment_post_ID;

    // Only count if comment is approved
    if ( $comment->comment_approved != '1' ) {
        return;
    }

    // Get post author
    $post_author_id = get_post_field( 'post_author', $post_id );

    // Get all approved comments on this post
    $args = array(
        'post_id' => $post_id,
        'status'  => 'approve',
        'type'    => 'comment',
    );

    $comments = get_comments( $args );

    $unique = [];

    foreach ( $comments as $c ) {
        // Skip comments by the post author (either logged-in or matching email)
        if (
            ( $c->user_id && $c->user_id == $post_author_id ) ||
            ( $c->comment_author_email === get_the_author_meta( 'user_email', $post_author_id ) )
        ) {
            continue;
        }

        // Track by email first, fallback to user_id if available
        $key = $c->user_id ? 'user_' . $c->user_id : 'email_' . strtolower( trim( $c->comment_author_email ) );
        $unique[ $key ] = true;
    }

    $count = count( $unique );

    //update_post_meta( $post_id, 'hh_unique_commenter_count', $count );

    $awards = maybe_unserialize(get_post_meta($post_id,'treatbox_awards',true));
    if(!is_array($awards)) {
        $awards = array();
    }
    if($count == 1) {
        if(!isset($awards['1_comments'])) {
            $awards['1_comments'] = 0;
        }
        $awards['1_comments'] += 1;
        trigger_reward($post_id, 'comments', 1);
    } else if($count == 10) {
        if(!isset($awards['10_comments'])) {
            $awards['10_comments'] = 0;
        }
        $awards['10_comments'] += 1;
        trigger_reward($post_id, 'comments', 10);
    } else if($count == 25) {
        if(!isset($awards['25_comments'])) {
            $awards['25_comments'] = 0;
        }
        $awards['25_comments'] += 1;
        trigger_reward($post_id, 'comments', 25);
    } else if($count == 50) {
        if(!isset($awards['50_comments'])) {
            $awards['50_comments'] = 0;
        }
        $awards['50_comments'] += 1;
        trigger_reward($post_id, 'comments', 50);
    } else if($count == 100) {
        if(!isset($awards['100_comments'])) {
            $awards['100_comments'] = 0;
        }
        $awards['100_comments'] += 1;
        trigger_reward($post_id, 'comments', 100);
    } else if($count == 500) {
        if(!isset($awards['500_comments'])) {
            $awards['500_comments'] = 0;
        }
        $awards['500_comments'] += 1;
        trigger_reward($post_id, 'comments', 500);
    }
    update_post_meta($post_id,'treatbox_awards',$awards);
}

// On new comment that is auto-approved
add_action( 'comment_post', function( $comment_id, $approved ) {
    if ( $approved == 1 ) {
        hh_update_unique_commenter_count( $comment_id );
    }
}, 20, 2 );

// On manual approval later
add_action( 'wp_set_comment_status', function( $comment_id, $status ) {
    if ( $status === 'approve' ) {
        hh_update_unique_commenter_count( $comment_id );
    }
}, 20, 2 );

function trigger_reward($post_id, $award_type, $award_count, $extra_details = array()) {
    $author = get_post_field( 'post_author', $post_id );
    $checked = get_user_meta( $author, 'hh_treat_box_updates', true );
    if($checked == 1) {
        $user = get_user_by('id', $author);
        $link = '<a href="'.get_permalink($post_id).'" title="Visit your treat box page">Visit your treat box page</a>';
        if($user !== false) {
            $to = $user->user_email;
            switch($award_type) {
                case 'comments':
                    if($award_count == 1) {
                        $subject = 'Your got your first comment at your treat box!';
                        $message = '<p>Your treat box page just got it&rsquo;s first comment, great job!</p>';
                        $message .= $link.' to view your reward. While you&rsquo;re there, you can respond to the commend.';
                        $message .= ' Generating more comments will get people involved with your treat box and drive more clicks to the shelters you support.</p>';
                    } else {
                        $subject = 'People are getting chatty at your treat box!';
                        $message = '<p>Your treat box page just passed '.$award_count.' comments, great job!</p>';
                        $message .= $link.' to view your reward. While you&rsquo;re there, you can respond to any open comments.';
                        $message .= ' Someone took the time out of the day to let you know how much they care, why not return the favor?</p>';
                    }
                    break;
                case 'shelter_visits':
                    if($award_count == 1) {
                        $subject = 'You just sent your first visitor to a shelter!';
                        if(isset($extra_details['shelter'])) {
                            $message = '<p>Someone just clicked off your treat box page to learn more about &ldquo;'.$extra_details['shelter'].'&rdquo;—great job!</p>';
                        } else {
                            $message = '<p>Someone just clicked off your treat box page to learn more about one of your shelters, great job!</p>';
                        }
                    } else {
                        $subject = 'You&rsquo;re sending a lot of traffic to your shelter!';
                        if(isset($extra_details['shelter'])) {
                            $message = '<p>Your shelter &ldquo;'.$extra_details['shelter'].'&rdquo; just passed '.$award_count.' clicks, great job!</p>';
                        } else {
                            $message = '<p>One of your shelters just passed '.$award_count.' clicks, great job!</p>';
                        }
                    }
                
                    $message .= '<p>Animal shelters need all the support we can give them, and you&rsquo;re definitely doing your part! ';
                    $message .= $link.' to view your reward. While you&rsquo;re there, you can post an update to your treat box visitors about all the good your box is doing.</p>';
                    break;
                case 'page_visits':
                    if($award_count == 1) {
                        $subject = 'You just got your first visitor to your treat box page!';
                        $message = '<p>Someone just visited your treat box page, great job!</p>';
                        $message .= '<p>'.$link.' to view your reward. Don&rsquo;t forget to stock your treat box, you&rsquo;re in for more visitors to come!</p>';
                    } else {
                        $subject = 'Your treat box is getting hot!';
                        $message = '<p>Your treat box page just passed '.$award_count.' visits, great job!</p>';
                        $message .= '<p>'.$link.' to view your reward. If you know someone who may also like a treat box, why not pass along a link while you&rsquo;re at it?</p>';
                    }
                    break;
            }
            $message .= '<p>Together, small moments like these make a big difference. We&rsquo;re so glad you&rsquo;re here to be part of it,</p><p>Hearts &amp; Hounds</p>';

            $message .= '<p>&nbsp;</p><p><em>To stop receiving treat box notifications, <a href="'.trailingslashit(get_bloginfo('url')).'my-account/edit-account/" title="Update Account">edit your account details</a> and unsubscribe from treat box updates.</p>';
        }

        send_woo_email( $to, $subject, $message );
    }
}

function send_woo_email( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
	if( ! class_exists( 'WC_Emails' ) ) { include_once WC_ABSPATH . 'includes/class-wc-emails.php'; }
	$mailer  = WC()->mailer();
	$message = $mailer->wrap_message( $subject, $message );
	return $mailer->send( $to, $subject, $message, $headers, $attachments );
}