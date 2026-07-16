<?php 
get_header();
?>

<!-- Start Main -->
<div class="contact-info-bar">
    <div class="row">
        <div class="col-xl-12">
            <h4 class="page-title"><?php the_title(); ?></h4>
        </div>
    </div>
</div>
<!-- ./ End Main -->

<?php
// Get featured image URL
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');

// Fallback image if no featured image is set
if (!$featured_image) {
    $featured_image = get_template_directory_uri() . '/assets/images/contact-page-banner.jpg';
}
?>

<!-- Start Banner -->
<main class="position-relative">
    <div class="welcome-banner-inner position-relative">
        
        <!-- Banner Image -->
        <img src="<?php echo esc_url($featured_image); ?>" 
             alt="<?php the_title_attribute(); ?>">

        <!-- Banner Title -->
        <div class="banner-content single-blog-title-banner">
            <h1><?php the_title(); ?></h1>
        </div>

    </div>
</main>
<!-- ./ End Banner -->

<div class="static-page">
    <div class="container p-0">
        <div class="row">
            <div class="col-xl-8 col-lg-8 left-aside mb-5">
                <?php the_content(); ?>
            </div>
              <!-- Sidebar -->
            <div class="col-lg-4 col-xl-4">

                <?php if ( is_active_sidebar('sidebar1') ) : ?>
                    <?php dynamic_sidebar('sidebar1'); ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<style>
.welcome-banner-inner {
    position: relative;
}

.welcome-banner-inner img {
    width: 100%;
    height: 450px;
    object-fit: cover;
}

.banner-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    width: 100%;
}

.banner-content h1 {
    color: #fff;
    font-size: 48px;
    font-weight: 700;
    margin: 0;
}
</style>
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

.single-blog-title-banner{
    padding: 20px;
    max-width: 900px;
    background: rgba(255, 255, 255, .6);
 
}

.single-blog-title-banner h1{
    color:#1d2a7c;
}
    
</style>

<?php 
get_footer();
?>