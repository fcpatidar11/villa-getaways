<?php
$conn = oracleDbConnection();
global $footer_location_id, $footer_destination_id;
// Only assigned by the villa templates; default them for every other page.
$footer_location_id    = $footer_location_id ?? "";
$footer_destination_id = $footer_destination_id ?? "";
$form_locations        = [];
$location_footer_id    = "";
$des_name = "";
$form_des = fetchDestinations($conn);
$__location_id = (isset($_COOKIE["__location_id"]) && $_COOKIE["__location_id"]) ? $_COOKIE["__location_id"] : "";
$__destination_id = (isset($_COOKIE["__destination_id"]) && $_COOKIE["__destination_id"]) ? $_COOKIE["__destination_id"] : "";
$destination_id = ( isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] ) ? $_REQUEST['destination_id'] : "";
$location_id = vg_request_array('location_id');
$countryIds = getCountryId($conn);
$class = "show";
if(count($location_id) > 1) {
    $locationIds = implode(",", $location_id);
}else {
    $locationIds = $location_id[0] ?? "";
}
if($destination_id) {
    $form_locations = fetchDestinationLocations($conn, $destination_id);


}
if(count($location_id) > 0) {
    $form_locations = fetchDestinationLocations($conn, $destination_id);
    $location_footer_id = $location_id[0];
    
    
}
if($footer_location_id) {
    $form_locations = fetchDestinationLocations($conn, $footer_destination_id);
   
}


?>
<!-- Start Content -->
<div class="container-fluid p-0">

    <section class="destinations pt-0 pb-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="destination-content pt-5 pl-4">
                        <h2>Villa <br> Getaways, </h2>
                        <div class="destination-content-links">
                            <div class="row">
                                <div class="col-md-4">
                                    <h4>Information</h4>
                                    <?php 
                                    wp_nav_menu(array(
                                        'container' => false,
                                        'container_class' => 'menu',
                                        'menu' => 'Information',
                                        'menu_class' => '',
                                        'theme_location' => 'secondary-nav',
                                        'before' => '',
                                        'after' => '<span></span>',
                                        'link_before' => '',
                                        'link_after' => '',
                                        'depth' => 2,
                                        'fallback_cb' => ''
                                    ));
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <h4>Our Small Print</h4>
                                    <?php 
                                    wp_nav_menu(array(
                                        'container' => false,
                                        'container_class' => 'menu',
                                        'menu' => 'Our Small Print',
                                        'menu_class' => '',
                                        'theme_location' => 'secondary-media-nav',
                                        'before' => '',
                                        'after' => '<span></span>',
                                        'link_before' => '',
                                        'link_after' => '',
                                        'depth' => 2,
                                        'fallback_cb' => ''
                                    ));
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <h4>Instagram</h4>
                                    <iframe src="https://snapwidget.com/embed/682684" class="snapwidget-widget" allowtransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; width:169px; height:169px"></iframe>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 newsletter-section">
                                    <h4>Villa Getaways Newsletter</h4>
                                    <p>
                                        Stay in the know with what is new in luxury travel and special offers.
                                    </p>
                                    <?php echo do_shortcode('[contact-form-7 id="373" title="Villa Getaways Newsletter"]'); ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 destination-content-form-wrap">
                    <div class="vg-tag">
                        <h4>VG</h4>
                    </div>
                    <div class="destination-content-form">
                        <span>Let us know</span>
                        <h3>What makes your ideal getaway</h3>
                        <?php //echo do_shortcode('[contact-form-7 id="359" title="Footer Form"]'); 
                        
                        function validate_mobile($mobile) {
                            return preg_match('/^[0-9]{10}+$/', $mobile);
                        }
                        
                        global $destination_name;
                        global $des_name;
                        global $loc_name;
                        global $location;
                        global $location_name;
                        global $heading;
                        
                        // Defaults for the GET render, before any submission has happened.
                        $result = "";
                        $destination_value = "";
                        $location_value = "";

                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST["let-us-know-form"] ?? "") == "Submit") {
                            // Retrieve form data
                            $firstname = $_POST['firstname'] ?? "";
                            $lastname = $_POST['lastname'] ?? "";
                            $number = $_POST['number'] ?? "";
                            $email = $_POST['email'] ?? "";
                            $destination = $_POST['destination_footer_id'] ?? "";
                            $message = $_POST['message'] ?? "";
                            $location = $_POST['location_footer_id'] ?? "";
                        
                            // Perform form validation
                            $errors = array();
                        
                            if (empty($firstname)) {
                                $errors[] = 'Please enter your first name';
                            }
                        
                            if (empty($lastname)) {
                                $errors[] = 'Please enter your last name';
                            }
                        
                            // if (empty($number)) {
                            //     $errors[] = 'Please enter your phone number';
                            // }
                            
                            // if(!empty($number) && ( !$valid = validate_mobile($number) ) ) {
                            //     $errors[] = 'Please enter correct number';
                            // }
                        
                            if (empty($email)) {
                                $errors[] = 'Please enter your email address';
                            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $errors[] = 'Please enter a valid email address';
                            }
                        
                            if (empty($destination) || $destination === 'default') {
                                $errors[] = 'Please select a destination';
                            }
                        
                            if (empty($message)) {
                                $errors[] = 'Please enter your message';
                            }
                            
                            $number = (int) $number;
                            
                            // Process form submission if there are no errors
                            if (empty($errors)) {
                                // Perform further processing or save data to a database
                                $result = enquiry_footer($conn, $firstname, $lastname, $email, $number,$destination, $location, $message);
                                
                                if($result== "OK") { 
                                    $destination_value = "";
                                    $location_value = "";
                                    $form_locations = [];
                                ?>
                                    <script type="text/javascript">
                                        function delay(ms) {
                                            return new Promise(resolve => setTimeout(resolve, ms));
                                        }
                                        
                                        delay(1000).then(() => {
                                            bootbox.alert({
                                                closeButton: false,
                                                size: "medium",
                                                message: "Thank you for your message. A staff member will contact you at the earliest possible time to discuss your requirement. Thank you."
                                            });
                                        });
                                        delay(1000).then(() => {
                                            document.getElementById("destination_footer").value = "";
                                            document.getElementById("location_footer").value = "";
                                        });
                                        
                                    </script>
                                    
                            <?php    
                                    
                                }else { ?>
                                    <!--<script type="text/javascript">-->
                                    <!--    function delay(ms) {-->
                                    <!--        return new Promise(resolve => setTimeout(resolve, ms));-->
                                    <!--    }-->
                                        
                                    <!--    delay(1000).then(() => {-->
                                    <!--        bootbox.alert({-->
                                    <!--            size: "medium",-->
                                    <!--            message: "<?php //echo $result; ?>"-->
                                    <!--        });-->
                                    <!--    });-->
                                    <!--</script>  -->
                             <?php   }
                            }
                        }
                        
                        ?>
                        <?php if (!empty($errors)) : ?>
                            <div class="error-messages">
                                <ul>
                                    <?php foreach ($errors as $error) : ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <form id="destination-form" name="destination-form" method="post" action="" >
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                       <input size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control" id="firstname" aria-required="true" aria-invalid="false" placeholder="First Name*" value="" type="text" name="firstname">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                       <input size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control" id="lastname" aria-required="true" aria-invalid="false" placeholder="Last Name*" value="" type="text" name="lastname">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                       <input size="40" class="wpcf7-form-control wpcf7-text wpcf7-tel wpcf7-validates-as-tel form-control" id="number" placeholder="Mobile" value="" type="tel" name="number">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email form-control" aria-required="true" aria-invalid="false" id="email" placeholder="Email*" value="" type="email" name="email">
                                    </div>

                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group destination-location" style="position: relative;">
                                        <select class="wpcf7-form-control wpcf7-select form-control col-sm-12" aria-invalid="false" name="destination_footer_id" id="destination_footer" value="<?php echo $destination_value; ?>">
                                            <option value="">Select Destination</option>
                                            <?php 
                                            foreach($form_des as $key=>$value) { 
                                            ?>
                                                <option value="<?php echo $key; ?>"
                                                    <?php
                                                    echo ($result != "OK" && ($destination_id == $key || $footer_destination_id == $key))
                                                        ? "selected"
                                                        : "";
                                                    ?>>
                                                    <?php echo $value; ?>
                                                </option>
                                            <?php    
                                            }?>
                                        </select>
                                            <svg class="loader" style="display: none;
                                                position: absolute;
                                                margin: 0 auto;
                                                top: 5px;
                                                left: 0;
                                                right: 0;" width="40px" height="40px" display="block" style="background:transparent;margin:auto" preserveAspectRatio="xMidYMid" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                            <g transform="rotate(0 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.9166666666666666s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(30 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.8333333333333334s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(60 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.75s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(90 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.6666666666666666s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(120 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.5833333333333334s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(150 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.5s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(180 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.4166666666666667s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(210 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.3333333333333333s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(240 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.25s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(270 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.16666666666666666s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(300 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="-0.08333333333333333s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            <g transform="rotate(330 50 50)">
                                            <rect x="47" y="24" width="6" height="12" rx="3" ry="6" fill="#fe718d">
                                            <animate attributeName="opacity" begin="0s" dur="1s" keyTimes="0;1" repeatCount="indefinite" values="1;0"/>
                                            </rect>
                                            </g>
                                            </svg>

                                    </div>
                                    <div class="">
                                        <div class="form-group destination-location" style="position: relative;">
                                            <select class="wpcf7-form-control wpcf7-select form-control col-sm-12 <?php echo $class; ?>" aria-invalid="false" name="location_footer_id" id="location_footer" value="<?php echo $location_value; ?>">
                                                <option value="">Select Location</option>
                                                    <?php 
                                                    if($form_locations && count($form_locations) > 0) {
                                                    foreach($form_locations as $key=>$value) { 
                                                    ?>
                                                        <option value="<?php echo $key; ?>" <?php echo $result=="OK" ? "" : ($location_footer_id==$key || $footer_location_id==$key ? "selected" : ""); ?>><?php echo $value; ?></option>
                                                    <?php    
                                                    } 
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group br-b">
                                        <textarea cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea form-control" aria-invalid="false" placeholder="Message" name="message" id="message"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="destination-content-form-actions">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="hidden" name="let-us-know-form" value="Submit">
                                            <input class="wpcf7-form-control has-spinner wpcf7-submit btn btn-primary btn-lock" type="submit" value="Submit">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>

            </div>
            
        </div>
    </section>
</div>

<!-- Start Footer -->
<footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <p><?php echo get_field("designed_by", "options"); ?></p>
                </div>
                <div class="col-md-6">
                    <ul class="social-icons">
                        <?php if(get_field("facebook", "options")) { ?>
                        <li><a target="_blank" href="<?php echo get_field("facebook", "options"); ?>"><i class="fab fa-facebook-f"></i></a></li>
                        <?php } ?>
                        
                        <?php if(get_field("instagram", "options")) { ?>
                        <li><a target="_blank" href="<?php echo get_field("instagram", "options"); ?>"><i class="fab fa-instagram"></i></a></li>
                        <?php } ?>

                        <?php if(get_field("linkedin", "options")) { ?>
                        <li><a target="_blank" href="<?php echo get_field("linkedin", "options"); ?>"><i class="fab fa-linkedin"></i></a></li>
                        <?php } ?>

                        <?php if(get_field("twitter", "options")) { ?>
                        <li><a target="_blank" href="<?php echo get_field("twitter", "options"); ?>"><i class="fab fa-twitter"></i></a></li>
                        <?php } ?>

                        <?php if(get_field("pinterest", "options")) { ?>
                        <li><a target="_blank" href="<?php echo get_field("pinterest", "options"); ?>"><i class="fab fa-pinterest"></i></a></li>
                        <?php } ?>

                        <?php if(get_field("phone", "options")) { ?>
                        <li><a href="tel: <?php echo get_field("phone", "options"); ?>"><i class="glyphicon glyphicon-earphone"></i></a></li>
                        <?php } ?>

                        <?php if(get_field("email", "options")) { ?>
                        <li><a href="mailto: <?php echo get_field("email", "options"); ?>"><i class="glyphicon glyphicon-envelope"></i></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <p class="float-left"><?php echo get_field("copyright_text", "options"); ?></p>
                </div>
            </div>
        </div>
    </footer>



<style>
        .checkbox-container {
            display: flex;
            align-items: center;
        }

        .checkbox-label {
            margin-right: 10px;
        }

        .checkbox-input {
            width: 20px;
            height: 20px;
            border: 1px solid #000;
            border-radius: 0;
        }

        /* Style for the recommend-popup */
        .recommend-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.9);
            z-index: 999;
        }

        .popup-content {
            max-height: 95%;
            overflow-y: auto;
        }

        /* Style for the close button */
        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        /* Style for the form */
        .form-container {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            padding: 40px;
            padding-top: 25px;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        #recommend-form .input-field {
            width: 48%;
            padding: 10px;
            max-width: 100%;
        }
        .recommend-select {
            width: 48%;
            position: relative;
        }
        
        #recommend-form .recommend-select .input-field {
            width: 100%;
            padding: 10px;
            max-width: 100%;
            background: white;
            appearance: none;
        }
        
        .recommend-select:after {
            content: "";
            position: absolute;
            width: 30px;
            height: 20px;
            right: 1px;
            top: 10px;
            background: white;
            margin-right: 2px;
            pointer-events: none;
            content: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"%3E%3Cpath d="M16.59 8.59L12 13.17L7.41 8.59L6 10L12 16L18 10L16.59 8.59Z" fill="black"/%3E%3C/svg%3E');
        }
        .comment-field {
            width: 100%;
            height: 100px;
            padding: 10px;
        }
        .recommend-form-header .img-block {
            max-width: 180px;
        }
        .recommend-form-header .img-block img{
            max-width: 100%;
        }
        .recommend-form-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-top: 15px;
        }
        .recommend-heading {
            max-width: 70%;
            margin-right: 30px;
        }
        .recommend-checkbox {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            margin-bottom: 15px;
        }
        .checkbox-container {
            width: 33%;
            display: flex;
            align-items: center;
        }
        .checkbox-label {
            margin: 5px;
        }
        input#recommend-submit-button {
            background: #1d2a7c;
            color: white;
            border: 1px solid;
            cursor: pointer;
            padding: 8px;
            width: 100%;
        }
        @media(max-width: 575px) {
            #recommend-popup .popup-content {
                max-width: 360px;
            }
            .recommend-form-header {
                flex-wrap: wrap;
                padding: 11px;
            }
            .recommend-form-header {
                margin-bottom: 0;
                flex-wrap: wrap;
                padding: 10px;
            }
            .recommend-form-header .img-block {
                max-width: unset;
                width: 100%;
            }
            .recommend-heading {
                max-width: unset;
                margin-right: 0;
                width: 100%;
            }
            .form-container {
                padding: 20px;
                padding-top: 3px;
            }
            .checkbox-container {
                width: 100%;
            }
            #recommend-form .input-field {
                width: 100%;
                margin-bottom: 10px;
            }
            .recommend-heading h3 {
                font-size: 22px;
            }
        }
        
        .content_1,.content_2,.content_3,.content_4 {
                    margin-bottom: 30px;
                    padding: 10px;
        }
        .bottom_content_section {
            margin: 20px 0px 40px;
        }
        
        
       .wp-block-latest-posts.wp-block-latest-posts__list li {
                margin: 10px 0;
                padding-bottom: 10px;
                border-bottom: 1px solid #ddd;
            }
        .wp-block-latest-posts.wp-block-latest-posts__list li a {
           color: #434343;
        }
        
    </style>

<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["recommend-form-submit"] ?? "") === "submit") {
    // Define an array to store validation errors
    $errors = [];

    // Check required fields
    $requiredFields = [
        "recommend-first-name" => "First Name Field",
        "recommend-last-name" => "Last Name Field",
        "recommend-email" => "Email Field",
        "recommend-destination" => "Destination Field",
        "recommend-comment" => "Comments Field"
    ];

    foreach ($requiredFields as $fieldName => $fieldLabel) {
        if (empty($_POST[$fieldName])) {
            $errors[] = $fieldLabel . " is required.";
        }
    }


    if (empty($errors)) {
        $firstName = $_POST["recommend-first-name"] ?? "";
        $lastName = $_POST["recommend-last-name"] ?? "";
        $email = $_POST["recommend-email"] ?? "";
        $destination = $_POST["recommend-destination"] ?? "";
        $location = $_POST["recommend-location"] ?? "";
        $adults = $_POST["recommend-adults"] ?? "";
        $children = $_POST["recommend-children"] ?? "";
        $comments = $_POST["recommend-comment"] ?? "";
        $dates_flexible = isset($_POST["recommend-dates-flexible"]) ? $_POST["recommend-dates-flexible"] : "0";
        $mobile = $_POST["recommend-mobile"] ?? "";
    
        $recommend_result = recommend($conn, $firstName, $lastName, $email, $mobile,$destination, $location, $comments, $adults, $children, $dates_flexible);

        if($recommend_result == "OK") {
    ?>
        <script type="text/javascript">
          function delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
          }
              
            delay(1000).then(() => {
                  bootbox.alert({
                    closeButton: false,
                    className:"recomment-success-alert",
                    size: "medium",
                    message: "Thank you for your message. A staff member will contact you at the earliest possible time to discuss your requirement. Thank you.",
                  });
              });
        </script> 
    <?php
        }
        
    } else {
        $error_msg = "";
        // Display validation errors
        foreach ($errors as $error) {
            $error_msg .= "<p style='color: red;'>$error</p>";
        }
    ?>
        <script type="text/javascript">
          function delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
          }
              
            delay(1000).then(() => {
                  bootbox.alert({
                    closeButton: false,
                    className:"recomment-alert",
                    size: "medium",
                    message: "<h4>You must correct the errors in the following fields:</h4><div class=''><?php echo $error_msg; ?></div>",
                  });
              });
        </script> 
    <?php
    }
}
?>

<div class="recommend-popup" id="recommend-popup">
        <div class="popup-content">
            <div class="recommend-form-header">
                <div class="img-block">
                    <img src="/wp-content/uploads/2023/03/vg-logo.jpg">
                </div>
                <div class="recommend-heading">
                    <h3>Request Recommendations</h3>
                    <p>We will send you hand picked properties matching your preferences</p>
                </div>
            </div>
            <form id="recommend-form" name="recommend-form" action="" method="post" class="form-container">
                <div class="form-row">
                    <input type="text" id="recommend-first-name" name="recommend-first-name" placeholder="* First Name" required class="input-field">
                    <input type="text" id="recommend-last-name" name="recommend-last-name" placeholder="* Last Name" required class="input-field">
                </div>

                <div class="form-row">
                    <input type="email" id="recommend-email" name="recommend-email" placeholder="* Email" required class="input-field">
                    <input type="tel" id="recommend-mobile" name="recommend-mobile" placeholder="Mobile" class="input-field">
                </div>

                <div class="form-row">
                    <div class="recommend-select">
                        <select id="recommend-destination" name="recommend-destination" required class="input-field">
                            <option value="">Select Destination</option>
                                <?php 
                                foreach($form_des as $key=>$value) { 
                                ?>
                                    <option value="<?php echo $key; ?>" <?php echo $result=="OK" ? "" : ($destination_id==$key || $footer_destination_id==$key ? "selected" : ""); ?>><?php echo $value; ?></option>
                                <?php    
                                }?>
                        </select>
                    </div>
                    <div class="recommend-select">
                        <select id="recommend-location" name="recommend-location" class="input-field">
                            <option value="">Select Location</option>
                                <?php 
                                if($form_locations && count($form_locations) > 0) {
                                    foreach($form_locations as $key=>$value) { 
                                    ?>
                                        <option value="<?php echo $key; ?>" <?php echo $result=="OK" ? "" : ($location_footer_id==$key || $footer_location_id==$key ? "selected" : ""); ?>><?php echo $value; ?></option>
                                    <?php    
                                } 
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="recommend-select">
                        <select id="recommend-adults" name="recommend-adults" class="input-field">
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
                    <div class="recommend-select">
                        <select id="recommend-children" name="recommend-children" class="input-field">
                            <option value="">Children</option>
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

                <h3 style=" margin-top: 20px;">Requirements:</h3>

                <div class="recommend-checkbox">
                    <div class="checkbox-container">
                        <input type="checkbox" id="recommend-swimming-pool" name="recommend-requirements[]" value="Swimming Pool" class="checkbox-input">
                        <label for="recommend-swimming-pool" class="checkbox-label">Swimming Pool</label>
                    </div>
    
                    <div class="checkbox-container">
                        <input type="checkbox" id="recommend-tennis-court" name="recommend-requirements[]" value="Tennis Court" class="checkbox-input">
                        <label for="recommend-tennis-court" class="checkbox-label">Tennis Court</label>
                    </div>
    
                    <div class="checkbox-container">
                        <input type="checkbox" id="recommend-dates-flexible" name="recommend-requirements[]" value="1" class="checkbox-input">
                        <label for="recommend-dates-flexible" class="checkbox-label">My Dates are Flexible</label>
                    </div>
    
                    <div class="checkbox-container">
                        <input type="checkbox" id="recommend-beach-front" name="recommend-requirements[]" value="Beach Front" class="checkbox-input">
                        <label for="recommend-beach-front" class="checkbox-label">Beach Front</label>
                    </div>
    
                    <div class="checkbox-container">
                        <input type="checkbox" id="recommend-close-to-shops" name="recommend-requirements[]" value="Close to Shops" class="checkbox-input">
                        <label for="recommend-close-to-shops" class="checkbox-label">Close to Shops</label>
                    </div>
                </div>

                <div class="form-row">
                    <textarea id="recommend-comment" name="recommend-comment" placeholder="* Comments" class="comment-field"></textarea>
                </div>
                <input type="hidden" name="recommend-form-submit" value="submit">
                <input id="recommend-submit-button" type="submit" value="Submit">
                <div id="error-message" style="color: red;"></div>

            </form>
            <div class="close-button" onclick="closeRecommendPopup()">&#x2715;</div>
        </div>
    </div>

    <script>
        function openRecommendPopup() {
            document.getElementById("recommend-popup").style.display = "flex";
        }

        function closeRecommendPopup() {
            document.getElementById("recommend-popup").style.display = "none";
        }
        
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        function toggleRedBorder(input, addRedBorder) {
            if (addRedBorder) {
                input.style.border = "1px solid red";
            } else {
                input.style.border = ""; // Remove red border
            }
        }

        document.getElementById("recommend-open-form-button").addEventListener("click", openRecommendPopup);

        document.getElementById("recommend-submit-button").addEventListener("click", function (event) {

            const firstName = document.getElementById("recommend-first-name");
            const lastName = document.getElementById("recommend-last-name");
            const email = document.getElementById("recommend-email");
            const destination = document.getElementById("recommend-destination");
            const comments = document.getElementById("recommend-comment");
            var errorMessage = "";
            var has_errors = false;
            // Function to add an error message to the error container
            function addErrorMessage(message) {
                errorMessage += '<div class="col-md-12">' + message + '</div>';
            }
        
            // Check for errors in each field
            if (firstName.value.trim() === "") {
                addErrorMessage("First Name is required.");
                has_errors = true;
            } 
        
            if (lastName.value.trim() === "") {
                addErrorMessage("Last Name is required.");
                has_errors = true
            } 
        
            if (email.value.trim() === "") {
                addErrorMessage("Email is required.");
                has_errors = true
            } 
        
            if (destination.value.trim() === "") {
                addErrorMessage("Destination is required.");
                has_errors = true
            } 
        
            if (comments.value.trim() === "") {
                addErrorMessage("Comments are required.");
                has_errors = true
            } 
            
            if (!validateEmail(email.value) && email.value) {
                addErrorMessage("Please enter a valid email address.");
                has_errors = true;
            }
            console.log(has_errors);
            if (has_errors) {
                event.preventDefault();
                if (errorMessage) bootbox.alert({
                    closeButton: false,
                    className: "recommend-error-alert",
                    message: '<h3>You must correct the errors in the following fields:</h3><div class="row">' + errorMessage + '</div>',
                });
                return false;
            }
            
            if (
                firstName.value.trim() === "" ||
                lastName.value.trim() === "" ||
                email.value.trim() === "" ||
                destination.value.trim() === "" ||
                comments.value.trim() === ""
            ) {
                
                toggleRedBorder(firstName, firstName.value.trim() === "");
                toggleRedBorder(lastName, lastName.value.trim() === "");
                toggleRedBorder(email, email.value.trim() === "");
                toggleRedBorder(destination, destination.value.trim() === "");
                toggleRedBorder(comments, comments.value.trim() === "");

                document.getElementById("error-message").textContent = "Please fill in all required fields.";
                event.preventDefault();
                return;
            } else {
                // Reset the error message
                document.getElementById("error-message").textContent = "";
                // Remove red borders
                toggleRedBorder(firstName, false);
                toggleRedBorder(lastName, false);
                toggleRedBorder(email, false);
                toggleRedBorder(destination, false);
                toggleRedBorder(comments, false);
                
                // Email validation
                if (!validateEmail(email.value)) {
                    document.getElementById("error-message").textContent = "Please enter a valid email address.";
                    event.preventDefault();
                    return;
                }
                
                const form = document.getElementById("recommend");
                const datesFlexible = document.getElementById("recommend-dates-flexible").checked ? "1" : "0";
                const selectedRequirements = [];
                const requirements = ["recommend-swimming-pool", "recommend-tennis-court", "recommend-beach-front", "recommend-close-to-shops"];
                for (const requirement of requirements) {
                    const checkbox = document.getElementById(requirement);
                    if (checkbox.checked) {
                        selectedRequirements.push(checkbox.value);
                    }
                }
    
                const commentField = document.getElementById("recommend-comment");
                const commentText = commentField.value;
                if (selectedRequirements.length > 0) {
                    const requirementsText = "Requirements - " + selectedRequirements.join(", ");
                    commentField.value = commentText + (commentText ? "\n" : "") + requirementsText;
                }
    
                // Update the hidden input for "Dates Flexible"
                const datesFlexibleInput = document.createElement("input");
                datesFlexibleInput.type = "hidden";
                datesFlexibleInput.name = "recommend-dates-flexible";
                datesFlexibleInput.value = datesFlexible;
                this.appendChild(datesFlexibleInput);
                form.submit();
            }
            
        });
    </script>
    
    
    <?php wp_footer(); ?>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://idangero.us/swiper/dist/js/swiper.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/popper.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/bootstrap.min.js"></script>

    <!-- Welcome Image Effects JavaScript -->
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/slippry.min.js"></script>
    <!-- COMMON JS -->

    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/owl.carousel.min.js"></script>

    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/thumbnail-slider.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/lightgallery/lightgallery.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/lightgallery/lg-thumbnail.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/lightgallery/lg-video.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/lightgallery/lg-autoplay.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/lightgallery/lg-zoom.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/lightgallery/lg-pager.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/lightgallery/lg-fullscreen.min.js"></script>
    <script src="https://unpkg.com/@fullcalendar/core@4.4.2/main.min.js"></script>
    <script src="https://unpkg.com/@fullcalendar/daygrid@4.4.2/main.min.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/select2.min.js"></script>
    <!--custom Script-->
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/common.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/enquiry-form.js"></script>
    <script>
        var calendar= null;
    </script>
    
    <?php 
    $url = $_SERVER['REQUEST_URI'];
    preg_match('/\d+/', $url, $matches);
    // Extract the matched integer value; URLs with no digits leave it 0.
    $vg_number = !empty($matches) ? (int) $matches[0] : 0;

    if( $vg_number ) {
        $event_dates = [];
        $conn = oracleDbConnection();
        $bedrooms = (isset($_COOKIE["__bedrooms"]) && $_COOKIE["__bedrooms"]) ? $_COOKIE["__bedrooms"] : 1;
        $date_start = ( isset($_COOKIE['__date_start']) && $_COOKIE['__date_start'] ) ? $_COOKIE['__date_start'] : date('d-m-Y', strtotime("today"));
        $villa_details = fetchVillaDetails($conn, $vg_number, $bedrooms);
        
        
        
        if(isset($villa_details) && count($villa_details) > 0) {
            $unavailable_dates = fetchUnavailableDates($conn, $villa_details["VILLA_ID"]);
            if( $unavailable_dates ) {
                foreach( $unavailable_dates AS $date ) {
                    $arrive = strtotime($date["ARRIVE"] ?? '');
                    $depart = strtotime($date["DEPART"] ?? '');
                    if( !$arrive || !$depart ) {
                        continue;
                    }
                    $temp_event_dates = [

                        "start" => date('Y-m-d', $arrive),
                        "end" => date('Y-m-d', $depart),
                        "rendering" => "background",
                        "backgroundColor" => "#ef4d4d",

                    ];
                    $event_dates[] = $temp_event_dates;
                }
            }
        }
        
        
           $conn = oracleDbConnection();
$bookedDates = fetchVillaBookedDates($conn,$villa_details["VILLA_ID"] ?? "");
$bookedDatesformatted =[];
//echo "<pre>";

foreach($bookedDates as $key => $dates){
   
    $bookedDatesformatted[$key]['ARRIVAL'] = formatDate($dates['ARRIVAL']);
    $bookedDatesformatted[$key]['CHECKOUT'] = $dates['CHECKOUT'];
    $bookedDatesformatted[$key]['LATE_CHECKOUT'] = $dates['LATE_CHECKOUT'];
    $bookedDatesformatted[$key]['ARRIVING'] = $dates['ARRIVING'];
    
}


if(!is_page('inquiry')) {
    
 $date_from = (isset($_COOKIE["__date_start"]) && $_COOKIE["__date_start"]) 
    ? date('Y-m-d', strtotime($_COOKIE["__date_start"])) 
    : "";

$date_to = (isset($_COOKIE["__date_end"]) && $_COOKIE["__date_end"]) 
    ? date('Y-m-d', strtotime($_COOKIE["__date_end"])) 
    : "";

    
?>
<div id="popup" class="popup" style="display: none;">
        <div class="popup-content modal-content inquiry-page">
            <div class="modal-header">
                <h3>Make an inquiry</h3>
                <span class="close" onclick="closeInquieryForm()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">   
                        <div class="hidden-xs">
                            <img name="villa_image" alt="Villa" src="">                                
                            <h4 id="villa-number"></h4>
                            <p id="bedandbath"></p>
                            <p>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                                <span class="glyphicon glyphicon-star"></span>
                            </p>
                        </div>                                
                    </div>
                    <div class="col-md-8">
                        <form id="inquiry_form" method="post" class="ajax" action="">
                            <input type="hidden" name="_villa_number" value="" />
                            <input type="hidden" name="_agent_id" value="" />
                            <input type="hidden" name="_villa_id" value="" />
                            <div class="row">
                                
                                    <!--<input type="text" id="datepickerHelper" style="padding: 0; border: none; line-height: 0; height: 0; position: absolute">-->
                                    <div class="inqiry-check-out form-group col-md-6" id="reservation-button-3">
                                    <!--<span class="input-group-addon" onclick="$('#reservation3_from').focus()"><span class="glyphicon glyphicon-calendar"></span></span>-->
                                        <input type="date" class="form-control" placeholder="Check-In" id="date_from" min="<?php echo $date_from; ?>" name="date_from" value="<?php echo $date_from; ?>" data-url="/getBookedDays/501/36/1" style="">
                                    </div>
                                    <div class="inqiry-check-in form-group col-md-6" id="reservation-button-3">
                                    <!--<span class="input-group-addon" onfocus="$('#reservation3_to').focus()"><span class="glyphicon glyphicon-calendar"></span></span>-->
                                        <input type="date" class="form-control" placeholder="Check-Out" id="date_to" min="<?php echo $date_to; ?>" name="date_to" value="<?php echo $date_to; ?>" data-url="/getBookedDays/501/36/1" style="">
                                    </div>
                                
                                    <div class="col-md-12 checkbox flexible-dates">
                                        <label><input type="checkbox" value="1" name="flexible" class="filled">
                                            My dates are flexible
                                        </label>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group night-budget">
                                            <select class="selectpicker white select-guests-2_inquiry" id="select-guests-2" required="" name="adults">
                                                <option value="0">Adults *</option>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select class="selectpicker white select-kids-2_inquiry" id="select-kids-2" required="" name="kids">
                                                <option value="0">Children Under 12</option>
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
                                <div class="col-md-12">
                                    
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
                                    <input type="text" class="form-control" placeholder="First Name *" required="" name="fname" id="fname" value="">
                                </div> 
                            
                                <div class="form-group col-md-5">
                                    <label class="sr-only" for="lname">Your Last Name</label>
                                    <input type="text" class="form-control" placeholder="Last Name *" required="" name="lname" id="lname" value="">
                                </div>
                            </div>  
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="sr-only" for="yourEmail">Your Email address</label>
                                    <input type="email" class="form-control" id="yourEmail" placeholder="Your Email *" required="" name="email" value="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="sr-only" for="yourPhone">Mobile Phone</label>
                                    <input type="text" class="form-control" placeholder="Mobile Phone"  name="phone" id="phone" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="sr-only" for="homePhone">Home Phone</label>
                                    <input type="text" class="form-control" placeholder="Home Phone" name="homePhone" id="homePhone" value="">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="sr-only" for="yourAddress">Address</label>
                                    <input type="text" class="form-control" placeholder="Address" name="address" id="address" value="">
                                </div>
                            </div>
                            
                            <div class="row">	
                                	
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
                                        <option value="">Country *</option>
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
                            <textarea class="form-control special-requirements" rows="3" placeholder="Are there any special requirements we can help you with?" name="questions"></textarea>
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
                            <button type="submit" name="submit" class="btn btn-success btn-lg btn-block" id="inquiry_form_submit" data-type="inquiry" data-url_check_captcha="/villas/checkCaptcha">
                            Send your inquiry 
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row" id="inquiry_rates">
                <div class="row">
                    
                    <?php //print_r($villa_details);?>
                    
                    <?php echo get_template_part('template-parts/villa/villa', 'ratespopup', ["villa_details" => $villa_details]); ?>
                </div>
            </div>
            <style>
                
            </style>
        </div>
</div>
<?php }


function remove_duplicate_arrivals($dates) {
    $seen = [];
    $unique = [];

    foreach ($dates as $entry) {
        $arrival = $entry['ARRIVAL'];

        if (!isset($seen[$arrival])) {
            $seen[$arrival] = true;
            $unique[] = $entry;
        }
    }

    return $unique;
}

$bookedDatesformatted = remove_duplicate_arrivals($bookedDatesformatted);

// A villa with no bookings leaves this empty; json_encode() below must still
// emit [] rather than null, or FullCalendar gets a null events list.
$event_dates2 = [];

foreach ($bookedDatesformatted as $entry) {

//print_r($entry);


        $arrival = strtotime($entry['ARRIVAL'] ?? '');
        if( !$arrival ) {
            continue;
        }
        $temp_event_dates = [

                        "start" => date('Y-m-d', $arrival),
                        "end" => date('Y-m-d', $arrival),
                        "classNames" => "booked",
                        //"backgroundColor" => "#ef4d4d",

                    ];

                    
                    
                      if ($entry['CHECKOUT'] === 'Y' && $entry['LATE_CHECKOUT']==0) {
                      $temp_event_dates['classNames'] = 'booked-half';
        
                      }
    
           if ($entry['ARRIVING'] === 'Y') {
            $temp_event_dates['classNames'] = 'booked-half-end';
           }
              
                                  $event_dates2[] = $temp_event_dates;      
                    
}


        
        
        ?>
        <script type="text/javascript">
        
            let event_dates = <?php echo json_encode($event_dates2); ?>;
            let date_start = "<?php echo $date_start; ?>";
            var dateComponents = date_start.split("-");
            let day = parseInt(dateComponents[0], 10);
            let month = parseInt(dateComponents[1], 10) - 1; // Month value in JavaScript starts from 0 (January is 0)
            let year = parseInt(dateComponents[2], 10);
            
            // Create a new Date object with the parsed components
            date_start = new Date(year, month, day);
            $(document).ready(function(){
                
                calendar = new FullCalendar.Calendar(document.getElementById('cal'), {
                    plugins: [ 'dayGrid' ],
                    contentHeight: 210,
                    header: {
                        left: 'prev',
                        center: 'title',
                        right: 'next'
                    },
                    defaultDate: date_start,
                    events: event_dates
                });
                calendar.render();
            });
        
        </script>
        <?php 
    }
    ?>
    
    <script type="text/javascript">
    
        function convertDate(date) {
            var dateComponents = date.split("-");
            let day = parseInt(dateComponents[0], 10);
            let month = parseInt(dateComponents[1], 10) - 1; // Month value in JavaScript starts from 0 (January is 0)
            let year = parseInt(dateComponents[2], 10);
            
            // Create a new Date object with the parsed components
            return convertedDate = new Date(year, month, day);
        }
        
        function fetchRates(id, dest_checkIn, dest_checkOut, bedrooms, percentage) {
            $.ajax({
                type : "POST",
                url : ajax_url.ajaxurl,
                data : {action: "fetch_rates_of_single_villa", id, dest_checkIn, dest_checkOut, bedrooms, percentage},
                success: function(ajaxResponse) {
                    
                    if(ajaxResponse.success) {
                        // Get a reference to the rates div
                        var ratesDiv = document.querySelector('.rates-block');
                        // Check if rates div exists and percentage is 0
                        if (!document.querySelector('.rates-block') && percentage == "0") {
                            var ratesDiv = document.createElement('div');
                            ratesDiv.className = 'rates-block';
            
                            var ratesSpan = document.createElement('span');
                            ratesSpan.textContent = "Rates: ";
                            ratesDiv.appendChild(ratesSpan);
            
                            var ratesValueSpan = document.createElement('span');
                            ratesValueSpan.textContent = "US$ " + ajaxResponse.data.rates + " per night";
                            ratesDiv.appendChild(ratesValueSpan);
            
                            var nightsSpan = document.createElement('span');
                            nightsSpan.textContent = "Total " + ajaxResponse.data.diff + " night" + (parseInt(ajaxResponse.data.diff) > 1 ? "s" : "") + ":";
                            ratesDiv.appendChild(nightsSpan);
            
                            var totalSpan = document.createElement('span');
                            totalSpan.textContent = "US$ " + ajaxResponse.data.total;
                            ratesDiv.appendChild(totalSpan);
            
                            document.querySelector('.rates').appendChild(ratesDiv);
                        }
                        // Check if rates div exists and percentage is not 0
                        else if (!document.querySelector('.rates-block') && percentage != "0") {
                            var ratesDiv = document.createElement('div');
                            ratesDiv.className = 'rates-block';
            
                            var ratesSpan = document.createElement('span');
                            ratesSpan.textContent = "Rates: ";
                            ratesDiv.appendChild(ratesSpan);
            
                            var ratesValueSpan = document.createElement('span');
                            ratesValueSpan.textContent = "US$ " + ajaxResponse.data.rates + " per night";
                            ratesDiv.appendChild(ratesValueSpan);
            
                            var taxSpan = document.createElement('span');
                            taxSpan.textContent = "Tax " + percentage + "% :";
                            ratesDiv.appendChild(taxSpan);
            
                            var taxValueSpan = document.createElement('span');
                            taxValueSpan.textContent = "$ " + ajaxResponse.data.tax + " tax";
                            ratesDiv.appendChild(taxValueSpan);
            
                            var nightsSpan = document.createElement('span');
                            nightsSpan.textContent = "Total " + ajaxResponse.data.diff + " night" + (parseInt(ajaxResponse.data.diff) > 1 ? "s" : "") + ":";
                            ratesDiv.appendChild(nightsSpan);
            
                            var totalSpan = document.createElement('span');
                            totalSpan.textContent = "US$ " + ajaxResponse.data.total;
                            ratesDiv.appendChild(totalSpan);
            
                            document.querySelector('.rates').appendChild(ratesDiv);
                        }
                        else {
                            if (percentage != 0) {
                                var ratesSpan = ratesDiv.querySelector('span:nth-of-type(2)');
                                
                                var taxesSpan = ratesDiv.querySelector('span:nth-of-type(4)');
                                taxesSpan.textContent = "US$ " + ajaxResponse.data.tax;
                                
                                var nightsSpan = ratesDiv.querySelector('span:nth-of-type(5)');
                                var totalSpan = ratesDiv.querySelector('span:nth-of-type(6)');
                                // Update the number of nights
                                
                            }else {
                                var ratesSpan = ratesDiv.querySelector('span:nth-of-type(2)');
                                
                                var nightsSpan = ratesDiv.querySelector('span:nth-of-type(3)');
                                
                                // Update the total value
                                var totalSpan = ratesDiv.querySelector('span:nth-of-type(4)');
                            }
                            
                            
                            ratesSpan.textContent = "US$ " + ajaxResponse.data.rates + " per night";
            
                            if (parseInt(ajaxResponse.data.diff) > 1) {
                              nightsSpan.textContent = "Total " + ajaxResponse.data.diff + " night(s):";
                            }else {
                                nightsSpan.textContent = "Total " + ajaxResponse.data.diff + " night:";
                            }
                            
                            totalSpan.textContent = "US$ " + ajaxResponse.data.total;
                        }
                    }
    
                },
                error:function(error){
                    console.log('message Error' + JSON.stringify(error));
                }                    
            });
        }
        
        
        $(document).ready(function(){
         
            // Attach onSelect event handler to the from-date input
            $("#search-fromdate, #filterSidebar_from").datepicker("option", "onSelect", function(selectedDate, inst) {
                $("#search-todate").datepicker().trigger('blur');
                
                // Parse the selected date string into a Date object
                let toDateString = getThreeDayFromToday(selectedDate);
                // Split the date string into day, month, and year components
                
                // Set the to-date input value to the calculated date string
                $("#search-todate, #filterSidebar_to").datepicker("option", "minDate", selectedDate);
                $("#search-todate, #filterSidebar_to").val(toDateString);
                
                setTimeout(function() {
                    $("#search-todate").datepicker('show');
                }, 100);
            });
            
            // Attach onSelect event handler to the from-date input
            $("#dest_checkIn").datepicker("option", "onSelect", function(selectedDate, inst) {
                // Parse the selected date string into a Date object
                let toDateString = getThreeDayFromToday(selectedDate);
                // Split the date string into day, month, and year components
                var dateComponents = selectedDate.split("-");
                var day = parseInt(dateComponents[0], 10);
                var month = parseInt(dateComponents[1], 10) - 1; // Month value in JavaScript starts from 0 (January is 0)
                var year = parseInt(dateComponents[2], 10);
                
                // Create a new Date object with the parsed components
                var newSelectedDate = new Date(year, month, day);
                // Destroy previous calendar instance if exists
                if (calendar) {
                    calendar.gotoDate(newSelectedDate);
                    //calendar.destroy();
                }
                
                // Set the to-date input value to the calculated date string
                $("#dest_checkOut").datepicker("option", "minDate", selectedDate);
                $("#dest_checkOut").val(toDateString);
                
                let bedrooms = $('#inquiry_room').val();
                let percentage = $('#villa_percentage').val();
                let id = $('#villa_id').val();
                

                fetchRates(id, selectedDate, toDateString, bedrooms, percentage);
                
                
            });
            
        
            
            //Note: this script should be placed at the bottom of the page, or after the slider markup. It cannot be placed in the head section of the page.
            var thumbs1 = document.getElementById("thumbnail-slider");
            var thumbs2 = document.getElementById("thumbs2");
            var closeBtn = document.getElementById("closeBtn");
            if( thumbs1 && thumbs2 && closeBtn ) {
                var slides = thumbs1.getElementsByTagName("li");
                for (var i = 0; i < slides.length; i++) {
                    slides[i].index = i;
                    slides[i].onclick = function(e) {
                        var li = this;
                        var clickedEnlargeBtn = false;
                        if (e.offsetX > 220 && e.offsetY < 25) clickedEnlargeBtn = true;
                        if (li.className.indexOf("active") != -1 || clickedEnlargeBtn) {
                            thumbs2.style.display = "block";
                            mcThumbs2.init(li.index);
                        }
                    };
                }
            
                thumbs2.onclick = closeBtn.onclick = function(e) {
                    //This event will be triggered only when clicking the area outside the thumbs or clicking the CLOSE button
                    thumbs2.style.display = "none";
                };
            }
        });
        
        
    </script>
  
    <script>
    function openInquieryForm(element) {
        var villaNumber = element.getAttribute('data-villa-number');
        var villaId = element.getAttribute('data-villa-id');
        var villaImage = element.getAttribute('data-villa-image');
        var villaBedrooms = element.getAttribute('data-bedrooms');
        var villaBathrooms = element.getAttribute('data-bathrooms');
        var agentId = element.getAttribute('data-agentId');
        
        var villaNumberField = document.querySelector('input[name="_villa_number"]');
        var villaIdField = document.querySelector('input[name="_villa_id"]');
        var villaImageField = document.querySelector('img[name="villa_image"]');
        var villaAgentIdField = document.querySelector('input[name="_agent_id"]');
        var bedandbathParagraph = document.getElementById('bedandbath');
        
        var villParagraph = document.getElementById('villa-number');
        villParagraph.innerText = 'Villa ' + villaNumber;
        bedandbathParagraph.innerText = villaBedrooms + ' bedrooms, ' + villaBathrooms + ' bathrooms';

        villaNumberField.value = villaNumber;
        villaIdField.value = villaId;
        villaImageField.src = villaImage;
        villaAgentIdField.value = agentId;
        
        var popup = document.getElementById('popup');
        popup.style.display = 'block';
    }
    
    function closeInquieryForm() {
        var popup = document.getElementById('popup');
        popup.style.display = 'none';
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Get a reference to the form element
  const form = document.getElementById('destination-form');

  // Add an event listener for form submission
  form.addEventListener('submit', function(event) {
    // Prevent the form from submitting by default
    event.preventDefault();
    // Perform form validation
    if (validateForm()) {
      // If the form is valid, you can submit it here
      form.submit();
    }
  });
  
  // Add event listeners to input fields to remove error messages
  const inputFields = document.querySelectorAll('#firstname, #lastname, #number, #email, #message');
  inputFields.forEach(function(field) {
    field.addEventListener('input', function() {
      const fieldId = field.getAttribute('id');
      removeErrorMessage(fieldId);
    });
  });

  // Add event listener to the destination select field to remove error message
  const destinationField = document.getElementById('destination_footer');
  destinationField.addEventListener('change', function() {
    removeErrorMessage('destination_footer');
  });
  
  
  // Trigger form validation on page load
  //validateForm();
});

// Function to remove the error message for a specific field
function removeErrorMessage(fieldId) {
  const field = document.getElementById(fieldId);
  const errorMessage = field.parentNode.querySelector('.error-message');
  if (errorMessage) {
    errorMessage.remove();
    field.classList.remove('error');
  }
}

// Function to validate the form
function validateForm() {
  // Reset any previous error messages
  resetErrorMessages();

  // Get form input values
  const firstName = document.getElementById('firstname').value;
  const lastName = document.getElementById('lastname').value;
  const number = document.getElementById('number').value;
  const email = document.getElementById('email').value;
  const destinationSelect = document.getElementById('destination_footer');
  const destination = destinationSelect.options[destinationSelect.selectedIndex].value;
  const message = document.getElementById('message').value;

  // Flag to track form validity
  let isValid = true;

  // Validate each input field
  if (firstName.trim() === '') {
    isValid = false;
    // Display an error message for the first name field
    displayErrorMessage('Please enter your first name', 'firstname');
  }
  if (lastName.trim() === '') {
    isValid = false;
    // Display an error message for the last name field
    displayErrorMessage('Please enter your last name', 'lastname');
  }
//   if (number.trim() === '') {
//     isValid = false;
//     // Display an error message for the number field
//     displayErrorMessage('Please enter your phone number', 'number');
//   }
  if (email.trim() === '') {
    isValid = false;
    // Display an error message for the email field
    displayErrorMessage('Please enter your email address', 'email');
  } else if (!isValidEmail(email)) {
    console.log(!isValidEmail(email), '!isValidEmail(email)')
    isValid = false;
    // Display an error message for invalid email format
    displayErrorMessage('Please enter a valid email address', 'email');
  }

console.log(destination, 'destination')
  if (destination == '') {
    isValid = false;
    // Display an error message for the destination field
    displayErrorMessage('Please select a destination', 'destination_footer');
  }

//   if (message.trim() === '') {
//     isValid = false;
//     // Display an error message for the message field
//     displayErrorMessage('Please enter your message', 'message');
//   }

  return isValid;
}

// Function to display an error message for a specific field
function displayErrorMessage(message, fieldId) {
  const field = document.getElementById(fieldId);
  const errorMessage = document.createElement('span');
  errorMessage.classList.add('error-message');
  errorMessage.textContent = message;

  // Check if an error message is already displayed
  const existingErrorMessage = field.parentNode.querySelector('.error-message');
  if (existingErrorMessage) {
    // If an error message already exists, replace it
    field.parentNode.replaceChild(errorMessage, existingErrorMessage);
  } else {
    // Otherwise, append the error message after the field
    field.parentNode.appendChild(errorMessage);
  }

  // Add a CSS class to indicate the field has an error
  field.classList.add('error');
}

// Function to reset all error messages and field styles
function resetErrorMessages() {
  const errorMessages = document.querySelectorAll('.error-message');
  errorMessages.forEach(function(errorMessage) {
    errorMessage.remove();
  });

  const fields = document.querySelectorAll('.error');
  fields.forEach(function(field) {
    field.classList.remove('error');
  });
}

// Function to check if an email is valid using a regular expression
function isValidEmail(email) {
  // Regular expression pattern for email validation
  const emailPattern = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
  console.log(emailPattern.test(email))
  return emailPattern.test(email);
}

document.addEventListener('DOMContentLoaded', function() {
    // Select all <p> elements inside the .banner-description
    let parentDiv = document.querySelector('.banner-description');
    let pElements = document.querySelectorAll('.banner-description p');
    
    if (pElements.length > 1) { 
        // Create the Read More button
        let readMoreButton = document.createElement('a');
        readMoreButton.textContent = "Read More";
        readMoreButton.href = "javascript:void(0)";
        readMoreButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.textContent === "Read More") {
                for (let i = 1; i < pElements.length; i++) {
                    pElements[i].style.display = 'block';
                }
                this.textContent = "Read Less";
                parentDiv.appendChild(this); // Move the link to the end
            } else {
                for (let i = 1; i < pElements.length; i++) {
                    pElements[i].style.display = 'none';
                }
                this.textContent = "Read More";
                pElements[0].after(this);
            }
        });

        // Hide all <p> elements except the first one
        // for (let i = 1; i < pElements.length; i++) { 
        //     pElements[i].style.display = 'none';
        // }

        // Insert the Read More button after the first <p> element
        pElements[0].parentNode.insertBefore(readMoreButton, pElements[0].nextSibling);
    }
});


</script>
</body>

</html>
