<?php 
/* Template Name: Villa Booking payment page */
get_header(); 

if(isset($_GET['ref'])){
    
 $conn = oracleDbConnection();
 $ref = $_GET['ref']; // or from $_GET['ref'], but sanitize it
 $data = fetchbookingDetails($conn, $ref);


// An unknown ref returns no rows; fall back to an empty record.
$data = is_array($data) && isset($data['0']) && is_array($data['0']) ? $data['0'] : [];


$booking_id = $data['BOOKING_ID'] ?? "";
 setcookie("booking_id",  $booking_id, time() + 86400, "/");
$amount = $data['AMOUNT'] ?? "";
 $fname =$data['FIRSTNAME'] ?? "";
 $lname = $data['LASTNAME'] ?? "";
 $no_people = $data['NUM_PEOPLE'] ?? "";
 $no_child =$data['NUM_CHILDREN'] ?? "";
 $address = $data['ADDRESS'] ?? "";
 $city = $data['SUBURB'] ?? "";
 $state = $data['STATE'] ?? "";
 $country = $data['COUNTRY'] ?? "";
 $zip = $data['POSTCODE'] ?? "";
 $phone = $data['PHONE_MOBILE'] ?? "";
 $email = $data['CLIENT_EMAIL'] ?? "";
 $c_code = $data['COUNTRY_ISO3'] ?? "";
 $p_code = $data['COUNTRY_NUMCODE'] ?? "";
 $villa_desc ="";
 
$currency_code = "$";
 
 
 
  setcookie("villa_currency",  $currency_code, time() + 86400, "/");
 
 ?>
    
<div class="static-page">
    <div class="container p-0">
        <div class="row">
            <div class="col-xl-12 left-aside mb-5">
            
             <div  class="row payment-methods">
                                        <div class="col-xl-12 left-aside mb-5 single-villa">
                                            <h5>Pay By</h5>
                                            <hr>
                                            <div class="row ">
                                                <div class="col-md-4 col">
                                                <input type="radio" id="payment_credit" class="payment_method" name="payment_method" checked="" value="master_credit" required="">
                                                    <button style="width: 150px;" type="button" class="btn btn-default payment-button">
                                                         <label for="payment_credit">
                                                            <img src="https://wptest.villagetaways.com/wp-content/uploads/2025/04/Mastercard.svg" style="padding:15px 0;">
                                                            <br>
                                                            Credit Card
                                                        </label>
                                                    </button>
                                                </div>
                                                <div class="col-md-4 col">
                                                    <input type="radio" id="payment_wire" class="payment_method" name="payment_method" value="wire_transfer">
                                                    <button style="width: 150px;" type="button" class="btn btn-default payment-button">
                                                        <label for="payment_wire">
                                                            <img src="https://wptest.villagetaways.com/wp-content/uploads/2025/04/bank-transfer.svg" style="padding:15px 0;">
                                                            <br>
                                                            TT / Wire Tranfer
                                                        </label>
                                                    </button>
                                                </div>
                                                <div class="col-md-4 col">
                                                    <input type="radio" id="payment_amex" class="payment_method" name="payment_method" value="amex_card">
                                                    <button style="width: 150px;" type="button" class="btn btn-default payment-button">
                                                        <label for="payment_amex">
                                                            <img src="https://wptest.villagetaways.com/wp-content/uploads/2025/04/Amex.svg" style="padding:15px 0;">
                                                             <br>
                                                            Amex
                                                        </label>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row amount">
                                                <div class="col-md-6 col">
                                                    <div class="villa-payment-block">
                                                        
                                                        Villa Gateways Will Recieve.<br>
                                                        <strong><span data-amount="<?php echo $amount;?>"><?php echo  $currency_code.' '.$amount;?></span></strong>
                                                        
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col">
                                                    <div class="actual-payment-block">
                                                        
                                                        <span id="charge-label">Your card will be charged for</span> <br>
                                                        
                                                       <strong> <?php echo  $currency_code." ";?><span id="actual-amount">XXX</span></strong>
                                                        
                                                    </div>
                                                </div>    
                                            
                                        </div>
                                        <div class="row action">
                                        <div class="col-md-12 col">
                                            <button style="width: 100%;" id="pay-now" type="submit" class="btn btn-success">Pay Now</button>
                                            </div>
                                        </div>
                                        
                                    </div>
           
            </div>
        </div>
    </div>
</div>  


<script src="https://checkout.flywire.com/flywire-payment.js"></script>

<script>


   $(document).ready(function() {
      
      
        let amount = parseFloat('<?php echo $amount; ?>'.replace(/,/g, ''));
        $('#actual-amount').text(amount + (amount * 2) / 100);
        
        $(document).on('click','#pay-now',function(){
            
             let booking_id = '<?php echo $booking_id;?>';
             let villa_currency = '<?php echo $currency_code; ?>;'
             let villa_desc = '<?php echo $villa_desc; ?>';
                        $.ajax({
                url: ajax_url.ajaxurl,
                type: 'POST',
                data: {action: "dierct_payment", bookingid:booking_id, villa_currency:villa_currency,villa_desc:villa_desc},
                success: function(response) {
                   
                },
                error: function (error) {
                    // $('.loader').hide();
                }
            }); 

                        
                        let fname = '<?php echo $fname;?>';
                        let lname = '<?php echo $lname;?>';
                        let email = '<?php echo $email;?>';
                        let phone = '<?php echo $phone;?>';
                        let address_line = '<?php echo $address;?>';
                        let city = '<?php echo $city;?>';
                        let state = '<?php echo $state;?>';
                        let zipcode = '<?php echo $zip;?>';
                        let country = '<?php echo $country ;?>';
                        let country_code = '<?php echo $c_code;?>';
                        let phone_code = '<?php  echo $p_code; ?>';
                       
                        let selectedMethod = $('input[name="payment_method"]:checked').val();
                       
                        
                         if(selectedMethod == 'master_credit'){
                             
                             
                             
                             var config = {
                         // Set the target environment (demo, prod)
                         env: "demo",
                         recipientCode: "VGI",
                         amount: amount*1.02,
                         
                          firstName: fname,
                          lastName: lname,
                          email: email,
                          phone: phone_code+" "+phone,
                          address: address_line,
                          city: city,
                          state: state,
                          zip: zipcode,
                          country: country_code,
                          
                          recipientFields: {
                            booking_reference: booking_id,
                            additional_information: booking_id,
                          },
                          
                          readonlyFields: [
                                 "booking_reference"
                          ],
                          
                             
                            paymentOptionsConfig: {
                                filters: {
                                type: ['credit_card'],
                                "excludedCreditCardsBrands":
                                [ "amex"]
                                }
                            },
                             
                          
                            
                
                        // Recommended (not required) validation error handler
                          onInvalidInput: function(errors) {
                            errors.forEach(function(error) {
                              alert(error.msg);
                            });
                          },
                
                          // Display payer and custom field input boxes
                          requestPayerInfo: true,
                          requestRecipientInfo: false,
                
                          // Set the return URL where payers are redirected to on completion
                          returnUrl: "https://wptest.villagetaways.com/response/",
                           
                         };


                         }
                         
                         if(selectedMethod == 'wire_transfer'){
                         
                         
                        var config = {
                         // Set the target environment (demo, prod)
                         env: "demo",
                         recipientCode: "VGI",
                         amount: amount,
                         
                          firstName: fname,
                          lastName: lname,
                          email: email,
                          phone: phone_code+" "+phone,
                          address: address_line,
                          city: city,
                          state: state,
                          zip: zipcode,
                          country: country_code,
                          
                          recipientFields: {
                            booking_reference: booking_id,
                            additional_information: booking_id,
                          },
                          
                       
                          
                          readonlyFields: [
                                 "booking_reference"
                          ],
                          
                             
                           paymentOptionsConfig: {
                            filters: {
                            type: ['bank_transfer']
                            }
                            },
                          
                        // Recommended (not required) validation error handler
                          onInvalidInput: function(errors) {
                            errors.forEach(function(error) {
                              alert(error.msg);
                            });
                          },
                
                          // Display payer and custom field input boxes
                          requestPayerInfo: true,
                          requestRecipientInfo: true,
                
                          // Set the return URL where payers are redirected to on completion
                          returnUrl: "https://wptest.villagetaways.com/response/",
                           
                         };
                             
                         }
                         
                         if(selectedMethod == 'amex_card'){
                             
                            var config = {
                         // Set the target environment (demo, prod)
                         env: "demo",
                         recipientCode: "JLR",
                         amount: amount*1.03,
                         
                          firstName: fname,
                          lastName: lname,
                          email: email,
                          phone: phone_code+" "+phone,
                          address: address_line,
                          city: city,
                          state: state,
                          zip: zipcode,
                          country: country_code,
                          
                          recipientFields: {
                            booking_reference: booking_id,
                            additional_information: booking_id,
                          },
                          
                       
                          
                          readonlyFields: [
                                 "booking_reference"
                          ],
                          
                             
                         
                        // Recommended (not required) validation error handler
                          onInvalidInput: function(errors) {
                            errors.forEach(function(error) {
                              alert(error.msg);
                            });
                          },
                
                          // Display payer and custom field input boxes
                          requestPayerInfo: true,
                          requestRecipientInfo: true,
                
                          // Set the return URL where payers are redirected to on completion
                          returnUrl: "https://wptest.villagetaways.com/response/",
                           
                         };
                             
                         }   
                        
                        
                         var modal = window.FlywirePayment.initiate(config);
                         modal.render();
        });
        
       $('input[name="payment_method"]').on('change', function() {
    var selectedValue = $(this).val();

    let amount = parseFloat('<?php echo $amount; ?>'.replace(/,/g, ''));

    if (selectedValue == 'master_credit') {
        $('#actual-amount').text(amount + (amount * 2) / 100);
        $('#charge-label').text('Your card vill be charged for');
    } else if (selectedValue == 'amex_card') {
        $('#actual-amount').text(amount + (amount * 3) / 100);
        $('#charge-label').text('Your card vill be charged for');
    } else {
        $('#actual-amount').text(amount);
        $('#charge-label').text('Transfer Amount');
    }
});

   });
</script>

    
<?php }else{ ?>

<div class="static-page">
    <div class="container p-0">
        <div class="row">
            <div class="col-xl-12 left-aside mb-5">
            
             <div  class="row payment-methods">
                                        <div class="col-xl-12 left-aside mb-5 single-villa">
                                            <h5>Pay By</h5>
                                            <hr>
                                            <div class="row ">
                                                <div class="col-md-4 col">
                                                <input type="radio" id="payment_credit" class="payment_method" name="payment_method" checked="" value="master_credit" required="">
                                                    <button style="width: 150px;" type="button" class="btn btn-default payment-button">
                                                         <label for="payment_credit">
                                                            <img src="https://wptest.villagetaways.com/wp-content/uploads/2025/04/Mastercard.svg" style="padding:15px 0;">
                                                            <br>
                                                            Credit Card
                                                        </label>
                                                    </button>
                                                </div>
                                                <div class="col-md-4 col">
                                                    <input type="radio" id="payment_wire" class="payment_method" name="payment_method" value="wire_transfer">
                                                    <button style="width: 150px;" type="button" class="btn btn-default payment-button">
                                                        <label for="payment_wire">
                                                            <img src="https://wptest.villagetaways.com/wp-content/uploads/2025/04/bank-transfer.svg" style="padding:15px 0;">
                                                            <br>
                                                            TT / Wire Tranfer
                                                        </label>
                                                    </button>
                                                </div>
                                                <div class="col-md-4 col">
                                                    <input type="radio" id="payment_amex" class="payment_method" name="payment_method" value="amex_card">
                                                    <button style="width: 150px;" type="button" class="btn btn-default payment-button">
                                                        <label for="payment_amex">
                                                            <img src="https://wptest.villagetaways.com/wp-content/uploads/2025/04/Amex.svg" style="padding:15px 0;">
                                                             <br>
                                                            Amex
                                                        </label>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row amount">
                                                <div class="col-md-6 col">
                                                    <div class="villa-payment-block">
                                                        
                                                        Villa Gateways Will Recieve.<br>
                                                        <strong><span data-amount="<?php echo ($_COOKIE["booking_amount"] ?? "");?>"><?php echo ($_COOKIE["csymbol"] ?? "").' '.($_COOKIE["booking_amount"] ?? "");?></span></strong>
                                                        
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col">
                                                    <div class="actual-payment-block">
                                                        
                                                        <span id="charge-label">Your card will be charged for</span> <br>
                                                        
                                                       <strong> <?php echo ($_COOKIE["csymbol"] ?? "")." ";?><span id="actual-amount">XXX</span></strong>
                                                        
                                                    </div>
                                                </div>    
                                            
                                        </div>
                                        <div class="row action">
                                        <div class="col-md-12 col">
                                            <button style="width: 100%;" id="pay-now" type="submit" class="btn btn-success">Pay Now</button>
                                            </div>
                                        </div>
                                        
                                    </div>
           
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.flywire.com/flywire-payment.js"></script>

<script>


   $(document).ready(function() {
      
      
        let amount = parseFloat('<?php echo ($_COOKIE["booking_amount"] ?? ""); ?>'.replace(/,/g, ''));
        $('#actual-amount').text(amount + (amount * 2) / 100);
        
        $(document).on('click','#pay-now',function(){

                        
                        let fname = '<?php echo ($_COOKIE["fname"] ?? "");?>';
                        let lname = '<?php echo ($_COOKIE["lname"] ?? "");?>';
                        let email = '<?php echo ($_COOKIE["email"] ?? "");?>';
                        let phone = '<?php echo ($_COOKIE["phone"] ?? "");?>';
                        let address_line = '<?php echo ($_COOKIE["address_line"] ?? "");?>';
                        let city = '<?php echo ($_COOKIE["address_city"] ?? "");?>';
                        let state = '<?php echo ($_COOKIE["address_state"] ?? "");?>';
                        let zipcode = '<?php echo ($_COOKIE["address_postcode"] ?? "");?>';
                        let country = '<?php echo ($_COOKIE["address_country"] ?? "");?>';
                        let country_code = '<?php echo ($_COOKIE["address_country_code"] ?? "");?>';
                        let phone_code = '<?php echo ($_COOKIE["phone_code"] ?? "");?>';
                        let booking_id = '<?php echo ($_COOKIE["booking_id"] ?? "");?>';
                        let selectedMethod = $('input[name="payment_method"]:checked').val();
                        
                        
                         if(selectedMethod == 'master_credit'){
                             
                             var config = {
                         // Set the target environment (demo, prod)
                         env: "demo",
                         recipientCode: "VGI",
                         amount: amount*1.02,
                         
                          firstName: fname,
                          lastName: lname,
                          email: email,
                          phone: phone_code+" "+phone,
                          address: address_line,
                          city: city,
                          state: state,
                          zip: zipcode,
                          country: country_code,
                          
                          recipientFields: {
                            booking_reference: booking_id,
                            additional_information: booking_id,
                          },
                          
                          readonlyFields: [
                                 "booking_reference"
                          ],
                          
                             
                            paymentOptionsConfig: {
                                filters: {
                                type: ['credit_card'],
                                "excludedCreditCardsBrands":
                                [ "amex"]
                                }
                            },
                             
                          
                            
                
                        // Recommended (not required) validation error handler
                          onInvalidInput: function(errors) {
                            errors.forEach(function(error) {
                              alert(error.msg);
                            });
                          },
                
                          // Display payer and custom field input boxes
                          requestPayerInfo: true,
                          requestRecipientInfo: false,
                
                          // Set the return URL where payers are redirected to on completion
                          returnUrl: "https://wptest.villagetaways.com/response/",
                           
                         };


                         }
                         
                         if(selectedMethod == 'wire_transfer'){
                         
                         
                        var config = {
                         // Set the target environment (demo, prod)
                         env: "demo",
                         recipientCode: "VGI",
                         amount: amount,
                         
                          firstName: fname,
                          lastName: lname,
                          email: email,
                          phone: phone_code+" "+phone,
                          address: address_line,
                          city: city,
                          state: state,
                          zip: zipcode,
                          country: country_code,
                          
                          recipientFields: {
                            booking_reference: booking_id,
                            additional_information: booking_id,
                          },
                          
                       
                          
                          readonlyFields: [
                                 "booking_reference"
                          ],
                          
                             
                           paymentOptionsConfig: {
                            filters: {
                            type: ['bank_transfer']
                            }
                            },
                          
                        // Recommended (not required) validation error handler
                          onInvalidInput: function(errors) {
                            errors.forEach(function(error) {
                              alert(error.msg);
                            });
                          },
                
                          // Display payer and custom field input boxes
                          requestPayerInfo: true,
                          requestRecipientInfo: true,
                
                          // Set the return URL where payers are redirected to on completion
                          returnUrl: "https://wptest.villagetaways.com/response/",
                           
                         };
                             
                         }
                         
                         if(selectedMethod == 'amex_card'){
                             
                            var config = {
                         // Set the target environment (demo, prod)
                         env: "demo",
                         recipientCode: "JLR",
                         amount: amount*1.03,
                         
                          firstName: fname,
                          lastName: lname,
                          email: email,
                          phone: phone_code+" "+phone,
                          address: address_line,
                          city: city,
                          state: state,
                          zip: zipcode,
                          country: country_code,
                          
                          recipientFields: {
                            booking_reference: booking_id,
                            additional_information: booking_id,
                          },
                          
                       
                          
                          readonlyFields: [
                                 "booking_reference"
                          ],
                          
                             
                         
                        // Recommended (not required) validation error handler
                          onInvalidInput: function(errors) {
                            errors.forEach(function(error) {
                              alert(error.msg);
                            });
                          },
                
                          // Display payer and custom field input boxes
                          requestPayerInfo: true,
                          requestRecipientInfo: true,
                
                          // Set the return URL where payers are redirected to on completion
                          returnUrl: "https://wptest.villagetaways.com/response/",
                           
                         };
                             
                         }   
                        
                        
                         var modal = window.FlywirePayment.initiate(config);
                         modal.render();
        });
        
       $('input[name="payment_method"]').on('change', function() {
    var selectedValue = $(this).val();

    let amount = parseFloat('<?php echo ($_COOKIE["booking_amount"] ?? ""); ?>'.replace(/,/g, ''));

    if (selectedValue == 'master_credit') {
        $('#actual-amount').text(amount + (amount * 2) / 100);
        $('#charge-label').text('Your card vill be charged for');
    } else if (selectedValue == 'amex_card') {
        $('#actual-amount').text(amount + (amount * 3) / 100);
        $('#charge-label').text('Your card vill be charged for');
    } else {
        $('#actual-amount').text(amount);
        $('#charge-label').text('Transfer Amount');
    }
});

   });
</script>

<?php } ?>

<style type="text/css">

    .row.amount {
        text-align:center;
    }
    
    .row.action {
      margin-top: 30px;
    }
    .secondary-form{
        margin-top:30px;
    }
    
    .row.amount{
        margin-top:40px;
    }
    .row.payment-methods {
      max-width: 600px;
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

<?php get_footer();?>
