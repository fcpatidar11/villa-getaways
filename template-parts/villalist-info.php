<div class="row">
<?php
$conn = oracleDbConnection();


$destination_id = ( isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] ) ? $_REQUEST['destination_id'] : '';


$date_start = ( isset($_REQUEST['date_start']) && $_REQUEST['date_start'] ) ? $_REQUEST['date_start'] : date('d-m-Y', strtotime("today"));
$date_end = ( isset($_REQUEST['date_end']) && $_REQUEST['date_end'] ) ? $_REQUEST['date_end'] : date('d-m-Y', strtotime("+1 year"));

$start = ( isset($_REQUEST['date_start']) && $_REQUEST['date_start'] ) ? $_REQUEST['date_start'] : date('d-m-Y', strtotime("today"));
$end = ( isset($_REQUEST['date_end']) && $_REQUEST['date_end'] ) ? $_REQUEST['date_end'] : date('d-m-Y', strtotime("+1 day"));
$date1 = DateTime::createFromFormat('d-m-Y', $start);
$date2 = DateTime::createFromFormat('d-m-Y', $end);
$diff = $date1->diff($date2)->format('%a');

$bedrooms = ( isset($_REQUEST['bedrooms']) && $_REQUEST['bedrooms'] ) ? $_REQUEST['bedrooms'] : 1;
$page = ( isset($_REQUEST['page']) && $_REQUEST['page'] ) ? $_REQUEST['page'] : 1;

$price = ( isset($_REQUEST['price']) && $_REQUEST['price'] ) ? $_REQUEST['price'] : "";

$location_ids = "";
if( isset($_REQUEST['location_id']) && $_REQUEST['location_id'] ) {
    foreach($_REQUEST['location_id'] as $id) {
        if($location_ids == "")
            $location_ids = $id;
        else
            $location_ids .= ", " . $id;
    }
}

$region_ids = "";
if( isset($_REQUEST['region_id']) && $_REQUEST['region_id'] ) {
    foreach($_REQUEST['region_id'] as $id) {
        if($region_ids == "")
            $region_ids = $id;
        else
            $region_ids .= ", " . $id;
    }
}

// if( $destination_id ) {
   
//     $villas = searchVillalistVillas($conn, $_REQUEST['destination_id'], $location_ids, $region_ids, date("d/m/Y", strtotime($date_start)), date("d/m/Y", strtotime($date_end)), $bedrooms, $page, $price);
// }else {
    $villas = $args['destination'];
//}

if ( isset($villas) && $villas ) {
    foreach( $villas AS $villa ) {
        $des_name = strtolower(str_replace(" ", "-", $villa['DESTINATION_NAME']));
        $loc_name = strtolower(str_replace(" ", "-", $villa['LOCATION_NAME']));
        $location = strtolower(str_replace(" ", "-", $villa['LOCATION']));
        $loc = $loc_name ? $loc_name : $location;
        $slug = "villa-rentals-" . $loc . "-" . $villa["VG_NUMBER"];
        $villa_images = fetchVillaSliderImages($conn, $villa["VILLA_ID"]);
        $villa_price = fetchVillaMinMaxPrice($conn, $villa["VILLA_ID"]);
        $agent_id = $villa["AGENT_ID"];
    ?>
    <div class="single-villa-box col-12 col-md-6">
        <div class="card-column">
            <div class="card-column__block">
                <?php if($villa['IS_PRIORITY']) { ?>
                <div class="img-wrapper-star">
                    <a id="star" href="javascript: void(0)" data-toggle="tooltip" data-placement="top" title="Highly Recommended">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.0277 7.09146L19.0277 7.09144C19.0556 7.06343 19.0613 7.04349 19.0636 7.03066C19.0667 7.01275 19.0653 6.98695 19.0536 6.95872C19.042 6.93047 19.025 6.91188 19.0112 6.90209C19.0016 6.89534 18.9848 6.88568 18.9464 6.88568H13.8515C13.0658 6.88568 12.3593 6.41183 12.0586 5.68766L19.0277 7.09146ZM19.0277 7.09146L14.907 11.2255C14.4087 11.7254 14.2209 12.4556 14.4137 13.1333L14.4138 13.1335L16.0479 18.8715C16.0479 18.8715 16.0479 18.8715 16.0479 18.8715C16.0618 18.9202 16.0555 18.9468 16.0494 18.963C16.0413 18.9848 16.0233 19.0113 15.9933 19.0333C15.9632 19.0553 15.9334 19.0638 15.912 19.0649C15.8967 19.0656 15.8712 19.0641 15.8303 19.0368L11.0699 15.8529L11.0699 15.8528C10.4171 15.4163 9.56598 15.4163 8.91312 15.853L4.16077 19.0315C4.16076 19.0315 4.16074 19.0315 4.16073 19.0315C4.11924 19.0593 4.09306 19.061 4.07707 19.0602C4.05488 19.0591 4.02438 19.0503 3.99378 19.0279C3.96319 19.0055 3.94476 18.9784 3.93638 18.956C3.93012 18.9392 3.92373 18.9118 3.93781 18.8624L3.93782 18.8624L5.56935 13.1335C5.76244 12.4555 5.57425 11.7252 5.07616 11.2255L5.07615 11.2255L0.975821 7.11189C0.975819 7.11189 0.975818 7.11189 0.975816 7.11189C0.946038 7.08201 0.939215 7.05985 0.936608 7.04475C0.933122 7.02456 0.934794 6.99644 0.947317 6.96611C0.959841 6.93577 0.978208 6.91533 0.993925 6.90422C1.00535 6.89614 1.02452 6.88568 1.06557 6.88568H6.13167C6.9174 6.88568 7.62388 6.41176 7.92463 5.68766L7.92463 5.68765L9.86201 1.02282C9.86201 1.02281 9.86202 1.0228 9.86203 1.02278C9.87947 0.980807 9.89893 0.964601 9.91275 0.955844C9.93088 0.944355 9.95816 0.935 9.99158 0.935C10.025 0.935 10.0523 0.944355 10.0704 0.955844C10.0842 0.964601 10.1037 0.980809 10.1211 1.02279C10.1211 1.0228 10.1211 1.02281 10.1211 1.02282L12.0585 5.68752L19.0277 7.09146Z" stroke="#9d9d9d" stroke-width="1.87"/>
                        </svg>
                    </a>
                </div>
                
                <!--<div class="img-star">-->
                <!--    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                <!--        <path d="M19.0277 7.09146L19.0277 7.09144C19.0556 7.06343 19.0613 7.04349 19.0636 7.03066C19.0667 7.01275 19.0653 6.98695 19.0536 6.95872C19.042 6.93047 19.025 6.91188 19.0112 6.90209C19.0016 6.89534 18.9848 6.88568 18.9464 6.88568H13.8515C13.0658 6.88568 12.3593 6.41183 12.0586 5.68766L19.0277 7.09146ZM19.0277 7.09146L14.907 11.2255C14.4087 11.7254 14.2209 12.4556 14.4137 13.1333L14.4138 13.1335L16.0479 18.8715C16.0479 18.8715 16.0479 18.8715 16.0479 18.8715C16.0618 18.9202 16.0555 18.9468 16.0494 18.963C16.0413 18.9848 16.0233 19.0113 15.9933 19.0333C15.9632 19.0553 15.9334 19.0638 15.912 19.0649C15.8967 19.0656 15.8712 19.0641 15.8303 19.0368L11.0699 15.8529L11.0699 15.8528C10.4171 15.4163 9.56598 15.4163 8.91312 15.853L4.16077 19.0315C4.16076 19.0315 4.16074 19.0315 4.16073 19.0315C4.11924 19.0593 4.09306 19.061 4.07707 19.0602C4.05488 19.0591 4.02438 19.0503 3.99378 19.0279C3.96319 19.0055 3.94476 18.9784 3.93638 18.956C3.93012 18.9392 3.92373 18.9118 3.93781 18.8624L3.93782 18.8624L5.56935 13.1335C5.76244 12.4555 5.57425 11.7252 5.07616 11.2255L5.07615 11.2255L0.975821 7.11189C0.975819 7.11189 0.975818 7.11189 0.975816 7.11189C0.946038 7.08201 0.939215 7.05985 0.936608 7.04475C0.933122 7.02456 0.934794 6.99644 0.947317 6.96611C0.959841 6.93577 0.978208 6.91533 0.993925 6.90422C1.00535 6.89614 1.02452 6.88568 1.06557 6.88568H6.13167C6.9174 6.88568 7.62388 6.41176 7.92463 5.68766L7.92463 5.68765L9.86201 1.02282C9.86201 1.02281 9.86202 1.0228 9.86203 1.02278C9.87947 0.980807 9.89893 0.964601 9.91275 0.955844C9.93088 0.944355 9.95816 0.935 9.99158 0.935C10.025 0.935 10.0523 0.944355 10.0704 0.955844C10.0842 0.964601 10.1037 0.980809 10.1211 1.02279C10.1211 1.0228 10.1211 1.02281 10.1211 1.02282L12.0585 5.68752L19.0277 7.09146Z" stroke="#9d9d9d" stroke-width="1.87"/>-->
                <!--    </svg>-->
                <!--</div>-->
                
                <?php } ?>
                
                <div class="img-wrapper-heart villa_fav">
                    <a href="javascript:toggleFavorite(3746, 'Ubud','Villa 3240')" class="heart-img-1" id="fav-<?php echo $villa["VG_NUMBER"]?>" data-id="<?php echo $villa["VG_NUMBER"]?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/>
                        </svg>
                    </a>
                </div>
                
                <!--<div class="img-heart">-->
                <!--    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">-->
                <!--        <path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8v-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5v3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20c0 0-.1-.1-.1-.1c0 0 0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5v3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2v-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z" stroke="#9d9d9d" fill="#9d9d9d" stroke-width="1.87" />-->
                <!--    </svg>-->
                <!--</div>-->
                
                    <div class="card__block card-img-slider-1 owl-carousel">
                        <?php 
                        if($villa_images) {
                            foreach($villa_images AS $villa_image) {
                            ?>
                            <a href="<?php echo home_url($des_name . "/" . $slug); ?>.html">
                                <div class="img-wrapper">
                                    <img class="owl-lazy" data-src="<?php echo home_url("/wp-content/uploads/" . $villa_image); ?>" alt="<?php echo $villa["VILLA_NAME"]; ?>" srcset="">
                                    <div class="content">
                                        <h3 class="villa_name"><?php echo "Villa " . $villa["VG_NUMBER"]; ?></h3>
                                        <p class="villa_position">
                                            <!--Ubud-->
                                            <?php echo $loc_name ? $loc_name : $location; ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                            <?php 
                            }
                        } else {
                            ?>
                            <a href="<?php echo home_url($des_name . "/" . $slug); ?>.html">
                                <div class="img-wrapper">
                                    <img class="owl-lazy" data-src="<?php echo home_url("/wp-content/uploads/" . $villa["IMAGE"]); ?>" alt="<?php echo $villa["VILLA_NAME"]; ?>" srcset="">
                                    <div class="content">
                                        <h3 class="villa_name"><?php echo $villa["VILLA_NAME"]; ?></h3>
                                        <p class="villa_position">Ubud</p>
                                    </div>
                                </div>
                             </a>
                            <?php
                        }
                        ?>
                    </div>
                
                <div class="card-additional-details d-flex">
                    <div class="details-heading d-flex">
                        <div class="img-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 24 24"><!--Generated by IJSVG (https://github.com/iconjar/IJSVG)--><g stroke-linecap="round" stroke-width="1" stroke="#000" fill="none" stroke-linejoin="round"><path d="M3,10.25h18l-4.37114e-08,1.77636e-15c0.552285,-2.41411e-08 1,0.447715 1,1v4h-20v-4l1.06581e-14,-1.07284e-07c8.33927e-08,-0.552285 0.447715,-1 1,-1Z"/><path d="M22,15.25v1v0c0,0.552285 -0.447715,1 -1,1h-18h-4.37114e-08c-0.552285,-2.41411e-08 -1,-0.447715 -1,-1c0,0 0,-3.55271e-15 0,-3.55271e-15v-1"/><path d="M3,17.25v2.5"/><path d="M21,17.25v2.5"/><path d="M3,10.25v-4l2.30926e-14,3.01992e-07c-1.66785e-07,-1.10457 0.89543,-2 2,-2h14l-8.74228e-08,1.77636e-15c1.10457,-4.82823e-08 2,0.89543 2,2v4"/><path d="M3,10.25l3.37508e-14,4.52987e-07c-2.50178e-07,-1.65685 1.34315,-3 3,-3h3l-1.31134e-07,3.55271e-15c1.65685,-7.24234e-08 3,1.34315 3,3"/><path d="M12,10.25l3.37508e-14,4.52987e-07c-2.50178e-07,-1.65685 1.34315,-3 3,-3h3l-1.31134e-07,3.55271e-15c1.65685,-7.24234e-08 3,1.34315 3,3"/></g></svg>
                        </div>
                        <p><?php echo $villa["BEDS"] ; ?> Bedrooms</p>
                        <p style="margin-left: 0;margin-right: 15px;"> </p>
                    </div>
                    <div class="details-heading d-flex">
                        
                        <div class="img-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 24 24"><!--Generated by IJSVG (https://github.com/iconjar/IJSVG)--><g stroke-linecap="round" stroke-width="1" stroke="#000" fill="none" stroke-linejoin="round"><path d="M13.5,14.274h9.486l-2.57266e-08,4.63523e-11c0.276142,-0.000497546 0.500403,0.222956 0.5009,0.499098c1.85716e-05,0.0103074 -0.000281588,0.0206129 -0.000900047,0.0309017l6.72876e-08,-1.14064e-06c-0.280602,4.75668 -4.22005,8.47033 -8.985,8.47h-5l-4.77021e-07,7.34062e-11c-4.76679,0.000733396 -8.70741,-3.71536 -8.986,-8.474l-2.62277e-08,-4.36331e-07c-0.0165687,-0.275645 0.193454,-0.512531 0.469099,-0.5291c0.0102888,-0.000618451 0.0205943,-0.000918602 0.0309017,-0.000900022h5.485"/><path d="M17.27,2.136l0.9,-0.9l1.74662e-08,-1.55314e-08c0.825428,-0.73399 2.08959,-0.659866 2.82358,0.165563c0.326822,0.367536 0.506758,0.842611 0.505423,1.33444v11.538"/><path d="M17.27,5.488l-3.67532e-08,3.59759e-08c0.935381,-0.915598 0.951418,-2.41611 0.03582,-3.35149c-0.915598,-0.935381 -2.41611,-0.951418 -3.35149,-0.03582c-0.0118988,0.0116471 -0.0236745,0.0234194 -0.0353252,0.0353147l-0.205,0.205l2.65405e-08,-2.65485e-08c-0.195191,0.19525 -0.195191,0.51175 -5.30811e-08,0.707l2.644,2.645l-3.52833e-08,-3.53726e-08c0.195015,0.195509 0.511597,0.195909 0.707106,0.000893854c0.000298304,-0.000297551 0.000596233,-0.000595479 0.000893784,-0.000893784Z"/><path d="M11.41,3.36l-1.82,0.83"/><path d="M15.89,7.86l-0.78,1.83"/><path d="M13.21,6.07l-1.42,1.41"/><path d="M13.5,13.274v0c0,-0.552285 -0.447715,-1 -1,-1h-5l-4.37114e-08,1.77636e-15c-0.552285,2.41411e-08 -1,0.447715 -1,1c0,0 0,0 0,0v5.5l5.32907e-15,7.54979e-08c4.16963e-08,0.276142 0.223858,0.5 0.5,0.5h6h-2.18557e-08c0.276142,1.20706e-08 0.5,-0.223858 0.5,-0.5Z"/></g></svg>
                        </div>
                        <p><?php echo $villa["BATHS"]; ?> Bathrooms</p>
                        <p style="margin-left: 0;margin-right: 15px;"> </p>
                    </div>
                    <div class="details-heading d-flex">
                        <div class="img-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 24 24"><!--Generated by IJSVG (https://github.com/iconjar/IJSVG)--><g stroke-linecap="round" stroke-width="1" stroke="#000" fill="none" stroke-linejoin="round"><path d="M10.5052,4.7448c1.65973,1.65973 1.65973,4.35068 0,6.01041c-1.65973,1.65973 -4.35068,1.65973 -6.01041,0c-1.65973,-1.65973 -1.65973,-4.35068 -1.77636e-15,-6.01041c1.65973,-1.65973 4.35068,-1.65973 6.01041,-1.77636e-15"/><path d="M0.5,20.5l7.99361e-14,1.05697e-06c-5.83749e-07,-3.86599 3.13401,-7 7,-7c3.86599,-5.83749e-07 7,3.13401 7,7c0,0 0,3.55271e-15 0,3.55271e-15Z"/><path d="M16.5,20.5h7l1.042e-09,0.000107061c0,-3.03757 -2.46243,-5.5 -5.5,-5.5c-0.879341,0 -1.74586,0.210842 -2.5269,0.614844"/><path d="M20.2421,7.9519c1.2692,1.2692 1.2692,3.32699 0,4.59619c-1.2692,1.2692 -3.32699,1.2692 -4.59619,0c-1.2692,-1.2692 -1.2692,-3.32699 0,-4.59619c1.2692,-1.2692 3.32699,-1.2692 4.59619,-8.88178e-16"/></g></svg>
                        </div>
                        <p><?php echo $villa["SLEEPS"]; ?> Guests </p>
                    </div>
                    
                    
                </div>
                <div class="card-content">
                    <?php if($villa['IS_PRIORITY']) { ?>
                    <p class="higly-recom">Highly Recommended</p>
                    <?php } ?>
                    <?php
                    if ($villa["PRICE"] > 0) {
                        if(isset($_REQUEST['date_start']) && $_REQUEST['date_start'] == "" && isset($_REQUEST['date_end']) && $_REQUEST['date_end'] == ""){
                            if($villa_price) { ?>
                                <p class="rate">$ <?php echo number_format($villa_price["MIN"], 2, '.', ','); ?> (MIN) - $ <?php echo number_format($villa_price["MAX"], 2,".", ","); ?> (MAX) nightly</p>
                            <?php }
                        }else {
                            $total_price = ( $villa["PRICE"] + (( $villa["PRICE"] * $villa["TAX_PERCENTAGE"] ) / 100 ) ) * $diff;
                            $formattedNumber = number_format($total_price, 0, ',', ',');
                            
                        ?>
                            <p class="best-rate">Best Rate</p>
                            <p class="rate"><?php echo $villa["CURRENCY"]; ?>$ <?php echo number_format($villa["PRICE"], 2, '.', ','); ?> <?php echo ($villa["TAX_PERCENTAGE"]) ? "+ " . $villa["TAX_PERCENTAGE"] . "% taxes" : ""; ?> x <?php echo $diff>1 ? "$diff nights": "1 night";?></p>
                            <p class="total-rate">Total <?php echo $villa["CURRENCY"]; ?>$ <?php echo number_format($total_price, 2, '.', ','); ?></p>
                    <?php 
                        }
                    } else {
                    ?>
                        <p>Contact us for Rates and Availablity</p>
                    <?php
                    }
                    ?>
                </div>
                <?php
                if ($villa["OFFER_TEXT"]) {
                ?>
                <div class="card-content">
                    <p>
                        <?php echo $villa["OFFER_TEXT"]; ?>
                    </p>
                </div>
                <?php 
                }
                ?>
            </div>
           
            <div class="info-buttons d-flex">
                <div class="btn-wrapper destop-button">
                    <a data-agentId="<?php echo $agent_id; ?>" data-bedrooms="<?php echo $villa["SLEEPS"]; ?>" data-bathrooms="<?php echo $villa["BATHS"]; ?>" data-villa-number="<?php echo $villa["VG_NUMBER"]; ?>" data-villa-id="<?php echo $villa["VILLA_ID"]; ?>" data-villa-image="<?php echo home_url("/wp-content/uploads/" . $villa['IMAGE']); ?>" href="javascript:void(0);" onclick="openInquieryForm(this)" class="btn">Inquire Now</a>
                </div>
                <div class="btn-wrapper mobile-button">
                    <a target="_blank" href="<?php echo home_url().'/inquiry?villa_number='.$villa["VG_NUMBER"]; ?>" class="btn">Inquire Now</a>
                </div>
                  <div class="btn-wrapper">
                    <a href="<?php echo home_url($des_name . "/" . $slug); ?>.html" class="btn">View Villa</a>
                </div>
            </div>
        </div>
    </div>
    <?php 
    }
} else {
    ?>
    <div class="single-villa-box col-12 col-md-12 no-villa-found">
        <h4>No Villa Found</h4>
    </div>
    <?php
}

$nextUrl = "";
$prevUrl = "";

if(!is_page('favourites')) {
    $currentUrl = "http" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on" ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $page = ( isset($_REQUEST['page']) && $_REQUEST['page'] ) ? $_REQUEST['page'] : 1;

    $parts = parse_url($currentUrl);
    parse_str($parts['query'], $queryParams);
    
    // Modify the page parameter
    if($page) {
        $queryParams['page'] = $page -1;
        // Build the new query string
        $newQueryString = http_build_query($queryParams);
        // Build the new URL with the modified query string
        $prevUrl = $parts['scheme'] . '://' . $parts['host'] . $parts['path'] . '?' . $newQueryString;
        $queryParams['page'] = $page + 1;
        $newQueryString = http_build_query($queryParams);
        $nextUrl = $parts['scheme'] . '://' . $parts['host'] . $parts['path'] . '?' . $newQueryString;
    }
    
    if ( isset($villas) && $villas ) {
        ?>
        <div class="pagination">
            <a class="<?php if($page == 1) echo "disable-btn"; ?>" href="<?php echo $prevUrl; ?>">Prev Page</a>
            <a class="<?php if(count($villas) < 20) echo "disable-btn"; ?>" href="<?php echo $nextUrl; ?>">Next Page</a>
        </div>
        <?php
    }
}
?>
</div>