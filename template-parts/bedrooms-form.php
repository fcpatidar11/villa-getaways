<?php 
$destination_id = ( isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] ) ? $_REQUEST['destination_id'] : "";
$date_start = ( isset($_REQUEST['date_start']) && $_REQUEST['date_start'] ) ? $_REQUEST['date_start'] : "";
$date_end = ( isset($_REQUEST['date_end']) && $_REQUEST['date_end'] ) ? $_REQUEST['date_end'] : "";
$bedrooms = ( isset($_REQUEST['bedrooms']) && $_REQUEST['bedrooms'] ) ? $_REQUEST['bedrooms'] : 1;
$price = ( isset($_REQUEST['price']) && $_REQUEST['price'] ) ? $_REQUEST['price'] : "";

$dateStart = ( isset($_REQUEST['date_start']) && $_REQUEST['date_start'] ) ? $_REQUEST['date_start'] : "";
$dateEnd = ( isset($_REQUEST['date_end']) && $_REQUEST['date_end'] ) ? $_REQUEST['date_end'] : "";


$location_ids = vg_request_array('location_id');
$region_ids = vg_request_array('region_id');

$bedroomNumberList = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
// get_template_part() only defines $args when the caller passes one.
$args = isset($args) && is_array($args) ? $args : [];
$args_destination_id = $args['destination_id'] ?? "";
$args_location_id = $args['location_id'] ?? "";
?>

<form action="" method="get" id="filter-by-bedrooms">
    <input type="hidden" name="destination_id" value="<?php echo $destination_id?:$args_destination_id; ?>" />
    
    <?php 
    if($location_ids) { 
        foreach($location_ids AS $location_id) {
        ?>
        <input type="hidden" name="location_id[]" value="<?php echo $location_id; ?>" />
        <?php 
        }
    }
    ?>
    
    <?php 
    if($args_location_id) {
        ?>
        <input type="hidden" name="location_id[]" value="<?php echo $args_location_id; ?>" />
        <?php 
    }
    ?>
    
    <?php 
    if($region_ids) { 
        foreach($region_ids AS $region_id) {
        ?>
        <input type="hidden" name="region_id[]" value="<?php echo $region_id; ?>" />
        <?php 
        }
    }
    ?>
    
    <!--<input type="hidden" name="date_start" value="<?php echo $date_start; ?>" />-->
    <!--<input type="hidden" name="date_end" value="<?php echo $date_end; ?>" />-->
    <!--<input type="hidden" name="is_search" value="1" />-->
    <select class="form-control" name="bedrooms" id="filterBarRooms">
        <option value="" selected="selected">BEDROOMS</option>
        <?php
        foreach($bedroomNumberList AS $bedroomNumber) {
        ?>
        <option <?php echo $bedrooms == $bedroomNumber ? "selected": ""; ?> value="<?php echo $bedroomNumber; ?>"><?php echo $bedroomNumber; ?>+</option>
        <?php 
        }
        ?>
    </select>
    <select class="form-control" name="price" id="filterPriceRecommended">
        <option value="" >SORT BY</option>
        <option <?php echo $price == "recommended" ? "selected": ""; ?> value="recommended">Recommended</option>
        <option <?php echo $price == "low" ? "selected": ""; ?> value="low">Low to High</option>
        <option <?php echo $price == "high" ? "selected": ""; ?> value="high">High to Low</option>
    </select>
 
    <input class="form-control filterSidebar_from" type="text" id="filterSidebar_from" value="<?php echo $dateStart; ?>" name="date_start" placeholder="From">
    <input class="form-control filterSidebar_to" type="text" id="filterSidebar_to" value="<?php echo $dateEnd; ?>" name="date_end" placeholder="To">
    
</form>
<script type="text/javascript">
    $(document).ready(function(){
        $( ".filterSidebar_from" ).datepicker( "setDate", <?php echo $dateStart; ?> );
        $( ".filterSidebar_to" ).datepicker( "setDate", <?php echo $dateEnd; ?> );
    });
</script>

