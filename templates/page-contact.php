<?php 
/* Template Name: Contact */
get_header();
?>

<!-- Start Main -->
<div class="contact-info-bar">
        <div class="row">
            <div class="col-xl-12">
                <h4 class="page-title">CONTACT US</h4>
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
            <div class="contact-tabs">
                <ul class="nav nav-tabs" role="tablist">
                    <?php
                    if( have_rows('countries') ):
                        $index = 1;
                        while( have_rows('countries') ) : the_row();
                            ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $index == 1 ? "active show" : ""; ?>" href="#location-<?php echo $index; ?>" data-toggle="tab">
                                    <?php echo get_sub_field('name'); ?>
                                </a>
                            </li>
                            <?php
                            $index++;
                        endwhile;
                    endif;
                    ?>
                </ul>
            </div>
        </div>
        <div class="tab-content contact-content locations">

            <?php
            if( have_rows('countries') ):
                $index = 1;
                while( have_rows('countries') ) : the_row();
                    
                    ?>
                    <div class="col-xl-6 col-lg-12 tab-pane <?php echo $index == 1 ? "active" : ""; ?>" id="location-<?php echo $index; ?>">
                        <div class="row row-eq-height">
                            <div class="col-xl-6 col-lg-12">
                                <div class="contact-info">
                                    <h2><?php echo get_sub_field('name'); ?></h2>

                                    <ul class="pl-0">
                                    <?php
                                    if( have_rows('locations') ):
                                        while( have_rows('locations') ) : the_row();
                                        ?>
                                        <li>
                                            <p>
                                                <strong><?php echo get_sub_field('address_1'); ?></strong><br/>
                                                <?php echo get_sub_field('address_2'); ?><br/>
                                                M: <a href="tel:<?php echo get_sub_field('phone'); ?>"><?php echo get_sub_field('phone'); ?></a><br/>
                                                E: <a href="mailto: <?php echo get_sub_field('email'); ?>"><?php echo get_sub_field('email'); ?></a>
                                            </p>
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
                    <?php
                    $index++;
                    
                endwhile;
            endif;
            ?>
            <div class="col-xl-6 col-lg-12">
                <?php echo get_template_part('template-parts/contact', 'form', array("id"=> $index)); ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>