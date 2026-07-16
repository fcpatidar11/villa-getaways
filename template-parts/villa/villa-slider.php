<?php
$conn = oracleDbConnection();
$villa_images = fetchVillaSliderImages($conn, $args["villa_details"]["VILLA_ID"]);
//$args["villa_details"]["VILLA_ID"] = "848";
$villa_videoes = fetchVillaVideos($conn, $args["villa_details"]["VILLA_ID"]);

function checkVideoSource($iframeSource) {
    // Parse the source URL to extract the domain
    $parsedUrl = parse_url($iframeSource);

    if (isset($parsedUrl['host'])) {
        $host = $parsedUrl['host'];

        // Check if the domain belongs to YouTube
        if (strpos($host, 'youtube.com') !== false || strpos($host, 'youtu.be') !== false) {
            return 'youtube';
        }

        // Check if the domain belongs to Vimeo
        if (strpos($host, 'vimeo.com') !== false) {
            return 'vimeo';
        }
    }

    // If the domain is not recognized as YouTube or Vimeo
    return 'unknown';
}

?>
<div class="directement-content">
    <div id="property-image-gallery">
        <div class="gallery">
            <!--<div class="hero video">-->
            <!--    <video autoplay muted loop>-->
            <!--        <source src="https://player.vimeo.com/external/138504815.sd.mp4?s=8a71ff38f08ec81efe50d35915afd426765a7526&profile_id=112" type="video/mp4" />-->
            <!--    </video>-->
            <!--</div>-->
            <!--<div class="hero video">-->
            <!--    <video loop autoplay muted preload="metadata" poster="https://images.unsplash.com/photo-1483555714914-20a74b6e45f5?crop=entropy&cs=srgb&fm=jpg&ixid=MnwxNDU4OXwwfDF8cmFuZG9tfHx8fHx8fHx8MTY0MzA4MTIwOQ&ixlib=rb-1.2.1&q=85">-->
            <!--      <source src="https://player.vimeo.com/external/138504815.sd.mp4?s=8a71ff38f08ec81efe50d35915afd426765a7526&profile_id=112" type="video/mp4" />-->
            <!--    </video>-->
            <!--</div>-->
            
            
            <?php 
            
           
            
            
            if(!empty($villa_videoes)) {
                foreach($villa_videoes as $videoSrc) {
                    
                    $videoSrc = str_replace("http://", "https://", $videoSrc);
                     $video = str_replace('src="', '', $videoSrc);
                    
                     $videoSource = checkVideoSource($video);
                    if($videoSource == 'vimeo') {
                    ?>
                    <!--src="https://player.vimeo.com/video/111606222?api=1&byline=0&portrait=0&title=0&background=1&mute=1&loop=1&autoplay=1&"-->
                    <!--src="https://www.youtube.com/embed/QV5EXOFcdrQ?enablejsapi=1&controls=0&loop=1"-->
                    <div class="hero <?php echo $videoSource; ?>">
                       <?php //echo $video; ?>
                       <iframe class="embed-player slide-media" <?php echo $videoSrc; ?> width="980" height="600" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen allow="autoplay"></iframe>-->
                    </div>
                <?php }else { ?>
                    <div class="hero youtube">
                      <iframe class="embed-player slide-media" width="980" height="520"  <?php echo $videoSrc; ?>frameborder="0" allowfullscreen></iframe> 
                    </div>
            <?php
                    } 
                } 
            } ?>
            
            

            <?php 
            if($villa_images) {
                 foreach($villa_images AS $villa_image) {
                 ?>
                    <div class="hero" style="background-image:url('<?php echo home_url("/wp-content/uploads/" . $villa_image); ?>');"></div>
            <?php 
                 }
            }
            ?>
            
        </div>
    </div>
    
    <div class="view-gallery">
        <?php 
        if($villa_images) {
            $data_gallery = "";
            foreach($villa_images AS $villa_image) {
                $data_gallery .= home_url("/wp-content/uploads/" . $villa_image) . ",";
            }
        }
        ?>
        <a href="javascript:;" data-gallery="<?php echo $data_gallery; ?>">
            <div class="count-box">
                VIEW GALLERY (<?php echo sizeof($villa_images); ?> IMAGES)
            </div>
        </a>
    </div>
</div>