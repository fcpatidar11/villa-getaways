<?php
/* Welcome to Bones :)
This is the core Bones file where most of the
main functions & features reside. If you have
any custom functions, it's best to put them
in the functions.php file.

Developed by: Eddie Machado
URL: http://themble.com/bones/

  - head cleanup (remove rsd, uri links, junk css, ect)
  - enqueueing scripts & styles
  - theme support functions
  - custom menu output & fallbacks
  - related post function
  - page-navi function
  - removing <p> from around images
  - customizing the post excerpt
  - custom google+ integration
  - adding custom fields to user profiles

*/

/*********************
WP_HEAD GOODNESS
The default wordpress head is
a mess. Let's clean it up by
removing all the junk we don't
need.
*********************/

function bones_head_cleanup() {
	// category feeds
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
	// remove WP version from css
	add_filter( 'style_loader_src', 'bones_remove_wp_ver_css_js', 9999 );
	// remove Wp version from scripts
	add_filter( 'script_loader_src', 'bones_remove_wp_ver_css_js', 9999 );

} /* end bones head cleanup */

// A better title
// http://www.deluxeblogtips.com/2012/03/better-title-meta-tag.html
function rw_title( $title, $sep, $seplocation ) {
  global $page, $paged;

  // Don't affect in feeds.
  if ( is_feed() ) return $title;

  // Add the blog's name
  if ( 'right' == $seplocation ) {
    $title .= get_bloginfo( 'name' );
  } else {
    $title = get_bloginfo( 'name' ) . $title;
  }

  // Add the blog description for the home/front page.
  $site_description = get_bloginfo( 'description', 'display' );

  if ( $site_description && ( is_home() || is_front_page() ) ) {
    $title .= " {$sep} {$site_description}";
  }

  // Add a page number if necessary:
  if ( $paged >= 2 || $page >= 2 ) {
    $title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
  }

  return $title;

} // end better title

// remove WP version from RSS
function bones_rss_version() { return ''; }

// remove WP version from scripts
function bones_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function bones_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function bones_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}

// remove injected CSS from gallery
function bones_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}


/*********************
SCRIPTS & ENQUEUEING
*********************/

function bones_scripts_and_styles() {
	if (!is_admin()) {
	    
	    //wp_register_style( "select2_css", get_template_directory_uri() . '/assets/js/select2.min.css', '', '1.0' );
		// adding jquery in the footer
		
		//wp_enqueue_style( 'select2_css' );
	//	wp_deregister_script( 'jquery' );
	//	wp_register_script( 'jquery', get_template_directory_uri() . '/assets/js/jquery-1.12.4.js', array(), '', true );
		
		wp_register_script( "ajax_call", get_template_directory_uri() . '/assets/js/ajax-call.js', array('jquery'), '', true );
		//wp_register_script( "select2_js", get_template_directory_uri() . '/assets/js/select2.min.js', array('jquery'), '1.0', true );
        wp_localize_script( 'ajax_call', 'ajax_url', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
    
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'ajax_call' );
        //wp_enqueue_script( 'select2_js' );
		// comment reply script for threaded comments
		if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
			wp_enqueue_script( 'comment-reply' );
		}

		// enqueue styles and scripts
		wp_enqueue_style( 'bones-stylesheet' );
		wp_enqueue_script( 'bones-js' );
	}
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function bones_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );

	// default thumb size
	set_post_thumbnail_size(125, 125, true);

	// rss thingy
	add_theme_support('automatic-feed-links');

	// wp menus
	add_theme_support( 'menus' );

	// registering wp3+ menus
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'bonestheme' ),   // main nav in header,
			'secondary-nav' => __( 'The Secondary Menu', 'bonestheme' ),   // secondary nav in footer
			'secondary-media-nav' => __( 'The Secondary Media Menu', 'bonestheme' ),   // secondary nav in footer
		)
	);
} /* end bones theme support */

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function bones_page_navi() {
  global $wp_query;
  $bignum = 999999999;
  if ( $wp_query->max_num_pages <= 1 )
    return;
  echo '<nav class="pagination">';
  echo paginate_links( array(
    'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
    'format'       => '',
    'current'      => max( 1, get_query_var('paged') ),
    'total'        => $wp_query->max_num_pages,
    'prev_text'    => '&larr;',
    'next_text'    => '&rarr;',
    'type'         => 'list',
    'end_size'     => 3,
    'mid_size'     => 3
  ) );
  echo '</nav>';
} /* end page navi */

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function bones_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function bones_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a class="excerpt-read-more" href="'. get_permalink( $post->ID ) . '" title="'. __( 'Read ', 'bonestheme' ) . esc_attr( get_the_title( $post->ID ) ).'">'. __( 'Read more &raquo;', 'bonestheme' ) .'</a>';
}

function showRatingDiamonds($ratings) {
    $html = "";
    if($ratings >= 1)
        $html .= '<a href="#"><img src="'. get_template_directory_uri() . '/assets/images/icon-dimond.png" alt=""></a>';
    
    if($ratings >= 2)
        $html .= '<a href="#"><img src="'. get_template_directory_uri() . '/assets/images/icon-dimond.png" alt=""></a>';
    
    if($ratings >= 3)
        $html .= '<a href="#"><img src="'. get_template_directory_uri() . '/assets/images/icon-dimond.png" alt=""></a>';
    
    if($ratings >= 4)
        $html .= '<a href="#"><img src="'. get_template_directory_uri() . '/assets/images/icon-dimond.png" alt=""></a>';
    
    if($ratings == 5)
        $html .= '<a href="#"><img src="'. get_template_directory_uri() . '/assets/images/icon-dimond.png" alt=""></a>';
        
    return $html;
}

add_action("wp_ajax_destination_location", "destination_location");
add_action('wp_ajax_nopriv_destination_location','destination_location');
if (!function_exists("destination_location")) {
    function destination_location() {
        $conn = oracleDbConnection();
        $no_regions = false;
        $html = '';
        if( isset($_REQUEST["type"]) && $_REQUEST["type"] && $_REQUEST["type"] == 'destination' ) {
            $locations = fetchDestinationLocations($conn, $_REQUEST["destination_id"]);
            $regions = fetchRegions($conn, $_REQUEST["destination_id"]);
            if(!empty($regions)) {
                if(count($regions) == 1) {
                    if( empty( $regions[""] ) || is_null( $regions[""] ) ) {
                        $no_regions = true;
                    }
                }
            };
            if($locations) {
                $html .= '<option value="">Select Location</option>';
                foreach($locations as $key => $location) {
                    $html .= '<option value="' . $key . '">' . $location . '</option>';
                }
            }
        } elseif( isset($_REQUEST["type"]) && $_REQUEST["type"] && $_REQUEST["type"] == 'location' ) {
            $locations = [];
            $location_ids = "";
            if( isset($_REQUEST["location_id"]) && $_REQUEST["location_id"] ) {
                foreach($_REQUEST["location_id"] AS $location_id) {
                    $locations[] = $location_id;
                }
                $location_ids = implode(",", $locations);
                
                $regions = fetchLocationRegions($conn, $location_ids);
                if($regions) {
                    foreach($regions as $key => $region) {
                        $html .= '<option value="' . $key . '">' . $region . '</option>';
                    }
                }
            }
        }
        $response = array(
            'html' => $html,
            'no_region' => $no_regions
        );

        echo json_encode($response); // Encode the response as JSON
        die;
    }
}

add_action("wp_ajax_fetch_rates_of_single_villa", "fetch_rates_of_single_villa");
add_action('wp_ajax_nopriv_fetch_rates_of_single_villa','fetch_rates_of_single_villa');
if (!function_exists("fetch_rates_of_single_villa")) {
    function fetch_rates_of_single_villa() {
        $conn = oracleDbConnection();
        if( isset($_REQUEST["id"]) && $_REQUEST["id"] &&
            isset($_REQUEST["dest_checkIn"]) && $_REQUEST["dest_checkIn"] && 
            isset($_REQUEST["dest_checkOut"]) && $_REQUEST["dest_checkOut"] && 
            isset($_REQUEST["bedrooms"]) && $_REQUEST["bedrooms"]
            
            ) {
            
            $percentage = isset($_REQUEST["percentage"]) && $_REQUEST["percentage"];
            if($percentage) {
                $percentage = $_REQUEST["percentage"];
            }else {
                $percentage = 0;
            }
            
            $date1 = DateTime::createFromFormat('d-m-Y', $_REQUEST["dest_checkIn"]);
            $date2 = DateTime::createFromFormat('d-m-Y', $_REQUEST["dest_checkOut"]);
            // Malformed dates yield false; treat as a single night rather than fatalling.
            $diff = ( $date1 && $date2 ) ? $date1->diff($date2)->format('%a') : 1;
            
            $rates = fetchRatesOfSingleVilla($conn, $_REQUEST["id"], date("d/m/Y", strtotime($_REQUEST["dest_checkIn"])), date("d/m/Y", strtotime($_REQUEST["dest_checkOut"])), $_REQUEST["bedrooms"]);
            $total = number_format((float)(( $rates + ($rates*$percentage)/100 )*$diff), 2, '.', '');
            $tax = number_format((float)$rates*($percentage/100), 2, '.', '');
            
            if($rates) {
                wp_send_json_success(
                    array(
                        'rates' => $rates,
                        'diff' => $diff,
                        'total' => $total,
                        'tax' => $tax
                    ), 200
                );
                wp_die();
            }
        } else{
            wp_send_json_error('Please select dates and bedrooms');
        }
        wp_die();
    }
}

add_action("wp_ajax_inquiry_form", "inquiry_form");
add_action('wp_ajax_nopriv_inquiry_form','inquiry_form');
if (!function_exists("inquiry_form")) {
    function inquiry_form() {
        $conn = oracleDbConnection();
        if(isset($_POST)) {
            $fname      =       isset($_POST['fname']) ? filter_var($_POST['fname'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $lname      =       isset($_POST['lname']) ? filter_var($_POST['lname'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $email      =       isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : "";
            $phone      =       isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT) : "";
            $homePhone  =       isset($_POST['homePhone']) ? filter_var($_POST['homePhone'], FILTER_SANITIZE_NUMBER_INT) : "";
            $country    =       isset($_POST['country']) ? filter_var($_POST['country'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $address    =       isset($_POST['address']) ? filter_var($_POST['address'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $city       =       isset($_POST['city']) ? filter_var($_POST['city'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $state = isset($_POST['state']) ? filter_var($_POST['state'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $zip        =       isset($_POST['zip']) ? filter_var($_POST['zip'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $date_from  =       isset($_POST['date_from']) ? filter_var($_POST['date_from'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $date_from =  date('d-m-Y', strtotime($date_from));
            $date_to  =         isset($_POST['date_to']) ? filter_var($_POST['date_to'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $date_to =  date('d-m-Y', strtotime($date_to));
            $title  =         isset($_POST['title']) ? filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $flexible = isset($_POST['flexible']) ? filter_var($_POST['flexible'], FILTER_SANITIZE_SPECIAL_CHARS) : "0";
            $newsletter = isset($_POST['newsletter']) ? filter_var($_POST['newsletter'], FILTER_SANITIZE_SPECIAL_CHARS) : "0";
            
            $adults = isset($_POST['adults']) ? filter_var($_POST['adults'], FILTER_SANITIZE_SPECIAL_CHARS) : "0";
            $kids = isset($_POST['kids']) ? filter_var($_POST['kids'], FILTER_SANITIZE_SPECIAL_CHARS) : "0";
            $budget = isset($_POST['budget']) ? filter_var($_POST['budget'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $questions = isset($_POST['questions']) ? filter_var($_POST['questions'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            
            $villa_number = isset($_POST['_villa_number']) ? filter_var($_POST['_villa_number'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $villa_id = isset($_POST['_villa_id']) ? filter_var($_POST['_villa_id'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            $agent_id = isset($_POST['_agent_id']) ? filter_var($_POST['_agent_id'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
            
            $error = [];
            if($fname == "" || $lname == "" || $email == "" || $country == "" || $date_from == "" || $date_to == ""){
                $error['error'] = "Please fill required fields";
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              $error['email'] = "Invalid email format";
            }

            if(empty($error) && count($error) === 0) {
                $result = enquiry($conn, $title, $fname, $lname, $email, $phone, $homePhone, $address, $city, $state, $zip, $country, $agent_id, $newsletter, $villa_id, $villa_number, $date_from, $date_to, $flexible, $adults, $kids, $budget, $questions);
                
                
                if ($result == 'OK') { 
                    $pageUrl = home_url()."/inquiry?villa_number=$villa_number";
                    
                    $success_msg = '<p>Thank you for your inquiry, an experienced reservation specialist will contact you shortly.</p>';
                    // $success_msg .= '<h6>*** IMPORTANT INFORMATION – BEWARE FRAUDSTERS ***</h6>';
                    // $success_msg .= '<p>It has come to our attention that scammers/hackers are attempting to gain access to clients inquiry information from villa rental agencies.</p>';
                    // $success_msg .= "<p>If you receive an email from another source claiming to have 'Last minute Cancellations' or 'Special Offers' and offering big discounts, usually with links to actual genuine websites;</p>";
                    // $success_msg .= '<p>BE WARNED!!</p>';
                    // $success_msg .= '<p>People have fallen victim to such scammers phishing attempts and in this way lost considerable sums of money.</p>';
                    // $success_msg .= '<p>Please stay vigilant and ONLY use reputable/verifiable companies when renting holiday accommodation on the internet. Villa Getaways Ltd has been successfully providing its clients safe and enjoyable villa rentals since 2001. Trust the source.</p>';
                    
                    wp_send_json_success(array('Form data received successfully.', $success_msg));
                } else {
                    wp_send_json_error(array('Form data not submitted.', $result));   
                }
            }else {
                wp_send_json_error(array('Form data not submitted.', $error));   
            }
            
            
            
          } else {
            wp_send_json_error('Form data not found.');
          }
    }
}
add_action("wp_ajax_villa_booking_details", "villa_booking_details");
add_action('wp_ajax_nopriv_villa_booking_details','villa_booking_details');
if (!function_exists("villa_booking_details")) {
    function villa_booking_details() {
       
        $f_name = $_REQUEST["fname"];
        $l_name = $_REQUEST["lname"] ;
        $address_line = $_REQUEST["address_line"];
        $address_state = $_REQUEST["address_state"];
        $address_country = $_REQUEST["address_country"];
        $address_postcode = $_REQUEST["address_postcode"];
        $email = $_REQUEST["email"];
        $villaId = (int) ($_REQUEST["villaId"] ?? 0);
        $amount = number_format((float) str_replace(',', '', $_REQUEST["amount"] ?? 0));
        $mobile = $_REQUEST["mobile_num"] ;
        $suburb = $_REQUEST["suburb"];
        $night = $_REQUEST["night"];
        $csymbol = $_REQUEST["csymbol"];
        // $villaprice = preg_replace('([^\d,.]+)', '', $_REQUEST["villaprice"]);
        $villaprice = $_REQUEST["villaprice"];
        $children = $_REQUEST["children"] ;
        $villa_desc = $_REQUEST["villa_desc"];
        $adults = $_REQUEST["adults"] ;
        $NoOfRooms = $_REQUEST["NoOfRooms"] ;
        $arrivalDate = $_REQUEST["arrivalDate"];
        $departureDate = $_REQUEST["departureDate"];
        $paymentMethod = $_REQUEST["paymentMethod"];
        $villa_currency = $_REQUEST["villa_currency"];
        $payment_details['villa_currency'] = $villa_currency;
        
        $address_country_code =  $_REQUEST["address_country_code"];
        $phone_code =  $_REQUEST["phone_code"];
        
        
        $conn = oracleDbConnection();
        

         $client_id = callCreateClient($conn, $f_name, $l_name, $email, $mobile, $address_line, $suburb, $address_state, $address_postcode, 1, $_REQUEST["villaId"]);
  
  
        setcookie("fname", $f_name, time() + 86400, "/");
        setcookie("lname", $l_name, time() + 86400, "/");
        setcookie("address_line", $address_line, time() + 86400, "/");
        setcookie("address_state", $address_state, time() + 86400, "/");
        setcookie("address_city", $suburb, time() + 86400, "/");
        setcookie("address_country", $address_country, time() + 86400, "/");
        setcookie("address_postcode", $address_postcode, time() + 86400, "/");
        setcookie("email", $email, time() + 86400, "/");
        setcookie("client_id", $client_id, time() + 86400, "/");
        setcookie("villa_id", $villaId, time() + 86400, "/");
        setcookie("villa_price", $villaprice, time() + 86400, "/"); 
        setcookie("nights", $night, time() + 86400, "/");
        setcookie("villa_desc", $villa_desc, time() + 86400, "/");
        setcookie("villa_currency",  $villa_currency, time() + 86400, "/");
        setcookie("csymbol",  $csymbol, time() + 86400, "/");
        setcookie("phone", $mobile, time() + 86400, "/");
        setcookie("phone_code", $phone_code, time() + 86400, "/");
        setcookie("address_country_code", $address_country_code, time() + 86400, "/");
        setcookie("booking_amount", $amount, time() + 86400, "/");
       
        
       
       
        $p_full_payment = 0;
        $p_payment_method = "wt";
        $format = 'd-m-Y'; // Date format
        $date = DateTime::createFromFormat($format, $arrivalDate ?? '');
        $today = new DateTime();
        if ($date) {
            $interval = $today->diff($date);
            if ($interval->days <= 30 && $date > $today) {
               $p_full_payment = 1;
            }
        }
        if ($paymentMethod == "master_credit") {
           $p_payment_method = "cc";
        } else {
            $p_payment_method = "wt";
            $p_full_payment = $p_full_payment+20;
        }
        $villaprice = str_replace(',', '', $villaprice);
      
        $booking_id = callCreateBooking($conn, $_REQUEST["villaId"], $client_id, $arrivalDate, $departureDate, $night, 
        $adults, $children, $NoOfRooms, $villaprice, $p_payment_method, $p_full_payment);

       setcookie("booking_id", $booking_id, time() + 86400, "/");
       
        die;
    }
}


add_action("wp_ajax_dierct_payment", "dierct_payment");
add_action('wp_ajax_nopriv_dierct_payment','dierct_payment');
if (!function_exists("dierct_payment")) {
    function dierct_payment() {
       
      
      $booking_id = $_POST['bookingid'];
      $villa_currency = $_POST['villa_currency'];
      $villa_desc = $_POST['villa_desc'];

      setcookie("booking_id", $booking_id, time() + 86400, "/");
      setcookie("villa_currency", $villa_currency, time() + 86400, "/");
       setcookie("villa_desc", $villa_desc, time() + 86400, "/");
      
      
        die;
    }
}



function getCurrencySymbol($currencyCode) {
    $currencySymbols = [
        'USD' => '$',
        'AUD' => 'A$',
        'EUR' => '€',
        'NZD' => 'NZ$',
        // Add more currency codes and symbols here if needed
    ];

    return isset($currencySymbols[$currencyCode]) ? $currencySymbols[$currencyCode] : 'Unknown currency';
}
?>
