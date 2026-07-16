<?php 
/* Template Name: Sitemap */
get_header();

$conn = oracleDbConnection();
$all_destinations = fetchDestinationsForMenu($conn);
$all_villas = fetchAllVillas($conn);
?>

<style type="text/css">
    ul.sitemap-links {
        list-style-type: none;
        padding: 0;
        margin: 0;
        column-count: 4;
        margin-top: 40px;
        margin-bottom: 20px;
    }
    ul.sitemap-links li {
        margin-bottom: 20px;
    }
    ul.sitemap-links li a {
        color: #000;
        text-decoration: none;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>

<!-- Start Main -->
<div class="contact-info-bar">
        <div class="row">
            <div class="col-xl-12">
                <h4 class="page-title">Sitemap</h4>
            </div>
        </div>
</div>
<!-- ./ End Main -->

<!-- Start Main -->
<main class="position-relative">
    <div class="welcome-banner-inner">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/contact-page-banner.jpg" alt="demo1_1">
    </div>
</main>
<!-- ./ End Main -->

<section class="contact-sec">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <ul class="sitemap-links">
                <?php
                // All Destinations, Location, and Regions
                if(sizeof($all_destinations)) {
                    foreach ($all_destinations as $destination) {
        			    $country_name = strtolower(str_replace(" ", "-", $destination['COUNTRY']));
        			    $destination_id = $destination['DESTINATION_ID'];
        			    ?>
        			    <li class="sitemap-link">
                            <a href="<?php echo home_url('destination/villa-rentals-' . $country_name); ?>">
        			            <?php echo $destination['COUNTRY']; ?>
        		            </a>
    		            </li>
        			    <?php
        			    $locations = $destination['LOCATIONS'];
			            if(sizeof($locations)) {
			                foreach ($locations as $location_id => $location) {
			                    $location_name = strtolower($location["LOCATION_URL_NAME"]);
    	    		            ?>
        			            <li class="sitemap-link">
        			                <a href="<?php echo home_url('destination/villa-rentals-' . $country_name . '-in-' . $location_name); ?>">
        			                    <?php echo $location['LOCATION_NAME']; ?>
    			                    </a>
        			            </li>
        			            <?php
			                }
			            }
                    }
                }
                
                // All Villas
                if( sizeof($all_villas) ) {
                    foreach( $all_villas AS $villa ) {
                        $des_name = strtolower(str_replace(" ", "-", $villa['DESTINATION_NAME']));
                        $loc_name = strtolower(str_replace(" ", "-", $villa['LOCATION_URL_NAME']));
                        $slug = "villa-rentals-" . $loc_name . "-" . $villa["VG_NUMBER"];
                        ?>
                        <li class="sitemap-link">
                            <a href="<?php echo home_url($des_name . "/" . $slug); ?>.html" class="btn">
                                <?php echo $villa["LOCATION_NAME"] . " Villa " . $villa["VG_NUMBER"]; ?>
                            </a>
			            </li>
                        <?php
                    }
                }
                
                // All Pages
                $pages = get_pages();
                if( $pages ) {
                    foreach( $pages as $page ) {
                        ?>
                        <li class="sitemap-link">
                            <a href="<?php echo home_url($page->post_name); ?>">
                                <?php echo $page->post_title; ?>
                            </a>
                        </li>
                        <?php
                    }
                }
                ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>