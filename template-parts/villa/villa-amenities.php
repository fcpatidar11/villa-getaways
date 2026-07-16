<div class="col-lg-12">
    <div class="destination-content">
        <div class="amenities-action-links" id="dest_tab">
            <?php if( !empty($args["villa_details"]["OCEANFRONT"]) ) { ?>
            <a class="" href="#" data-toggle="tab">
                <img src="<?php echo get_template_directory_uri() . "/assets/images/amenities/ocean-front.svg"; ?>">
                <br>Ocean Front</a>
            <?php } ?>
            
            <?php if( !empty($args["villa_details"]["OCEANVIEW"]) ) { ?>
            <a class="" href="#" data-toggle="tab">
                <img src="<?php echo get_template_directory_uri() . "/assets/images/amenities/ocean-view.svg"; ?>">
                <br>Ocean View</a>
            <?php } ?>
            
            <?php if( !empty($args["villa_details"]["POOL"]) ) { ?>
            <a class="" href="#" data-toggle="tab">
                <img src="<?php echo get_template_directory_uri() . "/assets/images/amenities/pool.svg"; ?>">
                <br>Pool</a>
            <?php } ?>
            
            <?php if( !empty($args["villa_details"]["AC"]) ) { ?>
            <a class="" href="#" data-toggle="tab">
                <img src="<?php echo get_template_directory_uri() . "/assets/images/amenities/aircond.svg"; ?>">
                <br>Airconditioner</a>
            <?php } ?>
            
            <?php if( !empty($args["villa_details"]["MAID"]) ) { ?>
            <a class="" href="#" data-toggle="tab">
                <img src="<?php echo get_template_directory_uri() . "/assets/images/amenities/maid.svg"; ?>">
                <br>Maid</a>
            <?php } ?>
            
            <?php if( !empty($args["villa_details"]["CHEF"]) ) { ?>
            <a class="" href="#" data-toggle="tab">
                <img src="<?php echo get_template_directory_uri() . "/assets/images/amenities/chef.svg"; ?>">
                <br>Chef</a>
            <?php } ?>
            
            <?php if( !empty($args["villa_details"]["BROADBAND"]) ) { ?>
            <a class="" href="#" data-toggle="tab">
                <img src="<?php echo get_template_directory_uri() . "/assets/images/amenities/wifi.svg"; ?>">
                <br>Broadband</a>
            <?php } ?>
            
            <?php if( !empty($args["villa_details"]["DAILY_BREAKFAST"]) ) { ?>
            <a class="" href="#" data-toggle="tab">
                <img src="<?php echo get_template_directory_uri() . "/assets/images/amenities/breakfast.svg"; ?>">
                <br>Daily Breakfast</a>
            <?php } ?>
            
            <?php if( !empty($args["villa_details"]["CAR_AND_DRIVER"]) ) { ?>
            <a class="" href="#" data-toggle="tab">
                <img src="<?php echo get_template_directory_uri() . "/assets/images/amenities/car.svg"; ?>">
                <br>Car and Driver</a>
            <?php } ?>
        </div>
    </div>
</div>