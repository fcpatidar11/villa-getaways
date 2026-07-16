
<div class="col-lg-6">
    <div class="destination-content">
        <h2><?php echo $args["villa_details"]["DESTINATION_NAME"]; ?></h2>
        <div class="destination-content-action">
            <p>
                <?php echo $args["villa_details"]["LONG_DESTINATION_DESCRIPTION"]; ?>
            </p>
        </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="right-aside">
        <div class="img-wrap">
            <img src="<?php echo home_url("/wp-content/uploads/" . $args["villa_details"]["DESTINATION_IMAGE"]); ?>" alt="Destionation Image">
        </div>
    </div>
</div>