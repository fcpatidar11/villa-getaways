<?php 
/* Template Name: Single Villa */
get_header(); ?>

<style>
  header {
    z-index: 3;
    position: relative;
}
</style>
<?php 
$url = $_SERVER['REQUEST_URI'];
preg_match('/\d+/', $url, $matches);

// Extract the matched integer value
$vg_number = (int) $matches[0];




$date_start = (isset($_COOKIE["__date_start"]) && $_COOKIE["__date_start"]) ? $_COOKIE["__date_start"] : "";
$date_end = (isset($_COOKIE["__date_end"]) && $_COOKIE["__date_end"]) ? $_COOKIE["__date_end"] : "";
$bedrooms = (isset($_COOKIE["__bedrooms"]) && $_COOKIE["__bedrooms"]) ? $_COOKIE["__bedrooms"] : 1;

$guest = 1;
$date_from = "";
$date_to = "";
$diff = "";
$villa_price = "";
$agent_id="";
$villa_id="";
$villa_floor_plans = [];
$number_of_bedrooms = [];
$external_book_url = "";




if(!empty($vg_number)) {
    $conn = oracleDbConnection();
    $countryIds = getCountryId($conn);
    $villa_details = fetchVillaDetails($conn, $vg_number, $bedrooms);
    
   
    global $footer_location_id;
    global $footer_destination_id;
    $footer_location_id = $villa_details['LOCATION_ID'];
    $footer_destination_id = $villa_details['DESTINATION_ID'];
    
    $agent_id = $villa_details['AGENT_ID'];
    $external_book_url = fetchExternalBookUrl($conn);
    if(isset($villa_details) && count($villa_details) > 0) {
        $villa_id = $villa_details['VILLA_ID'];
         
    $conn = oracleDbConnection();
    $bookedDates =[];               
    $bookedDates = fetchVillaBookedDates($conn,$villa_id);
 
    $bookedDatesformatted =[];

function formatDate($dateStr) {
    // Create a DateTime object from the original format
    $date = DateTime::createFromFormat('d-M-y', $dateStr);
    
    // Return the date in the desired format
    return $date->format('Y-m-d');
}
//echo "<pre>";
//print_r($bookedDates);
foreach($bookedDates as $key => $dates){
   
    $bookedDatesformatted[$key]['ARRIVAL'] = formatDate($dates['ARRIVAL']);
    $bookedDatesformatted[$key]['CHECKOUT'] = $dates['CHECKOUT'];
    $bookedDatesformatted[$key]['LATE_CHECKOUT'] = $dates['LATE_CHECKOUT'];
    $bookedDatesformatted[$key]['ARRIVING'] = $dates['ARRIVING'];
    
}



foreach ($bookedDatesformatted as $entry) {
    // Create the event structure with the placeholder [day]
    $event = [
        'date' => $entry['ARRIVAL'],
        'classname' => 'booked',
        'markup' => '[day]',  // Ensure no newlines
    ];

    // If it's the last day before checkout, you might want to change the class or markup
    if ($entry['CHECKOUT'] === 'Y' && $entry['LATE_CHECKOUT']==0) {
        $event['classname'] = 'booked-half';
        $event['markup'] = '[day]';  // Ensure no newlines
    }
    
    if ($entry['ARRIVING'] === 'Y') {
        $event['classname'] = 'booked-half-end';
        $event['markup'] = '[day]';  // Ensure no newlines
    }

    $events[] = $event;
}
        
        
        $number_of_bedrooms = fetchNumberOfBedrooms($conn, $villa_id);
        $villa_reviews = fetchVillaReviews($conn, $villa_id);
        if($bedrooms == "") {
            if(!empty($number_of_bedrooms)) {
                if(count($number_of_bedrooms) >= 1) {
                    $bedrooms = $number_of_bedrooms[0];
                }
            }    
        }
        
        $country_name = strtolower(str_replace(" ", "-", $villa_details["DESTINATION_NAME"]));
        $location_name = strtolower(str_replace(" ", "-", $villa_details['LOCATION_NAME']));
        $event_dates = [];
        $unavailable_dates = fetchUnavailableDates($conn, $villa_details["VILLA_ID"]);
        if( $unavailable_dates ) {
            foreach( $unavailable_dates AS $date ) {
                $temp_event_dates = [
                    "start" => date('Y-m-d', strtotime($date["ARRIVE"])),
                    "end" => date('Y-m-d', strtotime($date["DEPART"])),
                    "rendering" => "background",
                    "backgroundColor" => "#ef4d4d"
                ];
                $event_dates[] = $temp_event_dates;
            }
        }
        
        $date_from = $date_start?:date('d-m-Y', strtotime("+1 day"));
        $date_to =  $date_end?:date('d-m-Y', strtotime("+2 day"));
        
        $date1 = DateTime::createFromFormat('d-m-Y', $date_from);
        $date2 = DateTime::createFromFormat('d-m-Y', $date_to);
        $diff = $date1->diff($date2)->format('%a');
        
        
        $start = $date_start ?: "";
        $end = $date_end?: "";
        
        if(isset($_GET['dest_checkIn']) && $_GET['dest_checkIn'] && isset($_GET['dest_checkOut']) && $_GET['dest_checkOut']) {
            $date_from = isset($_GET['dest_checkIn']) ? filter_var($_GET['dest_checkIn'], FILTER_SANITIZE_SPECIAL_CHARS) : $date_from;
            $date_to = isset($_GET['dest_checkOut']) ? filter_var($_GET['dest_checkOut'], FILTER_SANITIZE_SPECIAL_CHARS) : $date_to;
            $bedrooms = isset($_GET['inquiry_room']) ? filter_var($_GET['inquiry_room'], FILTER_SANITIZE_SPECIAL_CHARS) : $bedrooms;
            //$guest = isset($_GET['inquiry_guest']) ? filter_var($_GET['inquiry_guest'], FILTER_SANITIZE_SPECIAL_CHARS) : $guest;
            
            $date1 = DateTime::createFromFormat('d-m-Y', $date_from);
            $date2 = DateTime::createFromFormat('d-m-Y', $date_to);
            $diff = $date1->diff($date2)->format('%a');
        }
        
        $rates = fetchRatesOfSingleVilla($conn, $villa_id, date("d/m/Y", strtotime($date_from)), date("d/m/Y", strtotime($date_to)), $bedrooms);

        if($start == "" && $end == "") {
            $villa_price = fetchVillaMinMaxPrice($conn, $villa_id);
        }
        
        $villa_floor_plans = fetchVillaFloorPlan($conn, $villa_id);
        $external_book_url .= "" . $villa_id . "," . date("d-m-Y", strtotime($date_from)) . "," . date("d-m-Y", strtotime($date_to));
    }
}


?>
<?php 
if(empty($villa_details)) { ?>
<div>
    <h2>No Villa Found</h2>
</div>
<?php 
}else { 
?>
<div class="dest-info-bar">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo home_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo home_url('destination/villa-rentals-' . $country_name); ?>"><?php echo $villa_details["DESTINATION_NAME"]; ?></a></li>
                    <li class="breadcrumb-item"><a href="<?php echo home_url('destination/villa-rentals-' . $country_name . '-in-' . $location_name); ?>"><?php echo $villa_details["LOCATION_NAME"]; ?></a></li>
                    <?php if(isset($villa_details["REGION_NAME"]) && $villa_details["REGION_NAME"]) { ?><li class="breadcrumb-item active" aria-current="page"><a href="<?php echo home_url('destination/villa-rentals-' . $country_name . '-in-' . $location_name. '-in-' .$villa_details["REGION_NAME"]); ?>"><?php echo $villa_details["REGION_NAME"]; ?></a></li><?php } ?>
                    <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo $url; ?>">Villa <?php echo $villa_details["VG_NUMBER"]; ?></a></li>
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

<!-- ./ End Header -->
<!-- Start Main -->
<main class="position-relative">
    <div id="dest-slider">
        <?php echo get_template_part('template-parts/villa/villa', 'slider', ["villa_details" => $villa_details]); ?>
    </div>
</main>
<!-- ./ End Main -->

<div class="dest-detail-bar">
    <div class="container p-0">
        <div class="row">
            <div class="col-lg-<?php echo !empty($villa_price) ?'3':'4';?>">
                <h1 class="villa-name"><?php if($villa_details["VILLA_TITLE"]!=""){ echo $villa_details["VILLA_TITLE"];}else{?><?php echo $villa_details["LOCATION_NAME"]; ?> Villa <?php echo $villa_details["VG_NUMBER"]; }  ?></h1>
            </div>
            
            <div class="col-lg-<?php echo !empty($villa_price) ?'3':'4';?>">
                <div class="dest-icon-wrap">
                    <ul>
                        
                        <li>
                            <img src="<?php echo home_url("/wp-content/uploads/2023/04/bedrooms.svg"); ?>">
                            <span> <?php echo $villa_details["BEDS"]; ?> </span>
                        </li>
                        <li>
                            <img src="<?php echo home_url("/wp-content/uploads/2023/04/bathrooms.svg"); ?>">
                            <span> <?php echo $villa_details["BATHS"]; ?> </span>
                        </li>
                        <li>
                            <img src="<?php echo home_url("/wp-content/uploads/2023/04/people.svg"); ?>">
                            <span>  <?php echo $villa_details["SLEEPS"]; ?> </span>
                        </li>
                    </ul>
                </div>
            </div>
            <?php if(!empty($villa_price)) { ?>
            <div class="col-lg-<?php echo !empty($villa_price) ?'3':'4';?>">
                <div class="dest-icon-wrap">
                    <p class="rate">Price $ <?php echo number_format($villa_price["MIN"], 2, '.', ','); ?> (MIN) - $ <?php echo number_format($villa_price["MAX"], 2,".", ","); ?> (MAX) nightly</p>
                </div>
            </div>
            <?php } ?>
            <div class="col-lg-<?php echo !empty($villa_price) ?'3':'4';?>">
                <div class="dest-owner-info">
                    <ul>
                        <?php
                        if($villa_details["IS_PRIORITY"]) {
                        ?>
                        <li>
                            <a hre="#" id="star" data-toggle="tooltip" data-placement="top" title="Highly Recommended">
                                
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.0277 7.09146L19.0277 7.09144C19.0556 7.06343 19.0613 7.04349 19.0636 7.03066C19.0667 7.01275 19.0653 6.98695 19.0536 6.95872C19.042 6.93047 19.025 6.91188 19.0112 6.90209C19.0016 6.89534 18.9848 6.88568 18.9464 6.88568H13.8515C13.0658 6.88568 12.3593 6.41183 12.0586 5.68766L19.0277 7.09146ZM19.0277 7.09146L14.907 11.2255C14.4087 11.7254 14.2209 12.4556 14.4137 13.1333L14.4138 13.1335L16.0479 18.8715C16.0479 18.8715 16.0479 18.8715 16.0479 18.8715C16.0618 18.9202 16.0555 18.9468 16.0494 18.963C16.0413 18.9848 16.0233 19.0113 15.9933 19.0333C15.9632 19.0553 15.9334 19.0638 15.912 19.0649C15.8967 19.0656 15.8712 19.0641 15.8303 19.0368L11.0699 15.8529L11.0699 15.8528C10.4171 15.4163 9.56598 15.4163 8.91312 15.853L4.16077 19.0315C4.16076 19.0315 4.16074 19.0315 4.16073 19.0315C4.11924 19.0593 4.09306 19.061 4.07707 19.0602C4.05488 19.0591 4.02438 19.0503 3.99378 19.0279C3.96319 19.0055 3.94476 18.9784 3.93638 18.956C3.93012 18.9392 3.92373 18.9118 3.93781 18.8624L3.93782 18.8624L5.56935 13.1335C5.76244 12.4555 5.57425 11.7252 5.07616 11.2255L5.07615 11.2255L0.975821 7.11189C0.975819 7.11189 0.975818 7.11189 0.975816 7.11189C0.946038 7.08201 0.939215 7.05985 0.936608 7.04475C0.933122 7.02456 0.934794 6.99644 0.947317 6.96611C0.959841 6.93577 0.978208 6.91533 0.993925 6.90422C1.00535 6.89614 1.02452 6.88568 1.06557 6.88568H6.13167C6.9174 6.88568 7.62388 6.41176 7.92463 5.68766L7.92463 5.68765L9.86201 1.02282C9.86201 1.02281 9.86202 1.0228 9.86203 1.02278C9.87947 0.980807 9.89893 0.964601 9.91275 0.955844C9.93088 0.944355 9.95816 0.935 9.99158 0.935C10.025 0.935 10.0523 0.944355 10.0704 0.955844C10.0842 0.964601 10.1037 0.980809 10.1211 1.02279C10.1211 1.0228 10.1211 1.02281 10.1211 1.02282L12.0585 5.68752L19.0277 7.09146Z" stroke="#9d9d9d" stroke-width="1.87"/>
                        </svg>
                            </a>
                        </li>
                        <?php } ?>
                        <li><a href="javascript:void(0)" onclick="javascript:openPopup(); return false;"><i class="fa fa-share-alt"></i></a></li>
                        <li class="villa_fav">
                            <a href="#"  id="fav-<?php echo $villa_details["VG_NUMBER"]; ?>" data-id="<?php echo $villa_details["VG_NUMBER"]; ?>"><i class="fa fa-heart"></i></a>
                        </li>
                        <li class="whatup-share">
                            <a href="javascript:void(0);" onclick="shareWhatsApp();">
                                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0,0,256,256" width="25px" height="25px" fill-rule="nonzero">
                                      <g fill="#23349b" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)">
                                          <path d="M25,2c-12.682,0 -23,10.318 -23,23c0,3.96 1.023,7.854 2.963,11.29l-2.926,10.44c-0.096,0.343 -0.003,0.711 0.245,0.966c0.191,0.197 0.451,0.304 0.718,0.304c0.08,0 0.161,-0.01 0.24,-0.029l10.896,-2.699c3.327,1.786 7.074,2.728 10.864,2.728c12.682,0 23,-10.318 23,-23c0,-12.682 -10.318,-23 -23,-23zM36.57,33.116c-0.492,1.362 -2.852,2.605 -3.986,2.772c-1.018,0.149 -2.306,0.213 -3.72,-0.231c-0.857,-0.27 -1.957,-0.628 -3.366,-1.229c-5.923,-2.526 -9.791,-8.415 -10.087,-8.804c-0.295,-0.389 -2.411,-3.161 -2.411,-6.03c0,-2.869 1.525,-4.28 2.067,-4.864c0.542,-0.584 1.181,-0.73 1.575,-0.73c0.394,0 0.787,0.005 1.132,0.021c0.363,0.018 0.85,-0.137 1.329,1.001c0.492,1.168 1.673,4.037 1.819,4.33c0.148,0.292 0.246,0.633 0.05,1.022c-0.196,0.389 -0.294,0.632 -0.59,0.973c-0.296,0.341 -0.62,0.76 -0.886,1.022c-0.296,0.291 -0.603,0.606 -0.259,1.19c0.344,0.584 1.529,2.493 3.285,4.039c2.255,1.986 4.158,2.602 4.748,2.894c0.59,0.292 0.935,0.243 1.279,-0.146c0.344,-0.39 1.476,-1.703 1.869,-2.286c0.393,-0.583 0.787,-0.487 1.329,-0.292c0.542,0.194 3.445,1.604 4.035,1.896c0.59,0.292 0.984,0.438 1.132,0.681c0.148,0.242 0.148,1.41 -0.344,2.771z"></path></g></g></svg>
                              </a>
                        </li>
                        <!--<li><a href="mailto:<?php echo $villa_details['AGENT_EMAIL']; ?>?subject=Inquiring%20<?php echo $villa_details["LOCATION_NAME"]; ?>%20Villa%20<?php echo $villa_details["VG_NUMBER"]; ?>"><i class="glyphicon glyphicon-envelope"></i></a></li>-->
                    </ul>
                    <div id="sharePopup" style="display: none;">
                      
                      <a href="javascript:void(0);" onclick="shareFacebook();">
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0,0,256,256" width="30px" height="30px" fill-rule="nonzero"><g fill="#1d2a7c" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)"><path d="M25,3c-12.15,0 -22,9.85 -22,22c0,11.03 8.125,20.137 18.712,21.728v-15.897h-5.443v-5.783h5.443v-3.848c0,-6.371 3.104,-9.168 8.399,-9.168c2.536,0 3.877,0.188 4.512,0.274v5.048h-3.612c-2.248,0 -3.033,2.131 -3.033,4.533v3.161h6.588l-0.894,5.783h-5.694v15.944c10.738,-1.457 19.022,-10.638 19.022,-21.775c0,-12.15 -9.85,-22 -22,-22z"></path></g></g></svg>
                      </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="dest-detail">
    <div class="container p-0">
        <div class="row">
            <div class="col-xl-6 left-aside mb-5 single-villa">
                <div id="single-villa-des">
                   <?php
                    if (is_object($villa_details['VILLA_DESCRIPTION'])) { // protect against a NULL LOB
                        $villa_description = $villa_details['VILLA_DESCRIPTION']->load();
                        $villa_details['VILLA_DESCRIPTION']->free();
                        echo htmlspecialchars_decode($villa_description);                    
    				} ?>
                </div>
            <button class="read-more-single">Read More</button>
            </div>
                
            <div class="col-xl-6 right-aside">
                <div class="calendar-img-box">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="R1289354195165339" style="background: #1d2a7c !important;height:100%;" class="p-3"> 
                                <h5 class="availability">Availability</h5>
                                <div id="cal" class="bg-white"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkInOut">
                                <h5>Bookings</h5>
                                <div class="rates">
                                    <?php 
                                    if($villa_details['PRICE'] != -1) {
                                        if($villa_price == "") {
                                            if($rates) {
                                                $total = number_format((float)(( $rates + ($rates*$villa_details['TAX_PERCENTAGE'])/100 )*$diff), 2, '.', '');
                                    ?>
                                                <div class="rates-block">
                                                    <span>Rates: </span> <span><?php echo "US$ ".$rates. " per night"; ?></span>
                                                    <?php 
                                                    if($villa_details['TAX_PERCENTAGE'] > 0){?>
                                                    <span>Taxes : <?php echo $villa_details['TAX_PERCENTAGE']; ?>%</span> <span>US$ <?php echo number_format((float)$rates*($villa_details['TAX_PERCENTAGE']/100), 2, '.', '');  ?></span>
                                                    <?php } ?>
                                                    <span>Total <?php echo $diff > 1 ? "$diff night(s)" : "1 night";?>: </span> <span><?php echo "US$ ".$total; ?></span>
                                                    <!--<span>US$ <?php echo $rates."+".$villa_details['TAX_PERCENTAGE']."% taxes x 1 nights"; ?></span>-->
                                                </div>
                                        <?php 
                                            }
                                        } 
                                    } ?>
                                </div>
                                <form method="GET" id="inquiry">
                                    <div class="date-range input-group ">
                                        <input type="hidden" class="form-control" name="villa_id" id="villa_id" value="<?php echo $villa_id; ?>"/>
                                        <input type="hidden" class="form-control" name="villa_percentage" id="villa_percentage" value="<?php echo $villa_details['TAX_PERCENTAGE']; ?>"/>
                                        
                                        <div>
                                            <label>From</label>
                                            <div class="checkIn">
                                                <input class="form-control" type="text" name="dest_checkIn" id="dest_checkIn" value="<?php echo $start; ?>" placeholder="Check In">
                                            </div>
                                        </div>
                                        <div>
                                            <label>To</label>
                                            <div class="checkOut">
                                                <input class="form-control" type="text" name="dest_checkOut" id="dest_checkOut" value="<?php echo $end; ?>" placeholder="Check Out">
                                            </div>
                                        </div>
                                        <div class="enquire-form-field enquire-form-select-wrapper">
                                            <label>Number of Bedrooms</label>
                                            <select class="form-control enquire-form-select-field" name="inquiry_room" id="inquiry_room">
                                                <!--<option>Select</option>-->
                                                <?php 
                                                if(isset($number_of_bedrooms) && $number_of_bedrooms) {
                                                    foreach($number_of_bedrooms AS $key => $number_of_bedroom) {
                                                        ?>
                                                        
                                                        <?php //echo ($key == 0 && !isset($_GET['inquiry_room'])) ? 'selected="selected"' : ""; ?> 
                                                        <?php //echo (isset($_GET['inquiry_room']) && $_GET['inquiry_room'] == $number_of_bedroom) ? 'selected="selected"' : ""; ?>
                                                        <option <?php echo $bedrooms == $number_of_bedroom ? "selected": ""; ?>
                                                        value="<?php echo $number_of_bedroom; ?>"><?php echo $number_of_bedroom; ?></option>        
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <!--<input type="text" class="form-control" name="inquiry_room" id="inquiry_room" value="<?php //echo $bedrooms?: 1;?>"/>-->
                                            
                                        </div>
                                        <!--<div class="enquire-form-field">-->
                                        <!--    <label>Guests</label>-->
                                        <!--    <input type="text" class="form-control" name="inquiry_guest" id="inquiry_guest" value="<?php echo $guest?: 1;?>"/>-->
                                        <!--</div>-->

                                        <!--<input type="hidden" class="form-control" name="submit" value="submit"/>-->
                                        
                                        <!--data-toggle="modal" data-target="#after_something_dlg" -->
                                    </div>
                                </form>
                                 <div id="from-calendar-popup" class="calendar-popup">
                                    <div id="from-calendar"></div>
                                </div>
                                
                                <div id="to-calendar-popup" class="calendar-popup">
                                    <div id="to-calendar"></div>
                                </div>
                                </form>
                                <a href="#" data-agentId="<?php echo $agent_id; ?>" data-bedrooms="<?php echo $villa_details["SLEEPS"]; ?>" data-bathrooms="<?php echo $villa_details["BATHS"]; ?>" data-villa-number="<?php echo $villa_details["VG_NUMBER"]; ?>" data-villa-id="<?php echo $villa_details["VILLA_ID"]; ?>" data-villa-image="<?php echo home_url("/wp-content/uploads/" . $villa_details['RANDOM_VILLA_IMAGE']); ?>" onclick="openInquieryForm(this)" class="btn enquire destop-button">INQUIRE</a>
                                <a target="_blank" href="<?php echo home_url().'/inquiry?villa_number='.$villa_details["VG_NUMBER"]; ?>" class="mobile-button btn enquire">INQUIRE</a>
                                    
                                <?php 
                                
                                if($villa_details['BOOKNOW']) { ?>
                                    <a id="book-villa" href="<?php echo home_url().'/villa-booking-'.$villa_details["VG_NUMBER"]; ?>" class="btn bookNow" value="">BOOK NOW</a>
                                <?php } ?>
                            </div>
                             
                        </div>
                    </div>
                </div>
                <div class="dest-bookingPerson">
                    <div class="row">
                        <div class="col-xl-4 col-lg-3 col-sm-4">
                            <div class="dest-avatar">
                                <div class="img-wrap"><img src="<?php echo home_url("/wp-content/uploads/" . $villa_details['AGENT_IMAGE']); ?>"></div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-9 col-sm-8">
                            <div class="dest-bookingPerson-detail">
                                <h6>Hi, I’m <?php echo $villa_details['AGENT_NAME']; ?></h6>
                                
                                <ul class="social-icons">
                                    <!--<li><a href="#"><i class="fab fa-linkedin"></i></a></li>-->
                                    <!--<li><a href="#"><i class="glyphicon glyphicon-earphone"></i></a></li>-->
                                    <!--href="mailto:<?php echo $villa_details['AGENT_EMAIL']; ?>"-->
                                    <li><a id="recommend-open-form-button"><i class="glyphicon glyphicon-envelope"></i></a></li>
                                </ul>
                                
                                <p>
                                    <?php echo $villa_details['VILLA_SUMMARY']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="dest-destinations-sec">
    <div class="dest-top-menu">
        <div class="container">
            <div class="row">
                <div class="title col-lg-2">
                    <h4 class="m-0">Menu</h4>
                </div>
                <div class="col-lg-10">
                    <ul class="nav nav-pills nav-justified" id="dest_menuTab">

                        <li>
                            <a class="active show" href="#dest_destination" data-toggle="tab">
                                <sup>01</sup> Destination
                            </a>
                        </li>
                        <li>
                            <a href="#dest_amenities" data-toggle="tab">
                                <sup>02</sup> Amenities
                            </a>
                        </li>
                        <li>
                            <a href="#dest_rates" data-toggle="tab">
                                <sup>03</sup> Rates
                            </a>
                        </li>
                        <li>
                            <a href="#dest_location" data-toggle="tab">
                                <sup>04</sup> Location
                            </a>
                        </li>
                        
                        <?php if($villa_floor_plans && sizeof($villa_floor_plans)) { ?>
                        <li>
                            <a href="#dest_floot_plan" data-toggle="tab">
                                <sup>05</sup> Floor Plan
                            </a>
                        </li>
                        <?php } ?>
                        
                        <?php if($villa_reviews && sizeof($villa_reviews) > 0) { ?>
                        <li>
                            <a href="#villa_review" data-toggle="tab">
                                <sup>05</sup> Reviews
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    
    
    <div class="container">

        <div class="tab-content">
            <!-- DESTIOATIONS TAB PANE START -->
            <div class="tab-pane active row" id="dest_destination">
                <div class="row">
                    <?php echo get_template_part('template-parts/villa/villa', 'destination', ["villa_details" => $villa_details]); ?>
                </div>
            </div>
            <!-- DESTIOATIONS TAB PANE END -->
			
            <!-- AMINITIES TAB PANE START -->
            <div class="tab-pane fade row" id="dest_amenities">
                <div class="row">
                    <?php echo get_template_part('template-parts/villa/villa', 'amenities', ["villa_details" => $villa_details]); ?>
                </div>
            </div>
            <!-- AMINITES TAB PANE END -->
			
            <!-- RATES TAB PANE START -->
            <div class="tab-pane fade row" id="dest_rates">
                <div class="row">
                    <?php echo get_template_part('template-parts/villa/villa', 'rates', ["villa_details" => $villa_details]); ?>
                </div>
            </div>
            <!-- RATES TAB PANE END -->
			
            <!-- LOCATION TAB PANE START -->
            <div class="tab-pane fade row" id="dest_location">
                <div class="row">
                    <?php echo get_template_part('template-parts/villa/villa', 'location', ["villa_details" => $villa_details]); ?>
                </div>
            </div>
            <!-- LOCATION TAB PANE END -->
            
            <!-- FLOOR PLAN TAB PANE START -->
            <?php if($villa_floor_plans && sizeof($villa_floor_plans)) { ?>
            <div class="tab-pane fade row" id="dest_floot_plan">
                <div class="row">
                    <?php echo get_template_part('template-parts/villa/villa', 'floor', ["villa_floor_plans" => $villa_floor_plans]); ?>
                </div>
            </div>
            <?php } ?>
            <?php if($villa_reviews && sizeof($villa_reviews) > 0) { ?>
            <div class="tab-pane fade row" id="villa_review">
                <div class="row">
                    <?php echo get_template_part('template-parts/villa/villa', 'reviews', ["villa_reviews" => $villa_reviews]); ?>
                </div>
            </div>
            <?php } ?>
            <!-- FLOOR PLAN TAB PANE END -->
        </div>
    </div>
</section>

<section class="dest-similar-sec">
    <div class="container">
        <div class="row no-gutters">
            <div class="col-12 text-center">
                <h2>Similar Luxury Villas</h2>
            </div>
        </div>
        <div class="row no-gutters">
            <?php echo get_template_part('template-parts/similar', 'villas', ["villa_details" => $villa_details]); ?>
        </div>
    </div>

</section>

<script>
  // Replace the URL, title, and description with your actual page URL, title, and description
  var pageURL = encodeURIComponent(window.location.href);
  var pageTitle = encodeURIComponent('<?php echo $villa_details["LOCATION_NAME"]; ?> Villa <?php echo $villa_details["VG_NUMBER"]; ?>');
  var pageDescription = encodeURIComponent('Your page description here');
  var pageImageURL = encodeURIComponent('<?php echo home_url("/wp-content/uploads/" . $villa_details['RANDOM_VILLA_IMAGE']); ?>');
  
  // Function to open the popup
  function openPopup() {
    
    var shareText = decodeURIComponent(pageTitle);
    var shareURL = decodeURIComponent(pageURL);
    var shareImage = decodeURIComponent(pageImageURL);

    var popup = document.getElementById('sharePopup');
    if (popup.style.display === 'block') {
      popup.style.display = 'none';
    } else {
      popup.style.display = 'block';
    }
  }
  
  // Function to close the popup
  function closePopup() {
    var popup = document.getElementById('sharePopup');
    popup.style.display = 'none';
  }
  
  // Function to share on WhatsApp
  function shareWhatsApp() {

    var shareText = decodeURIComponent(pageTitle);
    var shareURL = decodeURIComponent(pageURL);
    var shareImage = decodeURIComponent(pageImageURL);
    
    var whatsappLink = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(shareText + ' ' + shareURL + ' ' + shareImage);
    window.open(whatsappLink, '_blank');
    
    closePopup();
  }
  
  // Function to share on Facebook
  function shareFacebook() {
    var shareText = decodeURIComponent(pageTitle);
    var shareURL = decodeURIComponent(pageURL);
    var shareImage = decodeURIComponent(pageImageURL);
    
    var facebookLink = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareURL) + '&quote=' + encodeURIComponent(shareText) + '&picture=' + encodeURIComponent(shareImage);

    window.open(facebookLink, '_blank');
    
    closePopup();
  }
  
  // Function to update the URL with new dates
    function updateUrl(e) {
        e.preventDefault();
        let url = this.href;
        
        let dest_checkIn = $('#dest_checkIn').val();
        let dest_checkOut = $('#dest_checkOut').val();
        let inquiry_room = $('#inquiry_room').val();
        
      
        
        url = url+'/?arrivalDate='+dest_checkIn+'&departureDate='+dest_checkOut+'&NoOfRooms='+inquiry_room;
        
     
        // const fromDate = document.getElementById('dest_checkIn').value;
        // const toDate = document.getElementById('dest_checkOut').value;
        // if(!fromDate || !toDate) {
        //     return false;
        // }
        // const datePattern = /\b\d{2}-\d{2}-\d{4}\b/g;
        // const dateMatches = url.match(datePattern);
        
        // if (dateMatches) {
        //     console.log(dateMatches); // This will output an array of date strings ["19-09-2023", "20-09-2023"]
        // } else {
        //     console.log("No date patterns found in the URL");
        // }
        
        // url = url.replace(/\d{2}-\d{2}-\d{4},\d{2}-\d{2}-\d{4}/, `${fromDate},${toDate}`);
        // console.log(fromDate, toDate, url);
        // Open the updated URL in a new tab
        // window.open(url);
        // Redirect to the new URL
        window.location.href = url;
    }

    // Attach the updateUrl function to the button click event
    document.getElementById('book-villa').addEventListener('click', updateUrl);


</script>
 <!-- Zabuto Calendar CSS -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/zabuto_calendar.min.css">
<!-- Zabuto Calendar JS -->
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/zabuto_calendar.min.js"></script>
<script>
$(document).ready(function () {
    
     function formatDate(dateStr) {
        var date = new Date(dateStr);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear().toString(); // Get last 2 digits of the year
        return day + '-' + month + '-' + year;
    }

    
    var $arrivalDate = $('#from-calendar');
   var $departDate = $('#to-calendar');
  
    // Initialize Zabuto Calendar for "From" date
    $arrivalDate.zabuto_calendar({
        classname: 'table clickable',
         today_markup: '<span class="badge bg-primary">[day]</span>',
      events: <?php echo json_encode($events);?>
    });

    $departDate.zabuto_calendar({
        classname: 'table clickable',
         today_markup: '<span class="badge bg-primary">[day]</span>',
         events: <?php echo json_encode($events);?>

    });

    $arrivalDate.on('zabuto:calendar:day', function (e) {
       
      
       if(e.hasEvent && e.eventdata.classnames['0']!='booked-half'){
        alert('Already booked!');
       }else{
        $("#dest_checkIn").val(formatDate(e.value)); // Set the selected date in the input field
            $("#from-calendar-popup").hide();
             let toDateString = getThreeDayFromToday(formatDate(e.value));
            $("#dest_checkOut").val(toDateString);
             $("#from-calendar-popup").hide();
             
                 var selectedDate = $('#dest_checkIn').val();  // Default to today if no date is selected

             // Extract year and month from selected date
             var dateParts = selectedDate.split('-');
             var selectedYear = dateParts[2];  // Year from the arrival calendar
             var selectedMonth = dateParts[1]; // Month from the arrival calendar
           
            $('#to-calendar').remove();
            let $newDepartDate = $('<div id="to-calendar"></div>').appendTo('#to-calendar-popup');

            // Set the depart calendar to the same year and month as the arrival calendar
      
   $newDepartDate.zabuto_calendar({
        classname: 'table clickable',
         today_markup: '<span class="badge bg-primary">[day]</span>',
        events: <?php echo json_encode($events); ?>,
        year: parseInt(selectedYear),  // Year from arrival calendar
        month: parseInt(selectedMonth)  // Month from arrival calendar
    });
    
     $newDepartDate.on('zabuto:calendar:day', function (e) {
      
       if(e.hasEvent && e.eventdata.classnames['0']!='booked-half'){
        alert('Already booked!');
       }else{
        $("#dest_checkOut").val(formatDate(e.value)); // Set the selected date in the input field
        $("#to-calendar-popup").hide();
        document.getElementById('book-villa').disabled = true;
       }
       
    });
       }
       

    });
    
      $departDate.on('zabuto:calendar:day', function (e) {
      
       if(e.hasEvent && e.eventdata.classnames['0']!='booked-half'){
        alert('Already booked!');
       }else{
         $("#dest_checkOut").val(formatDate(e.value)); // Set the selected date in the input field
        $("#to-calendar-popup").hide();
        document.getElementById('book-villa').disabled = true;
       }
       
    });
    

   

    // Function to show the calendar popup
    function showCalendar(input, calendarPopup) {
        var offset = $(input).offset();
        $(calendarPopup).css({
            top: offset.top + $(input).outerHeight(),
            left: offset.left
        }).show();
    }

    // Show the "From" calendar popup when clicking on the input field
    $("#dest_checkIn").click(function () {
        showCalendar(this, "#from-calendar-popup");
    });

    // Show the "To" calendar popup when clicking on the input field
    $("#dest_checkOut").click(function () {
        showCalendar(this, "#to-calendar-popup");
    });

    // Hide the calendar popup when clicking outside of it
    $(document).mouseup(function (e) {
        if (!$('.calendar-popup').is(e.target) && $('.calendar-popup').has(e.target).length === 0) {
            $('.calendar-popup').hide();
        }
    });
});
</script>
 <style type="text/css" media="all">
  /*.pin-form-field.nm, .pin-form-field.cn {*/
  
  /*  width: 100%;*/
   
  /*}*/
  .pin-form-field {
    height: 2em;
    border: 1px solid black;
    padding: 0 0.5em;
    width:100%;
    background:#fff !important;
  }
  /*.pin-form-field.cvc, .pin-form-field.expiry {*/
  /*     width: 50%;*/
  /*}*/
  .popup {
    display: none;
    position: fixed;
    z-index: 10;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.popup-content {
    background-color: #eaeaea;
   
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 750px;
}
.container-fluid.p-0 {
    z-index: 3 !important;
    position: relative;
}

.closeBtn {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.closeBtn:hover,
.closeBtn:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
.container-fluid.p-0 {
    z-index:0;
    position:relative;
}
.cvs_expiry {
    display: flex;
    justify-content: space-between;
    margin:10px 0 15px;
}
.cvs_expiry > div{width:48%}
.credit_card_submit{
    background: #28a745;
    border: none;
    padding: 5px 40px;
    margin: 6px auto;
    color: #fff;
    width: 100%;
    text-align: center;
}
   .calendar-popup {
            display: none; /* Hide the calendar div initially */
            /* Position it relative to the input field */
            z-index: 1000; /* Ensure it appears above other elements */
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            
        }
        .calendar-popup .zabuto_calendar{
            width: 300px; /* Adjust width as needed */
            
        }
       .calendar-popup {
          z-index: 99999;
            position: absolute;
            top: 5% !important;
            left: 0 !important;
        }
        
        .zabuto-calendar__event.booked, .booked {
         background: #d9534f;
         color: #fff;
        }
        
        .fc-other-month .fc-day-number {
            color:transparent;
        }
        
        .fc-event-container .booked-half, .fc-event-container .booked-half-end, .fc-event-container .booked{
            
            height:26px;
            border-color:#d9534f;
            width:100%;
              left: -3px;
    padding: 0px;
    border: none;
            
        } 
        
        .booked-half {
   background: #d9534f;
    clip-path: polygon(0% 0%, 100% 0%, 00% 100%);
    width: 35px;
      color: #fff;
    height: 26px;
    background-repeat: no-repeat;
    background-size: contain;
      
}

.booked-half-end {
    background: #d9534f;
    clip-path: polygon(100% 0%, 0% 100%, 100% 100%); 
      color: #fff;
    width: 35px;
    height: 26px;
    background-repeat: no-repeat;
    background-size: contain;
}
.zabuto-calendar.table>tbody td .badge {
    font-size: 100%;
    color: #fff;
}
        
</style>

<!-- ./ End Main -->

<?php } ?>
<?php get_footer(); ?>