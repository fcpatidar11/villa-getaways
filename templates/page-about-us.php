<?php 
/* Template Name: About Us */
get_header();
?>

<!-- Start Main -->
<div class="contact-info-bar">
        <div class="row">
            <div class="col-xl-12">
                <h4 class="page-title">ABOUT US</h4>
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

<div class="static-page">
    <div class="container p-0">
        <div class="row">
            <div class="col-xl-12 left-aside mb-5">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>