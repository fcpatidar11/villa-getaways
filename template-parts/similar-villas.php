<?php 
$conn = oracleDbConnection();

$similar_villas = fetchSimilarLaxuryVillas($conn, $args["villa_details"]["DESTINATION_ID"], $args["villa_details"]["VILLA_ID"],$args["villa_details"]['BEDS']);
if($similar_villas) {
    foreach($similar_villas AS $similar_villa) {
        $destination_name = strtolower(str_replace(" ", "-", $similar_villa['DESTINATION_NAME']));
        $location_name = strtolower(str_replace(" ", "-", $similar_villa['LOCATION_NAME']));
        $slug = "villa-rentals-" . $location_name . "-" . $similar_villa["VG_NUMBER"];
        ?>
        <div class="col-lg-4 col-md-12" style="padding-right: 15px;padding-left: 15px;">
            <div class="similar dest-one">
                <a href="<?php echo home_url($destination_name . "/" . $slug); ?>.html">
                    <img src="<?php echo home_url("/wp-content/uploads/" . $similar_villa["RANDOM_VILLA_IMAGE"]); ?>">
                    <div class="dest-similar-infoBar">
                        <div class="row">
                            <div class="col-2 col-xs-2">
                                <div class="img-wrap float-left">
                                    <span class="dest-similar-beds"><?php echo $similar_villa["BEDS"]; ?></span>
                                </div>
                            </div>
                            <div class="col-8 col-xs-8">
                                <span class="text-center dest-similar-villaName">Villa <?php echo $similar_villa["VG_NUMBER"]; ?></span>
                            </div>
                            <div class="col-2 col-xs-2">
                                <div class="img-wrap float-right">
                                    <span class="dest-similar-people"><?php echo $similar_villa["SLEEPS"]; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php 
    }
}
?>