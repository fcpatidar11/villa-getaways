<?php
$conn = oracleDbConnection();
global $location_name;
global $destination_name;
global $des_name;
global $loc_name;
global $location;
global $loc;
global $title;
global $heading;



if( isset($_REQUEST["is_search"]) && $_REQUEST["is_search"] ) {
    // 86400 = 1 day
    setcookie("__destination_id", $_REQUEST['destination_id'], time() + (86400 * 30), "/", "", true);
    if(isset($_REQUEST['location_id']) && $_REQUEST['location_id']) {
        setcookie("__location_id", implode(",", $_REQUEST['location_id']), time() + (86400 * 30), "/", "", true);
    }
    setcookie("__date_start", $_REQUEST['date_start'], time() + (86400 * 30), "/", "", true);
    setcookie("__date_end", $_REQUEST['date_end'], time() + (86400 * 30), "/", "", true);
    setcookie("__bedrooms", $_REQUEST['bedrooms'], time() + (86400 * 30), "/", "", true);
    setcookie("__page", $_REQUEST['page'], time() + (86400 * 30), "/", "", true);
}

if(isset($_GET['date_start'])){
     setcookie("__date_start", $_REQUEST['date_start'], time() + (86400 * 30), "/", "", true);
}
if(isset($_GET['date_end'])){
     setcookie("__date_end", $_REQUEST['date_end'], time() + (86400 * 30), "/", "", true);
}

$urlPath = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');

$url = $_SERVER['REQUEST_URI'];
preg_match('/\d+/', $url, $matches);
// Extract the matched integer value
if(!empty($matches)) {
    $vg_number = (int) $matches[0];
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_template_directory_uri(); ?>/assets/favicon//favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_template_directory_uri(); ?>/assets/favicon//favicon-16x16.png">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/assets/favicon//site.webmanifest">
    
   
    <!-- Slippry CSS -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/slippry.css">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css">

    <!-- Glyphicon Icons CSS -->
    <link href="<?php echo get_template_directory_uri(); ?>/assets/css/glyphicons.css" rel="stylesheet">

    <!-- Owl Styles -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/owl.carousel.min.css">
    
    
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/select2.min.css">

    <!-- Custom CSS -->
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700|Raleway:300,400,600,700,800&amp;subset=latin-ext" rel="stylesheet">

    <!-- JqueryUI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link href=’https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css’ rel=’stylesheet’>
    <!-- Link Swiper's CSS -->
    <link rel="stylesheet" href="https://idangero.us/swiper/dist/css/swiper.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/bootstrap.min.css">

    <!-- Animation CSS -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/animate.css">
    
    <!-- Master Slider CSS -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/thumbnail-slider.css">
    <!-- Master Slider Skin -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/thumbs2.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css"/>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/lightgallery.min.css" type="text/css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" />
    <link rel="stylesheet" href="https://unpkg.com/@fullcalendar/core@4.4.2/main.min.css" type="text/css" />
    <link rel="stylesheet" href="https://unpkg.com/@fullcalendar/daygrid@4.4.2/main.min.css" type="text/css" />
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/style.css?version=<?php echo time(); ?>">
    
    <?php
        if(is_front_page()) {
            ?>
            <meta name="description" content="Choose from 2000+ Best Luxury Villas &amp; Vacation homes in top destinations worldwide. Too good to be missed, Visit Villa Getaways now &amp; see yourself." />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="sales@villagetaways.com" />
            <meta name="author" content="http://VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <title>Villa Holidays &amp; Luxury Vacation Home Rentals - Villa Getaways</title>
    <?php        
        }
    ?>
    <?php
    
   
    
    
    $wordVillas1 = "destination/villa-rentals";
    $wordVillas2 = "beachfront";
    $wordVillas3 = "exclusives";
    $wordVillas4 = "wedding";
    $wordVillas5 = "holiday";
    $wordVillas6 = "corporatevillas";
    $wordVillas7 = "video";
    $wordVillas8 = "exclusives";
    $wordVillas9 = "villas-list";
    
    if( strpos($urlPath, $wordVillas9) !== false) {
   
       $slug = basename($urlPath);
       $singleVillaList = fetchSingleVillasList($conn,$slug);
    
     
     ?>
      
      
      
        <title><?php if(ltrim($singleVillaList["HEAD_TITLE"]) != ""){ echo $singleVillaList["HEAD_TITLE"]; } else { echo "Villa Getaways"; }?></title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="description" content="<?php echo $singleVillaList["META_DESCRIPTION"]; ?>" />
               
        
  <?php   }
    
    
    if( strpos($urlPath, $wordVillas1) !== false) {
        $token = "rentals-";
        
    

        
        $header_destination_name = get_sub_string($urlPath, $token);
       $destination_name = string_between_two_string($urlPath, "rentals-", "-in");
      
        if($header_destination_name) {
            $destinationDetail = fetchDestinationByName($conn, ucwords( implode(" ", explode("-", $header_destination_name)) ));
        }
        if($destination_name){
            $destinationDetail = fetchDestinationByName($conn, ucwords( implode(" ", explode("-", $destination_name)) ));
        }
      
       $region_header_name = getLastSubstringAfterSecondIn($urlPath);
       
        $location_header_name = get_sub_string($urlPath, "in-");
        if($region_header_name) {
            $region_header_name = ucwords( implode(" ", explode("-", $region_header_name)) );
            $header_region_array = fetchRegionByName($conn, $region_header_name);
            if(isset($header_region_array) && count($header_region_array) > 1) {
                $meta_data = fetchMetaDataByRegion($conn, $header_region_array['REGION_ID']);
            }
            if(!empty($meta_data)) {
            ?>
                <title><?php echo isset($meta_data["TITLE"]) ? $meta_data["TITLE"] : "Villa Getaways"; ?></title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="description" content="<?php echo $meta_data["DESCRIPTION"]; ?>" />
                <meta name="block" content="false" />
                <meta name="copyright" content="<?php echo $meta_data["COPYRIGHT"]; ?>" />
                <meta name="email" content="<?php echo $meta_data["EMAIL"]; ?>" />
                <meta name="author" content="<?php echo $meta_data["AUTHOR"]; ?>" />
                <meta name="language" content="<?php echo $meta_data["LANGUAGE"]; ?>" />
                <meta name="mssmarttagspreventparsing" content="<?php echo $meta_data["MSSMARTTAGSPREVENTPARSING"]; ?>" />
            <?php 
            }
        }elseif($location_header_name) {
            $location_header_name = ucwords( implode(" ", explode("-", $location_header_name)) );
            $header_location_array = fetchLocationByName($conn, $location_header_name);
            if(isset($header_location_array) && count($header_location_array) > 1) {
                $meta_data = fetchMetaDataByLocationName($conn, $header_location_array['LOCATION_ID']);
            }
            if(!empty($meta_data)) {
            ?>
                <title><?php echo isset($meta_data["TITLE"]) ? $meta_data["TITLE"] : "$location_header_name Villa Rentals in $destination_name - Luxury Vacation Villas | Villa Getaways"; ?></title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="description" content="<?php echo $meta_data["DESCRIPTION"]; ?>" />
                <meta name="block" content="false" />
                <meta name="copyright" content="<?php echo $meta_data["COPYRIGHT"]; ?>" />
                <meta name="email" content="<?php echo $meta_data["EMAIL"]; ?>" />
                <meta name="author" content="<?php echo $meta_data["AUTHOR"]; ?>" />
                <meta name="language" content="<?php echo $meta_data["LANGUAGE"]; ?>" />
                <meta name="mssmarttagspreventparsing" content="<?php echo $meta_data["MSSMARTTAGSPREVENTPARSING"]; ?>" />
            <?php 
            }
        }
        else{
            if((!empty($destinationDetail)) ) {
                $meta_data = fetchMetaDataForDestination($conn, $destinationDetail["DESTINATION_ID"]);
                $name = $destination_name ? ucwords($destination_name) : ucwords($header_destination_name);
                if(!empty($meta_data)) {
                ?>
                    <title><?php echo isset($meta_data["TITLE"]) ? $meta_data["TITLE"] : " $name Villas | Holiday Homes, Vacation Rentals & Luxury Villas | Villa Getaways"; ?></title>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <meta name="description" content="<?php echo $meta_data["DESCRIPTION"]; ?>" />
                    <meta name="block" content="false" />
                    <meta name="copyright" content="<?php echo $meta_data["COPYRIGHT"]; ?>" />
                    <meta name="email" content="<?php echo $meta_data["EMAIL"]; ?>" />
                    <meta name="author" content="<?php echo $meta_data["AUTHOR"]; ?>" />
                    <meta name="language" content="<?php echo $meta_data["LANGUAGE"]; ?>" />
                    <meta name="mssmarttagspreventparsing" content="<?php echo $meta_data["MSSMARTTAGSPREVENTPARSING"]; ?>" />
                <?php 
                }
            }
        }
    } elseif(strpos($urlPath, $wordVillas2) !== false) {
        ?>
        <meta name="description" content="A stunning beachfront Luxury Villa can be yours when you contact Villa Getaways to book your exclusive Villa holiday. Beach access, ocean views, infinity pools and private spas are just a few of the temptations on offer. Contact the experienced consultants at Villa Getaways for personalised service and find your perfect luxury Villa." />
        <meta name="block" content="false" />
        <meta name="copyright" content="Villa Getaways Ltd" />
        <meta name="email" content="webmaster@villagetaways.net" />
        <meta name="author" content="VillaGetaways.com" />
        <meta name="language" content="en" />
        <meta name="mssmarttagspreventparsing" content="true" />
        <title>Villa Holidays &amp; Luxury Vacation Home Rentals - Villa Getaways</title>
        <?php
    } elseif(strpos($urlPath, $wordVillas3) !== false) { ?>
        <meta name="description" content="Choose from 2000+ Best Luxury Villas &amp; Vacation homes in top destinations worldwide. Too good to be missed, Visit Villa Getaways now &amp; see yourself." />
        <meta name="block" content="false" />
        <meta name="copyright" content="Villa Getaways Ltd" />
        <meta name="email" content="webmaster@villagetaways.net" />
        <meta name="author" content="VillaGetaways.com" />
        <meta name="language" content="en" />
        <meta name="mssmarttagspreventparsing" content="true" />
        <title>Villa Holidays &amp; Luxury Vacation Home Rentals - Villa Getaways</title>
        <?php
    } elseif(strpos($urlPath, $wordVillas4) !== false) { ?>
        <meta name="description" content="Planning to rent a luxury venue for your Dream Destination Wedding? Contact Villa Getaways to book a private luxury wedding villa with tropical manicured grounds, spectacular ocean views &amp; more. Let us spoil you &amp; your guests on your Big Day!" />
        <meta name="block" content="false" />
        <meta name="copyright" content="Villa Getaways Ltd" />
        <meta name="email" content="webmaster@villagetaways.net" />
        <meta name="author" content="VillaGetaways.com" />
        <meta name="language" content="en" />
        <meta name="mssmarttagspreventparsing" content="true" />
        <title>Villa Holidays &amp; Luxury Vacation Home Rentals - Villa Getaways</title>
        <?php
    } elseif(strpos($urlPath, $wordVillas5) !== false) { ?>
        <meta name="description" content="Choose from 2000+ Best Luxury Villas &amp; Vacation homes in top destinations worldwide. Too good to be missed, Visit Villa Getaways now &amp; see yourself." />
        <meta name="block" content="false" />
        <meta name="copyright" content="Villa Getaways Ltd" />
        <meta name="email" content="webmaster@villagetaways.net" />
        <meta name="author" content="VillaGetaways.com" />
        <meta name="language" content="en" />
        <meta name="mssmarttagspreventparsing" content="true" />
        <title>Villa Holidays &amp; Luxury Vacation Home Rentals - Villa Getaways</title>
        <?php
    } elseif(strpos($urlPath, $wordVillas6) !== false) { ?>
         <meta name="description" content="Look no further than Villa Getaways for corporate getaways and luxury accommodation in Top destinations in the World. Visit &amp; Speak to us today!" />
        <meta name="block" content="false" />
        <meta name="copyright" content="Villa Getaways Ltd" />
        <meta name="email" content="webmaster@villagetaways.net" />
        <meta name="author" content="VillaGetaways.com" />
        <meta name="language" content="en" />
        <meta name="mssmarttagspreventparsing" content="true" />
        <title>Villa Holidays &amp; Luxury Vacation Home Rentals - Villa Getaways</title>
        <?php
    } elseif(strpos($urlPath, $wordVillas7) !== false) { ?>
        <meta name="description" content="Watch these stunning videos of luxury Villas from all around the world to give you a view of that Villa Getaways has to offer. Watch videos now!" />
        <meta name="block" content="false" />
        <meta name="copyright" content="Villa Getaways Ltd" />
        <meta name="email" content="webmaster@villagetaways.net" />
        <meta name="author" content="VillaGetaways.com" />
        <meta name="language" content="en" />
        <meta name="mssmarttagspreventparsing" content="true" />
        <title>Luxury Villa Videos from Bali, Phuket &amp; more - Villa Getaways</title>
        <?php
    } elseif(strpos($urlPath, $wordVillas8) !== false) { ?>
        <meta name="description" content="Choose from 2000+ Best Luxury Villas &amp; Vacation homes in top destinations worldwide. Too good to be missed, Visit Villa Getaways now &amp; see yourself." />
        <meta name="block" content="false" />
        <meta name="copyright" content="Villa Getaways Ltd" />
        <meta name="email" content="webmaster@villagetaways.net" />
        <meta name="author" content="VillaGetaways.com" />
        <meta name="language" content="en" />
        <meta name="mssmarttagspreventparsing" content="true" />
        <title>Villa Holidays &amp; Luxury Vacation Home Rentals - Villa Getaways</title>
        <?php
    }
    
    
  
    
    ?>
    
    <?php 
    if( $vg_number ) {
        
        $villa_details = fetchVillaDetails($conn, $vg_number, 1);
        if(!empty($villa_details)) {
            $villa_id = $villa_details['VILLA_ID'];
            
            $meta_data = fetchMetaDataForVilla($conn, $villa_id);
            $actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            //$protocol = ((!emptyempty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://"; 
            if(!empty($meta_data)) {
                
                if(isset($meta_data["TITLE"]) && strlen($meta_data["TITLE"]) > 1) {
                    $title =  $meta_data["TITLE"];
                }elseif(isset($meta_data["PAGE_TITLE"]) && strlen($meta_data["PAGE_TITLE"]) > 1) {
                    $title =  $meta_data["PAGE_TITLE"];
                }else {
                    $title = "Villa Getaways";
                }
        ?>
                <title><?php echo $title; ?></title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="description" content="<?php echo $meta_data["DESCRIPTION"]; ?>" />
                <meta name="block" content="false" />
                <meta name="copyright" content="<?php echo $meta_data["COPYRIGHT"]; ?>" />
                <meta name="email" content="<?php echo $meta_data["EMAIL"]; ?>" />
                <meta name="author" content="<?php echo $meta_data["AUTHOR"]; ?>" />
                <meta name="language" content="<?php echo $meta_data["LANGUAGE"]; ?>" />
                <meta name="mssmarttagspreventparsing" content="<?php echo $meta_data["MSSMARTTAGSPREVENTPARSING"]; ?>" />
                <meta property="og:title" content="<?php echo isset($meta_data["DESCRIPTION"]) ? $meta_data["DESCRIPTION"] : "Villa Getaways"; ?>" />
                <meta property="og:url" content="<?php echo $actual_link; ?>" />
                <meta property="og:image" content="<?php echo home_url("/wp-content/uploads" . $villa_details['RANDOM_VILLA_IMAGE']); ?>" />

        <?php 
            }
        }
    }
    
    
    if(isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] 
        && isset($_REQUEST['location_id']) && $_REQUEST['location_id'] && count(_REQUEST['location_id'])>0 
        && isset($_REQUEST['region_id']) && $_REQUEST['region_id'] && count($_REQUEST['region_id'])>0){
        
        $location_first = $_REQUEST['location_id'][0];
        $region_first = $_REQUEST['region_id'][0];

        $meta_data = fetchMetaDataForLocation($conn, $_REQUEST['destination_id'], $location_first, $region_first);
        
        if(!empty($meta_data)) { ?>
            <title><?php echo isset($meta_data["TITLE"]) ? $meta_data["TITLE"] : "Villa Getaways"; ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="description" content="<?php echo $meta_data["DESCRIPTION"]; ?>" />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
    <?php
        }
    }elseif(isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] 
            && isset($_REQUEST['location_id']) && $_REQUEST['location_id'] && count(_REQUEST['location_id'])>0){
        
        $location_first = $_REQUEST['location_id'][0];
        $meta_data = fetchMetaDataForLocation($conn, $_REQUEST['destination_id'], $location_first);
        
        if(!empty($meta_data)) { ?>
            <title><?php echo isset($meta_data["TITLE"]) ? $meta_data["TITLE"] : "Villa Getaways"; ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="description" content="<?php echo $meta_data["DESCRIPTION"]; ?>" />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
    <?php
        }
    }elseif(isset($_REQUEST['destination_id']) && $_REQUEST['destination_id']) {
        
        $meta_data = fetchMetaDataForDestination($conn, $_REQUEST['destination_id']);
        if(!empty($meta_data)) { ?>
            <title><?php echo isset($meta_data["TITLE"]) ? $meta_data["TITLE"] : "Villa Getaways"; ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="description" content="<?php echo $meta_data["DESCRIPTION"]; ?>" />
            <meta name="block" content="false" />
            <meta name="copyright" content="<?php echo $meta_data["COPYRIGHT"]; ?>" />
            <meta name="email" content="<?php echo $meta_data["EMAIL"]; ?>" />
            <meta name="author" content="<?php echo $meta_data["AUTHOR"]; ?>" />
            <meta name="language" content="<?php echo $meta_data["LANGUAGE"]; ?>" />
            <meta name="mssmarttagspreventparsing" content="<?php echo $meta_data["MSSMARTTAGSPREVENTPARSING"]; ?>" />
    <?php
        }
    }
   
    ?>
    <?php
        if(is_page('about-villa-getaways')) {
            ?>
            <meta name="description" content="Read About Us &amp; Our History - Villa Getaways" />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <title>Read About Us &amp; Our History - Villa Getaways</title>
        <?php    
        }
    ?>
    <?php
        if(is_archive('testimonials')) {
            ?>
            <meta name="description" content="Read what our existing customers have to say about us and book with confidence. Here are some of the emails that we have received from our clients." />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <title>Testimonials &amp; Reviews - Villa Getaways</title>
        <?php    
        }
    ?>
    <?php
        if(is_page('faq')) {
            ?>
            <meta name="description" content="Do you ahve a question regarding Villa rentals? We have covered some of the most frequently asked questions here to answer your questions. Read here!" />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <title>Villa Rental Freqently Asked Questions (FAQs) - Villa Getaways</title>
        <?php    
        }
    ?>
    <?php
        if(is_page('media-press')) {
            ?>
            <meta name="description" content="Here are some samples of media and press that Villa Getaways have been featured in. Please click on a title or thumbnail to vew the article or image." />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <title>Media &amp; Press Releases &amp; Mentions - Villa Getaways</title>
        <?php    
        }
    ?>
    <?php
        if(is_page('meet-the-team')) {
            ?>
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <meta name="description" content="Choose from 2000+ Best Luxury Villas &amp; Vacation homes in top destinations worldwide. Too good to be missed, Visit Villa Getaways now &amp; see yourself." />
            <title>Villa Holidays &amp; Luxury Vacation Home Rentals - Villa Getaways</title>
        <?php    
        }
    ?>
    <?php
        if(is_page('contact') || is_page('authenticity')) {
            ?>
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <meta name="description" content="Choose from 2000+ Best Luxury Villas &amp; Vacation homes in top destinations worldwide. Too good to be missed, Visit Villa Getaways now &amp; see yourself." />
            <title>Villa Holidays &amp; Luxury Vacation Home Rentals - Villa Getaways</title>
        <?php    
        }
    ?>
    <?php
        if(is_page('rental-terms')) {
            ?>
            <meta name="description" content="Find out more about Villa Getaways rental terms. View information on payments, cancellation, refunds and other legal terms here." />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <title>  Villa &amp; Luxury Holiday Homes Rental Terms - Villa Getaways</title>
        <?php    
        }
    ?>
    <?php
        if(is_page('website-terms')) {
            ?>
            <meta name="description" content="Find details on Villa Getaways website usage terms and conditions here. If you disagree with any part of these terms and conditions, please do not use our website." />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <title> Website Terms &amp; Conditions - Villa Getaways</title>
        <?php    
        }
    ?>
    <?php
        if(is_page('privacy-policy')) {
            ?>
            <meta name="description" content="Villa Getaways takes your privacy seriously. Read more about our privacy policy and details on how we use the data collected on the website. Visit here." />
            <meta name="block" content="false" />
            <meta name="copyright" content="Villa Getaways Ltd" />
            <meta name="email" content="webmaster@villagetaways.net" />
            <meta name="author" content="VillaGetaways.com" />
            <meta name="language" content="en" />
            <meta name="mssmarttagspreventparsing" content="true" />
            <title>Privacy Policy - Villa Getaways</title>
        <?php    
        }
    ?>
    <?php wp_head(); ?>
    
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://kit.fontawesome.com/a6e47f3df1.js" crossorigin="anonymous"></script>
    
    
    </head>

<!-- Using class on body tag for specific page -->
<!-- id="contact-page" class="dest-page" -->

<?php 
$body_id = "";
$body_class = "";
if( get_the_ID() == 2 ) {
    $body_id = "";
    $body_class = "";
} elseif( get_the_ID() == 2 ) { // Home
    $body_id = "";
    $body_class = "";
} elseif( get_the_ID() == 6 ) { // About Us
    $body_id = "contact-page";
    $body_class = "";
} elseif( get_the_ID() == 8 ) { // Contact Us
    $body_id = "contact-page";
    $body_class = "";
} elseif( get_the_ID() == 35 ) { // Destination
    $body_id = "";
    $body_class = "dest-page";
} elseif( get_the_ID() == 31 ) { // Experiences
    $body_id = "";
    $body_class = "";
} elseif( get_the_ID() == 33 ) { // Experiences Long
    $body_id = "experiences-page";
    $body_class = "";
} else {
    $body_id = "";
    $body_class = "";
}
?>

<body id="<?php echo $body_id; ?>" class="<?php echo $body_class; ?>">
    <!-- Start Header -->
    <header>


        <nav class="navbar navbar-expand-xl bg-white">
            <a class="navbar-brand" href="<?php echo home_url(); ?>">
                <img width="230" src="<?php echo get_field("logo", "options"); ?>" alt="VILLA GETAWAYS" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon">
                  <i></i>
                  <i></i>
                  <i></i>
              </span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExample05">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown05" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" style="padding-top: 2rem;"> 
                            DESTINATIONS
                        </a>
                        <div class="dropdown-menu destination-menu" aria-labelledby="dropdown05">
                            <h3 class="menu-heading">Choose your next getaway</h3>
                            <div class="row">
                                <?php echo get_template_part('template-parts/destination', 'menu'); ?>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="dropdown05" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 
                        href="#" style="padding-top: 2rem;">
                            EXPERIENCES
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdown05">
                            <h3>Experiences</h3>
                            <div class="row no-gutters">
                                <?php
                                if( have_rows('experience_menu_items', 'options') ):
                                    $index = 1;
                                    while( have_rows('experience_menu_items', 'options') ) : the_row();
                                        if(get_sub_field('title') == "Holiday Season Villas") {
                                            $url = home_url(get_sub_field('link'));
                                        }else {
                                            $url = home_url(get_sub_field('link')); 
                                        }
                                        ?>
                                        <div class="col-md-2">
                                            <a href="<?php echo $url; ?>">
                                                <img src="<?php echo get_sub_field('image'); ?>" alt="<?php echo get_sub_field('title'); ?>">
                                                <h5><?php echo get_sub_field('title'); ?></h5>
                                                <p><?php echo get_sub_field('sub_title'); ?></p>
                                            </a>
                                        </div>
                                        <?php
                                        $index++;
                                    endwhile;
                                endif;
                                ?>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item <?php echo (get_the_ID() == 6) ? 'active' : ''; ?>">
                        <a class="nav-link" href="<?php echo get_field("about_us", "options"); ?>">About Us </a>
                    </li>
                    <li class="nav-item <?php echo (get_the_ID() == 8) ? 'active' : ''; ?>">
                        <a class="nav-link" href="<?php echo get_field("contact_us", "options"); ?>">Contact Us</a>
                    </li>
                     <li class="nav-item <?php echo (get_the_ID() == 432) ? 'active' : ''; ?>">
                        <a class="nav-link" href="/blog/">Blog</a>
                    </li>
                </ul>
                
                <form class="form-inline form-actions my-2 my-md-0 mx-3" action="<?php echo home_url("villas/display/Villa"); ?>"
                class="search_villa_form" name="search_villa_form" id="search_villa_form" method="POST">
                    <a href="javascript:void(0);" class="btn btn-searh">Search</a>

                    <div class="input-group mb-3">
                        <input class="form-control" type="text" value="" id="search_villa_id" name="search_villa_id" placeholder="Enter a Villa #" />
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary search_villa_button" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                
                    <a class="rounded-circle" href="<?php echo home_url("favourites"); ?>">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon-fav.png" alt="">
                        <span class="fav-counter" style="display: inline;">1</span>
                    </a>
                    <a class="rounded-circle" href="tel: <?php echo get_field("phone", "options"); ?>">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon-phone.png" alt="">
                    </a>
                </form>
                
                <div class="top-destination-form">
                    <div class="search-box animated fadeInDown">
                        <div class="search-box-form header-form">
                            <?php echo get_template_part('template-parts/header', 'form'); ?>
                        </div>
                    </div>
                </div>
                
            </div>
        </nav>
    </header>
    <!-- ./ End Header -->