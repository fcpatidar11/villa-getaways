<?php
$conn = oracleDbConnection();
$all_destinations = fetchDestinationsForMenu($conn);



if(sizeof($all_destinations)) {
    if(sizeof($all_destinations) > 1)
	    $destinations_chumks[0] = array_slice($all_destinations, 0, 2, true);
	    
    if(sizeof($all_destinations) > 8)
    	$destinations_chumks[1] = array_slice($all_destinations, 2, 5, true);
	
	if(sizeof($all_destinations) > 11)
    	$destinations_chumks[2] = array_slice($all_destinations, 7, 3, true); 
	
	if(sizeof($all_destinations) > 15)
    	$destinations_chumks[3] = array_slice($all_destinations, 10, 4, true);
	
	if(sizeof($all_destinations) > 20)
    	$destinations_chumks[4] = array_slice($all_destinations, 13, 5, true);

	if(sizeof($all_destinations) > 22)
    	$destinations_chumks[5] = array_slice($all_destinations, 18, 6, true);

	
	foreach ($destinations_chumks as $destinations) {
	    
	   
	    
	    
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