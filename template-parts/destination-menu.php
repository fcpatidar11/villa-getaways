<?php
$conn = oracleDbConnection();
$all_destinations = fetchDestinationsForMenu($conn);



$destination_chunks = [];
if(sizeof($all_destinations)) {

	// Spread the destinations over the menu columns by rendered height
	// (one row for the country link plus one row per location), so that no
	// destination is ever dropped and the columns stay roughly even.
	$column_count = min(6, sizeof($all_destinations));

	$weights = [];
	$remaining_weight = 0;
	foreach ($all_destinations as $destination_id => $destination) {
		$weights[$destination_id] = 1 + sizeof($destination['LOCATIONS'] ?? []);
		$remaining_weight += $weights[$destination_id];
	}

	$column_index = 0;
	$column_weight = 0;
	$remaining = sizeof($all_destinations);

	foreach ($all_destinations as $destination_id => $destination) {
		$destination_chunks[$column_index][$destination_id] = $destination;
		$column_weight += $weights[$destination_id];
		$remaining_weight -= $weights[$destination_id];
		$remaining--;

		$columns_left = $column_count - $column_index - 1;
		if($columns_left < 1) {
			continue;
		}

		// Target is recomputed from what is left, so the trailing columns
		// never end up starved by rounding in the earlier ones.
		$column_target = ceil(($column_weight + $remaining_weight) / ($columns_left + 1));

		// Move on once this column is full, but only while enough
		// destinations remain to keep every following column non-empty.
		if(($column_weight >= $column_target && $remaining > $columns_left) || $remaining === $columns_left) {
			$column_index++;
			$column_weight = 0;
		}
	}

	foreach ($destination_chunks as $destinations) {
	    
	   
	    
	    
		?>
		<div class="col-sm-2">
			<?php
			
			
			foreach ($destinations as $destination) {
			    $country_name = strtolower(str_replace(" ", "-", $destination['COUNTRY'] ?? ''));
			    $destination_id = $destination['DESTINATION_ID'];
    			?>
    		    <ul class="multi-column-dropdown">
    		    	<li>
    			        <a href="<?php echo home_url('destination/villa-rentals-' . $country_name); ?>">
    			            <?php echo $destination['COUNTRY'] . " (" . $destination['COUNTED'] . ")"; ?>
    		            </a>
    			        <ul>
			            <?php
			            $locations = $destination['LOCATIONS'];
			            
			           
			            
			            if(sizeof($locations)) {
			                foreach ($locations as $location_id => $location) {
			                   $location_name = strtolower(str_replace(" ", "-", $location['LOCATION_NAME'] ?? ''));
			                   //$location_name = strtolower($location["LOCATION_URL_NAME"]);
			                 //   $location_name = strtolower(str_replace(" ", "-", $location['LOCATION_URL_NAME']));
    	    		            ?>
        			            <li>
        			                <a href="<?php echo home_url('destination/villa-rentals-' . $country_name . '-in-' . $location_name); ?>">
        			                    <?php echo $location['LOCATION_NAME'] . " (" . $location['COUNTED'] . ")"; ?>
    			                    </a>
        			            </li>
        			            <?php
			                }
			            }
			            ?>
    			        </ul>
    			    </li>
    			</ul>
    			<?php
			}
			?>
		</div>
		<?php
	}
}
?>