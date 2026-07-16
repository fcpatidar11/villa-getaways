<?php 
/* Template Name: Villa Booking */
get_header();
if (isset($_GET)) {
    // set_transient('booking_details', $_GET, 12 * HOUR_IN_SECONDS); // Expires in 12 hours
}
// print_r($_GET);

function formatDate($dateStr) {
    // Create a DateTime object from the original format
    $date = DateTime::createFromFormat('d-M-y', $dateStr ?? '');

    // Unparseable or null input yields false; caller treats '' as "no date".
    return $date ? $date->format('Y-m-d') : '';
}

$arrivalDate = $_GET['arrivalDate'] ?? "";
$departureDate = $_GET['departureDate'] ?? "";
$NoOfRooms = $_GET['NoOfRooms'] ?? "";
// new DateTime() throws on unparseable input; an empty string means "now",
// which is the behaviour this page already relied on.
try {
    $date1 = new DateTime($arrivalDate ?: "now");
    $date2 = new DateTime($departureDate ?: "now");
    $numberOfNights= $date2->diff($date1)->format("%a");
} catch (Exception $e) {
    $date1 = new DateTime();
    $date2 = new DateTime();
    $numberOfNights = 0;
}
$show_details = false;
if( !empty($arrivalDate ) && !empty($departureDate ) && !empty($NoOfRooms )){
    $show_details = true;
}
$url_path = trim($_SERVER['REQUEST_URI'], '/');

// Split the URL by '?' to separate the path from the query string
$url_parts = explode('?', $url_path);

// The first part is the path without the query string
$path_without_query = $url_parts[0];
$path_without_query = trim($path_without_query, '/');
// Split the path by '-' to get the parts
$parts = explode('-', $path_without_query);

// Get the last part of the URL (which should be the booking ID)
$vg_number = end($parts);



//die;

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
$villa_pricing = [];
$external_book_url = "";
$events = [];
if(!empty($vg_number)) {
    $conn = oracleDbConnection();
    $standardText = fetchStandardTerms($conn);
    $countryIds = getCountryId($conn);
    $villa_details = fetchVillaDetails($conn, $vg_number, $bedrooms);
    
    global $footer_location_id;
    global $footer_destination_id;
    $footer_location_id = $villa_details['LOCATION_ID'] ?? "";
    $footer_destination_id = $villa_details['DESTINATION_ID'] ?? "";

    $agent_id = $villa_details['AGENT_ID'] ?? "";
    $external_book_url = fetchExternalBookUrl($conn);
    if(isset($villa_details) && count($villa_details) > 0) {
         $villa_id = $villa_details['VILLA_ID'];
       $conn = oracleDbConnection();
$bookedDates = fetchVillaBookedDates($conn,$villa_id);
$bookedDatesformatted =[];
//echo "<pre>";

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
        
        $number_of_rooms = fetchNumberOfRooms($conn, $villa_id);
        $villa_inclusions = fetchVillaInclusions($conn, $villa_id);
        
        if($arrivalDate && $departureDate && $NoOfRooms) {
            $villa_pricing = callBookNowPricing($conn, $villa_id, $arrivalDate, $departureDate, $NoOfRooms);
           
        }
        // $email and friends are never assigned in this template, so this branch
        // has never run; !empty() keeps it inert without warning on every load.
        if(!empty($email)) {
          $client_id = callCreateClient($conn, $firstName, $surname, $email, $mobile, $address, $suburb, $state, $postcode, 1, $villa_id);
        // set_transient('client_id', $client_id, 12 * HOUR_IN_SECONDS);
        // set_transient('villa_id', $villa_id, 12 * HOUR_IN_SECONDS);
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



if(isset($_POST['paymentoption']) && $_POST['paymentoption']=="wire_transfer"){
    // $p_villa_id = $_POST['villaid'];
    // $p_client_id = $_POST['clientid'];
    $p_arrive = $arrivalDate;
    $p_depart = $departureDate;
    // $p_num_nights = $_POST['departureDate'];
    // $p_num_people = isset($_POST['adults']) ? $_POST['adults'] : "";
    // // echo $p_num_people;
    // $p_num_children = $_POST['children'];
    // $p_bedrooms = $_POST['NoOfRooms'];
    // $p_price_night = str_replace(',', '', $_POST['villaprice']);
    // $p_payment_method = "wt";
    
  
    // $format = 'd-m-Y'; // Date format
    // $date = DateTime::createFromFormat($format, $p_arrive);
    // $today = new DateTime();
    // $interval = $today->diff($date);
   
    // if ($interval->days <= 30 && $date > $today) {
    //   $p_full_payment = 1;
    // } else {
    //     $p_full_payment = 0;
    // }
    
    // getting booking id
    //  $booking_id = callCreateBooking($conn, $p_villa_id, $p_client_id, $p_arrive, $p_depart, $p_num_nights, $p_num_people, $p_num_children, $p_bedrooms, $p_price_night, $p_payment_method, $p_full_payment);
     $booking_id =  ($_COOKIE["booking_id"] ?? "");
    //  $p_client_id = $_COOKIE["client_id"];
     $p_villa_id = ($_COOKIE["villa_id"] ?? "");
     
     $fname = ($_COOKIE["fname"] ?? "");
     $lname = ($_COOKIE["lname"] ?? "");
    
     $p_payment_method_id=2;
     $p_receipt_tx="";
     $p_currency_code=($_POST['currency'] ?? "");
     $csymbol =getCurrencySymbol($p_currency_code);
     $p_amount_charged=($_POST['camount'] ?? "");
     $p_pin_payment_fee="";
     $p_security_deposit=null;
     $p_description=($_POST['description'] ?? "");
     $p_payment_type_id=7;
     $p_amount_charged=$p_amount_charged+20;
     
     // Email recipient
$to = ($_POST['email_2'] ?? "");

   $currentDate = new DateTime();
    
    // Add 2 days to the current date
    $currentDate->modify('+2 days');
    
    // Return the new date in your desired format (e.g., Y-m-d)
    
// Email subject
$subject = 'Booking Confirmation and Payment Instructions';

// Email message (HTML content)
$message = '
<html>
<head>
    <title>Booking Confirmation and Payment Instructions</title>
</head>
<body>
    <p>Dear '.$fname." ".$lname.',</p>
    <p>Thank you for choosing to stay with us at '.$p_description.'. We are delighted to confirm your booking for the following details:</p>
    <p><strong>Booking Details:<strong></p>
     <ul>
     <li>Booking ID: '.$booking_id.'</li>
     <li>Villa/Room Name: '.$p_description.'</li>
     <li>Check-in Date: '.$p_arrive.'</li>
     <li>Check-out Date: '.$p_depart.'</li>
     </ul>
     <p>Total Amount Due: <strong>'.$csymbol.''.number_format($p_amount_charged, 2, '.', ',').' '.$p_currency_code.'</strong> (inclusive of '.$csymbol.'20.00 '.$p_currency_code.' bank processing fee)</p>

     <p>To confirm your booking, we kindly request you to complete the payment via bank transfer. Please find our bank details below:</p>
     

      <p>Bank Transfer Details:</p>
      <ul>
        <li>Bank Name: Bank of New Zealand (Harbour Quays branch)</li>
        <li>Account Name: Villa Getaways Ltd</li>
        <li>Payment Deadline: '.$currentDate->format("d-m-Y").'</li>';
        if($p_currency_code=="USD"){
        $message .= '<li>Account No.: 670644-0000</li>
        <li>Branch: 02-1000</li>
        <li>SWIFT Code: BKNZNZ22</li>
        <li>Currency Code: USD</li>
        <li>IBAN: 1000-670644-0000</li>';
         }elseif($p_currency_code=="AUD") { 
        $message .= ' <li>Account No.: 670644-0000</li>
        <li>Branch: 02-1000</li>
        <li>SWIFT Code: BKNZNZ22</li>
        <li>Currency Code: AUD</li>
        <li>IBAN: 1000-670644-0000</li>';
         }elseif($p_currency_code=="EUR"){
        $message .= '<li>Account No.: 670644-0002</li>
        <li>Branch: 02-1000</li>
        <li>SWIFT Code: BKNZNZ22</li>
        <li>Currency Code: EUR</li>
        <li>IBAN: 1000-670644-0000</li>';
         }else{
        $message .= '<li>Account No.: 02-0472-0190690-025</li>
        <li>Branch: 02-1000</li>
        <li>SWIFT Code: BKNZNZ22</li>
        <li>Currency Code: NZD</li>
        <li>IBAN: 1000-670644-0000</li>';
         }
        
       $message .= '</ul>    
      

     <p>Please ensure that the payment is made by the above deadline to secure your booking. Once the payment is completed, kindly send us a copy of the transfer receipt to  <a href=" Sales@VillaGetaways.com">Sales@VillaGetaways.com</a> for verification.</p>
    <p>Best regards,<br>villagetaways</p>
</body>
</html>
';

// Email headers
$headers = array(
    'Content-Type: text/html; charset=UTF-8',
    'From: Villa Gataways <admin@wptest.villagetaways.com>',
);

// Send the email
$success = wp_mail( $to, $subject, $message, $headers );
     
     
     //$success = callRecordPayment($conn, $booking_id,$p_payment_method_id,$p_receipt_tx,$p_currency_code,$p_amount_charged,$p_pin_payment_fee,$p_security_deposit,$p_description,$p_payment_type_id);
    
    if($success==1){
    ?>
    <script type="text/javascript">
        
            window.location.href = "https://wptest.villagetaways.com/response/?success=1&email=<?php echo $to;?>";
        
    </script>
    <?php
    }else{ ?>
    <script type="text/javascript">
        
            window.location.href = "https://wptest.villagetaways.com/response/?success=0";
        
    </script>
<?php }
    
}

?>
<style type="text/css">
    .secondary-form{
        margin-top:30px;
    }
    #confirm-booking{
        margin-top:16px;
    }
    h2.top-form-heading {
        background: #eaeaeaea;
        padding: 10px;
        width: 100%;
        text-align: center;
    }
    .head-section > h5 {
        background: #1D346A;
        color: #FFF;
        padding: 7px;
    }
    .table-head {
        background: #1D346A;
        color: #FFF;
    }
    #terms-and-conditions-block {
        display: none;
    }
    .payment-options label{
        margin:0px;
    }
   .payment-options .d-flex{
       align-items:center;
       gap:5px;
   }
   .dest-detail h5{
       padding: 12px;
       background-color:#eaeaea;
       color:black !important;
       font-weight:normal;
       width:100%;
   }
   .dest-detail .right-aside .calendar-img-box{
       background:unset;
   }
   .payment-options, .payment-methods .row{
       padding: 0px 12px;
   }
   div#show-data table td {
        border: none;
    }
    .payment_method:checked + button {
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    .payment-methods input{
        opacity:0;
    }
   @media (max-width: 500px){
       .payment-methods .col{
           margin-top:10px;
       }
   }
   
   .row.cardform{
       display:none;
   }
   .row.cardform.active{
       display:none;
   }
</style>

<div class="dest-detail-bar">
    <div class="container p-0">
        <div class="row">
            <div class="col-lg-<?php echo !empty($villa_price) ?'3':'4';?>">
                <h1 class="villa-name"><?php echo $villa_details["LOCATION_NAME"]; ?> Villa <?php echo $villa_details["VG_NUMBER"]; ?></h1>
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
                <div id="dest-slider">
                    <?php echo get_template_part('template-parts/villa/villa', 'image', ["villa_details" => $villa_details]); ?>
                </div>
                
                <div>
                    <br/>
                    <hr/>
                    <h4>Inclusions</h4>
                    <?php echo $villa_inclusions["INCLUSIONS"]; ?>
                </div>
                
                <?php if(isset( $villa_pricing ) && $villa_pricing && sizeof($villa_pricing) ) : 
                
                $csymbol =getCurrencySymbol($villa_pricing["o_currency"]);
                ?>
                <div class="dest-detail-s" id="show-data">
                    <div class="container p-0">
                        <div class="row">
                            <div class="col-xl-12 left-aside mb-5 single-villa">
                                <div class="head-section">
                                    <table class="table">
                                        <thead class="table-head">
                                            <tr>
                                                <h5>Pricing</h5>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong><?php echo $csymbol."". number_format($villa_pricing["o_night_rate"], 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?> x <?php echo $numberOfNights;  ?> nights</strong></td>
                                                <td style="text-align:right"><?php echo "$" . number_format(($villa_pricing["o_night_rate"] * $numberOfNights), 2, '.', ',') . " " . $villa_pricing["o_currency"];  ?></td>
                                            </tr>
                                            <tr>
                                                <td class="tax"><strong>Tax</strong><div class="tax_percent"><?php if($villa_pricing["o_tax_percent"]==0){echo 'N/A'; }else{echo $villa_pricing["o_tax_percent"]."%";} ?></div></td>
                                                <td style="text-align:right"><?php echo $csymbol."". number_format($villa_pricing["o_tax"], 2, '.', ',')." "." " . $villa_pricing["o_currency"]; ?> </td>
                                            </tr>
                                            
                                             <tr class="bank" style="display:none;">
                                                <td><strong>Bank processing fees</strong></td>
                                                <td style="text-align:right"> <?php echo "$20 ".$villa_pricing["o_currency"]; ?> </td>
                                            </tr>
                                            <tr class="bank" style="display:none;">
                                                <td><strong>Total</strong></td>
                                                <td style="text-align:right"><?php echo $csymbol."". number_format($villa_pricing["o_total"]+20, 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?></td>
                                            </tr>
                                            
                                            <tr class="cash">
                                                <td><strong>Total</strong></td>
                                                <td style="text-align:right"><?php echo $csymbol."". number_format($villa_pricing["o_total"], 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                            </tr>
                                            
                                             <tr  class="bank" style="display:none;">
                                                <td><strong>Deposit</strong></td>
                                                <td style="text-align:right"><?php echo $csymbol."" . number_format($villa_pricing["o_deposit"]+20, 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?></td>
                                            </tr>
                                            <tr  class="cash">
                                                <td><strong>Deposit</strong></td>
                                                <td style="text-align:right"><?php echo $csymbol."" . number_format($villa_pricing["o_deposit"], 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Security Bond</strong></td>
                                                <td style="text-align:right"><?php echo $csymbol."". number_format($villa_pricing["o_security_bond"], 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
                
            <div class="col-xl-6 right-aside">
                <div class="calendar-img-box" style="padding: 0 10px;">
                    <div class="row">
                        <h2 class="top-form-heading">Booking</h2><br/><br/>
                        <div class="col-md-12">
                            <div class="checkInOut">
                                <form method="POST" id="booking">
                                    <input type="hidden" class="form-control" name="villa_id" id="villa_id" value="<?php echo $villa_id; ?>"/>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Arrival <strong style="color:red">*</strong></label>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <div class="date-range input-group">
                                                <div class="checkIn">
                                                    <input class="form-control" type="text" name="arrival" id="arrival"  placeholder="Arrival" value="<?php echo $arrivalDate ? : "";?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Departure <strong style="color:red">*</strong></label>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <div class="date-range input-group">
                                                <div class="checkOut">
                                                    <input class="form-control" type="text" name="departure" id="departure"  value="<?php echo $departureDate ? : "";?>" placeholder="Departure" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Number of Rooms <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <select class="form-control" name="nr_of_rooms" id="nr_of_rooms" required>
                                                <option value="" selected disabled hidden>Select Number of Rooms</option>
                                                <?php foreach( $number_of_rooms AS $number ) { ?>
                                                    <option value="<?php echo $number; ?>"><?php echo $number; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                        
                                    <input class="form-control" type="hidden" name="villaid" id="villaid" value="<?php echo $villa_id;?>">
                                    <input class="form-control" type="hidden" name="vg_number" id="vg_number" value="<?php echo $vg_number;?>">
                                     <button style="width: 100%;margin:20px 0px;" type="submit"  id="confirm_book-villa" type="" class="btn btn-success">Get Pricing Details</button>
                               
                                  <div id="from-calendar-popup" class="calendar-popup">
                                    <div id="from-calendar"></div>
                                </div>
                                
                                <div id="to-calendar-popup" class="calendar-popup">
                                    <div id="to-calendar"></div>
                                </div>
                                </form>
                                
                               
                                <div class="row secondary-form">
                                    <h5>Your Details</h5><br><br>
                                </div>
                                <form method="POST" id="confirm-booking">
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>First Name <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <input class="form-control" type="text" name="first_name" id="first_name" value="<?php echo $firstName ? : '';?>" placeholder="First Name" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Surname <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <input class="form-control" type="text" name="surname" id="surname" value="<?php echo $surname ? : '';?>" placeholder="Surname" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Adults <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <input class="form-control" type="number" name="adults"  min="0" id="adults" value="<?php echo ($adults!="") ? $adults : '';?>" placeholder="Adults" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Children <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <input class="form-control" type="number" name="children" id="children" min="0" value="<?php echo ($children!="") ? $children : '';?>" placeholder="Children" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Address <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <textarea class="form-control" name="address" id="address" required placeholder="Address"><?php echo $address ? : '';?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Suburb <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <input class="form-control" type="text" name="suburb" id="suburb" value="<?php echo $suburb ? : '';?>" placeholder="Suburb" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>State <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <input class="form-control" type="text" name="state" id="state" value="<?php echo $state ? : '';?>" placeholder="State" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Country <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <select class="form-control" name="country_val" id="country_val" required>
                                                <option value="" selected disabled hidden>Select Country</option>
                                                <option value="AF" data-phone-code="+93">Afghanistan</option>
                                                <option value="AX" data-phone-code="+358">Åland Islands</option>
                                                <option value="AL" data-phone-code="+355">Albania</option>
                                                <option value="DZ" data-phone-code="+213">Algeria</option>
                                                <option value="AS" data-phone-code="+1-684">American Samoa</option>
                                                <option value="AD" data-phone-code="+376">Andorra</option>
                                                <option value="AO" data-phone-code="+244">Angola</option>
                                                <option value="AI" data-phone-code="+1-264">Anguilla</option>
                                                <option value="AQ" data-phone-code="+672">Antarctica</option>
                                                <option value="AG" data-phone-code="+1-268">Antigua and Barbuda</option>
                                                <option value="AR" data-phone-code="+54">Argentina</option>
                                                <option value="AM" data-phone-code="+374">Armenia</option>
                                                <option value="AW" data-phone-code="+297">Aruba</option>
                                                <option value="AU" data-phone-code="+61">Australia</option>
                                                <option value="AT" data-phone-code="+43">Austria</option>
                                                <option value="AZ" data-phone-code="+994">Azerbaijan</option>
                                                <option value="BS" data-phone-code="+1-242">Bahamas</option>
                                                <option value="BH" data-phone-code="+973">Bahrain</option>
                                                <option value="BD" data-phone-code="+880">Bangladesh</option>
                                                <option value="BB" data-phone-code="+1-246">Barbados</option>
                                                <option value="BY" data-phone-code="+375">Belarus</option>
                                                <option value="BE" data-phone-code="+32">Belgium</option>
                                                <option value="BZ" data-phone-code="+501">Belize</option>
                                                <option value="BJ" data-phone-code="+229">Benin</option>
                                                <option value="BM" data-phone-code="+1-441">Bermuda</option>
                                                <option value="BT" data-phone-code="+975">Bhutan</option>
                                                <option value="BO" data-phone-code="+591">Bolivia</option>
                                                <option value="BQ" data-phone-code="+599">Bonaire, Sint Eustatius and Saba</option>
                                                <option value="BA" data-phone-code="+387">Bosnia and Herzegovina</option>
                                                <option value="BW" data-phone-code="+267">Botswana</option>
                                                <option value="BV" data-phone-code="">Bouvet Island</option>
                                                <option value="BR" data-phone-code="+55">Brazil</option>
                                                <option value="IO" data-phone-code="+246">British Indian Ocean Territory</option>
                                                <option value="BN" data-phone-code="+673">Brunei Darussalam</option>
                                                <option value="BG" data-phone-code="+359">Bulgaria</option>
                                                <option value="BF" data-phone-code="+226">Burkina Faso</option>
                                                <option value="BI" data-phone-code="+257">Burundi</option>
                                                <option value="CV" data-phone-code="+238">Cabo Verde</option>
                                                <option value="KH" data-phone-code="+855">Cambodia</option>
                                                <option value="CM" data-phone-code="+237">Cameroon</option>
                                                <option value="CA" data-phone-code="+1">Canada</option>
                                                <option value="KY" data-phone-code="+1-345">Cayman Islands</option>
                                                <option value="CF" data-phone-code="+236">Central African Republic</option>
                                                <option value="TD" data-phone-code="+235">Chad</option>
                                                <option value="CL" data-phone-code="+56">Chile</option>
                                                <option value="CN" data-phone-code="+86">China</option>
                                                <option value="CX" data-phone-code="+61">Christmas Island</option>
                                                <option value="CC" data-phone-code="+61">Cocos (Keeling) Islands</option>
                                                <option value="CO" data-phone-code="+57">Colombia</option>
                                                <option value="KM" data-phone-code="+269">Comoros</option>
                                                <option value="CG" data-phone-code="+242">Congo</option>
                                                <option value="CD" data-phone-code="+243">Congo, Democratic Republic of the</option>
                                                <option value="CK" data-phone-code="+682">Cook Islands</option>
                                                <option value="CR" data-phone-code="+506">Costa Rica</option>
                                                <option value="CI" data-phone-code="+225">Côte d'Ivoire</option>
                                                <option value="HR" data-phone-code="+385">Croatia</option>
                                                <option value="CU" data-phone-code="+53">Cuba</option>
                                                <option value="CW" data-phone-code="+599">Curaçao</option>
                                                <option value="CY" data-phone-code="+357">Cyprus</option>
                                                <option value="CZ" data-phone-code="+420">Czech Republic</option>
                                                <option value="DK" data-phone-code="+45">Denmark</option>
                                                <option value="DJ" data-phone-code="+253">Djibouti</option>
                                                <option value="DM" data-phone-code="+1-767">Dominica</option>
                                                <option value="DO" data-phone-code="+1-809">Dominican Republic</option>
                                                <option value="EC" data-phone-code="+593">Ecuador</option>
                                                <option value="EG" data-phone-code="+20">Egypt</option>
                                                <option value="SV" data-phone-code="+503">El Salvador</option>
                                                <option value="GQ" data-phone-code="+240">Equatorial Guinea</option>
                                                <option value="ER" data-phone-code="+291">Eritrea</option>
                                                <option value="EE" data-phone-code="+372">Estonia</option>
                                                <option value="SZ" data-phone-code="+268">Eswatini</option>
                                                <option value="ET" data-phone-code="+251">Ethiopia</option>
                                                <option value="FK" data-phone-code="+500">Falkland Islands (Malvinas)</option>
                                                <option value="FO" data-phone-code="+298">Faroe Islands</option>
                                                <option value="FJ" data-phone-code="+679">Fiji</option>
                                                <option value="FI" data-phone-code="+358">Finland</option>
                                                <option value="FR" data-phone-code="+33">France</option>
                                                <option value="GF" data-phone-code="+594">French Guiana</option>
                                                <option value="PF" data-phone-code="+689">French Polynesia</option>
                                                <option value="TF" data-phone-code="">French Southern Territories</option>
                                                <option value="GA" data-phone-code="+241">Gabon</option>
                                                <option value="GM" data-phone-code="+220">Gambia</option>
                                                <option value="GE" data-phone-code="+995">Georgia</option>
                                                <option value="DE" data-phone-code="+49">Germany</option>
                                                <option value="GH" data-phone-code="+233">Ghana</option>
                                                <option value="GI" data-phone-code="+350">Gibraltar</option>
                                                <option value="GR" data-phone-code="+30">Greece</option>
                                                <option value="GL" data-phone-code="+299">Greenland</option>
                                                <option value="GD" data-phone-code="+1-473">Grenada</option>
                                                <option value="GP" data-phone-code="+590">Guadeloupe</option>
                                                <option value="GU" data-phone-code="+1-671">Guam</option>
                                                <option value="GT" data-phone-code="+502">Guatemala</option>
                                                <option value="GG" data-phone-code="+44">Guernsey</option>
                                                <option value="GN" data-phone-code="+224">Guinea</option>
                                                <option value="GW" data-phone-code="+245">Guinea-Bissau</option>
                                                <option value="GY" data-phone-code="+592">Guyana</option>
                                                <option value="HT" data-phone-code="+509">Haiti</option>
                                                <option value="HM" data-phone-code="">Heard Island and McDonald Islands</option>
                                                <option value="VA" data-phone-code="+39">Holy See</option>
                                                <option value="HN" data-phone-code="+504">Honduras</option>
                                                <option value="HK" data-phone-code="+852">Hong Kong</option>
                                                <option value="HU" data-phone-code="+36">Hungary</option>
                                                <option value="IS" data-phone-code="+354">Iceland</option>
                                                <option value="IN" data-phone-code="+91">India</option>
                                                <option value="ID" data-phone-code="+62">Indonesia</option>
                                                <option value="IR" data-phone-code="+98">Iran</option>
                                                <option value="IQ" data-phone-code="+964">Iraq</option>
                                                <option value="IE" data-phone-code="+353">Ireland</option>
                                                <option value="IM" data-phone-code="+44">Isle of Man</option>
                                                <option value="IL" data-phone-code="+972">Israel</option>
                                                <option value="IT" data-phone-code="+39">Italy</option>
                                                <option value="JM" data-phone-code="+1-876">Jamaica</option>
                                                <option value="JP" data-phone-code="+81">Japan</option>
                                                <option value="JE" data-phone-code="+44">Jersey</option>
                                                <option value="JO" data-phone-code="+962">Jordan</option>
                                                <option value="KZ" data-phone-code="+7">Kazakhstan</option>
                                                <option value="KE" data-phone-code="+254">Kenya</option>
                                                <option value="KI" data-phone-code="+686">Kiribati</option>
                                                <option value="KP" data-phone-code="+850">Korea (Democratic People's Republic of)</option>
                                                <option value="KR" data-phone-code="+82">Korea (Republic of)</option>
                                                <option value="KW" data-phone-code="+965">Kuwait</option>
                                                <option value="KG" data-phone-code="+996">Kyrgyzstan</option>
                                                <option value="LA" data-phone-code="+856">Lao People's Democratic Republic</option>
                                                <option value="LV" data-phone-code="+371">Latvia</option>
                                                <option value="LB" data-phone-code="+961">Lebanon</option>
                                                <option value="LS" data-phone-code="+266">Lesotho</option>
                                                <option value="LR" data-phone-code="+231">Liberia</option>
                                                <option value="LY" data-phone-code="+218">Libya</option>
                                                <option value="LI" data-phone-code="+423">Liechtenstein</option>
                                                <option value="LT" data-phone-code="+370">Lithuania</option>
                                                <option value="LU" data-phone-code="+352">Luxembourg</option>
                                                <option value="MO" data-phone-code="+853">Macao</option>
                                                <option value="MG" data-phone-code="+261">Madagascar</option>
                                                <option value="MW" data-phone-code="+265">Malawi</option>
                                                <option value="MY" data-phone-code="+60">Malaysia</option>
                                                <option value="MV" data-phone-code="+960">Maldives</option>
                                                <option value="ML" data-phone-code="+223">Mali</option>
                                                <option value="MT" data-phone-code="+356">Malta</option>
                                                <option value="MH" data-phone-code="+692">Marshall Islands</option>
                                                <option value="MQ" data-phone-code="+596">Martinique</option>
                                                <option value="MR" data-phone-code="+222">Mauritania</option>
                                                <option value="MU" data-phone-code="+230">Mauritius</option>
                                                <option value="YT" data-phone-code="+262">Mayotte</option>
                                                <option value="MX" data-phone-code="+52">Mexico</option>
                                                <option value="FM" data-phone-code="+691">Micronesia (Federated States of)</option>
                                                <option value="MD" data-phone-code="+373">Moldova</option>
                                                <option value="MC" data-phone-code="+377">Monaco</option>
                                                <option value="MN" data-phone-code="+976">Mongolia</option>
                                                <option value="ME" data-phone-code="+382">Montenegro</option>
                                                <option value="MS" data-phone-code="+1-664">Montserrat</option>
                                                <option value="MA" data-phone-code="+212">Morocco</option>
                                                <option value="MZ" data-phone-code="+258">Mozambique</option>
                                                <option value="MM" data-phone-code="+95">Myanmar</option>
                                                <option value="NA" data-phone-code="+264">Namibia</option>
                                                <option value="NR" data-phone-code="+674">Nauru</option>
                                                <option value="NP" data-phone-code="+977">Nepal</option>
                                                <option value="NL" data-phone-code="+31">Netherlands</option>
                                                <option value="NC" data-phone-code="+687">New Caledonia</option>
                                                <option value="NZ" data-phone-code="+64">New Zealand</option>
                                                <option value="NI" data-phone-code="+505">Nicaragua</option>
                                                <option value="NE" data-phone-code="+227">Niger</option>
                                                <option value="NG" data-phone-code="+234">Nigeria</option>
                                                <option value="NU" data-phone-code="+683">Niue</option>
                                                <option value="NF" data-phone-code="+672">Norfolk Island</option>
                                                <option value="MK" data-phone-code="+389">North Macedonia</option>
                                                <option value="MP" data-phone-code="+1">Northern Mariana Islands</option>
                                                <option value="NO" data-phone-code="+47">Norway</option>
                                                <option value="OM" data-phone-code="+968">Oman</option>
                                                <option value="PK" data-phone-code="+92">Pakistan</option>
                                                <option value="PW" data-phone-code="+680">Palau</option>
                                                <option value="PS" data-phone-code="+970">Palestine</option>
                                                <option value="PA" data-phone-code="+507">Panama</option>
                                                <option value="PG" data-phone-code="+675">Papua New Guinea</option>
                                                <option value="PY" data-phone-code="+595">Paraguay</option>
                                                <option value="PE" data-phone-code="+51">Peru</option>
                                                <option value="PH" data-phone-code="+63">Philippines</option>
                                                <option value="PN" data-phone-code="+64">Pitcairn</option>
                                                <option value="PL" data-phone-code="+48">Poland</option>
                                                <option value="PT" data-phone-code="+351">Portugal</option>
                                                <option value="PR" data-phone-code="+1">Puerto Rico</option>
                                                <option value="QA" data-phone-code="+974">Qatar</option>
                                                <option value="RE" data-phone-code="+262">Réunion</option>
                                                <option value="RO" data-phone-code="+40">Romania</option>
                                                <option value="RU" data-phone-code="+7">Russian Federation</option>
                                                <option value="RW" data-phone-code="+250">Rwanda</option>
                                                <option value="BL" data-phone-code="+590">Saint Barthélemy</option>
                                                <option value="SH" data-phone-code="+290">Saint Helena, Ascension and Tristan da Cunha</option>
                                                <option value="KN" data-phone-code="+1">Saint Kitts and Nevis</option>
                                                <option value="LC" data-phone-code="+1">Saint Lucia</option>
                                                <option value="MF" data-phone-code="+590">Saint Martin (French part)</option>
                                                <option value="PM" data-phone-code="+508">Saint Pierre and Miquelon</option>
                                                <option value="VC" data-phone-code="+1">Saint Vincent and the Grenadines</option>
                                                <option value="WS" data-phone-code="+685">Samoa</option>
                                                <option value="SM" data-phone-code="+378">San Marino</option>
                                                <option value="ST" data-phone-code="+239">Sao Tome and Principe</option>
                                                <option value="SA" data-phone-code="+966">Saudi Arabia</option>
                                                <option value="SN" data-phone-code="+221">Senegal</option>
                                                <option value="RS" data-phone-code="+381">Serbia</option>
                                                <option value="SC" data-phone-code="+248">Seychelles</option>
                                                <option value="SL" data-phone-code="+232">Sierra Leone</option>
                                                <option value="SG" data-phone-code="+65">Singapore</option>
                                                <option value="SX" data-phone-code="+1">Sint Maarten (Dutch part)</option>
                                                <option value="SK" data-phone-code="+421">Slovakia</option>
                                                <option value="SI" data-phone-code="+386">Slovenia</option>
                                                <option value="SB" data-phone-code="+677">Solomon Islands</option>
                                                <option value="SO" data-phone-code="+252">Somalia</option>
                                                <option value="ZA" data-phone-code="+27">South Africa</option>
                                                <option value="GS" data-phone-code="+500">South Georgia and the South Sandwich Islands</option>
                                                <option value="SS" data-phone-code="+211">South Sudan</option>
                                                <option value="ES" data-phone-code="+34">Spain</option>
                                                <option value="LK" data-phone-code="+94">Sri Lanka</option>
                                                <option value="SD" data-phone-code="+249">Sudan</option>
                                                <option value="SR" data-phone-code="+597">Suriname</option>
                                                <option value="SJ" data-phone-code="+47">Svalbard and Jan Mayen</option>
                                                <option value="SE" data-phone-code="+46">Sweden</option>
                                                <option value="CH" data-phone-code="+41">Switzerland</option>
                                                <option value="SY" data-phone-code="+963">Syrian Arab Republic</option>
                                                <option value="TW" data-phone-code="+886">Taiwan</option>
                                                <option value="TJ" data-phone-code="+992">Tajikistan</option>
                                                <option value="TZ" data-phone-code="+255">Tanzania</option>
                                                <option value="TH" data-phone-code="+66">Thailand</option>
                                                <option value="TL" data-phone-code="+670">Timor-Leste</option>
                                                <option value="TG" data-phone-code="+228">Togo</option>
                                                <option value="TK" data-phone-code="+690">Tokelau</option>
                                                <option value="TO" data-phone-code="+676">Tonga</option>
                                                <option value="TT" data-phone-code="+1">Trinidad and Tobago</option>
                                                <option value="TN" data-phone-code="+216">Tunisia</option>
                                                <option value="TR" data-phone-code="+90">Turkey</option>
                                                <option value="TM" data-phone-code="+993">Turkmenistan</option>
                                                <option value="TV" data-phone-code="+688">Tuvalu</option>
                                                <option value="UG" data-phone-code="+256">Uganda</option>
                                                <option value="UA" data-phone-code="+380">Ukraine</option>
                                                <option value="AE" data-phone-code="+971">United Arab Emirates</option>
                                                <option value="GB" data-phone-code="+44">United Kingdom</option>
                                                <option value="US" data-phone-code="+1">United States of America</option>
                                                <option value="UY" data-phone-code="+598">Uruguay</option>
                                                <option value="UZ" data-phone-code="+998">Uzbekistan</option>
                                                <option value="VU" data-phone-code="+678">Vanuatu</option>
                                                <option value="VE" data-phone-code="+58">Venezuela</option>
                                                <option value="VN" data-phone-code="+84">Viet Nam</option>
                                                <option value="EH" data-phone-code="+212">Western Sahara</option>
                                                <option value="YE" data-phone-code="+967">Yemen</option>
                                                <option value="ZM" data-phone-code="+260">Zambia</option>
                                                <option value="ZW" data-phone-code="+263">Zimbabwe</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Postcode <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <input class="form-control" type="text" name="postcode" id="postcode" value="<?php echo $postcode ? : '';?>" placeholder="Postcode" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Mobile <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <input class="form-control" type="text" name="mobile" id="mobile" value="<?php echo $mobile ? : '';?>" placeholder="Mobile" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label>Email <strong style="color:red">*</strong></label><br/>
                                        </div>
                                        <div class="col-md-9 form-group">
                                            <input class="form-control" type="email" name="email" id="email" value="<?php echo $email ? : '';?>" placeholder="Email" required>
                                        </div>
                                    </div>
                                   <div class="row">
                                        <div class="col-xl-12 left-aside mb-5 single-villa">
                                            <hr/>
                                            <label for="accept-terms"  style="color:black" >
                                                <input type="checkbox" name="accept-terms" id="accept-terms" value="1" required /> 
                                                I agree to the <a href="<?php echo home_url('/terms-of-booking'); ?>"  style="color:black" target="_blank">Terms and Conditions</a>
                                            </label>
                                        </div>
                                        <div class="col-xl-12 left-aside mb-5 single-villa">
                                            <button type="button" class="btn btn-success" id="show-hide-terms">
                                                <i class="fa fa-eye icon"></i>
                                                View
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row" id="terms-and-conditions-block">
                                        <div class="col-xl-12 left-aside mb-5 single-villa">
                                        <?php
                                        if (is_object($standardText['TEXT'])) { // protect against a NULL LOB
                                            $standard_terms_text = $standardText['TEXT']->load();
                                            $standardText['TEXT']->free();
                                            echo htmlspecialchars_decode($standard_terms_text);
                                		}
                                		?>
                                		</div>
                            		</div>
                            		 <div class="row">
                                        <div class="col-xl-12 left-aside mb-5 single-villa">
                                            <h5>Payment Option</h5>
                                            <hr/>
                                            <div class="row payment-options">
                                                <div class="col-md-8">
                                                    
                                                    <?php $depo=0; if( isset($villa_pricing["o_deposit"]) && $villa_pricing["o_deposit"] ) {
                                                    $depo=1;
                                                    ?>
                                                    <div class="d-flex">
                                                        <input type="radio" id="payment_options_1" class="payment_options" checked name="amount" value="<?php echo $villa_pricing["o_deposit"]; ?>" required>
                                                        <label class="cash" for="payment_options_1">Pay Deposite Only <?php echo $csymbol." ".number_format($villa_pricing["o_deposit"], 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?></label>
                                                        <label class="bank" style="display:none;" for="payment_options_1">Pay Deposite Only <?php echo $csymbol." ".number_format($villa_pricing["o_deposit"]+20, 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?></label>
                                                    </div>
                                                    <?php } ?>
                                                    <?php if( isset($villa_pricing["o_pay_full"]) && $villa_pricing["o_pay_full"] ) { ?>
                                                    <div class="d-flex" style="gap:5px">
                                                        <input type="radio" id="payment_options_2" class="payment_options" name="amount"  <?php if($depo==0){echo "checked";}?> value="<?php echo $villa_pricing["o_pay_full"]; ?>" required>
                                                        <label class="cash" for="payment_options_2">Pay in Full <?php echo $csymbol." ". number_format($villa_pricing["o_pay_full"], 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?></label>
                                                        <label class="bank" style="display:none;" for="payment_options_2">Pay in Full <?php echo $csymbol." ".number_format($villa_pricing["o_pay_full"]+20, 2, '.', ',') . " " . $villa_pricing["o_currency"]; ?></label>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                    
                                            </div>
                                        </div>
                                    </div>
                        		    <div style="display:none;" class="row payment-methods">
                                        <div class="col-xl-12 left-aside mb-5 single-villa">
                                            <h5>Pay By</h5>
                                            <hr/>
                                            <div class="row ">
                                                <div class="col-md-4 col">
                                                <input type="radio" id="payment_credit" class="payment_method" name="payment_method" checked value="master_credit" required checked>
                                                    <button style="width: 150px;" type="button" class="btn btn-default payment-button">
                                                         <label for="payment_credit">
                                                            <svg width="100px" height="100px" viewBox="0 -222 2000 2000" id="Laag_1" data-name="Laag 1" xmlns="http://www.w3.org/2000/svg">
                                                                <defs><style>.cls-1{fill:#231f20;}.cls-2{fill:#f79410;}.cls-3{fill:#ff5f00;}.cls-4{fill:#eb001b;}.cls-5{fill:#f79e1b;}</style></defs><title>Tekengebied 1</title><path class="cls-1" d="M1960.59,1534.9v4h3.73a3.93,3.93,0,0,0,2-.51,1.78,1.78,0,0,0,.75-1.53,1.71,1.71,0,0,0-.75-1.49,3.59,3.59,0,0,0-2-.51h-3.73Zm3.77-2.83a6.92,6.92,0,0,1,4.48,1.3,4.3,4.3,0,0,1,1.57,3.54,4.06,4.06,0,0,1-1.26,3.11,6.14,6.14,0,0,1-3.58,1.49l5,5.7h-3.85l-4.6-5.66h-1.49v5.66h-3.22v-15.13h7Zm-1,20.36a12,12,0,0,0,4.91-1,12.86,12.86,0,0,0,4-2.71,12.63,12.63,0,0,0,2.71-4,12.94,12.94,0,0,0,0-9.9,13.07,13.07,0,0,0-2.71-4,12.89,12.89,0,0,0-4-2.71,12.59,12.59,0,0,0-4.91-.94,13.12,13.12,0,0,0-5,.94,12.77,12.77,0,0,0-4.09,2.71,12.92,12.92,0,0,0-2.67,14,11.92,11.92,0,0,0,2.67,4,12.81,12.81,0,0,0,4.09,2.71,12.45,12.45,0,0,0,5,1m0-29a16.74,16.74,0,0,1,11.75,4.8,16,16,0,0,1,3.54,5.19,16.09,16.09,0,0,1,0,12.65,16.88,16.88,0,0,1-3.54,5.19,17.85,17.85,0,0,1-5.27,3.5,16.33,16.33,0,0,1-6.48,1.3,16.6,16.6,0,0,1-6.56-1.3,17.08,17.08,0,0,1-5.31-3.5A16.88,16.88,0,0,1,1948,1546a16.09,16.09,0,0,1,0-12.65,16,16,0,0,1,3.54-5.19,15.8,15.8,0,0,1,5.31-3.5,16.6,16.6,0,0,1,6.56-1.3M432.16,1465.1c0-28.85,18.9-52.55,49.79-52.55,29.52,0,49.44,22.68,49.44,52.55s-19.92,52.55-49.44,52.55c-30.89,0-49.79-23.7-49.79-52.55m132.88,0V1383H529.35V1403c-11.32-14.78-28.49-24.05-51.84-24.05-46,0-82.1,36.08-82.1,86.19s36.08,86.19,82.1,86.19c23.34,0,40.52-9.28,51.84-24.05v19.93H565V1465.1Zm1205.92,0c0-28.85,18.9-52.55,49.8-52.55,29.55,0,49.44,22.68,49.44,52.55s-19.89,52.55-49.44,52.55c-30.89,0-49.8-23.7-49.8-52.55m132.92,0v-148h-35.72V1403c-11.32-14.78-28.49-24.05-51.84-24.05-46,0-82.1,36.08-82.1,86.19s36.08,86.19,82.1,86.19c23.35,0,40.52-9.28,51.84-24.05v19.93h35.72V1465.1ZM1008,1410.86c23,0,37.77,14.42,41.54,39.81H964.38c3.81-23.7,18.2-39.81,43.63-39.81m.71-32c-48.1,0-81.75,35-81.75,86.19,0,52.19,35,86.19,84.14,86.19,24.72,0,47.36-6.17,67.28-23l-17.49-26.45c-13.76,11-31.28,17.17-47.75,17.17-23,0-43.94-10.65-49.09-40.2h121.87c.35-4.44.71-8.92.71-13.72-.36-51.17-32-86.19-77.94-86.19m430.9,86.19c0-28.85,18.9-52.55,49.79-52.55,29.52,0,49.44,22.68,49.44,52.55s-19.92,52.55-49.44,52.55c-30.89,0-49.8-23.7-49.8-52.55m132.88,0V1383H1536.8V1403c-11.36-14.78-28.49-24.05-51.84-24.05-46,0-82.1,36.08-82.1,86.19s36.08,86.19,82.1,86.19c23.35,0,40.48-9.28,51.84-24.05v19.93h35.68V1465.1Zm-334.42,0c0,49.79,34.66,86.19,87.56,86.19,24.72,0,41.19-5.5,59-19.57l-17.14-28.85c-13.4,9.63-27.47,14.78-43,14.78-28.49-.35-49.44-20.95-49.44-52.55s20.95-52.19,49.44-52.55c15.49,0,29.56,5.15,43,14.78l17.14-28.85c-17.84-14.07-34.31-19.57-59-19.57-52.9,0-87.56,36.39-87.56,86.19m460.1-86.19c-20.59,0-34,9.63-43.27,24.05V1383h-35.37v164.12h35.73v-92c0-27.16,11.67-42.25,35-42.25a57.87,57.87,0,0,1,22.32,4.13l11-33.64c-7.9-3.11-18.2-4.48-25.43-4.48m-956.64,17.17c-17.17-11.32-40.83-17.17-66.93-17.17-41.58,0-68.35,19.93-68.35,52.54,0,26.76,19.93,43.27,56.63,48.42l16.86,2.4c19.57,2.75,28.81,7.9,28.81,17.17,0,12.69-13,19.93-37.41,19.93-24.72,0-42.56-7.9-54.59-17.17L599.74,1530c19.57,14.42,44.29,21.3,71.06,21.3,47.4,0,74.87-22.32,74.87-53.57,0-28.85-21.62-43.94-57.34-49.09l-16.82-2.44c-15.45-2-27.83-5.11-27.83-16.11,0-12,11.67-19.22,31.25-19.22,20.95,0,41.23,7.9,51.17,14.07l15.45-28.85ZM1202,1378.91c-20.59,0-34,9.63-43.23,24.05V1383h-35.37v164.12h35.69v-92c0-27.16,11.67-42.25,35-42.25a57.87,57.87,0,0,1,22.32,4.13l11-33.64c-7.9-3.11-18.2-4.48-25.43-4.48M897.44,1383H839.08v-49.79H803V1383H769.71v32.62H803v74.87c0,38.08,14.78,60.76,57,60.76,15.49,0,33.33-4.8,44.65-12.69L894.34,1508c-10.65,6.17-22.32,9.28-31.6,9.28-17.84,0-23.66-11-23.66-27.47v-74.16h58.36ZM363.85,1547.16v-103c0-38.79-24.72-64.89-64.57-65.24-20.95-.35-42.56,6.17-57.69,29.2-11.32-18.2-29.16-29.2-54.24-29.2-17.53,0-34.66,5.15-48.07,24.37V1383H103.56v164.12h36v-91c0-28.49,15.8-43.63,40.2-43.63,23.7,0,35.69,15.45,35.69,43.27v91.34h36.08v-91c0-28.49,16.47-43.63,40.16-43.63,24.37,0,36,15.45,36,43.27v91.34Z"/><path class="cls-2" d="M1980.94,1001.22v-24h-6.25l-7.23,16.47-7.19-16.47H1954v24h4.44V983.14l6.76,15.6h4.6l6.76-15.64v18.12h4.4Zm-39.65,0V981.33h8v-4.05h-20.44v4.05h8v19.89h4.4Z"/><path class="cls-3" d="M1270.57,1104.15H729.71v-972h540.87Z"/><path class="cls-4" d="M764,618.17c0-197.17,92.32-372.81,236.08-486A615.46,615.46,0,0,0,618.09,0C276.72,0,0,276.76,0,618.17s276.72,618.17,618.09,618.17a615.46,615.46,0,0,0,382-132.17C856.34,991,764,815.35,764,618.17"/><path class="cls-5" d="M2000.25,618.17c0,341.41-276.72,618.17-618.09,618.17a615.65,615.65,0,0,1-382.05-132.17c143.8-113.19,236.12-288.82,236.12-486s-92.32-372.81-236.12-486A615.65,615.65,0,0,1,1382.15,0c341.37,0,618.09,276.76,618.09,618.17"/></svg>
                                                            <img src="https://wptest.villagetaways.com/wp-content/uploads/2024/08/visa-master.png" style="padding:15px 0;">
                                                            <br/>
                                                            Credit Card
                                                        </label>
                                                    </button>
                                                </div>
                                                <div class="col-md-4 col">
                                                    <input type="radio" id="payment_wire" class="payment_method" name="payment_method"  value="wire_transfer" >
                                                    <button style="width: 150px;" type="button" class="btn btn-default payment-button">
                                                        <label for="payment_wire">
                                                            <svg height="100px" viewBox=".45 -27.27099277 511.984 500.99959849" width="100px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><linearGradient id="a"><stop offset="0" stop-color="#41e700" stop-opacity="0"/><stop offset=".736" stop-color="#3acd00"/><stop offset=".781" stop-color="#36be00"/><stop offset=".926" stop-color="#299100"/><stop offset="1" stop-color="#248000"/></linearGradient><linearGradient id="b" gradientUnits="userSpaceOnUse" x1="47.507" x2="154.622" xlink:href="#a" y1="186.72" y2="186.72"/><linearGradient id="c" gradientUnits="userSpaceOnUse" x1="47.507" x2="154.622" xlink:href="#a" y1="219.718" y2="219.718"/><linearGradient id="d" gradientUnits="userSpaceOnUse" x1="47.507" x2="154.622" xlink:href="#a" y1="255.717" y2="255.717"/><path d="m482.308 102.531c13.854 0 25.126 11.271 25.126 25.126v257.9c0 13.854-11.271 25.126-25.126 25.126h-451.732c-13.854 0-25.126-11.271-25.126-25.126v-257.9c0-13.854 11.271-25.126 25.126-25.126zm0-5h-451.732c-16.638 0-30.126 13.488-30.126 30.126v257.9c0 16.639 13.488 30.126 30.126 30.126h451.732c16.639 0 30.126-13.487 30.126-30.126v-257.9c0-16.638-13.488-30.126-30.126-30.126z" fill="#00b5e5"/><g fill="#00afd8"><path d="m60.117 334.916v-45.39h-13.317v-14.048h42.833v14.048h-13.317v45.39zm37.433 0v-59.438h16.93c6.631 0 11.252.305 13.864.915 2.612.608 4.866 1.63 6.76 3.064 2.139 1.623 3.783 3.694 4.933 6.211 1.15 2.518 1.726 5.293 1.726 8.323 0 4.602-1.13 8.344-3.39 11.226-2.261 2.883-5.556 4.797-9.887 5.746l16.199 23.952h-18.31l-13.642-23.264v23.264h-15.183zm15.184-31.342h3.004c3.492 0 6.042-.595 7.653-1.787 1.61-1.19 2.416-3.058 2.416-5.604 0-2.978-.75-5.094-2.253-6.353-1.501-1.258-4.026-1.888-7.572-1.888h-3.248z"/><path d="m143.874 334.916 20.503-59.438h20.3l20.543 59.438h-16.119l-3.004-10.637h-23.02l-3.085 10.637zm22.695-22.208h16.118l-6.049-19.61c-.189-.594-.446-1.556-.771-2.882-.325-1.325-.744-3.03-1.258-5.115a324.03 324.03 0 0 1 -1.035 4.182 125.867 125.867 0 0 1 -1.036 3.815zm44.335 22.208v-59.438h15.63l22.451 30.572c.434.623 1.145 1.874 2.132 3.755s2.078 4.122 3.269 6.72a124.203 124.203 0 0 1 -.71-6.578 67.026 67.026 0 0 1 -.224-5.115v-29.354h15.55v59.438h-15.55l-22.451-30.692c-.461-.623-1.186-1.874-2.172-3.756-.988-1.881-2.064-4.094-3.229-6.638.325 2.491.562 4.695.711 6.618a67.5 67.5 0 0 1 .223 5.115v29.353zm73.485-19.203c2.41 2.735 4.783 4.777 7.125 6.131 2.341 1.354 4.676 2.03 7.005 2.03 2.165 0 3.944-.582 5.338-1.747 1.394-1.163 2.091-2.639 2.091-4.425 0-1.975-.603-3.498-1.806-4.566-1.206-1.07-3.933-2.172-8.182-3.31-5.818-1.568-9.934-3.614-12.342-6.131-2.41-2.517-3.613-5.967-3.613-10.354 0-5.684 1.901-10.317 5.705-13.904 3.802-3.586 8.734-5.38 14.798-5.38 3.274 0 6.38.44 9.317 1.319 2.937.88 5.718 2.213 8.343 3.999l-5.075 11.652c-1.84-1.568-3.729-2.754-5.663-3.553-1.936-.797-3.851-1.197-5.745-1.197-1.948 0-3.532.468-4.75 1.401s-1.827 2.131-1.827 3.592c0 1.488.534 2.681 1.604 3.573s3.134 1.746 6.191 2.559l.73.202c6.604 1.787 10.949 3.749 13.033 5.887 1.406 1.463 2.477 3.215 3.207 5.258.73 2.044 1.097 4.311 1.097 6.8 0 6.309-2.07 11.376-6.213 15.207-4.141 3.828-9.662 5.743-16.563 5.743-4.141 0-7.91-.704-11.308-2.111s-6.664-3.613-9.806-6.617zm45.674 19.203v-59.438h36.418v13.033h-20.625v10.312h19.448v12.749h-19.448v23.344zm45.228 0v-59.438h36.418v13.033h-20.624v10.312h19.448v12.749h-19.448v10.028h20.624v13.315h-36.418zm47.136 0v-59.438h16.931c6.632 0 11.252.305 13.864.915 2.612.608 4.866 1.63 6.761 3.064 2.139 1.623 3.781 3.694 4.933 6.211 1.15 2.518 1.726 5.293 1.726 8.323 0 4.602-1.131 8.344-3.39 11.226-2.261 2.883-5.555 4.797-9.886 5.746l16.198 23.952h-18.31l-13.642-23.264v23.264h-15.185zm15.185-31.342h3.005c3.491 0 6.042-.595 7.651-1.787 1.61-1.19 2.416-3.058 2.416-5.604 0-2.978-.751-5.094-2.253-6.353-1.501-1.258-4.026-1.888-7.571-1.888h-3.248z"/></g><path d="m232.585 178.848 10.789 40.816c.509 1.838.978 3.753 1.408 5.747s.879 4.281 1.349 6.861c.587-3.05 1.085-5.561 1.495-7.536.41-1.974.811-3.666 1.202-5.072l10.086-40.816h23.635l-24.279 85.855h-21.697l-10.615-36.653c-.392-1.291-1.036-3.812-1.935-7.565-.392-1.68-.704-2.991-.938-3.929-.196.821-.469 1.975-.821 3.46-.899 3.792-1.583 6.471-2.052 8.034l-10.439 36.653h-21.757l-24.22-85.855h23.633l9.852 41.05c.469 2.112.929 4.223 1.378 6.334s.87 4.281 1.261 6.509a378.42 378.42 0 0 1 1.144-5.161 473.98 473.98 0 0 1 1.847-7.683l10.791-41.05h18.883zm58.643 85.855v-85.855h23.634v85.855zm42.517 0v-85.855h24.454c9.577 0 16.253.439 20.026 1.319 3.773.879 7.027 2.356 9.765 4.427 3.088 2.346 5.463 5.337 7.125 8.973s2.492 7.644 2.492 12.022c0 6.647-1.632 12.051-4.896 16.215-3.265 4.164-8.024 6.929-14.279 8.298l23.399 34.601h-26.449l-19.704-33.603v33.604h-21.933zm21.933-45.273h4.339c5.044 0 8.728-.86 11.055-2.58 2.326-1.72 3.489-4.418 3.489-8.093 0-4.3-1.084-7.36-3.255-9.178-2.17-1.817-5.815-2.727-10.938-2.727h-4.69zm55.066 45.273v-85.855h52.604v18.825h-29.791v14.896h28.09v18.414h-28.09v14.485h29.791v19.235z" fill="#006e90"/><path d="m47.507 178.22h107.115v17h-107.115z" fill="url(#b)"/><path d="m47.507 211.218h107.115v17h-107.115z" fill="url(#c)"/><path d="m47.507 247.217h107.115v17h-107.115z" fill="url(#d)"/></svg>
                                                            <br/>
                                                            TT / Wire Tranfer
                                                        </label>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                    
                                    </div>
                                    <input class="form-control" type="hidden" name="night" id="night" value="<?php echo $numberOfNights;?>">
                                    <input class="form-control" type="hidden" name="villaprice" id="villaprice" value="<?php echo number_format($villa_pricing["o_night_rate"], 2, '.', ',');?>">
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <br/>
                                            <button style="width: 100%;" id="book-villa" type="submit" class="btn btn-success" <?php if(!$show_details){ echo "disabled";}?>>BOOK NOW</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <form id="wire_transfer_form" action="" method="post">
                                <input type="hidden" name="currency" value="<?php echo ($villa_pricing["o_currency"]) ? $villa_pricing["o_currency"] : "USD"; ?>">
                                <input type="hidden" name="description" value="<?php echo $villa_details["LOCATION_NAME"]; ?> Villa <?php echo $villa_details["VG_NUMBER"]; ?>">
                                <input type="hidden" id="wire_transfer_amount" name="camount" value="">
                                <input type="hidden" name="email" value="">
                                <input type="hidden" name="villaid" value="<?php echo $villa_id; ?>">
                                <input type="hidden" name="clientid" value="<?php echo $client_id; ?>">
                                <input type="hidden" name="villaprice" value="<?php echo number_format($villa_pricing["o_night_rate"], 2, '.', ',');?>">
                                <input type="hidden" name="num_nights" value="<?php echo $numberOfNights;?>">
                                <input type="hidden" name="email_2" id="email_2" value="">
                                <input type="hidden" name="paymentoption" value="wire_transfer">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="dest-detail-s">
    <!--<div class="container p-0">-->
       
        
      
    </div>
</div>

<div class="dest-detail-s">
    <div class="container p-0">
      
    </div>
</div>

<!-- ./ End Main -->

<script type="text/javascript">
// { dateFormat: "yy/dd/mm" }
    $(document).ready(function() {
        //  $("#arrival").datepicker({
        //     dateFormat: "dd-mm-yy",
        //     onSelect: function(dateText) {
        //     let toDateString = getThreeDayFromToday(dateText);
        //     $("#departure").datepicker("option", "minDate", dateText);
        //     $("#departure").val(toDateString);
        //     }
        // });
            // });
        $(document).on('click','#show-hide-terms',function(){
            var $icon = $(this).find('.icon');
            var isShow = $icon.hasClass('fa-eye');

            if (isShow) {
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                $(this).contents().last()[0].textContent = ' Hide';
            } else {
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                $(this).contents().last()[0].textContent = ' View';
            }
            $('#terms-and-conditions-block').slideToggle(500);
        });
        function getUrlVars(){
                var vars = [], hash;
                var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                for(var i = 0; i < hashes.length; i++)
                {
                    hash = hashes[i].split('=');
                    vars.push(hash[0]);
                    vars[hash[0]] = hash[1];
                }
                return vars;
        }
        let selectedCountry = getUrlVars()["country"];
        if(selectedCountry) {
            $("#country_val").val(selectedCountry);
        }
        selectedRooms = getUrlVars()["NoOfRooms"];
        if(selectedRooms){
         $("#nr_of_rooms").val(selectedRooms);   
        }
        $("form#booking").submit(function(){
            event.preventDefault();
            let arrivalDate = jQuery("form#booking").find("#arrival").val();
            let departureDate = jQuery("form#booking").find("#departure").val();
            let NoOfRooms = jQuery("form#booking").find("#nr_of_rooms").val();
            let vgNumber = jQuery("form#booking").find("#vg_number").val();
             window.location = '/villa-booking-'+vgNumber+'?arrivalDate=' + arrivalDate + '&departureDate=' +departureDate +
            '&NoOfRooms='+NoOfRooms;
        });
        $("form#confirm-booking").submit(function(){
            let paymentOption = jQuery(this).find(".payment_options:checked").val();
            let paymentMethod = jQuery(this).find(".payment_method:checked").val();
        });
       
         $('#payment_credit').on('click', function(event) {
              $('.row.cardform').addClass('active');
              $('tr.cash').show();
              $('tr.bank').hide();
               $('label.cash').show();
               $('label.bank').hide();
         });
         $('#payment_wire').on('click', function(event) {
              $('.row.cardform').removeClass('active');
               $('tr.cash').hide();
               $('tr.bank').show();
                $('label.cash').hide();
               $('label.bank').show();
         });
        $('#confirm-booking').on('submit', function(event) {
            event.preventDefault();
            if( $('input[name="accept-terms"]').val() && $('input[name="payment_method"]').val() == 'master_credit' ) {
                let firstName = jQuery("form#confirm-booking").find("#first_name").val();
                let lastName = jQuery("form#confirm-booking").find("#surname").val();
                let address_line1 = jQuery("form#confirm-booking").find("#address").val();
                let address_city = jQuery("form#confirm-booking").find("#suburb").val();
                let address_state = jQuery("form#confirm-booking").find("#state").val();
                //let address_country = jQuery("form#confirm-booking").find("#country_val").text();
                let address_country = jQuery('#country_val option:selected').text();
                let address_country_code = jQuery("form#confirm-booking").find("#country_val").val();
                let phone_code = jQuery('#country_val option:selected').data('phone-code');
                let address_postcode = jQuery("form#confirm-booking").find("#postcode").val();
                let email = jQuery("form#confirm-booking").find("#email").val();
                let villaId  = jQuery("form#booking").find("#villaid").val();
                
                let mobile_num = jQuery("form#confirm-booking").find("#mobile").val();
                let suburb = jQuery("form#confirm-booking").find("#suburb").val();
                let noOfNight = jQuery("form#confirm-booking").find("#night").val();
                let villaprice = jQuery("form#confirm-booking").find("#villaprice").val();
                let adults = jQuery("form#confirm-booking").find("#adults").val();
                let children = jQuery("form#confirm-booking").find("#children").val();
                let arrivalDate = jQuery("form#booking").find("#arrival").val();
                let departureDate = jQuery("form#booking").find("#departure").val();
                let NoOfRooms = jQuery("form#booking").find("#nr_of_rooms").val();
                let paymentMethod = jQuery("form#confirm-booking").find(".payment_method").val();
                console.log(paymentMethod);

          //      let baseUrl = 'let baseUrl = 'https://test-api.pinpayments.com/ql82/sc?&amount_editable=false&return_url='+return_url+'&currency=<?php echo ($villa_pricing["o_currency"]) ? $villa_pricing["o_currency"] : "USD"; ?>&amount=' + $('input[name="amount"]').val();';
               // let baseUrl = 'https://pay.pinpayments.com/ql82/sc?&amount_editable=false&currency=<?php echo ($villa_pricing["o_currency"]) ? $villa_pricing["o_currency"] : "USD"; ?>&amount=' + $('input[name="amount"]').val();
                let baseUrl ='https://pay.pinpayments.com/s0ic/sc/test?amount_editable=false&success_url=https%3A%2F%2Fwptest.villagetaways.com%2Fresponse%2F&currency=<?php echo ($villa_pricing["o_currency"]) ? $villa_pricing["o_currency"] : "USD"; ?>&amount='+$('input[name="amount"]').val();
               
               
                let description = '';
                let villa_desc = '<?php echo $villa_details["LOCATION_NAME"].' Villa '.$villa_details["VG_NUMBER"]; ?>';
                let villa_currency = '<?php echo ($villa_pricing["o_currency"]) ? $villa_pricing["o_currency"] : "USD"; ?>';
                let queryString = "&description=<?php echo $villa_details["LOCATION_NAME"]; ?> Villa <?php echo $villa_details["VG_NUMBER"]; ?>";
                if( firstName && surname ) {
                    queryString += "&name=" + firstName + " " + lastName;
                }
                
                if( email ) {
                    queryString += "&email=" + email;
                }
                
                if( address_line1 ) {
                    queryString += "&address_line1=" + address_line1;
                }
                
                if( address_city ) {
                    queryString += "&address_city=" + address_city;
                }
                
                if( address_state ) {
                    queryString += "&address_state=" + address_state;
                }
                
                if( address_postcode ) {
                    queryString += "&address_postcode=" + address_postcode;
                }
                
                if( address_country ) {
                    queryString += "&address_country=" + address_country;
                }
        
                baseUrl += queryString;
                
                
                let csymbol = "<?php echo $csymbol; ?>";
                
                $('#wire_transfer_amount').val($('input[name="amount"]').val());
                $('#email_2').val(email);
               var selectedPaymentMethod = $('input[name="payment_method"]:checked').val();
              $.ajax({
                url: ajax_url.ajaxurl,
                type: 'POST',
                data: {action: "villa_booking_details", fname : firstName , lname : lastName , address_line : address_line1 ,
                address_city : address_city , address_state : address_state , address_country : address_country ,
                address_postcode : address_postcode ,address_country_code:address_country_code, email : email ,csymbol:csymbol, amount: $('input[name="amount"]').val(),villaId : villaId ,villa_desc:villa_desc, villa_currency:villa_currency , mobile_num : mobile_num , phone_code:phone_code,
                suburb : suburb , night : noOfNight , villaprice : villaprice  , adults : adults , children : children ,
                arrivalDate : arrivalDate , departureDate : departureDate , NoOfRooms : NoOfRooms , paymentMethod : paymentMethod
                },
                success: function(response) {
                    
                 if(selectedPaymentMethod=="master_credit"){  
               // window.location.href = baseUrl;
               
                   window.location.href = 'https://wptest.villagetaways.com/confirm-payment/';
                   
                    }else{
                        $('#wire_transfer_form').submit();
                    }
                },
                error: function (error) {
                    // $('.loader').hide();
                }
            }); 
               
             }
        });
    });

</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
    var popup = document.getElementById('popup');
    var openPopupBtn = document.getElementById('openPopupBtn');
    var closeBtn = document.getElementsByClassName('closeBtn')[0];

    // Open the popup when the button is clicked
    openPopupBtn.addEventListener('click', function() {
        popup.style.display = 'block';
    });

    // Close the popup when the 'x' is clicked
    closeBtn.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    // Close the popup when clicking outside the popup content
    window.addEventListener('click', function(event) {
        if (event.target == popup) {
            popup.style.display = 'none';
        }
    });
});

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
   
   
   var selected_fr = $('#arrival').val();
   var selected_to = $('#departure').val();
   var fr_dateParts = selected_fr.split('-');
   var to_dateParts = selected_to.split('-');
   var fr_selectedYear = fr_dateParts[2];  // Year from the arrival calendar
   var fr_selectedMonth = fr_dateParts[1];
   var to_selectedYear = to_dateParts[2];  // Year from the arrival calendar
   var to_selectedMonth = to_dateParts[1];
  
  
    // Initialize Zabuto Calendar for "From" date
    $arrivalDate.zabuto_calendar({
        classname: 'table clickable',
         year: parseInt(fr_selectedYear),  // Year from arrival calendar
        month: parseInt(fr_selectedMonth),
         today_markup: '<span class="badge bg-primary">[day]</span>',
      events: <?php echo json_encode($events);?>
    });

    $departDate.zabuto_calendar({
        
        classname: 'table clickable',
         year: parseInt(to_selectedYear),  // Year from arrival calendar
        month: parseInt(to_selectedMonth),
         today_markup: '<span class="badge bg-primary">[day]</span>',
         events: <?php echo json_encode($events);?>

    });
    
    
  
    $arrivalDate.on('zabuto:calendar:day', function (e) {
       
      
       if(e.hasEvent && e.eventdata.classnames['0']!='booked-half'){
        alert('Already booked!');
       }else{
        $("#arrival").val(formatDate(e.value)); // Set the selected date in the input field
             $("#from-calendar-popup").hide();
             let toDateString = getThreeDayFromToday(formatDate(e.value));
             $("#departure").val(toDateString);
             $("#from-calendar-popup").hide();
             document.getElementById('book-villa').disabled = true;
            
             var selectedDate = $('#arrival').val();  // Default to today if no date is selected

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
        $("#departure").val(formatDate(e.value)); // Set the selected date in the input field
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
        $("#departure").val(formatDate(e.value)); // Set the selected date in the input field
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
    $("#arrival").click(function () {
        showCalendar(this, "#from-calendar-popup");
    });

    // Show the "To" calendar popup when clicking on the input field
    $("#departure").click(function () {
        showCalendar(this, "#to-calendar-popup");
    });

    // Hide the calendar popup when clicking outside of it
    $(document).mouseup(function (e) {
        if (!$('.calendar-popup').is(e.target) && $('.calendar-popup').has(e.target).length === 0) {
            $('.calendar-popup').hide();
        }
    });
    
    
    
    document.getElementById('nr_of_rooms').addEventListener('change', function() {
    var selectedValue = this.value;

      document.getElementById('book-villa').disabled = true;
    
  });
  
  
//   $('#from-calendar').on('click', function() {
//     // Get the selected date from the arrival calendar
//      var selectedDate = $('#arrival').val();  // Default to today if no date is selected

//      // Extract year and month from selected date
//      var dateParts = selectedDate.split('-');
//      var selectedYear = dateParts[2];  // Year from the arrival calendar
//      var selectedMonth = dateParts[1]; // Month from the arrival calendar
//      alert(selectedYear);
//      alert(selectedMonth);
     
// //     // Set the depart calendar to the same year and month as the arrival calendar
//     $departDate.zabuto_calendar({
//          classname: 'table clickable',
//          events: <?php echo json_encode($events); ?>,
//          year: parseInt('2025'),   // Set the depart calendar to the same year
//          month: parseInt('01')  // Set the depart calendar to the same month
//      });
//  });
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
    z-index: 1;
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
    max-width: 500px;
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
        
        .zabuto-calendar__event.booked {
         background: #d9534f;
         color: #fff;
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
td.tax {
    display: flex;
 }
 
 td.tax .tax_percent {
    margin-left: 50px;
    padding: 3px 10px;
   
}
.zabuto-calendar.table>tbody td .badge {
    font-size: 100%;
    color: #fff;
}
        
</style>

<?php get_footer(); ?>