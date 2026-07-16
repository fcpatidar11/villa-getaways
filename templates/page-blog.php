<?php
/* Template Name: Blogs */
get_header();
?>

<div class="contact-info-bar">
    <div class="row">
        <div class="col-xl-12">
            <h4 class="page-title">BLOG</h4>
        </div>
    </div>
</div>

<main class="position-relative">
    <div class="welcome-banner-inner">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/contact-page-banner.jpg" alt="Blog Banner">
    </div>
</main>

<section class="blog-page py-5">
    <div class="container">

        <div class="row">

            <!-- Blog Posts -->
            <div class="col-lg-8">

                <?php

                $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 5,
                    'paged'          => $paged
                );

                $blog_query = new WP_Query($args);

                if($blog_query->have_posts()) :


                    $i=0;
                    while($blog_query->have_posts()) :
                        $blog_query->the_post();
                        
                        if($i<5){
                ?>

                        <article class="blog-item mb-5">

                            <h2 class="blog-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="blog-meta mb-3">
                                <span>
                                    <i class="fa fa-calendar"></i>
                                    <?php echo get_the_date(); ?>
                                </span>

                                <span class="ms-3">
                                    <i class="fa fa-comments"></i>
                                    <?php comments_number('0 Comments', '1 Comment', '% Comments'); ?>
                                </span>
                            </div>

                            <?php if(has_post_thumbnail()) : ?>
                                <div class="blog-image mb-4">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('large', ['class' => 'img-fluid']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="blog-excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                Read More »
                            </a>

                            <div class="social-share mt-4">
                                <span>Share It Now</span>

                                <a href="https://facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank">
                                    <i class="fab fa-facebook-f"></i>
                                </a>

                                <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>" target="_blank">
                                    <i class="fab fa-twitter"></i>
                                </a>

                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>" target="_blank">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>

                        </article>

                <?php
                
                        }
                        $i++;
                    endwhile;
                ?>

                    <div class="blog-pagination">
                        <?php
                        echo paginate_links(array(
                            'total' => $blog_query->max_num_pages
                        ));
                        ?>
                    </div>

                <?php
                else :
                    echo '<p>No posts found.</p>';
                endif;

                wp_reset_postdata();
                ?>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">

                <?php if ( is_active_sidebar('sidebar1') ) : ?>
                    <?php dynamic_sidebar('sidebar1'); ?>
                <?php endif; ?>

            </div>

        </div>

    </div>
</section>
<style>
    .blog-title a{
    color:#1c2f6b;
    text-decoration:none;
}

.blog-title{
    font-size:34px;
    margin-bottom:15px;
}

.blog-meta{
    color:#999;
    font-size:14px;
}

.blog-image img{
    width:100%;
    height:auto;
}

.read-more-btn{
    color:#000;
    font-weight:600;
    text-decoration:none;
}

.social-share a{
    margin-right:10px;
}

.sidebar-widget{
    background:#f5f5f5;
    padding:20px;
    margin-bottom:30px;
}

.widget-title{
    background:#1c2f6b;
    color:#fff;
    padding:10px 15px;
    margin:-20px -20px 20px;
}

.popular-post {
    display: flex;
    gap: 15px;
    padding: 15px;
    border-bottom: 1px solid #ddd;
    align-items: flex-start;
}

.post-thumb {
    width: 80px;
    min-width: 80px;
    max-width: 80px;
}

.post-thumb img {
    width: 80px !important;
    height: 80px !important;
    object-fit: cover;
    display: block;
    border-radius: 3px;
}

.post-content {
    flex: 1;
}

.post-content h4 {
    margin: 0 0 8px;
    font-size: 16px;
    line-height: 1.4;
}

.custom-category-widget {
    background: #f3f3f3;
}

.category-widget-title {
    background: #24358b;
    color: #fff;
    margin: 0;
    padding: 15px 20px;
    font-size: 20px;
    font-weight: 700;
    text-transform: uppercase;
}

.category-list {
    list-style: none;
    margin: 0;
    padding: 20px;
}

.category-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.category-list li:last-child {
    margin-bottom: 0;
}

.category-list a {
    color: #555;
    text-decoration: none;
    font-size: 20px;
}

.category-list a:hover {
    color: #24358b;
}

.category-count {
    background: #d8d8e6;
    color: #666;
    min-width: 28px;
    height: 28px;
    line-height: 28px;
    text-align: center;
    border-radius: 5px;
    font-size: 13px;
    display: inline-block;
}

.custom-tags-widget {
    background: #f3f3f3;
}

.tags-widget-title {
    background: #24358b;
    color: #fff;
    margin: 0;
    padding: 15px 20px;
    font-size: 20px;
    font-weight: 700;
    text-transform: uppercase;
}

.tags-container {
    padding: 20px;
}

.tag-item {
    display: inline-block;
    margin: 0 8px 8px 0;
    padding: 8px 12px;
    border: 1px solid #d5d5d5;
    background: #f7f7f7;
    color: #666;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
}

.tag-item:hover {
    background: #24358b;
    color: #fff;
    border-color: #24358b;
}
.blog-pagination {
    display: flex;
    gap: 10px;
    margin: 40px 0;
    flex-wrap: wrap;
}

.blog-pagination .page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 42px;
    height: 42px;
    padding: 0 16px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
    color: #333;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.blog-pagination .page-numbers:hover {
    background: #0073aa;
    border-color: #0073aa;
    color: #fff;
}

.blog-pagination .current {
    background: #0073aa;
    border-color: #0073aa;
    color: #fff;
    cursor: default;
}

.blog-pagination .next,
.blog-pagination .prev {
    padding: 0 20px;
}

@media (max-width: 576px) {
    .blog-pagination {
        gap: 8px;
    }

    .blog-pagination .page-numbers {
        min-width: 38px;
        height: 38px;
        font-size: 14px;
        padding: 0 12px;
    }
}
    
</style>
<?php get_footer(); ?>