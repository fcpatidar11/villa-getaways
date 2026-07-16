<?php 
/* 
    Template Name: Favorites Villas

*/
get_header();

$conn = oracleDbConnection();
$heading = "";
$image = "2023/03/menu-villas-4.png";
$destination_id = ( isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] ) ? $_REQUEST['destination_id'] : "";
$location_ids_arr = [];
$locations = [];
$result = "";
$location_name = "";
$destination_name = "";
$url_path = trim(parse_url(add_query_arg(array()), PHP_URL_PATH), '/');
$heading = "Favourites Villas";
$vg_numbers = implode(',', explode('|', $_COOKIE["__favorites_villas"]));
if($vg_numbers) {
    $destination = fetchFavoritesVillaDetails($conn, $vg_numbers);
}
?>

<!-- Start Main -->
<main class="position-relative">
    <ul id="page-banner">
        <li title="<?php echo $heading; ?>">
            <img src="<?php echo home_url("/wp-content/uploads/" . $image); ?>" alt="<?php echo $heading; ?>">
        </li>
    </ul>
</main>
<!-- ./ End Main -->


<!-- Start Content -->
<div class="container-fluid">
    <section class="pro-wrapper">

        <div class="row">
            <div class="col-md-12 col-lg-9">
                <div class="filterBar">
                    <div class="col-8 float-left">
                        <?php $title = $heading ? $heading : ( $destination_name ? $destination_name : $location_name ); 
                        ?>
                        <h3><?php echo strtoupper(str_replace("-", " ", $title)); ?></h3>
                    </div>
                    <!--<div class="col-4 float-right">-->
                    <!--    <div class="float-right filterBarRooms">-->
                    <!--        <?php //echo get_template_part('template-parts/bedrooms', 'form'); ?>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>
                
                <!-- Villa Listings -->
                <?php echo get_template_part('template-parts/villa', 'info', ["destination" => $destination]); ?>
                
            </div>
            <div class="col-md-12 col-lg-3">
                <div class="filterSidebar">
                    <div class="header">
                        <div class="input-group date-range">
                            <?php //echo get_template_part('template-parts/dates', 'form'); ?>
                        </div>
                    </div>
                    <?php if(!is_page('favourites')) { ?>
                    <div class="filterOpt">
                        <a class="alpha-acc-link" data-toggle="collapse" href="#filterLocations" role="button" aria-expanded="false" aria-controls="filterLocations">
                            REGIONS & LOCATIONS
                        </a>
                        <div class="collapse alpha-acc-body" id="filterLocations">
                            <?php 
                            $country_name = strtolower(str_replace(" ", "-", $heading));
                            if(is_array($locations) && sizeof($locations) > 0){
                                foreach($locations as $location) {
                                $location_name = strtolower(str_replace(" ", "-", $location));
                            
                            ?>
                            <div class="filterRegions">
                                <a href="<?php echo home_url('destination/villa-rentals-' . $country_name . '-in-' . $location_name); ?>" class="sub-acc-link collapsed" >
                                    <?php echo $location; ?>
                                </a>
                            </div>
                            <?php } ?>
                        <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <!-- Filter Sidebar End -->
                
                <div class="sidebar-nav">
                    <ul class="top">
                        <?php
                        if(have_rows('experience_menu_items', 'options')):
                            while(have_rows('experience_menu_items', 'options')): the_row();
                                if(get_sub_field('title') == "Holiday Season Villas") {
                                    $url = home_url(get_sub_field('link')."&page=1");
                                }else {
                                    $url = home_url(get_sub_field('link')."?page=1"); 
                                }
                        ?>
                        <li style="background: url(<?php the_sub_field('image'); ?>) center center no-repeat; background-size: cover;">
                            <a href="<?php echo $url; ?>"><?php the_sub_field('title'); ?></a>
                        </li>
                        <?php
                            endwhile;
                        endif;
                        ?>
                    </ul>
                </div>
                
                </div>
            </div>
        </div>
    </section>

    <?php //echo get_template_part('template-parts/team', 'member'); ?>

</div>

<?php get_footer(); ?>