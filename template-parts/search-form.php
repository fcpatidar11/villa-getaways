<?php
$conn = oracleDbConnection();
$destinations = fetchDestinations($conn);

$destination_id = (isset($_COOKIE["__destination_id"]) && $_COOKIE["__destination_id"]) ? $_COOKIE["__destination_id"] : "";
$location_id = (isset($_COOKIE["__location_id"]) && $_COOKIE["__location_id"]) ? $_COOKIE["__location_id"] : "";
$date_start = (isset($_COOKIE["__date_start"]) && $_COOKIE["__date_start"]) ? $_COOKIE["__date_start"] : "";
$date_end = (isset($_COOKIE["__date_end"]) && $_COOKIE["__date_end"]) ? $_COOKIE["__date_end"] : "";
$bedrooms = (isset($_COOKIE["__bedrooms"]) && $_COOKIE["__bedrooms"]) ? $_COOKIE["__bedrooms"] : "";
$page = (isset($_COOKIE["__page"]) && $_COOKIE["__page"]) ? $_COOKIE["__page"] : "";

$bedroomNumberList = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

?>
<form action="<?php echo home_url("villas/browse"); ?>" class="search_form" name="search_form" id="search_form">
    <div class="row no-gutters">
        <div class="col-12 col-sm-6 col-md-6 col-lg-2 destinations-select-id">
            <select class="form-control destination" name="destination_id" id="destination">
                <option value="">DESTINATION</option>
                <?php
                if(sizeof($destinations)) {
                    $index = 1;
                    foreach($destinations AS $key => $destination) {
                        ?>
                        <?php //echo $destination_id == $key ? "selected": ""; ?>
                        <option value="<?php echo $key; ?>"><?php echo $destination; ?></option>                        
                        <?php
                        $index++;
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-6 col-lg-2 locations-select-id">
            <select class="form-control location" name="location_id[]" id="location" multiple="multiple" 
            data-live-search="true" 
            data-max-options="5" 
            data-title="Locations (All)" 
            data-selected-text-format="count" 
            data-live-search-placeholder="Search">
                <option value="" selected="selected">Locations (All)</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-6 col-lg-2 regions-select-id">
            <select class="form-control region" name="region_id[]" id="region" multiple="multiple" 
            data-live-search="true" 
            data-max-options="5" 
            data-title="Regions (All)" 
            data-selected-text-format="count" 
            data-live-search-placeholder="Search">
                <option value="" selected="selected">Regions (All)</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="input-group date-range">
                <input class="form-control from" type="text" id="search-fromdate" value="" placeholder="From" name="date_start">
                <input class="form-control to" type="text" id="search-todate" value="" placeholder="To" name="date_end">
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-6 col-lg-2">
            <select class="form-control bedrooms" name="bedrooms" id="bedrooms">
                <option value="">BEDROOMS</option>
                <?php
                foreach($bedroomNumberList AS $bedroomNumber) {
                ?>
                <option value="<?php echo $bedroomNumber; ?>"><?php echo $bedroomNumber; ?>+</option>
                <?php 
                }
                ?>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-md-6 col-lg-1 text-center">
            <input type="hidden" name="is_search" value="1" />
            <input type="hidden" name="page" value="1" />
             <!--TAKE ME THERE-->
            <button class="btn btn-take-me" type="submit">Search</button>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 show-errors" style="display: none;" id="show-errors"><span style="color: red;text-align: left;padding-left: 25px;font-size: 13px;"></span></div>
    </div>
</form>