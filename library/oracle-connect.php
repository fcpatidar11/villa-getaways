<?php

// Sanitize a single numeric id for safe inline use in SQL.
// Empty/non-numeric becomes the SQL literal NULL, so "= " . vg_int($x) stays
// valid syntax (no ORA-00936) and returns no rows instead of matching anything.
if ( ! function_exists( 'vg_int' ) ) {
    function vg_int($v) {
        return ( isset($v) && $v !== '' && is_numeric($v) ) ? (string) (int) $v : 'NULL';
    }
}

// Sanitize a comma-separated list of numeric ids for a SQL IN(...) clause.
// Keeps only positive integers; empty result becomes NULL so IN(NULL) is valid
// and matches nothing. Both guards block SQL injection from request values.
if ( ! function_exists( 'vg_int_list' ) ) {
    function vg_int_list($csv) {
        $ids = array_filter(array_map('intval', explode(',', (string) $csv)), function($n) { return $n > 0; });
        return $ids ? implode(',', $ids) : 'NULL';
    }
}

// Establish Oracle DB Connection
if ( ! function_exists( 'oracleDbConnection' ) ) {
    function oracleDbConnection() {
        // username = vg , password = vg, database = pdbdev1
        $conn = oci_connect('vg', 'vg', 'pdbdev1');
        if (!$conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        return $conn;
    }
}

// Fetch Destinations
if ( ! function_exists( 'fetchDestinations' ) ) {
    function fetchDestinations($conn) {
        // Prepare the statement
        $stid = oci_parse($conn, 'SELECT name, destination_id FROM destination ORDER BY position');
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $destinations = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $destinations[$row['DESTINATION_ID']] = $row['NAME'];
        }
        return $destinations;
    }
}


// Fetch Villa List
if ( ! function_exists( 'fetchVillasList' ) ) {
    function fetchVillasList($conn) {
        // Prepare the statement
        $stid = oci_parse($conn, 'select slug,CREATED_AT from villa_list');
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villalist = [];
        // Fetch the results of the query
           while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
          
             $villalist[] = $row;
        }
        return $villalist;
    }
}

// Fetch single villa List
if ( ! function_exists( 'fetchSingleVillasList' ) ) {
    function fetchSingleVillasList($conn,$slug) {
        // Prepare the statement
       // Prepare the statement
        $stid = oci_parse($conn, 'SELECT * FROM villa_list WHERE slug = :slug');
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Bind the slug value
        oci_bind_by_name($stid, ':slug', $slug);
        
        // Execute the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Fetch the results of the query
      
        $villalist = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);
        
        // Free the statement and close the connection if necessary
        oci_free_statement($stid);
        
        // Return the result
        return $villalist;

    }
}


// Fetch villas from villa List
if ( ! function_exists( 'fetchVillasFromVillaList' ) ) {
    function fetchVillasFromVillaList($conn, $villa_list_id, $page = 1, $price="low", $no_of_bedrooms = 1) {
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }
        
        $query = " select * ";
        // $query .= " , 'Villa ' || v.vg_number as villa_name ";
        // $query .= " , vg_number ";
        // $query .= " , destination_name, is_priority ";
        // $query .= " , beds, agent_id ";
        // $query .= " , baths ";
        // $query .= " , sleeps, location_name ";
        // $query .= " , location_name || case when region_name is not null then ', ' || region_name else null end as location ";
        // $query .= " , image ";
        // $query .= " , tax_percentage ";
        // $query .= " , currency  ";
        // $query .= " , 2 orderno ";
        $query .= " from villa_list_villas_vw v  ";
        $query .= "where villa_list_id = ".vg_int($villa_list_id);
        $query .= " offset ". $page ." rows ";
        $query .= " fetch next 20 rows only ";
        
       
        $stid = oci_parse($conn, $query);
        
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
       
       
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                 "VILLA_LIST_ID" => $row["VILLA_LIST_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "DESTINATION_NAME" => $row["DESTINATION_NAME"] ?? null,
                "LOCATION_NAME" => $row["LOCATION_NAME"] ?? null,
                "IS_PRIORITY" => $row["IS_PRIORITY"] ?? null,
                "AGENT_ID" => $row["AGENT_ID"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
    }
}

// Fetch Destinations
if ( ! function_exists( 'fetchDestinationTitle' ) ) {
    function fetchDestinationTitle($conn, $destination_id) {

        // Prepare the statement
        $query = "select nvl(title, name||' Villas') as title,description,content_1,content_bg_colour_1,content_2,content_bg_colour_2,content_3,content_bg_colour_3,content_4,content_bg_colour_4 from destination where destination_id = ".vg_int($destination_id);
        $stid = oci_parse( $conn, $query );
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $destinationTitle = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $destinationTitle['TITLE'] = $row['TITLE'];
            $destinationTitle['DESCRIPTION'] = $row['DESCRIPTION'];
            
           $destinationTitle['CONTENT_1'] = ($row['CONTENT_1']) ? $row['CONTENT_1']->load() : '';
           $destinationTitle['CONTENT_BG_COLOUR_1'] = $row['CONTENT_BG_COLOUR_1'];
           
           $destinationTitle['CONTENT_2'] = ($row['CONTENT_2']) ? $row['CONTENT_2']->load() : '';
           $destinationTitle['CONTENT_BG_COLOUR_2'] = $row['CONTENT_BG_COLOUR_2'];
           
           $destinationTitle['CONTENT_3'] = ($row['CONTENT_3']) ? $row['CONTENT_3']->load() : '';
           $destinationTitle['CONTENT_BG_COLOUR_3'] = $row['CONTENT_BG_COLOUR_3'];
           
           $destinationTitle['CONTENT_4'] = ($row['CONTENT_4']) ? $row['CONTENT_4']->load() : '';
           $destinationTitle['CONTENT_BG_COLOUR_4'] = $row['CONTENT_BG_COLOUR_4'];
        
        
        }
        return $destinationTitle;
    }
}

// Fetch Destinations
if ( ! function_exists( 'fetchLocationTitle' ) ) {
    function fetchLocationTitle($conn, $location_id) {
        // Prepare the statement
        $query = "select nvl(title, name||' Villas') as title ,description,content_1,content_bg_colour_1,content_2,content_bg_colour_2,content_3,content_bg_colour_3,content_4,content_bg_colour_4 from location where location_id = ".vg_int($location_id);
        $stid = oci_parse( $conn, $query );
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $locationTitle = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $locationTitle['TITLE'] = $row['TITLE'];
            $locationTitle['DESCRIPTION'] = $row['DESCRIPTION'];
            
            $locationTitle['CONTENT_1'] = ($row['CONTENT_1']) ? $row['CONTENT_1']->load() : '';
           $locationTitle['CONTENT_BG_COLOUR_1'] = $row['CONTENT_BG_COLOUR_1'];
           
           $locationTitle['CONTENT_2'] = ($row['CONTENT_2']) ? $row['CONTENT_2']->load() : '';
           $locationTitle['CONTENT_BG_COLOUR_2'] = $row['CONTENT_BG_COLOUR_2'];
           
           $locationTitle['CONTENT_3'] = ($row['CONTENT_3']) ? $row['CONTENT_3']->load() : '';
           $locationTitle['CONTENT_BG_COLOUR_3'] = $row['CONTENT_BG_COLOUR_3'];
           
           $locationTitle['CONTENT_4'] = ($row['CONTENT_4']) ? $row['CONTENT_4']->load() : '';
           $locationTitle['CONTENT_BG_COLOUR_4'] = $row['CONTENT_BG_COLOUR_4'];
        }
        return $locationTitle;
    }
}

// Fetch Destinations
if ( ! function_exists( 'fetchRegionTitle' ) ) {
    function fetchRegionTitle($conn, $region_id) {
        // Prepare the statement
        $query = "select nvl(title, name||' Villas') as title,description from region where region_id = ".vg_int($region_id);
        $stid = oci_parse( $conn, $query );
        
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $regionTitle = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $regionTitle['TITLE'] = $row['TITLE'];
            $regionTitle['DESCRIPTION'] = $row['DESCRIPTION'];
        }
        return $regionTitle;
    }
}

// Fetch Single Destination Name
if ( ! function_exists( 'fetchDestinationName' ) ) {
    function fetchDestinationName($conn, $destination_id) {
        // Prepare the statement
        $stid = oci_parse($conn, 'SELECT name FROM destination where destination_id = '.vg_int($destination_id) );
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $destination = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $destination[] = $row['NAME'];
        }
        return $destination;
    }
}

// Fetch Destination's Locations
if ( ! function_exists( 'fetchDestinationLocations' ) ) {
    function fetchDestinationLocations($conn, $destination_id) {
        // Prepare the statement
        $stid = oci_parse($conn, 'SELECT name, location_id FROM location WHERE destination_id = ' . vg_int($destination_id));
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $locations = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $locations[$row['LOCATION_ID']] = $row['NAME'];
        }
        return $locations;
    }
}

// Fetch Destination's Locations
if ( ! function_exists( 'fetchLocationsAndCount' ) ) {
    function fetchLocationsAndCount($conn, $destination_id) {
        // Prepare the statement
        $stid = oci_parse($conn, 'select * from ( select location_name, count(*) total from villa_location_vw where destination_id='.vg_int($destination_id).' group by location_name) order by location_name');
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $locations = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp_location = [];
            $temp_location = [
                "LOCATION_NAME" => $row["LOCATION_NAME"] ?? null,
                "TOTAL" => $row["TOTAL"] ?? null,
            ];
            $locations[$row["LOCATION_NAME"]] = $temp_location;
            
        }
        return $locations;
    }
}

// Fetch Destination's Locations
if ( ! function_exists( 'fetchRegions' ) ) {
    function fetchRegions($conn, $destination_id) {
        // Prepare the statement
        $stid = oci_parse($conn, 'select region_id, region_name from destination_locations_vw where destination_id ='.vg_int($destination_id));
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $regions = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $regions[$row["REGION_ID"]] = $row["REGION_NAME"];
        }
        return $regions;
    }
}

// Fetch Destination's Locations
if ( ! function_exists( 'fetchDestinationWithLocationsAndRegions' ) ) {
    function fetchDestinationWithLocationsAndRegions($conn, $destination_id) {
        // Prepare the statement
        $stid = oci_parse($conn, 'select * from ( select location_name, nvl(region_name, location_name) region_name,count(*) total
                from villa_location_vw where destination_id='.vg_int($destination_id).' group by location_name, nvl(region_name, location_name)) order by location_name, region_name');
                
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $locations = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp_destination = [];
            if( isset($row["LOCATION_NAME"]) && $row["LOCATION_NAME"] ) {
                $temp_location = [];
                $temp_location = [
                    "LOCATION_NAME" => $row["LOCATION_NAME"] ?? null,
                    "REGION_NAME" => $row["REGION_NAME"] ?? null,
                    "TOTAL" => $row["TOTAL"] ?? null,
                ];
                $locations[$row["LOCATION_NAME"]][] = $temp_location;
            }
        }
        return $locations;
    }
}

// Fetch Destination's Locations
if ( ! function_exists( 'fetchTotalCountOfLocations' ) ) {
    function fetchTotalCountOfLocations($conn, $destination_id) {
        // Prepare the statement
        $stid = oci_parse($conn, 'select count(*) from villa_location_vw where destination_id='.vg_int($destination_id).' and region_name is not null');
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $count = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $count['COUNT'] = $row['COUNT(*)'];
        }
        return $count;
    }
}


// Fetch Destination and their Locations for Menu
if ( ! function_exists( 'fetchDestinationsForMenu' ) ) {
    function fetchDestinationsForMenu($conn) {
        /*
        $query = 'SELECT * FROM (';
        $query .= ' SELECT destination_name, destination_id, location_name, count(location_name) counted';
        $query .= ' FROM villa_location_vw';
        $query .= ' GROUP BY rollup(destination_name, destination_id, location_name)';
        $query .= ' ORDER BY destination_name, location_name nulls first)';
        $query .= ' WHERE destination_name is not null and destination_id is not null';
        */
        
        /*
        $query = " select destination_name ";
        $query .= " , destination_id ";
        $query .= " , location_name ";
        $query .= " , counted  ";
        $query .= " , (select replace(location_url_name,' ','-') from location l where l.name = location_name fetch first row only) location_url_name ";
        $query .= " from ( ";
        $query .= " select destination_name, destination_id, location_name, count(location_name) counted ";
        $query .= " from villa_location_vw ";
        $query .= " group by rollup(destination_name, destination_id, location_name) ";
        $query .= " order by destination_name, location_name nulls first) ";
        $query .= " where destination_name is not null and destination_id is not null";
        */
        
        $query = " select destination_name , destination_id , location_name , counted , (select replace(location_url_name,' ','-') ";
        $query .= " from location l where l.name = location_name fetch first row only) location_url_name  ";
        $query .= " from ( select destination_name, destination_id, location_name, count(location_name) counted "; 
        $query .= " from villa_location_vw group by rollup(destination_name, destination_id, location_name)  ";
        $query .= " order by destination_name, location_name nulls first)  ";
        $query .= " where destination_name is not null and destination_id is not null";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $destinations = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp_destination = [];
            if( isset($row["LOCATION_NAME"]) && $row["LOCATION_NAME"] ) {
                $temp_location = [];
                $temp_location = [
                    "LOCATION_NAME" => $row["LOCATION_NAME"] ?? null,
                    "LOCATION_URL_NAME" => $row["LOCATION_URL_NAME"] ?? null,
                    "COUNTED" => $row["COUNTED"] ?? null,
                    "DESTINATION_NAME" => $row["DESTINATION_NAME"] ?? null,
                    "DESTINATION_ID" => $row["DESTINATION_ID"] ?? null
                ];
                $destinations[$row["DESTINATION_ID"]]["LOCATIONS"][] = $temp_location;
            } else {
                $temp_destination = [
                    "COUNTRY" => $row["DESTINATION_NAME"] ?? null,
                    "DESTINATION_ID" => $row["DESTINATION_ID"] ?? null,
                    "COUNTED" => $row["COUNTED"] ?? null,
                    "LOCATIONS" => []
                ];
                $destinations[$row["DESTINATION_ID"]] = $temp_destination;
            }
        }
        return $destinations;
    }
}

// // Fetch Villas By Destination
if ( ! function_exists( 'fetchVillasByDestination' ) ) {
    function fetchVillasByDestination($conn, $destination_id, $date_from, $date_to, $no_of_bedrooms) {
        $no_of_bedrooms = $no_of_bedrooms ? $no_of_bedrooms : 1;
        
        $query = "select villa_id ";
        $query .= ", 'Villa ' || v.vg_number as villa_name ";
        $query .= ", vg_number ";
        $query .= ", beds ";
        $query .= ", baths ";
        $query .= ", sleeps ";
        $query .= ", location_name || case when region_name is not null then ', ' || region_name else null end as location ";
        $query .= ", image ";
        $query .= ", villa_functions.calculate_price( v.villa_id , to_date('" . $date_from . "','dd/mm/yyyy'), to_date('" . $date_to . "','dd/mm/yyyy'), " . vg_int($no_of_bedrooms) . " ) AS price ";
        $query .= ", villa_functions.offer_text( v.villa_id , to_date('" . $date_from . "','dd/mm/yyyy'), to_date('" . $date_to . "','dd/mm/yyyy'), " . vg_int($no_of_bedrooms) . " ) AS offer_text ";
        $query .= ", tax_percentage ";
        $query .= ", currency  ";
        $query .= "from villa_location_vw v  ";
        $query .= "where exists (select 1 from villa_price vp where vp.villa_id = v.villa_id and to_date('" . $date_from . "','dd/mm/yyyy') between valid_from and valid_to)  ";
        $query .= "and exists (select 1 from booking b where to_date('" . $date_from . "','dd/mm/yyyy') not between arrive and depart and b.villa_id = v.villa_id)  ";
        $query .= "and  destination_id = " . vg_int($destination_id);
        $query .= "and beds >= " . vg_int($no_of_bedrooms);
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
    }
}

// Search Villas
if ( ! function_exists( 'searchVillas' ) ) {
    function searchVillas($conn, $destination_id, $location_ids, $region_ids, $date_from, $date_to, $no_of_bedrooms, $page = 1, $price = "") {
        
        $no_of_bedrooms = $no_of_bedrooms ? $no_of_bedrooms : 1;
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }
        
        
        // $query = "select * ";
        // $query .= " from ( select * ";
        // $query .= " from ( select villa_id, ";
        // $query .= " 'Villa ' || v.vg_number as villa_name,vg_number, beds, agent_id, baths, sleeps,is_priority,destination_name, ";
        // $query .= " destination_id,location_name, case when region_name is not null then region_name || ', ' || location_name else ";
        // $query .= " location_name end as location, image, ";
        // $query .= " villa_functions.calculate_price(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."' ";
        // $query .= " , 'dd/mm/yyyy'),".vg_int($no_of_bedrooms).") as price, ";
        // $query .= " villa_functions.offer_text(v.villa_id, to_date('".$date_to."', 'dd/mm/yyyy'), to_date('".$date_from."', 'dd/mm/yyyy' ";
        // $query .= " ), ".vg_int($no_of_bedrooms).") as offer_text,tax_percentage,currency from villa_location_vw v where exists ( select 1 ";
        // $query .= " from villa_price vp where vp.villa_id = v.villa_id and to_date('".$date_from."', 'dd/mm/yyyy') between valid_from ";
        // $query .= " and valid_to ) and not exists ( select 1 from booking b where to_date('".$date_from."', 'dd/mm/yyyy') between arrive and ";
        // $query .= " depart and b.villa_id = v.villa_id ) and destination_id = ".vg_int($destination_id)." and beds >= ".vg_int($no_of_bedrooms)." ";
        // if($location_ids)
        //     $query .= " and location_id in (" . vg_int_list($location_ids) . ") ";
             
        // if($region_ids)
        //     $query .= " and region_id in (" . vg_int_list($region_ids) . ") ";
        // $query .= " union ";
        // $query .= " select villa_id,'Villa ' || v.vg_number as villa_name,vg_number,beds,agent_id,baths,sleeps,is_priority,destination_name, ";
        // $query .= " destination_id,location_name,case when region_name is not null then region_name || ', ' || location_name else ";
        // $query .= " location_name end as location, image, ";
        // $query .= " villa_functions.calculate_price(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as price, ";
        // $query .= " villa_functions.offer_text(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date ('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as offer_text, ";
        // $query .= " tax_percentage,currency from villa_location_vw v where not exists ( select 1 from villa_price vp where vp.villa_id = v.villa_id ";
        // $query .= " and to_date('".$date_to."', 'dd/mm/yyyy') between valid_from and valid_to ) and not exists ( select 1 from booking b ";
        // $query .= " where to_date('".$date_from."', 'dd/mm/yyyy') between arrive and depart and b.villa_id = v.villa_id ) ";
        // $query .= " and destination_id = ".vg_int($destination_id)." and beds >= ".vg_int($no_of_bedrooms)." ";
        // if($location_ids)
        //     $query .= " and location_id in (" . vg_int_list($location_ids) . ") ";
             
        // if($region_ids)
        //     $query .= " and region_id in (" . vg_int_list($region_ids) . ") ";
        // $query .= " union ";
        // $query .= " select villa_id,'Villa ' || v.vg_number as villa_name,vg_number,beds,baths,agent_id, sleeps,is_priority,destination_name, ";
        // $query .= " destination_id,location_name,case when region_name is not null then region_name || ', ' || location_name else ";
        // $query .= " location_name end as location,image, ";
        // $query .= " villa_functions.calculate_price(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as price, ";
        // $query .= " villa_functions.offer_text(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as offer_text, ";
        // $query .= " tax_percentage,currency from villa_location_vw v where exists ( select 1 from booking b where to_date('".$date_from."', 'dd/mm/yyyy') between arrive and depart ";
        // $query .= " and b.villa_id = v.villa_id ) and destination_id = ".vg_int($destination_id)." and beds >= ".vg_int($no_of_bedrooms)." ";
        // if($location_ids)
        //     $query .= " and location_id in (" . vg_int_list($location_ids) . ") ";
             
        // if($region_ids)
        //     $query .= " and region_id in (" . vg_int_list($region_ids) . ") ";
        
        // $query .= " ) ";
        
        // if($price == "low") {
        //     $query .= " order by destination_name, price, sleeps ";
        // } elseif($price == "high") {
        //     $query .= " order by destination_name, price desc, sleeps ";
        // } elseif($price == 'recommended') {
        //     $query .= " order by destination_name, is_priority desc, sleeps ";
        // } else {
        //     $query .= " order by location,sleeps ";
        // }
        // $query .= " ) ";
        
        // $query .= " offset ".$page." rows fetch next 20 rows only ";
        
        $query = "select * ";
        $query .= " from ( select * ";
        $query .= " from ( select villa_id, ";
        $query .= " 'Villa ' || v.vg_number as villa_name,vg_number, beds, agent_id, baths, sleeps,is_priority,destination_name, ";
        $query .= " destination_id,location_name, case when region_name is not null then region_name || ', ' || location_name else ";
        $query .= " location_name end as location, image, 1 as availability, ";
        $query .= " villa_functions.calculate_price(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."' ";
        $query .= " , 'dd/mm/yyyy'),".vg_int($no_of_bedrooms).") as price, ";
        $query .= " villa_functions.offer_text(v.villa_id, to_date('".$date_to."', 'dd/mm/yyyy'), to_date('".$date_from."', 'dd/mm/yyyy' ";
        $query .= " ), ".vg_int($no_of_bedrooms).") as offer_text,tax_percentage,currency from villa_location_vw v where exists ( select 1 ";
        $query .= " from villa_price vp where vp.villa_id = v.villa_id and to_date('".$date_from."', 'dd/mm/yyyy') between valid_from ";
        $query .= " and valid_to ) and not exists ( select 1 from booking b where to_date('".$date_from."', 'dd/mm/yyyy') between arrive and ";
        $query .= " depart and b.villa_id = v.villa_id ) and destination_id = ".vg_int($destination_id)." and beds >= ".vg_int($no_of_bedrooms)." ";
        if($location_ids)
            $query .= " and location_id in (" . vg_int_list($location_ids) . ") ";
             
        if($region_ids)
            $query .= " and region_id in (" . vg_int_list($region_ids) . ") ";
        $query .= " union ";
        $query .= " select villa_id,'Villa ' || v.vg_number as villa_name,vg_number,beds,agent_id,baths,sleeps,is_priority,destination_name, ";
        $query .= " destination_id,location_name,case when region_name is not null then region_name || ', ' || location_name else ";
        $query .= " location_name end as location, image, 1 as availability, ";
        $query .= " villa_functions.calculate_price(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as price, ";
        $query .= " villa_functions.offer_text(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date ('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as offer_text, ";
        $query .= " tax_percentage,currency from villa_location_vw v where not exists ( select 1 from villa_price vp where vp.villa_id = v.villa_id ";
        $query .= " and to_date('".$date_to."', 'dd/mm/yyyy') between valid_from and valid_to ) and not exists ( select 1 from booking b ";
        $query .= " where to_date('".$date_from."', 'dd/mm/yyyy') between arrive and depart and b.villa_id = v.villa_id ) ";
        $query .= " and destination_id = ".vg_int($destination_id)." and beds >= ".vg_int($no_of_bedrooms)." ";
        if($location_ids)
            $query .= " and location_id in (" . vg_int_list($location_ids) . ") ";
             
        if($region_ids)
            $query .= " and region_id in (" . vg_int_list($region_ids) . ") ";
        $query .= " union ";
        $query .= " select villa_id,'Villa ' || v.vg_number as villa_name,vg_number,beds,baths,agent_id, sleeps,is_priority,destination_name, ";
        $query .= " destination_id,location_name,case when region_name is not null then region_name || ', ' || location_name else ";
        $query .= " location_name end as location,image, 0 as availability, ";
        $query .= " villa_functions.calculate_price(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as price, ";
        $query .= " villa_functions.offer_text(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as offer_text, ";
        $query .= " tax_percentage,currency from villa_location_vw v where exists ( select 1 from booking b where to_date('".$date_from."', 'dd/mm/yyyy') between arrive and depart ";
        $query .= " and b.villa_id = v.villa_id ) and destination_id = ".vg_int($destination_id)." and beds >= ".vg_int($no_of_bedrooms)." ";
        if($location_ids)
            $query .= " and location_id in (" . vg_int_list($location_ids) . ") ";
             
        if($region_ids)
            $query .= " and region_id in (" . vg_int_list($region_ids) . ") ";
        
        $query .= " ) ";
        
    //   if($price == "low") {
    //         $query .= " order by destination_name, price, sleeps, availability desc ";
    //     } elseif($price == "high") {
    //         $query .= " order by destination_name, price desc, sleeps, availability desc  ";
    //     } elseif($price == 'recommended') {
    //         $query .= " order by destination_name, is_priority desc, sleeps, availability desc  ";
    //     } else {
    //         $query .= " order by location,sleeps, availability desc  ";
    //     }
    
        
        if($price == "low") {
            $query .= " order by destination_name, is_priority desc, vg_number, price, sleeps, availability desc ";
        } elseif($price == "high") {
            $query .= " order by destination_name, is_priority desc, vg_number, price desc, sleeps, availability desc  ";
        } elseif($price == 'recommended') {
            $query .= " order by destination_name, is_priority desc, vg_number, sleeps, availability desc  ";
        } else {
            $query .= " order by location, is_priority desc, vg_number, sleeps, availability desc  ";
        }
        
        
    
        $query .= " ) ";
        
        $query .= " offset ".$page." rows fetch next 20 rows only ";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            
           
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "DESTINATION_NAME" => $row["DESTINATION_NAME"] ?? null,
                "DESTINATION_ID" => $row["DESTINATION_ID"] ?? null,
                "LOCATION_NAME"  => $row["LOCATION_NAME"] ?? null,
                "IS_PRIORITY"  => $row["IS_PRIORITY"] ?? null,
                "AGENT_ID"  => $row["AGENT_ID"] ?? null,
                "AVAILABILITY"=>$row["AVAILABILITY"] ?? null
            ];
            $villas[] = $temp;
        }
        
        return $villas;
    }
}


// Search Villas
if ( ! function_exists( 'searchVillalistVillas' ) ) {
    function searchVillalistVillas($conn, $destination_id, $location_ids, $region_ids, $date_from, $date_to, $no_of_bedrooms, $page = 1, $price = "") {
        
        $no_of_bedrooms = $no_of_bedrooms ? $no_of_bedrooms : 1;
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }
          
        $query = "select * ";
        $query .= " from ( select * ";
        $query .= " from ( select villa_id, ";
        $query .= " 'Villa ' || v.vg_number as villa_name,vg_number, beds, agent_id, baths, sleeps,is_priority,destination_name, ";
        $query .= " destination_id,location_name, case when region_name is not null then region_name || ', ' || location_name else ";
        $query .= " location_name end as location, image, ";
        $query .= " villa_functions.calculate_price(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."' ";
        $query .= " , 'dd/mm/yyyy'),".vg_int($no_of_bedrooms).") as price, ";
        $query .= " villa_functions.offer_text(v.villa_id, to_date('".$date_to."', 'dd/mm/yyyy'), to_date('".$date_from."', 'dd/mm/yyyy' ";
        $query .= " ), ".vg_int($no_of_bedrooms).") as offer_text,tax_percentage,currency from villa_location_vw v where exists ( select 1 ";
        $query .= " from villa_price vp where vp.villa_id = v.villa_id and to_date('".$date_from."', 'dd/mm/yyyy') between valid_from ";
        $query .= " and valid_to ) and not exists ( select 1 from booking b where to_date('".$date_from."', 'dd/mm/yyyy') between arrive and ";
        $query .= " depart and b.villa_id = v.villa_id ) and destination_id = ".vg_int($destination_id)." and beds >= ".vg_int($no_of_bedrooms)." ";
        if($location_ids)
            $query .= " and location_id in (" . vg_int_list($location_ids) . ") ";
             
        if($region_ids)
            $query .= " and region_id in (" . vg_int_list($region_ids) . ") ";
        $query .= " union ";
        $query .= " select villa_id,'Villa ' || v.vg_number as villa_name,vg_number,beds,agent_id,baths,sleeps,is_priority,destination_name, ";
        $query .= " destination_id,location_name,case when region_name is not null then region_name || ', ' || location_name else ";
        $query .= " location_name end as location, image, ";
        $query .= " villa_functions.calculate_price(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as price, ";
        $query .= " villa_functions.offer_text(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date ('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as offer_text, ";
        $query .= " tax_percentage,currency from villa_location_vw v where not exists ( select 1 from villa_price vp where vp.villa_id = v.villa_id ";
        $query .= " and to_date('".$date_to."', 'dd/mm/yyyy') between valid_from and valid_to ) and not exists ( select 1 from booking b ";
        $query .= " where to_date('".$date_from."', 'dd/mm/yyyy') between arrive and depart and b.villa_id = v.villa_id ) ";
        $query .= " and destination_id = ".vg_int($destination_id)." and beds >= ".vg_int($no_of_bedrooms)." ";
      
             
       
        $query .= " union ";
        $query .= " select villa_id,'Villa ' || v.vg_number as villa_name,vg_number,beds,baths,agent_id, sleeps,is_priority,destination_name, ";
        $query .= " destination_id,location_name,case when region_name is not null then region_name || ', ' || location_name else ";
        $query .= " location_name end as location,image, ";
        $query .= " villa_functions.calculate_price(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as price, ";
        $query .= " villa_functions.offer_text(v.villa_id, to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($no_of_bedrooms).") as offer_text, ";
        $query .= " tax_percentage,currency from villa_list_villas_vw v where exists ( select 1 from booking b where to_date('".$date_from."', 'dd/mm/yyyy') between arrive and depart ";
        $query .= " and b.villa_id = v.villa_id ) and destination_id = ".vg_int($destination_id)." and beds >= ".vg_int($no_of_bedrooms)." ";
      
             
       
        
        $query .= " ) ";
        
      
        if($price == "low") {
            $query .= " order by destination_name, is_priority desc, vg_number, price, sleeps, availability desc ";
        } elseif($price == "high") {
            $query .= " order by destination_name, is_priority desc, vg_number, price desc, sleeps, availability desc  ";
        } elseif($price == 'recommended') {
            $query .= " order by destination_name, is_priority desc, vg_number, sleeps, availability desc  ";
        } else {
            $query .= " order by location, is_priority desc, vg_number, sleeps, availability desc  ";
        }
      
      
        $query .= " ) ";
        
        $query .= " offset ".$page." rows fetch next 20 rows only ";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "DESTINATION_NAME" => $row["DESTINATION_NAME"] ?? null,
                "DESTINATION_ID" => $row["DESTINATION_ID"] ?? null,
                "LOCATION_NAME"  => $row["LOCATION_NAME"] ?? null,
                "IS_PRIORITY"  => $row["IS_PRIORITY"] ?? null,
                "AGENT_ID"  => $row["AGENT_ID"] ?? null
            ];
            $villas[] = $temp;
        }
        
        return $villas;
    }
}

// Fetch  Villa Images for Slider
if ( ! function_exists( 'fetchVillaSliderImages' ) ) {
    function fetchVillaSliderImages($conn, $villa_id) {
        $query = "select full_img from image where villa_id = " . vg_int($villa_id) . " and floorplan = 0 order by priority";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villa_images = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $villa_images[] = $row["FULL_IMG"];
        }
        return $villa_images;
    }
}

// Fetch  Villa Images for Slider
if ( ! function_exists( 'fetchVillaMinMaxPrice' ) ) {
    function fetchVillaMinMaxPrice($conn, $villa_id) {
        $query = "select min(price), max(price) from villa_price where valid_to >= trunc(sysdate) and villa_id =".vg_int($villa_id);
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villa_price = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            if($row["MIN(PRICE)"] && $row["MAX(PRICE)"]){
                $villa_price['MIN'] = $row["MIN(PRICE)"];
                $villa_price['MAX'] = $row["MAX(PRICE)"];
            }
        }
        return $villa_price;
    }
}

// Fetch Location's regions
if ( ! function_exists( 'fetchLocationRegions' ) ) {
    function fetchLocationRegions($conn, $location_ids) {
        $query = "select region_id, name from region where location_id in (" . vg_int_list($location_ids) . ")";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $regions = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $regions[$row['REGION_ID']] = $row['NAME'];
        }
        return $regions;
    }
}

// Fetch Villa Details
if ( ! function_exists( 'fetchVillaDetails' ) ) {
    function fetchVillaDetails($conn, $vg_number, $bedrooms = 1){
        // $query = "select vg_number, villa_id,villa_description, villa_summary, baths, sleeps ,is_priority,  beds , agent_id, agent_name, villa_summary, location_id, location_name, destination_id, region_name, destination_name, ";
        // $query .= "long_destination_description , oceanfront, oceanview, pool, ac, maid, chef, broadband, tennis, daily_breakfast, car_and_driver, destination_id ";
        // $query .= " , (select full_img from image i where i.villa_id = v.villa_id and i.floorplan = 0 and priority = 1 fetch first row only) As random_villa_image, agent_name, agent_image, agent_email, booknow, latitude, longitude ";
        // $query .= " , villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , ". vg_int($bedrooms) ." ) AS price ";
        // $query .= " , villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , ". vg_int($bedrooms) ."  ) AS offer_text ";
        // $query .= " , tax_percentage ";
        // $query .= " , currency  ";
        // $query .= " from villa_location_vw v ";
        // $query .= " where vg_number = " . $vg_number;
        
        
        $query = "select vg_number, villa_id,villa_description, villa_summary, baths, sleeps ,is_priority,  beds , agent_id, agent_name, villa_summary, location_id, location_name, destination_id, region_name, destination_name, ";
        $query .= "long_destination_description , oceanfront, oceanview, pool, ac, maid, chef, broadband, tennis, daily_breakfast, car_and_driver, destination_id ";
        $query .= " , (select full_img from image i where i.villa_id = v.villa_id and i.floorplan = 0 and priority = 1 fetch first row only) As random_villa_image, agent_name, agent_image, agent_email, booknow, latitude, longitude ";
        $query .= " , villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , ". vg_int($bedrooms) ." ) AS price ";
        $query .= " , villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , ". vg_int($bedrooms) ."  ) AS offer_text ";
        $query .= " , tax_percentage ";
        $query .= " , currency , destination_image, villa_title";
        $query .= " from villa_location_vw v ";
        $query .= " where vg_number = " . vg_int($vg_number);

        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villa_details = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
         
            $villa_details["VG_NUMBER"] = $row["VG_NUMBER"];
            $villa_details["VILLA_ID"] = $row["VILLA_ID"];
            $villa_details["VILLA_DESCRIPTION"] = $row["VILLA_DESCRIPTION"];
            $villa_details["VILLA_SUMMARY"] = $row["VILLA_SUMMARY"];
            $villa_details["LOCATION_NAME"] = $row["LOCATION_NAME"];
            $villa_details["LOCATION_ID"] = $row["LOCATION_ID"];
            $villa_details["DESTINATION_NAME"] = $row["DESTINATION_NAME"];
            $villa_details["DESTINATION_ID"] = $row["DESTINATION_ID"];
            $villa_details["LONG_DESTINATION_DESCRIPTION"] = $row["LONG_DESTINATION_DESCRIPTION"];
            $villa_details["OCEANFRONT"] = $row["OCEANFRONT"];
            $villa_details["OCEANVIEW"] = $row["OCEANVIEW"];
            $villa_details["POOL"] = $row["POOL"];
            $villa_details["AC"] = $row["AC"];
            $villa_details["MAID"] = $row["MAID"];
            $villa_details["CHEF"] = $row["CHEF"];
            $villa_details["BROADBAND"] = $row["BROADBAND"];
            $villa_details["DAILY_BREAKFAST"] = $row["DAILY_BREAKFAST"];
            $villa_details["CAR_AND_DRIVER"] = $row["CAR_AND_DRIVER"];
            $villa_details["DESTINATION_ID"] = $row["DESTINATION_ID"];
            $villa_details["RANDOM_VILLA_IMAGE"] = $row["RANDOM_VILLA_IMAGE"];
            $villa_details["BATHS"] = $row["BATHS"];
            $villa_details["SLEEPS"] = $row["SLEEPS"];
            $villa_details["BEDS"] = $row["BEDS"];
            $villa_details["BEDS"] = $row["BEDS"];
            $villa_details["AGENT_ID"] = $row["AGENT_ID"];
            $villa_details["AGENT_NAME"] = $row["AGENT_NAME"];
            $villa_details["AGENT_IMAGE"] = $row["AGENT_IMAGE"];
            $villa_details["AGENT_EMAIL"] = $row["AGENT_EMAIL"];
            $villa_details["BOOKNOW"] = $row["BOOKNOW"];
            $villa_details["LATITUDE"] = $row["LATITUDE"];
            $villa_details["LONGITUDE"] = $row["LONGITUDE"];
            $villa_details["IS_PRIORITY"] = $row["IS_PRIORITY"];
            $villa_details["PRICE"] = $row["PRICE"];
            $villa_details["OFFER_TEXT"] = $row["OFFER_TEXT"];
            $villa_details["CURRENCY"] = $row["CURRENCY"];
            $villa_details["VILLA_TITLE"] = $row["VILLA_TITLE"];
            $villa_details["TAX_PERCENTAGE"] = $row["TAX_PERCENTAGE"];
            $villa_details["REGION_NAME"] = $row["REGION_NAME"];
            $villa_details["DESTINATION_IMAGE"] = $row["DESTINATION_IMAGE"];
            
            
        }
        return $villa_details;
    }
}

// Fetch Destination
if ( ! function_exists( 'fetchVillaReviews' ) ) {
    function fetchVillaReviews($conn, $villa_id) { 
        $stid = oci_parse($conn, "Select * from review where villa_id =".vg_int($villa_id));
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $reviews = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $reviews[] = $row;
        }
        return $reviews;
    }
}
// Fetch Destination
if ( ! function_exists( 'fetchDestination' ) ) {
    function fetchDestination($conn, $destination_id) {
        // Prepare the statement
        $stid = oci_parse($conn, 'SELECT name, destination_id, image FROM destination WHERE destination_id = ' . vg_int($destination_id));
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $destination = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $destination["NAME"] = $row["NAME"];
            $destination["DESTINATION_ID"] = $row["DESTINATION_ID"];
            $destination["IMAGE"] = $row["IMAGE"];
        }
        return $destination;
    }
}

// Fetch Location
if ( ! function_exists( 'fetchLocation' ) ) {
    function fetchLocation($conn, $location_id) {
        // Prepare the statement
        $stid = oci_parse($conn, 'SELECT name, location_id FROM location WHERE location_id = ' . vg_int($location_id));
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $location = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $location["NAME"] = $row["NAME"];
            $location["LOCATION_ID"] = $row["LOCATION_ID"];
        }
        return $location;
    }
}


// Fetch Similar Luxury Villas
if ( ! function_exists( 'fetchSimilarLaxuryVillas' ) ) {
    function fetchSimilarLaxuryVillas($conn, $destination_id, $villa_id,$beds) {
        // $query = "select * from ( ";
        // $query .= " select vg_number, villa_id, baths, sleeps , beds, villa_description, location_name, destination_name, DBMS_RANDOM.value(low => 1, high => 10) randomnr ";
        // $query .= ", (select full_img from image i where i.villa_id = v.villa_id and floorplan = 0 and priority = 1 fetch first row only) random_villa_image ";
        // $query .= " from villa_location_vw v ";
        // $query .= " where destination_id= " . $destination_id . " and vg_number != " . $villa_id;
        // $query .= " order by randomnr) ";
        // $query .= " fetch  first 3 rows only";
        
          $query = "select * from ( ";
        $query .= " select vg_number, villa_id, baths, sleeps , beds, villa_description, location_name, destination_name, DBMS_RANDOM.value(low => 1, high => 10) randomnr ";
        $query .= ", (select full_img from image i where i.villa_id = v.villa_id and floorplan = 0 and priority = 1 fetch first row only) random_villa_image ";
        $query .= " from villa_location_vw v ";
        $query .= " where destination_id= " . vg_int($destination_id) . " and vg_number != " . vg_int($villa_id) ." and beds >= ".vg_int($beds);
        $query .= " order by randomnr) ";
        $query .= " fetch  first 3 rows only";
        
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $similar_villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp_similar_villas = [];
            $temp_similar_villas["VG_NUMBER"] = $row["VG_NUMBER"];
            $temp_similar_villas["VILLA_ID"] = $row["VILLA_ID"];
            $temp_similar_villas["VILLA_DESCRIPTION"] = $row["VILLA_DESCRIPTION"];
            $temp_similar_villas["LOCATION_NAME"] = $row["LOCATION_NAME"];
            $temp_similar_villas["DESTINATION_NAME"] = $row["DESTINATION_NAME"];
            $temp_similar_villas["RANDOMNR"] = $row["RANDOMNR"];
            $temp_similar_villas["RANDOM_VILLA_IMAGE"] = $row["RANDOM_VILLA_IMAGE"];
            $temp_similar_villas["BATHS"] = $row["BATHS"];
            $temp_similar_villas["SLEEPS"] = $row["SLEEPS"];
            $temp_similar_villas["BEDS"] = $row["BEDS"];
            
            $similar_villas[] = $temp_similar_villas;
        }
        return $similar_villas;
    }
}

// Fetch Unavailable Dates
if ( ! function_exists( 'fetchUnavailableDates' ) ) {
    function fetchUnavailableDates($conn, $villa_id) {
        $query = "select arrive,depart from booking where arrive>=trunc(sysdate) and villa_id = " . vg_int($villa_id) . " order by arrive";
        
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $unavailable_dates = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {

            $temp_unavailable_dates = [];
            $temp_unavailable_dates["ARRIVE"] = $row["ARRIVE"];
            $temp_unavailable_dates["DEPART"] = $row["DEPART"];
            
            $unavailable_dates[] = $temp_unavailable_dates;
        }
    
        return $unavailable_dates;
    }
}

// Fetch Villa Rates Headers
if( ! function_exists( 'fetchVillaRatesHeaders' ) ) {
    function fetchVillaRatesHeaders($conn, $villa_id) {
        $query = "select nr_of_rooms ";
        $query .= " , to_char(min(vp.price), '9,999,999') as min_price ";
        $query .= " , to_char(max(vp.price), '9,999,999') as max_price ";
        $query .= " ,decode(nvl(max(v.tax_percentage),0),0,null,' + ' ||max(v.tax_percentage) || '% service charge, taxes, etc')as tax_percentage    ";
        $query .= " from villa_price vp,villa v  ";
        $query .= " where v.villa_id = vp.villa_id ";
        $query .= " and vp.villa_id = :villa_id";
        $query .= " group by nr_of_rooms ";
        $query .= " order by nr_of_rooms";

        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        oci_bind_by_name($stid, ':villa_id', $villa_id);

        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        $villa_rates_header = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp_villa_rates_header = [];
            $temp_villa_rates_header["NR_OF_ROOMS"] = $row["NR_OF_ROOMS"];
            $temp_villa_rates_header["MIN_PRICE"] = $row["MIN_PRICE"];
            $temp_villa_rates_header["MAX_PRICE"] = $row["MAX_PRICE"];
            $temp_villa_rates_header["TAX_PERCENTAGE"] = $row["TAX_PERCENTAGE"];
            
            $villa_rates_header[] = $temp_villa_rates_header;
        }
        return $villa_rates_header;
    }
}

// Fetch Villa Rates Data
if( ! function_exists( 'fetchVillaRatesData' ) ) {
    function fetchVillaRatesData($conn, $villa_id, $no_of_rooms) {
        $query = "select pt.price_name ";
        $query .= " , vp.valid_from ";
        $query .= " , vp.valid_to ";
        $query .= " , to_char(vp.valid_from, 'Mon fmdd, yyyy') || ' - ' || to_char(vp.valid_to, 'Mon fmdd, yyyy') as period ";
        $query .= " , v.currency_code || to_char(vp.price, '9,999,999') || ' per night' as price ";
        $query .= " , vp.min_nr_days || ' Night min' as min_nr_days ";
        $query .= " from villa_price vp ";
        $query .= " , price_type pt ";
        $query .= " , villa v ";
        $query .= " where pt.price_type_id = vp.price_type_id ";
        $query .= " and v.villa_id = vp.villa_id ";
        $query .= " and vp.villa_id = :villa_id";
        $query .= " and vp.valid_from < to_date('01-jan-3000','dd-mon-yyyy')";
        if( $no_of_rooms ) {
            $query .= " and vp.nr_of_rooms = :no_of_rooms";
        } else {
            $query .= " and vp.nr_of_rooms IS NULL ";
        }
        $query .= " order by vp.valid_to";

        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        oci_bind_by_name($stid, ':villa_id', $villa_id);
        if( $no_of_rooms ) {
            oci_bind_by_name($stid, ':no_of_rooms', $no_of_rooms);
        }

        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villa_rates_data = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp_villa_rates_data = [];
            $temp_villa_rates_data["PRICE_NAME"] = $row["PRICE_NAME"];
            $temp_villa_rates_data["VALID_FROM"] = $row["VALID_FROM"];
            $temp_villa_rates_data["VALID_TO"] = $row["VALID_TO"];
            $temp_villa_rates_data["PERIOD"] = $row["PERIOD"];
            $temp_villa_rates_data["PRICE"] = $row["PRICE"];
            $temp_villa_rates_data["MIN_NR_DAYS"] = $row["MIN_NR_DAYS"];
            
            $villa_rates_data[] = $temp_villa_rates_data;
        }
        return $villa_rates_data;
    }
}

// Fetch Villas by Destination Name
if( !function_exists( 'fetchVillasByDestinationName' ) ) {
    function fetchVillasByDestinationName($conn, $destination_name, $page = 1, $no_of_bedrooms = 1) {
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }
        
        
        $query  = " select villa_id ";
        $query .= " , 'Villa ' || v.vg_number as villa_name ";
        $query .= " , vg_number ";
        $query .= " , beds, agent_id ";
        $query .= " , baths ";
        $query .= " , sleeps ";
        $query .= " , destination_name , is_priority, location_name ";
        $query .= " , location_name || case when region_name is not null then ', ' || region_name else null end as location ";
        $query .= " , image ";
        $query .= " , villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price ";
        $query .= " , tax_percentage ";
        $query .= " , currency ";
        $query .= " , 2 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where upper(destination_name) = upper(replace('" . $destination_name . "', '-', ' ')) ";
        $query .= " offset ". $page ." rows ";
        $query .= " fetch next 20 rows only ";

        
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "LOCATION_NAME" => $row["LOCATION_NAME"] ?? null,
                "DESTINATION_NAME" => $row["DESTINATION_NAME"] ?? null,
                "IS_PRIORITY" => $row["IS_PRIORITY"] ?? null,
                "AGENT_ID" => $row["AGENT_ID"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
    }
}

// Fetch Villas by Destination Name
if( !function_exists( 'fetchVillasByLocationName' ) ) {
    function fetchVillasByLocationName($conn, $location_name, $page = 1, $price="low", $no_of_bedrooms = 1) {
        
       
        
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }
        
        $query = " select villa_id ";
        $query .= " , 'Villa ' || v.vg_number as villa_name ";
        $query .= " , vg_number ";
        $query .= " , destination_name, is_priority ";
        $query .= " , beds, agent_id ";
        $query .= " , baths ";
        $query .= " , sleeps, location_name ";
        $query .= " , location_name || case when region_name is not null then ', ' || region_name else null end as location ";
        $query .= " , image, ";  // <--- comma added here
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price ";
        $query .= " , tax_percentage ";
        $query .= " , currency  ";
        $query .= " , 2 orderno ";
        $query .= " from villa_location_vw v  ";
        $query .= " where upper(location_name) = upper(replace('" . $location_name . "', '-', ' ')) or upper(region_name) = upper(replace('" . $location_name . "', '-', ' '))";
        $query .= " order by destination_name, is_priority desc, vg_number, sleeps";
        $query .= " offset ". $page ." rows ";
        $query .= " fetch next 20 rows only ";

        
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            
          
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
          
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
      
        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "DESTINATION_NAME" => $row["DESTINATION_NAME"] ?? null,
                "LOCATION_NAME" => $row["LOCATION_NAME"] ?? null,
                "IS_PRIORITY" => $row["IS_PRIORITY"] ?? null,
                "AGENT_ID" => $row["AGENT_ID"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
    }
}

// // Fetch Absolute Villas
if ( ! function_exists( 'fetchAbsoluteBeachFrontVillas' ) ) {
    function fetchAbsoluteBeachFrontVillas($conn, $page = 1, $price = "", $destination="", $location="") {
        
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }

        $query  = "select * ";
        $query .= " from ( select * ";
        $query .= " from ( select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1  ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 1 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from villa_price vp ";
        $query .= " where vp.villa_id = v.villa_id ";
        $query .= " and trunc(sysdate+7)  between valid_from and valid_to) ";
        $query .= " and exists (select 1 ";
        $query .= " from booking b ";
        $query .= " where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id) ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." )";
        }
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14)  , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 2 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where not exists (select 1 from villa_price vp where vp.villa_id = v.villa_id ) ";
        $query .= " and exists (select 1 from booking b where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id) ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." )";
        }
        //$query .= " and destination_id = 6 and beds >= 4 ";
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) ,trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 3 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from booking b where trunc(sysdate+7) between arrive and depart and b.villa_id = v.villa_id)   ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        $query .= " ) ";
        //$query .= " and destination_id = 6 and beds >= 4 ) ";
        $query .= " where absolute_beachfront = 1 ";
        $query .= " offset ". $page ." rows fetch next 20 rows only) ";
        if($price == "low") {
            $query .= " order by destination_name, price, orderno, sleeps ";
        } elseif($price == "high") {
            $query .= " order by destination_name, price desc, orderno, sleeps ";
        } elseif($price == 'recommended') {
            $query .= " order by destination_name, is_priority desc, orderno, sleeps ";
        } else {
            $query .= " order by orderno,location,sleeps ";
        }
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "IS_PRIORITY" => $row["IS_PRIORITY"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
    }
}

// // Fetch Wedding Villas
if ( ! function_exists( 'fetchWeddingVillas' ) ) {
    function fetchWeddingVillas($conn, $page = 1, $price = "", $destination="", $location="") {
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }

        $query  = "select * ";
        $query .= " from ( select * ";
        $query .= " from ( select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1  ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 1 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from villa_price vp ";
        $query .= " where vp.villa_id = v.villa_id ";
        $query .= " and trunc(sysdate+7)  between valid_from and valid_to) ";
        $query .= " and exists (select 1 ";
        $query .= " from booking b ";
        $query .= " where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id) ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14)  , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 2 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where not exists (select 1 from villa_price vp where vp.villa_id = v.villa_id ) ";
        $query .= " and exists (select 1 from booking b where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id)  ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) ,trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 3 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from booking b where trunc(sysdate+7) between arrive and depart and b.villa_id = v.villa_id)  ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        $query .= " ) ";
        $query .= " where wedding = 1 ";
        $query .= " offset ". $page ." rows fetch next 20 rows only) ";
        if($price == "low") {
            $query .= " order by destination_name, price, orderno, sleeps ";
        } elseif($price == "high") {
            $query .= " order by destination_name, price desc, orderno, sleeps ";
        } elseif($price == 'recommended') {
            $query .= " order by destination_name, is_priority desc, orderno, sleeps ";
        } else {
            $query .= " order by orderno,location,sleeps ";
        }
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "IS_PRIORITY" => $row["IS_PRIORITY"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
    }
}

// // Fetch Holiday Season Villas
if ( ! function_exists( 'fetchHollidaySeasonVillas' ) ) {
    function fetchHollidaySeasonVillas($conn, $page = 1, $price = "", $destination="", $location="") {
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }

        $query  = "select * ";
        $query .= " from ( select * ";
        $query .= " from ( select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps , is_priority, destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1  ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 1 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from villa_price vp ";
        $query .= " where vp.villa_id = v.villa_id ";
        $query .= " and trunc(sysdate+7)  between valid_from and valid_to) ";
        $query .= " and exists (select 1 ";
        $query .= " from booking b ";
        $query .= " where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id) ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps , is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14)  , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 2 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where not exists (select 1 from villa_price vp where vp.villa_id = v.villa_id ) ";
        $query .= " and exists (select 1 from booking b where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id)  ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps , is_priority, destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) ,trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 3 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from booking b where trunc(sysdate+7) between arrive and depart and b.villa_id = v.villa_id)  ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        $query .= " ) ";
        $query .= " where holiday_season = 1 ";
        $query .= " offset ". $page ." rows fetch next 20 rows only) ";
        if($price == "low") {
            $query .= " order by destination_name, price, orderno, sleeps ";
        } elseif($price == "high") {
            $query .= " order by destination_name, price desc, orderno, sleeps ";
        } elseif($price == 'recommended') {
            $query .= " order by destination_name, is_priority desc, orderno, sleeps ";
        } else {
            $query .= " order by orderno,location,sleeps ";
        }
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "IS_PRIORITY" => $row["IS_PRIORITY"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
    }
}

// // Fetch Corporate Retrat Villas
if ( ! function_exists( 'fetchCorporateRetreatsVillas' ) ) {
    function fetchCorporateRetreatsVillas($conn, $page = 1, $price = "", $destination="", $location="") {
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }

        $query  = "select * ";
        $query .= " from ( select * ";
        $query .= " from ( select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1  ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 1 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from villa_price vp ";
        $query .= " where vp.villa_id = v.villa_id ";
        $query .= " and trunc(sysdate+7)  between valid_from and valid_to) ";
        $query .= " and exists (select 1 ";
        $query .= " from booking b ";
        $query .= " where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id) ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14)  , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 2 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where not exists (select 1 from villa_price vp where vp.villa_id = v.villa_id ) ";
        $query .= " and exists (select 1 from booking b where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id)  ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) ,trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 3 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from booking b where trunc(sysdate+7) between arrive and depart and b.villa_id = v.villa_id)  ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        $query .= " ) ";
        $query .= " where corporate_retreats = 1 ";
        $query .= " offset ". $page ." rows fetch next 20 rows only) ";
        if($price == "low") {
            $query .= " order by destination_name, price, orderno, sleeps ";
        } elseif($price == "high") {
            $query .= " order by destination_name, price desc, orderno, sleeps ";
        } elseif($price == 'recommended') {
            $query .= " order by destination_name, is_priority desc, orderno, sleeps ";
        } else {
            $query .= " order by orderno,location,sleeps ";
        }
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "IS_PRIORITY" => $row["IS_PRIORITY"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
    }
}

// // Fetch Exclusive Villas
if ( ! function_exists( 'fetchExclusiveVillas' ) ) {
    function fetchExclusiveVillas($conn, $page = 1, $price = "", $destination="", $location="") {
        if($page == 1) {
            $page = 0;
        }else {
            $page = ($page - 1) * 20;
        }

        $query  = "select * ";
        $query .= " from ( select * ";
        $query .= " from ( select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps ,is_priority ,  destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1  ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 1 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from villa_price vp ";
        $query .= " where vp.villa_id = v.villa_id ";
        $query .= " and trunc(sysdate+7)  between valid_from and valid_to) ";
        $query .= " and exists (select 1 ";
        $query .= " from booking b ";
        $query .= " where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id) ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14)  , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 2 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where not exists (select 1 from villa_price vp where vp.villa_id = v.villa_id ) ";
        $query .= " and exists (select 1 from booking b where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id)  ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps, is_priority , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) ,trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, 3 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from booking b where trunc(sysdate+7) between arrive and depart and b.villa_id = v.villa_id)  ";
        if($destination) {
            $query .= " and destination_id = ".vg_int($destination);
        }
        if($location) {
            $query .= " and location_id in ( ".vg_int_list($location)." ) ";
        }
        $query .= " ) ";
        $query .= " where exclusive_villa = 1 ";
        $query .= " offset ". $page ." rows fetch next 20 rows only) ";
        if($price == "low") {
            $query .= " order by destination_name, price, orderno, sleeps ";
        } elseif($price == "high") {
            $query .= " order by destination_name, price desc, orderno, sleeps ";
        } elseif($price == 'recommended') {
            $query .= " order by destination_name, is_priority desc, orderno, sleeps ";
        } else {
            $query .= " order by orderno,location,sleeps ";
        }
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "IS_PRIORITY" => $row["IS_PRIORITY"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
    }
}

// Fetch Favorites Villa Details
if ( ! function_exists( 'fetchFavoritesVillaDetails' ) ) {
    function fetchFavoritesVillaDetails($conn, $vg_numbers){
        $query = "select vg_number, villa_id,villa_description, villa_summary, villa_name, baths, sleeps, is_priority , beds , agent_name, villa_summary, location_name, destination_name, ";
        $query .= "long_destination_description , oceanfront, oceanview, pool, ac, maid, chef, broadband, tennis, daily_breakfast, car_and_driver, destination_id ";
        $query .= " , (select full_img from image i where i.villa_id = v.villa_id fetch first row only) random_villa_image, agent_name, agent_image, agent_email, booknow, latitude, longitude ";
        $query .= " from villa_location_vw v ";
        $query .= " where vg_number IN (" . vg_int_list($vg_numbers) . ")";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $villa_details["VG_NUMBER"] = $row["VG_NUMBER"];
            $villa_details["VILLA_ID"] = $row["VILLA_ID"];
            $villa_details["VILLA_NAME"] = $row["VILLA_NAME"];
            $villa_details["VILLA_DESCRIPTION"] = $row["VILLA_DESCRIPTION"];
            $villa_details["VILLA_SUMMARY"] = $row["VILLA_SUMMARY"];
            $villa_details["LOCATION_NAME"] = $row["LOCATION_NAME"];
            $villa_details["DESTINATION_NAME"] = $row["DESTINATION_NAME"];
            $villa_details["LONG_DESTINATION_DESCRIPTION"] = $row["LONG_DESTINATION_DESCRIPTION"];
            $villa_details["OCEANFRONT"] = $row["OCEANFRONT"];
            $villa_details["OCEANVIEW"] = $row["OCEANVIEW"];
            $villa_details["POOL"] = $row["POOL"];
            $villa_details["AC"] = $row["AC"];
            $villa_details["MAID"] = $row["MAID"];
            $villa_details["CHEF"] = $row["CHEF"];
            $villa_details["BROADBAND"] = $row["BROADBAND"];
            $villa_details["DAILY_BREAKFAST"] = $row["DAILY_BREAKFAST"];
            $villa_details["CAR_AND_DRIVER"] = $row["CAR_AND_DRIVER"];
            $villa_details["DESTINATION_ID"] = $row["DESTINATION_ID"];
            $villa_details["RANDOM_VILLA_IMAGE"] = $row["RANDOM_VILLA_IMAGE"];
            $villa_details["BATHS"] = $row["BATHS"];
            $villa_details["SLEEPS"] = $row["SLEEPS"];
            $villa_details["BEDS"] = $row["BEDS"];
            $villa_details["BEDS"] = $row["BEDS"];
            $villa_details["AGENT_NAME"] = $row["AGENT_NAME"];
            $villa_details["AGENT_IMAGE"] = $row["AGENT_IMAGE"];
            $villa_details["AGENT_EMAIL"] = $row["AGENT_EMAIL"];
            $villa_details["BOOKNOW"] = $row["BOOKNOW"];
            $villa_details["LATITUDE"] = $row["LATITUDE"];
            $villa_details["LONGITUDE"] = $row["LONGITUDE"];
            $villa_details["IS_PRIORITY"] = $row["IS_PRIORITY"];
            $villas[] = $villa_details;
        }
        return $villas;
    }
}

// Fetch Destination
if ( ! function_exists( 'fetchDestinationByName' ) ) {
    function fetchDestinationByName($conn, $destination_name) {
        $query = "SELECT name, destination_id, image FROM destination WHERE name = '" . $destination_name . "'";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $destination = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $destination["NAME"] = $row["NAME"];
            $destination["DESTINATION_ID"] = $row["DESTINATION_ID"];
            $destination["IMAGE"] = $row["IMAGE"];
        }
        return $destination;
    }
}

// Fetch Location
if ( ! function_exists( 'fetchLocationByName' ) ) {
    function fetchLocationByName($conn, $location_name) {
        // Prepare the statement
        $stid = oci_parse($conn, "SELECT name, location_id FROM location WHERE name = '" . $location_name . "'");
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $location = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $location["NAME"] = $row["NAME"];
            $location["LOCATION_ID"] = $row["LOCATION_ID"];
        }
        return $location;
    }
}

// Fetch Location
if ( ! function_exists( 'fetchRegionByName' ) ) {
    function fetchRegionByName($conn, $region_name) {
        // Prepare the statement
        $stid = oci_parse($conn, "SELECT * FROM region WHERE name = '" . $region_name . "'");
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $region = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $region["NAME"] = $row["NAME"];
            $region["REGION_ID"] = $row["REGION_ID"];
        }
        return $region;
    }
}

if ( ! function_exists( 'fetchVillasByPriceFilter' ) ) {
    function fetchVillasByPriceFilter($conn, $destination_id , $price = "low") {
        $query = "select  ";
        $query .= " from ( select * ";
        $query .= " from ( select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1  ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, ";
        $query .= " is_priority, 1 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from villa_price vp ";
        $query .= " where vp.villa_id = v.villa_id ";
        $query .= " and trunc(sysdate+7)  between valid_from and valid_to) ";
        $query .= " and exists (select 1 ";
        $query .= " from booking b ";
        $query .= " where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id) ";
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14)  , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, ";
        $query .= " is_priority, 2 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where not exists (select 1 from villa_price vp where vp.villa_id = v.villa_id ) ";
        $query .= " and exists (select 1 from booking b where trunc(sysdate+7)  not between arrive and depart and b.villa_id = v.villa_id) and destination_id = ".vg_int($destination_id)." and beds >= 4 ";
        $query .= " union ";
        $query .= " select villa_id , 'Villa ' || v.vg_number as villa_name , vg_number , beds , baths , sleeps , destination_name, destination_id , ";
        $query .= " location_name || case when region_name is not null then ', ' || region_name else null end as location , image , ";
        $query .= " villa_functions.calculate_price( v.villa_id , trunc(sysdate+7) , trunc(sysdate+14) , 1 ) AS price , ";
        $query .= " villa_functions.offer_text( v.villa_id , trunc(sysdate+7) ,trunc(sysdate+14) , 1 ) AS offer_text , ";
        $query .= " tax_percentage , currency , absolute_beachfront, wedding, corporate_retreats, holiday_season, exclusive_villa, ";
        $query .= " is_priority, 3 orderno ";
        $query .= " from villa_location_vw v ";
        $query .= " where exists (select 1 from booking b where trunc(sysdate+7) between arrive and depart and b.villa_id = v.villa_id)  ) ";
        $query .= " where <whatever your filter criteria are>  ) ";
        if($price == "low") {
            $query .= " order by destination_name, location, price, orderno, sleeps ";
        }elseif($price == "high") {
            $query .= " order by destination_name, location, price desc, orderno, sleeps ";
        }else {
            $query .= " order by destination_name, location, is_priority, orderno, sleeps ";
        }
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "VILLA_ID" => $row["VILLA_ID"] ?? null,
                "VILLA_NAME" => $row["VILLA_NAME"] ?? null,
                "VG_NUMBER" => $row["VG_NUMBER"] ?? null,
                "BEDS" => $row["BEDS"] ?? null,
                "BATHS" => $row["BATHS"] ?? null,
                "SLEEPS" => $row["SLEEPS"] ?? null,
                "LOCATION" => $row["LOCATION"] ?? null,
                "IMAGE" => $row["IMAGE"] ?? null,
                "PRICE" => $row["PRICE"] ?? null,
                "OFFER_TEXT" => $row["OFFER_TEXT"] ?? null,
                "TAX_PERCENTAGE" => $row["TAX_PERCENTAGE"] ?? null,
                "CURRENCY" => $row["CURRENCY"] ?? null,
                "IS_PRIORITY" => $row["IS_PRIORITY"] ?? null
            ];
            $villas[] = $temp;
        }
        return $villas;
        
    }
}

if ( ! function_exists( 'enquiry' ) ) {
    function enquiry($conn, $title, $firstname, $lastname, $email_1, $phone_work, $phone_mobile, $address, $suburb, $state, $postcode, 
    $country_id, $agent_id, $opt_in_subscription, $villa_id, $villa_number, $arrive_at, $depart_at, $dates_flexible, 
    $num_people, $num_children, $nightly_budget, $comments) { 
        
        $tdin = oci_parse($conn, 'begin :retval := villa_functions.enquiry_form(
        :p_title, :p_firstname, :p_lastname, :p_email_1, :p_phone_work, :p_phone_mobile, :p_address, :p_suburb, 
        :p_state, :p_postcode, :p_country_id, :p_agent_id, :p_opt_in_subscription, :p_villa_id, :p_villa_number, 
        :p_arrive_at, :p_depart_at, :p_dates_flexible, :p_num_people, :p_num_children, :p_nightly_budget, 
        :p_comments); end;');
        
        oci_bind_by_name($tdin, ':p_title', $title, 10);
        oci_bind_by_name($tdin, ':p_firstname', $firstname, 60);
        oci_bind_by_name($tdin, ':p_lastname', $lastname, 60);
        oci_bind_by_name($tdin, ':p_email_1', $email_1, 120);
        oci_bind_by_name($tdin, ':p_phone_work', $phone_work, 30);
        oci_bind_by_name($tdin, ':p_phone_mobile', $phone_mobile, 30);
        oci_bind_by_name($tdin, ':p_address', $address, 240);
        oci_bind_by_name($tdin, ':p_suburb', $suburb, 60);
        oci_bind_by_name($tdin, ':p_state', $state, 60);
        oci_bind_by_name($tdin, ':p_postcode', $postcode, 10);
        oci_bind_by_name($tdin, ':p_country_id', $country_id, 10);
        oci_bind_by_name($tdin, ':p_agent_id', $agent_id, 10);
        oci_bind_by_name($tdin, ':p_opt_in_subscription', $opt_in_subscription, 1); 
        oci_bind_by_name($tdin, ':p_villa_id', $villa_id, 10);
        oci_bind_by_name($tdin, ':p_villa_number', $villa_number, 10);
        oci_bind_by_name($tdin, ':p_arrive_at', $arrive_at, 10);
        oci_bind_by_name($tdin, ':p_depart_at', $depart_at, 10);
        oci_bind_by_name($tdin, ':p_dates_flexible', $dates_flexible, 1);
        oci_bind_by_name($tdin, ':p_num_people', $num_people, 3);
        oci_bind_by_name($tdin, ':p_num_children', $num_children, 3);
        oci_bind_by_name($tdin, ':p_nightly_budget', $nightly_budget, 30);
        oci_bind_by_name($tdin, ':p_comments', $comments, 3000);
        
        $retval = null;
        oci_bind_by_name($tdin,':retval' ,$retval, 240);
        
        if (!$tdin) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($tdin);
        if (!$r) {
            $e = oci_error($tdin);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        return $retval;
    }
}

if ( ! function_exists( 'enquiry_footer' ) ) {
    function enquiry_footer($conn, $firstname, $lastname, $email_1, $phone_mobile,$destination_id, $location_id, $comments) {   
        
        $tdin = oci_parse($conn, 'begin :retval := villa_functions.enquiry_form(
        p_firstname => :p_firstname, p_lastname => :p_lastname,
        p_email_1 => :p_email_1,  p_phone_mobile => :p_phone_mobile,  
        p_destination_id => :p_destination_id, p_location_id => :p_location_id, 
        p_comments =>  :p_comments); end;');
        
        oci_bind_by_name($tdin, ':p_firstname', $firstname, 60);
        oci_bind_by_name($tdin, ':p_lastname', $lastname, 60);
        oci_bind_by_name($tdin, ':p_email_1', $email_1, 120);
        oci_bind_by_name($tdin, ':p_phone_mobile', $phone_mobile, 30);
        oci_bind_by_name($tdin, ':p_destination_id', $destination_id, 10);
        oci_bind_by_name($tdin, ':p_location_id', $location_id, 10);
        oci_bind_by_name($tdin, ':p_comments', $comments, 3000);
        
        $retval = null;
        oci_bind_by_name($tdin,':retval' ,$retval, 240);
        
        if (!$tdin) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($tdin);
        if (!$r) {
            $e = oci_error($tdin);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        return $retval;
    }
}


if ( ! function_exists( 'insertClientData' ) ) {
    function insertClientData($conn, $title, $fname, $lname, $email, $phone, $homePhone, $country, $address, $city, $zip, $questions, $date_from, $date_to, $agent_id, $newsletter) { 
        
        $query = "INSERT INTO client(title ,firstname ,lastname ,email_1 ,phone_work ,phone_mobile ,address ";
        $query.= " ,suburb ,state ,postcode ,country_id , agent_id, opt_in_subscription, created_at, updated_at, updated_by ) ";
        $query.= " values ( :title , :firstname , :lastname , :email_1 , :phone_work , :phone_mobile , :address, :suburb , :state , :postcode , :country_id , :agent_id, :opt_in_subscription, trunc(sysdate) , trunc(sysdate) , '1' ) ";
        $query.= " RETURNING client_id INTO :client_id_v";
        $stid = oci_parse($conn, $query);
        
        oci_bind_by_name($stid, ":title", $title, 10);
        oci_bind_by_name($stid, ":firstname", $fname, 60);
        oci_bind_by_name($stid, ":lastname", $lname, 60);
        oci_bind_by_name($stid, ":email_1", $email, 120);
        oci_bind_by_name($stid, ":phone_work", $phone, 30);
        oci_bind_by_name($stid, ":phone_mobile", $homePhone, 30);
        oci_bind_by_name($stid, ":address", $address, 240);
        oci_bind_by_name($stid, ":suburb", $city, 60);
        oci_bind_by_name($stid, ":state", $state, 60);
        oci_bind_by_name($stid, ":postcode", $zip, 10);
        oci_bind_by_name($stid, ":country_id", $country, 10);
        oci_bind_by_name($stid, ":agent_id", $agent_id, 10);
        oci_bind_by_name($stid, ":opt_in_subscription", $newsletter, 1);
        
        
        $client_id_v = null;
        oci_bind_by_name($stid, ":client_id_v", $client_id_v, 15);

        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        return $client_id_v;
    }
}



if ( ! function_exists( 'insertRefferals' ) ) {
    function insertRefferals($conn, $refferals = [], $client_id = "") {
        
        
        // Prepare the SQL query with placeholders for each row of data
        $query = 'INSERT INTO REFERRAL (CLIENT_ID, TITLE, LAST_NAME, FIRST_NAME, EMAIL_ADDRESS) VALUES ';
        $params = array();
        $paramIndex = 1;
        foreach ($refferals as $row) {
            // Add placeholders for this row of data
            $query .= "(:client_id{$paramIndex}, :title{$paramIndex}, :last_name{$paramIndex}, :first_name{$paramIndex}, :email_address{$paramIndex}), ";
            // Add parameter values for this row of data
            $params[":client_id{$paramIndex}"] = $client_id;
            $params[":title{$paramIndex}"] = $row['title'];
            $params[":last_name{$paramIndex}"] = $row['lastname'];
            $params[":first_name{$paramIndex}"] = $row['firstname'];
            $params[":email_address{$paramIndex}"] = $row['email'];
            $paramIndex++;
        }
        // Remove the trailing comma from the SQL query
        $query = substr($query, 0, -2);
        $stid = oci_parse($conn, $query);
        
        // Bind parameters for each row of data
        foreach ($params as $key => $value) {
            oci_bind_by_name($stid, $key, $params[$key]);
        }

        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        if ($r !== false) {
            $rowCount = oci_num_rows($stmt);
            return true;
        } else {
            return false;
        }
        
        // Return the enquiry_id
        return $rowCount;
        
    }
}


if ( ! function_exists( 'getCountryId' ) ) {
    function getCountryId($conn) { 
        $query = "select country_id, country_name from country order by country_name";
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $countries = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $temp = [
                "COUNTRY_ID" => $row["COUNTRY_ID"] ?? null,
                "COUNTRY_NAME" => $row["COUNTRY_NAME"] ?? null,
            ];
            $countries[] = $temp;
        }
        return $countries;
    }
}

if ( ! function_exists( 'fetchRatesOfSingleVilla' ) ) {
    function fetchRatesOfSingleVilla($conn, $villa_id, $date_from, $date_to, $bedrooms) { 
        
        $query = "select villa_functions.calculate_price(".vg_int($villa_id).", to_date('".$date_from."', 'dd/mm/yyyy'), to_date('".$date_to."', 'dd/mm/yyyy'), ".vg_int($bedrooms).") as price from dual";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $rates = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $rates = $row['PRICE'];
        }
        return $rates;
    } 
}

if ( ! function_exists( 'fetchMetaDataForVilla' ) ) {
    function fetchMetaDataForVilla($conn, $villa_id) {
        $query = "select 'Villa '||vg_number||' in '||location_name||', '||destination_name||' | Villa Getaways - Luxury Villa Rentals Worldwide. Vacation Villas and Holiday Rentals in '||destination_name as villa_description ";
        // $query .= " , <the url part>||image  as image ";
        $query .= " , 'Villa '||vg_number||' in '||location_name||', '||destination_name||' | VillaGetaways.com ' as page_title ";
        $query .= " , 'false' as block ";
        $query .= " , 'Villa Getaways Ltd' as copyright ";
        $query .= " , 'sales@villagetaways.com' as email ";
        $query .= " , 'VillaGetaways.com' as author ";
        $query .= " , 'en' as language ";
        $query .= " , 'true' as mssmarttagspreventparsing ";
        $query .= " , head_title as title ";
        $query .= " from villa_location_vw ";
        $query .= " where villa_id = " . vg_int($villa_id);
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $meta_data = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $meta_data["DESCRIPTION"] = $row["VILLA_DESCRIPTION"];
            $meta_data["BLOCK"] = $row["BLOCK"];
            $meta_data["COPYRIGHT"] = $row["COPYRIGHT"];
            $meta_data["EMAIL"] = $row["EMAIL"];
            $meta_data["AUTHOR"] = $row["AUTHOR"];
            $meta_data["LANGUAGE"] = $row["LANGUAGE"];
            $meta_data["MSSMARTTAGSPREVENTPARSING"] = $row["MSSMARTTAGSPREVENTPARSING"];
            $meta_data["TITLE"] = $row["TITLE"];
            $meta_data["PAGE_TITLE"] = $row["PAGE_TITLE"];
            // $meta_data["TITLE"] = $row["TITLE"];
        }
        return $meta_data;
    }
}

if ( ! function_exists( 'fetchMetaDataForDestination' ) ) {
    function fetchMetaDataForDestination($conn, $destination_id) {
        // $query = "select nvl(meta_description, head_title) as description ";
        // $query .= " , 'false' as block ";
        // $query .= " , 'Villa Getaways Ltd' as copyright ";
        // $query .= " , 'sales@villagetaways.com' as email ";
        // $query .= " , 'VillaGetaways.com' as author ";
        // $query .= " , 'en' as language  ";
        // $query .= " , 'true' as mssmarttagspreventparsing ";
        // $query .= " , head_title as title ";
        // $query .= " from destination ";
        // $query .= " where destination_id = " . vg_int($destination_id);
        
        $query = "select nvl(meta_description, head_title) as description ";
        $query .= " , 'false' as block ";
        $query .= " , 'Villa Getaways Ltd' as copyright ";
        $query .= " , 'sales@villagetaways.com' as email ";
        $query .= " , 'http://VillaGetaways.com ' as author ";
        $query .= " , 'en' as language  ";
        $query .= " , 'true' as mssmarttagspreventparsing ";
        $query .= " , head_title as title ";
        $query .= " from destination ";
        $query .= " where destination_id = " . vg_int($destination_id);
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $meta_data = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $meta_data["DESCRIPTION"] = $row["DESCRIPTION"];
            $meta_data["BLOCK"] = $row["BLOCK"];
            $meta_data["COPYRIGHT"] = $row["COPYRIGHT"];
            $meta_data["EMAIL"] = $row["EMAIL"];
            $meta_data["AUTHOR"] = $row["AUTHOR"];
            $meta_data["LANGUAGE"] = $row["LANGUAGE"];
            $meta_data["MSSMARTTAGSPREVENTPARSING"] = $row["MSSMARTTAGSPREVENTPARSING"];
            $meta_data["TITLE"] = $row["TITLE"];
        }
        return $meta_data;
    }
}

if ( ! function_exists( 'fetchMetaDataForLocation' ) ) {
    function fetchMetaDataForLocation($conn, $destination_id, $location_id) {
        
        $query = "select head_title as title,meta_description as description ";
        $query .= " from location ";
        $query .= " where location_id = " . vg_int($location_id);
        $query .= " and destination_id = " . vg_int($destination_id);
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $meta_data = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $meta_data["DESCRIPTION"] = $row["DESCRIPTION"];
            $meta_data["TITLE"] = $row["TITLE"];
        }
        return $meta_data;
    }
}

if ( ! function_exists( 'fetchMetaDataForRegion' ) ) {
    function fetchMetaDataForRegion($conn, $destination_id, $location_id, $region_id) {
        
        $query = "select head_title as title,meta_description as description ";
        $query .= " from region ";
        $query .= " where region_id = " . vg_int($region_id);
        $query .= " and location_id = " . vg_int($location_id);
        $query .= " and destination_id = " . vg_int($destination_id);
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $meta_data = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $meta_data["DESCRIPTION"] = $row["DESCRIPTION"];
            $meta_data["TITLE"] = $row["TITLE"];
        }
        return $meta_data;
    }
}

if ( ! function_exists( 'fetchMetaDataByRegion' ) ) {
    function fetchMetaDataByRegion($conn,$region_id) {
        
        $query = "select head_title as title,meta_description as description ";
        $query .= " from region ";
        $query .= " where region_id = " . vg_int($region_id);
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            echo $e['message']; die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $meta_data = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $meta_data["DESCRIPTION"] = $row["DESCRIPTION"];
            $meta_data["TITLE"] = $row["TITLE"];
        }
        return $meta_data;
    }
}


if ( ! function_exists( 'fetchVillaFloorPlan' ) ) {
    function fetchVillaFloorPlan($conn, $villa_id) {
        $query = "select full_img from image  where villa_id = " . vg_int($villa_id) . "  and floorplan = 1 ";
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villa_floor_plans = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $villa_floor_plans[] = $row["FULL_IMG"];
        }
        return $villa_floor_plans;
    }
}

if ( ! function_exists( 'fetchVillaVideos' ) ) {
    function fetchVillaVideos($conn, $villa_id) {
        $query = "select http_code from video where villa_id = " . vg_int($villa_id);
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $villa_videoes = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $villa_videoes[] = $row["HTTP_CODE"];
        }
        return $villa_videoes;
    }
}

if( ! function_exists( 'fetchExternalBookUrl' ) ) {
    function fetchExternalBookUrl($conn) {
        $query = "select name_value from app_settings where name='BOOKURL' and environment = (select name_value from app_settings where name='SITE')";
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $external_book_url = "";
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $external_book_url = $row["NAME_VALUE"];
        }
        return $external_book_url;
    }
}

if( ! function_exists( 'fetchNumberOfBedrooms' ) ) {
    function fetchNumberOfBedrooms($conn, $villa_id) {
        $query = "select distinct villa_id, ";
        $query .= "case ";
        $query .= "when nr_of_rooms is null then (select beds from villa v where v.villa_id= p.villa_id) ";
        $query .= "else nr_of_rooms ";
        $query .= "end nr_of_rooms ";
        $query .= "from villa_price p ";
        $query .= "where villa_id = " . vg_int($villa_id);
        $query .= " order by nr_of_rooms";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $number_of_bedrooms = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $number_of_bedrooms[] = $row["NR_OF_ROOMS"];
        }
        return $number_of_bedrooms;
    }
}

if( ! function_exists( 'fetchAllVillas' ) ) {
    function fetchAllVillas($conn) {
        $query = "select location_name,  vg_number , destination_name, location_url_name ";
        $query .= "from villa_location_vw";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villas = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $villas[] = $row;
        }
        return $villas;
    }
}

if ( ! function_exists( 'insertEnquireData' ) ) {
    function insertEnquireData($conn, $clientId, $agentId, $villa_id, $vg_number, $date_from, $date_to, $flexible, $adults, $kids, $budget, $questions) { 
        $query = "INSERT INTO ENQUIRY ( CLIENT_ID ,AGENT_ID ,VILLA_ID ,VILLA_NUMBER ,ARRIVE_AT ,DEPART_AT  ";
        $query.= " , DATES_FLEXIBLE ,NUM_PEOPLE ,NUM_CHILDREN ,NIGHTLY_BUDGET ,COMMENTS ,ENQUIRY_STATUS_ID ) ";
        $query.= " values ( :S_CLIENT_ID ,:S_AGENT_ID ,:S_VILLA_ID ,:S_VG_NUMBER  ,TO_DATE(:S_ARRIVE_AT, 'DD/MM/YYYY'), TO_DATE(:S_DEPART_AT, 'DD/MM/YYYY'), :S_FEXIBLE_DATE, :S_NUM_PEOPLE ";
        $query.= " , :S_NUM_CHILDREN , :S_NIGHTLY_BUDGET, :S_COMMENTS, 2 ) ";
        $query.= " RETURNING enquiry_id INTO :enquiry_id_v";

        //Prepare the statement
        $stid = oci_parse($conn, $query);
        
        oci_bind_by_name($stid, ":S_CLIENT_ID", $clientId, 10);
        oci_bind_by_name($stid, ":S_AGENT_ID", $agentId, 60);
        oci_bind_by_name($stid, ":S_VILLA_ID", $villa_id, 60);
        oci_bind_by_name($stid, ":S_VG_NUMBER", $vg_number, 10);
        oci_bind_by_name($stid, ":S_ARRIVE_AT", $date_from, 60);
        oci_bind_by_name($stid, ":S_DEPART_AT", $date_to, 60);
        oci_bind_by_name($stid, ":S_FEXIBLE_DATE", $flexible, 1);
        oci_bind_by_name($stid, ":S_NUM_PEOPLE", $adults, 10);
        oci_bind_by_name($stid, ":S_NUM_CHILDREN", $kids, 10);
        oci_bind_by_name($stid, ":S_NIGHTLY_BUDGET", $budget, 60);
        oci_bind_by_name($stid, ":S_COMMENTS", $questions, 240);
        
        $enquiry_id_v = null;
        oci_bind_by_name($stid, ":enquiry_id_v", $enquiry_id_v, 15);

        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Return the enquiry_id
        return $enquiry_id_v;
        
    }
}

if( ! function_exists( 'recommend' ) ) {
    function recommend($conn, $firstname, $lastname, $email_1, $phone_mobile,$destination_id, $location_id, $comments, $adults, $children, $dates_flexible) {   
            
            $recommend = "1";
            
            $tdin = oci_parse($conn, 'begin :retval := villa_functions.enquiry_form(
            p_firstname => :p_firstname, p_lastname => :p_lastname,
            p_email_1 => :p_email_1,  p_phone_mobile => :p_phone_mobile,  
            p_destination_id => :p_destination_id, p_location_id => :p_location_id,
            p_num_people => :p_adults, p_num_children => :p_children,
            p_dates_flexible => :p_dates_flexible,
            p_comments =>  :p_comments,
            p_recommend => :p_recommend); end;');
            
            oci_bind_by_name($tdin, ':p_firstname', $firstname, 60);
            oci_bind_by_name($tdin, ':p_lastname', $lastname, 60);
            oci_bind_by_name($tdin, ':p_email_1', $email_1, 120);
            oci_bind_by_name($tdin, ':p_phone_mobile', $phone_mobile, 30);
            oci_bind_by_name($tdin, ':p_destination_id', $destination_id, 10);
            oci_bind_by_name($tdin, ':p_location_id', $location_id, 10);
            oci_bind_by_name($tdin, ':p_dates_flexible', $dates_flexible, 1);
            oci_bind_by_name($tdin, ':p_adults', $adults, 2);
            oci_bind_by_name($tdin, ':p_children', $children, 2);    
            oci_bind_by_name($tdin, ':p_comments', $comments, 3000);
            oci_bind_by_name($tdin, ':p_recommend', $recommend, 1);
            
            $retval = null;
            oci_bind_by_name($tdin,':retval' ,$retval, 240);
            
            if (!$tdin) {
                $e = oci_error($conn);
                echo $e['message']; die;
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            
            // Perform the logic of the query
            $r = oci_execute($tdin);
            if (!$r) {
                $e = oci_error($tdin);
                echo $e['message']; die;
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }
            
            return $retval;
    }
}

if( ! function_exists( 'fetchNumberOfRooms' ) ) {
    function fetchNumberOfRooms($conn, $villa_id) {
      //  $query = "select distinct nr_of_rooms from villa_price where villa_id = " . vg_int($villa_id) . " and nr_of_rooms is not null";
        
        
        $query="select distinct nr_of_rooms from villa_price where villa_id = " . vg_int($villa_id) . " and nr_of_rooms is not null
union
select beds from villa where villa_id = " . vg_int($villa_id);
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $number_of_bedrooms = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $number_of_bedrooms[] = $row["NR_OF_ROOMS"];
        }
        return $number_of_bedrooms;
    }
}

if( ! function_exists( 'fetchStandardTerms' ) ) {
    function fetchStandardTerms($conn) {
        $query = "select text from template where name = 'Standard Terms NZ'";
        
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $standard_terms_text = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $standard_terms_text["TEXT"] = $row["TEXT"];
        }
        return $standard_terms_text;
    }
}

if( ! function_exists( 'fetchVillaInclusions' ) ) {
    function fetchVillaInclusions($conn, $villa_id) {
        $query = "select '<ul>'||
			 decode(t_absolute_beachfront,1,'<li>Absolute Beach Front</li>',null)||
			 decode(oceanfront,1,'<li>Ocean Front</li>',null)||
             decode(oceanview,1,'<li>Ocean View </li>',null)||
			 decode(pool,1,'<li>Pool</li>',null)||
			 decode(ac,1,'<li>Airconditioning</li>',null)||
			 decode(maid,1,'<li>Housekeeping Service </li>',null)||
			 decode(chef,1,'<li>Private chef service for lunch or dinner </li>',null)||
			 decode(broadband,1,'<li>Internet and WiFi </li>',null)||
			 decode(tennis,1,'<li>Tennis Court </li>',null)||
			 decode(daily_breakfast,1,'<li>Daily Continental breakfasts with eggs/bacon for hot breakfast items</li>',null)||
			 decode(car_and_driver,1,'<li>Airport transfers, one round trip</li>',null)||
			'</ul>' AS INCLUSIONS
        from villa 
    	where villa_id = " . vg_int($villa_id);
	    
	    // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            var_dump($e['message']);
            die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            var_dump($e['message']);
            die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villa_inclusions = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $villa_inclusions["INCLUSIONS"] = $row["INCLUSIONS"];
        }
        return $villa_inclusions;
    }
}

if( ! function_exists( 'fetchVillaPricing' ) ) {
    function fetchVillaPricing($conn, $villa_id, $nightly_rate, $nr_of_nights) {
        $query = "select round( ( (v.tax_percentage * $nightly_rate) / 100),2) * $nr_of_nights as tax, 
            (round(( (v.tax_percentage * $nightly_rate) / 100),2) + $nightly_rate ) * $nr_of_nights as total, 
            v.security_deposit, 
            c.currency_prefix,
            v.currency_code, 
            v.tax_percentage
        from villa v, currency c
	    where c.currency_code = v.currency_code
	    and villa_id = " . vg_int($villa_id);
	    
	    // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            // var_dump($e['message']);
            // die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            // var_dump($e['message']);
            // die;
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        $villa_pricing = [];
        // Fetch the results of the query
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $villa_pricing["TAX"] = $row["TAX"];
            $villa_pricing["TOTAL"] = $row["TOTAL"];
            $villa_pricing["SECURITY_DEPOSITE"] = $row["SECURITY_DEPOSITE"];
            $villa_pricing["CURRENCY_PREFIX"] = $row["CURRENCY_PREFIX"];
            $villa_pricing["CURRENCY_CODE"] = $row["CURRENCY_CODE"];
            $villa_pricing["TAX_PERCENTAGE"] = $row["TAX_PERCENTAGE"];
        }
        return $villa_pricing;
    }
}

if( ! function_exists( 'callBookNowPricing' ) ) {
    function callBookNowPricing($conn, $villa_id, $from, $to, $bedrooms) {
        $query = 'BEGIN VILLA_FUNCTIONS.BOOKNOW_PRICING(:p_from, :p_to, :p_villa_id, :p_bedrooms, :o_night_rate, :o_tax_percent, :o_tax, :o_total, :o_deposit, :o_security_bond, :o_pay_full, :o_currency); END;';
            
	    // Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        
        // Bind the input parameter
        oci_bind_by_name($stid, ':p_from', $from);
        oci_bind_by_name($stid, ':p_to', $to);
        oci_bind_by_name($stid, ':p_villa_id', $villa_id);
        oci_bind_by_name($stid, ':p_bedrooms', $bedrooms);
        
        $o_night_rate = 0;
        $o_tax_percent =0;
        $o_tax = 0;
        $o_total = 0;
        $o_deposit = 0;
        $o_pay_full = 0;
        $o_security_bond   = 0;
        $o_currency = "";
        
        
        
        
        
        // Bind the output parameter
        oci_bind_by_name($stid, ':o_night_rate', $o_night_rate, 32);
        oci_bind_by_name($stid, ':o_tax_percent', $o_tax_percent, 32);
        oci_bind_by_name($stid, ':o_tax', $o_tax, 32);
        oci_bind_by_name($stid, ':o_total', $o_total, 32);
        oci_bind_by_name($stid, ':o_deposit', $o_deposit, 32);
        oci_bind_by_name($stid, ':o_security_bond', $o_pay_full, 32);
        oci_bind_by_name($stid, ':o_pay_full', $o_security_bond, 32);
        oci_bind_by_name($stid, ':o_currency', $o_currency, 32);
        
        // Perform the logic of the query
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        // Bind the output parameter
        $villa_pricing = [];
        $villa_pricing["o_night_rate"] = $o_night_rate;
        $villa_pricing["o_tax"] = $o_tax;
        $villa_pricing["o_tax_percent"] = $o_tax_percent;
        $villa_pricing["o_total"] = $o_total;
        $villa_pricing["o_deposit"] = $o_deposit;
        $villa_pricing["o_security_bond"] = $o_pay_full;
        $villa_pricing["o_pay_full"] = $o_security_bond;
        $villa_pricing["o_currency"] = $o_currency;
        
        // Free the statement resource
        oci_free_statement($stmt);
        
        return $villa_pricing;
    }
}

if (!function_exists('callCreateClient')) {
    function callCreateClient($conn, $p_firstname, $p_lastname, $p_email_1, $p_phone_mobile, $p_address, $p_suburb, $p_state, $p_postcode, $p_country_id, $p_villa_id) {
         
         $p_title = null;
        
        // Define the query with a return parameter for the client ID
        $query = 'BEGIN :r_client_id := VILLA_FUNCTIONS.PROCESS_CLIENT_DETAILS(:p_title, :p_firstname, :p_lastname, :p_email_1, :p_phone_mobile, :p_address, :p_suburb, :p_state, :p_postcode, :p_country_id, :p_villa_id); END;';

        // Prepare the statement
        $stid = oci_parse($conn, $query);
        // Check if the statement preparation was successful
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error("OCI Parse Error: " . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            return false;
        }

        // Define the return parameter
        $r_client_id = 0;

        // Bind the input parameters
        oci_bind_by_name($stid, ':p_title', $p_title);
        oci_bind_by_name($stid, ':p_firstname', $p_firstname);
        oci_bind_by_name($stid, ':p_lastname', $p_lastname);
        oci_bind_by_name($stid, ':p_email_1', $p_email_1);
        oci_bind_by_name($stid, ':p_phone_mobile', $p_phone_mobile);
        oci_bind_by_name($stid, ':p_address', $p_address);
        oci_bind_by_name($stid, ':p_suburb', $p_suburb);
        oci_bind_by_name($stid, ':p_state', $p_state);
        oci_bind_by_name($stid, ':p_postcode', $p_postcode);
        oci_bind_by_name($stid, ':p_country_id', $p_country_id);
        oci_bind_by_name($stid, ':p_villa_id', $p_villa_id);

        // Bind the output parameter
        oci_bind_by_name($stid, ':r_client_id', $r_client_id, 32);

        // Execute the query
        $result = oci_execute($stid);

        // Check if the query execution was successful
        if (!$result) {
            $e = oci_error($stid);
           
            trigger_error("OCI Execute Error: " . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            oci_free_statement($stid);
            $r_client_id = 0;
        }

        // Free the statement resource
        oci_free_statement($stid);

        // Return the result, including the client ID
        return $r_client_id;
    }
}

if (!function_exists('callCreateBooking')) {
    function callCreateBooking($conn, $p_villa_id, $p_client_id, $p_arrive, $p_depart, $p_num_nights, $p_num_people, $p_num_children, $p_bedrooms, $p_price_night, $p_payment_method, $p_full_payment) {
        
        // Define the query with a return parameter for the client ID
         $query = 'BEGIN VILLA_FUNCTIONS.CREATE_BOOKING(:p_villa_id, :p_client_id, :p_arrive, :p_depart, :p_num_nights, :p_num_people, :p_num_children, :p_bedrooms, :p_price_night, :p_payment_method, :p_full_payment, :o_booking_id); END;';

        // Prepare the statement
        $stid = oci_parse($conn, $query);

        // Check if the statement preparation was successful
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error("OCI Parse Error: " . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            return false;
        }

        // Define the return parameter 
        $o_booking_id = 0;

        // Bind the input parameters
        oci_bind_by_name($stid, ':p_villa_id', $p_villa_id);
        oci_bind_by_name($stid, ':p_client_id', $p_client_id);
        oci_bind_by_name($stid, ':p_arrive', $p_arrive);
        oci_bind_by_name($stid, ':p_depart', $p_depart);
        oci_bind_by_name($stid, ':p_num_nights', $p_num_nights);
        oci_bind_by_name($stid, ':p_num_people', $p_num_people);
        oci_bind_by_name($stid, ':p_num_children', $p_num_children);
        oci_bind_by_name($stid, ':p_bedrooms', $p_bedrooms);
        oci_bind_by_name($stid, ':p_price_night', $p_price_night);
        oci_bind_by_name($stid, ':p_payment_method', $p_payment_method);
        oci_bind_by_name($stid, ':p_full_payment', $p_full_payment);

        // // Bind the output parameter
        // oci_bind_by_name($stid, ':o_booking_id', $o_booking_id);
         oci_bind_by_name($stid, ':o_booking_id', $o_booking_id,32);

        // Execute the query
        $result = oci_execute($stid);

        // Check if the query execution was successful
        if (!$result) {
            $e = oci_error($stid);
            trigger_error("OCI Execute Error: " . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            oci_free_statement($stid);
            return 0;
        }

        // Free the statement resource
        oci_free_statement($stid);

        // Return the result, including the client ID
        return $o_booking_id;
    }
}

if (!function_exists('callRecordPayment')) {
    function callRecordPayment($conn,$p_booking_id,$p_payment_method_id,$p_receipt_tx,$p_currency_code,$p_amount_charged,$p_pin_payment_fee,$p_security_deposit,$p_description,$p_payment_type_id) {
        
        // Define the query with a return parameter for the client ID
        $query = 'BEGIN :return_value := VILLA_FUNCTIONS.RECORD_PAYMENT(:p_booking_id,:p_payment_method_id,:p_receipt_tx,:p_currency_code,:p_amount_charged,:p_pin_payment_fee,:p_security_deposit,:p_description,:p_payment_type_id); END;';

      //  $query = 'BEGIN :return_value := villa_functions.record_payment(....); END;';

        // Prepare the statement
        $stid = oci_parse($conn, $query);

        // Check if the statement preparation was successful
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error("OCI Parse Error: " . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            return false;
        }

        // Bind the input parameters
        oci_bind_by_name($stid, ':p_booking_id', $p_booking_id);
        oci_bind_by_name($stid, ':p_payment_method_id', $p_payment_method_id);
        oci_bind_by_name($stid, ':p_receipt_tx', $p_receipt_tx);
        oci_bind_by_name($stid, ':p_currency_code', $p_currency_code);
        oci_bind_by_name($stid, ':p_amount_charged', $p_amount_charged);
        oci_bind_by_name($stid, ':p_pin_payment_fee', $p_pin_payment_fee);
        oci_bind_by_name($stid, ':p_security_deposit', $p_security_deposit);
        oci_bind_by_name($stid, ':p_description', $p_description);
        oci_bind_by_name($stid, ':p_payment_type_id', $p_payment_type_id);
      
        // Bind the output parameter
        oci_bind_by_name($stid, ':return_value', $return_value, 32);

        // Execute the query
        oci_execute($stid);
       
        // Free the statement resource
        oci_free_statement($stid);

        // Return the result, including the client ID
        return $return_value;
    }
}


if (!function_exists('callPinPayment')) {
    function callPinPayment($pin_token,$amount,$description,$currency,$email) {

    $api_key = '42uZdnVGfbkwy0Iy-wFyzQ';

    $data = array(
        'amount' => $amount,
        'currency' => $currency,
        'description' => $description,
        'email' => $email,
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'card_token' => $pin_token,
    );

        // Initialize cURL
        $ch = curl_init('https://test-api.pinpayments.com/1/charges');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        // Execute the request
        $response = curl_exec($ch);
        $response_data = json_decode($response, true);

        return $response_data;
    }
} 

// Check Villa availability
if ( ! function_exists( 'is_villa_available' ) ) {
    function is_villa_available($conn,$villaId,$from,$to) {
       
         
        // Define the query with a return parameter for the client ID
        $query = 'BEGIN :return_value := VILLA_FUNCTIONS.IS_VILLA_AVAILABLE(:p_villa_id,:p_from,:p_to); END;';

   
        // Prepare the statement
        $stid = oci_parse($conn, $query);
        
     

        // Check if the statement preparation was successful
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error("OCI Parse Error: " . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            return false;
        }


   
        // Bind the input parameters
        oci_bind_by_name($stid, ':p_villa_id', $villaId);
        oci_bind_by_name($stid, ':p_from', $from);
        oci_bind_by_name($stid, ':p_to', $to);
      
        // Bind the output parameter
        oci_bind_by_name($stid, ':return_value', $return_value, 32);

          // Execute the query
        $result = oci_execute($stid);

        // Check if the query execution was successful
        if (!$result) {
            $e = oci_error($stid);
            trigger_error("OCI Execute Error: " . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            oci_free_statement($stid);
            return 0;
        }
       
        // Free the statement resource
        oci_free_statement($stid);

        // Return the result, including the client ID
        return $return_value;
       
    }
}
// Fetch Booked dates
if ( ! function_exists( 'fetchVillaBookedDates' ) ) {
    function fetchVillaBookedDates($conn, $villa_id) {

        // Prepare the query with a placeholder for the villa_id
         $query = "select b2.arrival, case when arrival = b.depart then 'Y' else 'N' end checkout, late_checkout,
       (select 'Y' from villa_calendar_vw where villa_id=:villa_id and arrive=b2.arrival fetch first row only) arriving
    from villa_calendar_vw b, lateral
        (select (b.arrive + level -1) arrival
           from dual
         connect by level <= (b.depart - b.arrive) + 1) b2
   where villa_id = :villa_id
     and ( trunc(sysdate) between b.arrive and b.depart
           or b.arrive > trunc(sysdate)
        )
  order by arrival";

        //// Prepare the statement
        $stid = oci_parse($conn, $query);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            return false;
        }

        // Bind the villa_id parameter to the query
        oci_bind_by_name($stid, ':villa_id', $villa_id);

        // Execute the statement
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            return false;
        }

        // Fetch all rows
        $result = [];
        while ($row = oci_fetch_array
        
        ($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
           
            $result[] = $row;
        }

        // Free the statement resource
        oci_free_statement($stid);

        // Return the fetched rows
        return $result;
    }
}



if (!function_exists('fetchbookingDetails')) {
    function fetchbookingDetails($conn, $ref) {
        // Prepare the SQL with a bind variable
        $sql = "SELECT b.booking_id, b.firstname, b.lastname, b.num_people, b.num_children, 
                       b.address, b.suburb, b.state, b.country, b.postcode, b.phone_mobile, 
                       b.client_email, b.currency_code, p.amount , b.currency_code, b.phone_mobile, b.phone_home,
                       c.country_iso, c.country_iso3, c.country_numcode
                FROM payment_link p, booking_vw b, country c
                WHERE b.booking_id = p.booking_id 
                AND upper(c.country_name) = upper(b.country)
                AND p.payment_link_id = :ref
FETCH FIRST ROW ONLY";

        // Parse the statement
        $stid = oci_parse($conn, $sql);
        if (!$stid) {
            $e = oci_error($conn);
            trigger_error('SQL Parse Error: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            return [];
        }

        // Bind the parameter
        oci_bind_by_name($stid, ":ref", $ref);

        // Execute the statement
        $r = oci_execute($stid);
        if (!$r) {
            $e = oci_error($stid);
            trigger_error('SQL Execution Error: ' . htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            return [];
        }

        // Fetch and return results
        $results = [];
        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $results[] = $row;
        }

        return $results;
    }
}

if (isset($conn) && is_resource($conn)) {
    oci_close($conn);
}
?>
