<?php
$conn = oracleDbConnection();
$villa_images = fetchVillaSliderImages($conn, ($args["villa_details"]["VILLA_ID"] ?? ""));
//($args["villa_details"]["VILLA_ID"] ?? "") = "848";
$villa_videoes = fetchVillaVideos($conn, ($args["villa_details"]["VILLA_ID"] ?? ""));


?>
<div class="directement-content">
    <div id="property-image">
      
            
                    <div class="hero"><img width="600" src="<?php echo home_url("/wp-content/uploads/" . $villa_images['0']); ?>"></div>
    
    </div>
</div>