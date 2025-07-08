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
        // Show "Add New Box"
        echo '<h2>Add New Treat Box</h2>';
        
        ?>
        <form method="post" id="add-new-box-form">
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="box_name">Box Name</label>
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
        echo '<a href="' . esc_url( wc_get_account_endpoint_url( 'treat-boxes' ) ) . '">← Back to All Treat Boxes</a>';
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
add_action( 'woocommerce_account_treat-boxes_endpoint', 'custom_my_account_boxes_content' );

function handle_new_box_submission() {
    if ( isset($_POST['submit_new_box']) ) {
        $errors = array();
        $box_name   = sanitize_text_field($_POST['box_name']);
        $box_number = sanitize_text_field($_POST['box_number']);

        $hh_box_count = get_option( 'hh_box_count', array() );
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
            'post_name' => 'box'.$box_number,
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

            wc_add_notice( 'Your treat box has been created!!', 'success' );
            if(!empty($errors)) {
                wc_add_notice( implode('<br>',$errors), 'notice' );
            }
            wp_redirect( wc_get_account_endpoint_url( 'treat-boxes' ) );
            
            exit;
        } else {
            wc_add_notice( 'There was an error saving your box.', 'error' );
        }
    }
}
add_action( 'woocommerce_account_treat-boxes_endpoint', 'handle_new_box_submission', 5 );

function get_next_box_number() {
    $hh_box_count = get_option( 'hh_box_count', array() );
    if (empty($hh_box_count)) {
        $lowest_value = 1; // Set to 1 if array is empty
    } else {
        $lowest_value = min($hh_box_count) + 1; // Find the lowest value if the array is not empty
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
        'author'         => $user_id,        // Filter by the current user
    );

    // The Query
    $query = new WP_Query( $args );

    // Check if there are any posts
    if ( $query->have_posts() ) {
        echo '<ul>';
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<li class="mb-2">';
            echo '<a href="' . esc_url( wc_get_account_endpoint_url( 'treat-boxes' ) . 'edit/' . get_the_ID() ) . '">' . get_the_title() . '</a>';
            echo '</li>';
        }
        echo '</ul>';
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
                    <input type="text" class="input-text" name="location" value="<?php echo esc_attr( $location ); ?>" required>
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
                    $hh_box_count = get_option( 'hh_box_count', array() );
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
                    $hh_box_count = get_option( 'hh_box_count', array() );
                    if(($key = array_search($current_box, $hh_box_count)) !== false) {
                        unset($hh_box_count[$key]);
                    }    
                    if(in_array($box_number, $hh_box_count)) {
                        $box_number = get_next_box_number();
                        $errors[] = 'The box number you specified is not available. You have been assigned a new box number but can edit your box to set a new number.';
                    }
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