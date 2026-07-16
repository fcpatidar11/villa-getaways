<?php 
/* Template Name: Landing */
get_header();
?>

<!-- Start Main -->
<main class="position-relative">
    <ul id="pictures-demo">
        <?php
        if( have_rows('slider') ):
            $index = 1;
            while( have_rows('slider') ) : the_row();
                ?>
                <li>
                    <img src="<?php echo get_sub_field('slide_image'); ?>" alt="Slide <?php echo $index; ?>">
                </li>
                <?php
                $index++;
            endwhile;
        endif;
        ?>
    </ul>
    
    <div class="welcome w-100">
        <div class="welcome-warm-section">
            <div class="container">
                <div class="row  animated fadeInDown">
                    <div class="col-12 col-sm-12 col-md-12 pr-md-4 text-center text-sm-center text-md-right">
                        <h2><?php echo get_field('sub_heading'); ?></h2>
                        <h3><?php echo get_field('heading'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="search-box animated fadeInDown">
                    <div class="search-box-form home-form">
                        <?php echo get_template_part('template-parts/search', 'form'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- ./ End Main -->

<!-- Start Content -->
<div class="container-fluid no-padding">

    <section class="promos-wrapper">

        <div class="row no-gutters">
            <?php
            if( have_rows('villa_types') ):
                $index = 1;
                while( have_rows('villa_types') ) : the_row();
                    ?>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                        <div class="promos">
                            <img src="<?php echo get_sub_field('image'); ?>" alt="<?php echo get_sub_field('title'); ?>">
                            <div class="promo-text">
                                <h3>
                                    <span>
                                        <a href="<?php echo get_sub_field('link'); ?>"><?php echo get_sub_field('title'); ?></a>
                                    </span>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <?php
                    $index++;
                endwhile;
            endif;
            ?>
        </div>
    </section>

    <section class="destinations">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="destination-content pt-5">
                        <h2><?php echo get_field('statistics_heading'); ?></h2>
                        <?php echo get_field('statistics_content'); ?>
                        <div class="destination-content-action">
                            <h3><?php echo get_field('statistics_sub_heading'); ?></h3>
                            <div class="destination-action-links">

                                <ul>
                                    <li>
                                        <span title="Luxury Car Hire" class="car"></span>
                                    </li>
                                    <li>
                                        <span title="Private Jet Charter" class="departures"></span>
                                    </li>
                                    <li>
                                        <span title="Luxury Yacht Rentals" class="sailboat"></span>
                                    </li>
                                    <li>
                                        <span title="Custom Itineraries" class="passport"></span>
                                    </li>
                                    <li>
                                        <span title="Tailored family Getaways" class="family"></span>
                                    </li>
                                    <li>
                                        <span title="Beach Club Reservations" class="cocktail"></span>
                                    </li>
                                    <li>
                                        <span title="Recommendations finest Boutiques" class="shopping-bag"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="destination-boxes">
                        <div class="row no-gutters">
                            <div class="col-lg-6">
                                <img src="<?php echo get_field('image_1'); ?>" alt="">
                            </div>
                            <div class="col-lg-6 destination-box-content">
                                <div class="row no-gutters">
                                    <div class="col navy-blue">
                                        <div class="destination-boxes-text">
                                            <h3><?php echo get_field('combined_industry_experience'); ?></h3>
                                            <p><?php echo get_field('combined_industry_experience_content'); ?></p>
                                        </div>
                                    </div>
                                    <div class="col navy-blue-light">
                                        <div class="destination-boxes-text">
                                            <h3><?php echo get_field('client_memories'); ?></h3>
                                            <p><?php echo get_field('client_memories_content'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row no-gutters">
                                    <div class="col navy-blue-cloud">
                                        <div class="destination-boxes-text">
                                            <h3><?php echo get_field('luxury_villas'); ?></h3>
                                            <p><?php echo get_field('luxury_villas_content'); ?></p>
                                        </div>
                                    </div>
                                    <div class="col navy-blue-dark">
                                        <div class="destination-boxes-text">
                                            <h3><?php echo get_field('professional_consultants'); ?></h3>
                                            <p><?php echo get_field('professional_consultants_content'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row  no-gutters">

                            <div class="col-lg-6 destination-box-content">
                                <div class="row no-gutters">
                                    <div class="col navy-blue">
                                        <div class="destination-boxes-text">
                                            <h3><?php echo get_field('villa_getaways_offices'); ?></h3>
                                            <p><?php echo get_field('villa_getaways_offices_content'); ?></p>
                                        </div>
                                    </div>
                                    <div class="col navy-blue-light">
                                        <div class="destination-boxes-text">
                                            <h3><?php echo get_field('team_got_together'); ?></h3>
                                            <p><?php echo get_field('team_got_together_content'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row no-gutters">
                                    <div class="col navy-blue-cloud">
                                        <div class="destination-boxes-text">
                                            <h3><?php echo get_field('private_island_homes'); ?></h3>
                                            <p><?php echo get_field('private_island_homes_content'); ?></p>
                                        </div>
                                    </div>
                                    <div class="col navy-blue-dark">
                                        <div class="destination-boxes-text">
                                            <h3><?php echo get_field('awards_won'); ?></h3>
                                            <p><?php echo get_field('awards_won_content'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <img src="<?php echo get_field('image_2'); ?>" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Start Banner -->
    <section class="location-banner">
        <div class="container-fluid mt-164 p-0">
            
            <div class="welcome-banner">
                <div class="owl-carousel location-slider owl-theme">
                    <?php
                    if( have_rows('favourite_places') ):
                        $index = 1;
                        while( have_rows('favourite_places') ) : the_row();
                            ?>
                            <div class="item">
                                <div class="item-caption-wrapper">
                                    <div class="container">
                                        <div class="item-caption">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <h2><?php echo get_sub_field('sub_heading'); ?></h2>
                                                    <a href="<?php echo home_url("destination/villa-rentals-" . get_sub_field('link')); ?>">
                                                        <h4><?php echo get_sub_field('heading'); ?></h4>
                                                    </a>
                                                </div>
                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?php echo home_url("destination/villa-rentals-" . get_sub_field('link')); ?>">
                                    <img src="<?php echo get_sub_field('thumbnail'); ?>" alt="<?php echo get_sub_field('heading'); ?>">
                                </a>
                            </div>
                            <?php
                            $index++;
                        endwhile;
                    endif;
                    ?>
                </div>
            </div>
            
            <!--
            <div class="owl-carousel location-slider owl-theme">
                <?php
                /*
                if( have_rows('favourite_places') ):
                    $index = 1;
                    while( have_rows('favourite_places') ) : the_row();
                        ?>
                        <div class="item" data-dot="<h2><?php echo get_sub_field('heading'); ?></h2><img src='<?php echo get_sub_field('thumbnail'); ?>' /> ">
                            <div class="item-caption-wrapper">
                                <div class="container">
                                    <div class="item-caption">
                                        <h2><?php echo get_sub_field('sub_heading'); ?></h2>
                                    </div>
                                </div>
                            </div>
                            <img src="<?php echo get_sub_field('image'); ?>" alt="<?php echo get_sub_field('heading'); ?>">
                        </div>
                        <?php
                        $index++;
                    endwhile;
                endif;
                */
                ?>
            </div>
            -->
            
        </div>
    </section>

    <!-- ./ End Banner -->

    <section class="destinations">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="destination-boxes">
                        <div class="row no-gutters">
                            <div class="col-lg-6">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/daniel-olah-317207-unsplash.png" alt="">
                            </div>
                            <div class="col-lg-6 destination-box-content">
                                <div class="row no-gutters">
                                    <div class="col navy-blue">
                                        <div class="destination-boxes-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vg-img-7.png" alt="">
                                            <div class="display-destination-text">
                                                <span>
                                                    <h4>VG</h4>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col navy-blue-light">
                                        <div class="destination-boxes-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vg-img-1.png" alt="">
                                            <div class="display-destination-text">
                                                <span>
                                                    <h4>VG</h4>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row no-gutters">
                                    <div class="col navy-blue-cloud">
                                        <div class="destination-boxes-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vg-img-2.png" alt="">
                                            <div class="display-destination-text">
                                                <span>
                                                    <h4>VG</h4>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col navy-blue-dark">
                                        <div class="destination-boxes-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vg-img-3.png" alt="">
                                            <div class="display-destination-text">
                                                <span>
                                                    <h4>VG</h4>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row  no-gutters">
                            <div class="col-lg-6 destination-box-content">
                                <div class="row no-gutters">
                                    <div class="col navy-blue">
                                        <div class="destination-boxes-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vg-img-4.png" alt="">
                                            <div class="display-destination-text">
                                                <span>
                                                    <h4>VG</h4>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col navy-blue-light">
                                        <div class="destination-boxes-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vg-img-5.png" alt="">
                                            <div class="display-destination-text">
                                                <span>
                                                    <h4>VG</h4>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row no-gutters">
                                    <div class="col navy-blue-cloud">
                                        <div class="destination-boxes-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vg-img-6.png" alt="">
                                            <div class="display-destination-text">
                                                <span>
                                                    <h4>VG</h4>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col navy-blue-dark">
                                        <div class="destination-boxes-img">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/vg-img-7.png" alt="">
                                            <div class="display-destination-text">
                                                <span>
                                                    <h4>VG</h4>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/dan-freeman-407983-unsplash-2.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="destination-content pt-5 pl-4">
                        <h2><?php echo get_field('lifestyle_title'); ?></h2>
                        <?php echo get_field('lifestyle_content'); ?>
                        <div class="destination-content-action">
                            <h3><?php echo get_field('lifestyle_sub_title'); ?></h3>
                            <div class="destination-action-links">
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
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</div>

<!-- ./ End Main -->

<?php get_footer(); ?>