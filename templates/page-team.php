<?php 
/* Template Name: Team */
get_header();
?>

<!-- Start Main -->
<div class="contact-info-bar">
        <div class="row">
            <div class="col-xl-12">
                <h4 class="page-title">Meet the Team</h4>
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
        <?php
        $args = array(  
            'post_status' => 'publish',
            'posts_per_page' => 50,
            'post_type' => 'team_members',
            'order' => "ASC",
            'orderby' => "ID"
        );
        $team_members = new WP_Query( $args );
        if ( $team_members->have_posts() ) {
            while ( $team_members->have_posts() ) {
                $team_members->the_post();
                $team_member_id = get_the_ID() ;
        	    ?>
                <div class="row team-member">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 left-aside mb-5 image">
                        <?php echo get_the_post_thumbnail( $team_member_id, 'medium' ); ?>
                    </div>
                    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-xs-12 left-aside mb-5 title">
                        <h4><?php the_title(); ?></h4>
                        <h6><?php echo get_field("designation", $team_member_id); ?></h6>
                        <?php the_content(); ?>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12 left-aside mb-5 info">
                        <p class="country-based">
                            <strong>
                                <?php echo get_field("country", $team_member_id); ?> Based
                            </strong>
                            <br/>
                            <string>M.:</string>
                            <a href="tel:<?php echo get_field("phone", $team_member_id); ?>"><?php echo get_field("phone", $team_member_id); ?></a>
                            <br/>
                            <string>E.:</string> 
                            <a href="mailto:<?php echo get_field("email", $team_member_id); ?>"><?php echo get_field("email", $team_member_id); ?></a>
                        </p>
                        
                        <?php
                        if( have_rows('destination_specialist', $team_member_id) ):
                            ?>
                            <p>
                                <strong>Destination Specialist:</strong>
                                <br/>
                                <ul>
                                <?php
                                while( have_rows('destination_specialist', $team_member_id) ) : the_row();
                                    ?>
                                    <li>
                                        <a href="<?php echo get_sub_field('link'); ?>">
                                            <?php echo get_sub_field('destination_name'); ?>
                                        </a>
                                    </li>
                                    <?php
                                endwhile;
                                ?>
                                </ul>
                            </p>
                            <?php
                        endif;
                        ?>
                    </div>
                </div>
                <?php
            }
        }
        wp_reset_postdata();
        ?>
    </div>
</div>

<?php get_footer(); ?>