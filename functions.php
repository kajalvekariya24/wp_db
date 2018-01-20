<?php

    if(is_admin()) {
        require_once get_template_directory() . '/admin/admin.php';
    }

    define( 'STM_TEMPLATE_URI', get_template_directory_uri() );
    define( 'STM_TEMPLATE_DIR', get_template_directory() );
    define( 'STM_THEME_SLUG', 'stm' );
    define( 'STM_INC_PATH', get_template_directory() . '/inc' );
    define( 'STM_CUSTOMIZER_PATH', get_template_directory() . '/inc/customizer' );
    define( 'STM_CUSTOMIZER_URI', get_template_directory_uri() . '/inc/customizer' );

    //  Include path
    $inc_path = get_template_directory() . '/inc';

    //  Widgets path
    $widgets_path = get_template_directory() . '/inc/widgets';


    define('motors', 'motors');

        // Theme setups
        require_once STM_CUSTOMIZER_PATH . '/customizer.class.php';

        // Custom code and theme main setups
        require_once( $inc_path . '/setup.php' );

        // Enqueue scripts and styles for theme
        require_once( $inc_path . '/scripts_styles.php' );

        // Custom code for any outputs modifying
        require_once( $inc_path . '/custom.php' );

        // Required plugins for theme
        require_once( $inc_path . '/tgm/tgm-plugin-registration.php' );

        // Visual composer custom modules
        if ( defined( 'WPB_VC_VERSION' ) ) {
            require_once( $inc_path . '/visual_composer.php' );
        }

        // Custom code for any outputs modifying with ajax relation
        require_once( $inc_path . '/stm-ajax.php' );

        // Custom code for filter output
        //require_once( $inc_path . '/listing-filter.php' );
        require_once( $inc_path . '/user-filter.php' );

        //User
        if(stm_is_listing()) {
            require_once( $inc_path . '/user-extra.php' );
        }

        require_once( $inc_path . '/user-vc-register.php' );

        require_once( $inc_path . '/stm_single_dealer.php' );

        // Custom code for woocommerce modifying
        if( class_exists( 'WooCommerce' ) ) {
            require_once( $inc_path . '/woocommerce_setups.php' );
            if(stm_is_rental()) {
                require_once( $inc_path . '/woocommerce_setups_rental.php' );
            }
        }

        //Widgets
        require_once( $widgets_path . '/socials.php' );
        require_once( $widgets_path . '/text-widget.php' );
        require_once( $widgets_path . '/latest-posts.php' );
        require_once( $widgets_path . '/address.php' );
        require_once( $widgets_path . '/dealer_info.php' );
        require_once( $widgets_path . '/car_location.php' );
        require_once( $widgets_path . '/similar_cars.php' );
        require_once( $widgets_path . '/car-contact-form.php' );
        require_once( $widgets_path . '/contacts.php' );
        if(stm_is_boats()) {
            require_once( $widgets_path . '/schedule_showing.php' );
            require_once( $widgets_path . '/car_calculator.php' );
        }


class WPSE_OR_Query extends WP_Query 
{       
    protected $meta_or_tax  = FALSE;
    protected $tax_args     = NULL;
    protected $meta_args    = NULL;

    public function __construct( $args = array() )
    {
        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 10 );
        add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 10 );
        parent::__construct( $args );
    }

    public function pre_get_posts( $qry )
    {       
        remove_action( current_filter(), array( $this, __FUNCTION__ ) );            
        // Get query vars
        $this->meta_or_tax = ( isset( $qry->query_vars['meta_or_tax'] ) ) ? $qry->query_vars['meta_or_tax'] : FALSE;
        if( $this->meta_or_tax )
        { 
            $this->tax_args = ( isset( $qry->query_vars['tax_query'] ) ) ? $qry->query_vars['tax_query'] : NULL;
            $this->meta_args = ( isset( $qry->query_vars['meta_query'] ) ) ? $qry->query_vars['meta_query'] : NULL;
            // Unset meta and tax query
            unset( $qry->query_vars['meta_query'] );
            unset( $qry->query_vars['tax_query'] );
        }
    }

    public function posts_clauses( $clauses )
    {       
        global $wpdb;       
        $field = 'ID';
        remove_filter( current_filter(), array( $this, __FUNCTION__ ) );    
        // Reconstruct the "tax OR meta" query
        if( $this->meta_or_tax && is_array( $this->tax_args ) &&  is_array( $this->meta_args )  )
        {
            // Tax query
            $tax_query = new WP_Tax_Query( $this->tax_args );
            $sql_tax = $tax_query->get_sql( $wpdb->posts, $field );
            // Meta query
            $meta_query = new WP_Meta_Query( $this->meta_args );
            $sql_meta = $meta_query->get_sql( 'post', $wpdb->posts, $field );
            // Where part
            if( isset( $sql_meta['where'] ) && isset( $sql_tax['where'] ) )
            {
                $t = substr( trim( $sql_tax['where'] ), 4 );
                $m = substr( trim( $sql_meta['where'] ), 4 );
                $clauses['where'] .= sprintf( ' AND ( %s OR  %s ) ', $t, $m );              
            }
            // Join/Groupby part
            if( isset( $sql_meta['join'] ) && isset( $sql_tax['join'] ) )
            {
                $clauses['join']    .= sprintf( ' %s %s ', $sql_meta['join'], $sql_tax['join'] );               
                $clauses['groupby'] .= sprintf( ' %s.%s ', $wpdb->posts, $field );
            }       
        }   
        return $clauses;
    }

}

/* Email From */
add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');
 
function new_mail_from($old) {
 return 'noreply@autokwt.com';
}
function new_mail_from_name($old) {
 return 'AUTOKWT';
}


/* Pricing table */
function pricing_table($atts) {

    $plan_id = $atts['plan_id'];
    global $wpdb;

    if($plan_id)
    {
        $results = $wpdb->get_results("SELECT * FROM wp_pmpro_membership_levels where id = $plan_id");
    }else{
        $results = $wpdb->get_results("SELECT * FROM wp_pmpro_membership_levels");
    }

    
    //$total_plans_row = (count($results) + 1) % 12 ;
    //echo 'total_plans_row '.$total_plans_row;
    $total_plans_row = $plan_id ? 6 : 2;
    //print_r($results);

    echo '<div class="col-md-'.$total_plans_row.' text-center heading_row">';
    echo '<div class="plan_title">&nbsp;</div>';
    echo '<div class="plan_description">
            <ul class="vehicle_details">
                <li>Active Listing</li>
                <li># of vehicle to start </li>
                <li># of vehicle to post</li>
                <li>Dashboard</li>
                <li>Image Upload Quality </li>
                <li>Image Upload per vehicle</li>
            </ul>
            <ul class="vehicle_discount">
                <li>Add-on Discount</li>
                <li>Car Add-on</li>
                <li>Motorcycle Add-on</li>
                <li>ATV Add-on</li>
            </ul>
            <ul class="vehicle_price">    
                <li>Add - Price on</li>
            </ul>
            <ul class="vehicle_details_pm">
                <li>Price Per Month</li>
            </ul>';
    echo '</div>';
    echo '</div>';    
    foreach ($results as $result) {
        echo '<div class="col-md-'.$total_plans_row.' text-center">';
        echo '<div class="plan_title">'.$result->name.'</div>';
        echo '<div class="plan_description">';
        echo '<ul>';
        echo '<li class="active_listing">';
          if ($result->no_active_vehicles == -1) {
            echo 'Unlimited';
          } else {
            echo $result->no_active_vehicles;
          }
        echo '</li>';
        echo '<li class="vehicle_to_start">';
          if ($result->minimum_vehicles == -1) {
            echo 'Unlimited';
          } else {
            echo $result->minimum_vehicles;
          }
        echo '</li>';
        echo '<li class="vehicle_to_post">'.$result->no_of_cars.'</li>';
        echo '<li class="dashboard">';
        echo ($result->is_show_dashboard == 1) ? 'Yes' : 'No' .'</li>';
        echo '<li class="image_upload_quality">';
               switch($result->img_upload_quality) {
                    case 0:
                        echo "HD Resolution";
                        break;
                    case 1:
                        echo "Thumbnail";
                        break;
                    case 2:
                        echo "Medium";
                        break;
                    case 3:
                        echo "Large";
                        break;
               }
        echo '</li>';
        echo '<li class="image_upload_per_vehicle">'.$result->no_of_img.'</li>';
        echo  $result->description;
        echo '</ul>';
        echo '</div>';

        echo '<div class="plan_price">'.$result->initial_payment.'</div>';
        echo '<div class="plan_btn"><a href="'.site_url().'/membership-account/membership-checkout/?level='.$result->id.'">Join Membership</a></div>';
        echo '</div>';
    }
    
    
}
add_shortcode( 'pricing_table', 'pricing_table' );




/* popup Pricing table */
function pricing_table_on_popup($atts) {

    $plan_id = $atts['plan_id'];
    global $wpdb;

    if($plan_id)
    {
        $results = $wpdb->get_results("SELECT * FROM wp_pmpro_membership_levels where id = $plan_id");
    }else{
        $results = $wpdb->get_results("SELECT * FROM wp_pmpro_membership_levels");
    }

    
    //$total_plans_row = (count($results) + 1) % 12 ;
    //echo 'total_plans_row '.$total_plans_row;
    $total_plans_row = $plan_id ? 6 : 2;
    //print_r($results);

    echo '<div class="col-md-'.$total_plans_row.' col-xs-'.$total_plans_row.' md-left">';
    echo '<div class="plan_title">&nbsp;</div>';
    echo '<div class="plan_description">
            <ul class="vehicle_details">
                <li class="hide">Active Listing</li>
                <li>Vehicles to list</li>
                <li class="hide"># of vehicle to post</li>
                <li>Dashboard</li>
                <li>Image Uploads</li>
                <li class="hide">Image Upload per vehicle</li>
            </ul>
            <ul class="vehicle_discount">
                <li>Add-ons</li>
                <li class="hide">Car Add-on</li>
                <li class="hide">Motorcycle Add-on</li>
                <li class="hide">ATV Add-on</li>
            </ul>
            <ul class="vehicle_price">    
                <li class="hide">Add - Price on</li>
            </ul>
            <ul class="vehicle_details_pm">
                <li class="hide">Price Per Month</li>
            </ul>';
    echo '</div>';
    echo '</div>';    
    foreach ($results as $result) {
        echo '<div class="col-md-'.$total_plans_row.' md-right">';
        echo '<div class="plan_title">'.$result->name.'</div>';
        echo '<div class="plan_description">';
        echo '<ul>';
        echo '<li class="dashboard">'.getMembershipCustomFields('no_of_cars').'</li>';

        echo '<li class="dashboard">';
        echo ($result->is_show_dashboard == 1) ? 'Yes' : 'No' .'</li>';

        echo '<li class="dashboard">'.$result->no_of_img.'</li>'; 
        echo '<li class="dashboard"> N/A </li>';

        echo '</ul>';
        echo '</div>';
        echo '<div class="plan_price">'.$result->initial_payment.'</div>';
        echo '<div class="plan_btn"><a href="'.site_url().'/membership-account/membership-checkout/?level='.$result->id.'">Join Membership</a></div>';
        echo '</div>';
        echo '<div class="mp-final-price">'.round($result->initial_payment).' <span>K.D.</span></div>';
    }
    
    
}
add_shortcode( 'pricing_table_on_popup', 'pricing_table_on_popup' );

/* ------------------------------------------------------------ */
/* Show Data on pricing step */

function save_form_data() {
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
        //print_r($_REQUEST['data']);
        global $wpdb;
        $queryString = $_REQUEST['data'];
        parse_str($queryString, $out);
        //print_r($queryString);
        $cur_meta_id = $out['current_id'];
        //print_r($_REQUEST['data']);
        //echo $cur_meta_id;

        $user_ID= get_current_user_id(); 
        echo $user_ID;  
        if($user_ID){
            if($cur_meta_id){
                $wpdb->update( 
                'wp_usermeta', 
                array( 
                    'meta_value' => $_REQUEST['data']
                ), 
                array( 'umeta_id' => $cur_meta_id ));

                // update_user_meta( $cur_meta_id, $user_ID, 'saved_form_data', $_REQUEST['data']);
            }else{
                add_user_meta( $user_ID, 'saved_form_data', $_REQUEST['data']);
            }
        }
    }
    // Always die in functions echoing ajax content
   die();
} 
add_action( 'wp_ajax_save_form_data', 'save_form_data' );

/* ------------------------------------------------------------ */


/* ------------------------------------------------------------ */
/* Delete Data on pricing step */

function delete_form_data() {
    // The $_REQUEST contains all the data sent via ajax
        $queryString = $_REQUEST['data'];
        parse_str($queryString, $out);
        //print_r($queryString);
        $cur_meta_id = $out['current_id'];

        global $wpdb;
        
        $wpdb->delete( 'wp_usermeta', array( 'umeta_id' => $cur_meta_id ) );

        
        // $user_ID= get_current_user_id();   
        // if($user_ID){
        //     update_user_meta( $user_ID, 'saved_form_data', 'NA');
        // }
    // Always die in functions echoing ajax content
   die();
} 
add_action( 'wp_ajax_delete_form_data', 'delete_form_data' );

/* ------------------------------------------------------------ */


function filter_ajax_request() {
 
    $queryString = $_POST['data'];
    parse_str($queryString, $out);
    //echo '<pre>'.print_r($out, 1).'</pre>';

    $moter = $out['moter'];
    $make = $out['make'];
    $serie = $out['serie'];
    $max_price = $out['max_price'];

    return include 'vc_templates/stm_listings_tabs_ajax.php';
    //echo $max_price;



   wp_die();
} 
add_action( 'wp_ajax_filter_ajax_request', 'filter_ajax_request' );
add_action("wp_ajax_nopriv_filter_ajax_request", "filter_ajax_request");



/* AJAX FUNCTION */
function prefix_ajax_add_car() {
    // Handle request then generate response using WP_Ajax_Response
    return 'Sample ';
    // Don't forget to stop execution afterward.
    wp_die();
}

add_action("wp_ajax_prefix_ajax_add_car", "prefix_ajax_add_car" );
add_action("wp_ajax_nopriv_prefix_ajax_add_car", "prefix_ajax_add_car");


add_filter( 'login_url', 'my_login_page', 10, 3 );
function my_login_page( $login_url, $redirect, $force_reauth ) {
    $login_page = home_url( '/loginregister/' );
    $login_url = add_query_arg( 'redirect_to', $redirect, $login_page );
    return $login_url;
}

function user_info()
{
    global $wpdb;
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    global $current_user;
    wp_get_current_user();
    $user_id = get_current_user_id();

    //echo $user_id;
    global $wp;
    $current_url = home_url(add_query_arg(array(),$wp->request));
    $args = array(
                    'post_type' => stm_listings_post_type(),
                    'paged'             =>  $paged,
                    'posts_per_page'    => 10,
                    'author'  => $current_user->ID,
                    'post_status' => array('pending','publish','draft'),
                );
    $recent_query = new WP_Query($args); 
    // echo '<pre>';
    // print_r($recent_query);

    $a=1; 

    $query = 'SELECT umeta_id,meta_value FROM wp_usermeta WHERE meta_key  = "saved_form_data" 
                AND user_id='.$user_id.' ORDER BY umeta_id DESC';
    //echo $query;            
    $results = $wpdb->get_results($query,ARRAY_N);

    // echo '<pre>';
    // print_r($results);

    foreach ($results as $result) {
        parse_str($result[1], $filled_fields);
        $auto_submit_your_add = $filled_fields['auto_submit_your_add'];
        $auto_how_many_cars = $filled_fields['auto_how_many_cars'];
        $auto_motor = $filled_fields['auto_motor'];
        $auto_transmission = $filled_fields['auto_transmission'];
        $auto_make = $filled_fields['auto_make'];
        $auto_series = $filled_fields['auto_series'];
        $auto_year = $filled_fields['auto_year'];
        $auto_condition = $filled_fields['auto_condition'];
        $auto_body = $filled_fields['auto_body'];
        $auto_have_photos = $filled_fields['auto_have_photos'];
        $auto_make_confirm = $filled_fields['auto_make_confirm'];
        $auto_registration_number = $filled_fields['auto_registration_number'];
        $last_step = $filled_fields['last_step'] ? $filled_fields['last_step'] : 'first';

        $all_json = json_encode($filled_fields);


        if($all_json)
        {   
            echo '<div class="lr-top lr-saved-list">
            <div class="lr-top-alpha"><span class="lr-botm-status red_dot">
                                <a href="#"><i class="fa fa-circle" aria-hidden="true"></i></a></span></div>
            <a data-popup-open="popup-1" href="#" data-info='.$all_json.' data-id='.$result[0].' class="show_step"><div class="lr-top-title"><h4>'. $auto_condition .' '.$auto_body.' '. $auto_series .' '.$auto_year.'</h4></div></a>
            </div>';
        }

        $a = $a +1;         
    }
   if ( $recent_query->have_posts() ) :
    while($recent_query->have_posts()) : $recent_query->the_post();

    if(get_post_status(get_the_id()) == 'pending' || get_post_status(get_the_id()) == 'draft') {
        $post_status_class='red_dot';
    }else{
        $post_status_class='green_dot';
    }
    ?>
    <div class="listing_row">

        <div class="lr-top">
            <div class="lr-top-alpha">
            
            <div class="lr-botm-status <?php echo $post_status_class; ?>">
            <?php
                if(get_post_status(get_the_id()) == 'pending')
                {
                    echo '<i class="fa fa-circle" aria-hidden="true"></i>';  
                }else{
                    if(get_post_status(get_the_id()) == 'draft'): ?>
                    <a href="<?php echo add_query_arg(array('stm_enable_user_car' => get_the_ID()),$current_url);?>">
                    <i class="fa fa-circle" aria-hidden="true"></i>
            </a>
            <?php else: ?>
                <a href="<?php echo add_query_arg(array('stm_disable_user_car' => get_the_ID()),$current_url);?>">
                    <i class="fa fa-circle" aria-hidden="true"></i>
                </a>
            <?php endif;
            }
            ?>
        </div>
         <div class="lr-botm-id"><span class="number"><?php  echo get_the_ID(); ?></span></div>
        
        </div>
            <div class="lr-top-title"><h4><?php the_title(); ?></h4></div>
        </div>
        <div class="lr-botm">
        <div class="lr-botm-status <?php echo $post_status_class; ?>">
            <?php
                if(get_post_status(get_the_id()) == 'pending')
                {
                    echo '<i class="fa fa-circle" aria-hidden="true"></i>';  
                }else{
                    if(get_post_status(get_the_id()) == 'draft'): ?>
                    <a href="<?php echo add_query_arg(array('stm_enable_user_car' => get_the_ID()),$current_url);?>">
                    <i class="fa fa-circle" aria-hidden="true"></i>
            </a>
            <?php else: ?>
                <a href="<?php echo add_query_arg(array('stm_disable_user_car' => get_the_ID()),$current_url);?>">
                    <i class="fa fa-circle" aria-hidden="true"></i>
                </a>
            <?php endif;
            }
            ?>
        </div>
        <div class="lr-botm-id"><span class="number"><?php  echo "ID : ".get_the_ID(); ?></span></div>
        <div class="lr-botm-type">
            <ul>
            <li>
                <?php if(get_post_status(get_the_id()) == 'draft') { ?>
                    <a href="#" class="disabled_control"><i class="fa fa-globe"></i></a>
                <?php 
                    } else {
                    $vehicleType = get_post_meta(get_the_id(),'vehicleType',true);
                    if($vehicleType == 'Car') { $viewUrl = get_permalink(); }
                    if($vehicleType == 'Buggy') { $viewUrl = site_url().'/buggy-view?post_id='.get_the_id();  }
                    if($vehicleType == 'Bike') { $viewUrl = site_url().'/bike-view?post_id='.get_the_id();  }
                ?>
                    <a href="<?php echo $viewUrl; ?>"><i class="fa fa-globe"></i></a>
                <?php } ?>    
            </li>

            <li>
            <?php 
            if(get_post_status(get_the_id()) == 'draft') {
                echo '<a href="#" class="disabled_control"><i class="fa fa-eye"></i></a>';  
            }else{
            if(get_post_status(get_the_id()) == 'draft'): ?>
            <a href="<?php echo add_query_arg(array('stm_enable_user_car' => get_the_ID()),$current_url);?>">
                <i class="fa fa-eye"></i>
            </a>
            <?php else: ?>
                <a href="<?php echo add_query_arg(array('stm_disable_user_car' => get_the_ID()),$current_url);?>">
                    <i class="fa fa-eye"></i>
                </a>
            <?php endif;
            }
            ?>
            </li> 
            <li>
              <a href="#"><i class="fa fa-envelope-o" aria-hidden="true"></i></a>
            </li>
            <li>
                <span class="copyLink" onclick="copyToClipboard('<?php echo get_permalink() ?>')" style="cursor:pointer;"><i class="fa fa-link" aria-hidden="true"></i></span>
            </li>    
            <li>
                <?php
                    /**
                     * Decide whether is it bike or car
                     */
                    if($vehicleType == 'Car') { $editUrl = site_url().'/car-edit/?post_id='.get_the_ID(); }
                    if($vehicleType == 'Bike') { $editUrl = site_url().'/bike-edit/?post_id='.get_the_ID(); } 
                    if($vehicleType == 'Buggy') { $editUrl = site_url().'/buggy-edit/?post_id='.get_the_ID(); }   
                 ?>
                <a href="<?php echo $editUrl; ?>" class="<?php if(get_post_status(get_the_id()) == 'pending') { echo 'disabled_control'; }?>">
                    <i class="fa fa-cog" aria-hidden="true"></i>  
                </a>
            </li>
            <li>
               <?php if(get_post_status(get_the_id()) == 'draft') { ?>
                    <a href="#" class="disabled_control"><i class="fa fa-print" aria-hidden="true"></i></a>  
                <?php } else { $printUrl = site_url().'/vehicle-printing/?post_id='.get_the_ID(); ?>   
                    <span onclick="printVehicleDetails('<?php echo $printUrl; ?>')" style="cursor:pointer"><i class="fa fa-print" aria-hidden="true"></i></span>
                <?php } ?>
            </li>
            <li>
                <?php if(get_post_status(get_the_id()) == 'draft') { ?>
                    <a href="#" class="disabled_control"><i class="fa fa-download" aria-hidden="true"></i></a>  
                <?php } else { if(function_exists('mpdf_pdfbutton')) mpdf_pdfbutton(true, '<i class="fa fa-download" aria-hidden="true"></i>', 'my login text'); } ?> 
           </li>
            </ul>
        </div>
        <div class="lr-botm-submi">
            Date of submission : <?php echo get_the_date(' F j ,Y'); ?><a class="stm-delete-confirmation <?php if(get_post_status(get_the_id()) == 'pending') { echo 'disabled_control'; }?>" href="<?php echo esc_url(add_query_arg(array('stm_move_trash_car' => get_the_ID()), stm_get_author_link(''))); ?>" data-title="<?php the_title(); ?>">
                        <?php // esc_html_e('Delete', 'motors'); ?>
                        <i class="fa fa-trash-o"></i>
            </a> 
        </div>
        </div>
        
    </div>
    <?php 
    $a = $a +1; 
    endwhile;?>
    <div class="alignleft"><?php echo get_previous_posts_link( 'prev' ); ?></div>
 
    <div class="alignright"><?php echo get_next_posts_link( 'next', $recent_query->max_num_pages ); ?></div>

<?php 
// clean up after our query
wp_reset_postdata(); 
?>

<?php else:  ?>
    <div class="nt-avilble">
          <div class="">
                         <!-- <span> <img src="http://autokwd.aistechnolabs.xyz/wp-content/uploads/2017/12/sad-face.png" alt="" width="90px"></span>
                         <br/> -->
                          <?php _e( 'You don\'t Have any published Ads,Add one Now !'); ?>
                    </div>
        </div>
      <!--   <script>
                        $(document).ready(function() {
  function setHeight() {
    windowHeight = $(window).innerHeight();
    $('.nt-avilble').css('min-height', windowHeight);
  };
  setHeight();
  
  $(window).resize(function() {
    setHeight();
  });
});

                    </script> -->

<?php endif; 
    global $post;
    $currentPageSlug = $post->post_name;
    $restrictionPage = array('motor-gallery','account','listings');
    if (!in_array($currentPageSlug,$restrictionPage)) {
        if(is_user_logged_in() && function_exists('pmpro_hasMembershipLevel') && pmpro_hasMembershipLevel()) {
            $totalPostAsPerPlan = getMembershipCustomFields('no_of_cars');
            $totalPosts = getAllUserListingTypePost();
            $remainingPosts = $totalPostAsPerPlan - $totalPosts;
            if ($remainingPosts <= 0 || $totalPostAsPerPlan <= 0) {
                echo "<div class='listing_row' style='border:0 !important'>
                            <div class='alert alert-danger'>
                                No Posts available <br />
                                <a href='".site_url().'/membership-account/membership-levels'."'>Renew Membership</a>
                            </div>
                        </div>";
            } else {
                     echo '<div class="add_more_cars_btn">
                            <a data-popup-open="popup-1" href="#" id="new_popup"><i class="fa fa-plus" aria-hidden="true"></i></a>
                      </div>'; 
            }
                
        } 
    }  
 }       

add_shortcode( 'user_info', 'user_info' );


/* Display left side data of user admin panel */
function user_sidebar(){
    
    global $current_user;
    //print_r($current_user);
    ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <ul class="account_links">
                <li><a href="<?php echo site_url().'/motor-gallery' ?>">
                    <i class="fa fa-car" aria-hidden="true"></i>
                    <span> Motor Gallery</span>
                </a></li>
                <li><a href="<?php echo site_url().'/my-favourite-list' ?>">
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <span> Favourite</span>
                </a></li>
                <li>
                    <?php
                        $header_listing_btn_link = get_theme_mod('header_listing_btn_link', '/add-car');
                        $header_listing_btn_text = get_theme_mod('header_listing_btn_text', esc_html__('Add your item', 'motors'));
                        if (!empty($header_listing_btn_link) and !empty($header_listing_btn_text)){
                            global $wpdb;
                            $user_ID= get_current_user_id(); 
                            $query = 'SELECT meta_value FROM wp_usermeta WHERE meta_key  = "saved_form_data" 
                            AND user_id='.$user_ID;
                            $results = $wpdb->get_results($query ,OBJECT );

                            // Check no of remaing post by membership plan
                            $totalPostAsPerPlan = getMembershipCustomFields('no_of_cars');
                            $totalPosts = getAllUserListingTypePost();
                        }
                        if(count($results) <= 0 && $totalPosts < $totalPostAsPerPlan){
                        ?>
                    <a href="#" data-popup-open="popup-1" id="sidebarAddVehicleBtn">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        <span> Add Vehicle</span>
                    </a>
                    <?php
                        } else {
                     ?>
                        <a href="<?php echo site_url(); ?>/listing">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            <span>Add Vehicle</span>
                        </a>
                     <?php } ?>
                </li>
                <li>
                    <a href="<?php echo site_url().'/media-library' ?>">
                        <i class="fa fa-file-image-o" aria-hidden="true"></i>
                        <span>Media Library</span>   
                    </a>
                </li>
                <li><a href="<?php echo site_url().'/account' ?>">
                    <i class="fa fa-cog" aria-hidden="true"></i>
                    <span> Account Settings</span>
                </a></li>
            </ul>
            
             <ul class="car-account">
             <?php global $current_user;   ?>
                <li>
                    <a href="#">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/bike.png" />
                        <span>
                           <?php
                                $customePostCountCar = array(
                                    'post_type'     => 'listings',
                                    'post_status'   => 'publish', 
                                    'posts_per_page' => -1,
                                    'author' => $current_user->ID,
                                    'tax_query' => array(
                                        array(
                                          'taxonomy' => 'moter',
                                          'terms' => '162'
                                    )
                                  )
                                );

                                $getTotalCarPosts = new WP_Query( $customePostCountCar);
                                echo $getTotalCarPosts->post_count
                                
                            ?>
                        </span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/car.png" />
                         <span>
                            <?php
                                $customePostCountCar = array(
                                    'post_type'     => 'listings',
                                    'post_status'   => 'publish', 
                                    'posts_per_page' => -1,
                                    'author' => $current_user->ID,
                                    'tax_query' => array(
                                        array(
                                          'taxonomy' => 'moter',
                                          'terms' => '161'
                                    )
                                  )
                                );

                                $getTotalCarPosts = new WP_Query( $customePostCountCar);
                                echo $getTotalCarPosts->post_count
                                
                            ?>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/scooter.png" />
                        <span>
                            <?php
                                $customePostCountBuggey = array(
                                    'post_type'     => 'listings',
                                    'post_status'   => 'publish', 
                                    'posts_per_page' => -1,
                                    'author' => $current_user->ID,
                                    'tax_query' => array(
                                        array(
                                          'taxonomy' => 'moter',
                                          'terms' => '163'
                                    )
                                  )
                                );

                                $getTotalBuggeyPosts = new WP_Query( $customePostCountBuggey);
                                echo $getTotalBuggeyPosts->post_count;
                            ?>
                        </span>
                    </a>
                </li>
            </ul>
            <ul class="car-account" style="display:none">
                <li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/yat.png" /><span>0</span></a></li>
                <li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/boat.png" /><span>4</span></a></li>
                <li><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/boat2.png" /><span>3</span></a></li>
            </ul>
            
        </div>
    </div>
    <div class="acco-logout"><a href="<?php echo wp_logout_url( home_url() ); ?>">
            <i class="fa fa-power-off" aria-hidden="true"></i>
            <span class="text">Log out</span>
            </a></div>
   </div> 
<?php 
} 
add_shortcode( 'user_sidebar', 'user_sidebar' );


/*Display User profile Pic on user admin panel page*/
function user_icon()
{
    //$user = wp_get_current_user();
    global $current_user;
    get_currentuserinfo();
    $user_id = $user->ID;
    $user_fields = stm_get_user_custom_fields( $user_id );
?>
    <div class="profile_picture sticky">
        <div class="acco-title">
                        <?php global $current_user;
                        wp_get_current_user();
                        echo $current_user->user_login;
                        
                        ?>
        </div>
    <div class="profile_area">
        <?php
        $link = add_query_arg(array('page_admin' => 'settings'), stm_get_author_link(''));
        $hide_empty = '';
        if(!empty($user_fields['image'])) 
        {
            $hide_empty = 'hide-empty';
            echo '<img src="'.esc_url($user_fields['image']).'" width="100%" />';
        } 
        else 
        {
            $hide_empty = 'hide-photo';
            echo '<img src="'.get_template_directory_uri().'/assets/images/gravataricon.png" width="100%" />';
        } ?>
        
        
    </div>
    
<?php 
}
add_shortcode( 'user_icon', 'user_icon' );
?>

<?php
function  year()
  {
      $year = get_post_meta( get_the_ID(), 'ca-year', true );
      if($year)
      {  
       echo $year;
      }
  }
add_shortcode( 'year', 'year' );
function  series()
  {
     $series = get_post_meta( get_the_ID(), 'serie', true );
     if($series)
      {  
      echo $series;
      }
  }
add_shortcode( 'series', 'series' );

function  autobody()
  {
      $body = get_post_meta( get_the_ID(), 'body', true );
      if($body)
      {   
      echo $body;
      }
  }
add_shortcode( 'autobody', 'autobody' );

  function  title()
  {
      $title = get_post_meta( get_the_ID(), 'title', true );
      if($title)
      {   
      echo $title;
      }
  }
add_shortcode( 'title', 'title' );

 function  register()
  {  
      
      $register = get_post_meta( get_the_ID(), 'registration_date', true );
      if ($register)
      {
      echo $register;
      }
  }
add_shortcode( 'register', 'register' );

 function  vinnumber()
  {
      $vin = get_post_meta( get_the_ID(), 'vin_number', true );
      if($vin)
      {  
      echo $vin;
      }
      
  }
add_shortcode( 'vinnumber', 'vinnumber' );

function  make()
  {
      $make = get_post_meta( get_the_ID(), 'make', true );
      if($make)
      {  
      echo $make;
      }
      
  }
add_shortcode( 'make', 'make' );
function  mileage()
  {
      $mileage = get_post_meta( get_the_ID(), 'mileage', true );
      if($mileage)
      {  
      echo $mileage;
      }
      
  }
add_shortcode( 'mileage', 'mileage' );

function  price()
  {
      $price = get_post_meta( get_the_ID(), 'price', true );
      if($price)
      {  
      echo $price;
      }
      
  }
add_shortcode( 'price', 'price' );
function  exteriorcolor()
  {
      $exteriorcolor = get_post_meta( get_the_ID(), 'exterior-color', true );
      if($exteriorcolor)
      {  
      echo $exteriorcolor;
      }
      
  }
add_shortcode( 'exteriorcolor', 'exteriorcolor' );

function  interiorcolor()
  {
      $interiorcolor = get_post_meta( get_the_ID(), 'interior-color', true );
      if($interiorcolor)
      {  
      echo $interiorcolor;
      }
      
  }
add_shortcode( 'interiorcolor', 'interiorcolor' );

function  engine()
  {
      $engine = get_post_meta( get_the_ID(), 'engine', true );
      if($engine)
      {  
      echo $engine;
      } 
      
  }
add_shortcode( 'engine', 'engine' );

function  fuel()
  {
      $fuel = get_post_meta( get_the_ID(), 'fuel-economy', true );
      if($fuel)
      {  
      echo $fuel;
      }
      
  }
add_shortcode( 'fuel', 'fuel' );

function  transmission()
  {
      $transmission = get_post_meta( get_the_ID(), 'transmission', true );
      if($transmission)
      {  
      echo $transmission;
      }
      
  }
add_shortcode( 'transmission', 'transmission' );

 
function  email()
  {
       $useremail = get_the_author_meta( 'user_email' ); 
      
   if($useremail)
   {
        echo  $useremail;
   }

      
  }
add_shortcode( 'email', 'email' );

function  nickname()
  {
      
       $nickname = get_the_author_meta('nickname');
 
     if($nickname)
     { 
        echo  $nickname;
     }
      
  }
add_shortcode( 'nickname', 'nickname' );

function  phone()
  {
      
       $phone = get_the_author_meta('stm_phone');
 
     if($phone)
     { 
        echo  $phone;
     }
      
  }
add_shortcode( 'phone', 'phone' );

   function SearchFilter($query) {
    if ($query->is_search) {
       $query->set('post_type', 'listings');
    }
    //echo "<pre>";
    //print_r($query);
        return $query;
}
add_filter('pre_get_posts','SearchFilter');

function ajaxChangePostStatus() {
   $post_id = $_POST['post_id'];
   $chageTo = $_POST['type'];
   if ($chageTo == 'do_active') {
        $user = wp_get_current_user();
        $totalActivePostAsPerPlan = getMembershipCustomFields('no_active_vehicles');
        $totalPosts = getUserActivePost();
        if ($totalActivePostAsPerPlan == -1) {
            $enable_car = array(
                'ID' => $post_id,
                'post_status' => 'publish'
            );
            echo wp_update_post($enable_car);   
        } else {
            if ($totalActivePostAsPerPlan > $totalPosts || $totalPosts <= 0) {
                    $enable_car = array(
                        'ID' => $post_id,
                        'post_status' => 'publish'
                    );
                  echo  wp_update_post($enable_car);

            } else {
                echo 0;       
            }
        }    

   } else {
         $disable_car = array(
                'ID' => $post_id,
                'post_status' => 'draft'
        );
        wp_update_post($disable_car);
        echo 0;
    }
   die();
}
 add_action( 'wp_ajax_ajaxChangePostStatus', 'ajaxChangePostStatus' ); 
 add_action( 'wp_ajax_nopriv_ajaxChangePostStatus', 'ajaxChangePostStatus' );

function profile()
{
      global $current_user;
      get_currentuserinfo();
     $userrole = get_the_author_meta( 'role' ); 
    $phone = get_the_author_meta( 'stm_phone' ); 
      $account = get_the_author_meta( 'account_status' ); 
      //$register = get_post_meta( get_the_ID(), 'registration_date', true );
     //$exp = get_post_meta( get_the_ID(), 'pmpro_ExpirationYear', true );
     //$exp= get_user_meta( $user_id, 'pmpro_ExpirationYear', true );
    //$permission = get_user_meta( $current_user->ID, 'pmpro_ExpirationYear' , true );
    $expirationyear = get_the_author_meta('pmpro_ExpirationYear');
    
    ?>
<div class="form-profile-dec">
<form method="post" id="editProfile" enctype="multipart/form-data">
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
          <label>First Name:</label><span> <input type="text" name="user_firstname" value=" <?php  echo  $current_user->user_firstname ?>"></span>
        </div>
     </div>
    <div class="col-sm-6">
        <div class="form-group">
          <label>Last Name:</label><span> <input type="text" name="user_lastname" value="<?php echo $current_user->user_lastname ?>"></span>
        </div>
     </div>
    
    <div class="col-sm-6">
        <div class="form-group">
          <label>Email:</label><span> <input type="text" name="user_email" value="<?php   echo  $current_user->user_email ?>"></span>
        </div>
     </div>
    <div class="col-sm-6">
        <div class="form-group">
          <label>Phone:</label><span> <input type="text" name="stm_phone" value="<?php  echo $current_user->stm_phone  ?>"></span>
        </div>
    </div>
     
     
    <div class="col-sm-12">
        <label>Biographical Info:</label>
        <br>
        <textarea name="description" rows="5" cols="40"><?php echo  $current_user->description ?></textarea>  
    </div>

    <div class="col-sm-12">
        <label>Profile Image:</label><br />
        <input type="file" name="prfImg" />  
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <label>Username:</label>
            <span> 
                <?php echo  $current_user->user_login ?>
            </span>
        </div> 
    </div>


</div>
  <input type="submit" name="submit" value="Submit">  
  </form>
</div>
<?php   
}
add_shortcode( 'profile', 'profile' );


//social medie

function socialmedia()
{
    $userId = get_current_user_id();

  ?>

  <form method="post" id="editsocialmedia">
  <div class="row">


  <div class="col-sm-12">
  <div class="form-group">
  <label>Facebook:</label><span> <input type="url" name="facebook" value="<?php echo get_user_meta($userId,'facebook',true); ?> " class="form-control" ></span>
 </div>
  </div>

  <div class="col-sm-12">
  <div class="form-group">
          <label>Twitter:</label><span> <input type="url" name="twitter" value=" <?php echo get_user_meta($userId,'twitter',true);?>" class="form-control" ></span>
        </div>
  </div>
    <div class="col-sm-12">
  <div class="form-group">
          <label>Linkedin:</label><span> <input type="url" name="linkdin" value=" <?php echo get_user_meta($userId,'linkdin',true); ?>" class="form-control" ></span>
        </div>
  </div>
  <div class="col-sm-12">
  <div class="form-group">
          <label>YouTube:</label><span> <input type="url" name="youtube" value=" <?php  echo  get_user_meta($userId,'youtube',true); ?>" class="form-control" ></span>
        </div>
  </div>
  <div class="col-sm-12">
  <div class="form-group"><label>Instagram:</label><span> <input type="url" name="instagram" value=" <?php  echo  get_user_meta($userId,'instagram',true); ?>" class="form-control"></span>
        </div>
  </div>
<div class="col-sm-12">
  <div class="form-group">
  <label>Pinterest:</label><span> <input type="url" name="pintrest" value=" <?php  echo get_user_meta($userId,'pintrest',true); ?>" class="form-control" ></span>
        </div>
  </div>
  <div class="col-sm-12">
  <div class="form-group">
          <label>Tumblr:</label><span> <input type="url" name="tumbler" value=" <?php  echo  get_user_meta($userId,'tumbler',true); ?>" class="form-control" ></span>
        </div>
  </div>

  <div class="form-group">
        <input type="submit" name="submit" value="Submit"> 
  </div>

</div>
</form>
 
<?php
}
add_shortcode( 'socialmedia', 'socialmedia' );
/**
 * Function is used to send email to seller of vehicle.
 * This function is called from sidebar
 */
function sendMessageToSeller() {
    $msg = $_POST['msg'];
    $postAuthorId = $_POST['author'];
    $authorEmail = get_the_author_meta('user_email',$postAuthorId);
    $postTitle = $_POST['post_title'];

    $mailSubject = "Inquiry of ".$postTitle;
    $sendMail = wp_mail($authorEmail,$mailSubject,$msg);
    echo $sendMail;
}
add_action('wp_ajax_sendMessageToSeller', 'sendMessageToSeller');
add_action('wp_ajax_nopriv_sendMessageToSeller', 'sendMessageToSeller');

/**
 * Function is used to get membership details from no of vehicles provided by user
 * Function is called when user tries to activate his account
 */

 function addSocialMedia(){
     global $current_user;
      get_currentuserinfo();
      $userId = get_current_user_id();
      //echo $userId;
      $facebook = $_POST['facebook'];  
      $twitter = $_POST['twitter'];
      $linkdin = $_POST['linkdin'];
      $youtube = $_POST['youtube'];
      $instagram = $_POST['instagram'];
      $pintrest = $_POST['pintrest'];
      $tumbler = $_POST['tumbler'];

    
       
    $getUserMetaFb = get_user_meta($userId,'facebook',true);
      if(empty($getUserMetaFb)){
            $facebook = add_user_meta($userId,'facebook',$facebook);
      } else {
            $facebook = update_user_meta($userId,'facebook',$facebook);
      }
   

    $getUserMetaTw = get_user_meta($userId,'twitter',true);
      if(empty($getUserMetaTw)){
            $twitter = add_user_meta($userId,'twitter',$twitter);
      } else {
            $twitter = update_user_meta($userId,'twitter',$twitter);
      }

      
    $getUserMetaLi = get_user_meta($userId,'linkdin',true);
      if(empty($getUserMetaLi)){
            $linkdin = add_user_meta($userId,'linkdin',$linkdin);
      } else {
            $linkdin = update_user_meta($userId,'linkdin',$linkdin);
      }


    $getUserMetaYou = get_user_meta($userId,'youtube',true);
      if(empty($getUserMetaYou)){
            $youtube = add_user_meta($userId,'youtube',$youtube);
      } else {
            $youtube = update_user_meta($userId,'youtube',$youtube);
      }

    $getUserMetaIn = get_user_meta($userId,'instagram',true);
      if(empty($getUserMetaIn)){
            $instagram = add_user_meta($userId,'instagram',$instagram);
      } else {
            $instagram = update_user_meta($userId,'instagram',$instagram);
      }

    $getUserMetaPin = get_user_meta($userId,'pintrest',true);
      if(empty($getUserMetaPin)){
            $pintrest = add_user_meta($userId,'pintrest',$pintrest);
      } else {
            $pintrest = update_user_meta($userId,'pintrest',$pintrest);
      }

    $getUserMetaTum = get_user_meta($userId,'tumbler',true);
      if(empty($getUserMetaTum)){
            $tumbler = add_user_meta($userId,'tumbler',$tumbler);
      } else {
            $tumbler = update_user_meta($userId,'tumbler',$tumbler);
      }
      echo 1;
      die();
     
}
add_action('wp_ajax_addSocialMedia', 'addSocialMedia');
add_action('wp_ajax_nopriv_addSocialMedia', 'addSocialMedia');



function getMembershipDetailsFromNoOfVehicles() {
    $vehicles = $_POST['vehicles'];
    global $wpdb;
    $getMembershipDtls = $wpdb->get_results("SELECT * FROM `wp_pmpro_membership_levels` WHERE `no_active_vehicles` = '".$vehicles."'");
    if (count($getMembershipDtls) > 0) {
        echo json_encode($getMembershipDtls);
    } else {
        $getRandomMembership = $wpdb->get_results("SELECT * FROM `wp_pmpro_membership_levels` WHERE `no_of_cars` >= '".$vehicles."' ORDER BY `no_of_cars` ASC LIMIT 1");
        echo json_encode($getRandomMembership);
    }
    die();
}

add_action('wp_ajax_getMembershipDetailsFromNoOfVehicles', 'getMembershipDetailsFromNoOfVehicles');
add_action('wp_ajax_nopriv_getMembershipDetailsFromNoOfVehicles', 'getMembershipDetailsFromNoOfVehicles');

function getVehicleModelFromMake() {
    global $wpdb;
    $make = $_POST['make'];
    $sqlGetModel = $wpdb->get_results("SELECT DISTINCT `model` FROM `vehicle_predifined_data` WHERE `make` = '".$make."'");
    if (sizeof($sqlGetModel) > 0) {
        foreach($sqlGetModel as $model) {
            $option .= "<option value='".$model->model."'>".ucwords($model->model)."</option>";
        }
    } else {
        $option .= "<option selected>Model Not Available</option>";
    }
    echo $option;
    die();
}
add_action('wp_ajax_getVehicleModelFromMake', 'getVehicleModelFromMake');
add_action('wp_ajax_nopriv_getVehicleModelFromMake', 'getVehicleModelFromMake');

function getVehicleLaunchYearFromModel() {
    $model = $_POST['model'];
    $option = "";
    global $wpdb;
    $sqlGetModelLaunchYear = $wpdb->get_results("SELECT DISTINCT `launch_year` FROM `vehicle_predifined_data` WHERE `model` = '".$model."'");
    if (sizeof($sqlGetModelLaunchYear) > 0) {
        foreach($sqlGetModelLaunchYear as $ly) {
            $option .= "<option value='".$ly->launch_year."'>".$ly->launch_year."</option>";
        }
    } else {
        $option .= "<option value='-1'>Launch year is not available</option>";
    }
    echo $option;
    die();
}

add_action('wp_ajax_getVehicleLaunchYearFromModel', 'getVehicleLaunchYearFromModel');
add_action('wp_ajax_nopriv_getVehicleLaunchYearFromModel', 'getVehicleLaunchYearFromModel');
    
    function updateUserProfile() {
        $response = array();
        $user_id = get_current_user_id();
       
        if(isset($_POST) && !isset($_FILES['prfImg'])){
            $updateData = array(
                'ID' => $user_id,
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['user_lastname'],
                'description' => $_POST['description'],
                'user_email' => $_POST['user_email'],
            );
            wp_update_user($updateData);
            update_usermeta($user_id,'stm_phone',$_POST['stm_phone']);
            $response['status'] = "Successfully updated!";
        }

        if(isset($_FILES['prfImg'])){
            $removeOldSql = get_user_meta($user_id,'stm_user_avatar_path',true);
            $imgArr = $_FILES['prfImg'];
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($imgArr, $upload_overrides);
            update_usermeta($user_id,'stm_user_avatar',$movefile['url']);
            update_usermeta($user_id,'stm_user_avatar_path',$movefile['file']);
            $response['url'] = $movefile['url'];
        }
        echo json_encode($response);
        die();
    }
    add_action('wp_ajax_updateUserProfile', 'updateUserProfile');
    add_action('wp_ajax_nopriv_updateUserProfile', 'updateUserProfile');

    function changePassword() {
        // echo "<pre>";
        // print_r($_POST);
        $oldPswd = $_POST['oldPswd'];
        $hashOldPswd = wp_hash_password($oldPswd);
        $user_id = get_current_user_id();
        $user = get_user_by( 'ID', $user_id );
        $error = false;
        $checkOldPassword = wp_check_password($oldPswd, $user->data->user_pass, $user->ID);
        if (!$checkOldPassword || $checkOldPassword == false){
            echo 1;
            $error = true;
        }

        if ($error == false) {
            wp_set_password($_POST['newPswd'],$user_id);
            echo 2;
        }
        die();
    }
    add_action('wp_ajax_changePassword', 'changePassword');
    add_action('wp_ajax_nopriv_changePassword', 'changePassword');

    function getBikeModelFromMake() {
        $bikeMake = $_POST['make'];
        global $wpdb;
        $option = "";
        $getModels = $wpdb->get_results("SELECT DISTINCT `model` FROM `bike_predefined_data` WHERE `make` = '".$bikeMake."'");
        if(sizeof($getModels) > 0){
            foreach($getModels as $model) {
                $option .= "<option value='".$model->model."'>".$model->model."</option>";
            }
        } else {
            $option .= "<option value='-1'>Model not available</option>";
        }
        echo $option;
    }

    add_action('wp_ajax_getBikeModelFromMake', 'getBikeModelFromMake');
    add_action('wp_ajax_nopriv_getBikeModelFromMake', 'getBikeModelFromMake');

    function add_theme_scripts_css(){
        wp_enqueue_script( 'script', get_template_directory_uri() . '/js/multi_step_form.js', array ( 'jquery' ), 1.1, true);
        wp_enqueue_script( 'jQueryForm', get_template_directory_uri() . '/js/jquery.form.js', array ( 'jquery' ), 1.1, true);
        wp_enqueue_script( 'jQuerySlider', get_template_directory_uri() . '/assets/slider/jquery.nstSlider.min.js', array ( 'jquery' ), 1.1, true);
    }
    add_action( 'wp_enqueue_scripts', 'add_theme_scripts_css' );

     /**
     * Function adds a bike and upload bike's images
     */
    function stm_add_bike() {
        
        $validationMsg = array(); 
        $error = false;
        $mainFeatures = str_replace("\\","",$_POST['vehicleMainFeatures']);
        $response = array();

        if(empty($_POST['make'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle make"; }
        if(empty($_POST['model'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle model"; }
        if(empty($_POST['bikeTravelled'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle travelled"; }  
        if(empty($_POST['category'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle category"; }  
        if(empty($_POST['vehicleLaunchYear'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle launch year"; } 
        if(empty($_POST['bikeExteriorColor'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle color"; }   
        if(empty($_POST['displacement'])){ $error = true; $validationMsg[]['message'] = "Enter displacement"; }   
        if(empty($_POST['vehicleEngine'])){ $error = true; $validationMsg[]['message'] = "Enter engine type"; }   
        if(empty($_POST['vehiclePower'])){ $error = true; $validationMsg[]['message'] = "Enter power"; }   
        if(empty($_POST['vehicleSpeed'])){ $error = true; $validationMsg[]['message'] = "Enter 0-100 KPH speed in second"; }   
        if(empty($_POST['vehicleTopSpeed'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle top speed"; }   
        if(empty($_POST['vehicleTorque'])){ $error = true; $validationMsg[]['message'] = "Enter torque"; }   
        if(empty($_POST['vehicleGearBox'])){ $error = true; $validationMsg[]['message'] = "Enter gearbox details"; }   
        if(empty($_POST['vehicleLocation'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle location"; }   
        if(empty($_POST['vehiclePrice'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle asking price"; }   
        if(empty($mainFeatures)) { $error = true; $validationMsg[]['message'] = "Select at least one feature"; }
        if($error == true){
            $response['validationMsg'] = $validationMsg;
        } else {
            $is_error_post = false;
            $postTitleCumName = $_POST['make']." ".$_POST['model']." ".$_POST['vehicleLaunchYear'];
            $totalActivePostAsPerPlan = getMembershipCustomFields('no_active_vehicles');
            $getUserActivePost = getUserActivePost();
            $postStatus = "";
            if ($totalActivePostAsPerPlan == -1) {
                $postStatus = 'publish';
            } elseif($totalActivePostAsPerPlan <= 0 || $getUserActivePost >= $totalActivePostAsPerPlan || !$totalActivePostAsPerPlan) {
                $postStatus = 'pending';
            } else {
                $postStatus = 'publish'; 
            }
            $postData = array(
                'post_type' => stm_listings_post_type(),
                'post_title' => $postTitleCumName,
                'post_status' => $postStatus,
                'post_name' => $postTitleCumName,
            );
            $postId = wp_insert_post($postData);
            if (!is_wp_error($postId)) {
                $user_id = get_current_user_id();
                add_post_meta($postId,'VehicleMake',$_POST['make']);
                add_post_meta($postId,'VehicleModel',$_POST['model']);
                add_post_meta($postId,'vehicleTravelled',$_POST['bikeTravelled']);
                add_post_meta($postId,'bikeCategory',$_POST['category']);
                add_post_meta($postId,'VehicleLaunchYear',$_POST['vehicleLaunchYear']);
                add_post_meta($postId,'bikeExteriorColor',$_POST['bikeExteriorColor']);
                add_post_meta($postId,'bikeDisplacement',$_POST['displacement']);
                add_post_meta($postId,'engine',$_POST['vehicleEngine']);
                add_post_meta($postId,'bikePower',$_POST['vehiclePower']);
                add_post_meta($postId,'bikeSpeed',$_POST['vehicleSpeed']);
                add_post_meta($postId,'bikeTopSpeed',$_POST['vehicleTopSpeed']);
                add_post_meta($postId,'bikeTorque',$_POST['vehicleTorque']);
                add_post_meta($postId,'bikeGearBox',$_POST['vehicleGearBox']);
                add_post_meta($postId,'bikeLocation',$_POST['vehicleLocation']);
                add_post_meta($postId,'bikeMainFeatures',$mainFeatures);
                add_post_meta($postId,'bikeOtherFeatures',$_POST['vehicleOtherFeatures']);
                add_post_meta($postId,'bikeSellerNotes',$_POST['sellerNotes']);
                add_post_meta($postId,'vehicleType','Bike');
                add_post_meta($postId,'stm_bike_user',$user_id);
                add_post_meta($postId,'bikeVinNo',$_POST['vehicleVinNo']);
                add_post_meta($postId,'bikeShape',$_POST['bikeShape']);
                add_post_meta($postId,'vehiclePrice',$_POST['vehiclePrice']);

                /**
                 * Not Showing Row restriction strat
                 */

                if(isset($_POST['isShowMakeModel']) || !empty($_POST['isShowMakeModel'])) { add_post_meta($postId,'isShowMakeModel',0); } else { add_post_meta($postId,'isShowMakeModel',1); }
                if(isset($_POST['isShowTravelled']) || !empty($_POST['isShowTravelled'])) { add_post_meta($postId,'isShowTravelled',0); } else { add_post_meta($postId,'isShowTravelled',1); }
                if(isset($_POST['isShowCatLaunYr']) || !empty($_POST['isShowCatLaunYr'])) { add_post_meta($postId,'isShowCatLaunYr',0); } else { add_post_meta($postId,'isShowCatLaunYr',1); }
                if(isset($_POST['isShowColor']) || !empty($_POST['isShowColor'])) { add_post_meta($postId,'isShowColor',0); } else { add_post_meta($postId,'isShowColor',1); }
                if(isset($_POST['isShowMisc']) || !empty($_POST['isShowMisc'])) { add_post_meta($postId,'isShowMisc',0); } else { add_post_meta($postId,'isShowMisc',1); }
                if(isset($_POST['isShowLocation']) || !empty($_POST['isShowLocation'])) { add_post_meta($postId,'isShowLocation',0); } else { add_post_meta($postId,'isShowLocation',1); }
                if(isset($_POST['isShowFeature']) || !empty($_POST['isShowFeature'])) { add_post_meta($postId,'isShowFeature',0); } else { add_post_meta($postId,'isShowFeature',1); }
                if(isset($_POST['isShowSellerNote']) || !empty($_POST['isShowSellerNote'])) { add_post_meta($postId,'isShowSellerNote',0); } else { add_post_meta($postId,'isShowSellerNote',1); }
                if(isset($_POST['isShowOtherDtl']) || !empty($_POST['isShowOtherDtl'])) { add_post_meta($postId,'isShowOtherDtl',0); } else { add_post_meta($postId,'isShowOtherDtl',1); }
                if(isset($_POST['isShowOtherContDtl']) || !empty($_POST['isShowOtherContDtl'])) { add_post_meta($postId,'isShowOtherContDtl',0); } else { add_post_meta($postId,'isShowOtherContDtl',1); }


                /**
                 * File upload process start
                 */
                $count = 0;
                $filesName = $_FILES['stm_car_gallery_add']['name'];
                $filesTempName = $_FILES['stm_car_gallery_add']['tmp_name'];
                $wp_upload_dir = wp_upload_dir();
                $path = $wp_upload_dir['path'] . '/';
                $attachments_ids = array();
                foreach($filesName as $files) {
                    $fileTmpName = $filesTempName[$count];
                    $new_filename = 'post_id_' . $postId . '_' . stm_media_random_affix() . '.' . pathinfo($files, PATHINFO_EXTENSION);
                    $filename = $path . $new_filename;
                    if (move_uploaded_file($fileTmpName, $filename)) {
                        $filetype = wp_check_filetype(basename($filename), null);
                        $attach_id = wp_insert_attachment(array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit',
                        ), $filename, $postId);

                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
                        $attachments_ids[$new_filename] = $attach_id;
                    }
                    $count++;   
                }
               
                $current_attachments = get_posts(array(
                    'fields' => 'ids',
                    'post_type' => 'attachment',
                    'posts_per_page' => -1,
                    'post_parent' => $postId,
                ));
                $delete_attachments = array_diff($current_attachments, $attachments_ids);
                foreach ($delete_attachments as $delete_attachment) {
                    stm_delete_media(intval($delete_attachment));
                }

                ksort($attachments_ids);
                if (!empty($attachments_ids)) {
                    update_post_meta($postId, '_thumbnail_id', reset($attachments_ids));
                    array_shift($attachments_ids);
                }
                update_post_meta($postId, 'gallery', $attachments_ids);
                $response['url'] = site_url().'/motor-gallery';
               
            } else {
                $response['validationMsg'] = $postId->get_error_message();
            }
        }
        echo json_encode($response);  
        die();  
    }
    add_action('wp_ajax_stm_add_bike', 'stm_add_bike');
    add_action('wp_ajax_nopriv_stm_add_bike', 'stm_add_bike');

    function stm_edit_car() {

        $postId = (int)$_POST['postId'];
        $response = array();
        $validationMsg = array(); 
        $error = false;
        $mainFeatures = str_replace("\\","",$_POST['vehicleMainFeatures']);

        /**
         * Edit Vehicle server side validation
         */

        if(empty($_POST['VehicleMake'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle make"; }
        if(empty($_POST['VehicleModel'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle model"; }
        if(empty($_POST['VehicleLaunchYear'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle launch year"; }  
        if(empty($_POST['vehicleModelBodyStyles'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle body styles"; }  
        if(empty($_POST['vehicleEngine'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle engine details"; } 
        if(empty($_POST['vehicleGearBox'])){ $error = true; $validationMsg[]['message'] = "Enter gearbox details"; }   
        if(empty($_POST['vehiclePower'])){ $error = true; $validationMsg[]['message'] = "Enter power details"; }   
        if(empty($_POST['vehicleTorque'])){ $error = true; $validationMsg[]['message'] = "Enter torque details"; }   
        if(empty($_POST['vehicleFuelEconomy'])){ $error = true; $validationMsg[]['message'] = "Enter fuel economy"; }   
        if(empty($_POST['vehicle0-100KPH'])){ $error = true; $validationMsg[]['message'] = "Enter 0-100 KPH speed in second"; }   
        if(empty($_POST['vehicleTopSpeed'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle top speed"; }   
        if(empty($_POST['vehicleTravelled'])){ $error = true; $validationMsg[]['message'] = "Enter total travelled"; }   
        if(empty($_POST['vehicleInteriorColor'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle interior color"; }   
        if(empty($_POST['vehicleExteriorColor'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle exterior color"; }   
        if(empty($_POST['vehicleLocation'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle location"; }   
        if(empty($_POST['vehiclePrice'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle asking price"; }   
        if(empty($mainFeatures)) { $error = true; $validationMsg[]['message'] = "Select at least one feature"; }   

        if($error == true) {
            $response['validationMsg'] = $validationMsg;
        } else {
            $postTitleCumName = $_POST['VehicleMake']." ".$_POST['VehicleModel']." ".$_POST['VehicleLaunchYear'];
            $updatePost = array(
                'ID' => $postId,
                'post_title' => $postTitleCumName,
                'post_name' => $postTitleCumName
            );
            wp_update_post($updatePost); 
            update_post_meta($postId,'VehicleMake',$_POST['VehicleMake']);
            update_post_meta($postId,'VehicleModel',$_POST['VehicleModel']);
            update_post_meta($postId,'VehicleLaunchYear',$_POST['VehicleLaunchYear']);
            update_post_meta($postId,'vehicleModelBodyStyles',$_POST['vehicleModelBodyStyles']);
            update_post_meta($postId,'vehicleEngine',$_POST['vehicleEngine']);
            update_post_meta($postId,'vehicleGearBox',$_POST['vehicleGearBox']);
            update_post_meta($postId,'vehiclePower',$_POST['vehiclePower']);
            update_post_meta($postId,'vehicleTorque',$_POST['vehicleTorque']);
            update_post_meta($postId,'vehicleFuelEconomy',$_POST['vehicleFuelEconomy']);
            update_post_meta($postId,'VehicleSpeed',$_POST['vehicle0-100KPH']);
            update_post_meta($postId,'vehicleTopSpeed',$_POST['vehicleTopSpeed']);
            update_post_meta($postId,'vehiclePrice',$_POST['vehiclePrice']);
            update_post_meta($postId,'vehicleTravelled',$_POST['vehicleTravelled']);
            update_post_meta($postId,'vehicleCustomTitle',$_POST['vehicleCustomTitle']);   
            update_post_meta($postId,'vehicleLocation',$_POST['vehicleLocation']);
            update_post_meta($postId,'sellerNotes',$_POST['sellerNotes']);
            update_post_meta($postId,'vehicleExteriorColor',$_POST['vehicleExteriorColor']);
            update_post_meta($postId,'vehicleInteriorColor',$_POST['vehicleInteriorColor']);
            update_post_meta($postId,'vehicleMainFeatures',$mainFeatures);
            update_post_meta($postId,'vehicleOtherFeatures',$_POST['vehicleOtherFeatures']);
            /**
             * Edit post meta for displaying row or not
             *
             */
            if(isset($_POST['isShowMake']) || !empty($_POST['isShowMake'])) { update_post_meta($postId,'isShowMake',0); } else { update_post_meta($postId,'isShowMake',1); }
            if(isset($_POST['isShowVMMBLY']) || !empty($_POST['isShowVMMBLY'])) { update_post_meta($postId,'isShowVMMBLY',0); } else { update_post_meta($postId,'isShowVMMBLY',1); }
            if(isset($_POST['isShowColors']) || !empty($_POST['isShowColors'])) { update_post_meta($postId,'isShowColors',0); } else { update_post_meta($postId,'isShowColors',1); }
            if(isset($_POST['isShowMisc']) || !empty($_POST['isShowMisc'])) { update_post_meta($postId,'isShowMisc',0); } else { update_post_meta($postId,'isShowMisc',1); }
            if(isset($_POST['isShowLocation']) || !empty($_POST['isShowLocation'])) { update_post_meta($postId,'isShowLocation',0); } else { update_post_meta($postId,'isShowLocation',1); }
            if(isset($_POST['isShowFeatures']) || !empty($_POST['isShowFeatures'])) { update_post_meta($postId,'isShowFeatures',0); } else { update_post_meta($postId,'isShowFeatures',1); }
            if(isset($_POST['isShowSellerNotes']) || !empty($_POST['isShowSellerNotes'])) { update_post_meta($postId,'isShowSellerNotes',0); } else { update_post_meta($postId,'isShowSellerNotes',1); }
            if(isset($_POST['isShowOtherDtls']) || !empty($_POST['isShowOtherDtls'])) { update_post_meta($postId,'isShowOtherDtls',0); } else { update_post_meta($postId,'isShowOtherDtls',1); }
            if(isset($_POST['isShowContactDetails']) || !empty($_POST['isShowContactDetails'])) { update_post_meta($postId,'isShowContactDetails',0); } else { update_post_meta($postId,'isShowContactDetails',1); }

            if(!empty($_FILES)) {
                
                /**
                 * Delete attachment post if any
                 */
                $sqlDeleteOldAttachmentPost = array( 
                    'post_parent' => $postId,
                    'post_type' => 'attachment'
                );
                $oldPostsAttachment = get_posts($sqlDeleteOldAttachmentPost);
                if (is_array($oldPostsAttachment) && count($oldPostsAttachment) > 0) {
                    foreach($oldPostsAttachment as $post){
                        wp_delete_post($post->ID, true);
                    }
                }

                $count = 0;
                $filesName = $_FILES['stm_car_gallery_add']['name'];
                $filesTempName = $_FILES['stm_car_gallery_add']['tmp_name'];
                $wp_upload_dir = wp_upload_dir();
                $path = $wp_upload_dir['path'] . '/';
                $attachments_ids = array();
                foreach($filesName as $files) {
                    $fileTmpName = $filesTempName[$count];
                    $new_filename = 'post_id_' . $postId . '_' . stm_media_random_affix() . '.' . pathinfo($files, PATHINFO_EXTENSION);
                    $filename = $path . $new_filename;
                    if (move_uploaded_file($fileTmpName, $filename)) {
                        $filetype = wp_check_filetype(basename($filename), null);
                        $attach_id = wp_insert_attachment(array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit',
                        ), $filename, $postId);

                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
                        $attachments_ids[$new_filename] = $attach_id;
                    }
                    $count++;   
                }
               
                $current_attachments = get_posts(array(
                    'fields' => 'ids',
                    'post_type' => 'attachment',
                    'posts_per_page' => -1,
                    'post_parent' => $postId,
                ));
                $delete_attachments = array_diff($current_attachments, $attachments_ids);
                foreach ($delete_attachments as $delete_attachment) {
                    stm_delete_media(intval($delete_attachment));
                }

                ksort($attachments_ids);
                if (!empty($attachments_ids)) {
                    update_post_meta($postId, '_thumbnail_id', reset($attachments_ids));
                    array_shift($attachments_ids);
                }
                update_post_meta($postId, 'gallery', $attachments_ids);
            }
            $response['url'] = site_url().'/motor-gallery';  
        }

        echo json_encode($response);
        die();
    }

    add_action('wp_ajax_stm_edit_car', 'stm_edit_car');
    add_action('wp_ajax_nopriv_stm_edit_car', 'stm_edit_car');

    function stm_edit_bike() {
        $postId = (int)$_POST['postId'];
        $response = array();
        $validationMsg = array(); 
        $error = false;
        $mainFeatures = str_replace("\\","",$_POST['vehicleMainFeatures']); 

        /**
         * Add server side validation
         */
        if(empty($_POST['make'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle make"; }
        if(empty($_POST['model'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle model"; }
        if(empty($_POST['bikeTravelled'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle travelled"; }  
        if(empty($_POST['category'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle category"; }  
        if(empty($_POST['vehicleLaunchYear'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle launch year"; } 
        if(empty($_POST['bikeExteriorColor'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle color"; }   
        if(empty($_POST['displacement'])){ $error = true; $validationMsg[]['message'] = "Enter displacement"; }   
        if(empty($_POST['vehicleEngine'])){ $error = true; $validationMsg[]['message'] = "Enter engine type"; }   
        if(empty($_POST['vehiclePower'])){ $error = true; $validationMsg[]['message'] = "Enter power"; }   
        if(empty($_POST['vehicleSpeed'])){ $error = true; $validationMsg[]['message'] = "Enter 0-100 KPH speed in second"; }   
        if(empty($_POST['vehicleTopSpeed'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle top speed"; }   
        if(empty($_POST['vehicleTorque'])){ $error = true; $validationMsg[]['message'] = "Enter torque"; }   
        if(empty($_POST['vehicleGearBox'])){ $error = true; $validationMsg[]['message'] = "Enter gearbox details"; }   
        if(empty($_POST['vehicleLocation'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle location"; }   
        if(empty($_POST['vehiclePrice'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle asking price"; }   
        if(empty($mainFeatures)) { $error = true; $validationMsg[]['message'] = "Select at least one feature"; }
        
        if($error == true){
            $response['validationMsg'] = $validationMsg;
        } else {
            $postTitleCumName = $_POST['make']." ".$_POST['model']." ".$_POST['vehicleLaunchYear'];
            $updatePost = array(
                'ID' => $postId,
                'post_title' => $postTitleCumName,
                'post_name' => $postTitleCumName
            );
            wp_update_post($updatePost);
            update_post_meta($postId,'VehicleMake',$_POST['make']);
            update_post_meta($postId,'VehicleModel',$_POST['model']);
            update_post_meta($postId,'vehicleTravelled',$_POST['bikeTravelled']);
            update_post_meta($postId,'bikeCategory',$_POST['category']);
            update_post_meta($postId,'VehicleLaunchYear',$_POST['vehicleLaunchYear']);
            update_post_meta($postId,'bikeExteriorColor',$_POST['bikeExteriorColor']);
            update_post_meta($postId,'bikeDisplacement',$_POST['displacement']);
            update_post_meta($postId,'engine',$_POST['vehicleEngine']);
            update_post_meta($postId,'bikePower',$_POST['vehiclePower']);
            update_post_meta($postId,'bikeSpeed',$_POST['vehicleSpeed']);
            update_post_meta($postId,'bikeTopSpeed',$_POST['vehicleTopSpeed']);
            update_post_meta($postId,'bikeTorque',$_POST['vehicleTorque']);
            update_post_meta($postId,'bikeGearBox',$_POST['vehicleGearBox']);
            update_post_meta($postId,'bikeLocation',$_POST['vehicleLocation']);
            update_post_meta($postId,'bikeMainFeatures',$mainFeatures);
            update_post_meta($postId,'bikeOtherFeatures',$_POST['vehicleOtherFeatures']);
            update_post_meta($postId,'bikeSellerNotes',$_POST['sellerNotes']);
            update_post_meta($postId,'vehiclePrice',$_POST['vehiclePrice']);

             /**
              * Not Showing Row restriction strat
              */
            if(isset($_POST['isShowMakeModel']) || !empty($_POST['isShowMakeModel'])) { update_post_meta($postId,'isShowMakeModel',0); } else { update_post_meta($postId,'isShowMakeModel',1); }
            if(isset($_POST['isShowTravelled']) || !empty($_POST['isShowTravelled'])) { update_post_meta($postId,'isShowTravelled',0); } else { update_post_meta($postId,'isShowTravelled',1); }
            if(isset($_POST['isShowCatLaunYr']) || !empty($_POST['isShowCatLaunYr'])) { update_post_meta($postId,'isShowCatLaunYr',0); } else { update_post_meta($postId,'isShowCatLaunYr',1); }
            if(isset($_POST['isShowColor']) || !empty($_POST['isShowColor'])) { update_post_meta($postId,'isShowColor',0); } else { update_post_meta($postId,'isShowColor',1); }
            if(isset($_POST['isShowMisc']) || !empty($_POST['isShowMisc'])) { update_post_meta($postId,'isShowMisc',0); } else { update_post_meta($postId,'isShowMisc',1); }
            if(isset($_POST['isShowLocation']) || !empty($_POST['isShowLocation'])) { update_post_meta($postId,'isShowLocation',0); } else { update_post_meta($postId,'isShowLocation',1); }
            if(isset($_POST['isShowFeature']) || !empty($_POST['isShowFeature'])) { update_post_meta($postId,'isShowFeature',0); } else { update_post_meta($postId,'isShowFeature',1); }
            if(isset($_POST['isShowSellerNote']) || !empty($_POST['isShowSellerNote'])) { update_post_meta($postId,'isShowSellerNote',0); } else { update_post_meta($postId,'isShowSellerNote',1); }
            if(isset($_POST['isShowOtherDtl']) || !empty($_POST['isShowOtherDtl'])) { update_post_meta($postId,'isShowOtherDtl',0); } else { update_post_meta($postId,'isShowOtherDtl',1); }
            if(isset($_POST['isShowOtherContDtl']) || !empty($_POST['isShowOtherContDtl'])) { update_post_meta($postId,'isShowOtherContDtl',0); } else { update_post_meta($postId,'isShowOtherContDtl',1); }

            if(!empty($_FILES)) {
                
                /**
                 * Delete attachment post if any
                 */
                $sqlDeleteOldAttachmentPost = array( 
                    'post_parent' => $postId,
                    'post_type' => 'attachment'
                );
                $oldPostsAttachment = get_posts($sqlDeleteOldAttachmentPost);
                if (is_array($oldPostsAttachment) && count($oldPostsAttachment) > 0) {
                    foreach($oldPostsAttachment as $post){
                        wp_delete_post($post->ID, true);
                    }
                }

                $count = 0;
                $filesName = $_FILES['stm_car_gallery_add']['name'];
                $filesTempName = $_FILES['stm_car_gallery_add']['tmp_name'];
                $wp_upload_dir = wp_upload_dir();
                $path = $wp_upload_dir['path'] . '/';
                $attachments_ids = array();
                foreach($filesName as $files) {
                    $fileTmpName = $filesTempName[$count];
                    $new_filename = 'post_id_' . $postId . '_' . stm_media_random_affix() . '.' . pathinfo($files, PATHINFO_EXTENSION);
                    $filename = $path . $new_filename;
                    if (move_uploaded_file($fileTmpName, $filename)) {
                        $filetype = wp_check_filetype(basename($filename), null);
                        $attach_id = wp_insert_attachment(array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit',
                        ), $filename, $postId);

                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
                        $attachments_ids[$new_filename] = $attach_id;
                    }
                    $count++;   
                }
               
                $current_attachments = get_posts(array(
                    'fields' => 'ids',
                    'post_type' => 'attachment',
                    'posts_per_page' => -1,
                    'post_parent' => $postId,
                ));
                $delete_attachments = array_diff($current_attachments, $attachments_ids);
                foreach ($delete_attachments as $delete_attachment) {
                    stm_delete_media(intval($delete_attachment));
                }

                ksort($attachments_ids);
                if (!empty($attachments_ids)) {
                    update_post_meta($postId, '_thumbnail_id', reset($attachments_ids));
                    array_shift($attachments_ids);
                }
                update_post_meta($postId, 'gallery', $attachments_ids);
            }
            $response['url'] = site_url().'/motor-gallery';  
 
        }

        echo json_encode($response);
        die();

    }
    add_action('wp_ajax_stm_edit_bike', 'stm_edit_bike');
    add_action('wp_ajax_nopriv_stm_edit_bike', 'stm_edit_bike');

    function add_media_library() {
        $uploadedfile = $_FILES['upload'];
        $count = 0;
        $fileNameArr = $uploadedfile['name'];
        $fileTypeArr = $uploadedfile['type'];
        $fileTmpNameArr = $uploadedfile['tmp_name'];
        $fileErrorArr = $uploadedfile['error'];
        $fileSizeArr = $uploadedfile['size'];
        $uploadedImgPathArr = array();

        foreach($fileNameArr as $fileKey => $fileValue) {
            $fileData = array();
            $fileData['name'] = $fileValue;
            $fileData['type'] = $fileTypeArr[$count];
            $fileData['tmp_name'] = $fileTmpNameArr[$count];
            $fileData['error'] = $fileErrorArr[$count];
            $fileData['size'] = $fileSizeArr[$count];
            $movefile = wp_handle_upload($fileData,array('test_form' => FALSE));
            $uploadedImgPathArr[] = $movefile['url'];
            $count++;
        }

        $user_id = get_current_user_id();
        $getGallery = get_user_meta($user_id,'media_gallery',true);
        if(empty($getGallery) || $getGallery == '') {
            if(sizeof($uploadedImgPathArr) == 1) {
                $imageUrl = $uploadedImgPathArr[0]; 
            } else {
                $imageUrl = implode(",",$uploadedImgPathArr);
            }
            add_user_meta($user_id,'media_gallery',$imageUrl);
        } else {
            $dbUserGallery = get_user_meta($user_id,'media_gallery',true);
            if(sizeof($uploadedImgPathArr) == 1) {
                $dbUserGallery .= ",".$uploadedImgPathArr[0]; 
            } else {
                $imgUrlStr = implode(",",$uploadedImgPathArr);
                $dbUserGallery .= ",".$imgUrlStr;
            }
            update_user_meta($user_id,'media_gallery',$dbUserGallery);
        }
        echo 1;
        die();
    }
    add_action('wp_ajax_add_media_library', 'add_media_library');
    add_action('wp_ajax_nopriv_add_media_library', 'add_media_library');

    function getAllMediaQuery(){
        $user_id = get_current_user_id();
        $mediaGalleryDb = get_user_meta($user_id,'media_gallery',true);
        if(!empty($mediaGalleryDb) || $mediaGalleryDb != ''){
            $mediaGalleryArr = explode(",",$mediaGalleryDb);
            $html = "";
            $html .=  "<div class='col-md-12'>";
            $html .= "<div class='row m-t-10'>";
            foreach ($mediaGalleryArr as $value) {
                $html .= "<div class='col-md-4'>";
                $html .= "<div class='media_gallery_inner'>";
                $html .= "<img src='".$value."' />";
                $html .= "</div>";
                $html .= "</div>";
            }
            $html .= "</div>";
            $html .= "</div>";
            echo $html;
        } 
        die();  
    }
    add_action('wp_ajax_getAllMediaQuery', 'getAllMediaQuery');
    add_action('wp_ajax_nopriv_getAllMediaQuery', 'getAllMediaQuery');

/* -------------------------------- */
/* SEARCH PAGE GET MODELS FROM MAKE */ 
/* -------------------------------- */

function ajaxModelsFromMake(){
    $make = $_POST['make'];
    global $wpdb;
    $models = $wpdb->get_results('SELECT DISTINCT model FROM vehicle_predifined_data WHERE make="'.$make.'" ',OBJECT);
    
    //print_r($models);
    $models_select = '';
    
    foreach ($models as $key => $model) {
        $models_select .= '<option value="'.$model->model.'">'.$model->model.'</option>';
    }
    

    echo $models_select;
    //print_r($models);
    die();

}
add_action('wp_ajax_ajaxModelsFromMake', 'ajaxModelsFromMake');
add_action('wp_ajax_nopriv_ajaxModelsFromMake', 'ajaxModelsFromMake');

/* -------------------------------- */
/* SEARCH PAGE GET YEAR FROM MAKE */ 
/* -------------------------------- */

function ajaxYearFromMakeModel(){
    $make = $_POST['vars']['make'];
    $model = $_POST['vars']['model'];

    echo $model;
    
    global $wpdb;
    $launch_years = $wpdb->get_results('SELECT DISTINCT launch_year FROM vehicle_predifined_data WHERE make="'.$make.'" AND model="'.$model.'" ',OBJECT);
    
    print_r($launch_years);
    $year_select = '';
    
    foreach ($launch_years as $key => $launch_year) {
        $year_select .= '<option value="'.$launch_year->launch_year.'">'.$launch_year->launch_year.'</option>';
    }
    

    echo $year_select;
    //print_r($models);
    die();
}
add_action('wp_ajax_ajaxYearFromMakeModel', 'ajaxYearFromMakeModel');
add_action('wp_ajax_nopriv_ajaxYearFromMakeModel', 'ajaxYearFromMakeModel');

function homePageSearchGetAllMake(){
    $type = $_POST['type'];
    $response = array();
    global $wpdb;
    if($type == 'car'){
        $carMake = $wpdb->get_results('SELECT DISTINCT `make` FROM `vehicle_predifined_data`');
        foreach($carMake as $make){
             $response[] = $make->make;   
        }
    }
    if($type == 'bike'){
        $bikeMake = $wpdb->get_results('SELECT DISTINCT `make` FROM `bike_predefined_data`');
        foreach($bikeMake as $make){
             $response[] = $make->make;   
        }
    }

    if($type == 'buggy'){
        $buggyMake = $wpdb->get_results('SELECT DISTINCT `make` FROM `buggy_predefined_data`');
        foreach($buggyMake as $make){
             $response[] = $make->make;   
        }
    }
    echo json_encode($response);
    die();
}
add_action('wp_ajax_homePageSearchGetAllMake', 'homePageSearchGetAllMake');
add_action('wp_ajax_nopriv_homePageSearchGetAllMake', 'homePageSearchGetAllMake');
    
function homePageSearchGetAllModelFromMake(){
    $type = $_POST['type'];
    $response = array();
    global $wpdb;
    if($type == 'car') {
        $carModel = $wpdb->get_results("SELECT DISTINCT `model` FROM `vehicle_predifined_data` WHERE `make` = '".$_POST['make']."'");
        foreach($carModel as $model){
             $response[] = $model->model;   
        }   
    }
    if($type == 'bike') {
        $bikeModel = $wpdb->get_results("SELECT DISTINCT `model` FROM `bike_predefined_data` WHERE `make` = '".$_POST['make']."'");
        foreach($bikeModel as $model){
             $response[] = $model->model;   
        }   
    }

    if($type == 'buggy') {
        $buggyModel = $wpdb->get_results("SELECT DISTINCT `model` FROM `buggy_predefined_data` WHERE `make` = '".$_POST['make']."'");
        foreach($buggyModel as $model){
             $response[] = $model->model;   
        }   
    }


    echo json_encode($response);
    die(); 
}
add_action('wp_ajax_homePageSearchGetAllModelFromMake', 'homePageSearchGetAllModelFromMake');
add_action('wp_ajax_nopriv_homePageSearchGetAllModelFromMake', 'homePageSearchGetAllModelFromMake');

function homePageSearchGetAllYearFromModel(){
    $type = $_POST['type'];
    $response = array();
    global $wpdb;
    if($type == 'car') {
        $carYear = $wpdb->get_results("SELECT DISTINCT `launch_year` FROM `vehicle_predifined_data` WHERE `make` = '".$_POST['make']."' AND `model` = '".$_POST['model']."'");
        foreach($carYear as $year){
             $response[] = $year->launch_year;   
        }   
    }
    if($type == 'bike') {
        $bikeYear = $wpdb->get_results("SELECT DISTINCT `launch_year` FROM `bike_predefined_data` WHERE `make` = '".$_POST['make']."' AND `model` = '".$_POST['model']."'");
        foreach($bikeYear as $year){
             $response[] = $year->launch_year;   
        }   
    }

    if($type == 'buggy') {
        $buggyYear = $wpdb->get_results("SELECT DISTINCT `launch_year` FROM `buggy_predefined_data` WHERE `make` = '".$_POST['make']."' AND `model` = '".$_POST['model']."'");
        foreach($buggyYear as $year){
             $response[] = $year->launch_year;   
        }   
    }
    echo json_encode($response);
    die();
}
add_action('wp_ajax_homePageSearchGetAllYearFromModel', 'homePageSearchGetAllYearFromModel');
add_action('wp_ajax_nopriv_homePageSearchGetAllYearFromModel', 'homePageSearchGetAllYearFromModel');
    
function getBuggyModelFromMake(){
    $make = $_POST['make'];
    global $wpdb;
    $html = "";
    $buggyModel = $wpdb->get_results("SELECT DISTINCT `model` FROM `buggy_predefined_data` WHERE `make` = '".$make."'");
    foreach($buggyModel as $model){
        $html .= "<option value='".$model->model."'>".$model->model."</option>";  
    }
    echo $html; 
    die();
}
add_action('wp_ajax_getBuggyModelFromMake', 'getBuggyModelFromMake');
add_action('wp_ajax_nopriv_getBuggyModelFromMake', 'getBuggyModelFromMake');

function stm_add_buggy(){
    $validationMsg = array(); 
    $error = false;
    $mainFeatures = str_replace("\\","",$_POST['vehicleMainFeatures']);
    $response = array();

    if(empty($_POST['vehicleMake'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle make"; }
    if(empty($_POST['VehicleModel'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle model"; }
    if(empty($_POST['buggyTravelled'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle travelled"; }  
    if(empty($_POST['vehicleYear'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle launch year"; }  
    if(empty($_POST['fuelSystem'])){ $error = true; $validationMsg[]['message'] = "Enter fuel system"; } 
    if(empty($_POST['buggyExteriorColor'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle color"; }   
    if(empty($_POST['displacement'])){ $error = true; $validationMsg[]['message'] = "Enter displacement"; }   
    if(empty($_POST['vehicleEngine'])){ $error = true; $validationMsg[]['message'] = "Enter engine type"; }   
    if(empty($_POST['vehiclePower'])){ $error = true; $validationMsg[]['message'] = "Enter power"; }   
    if(empty($_POST['vehicleSpeed'])){ $error = true; $validationMsg[]['message'] = "Enter 0-100 KPH speed in second"; }   
    if(empty($_POST['vehicleTopSpeed'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle top speed"; }   
    if(empty($_POST['valves'])){ $error = true; $validationMsg[]['message'] = "Enter valves"; }   
    if(empty($_POST['vehicleTorque'])){ $error = true; $validationMsg[]['message'] = "Enter torque"; }   
    if(empty($_POST['vehicleGearBox'])){ $error = true; $validationMsg[]['message'] = "Enter gearbox details"; }   
    if(empty($_POST['vehicleLocation'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle location"; }   
    if(empty($_POST['vehiclePrice'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle asking price"; }   
    if(empty($mainFeatures)) { $error = true; $validationMsg[]['message'] = "Select at least one feature"; }
    
    if($error == true){
            $response['validationMsg'] = $validationMsg;
    } else {
        $postTitleCumName = $_POST['vehicleMake']." ".$_POST['VehicleModel']." ".$_POST['vehicleYear'];
        $totalActivePostAsPerPlan = getMembershipCustomFields('no_active_vehicles');
        $getUserActivePost = getUserActivePost();
        $postStatus = "";
        if ($totalActivePostAsPerPlan == -1) {
            $postStatus = 'publish';
        } elseif($totalActivePostAsPerPlan <= 0 || $getUserActivePost >= $totalActivePostAsPerPlan || !$totalActivePostAsPerPlan) {
            $postStatus = 'pending';
        } else {
            $postStatus = 'publish'; 
        }
        $postData = array(
            'post_type' => stm_listings_post_type(),
            'post_title' => $postTitleCumName,
            'post_status' => $postStatus,
            'post_name' => $postTitleCumName,
        );
        $postId = wp_insert_post($postData);
        if (!is_wp_error($postId)) {
            $user_id = get_current_user_id();
            add_post_meta($postId,'VehicleMake',$_POST['vehicleMake']);
            add_post_meta($postId,'VehicleModel',$_POST['VehicleModel']);
            add_post_meta($postId,'vehicleTravelled',$_POST['buggyTravelled']);
            add_post_meta($postId,'VehicleLaunchYear',$_POST['vehicleYear']);
            add_post_meta($postId,'fuelSystem',$_POST['fuelSystem']);

            add_post_meta($postId,'buggyExteriorColor',$_POST['buggyExteriorColor']);
            add_post_meta($postId,'buggyDisplacement',$_POST['displacement']);
            add_post_meta($postId,'engine',$_POST['vehicleEngine']);
            add_post_meta($postId,'buggyPower',$_POST['vehiclePower']);
            add_post_meta($postId,'buggySpeed',$_POST['vehicleSpeed']);
            add_post_meta($postId,'buggyTopSpeed',$_POST['vehicleTopSpeed']);
            add_post_meta($postId,'buggyValves',$_POST['valves']);
            add_post_meta($postId,'buggyTorque',$_POST['vehicleTorque']);
            add_post_meta($postId,'buggyGearBox',$_POST['vehicleGearBox']);
            add_post_meta($postId,'buggyLocation',$_POST['vehicleLocation']);
            add_post_meta($postId,'buggyMainFeatures',$mainFeatures);
            add_post_meta($postId,'buggyOtherFeatures',$_POST['vehicleOtherFeatures']);
            add_post_meta($postId,'buggySellerNotes',$_POST['sellerNotes']);

            add_post_meta($postId,'fuelControl',$_POST['vechicleFuelControl']);
            add_post_meta($postId,'starter',$_POST['vehicleStarter']);

            add_post_meta($postId,'vehicleType','Buggy');
            add_post_meta($postId,'stm_buggy_user',$user_id);
            add_post_meta($postId,'buggyVinNo',$_POST['vehicleVinNo']);
            add_post_meta($postId,'buggyShape',$_POST['buggyShape']);
            add_post_meta($postId,'vehiclePrice',$_POST['vehiclePrice']);

            /**
             * Not Showing Row restriction strat
             */

            if(isset($_POST['isShowMakeModel']) || !empty($_POST['isShowMakeModel'])) { add_post_meta($postId,'isShowMakeModel',0); } else { add_post_meta($postId,'isShowMakeModel',1); }
            if(isset($_POST['isShowTravelled']) || !empty($_POST['isShowTravelled'])) { add_post_meta($postId,'isShowTravelled',0); } else { add_post_meta($postId,'isShowTravelled',1); }
            if(isset($_POST['isShowLaunYrFuelSys']) || !empty($_POST['isShowLaunYrFuelSys'])) { add_post_meta($postId,'isShowLaunYrFuelSys',0); } else { add_post_meta($postId,'isShowLaunYrFuelSys',1); }
            if(isset($_POST['isShowColor']) || !empty($_POST['isShowColor'])) { add_post_meta($postId,'isShowColor',0); } else { add_post_meta($postId,'isShowColor',1); }
            if(isset($_POST['isShowMisc']) || !empty($_POST['isShowMisc'])) { add_post_meta($postId,'isShowMisc',0); } else { add_post_meta($postId,'isShowMisc',1); }
            if(isset($_POST['isShowLocation']) || !empty($_POST['isShowLocation'])) { add_post_meta($postId,'isShowLocation',0); } else { add_post_meta($postId,'isShowLocation',1); }
            if(isset($_POST['isShowFeature']) || !empty($_POST['isShowFeature'])) { add_post_meta($postId,'isShowFeature',0); } else { add_post_meta($postId,'isShowFeature',1); }
            if(isset($_POST['isShowSellerNote']) || !empty($_POST['isShowSellerNote'])) { add_post_meta($postId,'isShowSellerNote',0); } else { add_post_meta($postId,'isShowSellerNote',1); }
            if(isset($_POST['isShowOtherDtl']) || !empty($_POST['isShowOtherDtl'])) { add_post_meta($postId,'isShowOtherDtl',0); } else { add_post_meta($postId,'isShowOtherDtl',1); }
            if(isset($_POST['isShowContDtl']) || !empty($_POST['isShowContDtl'])) { add_post_meta($postId,'isShowContDtl',0); } else { add_post_meta($postId,'isShowContDtl',1); }

            /**
             * File upload process start
             */
            $count = 0;
            $filesName = $_FILES['stm_car_gallery_add']['name'];
            $filesTempName = $_FILES['stm_car_gallery_add']['tmp_name'];
            $wp_upload_dir = wp_upload_dir();
            $path = $wp_upload_dir['path'] . '/';
            $attachments_ids = array();
            foreach($filesName as $files) {
                $fileTmpName = $filesTempName[$count];
                $new_filename = 'post_id_' . $postId . '_' . stm_media_random_affix() . '.' . pathinfo($files, PATHINFO_EXTENSION);
                $filename = $path . $new_filename;
                if (move_uploaded_file($fileTmpName, $filename)) {
                    $filetype = wp_check_filetype(basename($filename), null);
                    $attach_id = wp_insert_attachment(array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                        'post_mime_type' => $filetype['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit',
                    ), $filename, $postId);

                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
                    $attachments_ids[$new_filename] = $attach_id;
                }
                $count++;   
            }
           
            $current_attachments = get_posts(array(
                'fields' => 'ids',
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_parent' => $postId,
            ));
            $delete_attachments = array_diff($current_attachments, $attachments_ids);
            foreach ($delete_attachments as $delete_attachment) {
                stm_delete_media(intval($delete_attachment));
            }

            ksort($attachments_ids);
            if (!empty($attachments_ids)) {
                update_post_meta($postId, '_thumbnail_id', reset($attachments_ids));
                array_shift($attachments_ids);
            }
            update_post_meta($postId, 'gallery', $attachments_ids);
            $response['url'] = site_url().'/motor-gallery';
               
        } else {
            $response['validationMsg'] = $postId->get_error_message();
        }
    }
    echo json_encode($response);  
    die();  

}
add_action('wp_ajax_stm_add_buggy', 'stm_add_buggy');
add_action('wp_ajax_nopriv_stm_add_buggy', 'stm_add_buggy');

function stm_edit_buggy(){
    $postId = (int)$_POST['postId'];
    $validationMsg = array(); 
    $error = false;
    $mainFeatures = str_replace("\\","",$_POST['vehicleMainFeatures']);
    $response = array();

    if(empty($_POST['vehicleMake'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle make"; }
    if(empty($_POST['VehicleModel'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle model"; }
    if(empty($_POST['buggyTravelled'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle travelled"; }  
    if(empty($_POST['vehicleYear'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle launch year"; }  
    if(empty($_POST['fuelSystem'])){ $error = true; $validationMsg[]['message'] = "Enter fuel system"; } 
    if(empty($_POST['buggyExteriorColor'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle color"; }   
    if(empty($_POST['displacement'])){ $error = true; $validationMsg[]['message'] = "Enter displacement"; }   
    if(empty($_POST['vehicleEngine'])){ $error = true; $validationMsg[]['message'] = "Enter engine type"; }   
    if(empty($_POST['vehiclePower'])){ $error = true; $validationMsg[]['message'] = "Enter power"; }   
    if(empty($_POST['vehicleSpeed'])){ $error = true; $validationMsg[]['message'] = "Enter 0-100 KPH speed in second"; }   
    if(empty($_POST['vehicleTopSpeed'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle top speed"; }   
    if(empty($_POST['valves'])){ $error = true; $validationMsg[]['message'] = "Enter valves"; }   
    if(empty($_POST['vehicleTorque'])){ $error = true; $validationMsg[]['message'] = "Enter torque"; }   
    if(empty($_POST['vehicleGearBox'])){ $error = true; $validationMsg[]['message'] = "Enter gearbox details"; }   
    if(empty($_POST['vehicleLocation'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle location"; }   
    if(empty($_POST['vehiclePrice'])){ $error = true; $validationMsg[]['message'] = "Enter vehicle asking price"; }   
    if(empty($mainFeatures)) { $error = true; $validationMsg[]['message'] = "Select at least one feature"; }

    if($error == true){
            $response['validationMsg'] = $validationMsg;
    } else {
        $postTitleCumName = $_POST['vehicleMake']." ".$_POST['VehicleModel']." ".$_POST['vehicleYear'];
        $postData = array(
            'ID' => $postId,
            'post_title' => $postTitleCumName,
            'post_name' => $postTitleCumName,
        );
        wp_update_post($postData);
        update_post_meta($postId,'VehicleMake',$_POST['vehicleMake']);
        update_post_meta($postId,'VehicleModel',$_POST['VehicleModel']);
        update_post_meta($postId,'vehicleTravelled',$_POST['buggyTravelled']);
        update_post_meta($postId,'VehicleLaunchYear',$_POST['vehicleYear']);
        update_post_meta($postId,'fuelSystem',$_POST['fuelSystem']);
        update_post_meta($postId,'buggyExteriorColor',$_POST['buggyExteriorColor']);
        update_post_meta($postId,'buggyDisplacement',$_POST['displacement']);
        update_post_meta($postId,'engine',$_POST['vehicleEngine']);
        update_post_meta($postId,'buggyPower',$_POST['vehiclePower']);
        update_post_meta($postId,'buggySpeed',$_POST['vehicleSpeed']);
        update_post_meta($postId,'buggyTopSpeed',$_POST['vehicleTopSpeed']);
        update_post_meta($postId,'buggyValves',$_POST['valves']);
        update_post_meta($postId,'buggyTorque',$_POST['vehicleTorque']);
        update_post_meta($postId,'buggyGearBox',$_POST['vehicleGearBox']);
        update_post_meta($postId,'buggyLocation',$_POST['vehicleLocation']);
        update_post_meta($postId,'buggyMainFeatures',$mainFeatures);
        update_post_meta($postId,'buggyOtherFeatures',$_POST['vehicleOtherFeatures']);
        update_post_meta($postId,'buggySellerNotes',$_POST['sellerNotes']);
        update_post_meta($postId,'fuelControl',$_POST['vechicleFuelControl']);
        update_post_meta($postId,'starter',$_POST['vehicleStarter']);
        update_post_meta($postId,'vehiclePrice',$_POST['vehiclePrice']);

        /**
         * Not Showing Row restriction strat
         */

        if(isset($_POST['isShowMakeModel']) || !empty($_POST['isShowMakeModel'])) { update_post_meta($postId,'isShowMakeModel',0); } else { update_post_meta($postId,'isShowMakeModel',1); }
        if(isset($_POST['isShowTravelled']) || !empty($_POST['isShowTravelled'])) { update_post_meta($postId,'isShowTravelled',0); } else { update_post_meta($postId,'isShowTravelled',1); }
        if(isset($_POST['isShowLaunYrFuelSys']) || !empty($_POST['isShowLaunYrFuelSys'])) { update_post_meta($postId,'isShowLaunYrFuelSys',0); } else { update_post_meta($postId,'isShowLaunYrFuelSys',1); }
        if(isset($_POST['isShowColor']) || !empty($_POST['isShowColor'])) { update_post_meta($postId,'isShowColor',0); } else { update_post_meta($postId,'isShowColor',1); }
        if(isset($_POST['isShowMisc']) || !empty($_POST['isShowMisc'])) { update_post_meta($postId,'isShowMisc',0); } else { update_post_meta($postId,'isShowMisc',1); }
        if(isset($_POST['isShowLocation']) || !empty($_POST['isShowLocation'])) { update_post_meta($postId,'isShowLocation',0); } else { update_post_meta($postId,'isShowLocation',1); }
        if(isset($_POST['isShowFeature']) || !empty($_POST['isShowFeature'])) { update_post_meta($postId,'isShowFeature',0); } else { update_post_meta($postId,'isShowFeature',1); }
        if(isset($_POST['isShowSellerNote']) || !empty($_POST['isShowSellerNote'])) { update_post_meta($postId,'isShowSellerNote',0); } else { update_post_meta($postId,'isShowSellerNote',1); }
        if(isset($_POST['isShowOtherDtl']) || !empty($_POST['isShowOtherDtl'])) { update_post_meta($postId,'isShowOtherDtl',0); } else { update_post_meta($postId,'isShowOtherDtl',1); }
        if(isset($_POST['isShowContDtl']) || !empty($_POST['isShowContDtl'])) { update_post_meta($postId,'isShowContDtl',0); } else { update_post_meta($postId,'isShowContDtl',1); }

        if(!empty($_FILES)) {
                
                $count = 0;
                $filesName = $_FILES['stm_car_gallery_add']['name'];
                $filesTempName = $_FILES['stm_car_gallery_add']['tmp_name'];
                $wp_upload_dir = wp_upload_dir();
                $path = $wp_upload_dir['path'] . '/';
                $attachments_ids = array();
                foreach($filesName as $files) {
                    $fileTmpName = $filesTempName[$count];
                    $new_filename = 'post_id_' . $postId . '_' . stm_media_random_affix() . '.' . pathinfo($files, PATHINFO_EXTENSION);
                    $filename = $path . $new_filename;
                    if (move_uploaded_file($fileTmpName, $filename)) {
                        $filetype = wp_check_filetype(basename($filename), null);
                        $attach_id = wp_insert_attachment(array(
                            'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit',
                        ), $filename, $postId);

                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
                        $attachments_ids[$new_filename] = $attach_id;
                    }
                    $count++;   
                }
               
                $current_attachments = get_posts(array(
                    'fields' => 'ids',
                    'post_type' => 'attachment',
                    'posts_per_page' => -1,
                    'post_parent' => $postId,
                ));
                $delete_attachments = array_diff($current_attachments, $attachments_ids);
                foreach ($delete_attachments as $delete_attachment) {
                    stm_delete_media(intval($delete_attachment));
                }

                ksort($attachments_ids);
                if (!empty($attachments_ids)) {
                    update_post_meta($postId, '_thumbnail_id', reset($attachments_ids));
                    array_shift($attachments_ids);
                }
                update_post_meta($postId, 'gallery', $attachments_ids);
        }
        $response['url'] = site_url().'/motor-gallery'; 

    }
    echo json_encode($response);
    die();
}
add_action('wp_ajax_stm_edit_buggy', 'stm_edit_buggy');
add_action('wp_ajax_nopriv_stm_edit_buggy', 'stm_edit_buggy');

function searchByMake(){
    $make = $_POST['make'];
    $type = $_POST['type'];
    global $wpdb;
    $response = array();
    $modelFlag = 0;
    if($type == 'car'){
        $carModel = $wpdb->get_results("SELECT DISTINCT `model` FROM `vehicle_predifined_data` WHERE `make` = '".$_POST['make']."'");
        foreach($carModel as $model){
             $response['model'][$modelFlag] = $model->model;  
             $modelFlag++; 
        }
        $searchArg = array(
            'post_type' => 'listings',
            'post_status' => array('publish','draft','pending'),
            's' => $make
        );
        $searchedPost = new WP_Query($searchArg); 
        if ( $searchedPost->have_posts() ) :
            while ( $searchedPost->have_posts() ) : $searchedPost->the_post();  
                $postId = get_the_ID();
                $response['vehicle']['id'][] = $postId;
                $response['vehicle']['title'][] = get_the_title();
                $response['vehicle']['travelled'] = get_post_meta($postId,'vehicleTravelled',true);
                $response['vehicle']['Price'] = get_post_meta($postId,'vehiclePrice',true);
                $response['vehicle']['Price'] = get_post_meta($postId,'vehiclePrice',true);

            endwhile;
        endif;
       
          //echo the_launch_year();
    } 
    echo json_encode($response);
  die(); 
}
add_action('wp_ajax_searchByMake', 'searchByMake');
add_action('wp_ajax_nopriv_searchByMake', 'searchByMake');
?>


 