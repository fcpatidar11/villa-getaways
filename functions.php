<?php
/*
Author: Eddie Machado
URL: http://themble.com/bones/

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, ect.
*/

// LOAD BONES CORE (if you remove this, the theme will break)
require_once( 'library/bones.php' );

// CUSTOMIZE THE WORDPRESS ADMIN (off by default)
require_once( 'library/admin.php' );

// Oracle DB Connection
require_once( 'library/oracle-connect.php' );

/*********************
LAUNCH BONES
Let's get everything up and running.
*********************/

function bones_ahoy() {
  // Copy and paste this require_once line for each post type you have...
  // UNCOMMENT THE BELOW LINE TO SEE THE EXAMPLE CUSTOM POST TYPE IN WORDPRESS
  // require_once('post-types/practice-areas.php');

  // launching operation cleanup
  add_action( 'init', 'bones_head_cleanup' );
  
  // A better title
  add_filter( 'wp_title', 'rw_title', 10, 3 );
  
  // remove WP version from RSS
  add_filter( 'the_generator', 'bones_rss_version' );
  
  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'bones_remove_wp_widget_recent_comments_style', 1 );
  
  // clean up comment styles in the head
  add_action( 'wp_head', 'bones_remove_recent_comments_style', 1 );
  
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'bones_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'bones_scripts_and_styles', 1 );

  // launching this stuff after theme setup
  bones_theme_support();

  // adding sidebars to Wordpress (these are created in functions.php)
  add_action( 'widgets_init', 'bones_register_sidebars' );

  // cleaning up random code around images
  add_filter( 'the_content', 'bones_filter_ptags_on_images' );
  
  // cleaning up excerpt
  add_filter( 'excerpt_more', 'bones_excerpt_more' );

} /* end bones ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'bones_ahoy' );

//* Enqueue Ajax call on wp_enqueue_scripts hook

add_action( 'init', 'script_enqueuer' );

function script_enqueuer() {

   
}


/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
  $content_width = 640;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'post-block-image', 580, 300, true );
add_image_size( 'content-block-image', 740, 583, true );
add_image_size( 'video-block-thumbnail', 350 , 200, true);

add_filter( 'image_size_names_choose', 'bones_custom_image_sizes' );

function bones_custom_image_sizes( $sizes ) {
  return array_merge( $sizes, array(
    'post-block-image' => __('580px by 300px'),
    'content-block-image' => __('740px by 583px'),
    'video-block-thumbanil' => __('350px by 200px'),
  ) );
}

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function bones_register_sidebars() {
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'bonestheme' ),
		'description' => __( 'The first (primary) sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));
	
		register_sidebar(array(
		'id' => 'header',
		'name' => __( 'Header', 'bonestheme' ),
		'description' => __( 'Header Text sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));
	
		register_sidebar(array(
		'id' => 'footer',
		'name' => __( 'Footer', 'bonestheme' ),
		'description' => __( 'Footer Text sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));
} // don't remove this bracket!


/************* REMOVE SUPPORT FOR EMOJIS *********************/

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
add_filter('show_recent_comments_widget_style', function() { return false; });

/************* ADD ACF OPTIONS PAGE *********************/

// Add ACF Options page
// if( function_exists('acf_add_options_page') ) {
//   acf_add_options_page();  
// }
add_action('acf/init', function () {
    acf_add_options_page();
});

/************* REMOVE WPCF7 JS / CSS, ADDING BACK IN ONLY WHEN NEEDED *********************/

//add_filter( 'wpcf7_load_js', '__return_false' );
//add_filter( 'wpcf7_load_css', '__return_false' );

function wrv_enqueue_wpcf7_scripts() {

  if (is_singular()) {
    $post = get_post();

    if (has_shortcode($post->post_content, 'contact-form-7')) {

      if (function_exists('wpcf7_enqueue_scripts')) {
        wpcf7_enqueue_scripts();
      }

      if (function_exists('wpcf7_enqueue_styles')) {
        wpcf7_enqueue_styles();
      }
    }
  }
}

add_action('wp_enqueue_scripts', 'wrv_enqueue_wpcf7_scripts', 99);

// Function to get archives list with limited months
function wpb_limit_archives() { 
 
    $my_archives = wp_get_archives(array(
        'type' => 'monthly', 
        'limit' => 10,
        'echo' => 0
    ));
         
    return $my_archives; 
 
} 
 
// Create a shortcode
add_shortcode('wpb_custom_archives', 'wpb_limit_archives'); 

// Function to get cateogry list with limited
function wpb_limit_categories() { 
    
    $my_categories = wp_list_categories(array(
        'number'=> 10,
        'title_li' => '',
        'echo' => 0
    ));
    
    return $my_categories;
 
} 
 
// Create a shortcode
add_shortcode('wpb_custom_categories', 'wpb_limit_categories'); 
 
// Enable shortcode execution in text widget
add_filter('widget_text', 'do_shortcode'); 
add_filter('wpcf7_autop_or_not', '__return_false');
add_action('init', function() {
    $url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
    $word_villas_1 = "destination/villa-rentals";
    $word_villas_2 = "villas/browse";
    $word_villa_1 = "villas/display/Villa";
    $word_villas_3 = "beachfront";
    $word_villas_4 = "wedding";
    $word_villas_5 = "villas/holidaysAvailability/holiday";
    $word_villas_6 = "corporatevillas";
    $word_villas_7 = "exclusives";
    $word_villa_2 = "villa-rentals";
    $word_villa_list ="villas-list";
    
  
    
    
    if( (strpos($url_path, $word_villas_1) !== false) 
        || (strpos($url_path, $word_villas_2) !== false)
        || (strpos($url_path, $word_villas_3) !== false) 
        || (strpos($url_path, $word_villas_4) !== false) 
        || (strpos($url_path, $word_villas_5) !== false) 
        || (strpos($url_path, $word_villas_6) !== false) 
        || (strpos($url_path, $word_villas_7) !== false) 
    ){
        
        
        $load = locate_template('templates/page-villas.php', true);
        if ($load) {
            exit(); // just exit if template was found and loaded
        }
    } elseif( (strpos($url_path, $word_villa_1) !== false) 
        || (strpos($url_path, $word_villa_2) !== false)
    ){
        
        
        $load = locate_template('templates/page-villa.php', true);
        if ($load) {
            exit(); // just exit if template was found and loaded
        }
    }elseif(strpos($url_path, $word_villa_list)!==false ){
        
        $load = locate_template('templates/page-villa-list.php', true);
        if ($load) {
            exit(); // just exit if template was found and loaded
        } 
    }
});


/* DON'T DELETE THIS CLOSING TAG */ 


add_filter('wpcf7_form_tag_data_option', function($data, $options, $args) {
	$data = [];
	foreach ($options as $option) {
		
		if ($option === 'destination') {
		    $conn = oracleDbConnection();
            $destinations = fetchDestinations($conn);
            if($destinations) {
                $data = array_merge($data, $destinations);
            }
			
		}
	}
	return $data;
}, 10, 3);

function villa_booking_rewrite_rule() {
    add_rewrite_rule(
        '^villa-booking-([0-9]+)/?',
        'index.php?pagename=villa-booking&booking_id=$matches[1]',
        'top'
    );
}
add_action('init', 'villa_booking_rewrite_rule');

// Make sure to flush rewrite rules when your theme or plugin is activated
function villa_booking_flush_rewrite_rules() {
    villa_booking_rewrite_rule();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'villa_booking_flush_rewrite_rules');

function villa_booking_query_vars( $query_vars ) {
    $query_vars[] = 'booking_id';
    return $query_vars;
}
add_filter( 'query_vars', 'villa_booking_query_vars' );

// Modify the template to use the booking ID from the query var
function villa_booking_template_redirect() {   get_query_var( 'booking_id' );
    if ( get_query_var( 'pagename' ) == 'villa-booking' && get_query_var( 'booking_id' ) ) {
        include( locate_template( 'villa-booking.php' ) );
       // exit;
    }
}
add_action( 'template_redirect', 'villa_booking_template_redirect' );

add_filter('wpseo_sitemap_index', function ($sitemap_index) {
   
   
   $conn = oracleDbConnection();
   $lists = fetchVillasList($conn);

    $sitemap_links =[];
   
   if(!empty($lists)){

      foreach($lists as $list){
            if(isset($list['SLUG'])){
                  $sitemaplinks[] = array('url' =>home_url('/villas-list/').$list['SLUG'], 'lastmod' =>strtotime($list['CREATED_AT']));
            }
            
      }
    }
    // Append each custom URL to the sitemap index
    foreach ($sitemaplinks as $url) {
        $sitemap_index .= '<sitemap><loc>' . esc_url($url['url']) . '</loc><lastmod>'.gmdate('Y-m-d\TH:i:s\Z', $url['lastmod']).'</lastmod></sitemap>';
    }

    return $sitemap_index;
});


// popular posts shortcode_atts

function custom_popular_posts_shortcode($atts) {

    $atts = shortcode_atts(
        array(
            'posts' => 5,
            'title' => 'POPULAR POSTS'
        ),
        $atts,
        'popular_posts'
    );

    $posts = get_posts(array(
        'post_type'        => 'post',
        'post_status'      => 'publish',
        'numberposts'      => intval($atts['posts']),
        'orderby'          => 'comment_count',
        'order'            => 'DESC',
        'suppress_filters' => true
    ));

    if (empty($posts)) {
        return '<p>No posts found.</p>';
    }

    ob_start();
    ?>

    <div class="popular-posts-widget">
       <h3 class="category-widget-title">
            <?php echo esc_html($atts['title']); ?>
        </h3>
     
    <?php    foreach ($posts as $post) {

    $post_id = $post->ID;
    $title   = get_the_title($post_id);
    $link    = get_permalink($post_id);
    $date    = get_the_date('F j, Y', $post_id);
    $comments = get_comments_number($post_id);
    $thumb   = get_the_post_thumbnail_url($post_id, 'thumbnail');
    ?>

    <div class="popular-post">

        <div class="post-thumb">
            <a href="<?php echo esc_url($link); ?>">
                <?php if ($thumb) : ?>
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>">
                <?php endif; ?>
            </a>
        </div>

        <div class="post-content">
            <h4>
                <a href="<?php echo esc_url($link); ?>">
                    <?php echo esc_html($title); ?>
                </a>
            </h4>

            <div class="post-meta">
                <span><?php echo esc_html($date); ?></span>
                <span><?php echo intval($comments); ?> Comments</span>
            </div>
        </div>

    </div>

    <?php
} ?>

    </div>

    <?php
    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('popular_posts', 'custom_popular_posts_shortcode');


// Categories list with count

function custom_category_list_shortcode($atts) {

    $atts = shortcode_atts(
        array(
            'title'      => 'CATEGORIES',
            'hide_empty' => true,
        ),
        $atts,
        'category_list'
    );

    $categories = get_categories(array(
        'hide_empty' => filter_var($atts['hide_empty'], FILTER_VALIDATE_BOOLEAN),
        'orderby'    => 'name',
        'order'      => 'ASC',
    ));

    if (empty($categories)) {
        return '<p>No categories found.</p>';
    }

    ob_start();
    ?>

    <div class="custom-category-widget">

        <h3 class="category-widget-title">
            <?php echo esc_html($atts['title']); ?>
        </h3>

        <ul class="category-list">

            <?php foreach ($categories as $category) : ?>

                <li>
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                        <?php echo esc_html($category->name); ?>
                    </a>

                    <span class="category-count">
                        <?php echo intval($category->count); ?>
                    </span>
                </li>

            <?php endforeach; ?>

        </ul>

    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('category_list', 'custom_category_list_shortcode');


// Tags list

function custom_tags_shortcode($atts) {

    $atts = shortcode_atts(
        array(
            'title' => 'TAGS',
            'number' => 50,
        ),
        $atts,
        'blog_tags'
    );

    $tags = get_tags(array(
        'orderby' => 'name',
        'order'   => 'ASC',
        'number'  => intval($atts['number']),
    ));

    if (empty($tags)) {
        return '<p>No tags found.</p>';
    }

    ob_start();
    ?>

    <div class="custom-tags-widget">

        <h3 class="tags-widget-title">
            <?php echo esc_html($atts['title']); ?>
        </h3>

        <div class="tags-container">

            <?php foreach ($tags as $tag) : ?>

                <a
                    href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>"
                    class="tag-item"
                    title="<?php echo esc_attr($tag->count . ' posts'); ?>"
                >
                    <?php echo esc_html($tag->name); ?>
                </a>

            <?php endforeach; ?>

        </div>

    </div>

    <?php

    return ob_get_clean();
}

add_shortcode('blog_tags', 'custom_tags_shortcode');

