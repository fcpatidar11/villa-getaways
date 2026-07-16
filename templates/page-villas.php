<?php 
/* Template Name: Multiple Villas */
get_header(); ?>

<style>
.banner-description div {
    display: block !important;
}
</style>
<?php $conn = oracleDbConnection();
global $location_name;
global $destination_name;
global $des_name;
global $loc_name;
global $location;
global $loc;
global $title;
global $heading;
$new_location_name = "";
$image = "2023/03/menu-villas-4.png";
$destination_id = ( isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] ) ? $_REQUEST['destination_id'] : "";
$page = ( isset($_REQUEST['page']) && $_REQUEST['page'] ) ? $_REQUEST['page'] : 1;
$price = ( isset($_REQUEST['price']) && $_REQUEST['price'] ) ? $_REQUEST['price'] : "";
$location_id = "";
$location_ids_arr = [];
$location_ids_arr = [];
$location_ids_arr = [];
$locations = [];
$result = "";
$locationAndRegins = [];
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

// if($destination_id) {
//     $des_title = fetchDestinationTitle($conn, $destination_id);
// }

function get_sub_string($string, $search) {
    $result = "";
    $index = strpos($string, $search);
    if ($index !== false) {
        return $result = substr($string, $index + strlen($search));
    }
    return null;
}

function string_between_two_string($str, $starting_word, $ending_word) {
    $result = "";
    $subtring_start = strpos($str, $starting_word);
    //Adding the starting index of the starting word to
    //its length would give its ending index
    $subtring_start += strlen($starting_word); 
    //Length of our required sub string
    $size = strpos($str, $ending_word, $subtring_start) - $subtring_start; 
    
    // Return the substring from the index substring_start of length size
    $result = substr($str, $subtring_start, $size);
    return $result;
}


function getLastSubstringAfterSecondIn($url) {
    // Find the position of the first occurrence of "in"
    $delimiter = '-in-';
    $lastPos = strrpos($url, $delimiter);

    if ($lastPos !== false) {
        return substr($url, $lastPos + strlen($delimiter));
    }

    return false; // '-in-' not found
}


if( strpos($url_path, $word_villas_1) !== false)  {
    
   
    $token = "in-";
    $location_name = get_sub_string($url_path, $token);
   
    
   
     $destination_name = string_between_two_string($url_path, "rentals-", "-in");
   
     $region_url_name = getLastSubstringAfterSecondIn($url_path);
    
  
    $path = trim(parse_url($url_path, PHP_URL_PATH), '/');

    // Count how many times "in" appears as a word
    $in_count = substr_count($path, '-in-');
    
    if($in_count==1){
         $region_url_name ="";
    }
    
    
    if($region_url_name) {
        $destination = fetchVillasByLocationName($conn, $region_url_name, $page, $price);
    
       
        $location_url_name = string_between_two_string($url_path, "in-", "-in");
        
        $region_heading = ucwords( implode(" ", explode("-", $location_url_name)) );
        
        $heading = ucwords( implode(" ", explode("-", $region_url_name)) );
        $lo_name = fetchRegionByName($conn, $heading);
        $regin_id = $lo_name['REGION_ID'];
        if($destination_name) {
            $destinationDetail = fetchDestinationByName($conn, ucwords( implode(" ", explode("-", $destination_name)) ));
            //$heading = $destinationDetail["NAME"];
          
          
            $image = $destinationDetail["IMAGE"];
            $destination_id = $destinationDetail["DESTINATION_ID"];
        }
    }elseif($location_name) {
        $destination = fetchVillasByLocationName($conn, $location_name, $page, $price);
        
      
        $heading = ucwords( implode(" ", explode("-", $location_name)) );
        
        $lo_name = fetchLocationByName($conn, $heading);
        $new_location_name = $destination[0]["LOCATION_NAME"];

        if(!empty($lo_name)) {
            $location_id= $lo_name['LOCATION_ID'];
        }else {
            $lo_name = fetchRegionByName($conn, $heading);
            $regin_id = $lo_name['REGION_ID'];
        }
        if($destination_name) {
            $destinationDetail = fetchDestinationByName($conn, ucwords( implode(" ", explode("-", $destination_name)) ));
            //$heading = $destinationDetail["NAME"];
            $image = $destinationDetail["IMAGE"];
            $destination_id = $destinationDetail["DESTINATION_ID"];
        }
        
         
        
    } else {
        
    
        $token = "rentals-";
         $destination_name = get_sub_string($url_path, $token);
        if($destination_name) {
            $destination = fetchVillasByDestinationName($conn, $destination_name, $page);
            $destinationDetail = fetchDestinationByName($conn, ucwords( implode(" ", explode("-", $destination_name)) ));
            
            $heading = $destinationDetail["NAME"];
            $image = $destinationDetail["IMAGE"];
            $destination_id = $destinationDetail["DESTINATION_ID"];
        }
    }
}


if( strpos($url_path, $word_villas_3) !== false)  {
    
      
  
    if(isset($_REQUEST['dest_id']) && $_REQUEST['dest_id']){
        if(isset($_REQUEST['loc_ids']) && $_REQUEST['loc_ids'] ){
            $destination = fetchAbsoluteBeachFrontVillas($conn, $page, $price, $_REQUEST['dest_id'], $_REQUEST['loc_ids']);
        }else {
            $destination = fetchAbsoluteBeachFrontVillas($conn, $page, $price, $_REQUEST['dest_id']);
        }
    } else {
        $destination = fetchAbsoluteBeachFrontVillas($conn, $page, $price);
    }
    //$destination = fetchAbsoluteBeachFrontVillas($conn, $page, $price);
    $heading = "Absolute Beachfront Villas";
}

if( strpos($url_path, $word_villas_4) !== false)  {
     
   
    
    if(isset($_REQUEST['dest_id']) && $_REQUEST['dest_id']){
        if(isset($_REQUEST['loc_ids']) && $_REQUEST['loc_ids'] ){
            $destination = fetchWeddingVillas($conn, $page, $price, $_REQUEST['dest_id'], $_REQUEST['loc_ids']);
        }else {
            $destination = fetchWeddingVillas($conn, $page, $price, $_REQUEST['dest_id']);
        }
    }else {
        $destination = fetchWeddingVillas($conn, $page, $price);
    }
    $heading = "Wedding Villas";
}

if( strpos($url_path, $word_villas_5) !== false)  {
    if(isset($_REQUEST['dest_id']) && $_REQUEST['dest_id']){
        if(isset($_REQUEST['loc_ids']) && $_REQUEST['loc_ids'] ){
            $destination = fetchHollidaySeasonVillas($conn, $page, $price, $_REQUEST['dest_id'], $_REQUEST['loc_ids']);
        }else {
            $destination = fetchHollidaySeasonVillas($conn, $page, $price, $_REQUEST['dest_id']);
        }
    }else {
        $destination = fetchHollidaySeasonVillas($conn, $page, $price);
    }
    $heading = "Holiday Season Villas";
}

if( strpos($url_path, $word_villas_6) !== false)  {
     
    if(isset($_REQUEST['dest_id']) && $_REQUEST['dest_id']){
        if(isset($_REQUEST['loc_ids']) && $_REQUEST['loc_ids']) {
            $destination = fetchCorporateRetreatsVillas($conn, $page, $price, $_REQUEST['dest_id'], $_REQUEST['loc_ids']);
        }else {
            $destination = fetchCorporateRetreatsVillas($conn, $page, $price, $_REQUEST['dest_id']);
        }
    }else {
        $destination = fetchCorporateRetreatsVillas($conn, $page, $price);
    }
    $heading = "Corporate Retreats Villas";
}


if( strpos($url_path, $word_villas_7) !== false)  {
    
  
    if(isset($_REQUEST['dest_id']) && $_REQUEST['dest_id']){
        if(isset($_REQUEST['loc_ids']) && $_REQUEST['loc_ids'] ){
            $destination = fetchExclusiveVillas($conn, $page, $price, $_REQUEST['dest_id'], $_REQUEST['loc_ids']);
        }else {
            $destination = fetchExclusiveVillas($conn, $page, $price, $_REQUEST['dest_id']);
        }
    }else {
        $destination = fetchExclusiveVillas($conn, $page, $price);
        
    }
    
   
    $heading = "Exclusive Villas";
}


if($destination_id){
   
    //$locations = fetchDestinationLocations($conn, $destination_id);
    $destination_id = (int) $destination_id;
    $count = fetchTotalCountOfLocations($conn, $destination_id);
    
    if($count['COUNT'] !== "0" && $count['COUNT'] !== null) {
       
        $locationAndRegins = fetchDestinationWithLocationsAndRegions($conn, $destination_id);
    }else {
      
        $locations = fetchLocationsAndCount($conn, $destination_id);
    }
}

foreach(vg_request_array('location_id') as $id) {
    if( $id )
        $location_ids_arr[] = $id;
}

if( $location_ids_arr ) {

    if( is_array($location_ids_arr) && sizeof($location_ids_arr) > 1) {
        
        $destination = fetchDestination($conn, $destination_id);
        $heading = $destination["NAME"];
        $destination_name = strtolower(str_replace(" ", "-", $heading));
        $image = $destination["IMAGE"];
    }
    if( is_array($location_ids_arr) && sizeof($location_ids_arr) == 1) {
         
        $location = fetchLocation($conn, $location_ids_arr[0]);
        if($destination_id) {
            $destination = fetchDestination($conn, $destination_id);
            
          
            
            $heading = $destination["NAME"];
            $destination_name = strtolower(str_replace(" ", "-", $heading));
            $image = $destination["IMAGE"];
        }
        $heading = $location["NAME"];
    }
    
}

if( is_array($location_ids_arr) && sizeof($location_ids_arr) == 0) {
    
   
    if($destination_id) {
        if(!isset($destination) && sizeof($location_ids_arr) <= 20) {
            $destination = fetchDestination($conn, $destination_id);
            if(isset($destination)) {
                $heading = $destination["NAME"];
                $destination_name = strtolower(str_replace(" ", "-", $heading));
                $image = $destination["IMAGE"];
            }
        }
    }
}
$desTitle = "";
$request_region_ids = vg_request_array('region_id');
$request_location_ids = vg_request_array('location_id');
if( $request_region_ids ) {
    $desTitle = fetchRegionTitle($conn, $request_region_ids[0]);
}elseif( $request_location_ids ) {
    $desTitle = fetchLocationTitle($conn, $request_location_ids[0]);
}else{
    if(!empty($regin_id)) {
        $desTitle = fetchRegionTitle($conn, $regin_id);
        
    }elseif($location_id) {
        $desTitle = fetchLocationTitle($conn, $location_id);
        
      

    }elseif($destination_id) {
        $desTitle = fetchDestinationTitle($conn, $destination_id);
       
    }
}




?>
<?php $title = $heading ? $heading : ( $location_name ? $location_name : $destination_name );    

$region_heading_breadcrum = strtolower(str_replace([" "], "-", $region_heading ?? ""));
//$region_heading_breadcrum = strtolower($region_heading);
?>
<div class="dest-info-bar">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo home_url(); ?>">Home</a></li>
                    <?php if($destination_name) { ?>
                    <li class="breadcrumb-item"><a href="<?php echo home_url('destination/villa-rentals-' . $destination_name); ?>"><?php echo ucfirst(str_replace("-", " ", $destination_name)); ?></a></li>
                    <!--<li class="breadcrumb-item"><?php echo ucfirst($destination_name); ?></a></li>-->
                    <?php } ?>
                    <?php 
                    if($region_heading_breadcrum) { ?>
                        <li class="breadcrumb-item"><a href="<?php echo home_url('destination/villa-rentals-' . $destination_name . '-in-'. $region_heading_breadcrum); ?>"><?php echo ucwords(str_replace(["-", "/"], " ", $region_heading_breadcrum)); ?></a></li>
                   <?php }
                    ?>
                    
                    <?php if($title && ucfirst($destination_name) != $title) { ?><li class="breadcrumb-item"><a href="<?php echo home_url('destination/villa-rentals-' . $destination_name . '-in-'. $region_heading_breadcrum.'-in-'.$region_url_name); ?>"><?php echo ucwords(str_replace(["-", "/"], " ", $title)); ?></a></li><?php } ?>
                    <!--<li class="breadcrumb-item"><a href="<?php echo home_url('destination/villa-rentals-' . $country_name); ?>"><?php echo $villa_details["DESTINATION_NAME"]; ?></a></li>-->
                    <!--<li class="breadcrumb-item"><a href="<?php echo home_url('destination/villa-rentals-' . $country_name . '-in-' . $location_name); ?>"><?php echo $villa_details["LOCATION_NAME"]; ?></a></li>-->
                    <!--<li class="breadcrumb-item active" aria-current="page">Villa <?php echo $villa_details["VG_NUMBER"]; ?></li>-->
                </ol>
            </nav>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <h4 class="page-title">DESTINATION <?php echo strtoupper($villa_details["DESTINATION_NAME"] ?? ""); ?></h4>
        </div>
        <div class="col-lg-4 col-md-6 next-villa col-sm-12 col-xs-12">
            <!--<a href="#">NEXT VILLA <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon-arrow-right.png" alt="Next Villa"></a>-->
        </div>
    </div>
</div>

<?php 
    $title = $heading ? $heading : ( $location_name ? $location_name : $destination_name ); 
    

  
   ?>

<!-- Start Main -->
<main class="position-relative">
    <ul id="page-banner">
        <div class="banner-description">
            <h1 class="banner-title"><?php echo isset($desTitle['TITLE']) && strlen($desTitle['TITLE']) > 1 ? $desTitle['TITLE'] : strtoupper(str_replace("-", " ", $title))." Villas"; ?></h1>
            <?php
            
             echo $desTitle['DESCRIPTION'] ? $desTitle['DESCRIPTION'] : ""; ?>
        </div>
        
        <li title="<?php echo $heading; ?>">
            <?php if( $image ) { ?>
                <img src="<?php echo home_url("/wp-content/uploads/" . $image); ?>" alt="<?php echo $heading; ?>">
            <?php } else { ?>
                <img src="<?php echo home_url("/wp-content/uploads/2023/03/menu-villas-4.png"); ?>" alt="<?php echo $heading; ?>">
            <?php } ?>
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
                    <!--<div class="col-8 float-left">-->
                    <div class="col-lg-4 col-md-3 col-sm-12 col-xs-12 float-left">
                        <?php 
                        $title = $heading ? $heading : ( $location_name ? $location_name : $destination_name ); 
                        ?>
                        <h3><?php echo strtoupper(str_replace("-", " ", $title)); ?></h3>
                    </div>
                    <!--<div class="col-4 d-flex">-->
                    <div class="col-lg-8 col-md-9 col-sm-12 col-xs-12 d-flex">
                        <div class="filterBarRooms input-group date-range">
                            <?php echo get_template_part('template-parts/bedrooms', 'form', ["destination_id" => $destination_id, "location_id" => $location_id]); ?>
                            <?php //echo get_template_part('template-parts/dates', 'form'); ?>
                        </div>
                        <?php //echo get_template_part('template-parts/price', 'form'); ?>
                        
                    </div>
                </div>
                
                
                <!-- Villa Listings -->
                <?php 
                echo get_template_part('template-parts/villa', 'info', ["destination" => $destination, 'url' => $url_path]); ?>
                
                <div class="bottom_content_section">
          
                        <?php if($desTitle['CONTENT_1']){?>
                        <div class="content_1" style="background:<?php echo $desTitle['CONTENT_BG_COLOUR_1'] ?>;">
                        <?php echo $desTitle['CONTENT_1'];?>
                        </div>
                        <?php } ?>
                        <?php if($desTitle['CONTENT_2']){?>
                        <div class="content_2" style="background:<?php echo $desTitle['CONTENT_BG_COLOUR_2'] ?>;">
                        <?php echo $desTitle['CONTENT_2'];?>
                        </div>
                        <?php } ?>
                        <?php if($desTitle['CONTENT_3']){?>
                        <div class="content_3" style="background:<?php echo $desTitle['CONTENT_BG_COLOUR_3'] ?>;">
                        <?php echo $desTitle['CONTENT_3'];?>
                        </div>
                        <?php } ?>
                        <?php if($desTitle['CONTENT_4']){?>
                        <div class="content_4" style="background:<?php echo $desTitle['CONTENT_BG_COLOUR_4'] ?>;">
                        <?php echo $desTitle['CONTENT_4'];?>
                        </div>
                        <?php } ?>
                </div>
        
      
                
            </div>

            <div class="col-md-12 col-lg-3">
                <div class="filterSidebar">
                    <!--<div class="header">-->
                    <!--    <div class="input-group date-range">-->
                    <!--        <?php echo get_template_part('template-parts/dates', 'form'); ?>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    <div class="filterOpt">
                        <a class="alpha-acc-link" data-toggle="collapse" href="#filterLocations" role="button" aria-expanded="false" aria-controls="filterLocations">
                            REGIONS & LOCATIONS
                        </a>
                        <?php
                        if(isset($locationAndRegins) && count($locationAndRegins) > 0){
                        ?>
                        <div class="collapse alpha-acc-body show" id="filterLocations">
                            <?php 
                            $country_name = strtolower(str_replace(" ", "-", $heading));
                            
                            if(is_array($locationAndRegins) && sizeof($locationAndRegins) > 0){
                                foreach($locationAndRegins as $location => $regions) {
                                    $location_name = strtolower(str_replace(" ", "-", $location));
                                    $location_name = strtolower(str_replace([" ", "/"], "-", $location));

                                    $locationLowerCaseNew = strtolower(str_replace(" ", "-", $locationLowerCase ?? ""));
                                    $locationLowerCase = strtolower(str_replace("-", " ", $location_name));
                                    $titleLowerCase = strtolower($title);
                                    $new_location_name = strtolower($new_location_name);
                                    $url_location_name = strtolower(str_replace("-", " ", $location_url_name ?? ""));
                                // href="<?php echo home_url('destination/villa-rentals-' . ($destination_name ? $destination_name: $country_name) . '-in-' . $location_name);
                                ?>
                                <div class="filterRegions">
                                    <a data-toggle="collapse" href="#<?php echo $location_name; ?>" role="button" aria-expanded="false" aria-controls="filterLocations" class="sub-acc-link collapsed" >
                                        <?php echo $location.$regions['TOTAL']; ?>
                                    </a>
                                    <div class="collapse <?php echo $locationLowerCase == $titleLowerCase ? "show": ($locationLowerCase == $new_location_name ? "show": ($locationLowerCase == $url_location_name ? "show": "") );?>" id="<?php echo $location_name; ?>">
                                        
                                        <div class="sub-acc-body">
                                             <ul>
                                            <?php
                                            foreach($regions as $region) {
                                                $region_location_name  = strtolower(str_replace(" ", "-", $region['LOCATION_NAME']));
                                                $region_name = strtolower(str_replace(" ", "-", $region['REGION_NAME']));
                                                
                                        ?>
                                            <li><a href="<?php echo home_url('destination/villa-rentals-' . ($destination_name ? $destination_name: $country_name) . '-in-' .$region_location_name . '-in-'. $region_name) ?>" ><?php echo $region['REGION_NAME']." (".$region['TOTAL'].")"; ?></a></li>
                                            
                                        
                                        <?php } ?>
                                            <ul>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                        <?php } ?>
                        </div>
                        <?php } else { ?>
                        <div class="collapse alpha-acc-body show" id="filterLocations">
                            <?php 
                            $country_name = strtolower(str_replace(" ", "-", $heading));
                            if(is_array($locations) && sizeof($locations) > 0){
                                foreach($locations as $location => $value) {
                                $location_name = strtolower(str_replace(" ", "-", $location));
                                //data-toggle="collapse" href="#locA" role="button" aria-expanded="false" aria-controls="filterLocations" 
                            ?>
                            <div class="filterRegions">
                                <a href="<?php echo home_url('destination/villa-rentals-' . ($destination_name ? $destination_name: $country_name) . '-in-' . $location_name); ?>" class="sub-acc-link collapsed" >
                                    <?php echo $location." (".$value['TOTAL'].")"; ?>
                                </a>
                            </div>
                            <?php } ?>
                        <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- Filter Sidebar End -->
                
                <div class="sidebar-nav">
                    <ul class="top">
                        <?php
                        if(have_rows('experience_menu_items', 'options')):
                            while(have_rows('experience_menu_items', 'options')): the_row();
                            
                            if($destination_id){
                                $link = get_sub_field('link').'?dest_id='.$destination_id;
                                if(sizeof($location_ids_arr) > 1){
                                    $location_ids_ar = implode(",",$location_ids_arr);
                                    $link = get_sub_field('link').'?dest_id='.$destination_id.'&loc_ids='.$location_ids_ar;
                                }elseif(sizeof($location_ids_arr) == 1) {
                                    $link = get_sub_field('link').'?dest_id='.$destination_id.'&loc_ids='.$location_ids_arr[0];
                                }elseif(!empty($lo_name)) {
                                    $link = get_sub_field('link').'?dest_id='.$destination_id.'&loc_ids='.$lo_name['LOCATION_ID'];
                                }
                            }else {
                               
                               if(isset($_REQUEST['dest_id']) && $_REQUEST['dest_id']){
                                    if(isset($_REQUEST['loc_ids']) && $_REQUEST['loc_ids']) {
                                        
                                        $link = get_sub_field('link').'?dest_id='.$_REQUEST['dest_id'].'&loc_ids='.$_REQUEST['loc_ids'];
                                    }else {
                                        $link = get_sub_field('link').'?dest_id='.$_REQUEST['dest_id'];
                                    }
                                }else {
                                    $link = get_sub_field('link');
                                }
                            }
                            
                        ?>
                        <li style="background: url(<?php the_sub_field('image'); ?>) center center no-repeat; background-size: cover;">
                            <a href="/<?php echo $link ?>"><?php the_sub_field('title'); ?></a>
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

    <?php //echo get_template_part('template-parts/team', 'member'); 
    $abc ="abc";
    ?>

</div>




<?php get_footer(); ?>