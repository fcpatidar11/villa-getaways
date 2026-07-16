<?php 
/* Template Name: Villa Booking response */

setcookie("fname", "", time() - 3600, "/");
setcookie("lname", "", time() - 3600, "/");
setcookie("address_line", "", time() - 3600, "/");
setcookie("address_state", "", time() - 3600, "/");
setcookie("address_city", "", time() - 3600, "/");
setcookie("address_country", "", time() - 3600, "/");
setcookie("address_postcode", "", time() - 3600, "/");
setcookie("email", "", time() - 3600, "/");
setcookie("client_id", "", time() - 3600, "/");
setcookie("villa_id", "", time() - 3600, "/");
setcookie("villa_price", "", time() - 3600, "/"); 
setcookie("nights", "", time() - 3600, "/");
setcookie("villa_desc", "", time() - 3600, "/");
setcookie("villa_currency",  "", time() - 3600, "/");
setcookie("csymbol",  "", time() - 3600, "/");
setcookie("phone", "", time() - 3600, "/");
setcookie("phone_code", "", time() - 3600, "/");
setcookie("address_country_code", "", time() - 3600, "/");
setcookie("booking_amount", "", time() - 3600, "/");
setcookie("booking_id", "", time() - 3600, "/");


get_header();

if(isset($_GET['reference'])){
    
    
if(isset($_GET['status']) && ($_GET['status']=='pending' || $_GET['status']=='active' || $_GET['status']=='success') ){
   
     
     $p_client_id = $_COOKIE["client_id"];
     $p_villa_id = $_COOKIE["villa_id"];
     $conn = oracleDbConnection();
         
     // getting booking id
     $booking_id = $_COOKIE["booking_id"];
     //  echo $booking_id;
     $p_payment_method_id=1;
         
         $p_receipt_tx=$_GET['reference'];
         $p_currency_code= $_COOKIE["villa_currency"];
         $p_amount_charged=$_GET['amount'];
         $p_pin_payment_fee=0;
         $p_security_deposit=null;
         $p_description=$_COOKIE["villa_desc"];
         $paysuccess = $_GET['status'];
         $p_payment_type_id=7;
         $success = callRecordPayment($conn, $booking_id,$p_payment_method_id,$p_receipt_tx,$p_currency_code,$p_amount_charged,$p_pin_payment_fee,$p_security_deposit,$p_description,$p_payment_type_id);

         if($success==0 && ($paysuccess=='active' || $paysuccess=='success' || $paysuccess=='pending') ){?> 
             <div class="static-page">
            <div class="container p-0">
                <div class="row">
                    <div class="col-xl-12 left-aside mb-5">
                      
                        <h2>Booking Confirmed!</h2>
                        Your booking is confirmed! Thank you for choosing us. We are excited to serve you soon.
                    
                    </div>
                </div>
            </div>
        </div>
    <?php
         }else{ ?>
    
    
     <div class="static-page">
        <div class="container p-0">
            <div class="row">
                <div class="col-xl-12 left-aside mb-5">
               
                <h2>Booking Failed!</h2>
                    Unfortunately, your booking could not be completed at this time. Please check your details and try again. If the issue persists, contact our support team for assistance.
                </div>
            </div>
        </div>
    </div>


<?php } 
    
    
} else{ ?>
    
   <div class="static-page">
        <div class="container p-0">
            <div class="row">
                <div class="col-xl-12 left-aside mb-5">
               
                <h2>Booking Failed!</h2>
                    Unfortunately, your booking could not be completed at this time. Please check your details and try again. If the issue persists, contact our support team for assistance.
                </div>
            </div>
        </div>
    </div>
  
    
<?php }
}


if(isset($_GET['charge_token'])){
    
    $bookingdetails = get_transient('booking_details');
    // if ( $bookingdetails !== false ) {
 
    $charge_token = $_GET['charge_token'];
    $curl = curl_init();
    //https://pay.pinpayments.com/s0ic/test?amount_editable=false&success_url=https%3A%2F%2Fwptest.villagetaways.com%2Fresponse%2F&currency=USD&amount=1000&description=testing
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://test-api.pinpayments.com/1/charges/$charge_token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Basic " . base64_encode("42uZdnVGfbkwy0Iy-wFyzQ:")
        ],
    ]);

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $charge_details = json_decode($response, true);
  

    if($charge_details['response']['success']){


        // $bookingdetails = get_transient('booking_details');
      
      
        $p_client_id = $_COOKIE["client_id"];
        $p_villa_id = $_COOKIE["villa_id"];
        // echo $p_client_id . " ". $p_villa_id . "<br>";
        // $p_arrive = $bookingdetails['arrivalDate'];
        // $bookingdetails['departureDate'];
        // $p_num_nights = get_transient('nights');
        // $p_num_people = $bookingdetails['adults'];
        // $p_num_children = $bookingdetails['children'];
        // $p_bedrooms = $bookingdetails['NoOfRooms'];
        // $p_price_night = str_replace(',', '', get_transient('villa_price'));
        // $p_payment_method = "cc";
        
      
        // $format = 'd-m-Y'; // Date format
        // $date = DateTime::createFromFormat($format, $p_arrive);
        // $today = new DateTime();
        // $interval = $today->diff($date);
       
        // if ($interval->days <= 30 && $date > $today) {
        //   $p_full_payment = 1;
        // } else {
        //     $p_full_payment = 0;
        // }
        
        $conn = oracleDbConnection();
         
        // getting booking id
         $booking_id = $_COOKIE["booking_id"];
        //  echo $booking_id;
         $p_payment_method_id=1;
         $p_receipt_tx=$charge_details['response']['token'];
         $p_currency_code=$charge_details['response']['currency'];
         $p_amount_charged=number_format($charge_details['response']['amount'] / 100, 2);
         $p_pin_payment_fee=$charge_details['response']['total_fees'];;
         $p_security_deposit=null;
         $p_description=$charge_details['response']['description'];
         $paysuccess = $charge_details['response']['success'];
         $p_payment_type_id=7;
         $success = callRecordPayment($conn, $booking_id,$p_payment_method_id,$p_receipt_tx,$p_currency_code,$p_amount_charged,$p_pin_payment_fee,$p_security_deposit,$p_description,$p_payment_type_id);
         if($success==0 && $paysuccess==1){?> 
             <div class="static-page">
            <div class="container p-0">
                <div class="row">
                    <div class="col-xl-12 left-aside mb-5">
                      
                        <h2>Booking Confirmed!</h2>
                        Your booking is confirmed! Thank you for choosing us. We are excited to serve you soon.
                    
                    </div>
                </div>
            </div>
        </div>

   <?php } else { ?>
        <div class="static-page">
        <div class="container p-0">
            <div class="row">
                <div class="col-xl-12 left-aside mb-5">
               
                <h2>Booking Failed!</h2>
                    Unfortunately, your booking could not be completed at this time. Please check your details and try again. If the issue persists, contact our support team for assistance.
                </div>
            </div>
        </div>
    </div>
 <?php } 
}

// delete_transient( 'booking_details' );
// delete_transient( 'client_id' );
// delete_transient( 'villa_id' );
// delete_transient( 'nights' );
// delete_transient( 'villa_price' );

 // set_transient('booking_details', "", 12 * HOUR_IN_SECONDS); // Expires in 12 hours

// }
}

if(isset($_GET['success'])){

?>

<div class="static-page">
    <div class="container p-0">
        <div class="row">
            <div class="col-xl-12 left-aside mb-5">
            <?php if($_GET['success']==1){?>    
                <h2>Booking Confirmed!</h2>
                Your booking is confirmed! Thank you for choosing us. We are excited to serve you soon.
                <div id="m_popup" class="popup">
   
                  <div class="popup-content">
                       <span id="closeButton" class="closeBtn">X</span>
                        
                        <div>Thank you for your booking, we have send details for the bank transfer to your email address at <a href="mailto:<?php echo $_GET['email'];?>"><?php echo $_GET['email'];?></a></div>
                  </div>  
    
                 </div>
                
            <?php } else { ?>
            <h2>Booking Failed!</h2>
                Unfortunately, your booking could not be completed at this time. Please check your details and try again. If the issue persists, contact our support team for assistance.
            <?php } ?>
            </div>
        </div>
    </div>
</div>


<?php }


?>
<style>
 /* The Popup Overlay */
.popup {
    display: block; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
}

/* Popup Content */
.popup-content {
    position: relative;
    background-color: #fff;
  
    padding: 20px;
    border-radius: 10px;
    width: 80%; /* Could be more or less depending on screen size */
    max-width: 500px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

/* Close Button */
.closeBtn {
    position: absolute;
    top: 10px;
    right: 20px;
    color: #aaa;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
}

.closeBtn:hover,
.closeBtn:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

/* Link Styling */
.popup-content a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}
.popup-content div {
    margin-top:25px;
}

.popup-content a:hover {
    text-decoration: underline;
}

</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    var popup = document.getElementById('m_popup');
     var closeBtn = document.getElementById('closeButton');
   
      
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
<?php get_footer();?>
