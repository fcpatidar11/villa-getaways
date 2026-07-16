<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12"></div>
<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 villa-floor-plans">
    <div class="card__block card-img-slider-1 owl-carousel">
        <?php 
        if( !empty($args["villa_floor_plans"])) {
            foreach($args["villa_floor_plans"] AS $villa_floor_plan) {
            ?>
            <div class="img-wrapper">
                <img class="owl-lazy" data-src="<?php echo home_url("/wp-content/uploads/" . $villa_floor_plan); ?>" alt="Villa Floor Plan" srcset="">
            </div>
            <?php
            }
        } else {
            ?>
            <div class="img-wrapper">
                <img class="owl-lazy" data-src="<?php echo home_url("/wp-content/uploads/" . ($args["villa_details"]["RANDOM_VILLA_IMAGE"] ?? "")); ?>" alt="Destination Image" srcset="">
            </div>
          https://wptest.villagetaways.com/wp-admin/admin.php?page=wpide#  <?php
        }
        ?>
    </div>
</div>
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12"></div>