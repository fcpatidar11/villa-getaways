<?php 
/* Template Name: Villas list */
get_header();

$conn = oracleDbConnection();
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


$slug = basename($url_path);
$singleVilla = fetchSingleVillasList($conn,$slug);


$location_name = $singleVilla['NAME'];
$location_desc = $singleVilla['DESCRIPTION'];
$location_image = $singleVilla['IMAGE'];
$location_id = $singleVilla['VILLA_LIST_ID'];

$destination_id = $location_id;
$villas = fetchVillasFromVillaList($conn, $location_id, $page, $price="low", $no_of_bedrooms = 1);



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


// function getLastSubstringAfterSecondIn($url) {
//     // Find the position of the first occurrence of "in"
//      $firstPos = strpos($url, "in");
   
   
//     // Check if the first occurrence is found
//     if ($firstPos !== false) {
//         // Find the position of the second occurrence of "in" starting from the position after the first occurrence
//         $secondPos = strpos($url, "in", $firstPos + strlen("in"));

//         // Check if the second occurrence is found
//         if ($secondPos !== false) {
//             // Check if there is at least one more occurrence of "in" after the second occurrence
//             $thirdPos = strpos($url, "in", $secondPos + strlen("in"));
//             if ($thirdPos !== false) {
//                 // Get the value after the third occurrence of "in"
//                 $result = substr($url, $thirdPos + strlen("in")+1); // Adding length of "in" to skip it
//                 return $result;
//             }
//             return false;
//         }
//     }

//     // If the function reaches here, it means the second occurrence of "in" was not found
//     return false;
// }

function getLastSubstringAfterSecondIn($url) {
    // Find the position of the first occurrence of "in"
    $delimiter = '-in-';
    $lastPos = strrpos($url, $delimiter);

    if ($lastPos !== false) {
        return substr($url, $lastPos + strlen($delimiter));
    }

    return false; // '-in-' not found
}

// if($destination_id){
//     //$locations = fetchDestinationLocations($conn, $destination_id);
//     $destination_id = (int) $destination_id;
//     $count = fetchTotalCountOfLocations($conn, $destination_id);
    
//     if($count['COUNT'] !== "0" && $count['COUNT'] !== null) {
//         $locationAndRegins = fetchDestinationWithLocationsAndRegions($conn, $destination_id);
//     }else {
//         $locations = fetchLocationsAndCount($conn, $destination_id);
//     }
// }

// if( isset($_REQUEST['location_id']) && $_REQUEST['location_id'] ) {
//     foreach($_REQUEST['location_id'] as $id) {
//         if( $id )
//             $location_ids_arr[] = $id;
//     }
    
//     if( is_array($location_ids_arr) && sizeof($location_ids_arr) > 1) {
//         $destination = fetchDestination($conn, $destination_id);
//         $heading = $destination["NAME"];
//         $destination_name = strtolower(str_replace(" ", "-", $heading));
//         $image = $destination["IMAGE"];
//     }
//     if( is_array($location_ids_arr) && sizeof($location_ids_arr) == 1) {
//         $location = fetchLocation($conn, $location_ids_arr[0]);
//         if($destination_id) {
//             $destination = fetchDestination($conn, $destination_id);
//             $heading = $destination["NAME"];
//             $destination_name = strtolower(str_replace(" ", "-", $heading));
//             $image = $destination["IMAGE"];
//         }
//         $heading = $location["NAME"];
//     }
    
// }

// if( is_array($location_ids_arr) && sizeof($location_ids_arr) == 0) {
//     if($destination_id) {
//         if(!isset($destination) && sizeof($location_ids_arr) <= 20) {
//             $destination = fetchDestination($conn, $destination_id);
//             if(isset($destination)) {
//                 $heading = $destination["NAME"];
//                 $destination_name = strtolower(str_replace(" ", "-", $heading));
//                 $image = $destination["IMAGE"];
//             }
//         }
//     }
// }


?>
<?php $title = $heading ? $heading : ( $location_name ? $location_name : $destination_name );    

$region_heading_breadcrum = strtolower(str_replace([" "], "-", $region_heading));
//$region_heading_breadcrum = strtolower($region_heading);
?>
<div class="dest-info-bar">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo home_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><?php echo $location_name;?></li>
                    <!--<li class="breadcrumb-item"><a href="<?php echo home_url('destination/villa-rentals-' . $country_name); ?>"><?php echo $villa_details["DESTINATION_NAME"]; ?></a></li>-->
                    <!--<li class="breadcrumb-item"><a href="<?php echo home_url('destination/villa-rentals-' . $country_name . '-in-' . $location_name); ?>"><?php echo $villa_details["LOCATION_NAME"]; ?></a></li>-->
                    <!--<li class="breadcrumb-item active" aria-current="page">Villa <?php echo $villa_details["VG_NUMBER"]; ?></li>-->
                </ol>
            </nav>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <h4 class="page-title">DESTINATION <?php echo strtoupper($villa_details["DESTINATION_NAME"]); ?></h4>
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
            <h1 class="banner-title"><?php echo $title." Villas"; ?></h1>
            <?php echo $location_desc ? $location_desc : ""; ?>
        </div>
        
        <li title="<?php echo $heading; ?>">
            <?php if( $location_image ) { ?>
                <img src="<?php echo home_url("/wp-content/uploads/" . $location_image); ?>" alt="<?php echo $heading; ?>">
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
            <div class="col-md-12 col-lg-12">
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
                echo get_template_part('template-parts/villalist', 'info', ["destination" => $villas, 'url' => $url_path]); ?>
                
            </div>

          
            </div>
        </div>
    </section>

    <?php //echo get_template_part('template-parts/team', 'member'); 
    $abc ="abc";
    ?>

</div>
<style>#filterSidebar_from,#filterSidebar_to{display:none;}.filterBarRooms form {
    width: 40%;
}</style>
<?php get_footer(); ?>