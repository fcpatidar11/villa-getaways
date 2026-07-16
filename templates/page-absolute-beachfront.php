<?php 
/* Template Name: Absolue Breachfront Villas */
get_header();

$conn = oracleDbConnection();
$heading = "";
$image = "2023/03/menu-villas-4.png";
$destination_id = ( isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] ) ? $_REQUEST['destination_id'] : "";
$page = ( isset($_REQUEST['page']) && $_REQUEST['page'] ) ? $_REQUEST['page'] : 1;
$location_ids_arr = [];
$locations = [];
$result = "";
$location_name = "";
$destination_name = "";
$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');

function get_sub_string($string, $search) {
    $result = "";
    $index = strpos($string, $search);
    if ($index !== false) {
        return $result = substr($string, $index + strlen($search));
    }
    return null;
}

function string_between_two_string($str, $starting_word, $ending_word) {
    $subtring_start = strpos($str, $starting_word);
    //Adding the starting index of the starting word to
    //its length would give its ending index
    $subtring_start += strlen($starting_word); 
    //Length of our required sub string
    $size = strpos($str, $ending_word, $subtring_start) - $subtring_start; 
    // Return the substring from the index substring_start of length size
    return substr($str, $subtring_start, $size); 
}

if( strpos($url_path, $word_villas_1) !== false)  {
    $token = "in-";
    $location_name = get_sub_string($url_path, $token);
    $destination_name = string_between_two_string($url_path, "rentals-", "-in");
    if($location_name) {
        $destination = fetchVillasByLocationName($conn, $location_name, $page);
        if($destination_name) {
            $destinationDetail = fetchDestinationByName($conn, ucwords( implode(" ", explode("-", $destination_name)) ));
            $heading = $destinationDetail["NAME"];
            $image = $destinationDetail["IMAGE"];
        }
    } else {
        $token = "rentals-";
        $destination_name = get_sub_string($url_path, $token);
        if($destination_name) {
            $destination = fetchVillasByDestinationName($conn, $destination_name, $page);
            
            $destinationDetail = fetchDestinationByName($conn, ucwords( implode(" ", explode("-", $destination_name)) ));
            $heading = $destinationDetail["NAME"];
            $image = $destinationDetail["IMAGE"];
        }
    }
}

if( strpos($url_path, $word_villas_3) !== false)  {
    $destination = fetchAbsoluteBeachFrontVillas($conn, $page);
    $heading = "Absolute Beachfront Villas";
}

if( strpos($url_path, $word_villas_4) !== false)  {
    $destination = fetchWeddingVillas($conn, $page);
    $heading = "Wedding Villas";
}

//if( strpos($url_path, $word_villas_5) !== false)  {
    
    $destination = fetchHollidaySeasonVillas($conn, $page);
    $heading = "Holliday Season Villas";
//}

if( strpos($url_path, $word_villas_6) !== false)  {
    $destination = fetchCorporateRetreatsVillas($conn, $page);
    $heading = "Corporate Retreats Villas";
}

if( strpos($url_path, $word_villas_7) !== false)  {
    $destination = fetchExclusiveVillas($conn, $page);
    $heading = "Exclusive Villas";
}

if($destination_id){
    $locations = fetchDestinationLocations($conn, $destination_id);
}

if( isset($_REQUEST['location_id']) && $_REQUEST['location_id'] ) {
    foreach($_REQUEST['location_id'] as $id) {
        if( $id )
            $location_ids_arr[] = $id;
    }
}


if( is_array($location_ids_arr) && sizeof($location_ids_arr) > 1) {
    $destination = fetchDestination($conn, $destination_id);
    $heading = $destination["NAME"];
    $image = $destination["IMAGE"];
}
if( is_array($location_ids_arr) && sizeof($location_ids_arr) == 1) {
    $location = fetchLocation($conn, $location_ids_arr[0]);
    if($destination_id) {
        $destination = fetchDestination($conn, $destination_id);
        $image = $destination["IMAGE"];
    }
    $heading = $location["NAME"];
} 

if( is_array($location_ids_arr) && sizeof($location_ids_arr) == 0) {
    if($destination_id) {
        $destination = fetchDestination($conn, $destination_id);
        if($destination) {
            $heading = $destination["NAME"];
            $image = $destination["IMAGE"];
        }
    }
}

?>

<!-- Start Main -->
<main class="position-relative">
    <ul id="page-banner">
        <li title="<?php echo $heading; ?>">
            <img src="<?php echo home_url("/wp-content/uploads/" . $image); ?>" alt="<?php echo $heading; ?>">
        </li>
    </ul>
</main>
<!-- ./ End Main -->


<!-- Start Content -->
<div class="container-fluid">
    <section class="pro-wrapper">

        <div class="row">
            <div class="col-md-12 col-lg-9">
                <div class="filterBar">
                    <div class="col-8 float-left">
                        <?php $title = $heading ? $heading : ( $destination_name ? $destination_name : $location_name ); 
                        ?>
                        <h3><?php echo strtoupper(str_replace("-", " ", $title)); ?></h3>
                    </div>
                    <div class="col-4 float-right">
                        <div class="float-right filterBarRooms">
                            <?php echo get_template_part('template-parts/bedrooms', 'form'); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Villa Listings -->
                <?php echo get_template_part('template-parts/villa', 'info', ["destination" => $destination, 'url' => $url_path]); ?>
                
            </div>
            <div class="col-md-12 col-lg-3">
                <div class="filterSidebar">
                    <div class="header">
                        <div class="input-group date-range">
                            <?php echo get_template_part('template-parts/dates', 'form'); ?>
                        </div>
                    </div>
                    <div class="filterOpt">
                        <a class="alpha-acc-link" data-toggle="collapse" href="#filterLocations" role="button" aria-expanded="false" aria-controls="filterLocations">
                            REGIONS & LOCATIONS
                        </a>
                        <div class="collapse alpha-acc-body" id="filterLocations">
                            <?php 
                            $country_name = strtolower(str_replace(" ", "-", $heading));
                            if(is_array($locations) && sizeof($locations) > 0){
                                foreach($locations as $location) {
                                $location_name = strtolower(str_replace(" ", "-", $location));
                            
                            ?>
                            <div class="filterRegions">
                                <a href="<?php echo home_url('destination/villa-rentals-' . $country_name . '-in-' . $location_name); ?>" class="sub-acc-link collapsed" >
                                    <?php echo $location; ?>
                                </a>
                            </div>
                            <?php } ?>
                        <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- Filter Sidebar End -->
                
                <div class="sidebar-nav">
                    <ul class="top">
                        <?php
                        if(have_rows('experience_menu_items', 'options')):
                            while(have_rows('experience_menu_items', 'options')): the_row();
                            if(get_sub_field('title') == "Holiday Season Villas") {
                                $url = home_url(get_sub_field('link')."&page=1");
                            }else {
                                $url = home_url(get_sub_field('link')."?page=1"); 
                            }
                        ?>
                        <li style="background: url(<?php the_sub_field('image'); ?>) center center no-repeat; background-size: cover;">
                            <a href="<?php echo $url; ?>"><?php the_sub_field('title'); ?></a>
                        </li>
                        <?php
                            endwhile;
                        endif;
                        ?>
                    </ul>
                </div>
                
                </div>
            </div>
        </div>
    </section>

    <?php //echo get_template_part('template-parts/team', 'member'); ?>

</div>
<?php get_footer(); ?>