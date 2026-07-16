<?php
if ( ! function_exists( 'generateStarRating' ) ) {
    function generateStarRating($rating) {
        $maxStars = 5; // The maximum number of stars in the rating
        $output = '';
        // Fill in the stars based on the rating
        for ($i = 1; $i <= $maxStars; $i++) {
            if ($i <= $rating) {
                // If the current star is less than or equal to the rating, fill it in
                $output .= '<span class="fa fa-star checked"></span>';
            } else {
                // Otherwise, leave it empty
                // $output .= '<span class="fa fa-star"></span>';
            }
        }
        // return $output . " (<strong>" . $rating . "/10</strong>)";
        return $output;
    }
}
?>
<style type="text/css">
.checked {
    color: orange;
}
</style>
<div class="col-lg-12">
    <div class="destination-content pt-5">
        <h2>Reviews</h2>
        <div class="destination-content-action">
            <?php 
            if(!empty($args["villa_reviews"])) {
                foreach( $args["villa_reviews"] AS $villa_review ) {
                    
                    
                    ?>
                    
                    
                    <div>
                        <strong><?php echo $villa_review["NAME"]; ?></strong> said:
                        <?php echo ($villa_review["RATING"]) ? "<p>" . generateStarRating($villa_review["RATING"]) . " | ".$villa_review["DATE_STAYED"]."</p>": ""; ?>
                        <?php echo ($villa_review["TITLE"]) ? "<h3>" . $villa_review["TITLE"] . "</h3>": ""; ?>
                        <?php echo ($villa_review["DESCRIPTION"]) ? "<p>" . $villa_review["DESCRIPTION"] . "</p>": ""; ?>
                        
                        <hr />
                    </div>
                    <?php 
                }
            }
            ?>
        </div>
    </div>
</div>