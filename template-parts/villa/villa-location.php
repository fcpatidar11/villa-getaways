<?php
$latitude = ($args["villa_details"]["LATITUDE"] ?? "");
$longitude = ($args["villa_details"]["LONGITUDE"] ?? "");
$villa_number = ($args["villa_details"]["VG_NUMBER"] ?? "");
$locationa_name = ($args["villa_details"]["LOCATION_NAME"] ?? "");
$destination_name = ($args["villa_details"]["DESTINATION_NAME"] ?? "");
?>
<script type="text/javascript">
    function initMap() {
        const myLatLng = { lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?> };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 17,
            center: myLatLng,
        });
        const contentString = 'Villa <?php echo $villa_number; ?> <br> <?php echo $locationa_name." ".$destination_name; ?>';
        const infowindow = new google.maps.InfoWindow({
            content: contentString,
            //ariaLabel: "Uluru",
        });
        const marker = new google.maps.Marker({
            position: myLatLng,
            map,
            title: "Villa <?php echo $villa_number; ?>",
        });
        marker.addListener("click", () => {
            infowindow.open({
                anchor: marker,
                map,
            });
        });
    }
    window.initMap = initMap;
</script>

<div class="col-lg-12">
    <div class="destination-content">
        <div class="destination-content-action">
            <div id="map"></div>
        </div>
    </div>
</div>
<script defer 
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC2gcgiXaJ9CuVjs1QmHlqxMHLVGIRLaRc&callback=initMap&v=weekly">
</script>