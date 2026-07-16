<?php 
$conn = oracleDbConnection();
$villa_rates_headers = fetchVillaRatesHeaders($conn, ($args["villa_details"]["VILLA_ID"] ?? ""));

if( $villa_rates_headers ) {
    $i=1;
    foreach($villa_rates_headers AS $villa_rates_header) {
        $villa_rates_data = fetchVillaRatesData($conn, ($args["villa_details"]["VILLA_ID"] ?? ""), $villa_rates_header["NR_OF_ROOMS"]);
        ?>
        <div class="col-lg-12">
            <div class="rates">
                <a class="rates-header" data-toggle="collapse" href="#rooms_<?php echo $i; ?>" role="button" aria-expanded="true" aria-controls="rates">
                    <?php echo $villa_rates_header["NR_OF_ROOMS"]; ?> Bedrooms | USD $ <?php echo $villa_rates_header["MIN_PRICE"]; ?> per night - USD $ <?php echo $villa_rates_header["MAX_PRICE"]; ?> per night <?php echo $villa_rates_header["TAX_PERCENTAGE"]; ?>
                </a>
                <div id="rooms_<?php echo $i; ?>" class="collapse <?php echo $i==1?"show": "";?>" style="">
                    <div class="rates-table-wrapper">
                        <table class="rates-table">
                            <tbody>
                                <?php 
                                if( $villa_rates_data ) {
                                    foreach($villa_rates_data AS $villa_rate) {
                                      ?>
                                    <tr>
                                        <td><?php echo $villa_rate["PRICE_NAME"]; ?></td>
                                        <td><?php echo $villa_rate["PERIOD"]; ?></td>
                                        <td><?php echo $villa_rate["PRICE"]; ?></td>
                                        <td><?php echo $villa_rate["MIN_NR_DAYS"]; ?></td>
                                      
                                    </tr>
                                    <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
    $i++;
    }
}
?>