<?php 
/* Template Name: Inquiry */
get_header();


$villa_number = $_GET['villa_number'];
$agent_id = "";
$villa_id = "";
if($villa_number) {
    $conn = oracleDbConnection();
    $wlcome_msg = "";
    $bedrooms = (isset($_COOKIE["__bedrooms"]) && $_COOKIE["__bedrooms"]) ? $_COOKIE["__bedrooms"] : 1;
    $villa_details = fetchVillaDetails($conn, $villa_number, $bedrooms);
    $countryIds = getCountryId($conn);
    $image = $villa_details['RANDOM_VILLA_IMAGE'];
    $villa_id = $villa_details['VILLA_ID'];
    $agent_id = $villa_details['AGENT_ID'];
    $date_from = date("d-m-Y");
    $date_to = date('d-m-Y', strtotime(' +1 day'));
}



if(isset($_POST['submit'])) {
    $fname      =       isset($_POST['fname']) ? filter_var($_POST['fname'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $lname      =       isset($_POST['lname']) ? filter_var($_POST['lname'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $email      =       isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : "";
    $phone      =       isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT) : "";
    $homePhone  =       isset($_POST['homePhone']) ? filter_var($_POST['homePhone'], FILTER_SANITIZE_NUMBER_INT) : "";
    $country    =       isset($_POST['country']) ? filter_var($_POST['country'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $address    =       isset($_POST['address']) ? filter_var($_POST['address'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $city       =       isset($_POST['city']) ? filter_var($_POST['city'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $state = isset($_POST['state']) ? filter_var($_POST['state'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $zip        =       isset($_POST['zip']) ? filter_var($_POST['zip'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $date_from  =       isset($_POST['date_from']) ? filter_var($_POST['date_from'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $date_from =  date('d-m-Y', strtotime($date_from));
    $date_to  =         isset($_POST['date_to']) ? filter_var($_POST['date_to'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $date_to =  date('d-m-Y', strtotime($date_to));
    $title  =         isset($_POST['title']) ? filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $flexible = isset($_POST['flexible']) ? filter_var($_POST['flexible'], FILTER_SANITIZE_SPECIAL_CHARS) : "0";
    $newsletter = isset($_POST['newsletter']) ? filter_var($_POST['newsletter'], FILTER_SANITIZE_SPECIAL_CHARS) : "0";
    
    $adults = isset($_POST['adults']) ? filter_var($_POST['adults'], FILTER_SANITIZE_SPECIAL_CHARS) : "0";
    $kids = isset($_POST['kids']) ? filter_var($_POST['kids'], FILTER_SANITIZE_SPECIAL_CHARS) : "0";
    $budget = isset($_POST['budget']) ? filter_var($_POST['budget'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    $questions = isset($_POST['questions']) ? filter_var($_POST['questions'], FILTER_SANITIZE_SPECIAL_CHARS) : "";
    
    $error = [];
    if($fname == "" || $lname == "" || $email == "" || $country == "" || $date_from == "" || $date_to == ""){
        $error['error'] = "Please fill required fields";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error['email'] = "Invalid email format";
    }
    if(empty($error) && count($error) === 0) {
        $result = enquiry($conn, $title, $fname, $lname, $email, $phone, $homePhone, $address, $city, $state, $zip, $country, $agent_id, $newsletter, $villa_id, $villa_number, $date_from, $date_to, $flexible, $adults, $kids, $budget, $questions);
        //$result = "OK";
        if ($result == 'OK') { 
            $pageUrl = home_url()."/inquiry?villa_number=$villa_number";
            
            $success_msg = '<p>Thank you for your inquiry, an experienced reservation specialist will contact you shortly.</p>';
            // $success_msg .= '<h6>*** IMPORTANT INFORMATION – BEWARE FRAUDSTERS ***</h6>';
            // $success_msg .= '<p>It has come to our attention that scammers/hackers are attempting to gain access to clients inquiry information from villa rental agencies.</p>';
            // $success_msg .= "<p>If you receive an email from another source claiming to have 'Last minute Cancellations' or 'Special Offers' and offering big discounts, usually with links to actual genuine websites;</p>";
            // $success_msg .= '<p>BE WARNED!!</p>';
            // $success_msg .= '<p>People have fallen victim to such scammers phishing attempts and in this way lost considerable sums of money.</p>';
            // $success_msg .= '<p>Please stay vigilant and ONLY use reputable/verifiable companies when renting holiday accommodation on the internet. Villa Getaways Ltd has been successfully providing its clients safe and enjoyable villa rentals since 2001. Trust the source.</p>';
            
        ?>
            <script type="text/javascript">
                console.log('first')
                function delay(ms) {
                    return new Promise(resolve => setTimeout(resolve, ms));
                }
                // Display success message after 1 second
                delay(1000).then(() => {
                    bootbox.alert({
                        colseButton: false,
                        className:"enquiry-villa-success-alert",
                        size: "large",
                        message: "<?php echo $success_msg; ?>"
                    });
                });
                
                // Load the page after 2 seconds
                delay(3000).then(() => {
                    //window.location.href = '<?php echo $pageUrl; ?>'; // Replace with your desired page URL
                    window.close();
                });
            </script>
        <?php
        } else { ?>
            <script>
                setTimeout(function(){
                    bootbox.alert('<p>Something went wrong, please try again later or contact us directly using our email address sales@villagetaways.com.</p>');
                }, 5000)
            </script>
        <?php
        }
    }
}
?>


<div style="display: block;">
    <div>
        <div class="modal-content inquiry-page">
            <div class="modal-header">
                <h3>Make an inquiry</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">   
                        <div class="hidden-xs">
                            <img alt="Villa <?php echo $villa_details['VILLA_ID']; ?>" src="<?php echo home_url("/wp-content/uploads/" . $villa_details['RANDOM_VILLA_IMAGE']); ?>">                                
                            <h4>Villa <?php echo $villa_details['VG_NUMBER']; ?></h4>
                            <p><?php echo $villa_details['BEDS']; ?> bedrooms, <?php echo $villa_details['SLEEPS']; ?> Bathrooms</p>
                            <p>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                            </p>
                        </div>                                
                    </div>
                    <div class="col-md-7">
                        
                        <?php
                        if(isset($error) && count($error) > 0 ) {
                            ?>
                            <ul>
                            <?php
                            foreach($error as $key => $value){
                                echo "<li>".$value."</li>";
                            }?>
                            </ul>
                            <?php
                        }else {
                            echo "<h2>".$wlcome_msg."</h2>";
                        }
                        ?>
                        
                        <form id="inquiry_form" method="post" action="">
                            <input type="hidden" name="villa_number" value="<?php echo $villa_number; ?>" />
                            <input type="hidden" name="agent_id" value="<?php echo $agent_id; ?>" />
                            <input type="hidden" name="villa_id" value="<?php echo $villa_id; ?>" />
                            <p>Let's get started...</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <!--<input type="text" id="datepickerHelper" style="padding: 0; border: none; line-height: 0; height: 0; position: absolute">-->
                                    <div class="input-group" id="reservation-button-3">
                                    <!--<span class="input-group-addon" onclick="$('#reservation3_from').focus()"><span class="glyphicon glyphicon-calendar"></span></span>-->
                                    <input type="text" class="form-control" placeholder="Check-In" id="date_from" min="<?php echo $date_from; ?>" name="date_from" value="" data-url="/getBookedDays/501/36/1" style="">
                                    </div>
                                    <div class="input-group" id="reservation-button-3" style="margin-top: 15px;">
                                    <!--<span class="input-group-addon" onfocus="$('#reservation3_to').focus()"><span class="glyphicon glyphicon-calendar"></span></span>-->
                                    <input type="text" class="form-control" placeholder="Check-Out" id="date_to" min="<?php echo $date_to; ?>" name="date_to" value="" data-url="/getBookedDays/501/36/1" style="">
                                    </div>
                                
                                    <div class="checkbox flexible-dates">
                                        <label><input type="checkbox" value="1" name="flexible" class="filled">
                                            My dates are flexible
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-6 without-left-padding without-right-padding">
                                        <div class="form-group night-budget">
                                            <select class="selectpicker white select-guests-2_inquiry" id="select-guests-2" required="" name="adults">
                                                <option value="0">Adults</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="23">23</option>
                                                <option value="24">24</option>
                                                <option value="25">25+</option>
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="col-md-6 without-right-padding kids-wrapper">
                                        <div class="form-group">
                                            <select class="selectpicker white select-kids-2_inquiry" id="select-kids-2" required="" name="kids">
                                                <option value="0">Children Under 12 Kids</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10+</option>
                                            </select>
                                        
                                        </div> 
                                    </div>
                                    <div class="form-group">    
                                        <select class="selectpicker white" id="select-budget" name="budget">
                                            <option value="0">Nightly budget?</option>
                                            <option value="$300 - $500">$300 - $500</option>
                                            <option value="$500 - $750">$500 - $750</option>
                                            <option value="$750 - $1000">$750 - $1000</option>
                                            <option value="$1000 - $1250">$1000 - $1250</option>
                                            <option value="$1250 - $1500">$1250 - $1500</option>
                                            <option value="$1500 - $2000">$1500 - $2000</option>
                                            <option value="$2000 - $2500">$2000 - $2500</option>
                                            <option value="$2500 - $5000">$2500 - $5000</option>
                                            <option value="$5000 - $10000">$5000 - $10000</option>
                                            <option value="$10000+">$10000+</option>
                                        </select>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-2" style="padding-right: 0">
                                    <select class="selectpicker btn-light white" id="select-title" name="title">
                                        <option value="">Title</option>
                                        <option>Mr.</option>
                                        <option>Mrs.</option>
                                        <option>Ms.</option>
                                        <option>Miss.</option>
                                        <option>Dr.</option>
                                    </select>
                                </div>	
                                <div class="form-group col-md-5">
                                    <label class="sr-only" for="fname">Your First Name</label>
                                    <input type="text" class="form-control" placeholder="First Name*" required="" name="fname" id="fname" value="">
                                </div> 
                            
                                <div class="form-group col-md-5">
                                    <label class="sr-only" for="lname">Your Last Name</label>
                                    <input type="text" class="form-control" placeholder="Last Name*" required="" name="lname" id="lname" value="">
                                </div>
                            </div>  
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="sr-only" for="yourEmail">Your Email address</label>
                                    <input type="email" class="form-control" id="yourEmail" placeholder="Your Email*" required="" name="email" value="">
                                </div>
                            </div>
                            <div class="row">
                            
                                <div class="form-group col-md-6">
                                    <label class="sr-only" for="yourPhone">Mobile Phone</label>
                                    <input type="text" class="form-control" placeholder="Mobile Phone"  name="phone" id="phone" value="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="sr-only" for="homePhone">Home Phone</label>
                                    <input type="text" class="form-control" placeholder="Home Phone" name="homePhone" id="homePhone" value="">
                                </div>
                            </div>
                            
                            <div class="row">	
                                <div class="form-group col-md-12">
                                    <label class="sr-only" for="yourAddress">Address</label>
                                    <input type="text" class="form-control" placeholder="Address" name="address" id="address" value="">
                                </div>	
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="sr-only" for="yourCity">City</label>
                                    <input type="text" class="form-control" placeholder="City" name="city" id="city" value="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="sr-only" for="yourState">State</label>
                                    <input type="text" class="form-control" placeholder="State" name="state" id="state" value="">
                                </div>
                            </div>
                            <div class="row">                                            
                                <div class="form-group col-md-6">
                                    <label class="sr-only" for="yourZip">Zip</label>
                                    <input type="text" class="form-control" placeholder="Zip" name="zip" id="zip" value="">
                                </div>	
                                <div class="form-group col-md-6">
                                    <!--<div class="combobox-container"> -->
                                    <!--    <input type="hidden" name="country" value=""> -->
                                    <!--    <div class="input-group"> -->
                                    <!--    <input type="text" autocomplete="off" placeholder="Country" required="required" class="country white form-control"> -->
                                    <!--    <span class="input-group-addon dropdown-toggle" data-dropdown="dropdown"> <span class="caret"></span> <span class="glyphicon glyphicon-remove">-->
                                            
                                    <!--    </span> </span> -->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <select class="country white form-control" autocomplete="off" required="" name="country" id="country">
                                        <option value="">Country</option>
                                        <?php 
                                        foreach($countryIds as  $value) {
                                            ?>
                                            <option value="<?php echo $value['COUNTRY_ID']?>"><?php echo $value['COUNTRY_NAME']?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <textarea class="form-control" rows="3" placeholder="Are there any special requirements we can help you with?" name="questions"></textarea>
                            <div class="checkbox">
                                <label>
                                <input type="checkbox" value="1" checked="" name="remember_me" class="filled">
                                Remember my details.
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                <input type="checkbox" value="1" checked="" name="newsletter" class="filled">
                                Send me exclusive Villa Getaways offers.
                                </label>
                            </div>
                            <!--<div class="checkbox" style="text-align: center">-->
                            <!--<label>-->
                            <!--<div class="g-recaptcha" data-theme="dark" data-sitekey="6Lco-QUTAAAAAGgWYH3VSxjCJb776zv51m-Vi-fA"><div style="width: 304px; height: 78px;"><div><iframe title="reCAPTCHA" src="https://www.google.com/recaptcha/api2/anchor?ar=1&amp;k=6Lco-QUTAAAAAGgWYH3VSxjCJb776zv51m-Vi-fA&amp;co=aHR0cHM6Ly93d3cudmlsbGFnZXRhd2F5cy5jb206NDQz&amp;hl=en&amp;v=4PnKmGB9wRHh1i04o7YUICeI&amp;theme=dark&amp;size=normal&amp;cb=al2am1m1bbh9" width="304" height="78" role="presentation" name="a-3jl1u4z61n75" frameborder="0" scrolling="no" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-top-navigation allow-modals allow-popups-to-escape-sandbox"></iframe></div><textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid rgb(193, 193, 193); margin: 10px 25px; padding: 0px; resize: none; display: none;"></textarea></div><iframe style="display: none;"></iframe></div>-->
                            <!--<script src="https://www.google.com/recaptcha/api.js" async="" defer=""></script>-->
                            <!--</label>-->
                            <!--</div>-->
                            <!--<script src="https://cdn.pin.net.au/pin.v2.js"></script>-->
                            <input type="submit" name="submit" value="Send your inquiry" class="btn btn-success btn-lg btn-block" id="inquiry_form_submit" />
                            
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-left"></div>
            <div class="row" id="inquiry_rates">
                <div class="row">
                    <?php echo get_template_part('template-parts/villa/villa', 'rates', ["villa_details" => $villa_details]); ?>
                </div>
            </div>
            <style>
                #reservation3_from {background: url('/images/red_star.png') no-repeat 69px 11px}
                #reservation3_to {background: url('/images/red_star.png') no-repeat 82px 11px}
                #yourEmail {background: url('/images/red_star.png') no-repeat 82px 11px}
                #fname {background: url('/images/red_star.png') no-repeat 81px 11px}
                #lname {background: url('/images/red_star.png') no-repeat 79px 11px}
                #phone {background: url('/images/red_star.png') no-repeat 87px 11px}
                #cardNumber {background: url('/images/red_star.png') no-repeat 98px 11px}
                #nameOnCard {background: url('/images/red_star.png') no-repeat 102px 11px}
                #expiry {background: url('/images/red_star.png') no-repeat 63px 11px}
                #cvc {background: url('/images/red_star.png') no-repeat 43px 11px}
                .country {background: url('/images/red_star.png') no-repeat 65px 11px}
                #inquiry_form .filled {background: none; background: #fff;}
                .container-book-now table, .container-book-now table tr, .container-book-now table td, .container-book-now p, .container-book-now #paymentDep, .container-book-now #paymentDep .glyphicon, .container-book-now #terms-container a  {color: #fff;}
                .container-book-now .modal-header .close {display: none;}
            </style>
        </div>
    </div>
</div>


<?php get_footer(); ?>