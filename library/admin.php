<?php
/*
This file handles the admin area and functions.
You can use this file to make changes to the
dashboard. Updates to this page are coming soon.
It's turned off by default, but you can call it
via the functions file.

Developed by: Eddie Machado
URL: http://themble.com/bones/

Special Thanks for code & inspiration to:
@jackmcconnell - http://www.voltronik.co.uk/
Digging into WP - http://digwp.com/2010/10/customize-wordpress-dashboard/
*/

/************* DASHBOARD WIDGETS *****************/

// disable default dashboard widgets
function disable_default_dashboard_widgets() {
	global $wp_meta_boxes;
	//unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);     // Right Now Widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);        // Activity Widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']); // Comments Widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);  // Incoming Links Widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);         // Plugins Widget

	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);       // Quick Press Widget
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);     // Recent Drafts Widget
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);           //
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);         //

	// remove plugin dashboard boxes
	unset($wp_meta_boxes['dashboard']['normal']['core']['yoast_db_widget']);           // Yoast's SEO Plugin Widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard']);        // Gravity Forms Plugin Widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['bbp-dashboard-right-now']);   // bbPress Plugin Widget
}

// removing the dashboard widgets
add_action( 'wp_dashboard_setup', 'disable_default_dashboard_widgets' );


/************* CUSTOM LOGIN PAGE *****************/

// calling your own login css so you can style it

//Updated to proper 'enqueue' method
//http://codex.wordpress.org/Plugin_API/Action_Reference/login_enqueue_scripts
function bones_login_css() {
	wp_enqueue_style( 'bones_login_css', get_template_directory_uri() . '/library/css/login.min.css', false );
}

// changing the logo link from wordpress.org to your site
function bones_login_url() {  return home_url(); }

// changing the alt text on the logo to show your site name
function bones_login_title() { return get_option( 'blogname' ); }

// calling it only on the login page
add_action( 'login_enqueue_scripts', 'bones_login_css', 10 );
add_filter( 'login_headerurl', 'bones_login_url' );
add_filter( 'login_headertitle', 'bones_login_title' );


/************* CUSTOMIZE ADMIN *******************/

function load_custom_wp_admin_style() {
    wp_register_style( 'custom_wp_admin_css', get_template_directory_uri() . '/library/css/admin.min.css', false );
    wp_enqueue_style( 'custom_wp_admin_css' );
}

add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );

// Custom Backend Footer
function bones_custom_admin_footer() {
	_e( '<span id="footer-thankyou">Developed by <a href="https://www.encoresky.com" target="_blank">EncoreSky Technologies</a></span>. Built using <a href="http://themble.com/bones" target="_blank">Bones</a>.', 'bonestheme' );
}

// adding it to the admin area
add_filter( 'admin_footer_text', 'bones_custom_admin_footer' );

function cw_post_type_destinations() {
	$supports = array(
	  'title', // post title
	  'editor', // post content
	  'author', // post author
	  'thumbnail', // featured images
	  'excerpt', // post excerpt
	  'custom-fields', // custom fields
	  'comments', // post comments
	  'revisions', // post revisions
	  'post-formats', // post formats
	);
	$labels = array(
	  'name' => _x('Destinations', 'plural'),
	  'singular_name' => _x('Destination', 'singular'),
	  'menu_name' => _x('Destinations', 'admin menu'),
	  'name_admin_bar' => _x('Destinations', 'admin bar'),
	  'add_new' => _x('Add New', 'add new'),
	  'add_new_item' => __('Add new destination'),
	  'new_item' => __('New destination'),
	  'edit_item' => __('Edit destination'),
	  'view_item' => __('View destination'),
	  'all_items' => __('All destinations'),
	  'search_items' => __('Search destinations'),
	  'not_found' => __('No destination found.'),
	);
	$args = array(
	  'supports' => $supports,
	  'labels' => $labels,
	  'public' => true,
	  'query_var' => true,
	  'rewrite' => array('slug' => 'destination'),
	  'has_archive' => true,
	  'hierarchical' => false,
	);
	register_post_type('destination', $args);
  
	register_taxonomy("categories", 
		array("destination"), 
		array(
			"hierarchical" => true, 
			"label" => "Categories", 
			"singular_label" => "Category", 
			"rewrite" => array(
				'slug' => 'destination', 
				'with_front'=> false
			)
		)
	);

}
add_action('init', 'cw_post_type_destinations');

function cw_post_type_testimonials() {
	$supports = array(
	  'title', // post title
	  'editor', // post content
	  'author', // post author
	  'thumbnail', // featured images
	  'excerpt', // post excerpt
	  'custom-fields', // custom fields
	  'comments', // post comments
	  'revisions', // post revisions
	  'post-formats', // post formats
	);
	$labels = array(
	  'name' => _x('Testimonials', 'plural'),
	  'singular_name' => _x('Testimonial', 'singular'),
	  'menu_name' => _x('Testimonials', 'admin menu'),
	  'name_admin_bar' => _x('Testimonial', 'admin bar'),
	  'add_new' => _x('Add New', 'add new'),
	  'add_new_item' => __('Add new testimonial'),
	  'new_item' => __('New testimonial'),
	  'edit_item' => __('Edit testimonial'),
	  'view_item' => __('View testimonial'),
	  'all_items' => __('All testimonials'),
	  'search_items' => __('Search testimonials'),
	  'not_found' => __('No testimonial found.'),
	);
	$args = array(
	  'supports' => $supports,
	  'labels' => $labels,
	  'public' => true,
	  'query_var' => true,
	  'rewrite' => array('slug' => 'testimonials'),
	  'has_archive' => true,
	  'hierarchical' => false,
	);
	register_post_type('testimonials', $args);
}
add_action('init', 'cw_post_type_testimonials');

function cw_post_type_team_members() {
	$supports = array(
	  'title', // post title
	  'editor', // post content
	  'author', // post author
	  'thumbnail', // featured images
	  'excerpt', // post excerpt
	  'custom-fields', // custom fields
	  'comments', // post comments
	  'revisions', // post revisions
	  'post-formats', // post formats
	);
	$labels = array(
	  'name' => _x('Team Members', 'plural'),
	  'singular_name' => _x('Team Member', 'singular'),
	  'menu_name' => _x('Team Members', 'admin menu'),
	  'name_admin_bar' => _x('Team member', 'admin bar'),
	  'add_new' => _x('Add new', 'add new'),
	  'add_new_item' => __('Add new team member'),
	  'new_item' => __('New team member'),
	  'edit_item' => __('Edit team member'),
	  'view_item' => __('View team member'),
	  'all_items' => __('All team members'),
	  'search_items' => __('Search team members'),
	  'not_found' => __('No team member found.'),
	);
	$args = array(
	  'supports' => $supports,
	  'labels' => $labels,
	  'public' => true,
	  'query_var' => true,
	  'rewrite' => array('slug' => 'team_members'),
	  'has_archive' => true,
	  'hierarchical' => false,
	);
	register_post_type('team_members', $args);
}
add_action('init', 'cw_post_type_team_members');


?>