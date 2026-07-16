<?php
$conn = oracleDbConnection();
$villa_id = $args["villa_details"]["VILLA_ID"] ?? "";
if( empty($villa_id) ) { return; }
$villa_rates_headers = fetchVillaRatesHeaders($conn, $villa_id);

if( $villa_rates_headers ) {
    $i=1;
    foreach($villa_rates_headers AS $villa_rates_header) {
        $villa_rates_data = fetchVillaRatesData($conn, $villa_id, $villa_rates_header["NR_OF_ROOMS"]);
        
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
                                        <td width="100">
                                            <a href="<?php echo home_url(); ?>/inquiry?villa_number=<?php echo $args["villa_details"]['VG_NUMBER'] ?? ""; ?>" class="btn btn-primary">INQUIRE</a>
                                            <!--<a href="javascript:apex.navigation.dialog('\u002Fords\u002Fvgweb\u002Fr\u002Fvg\u002Finquire?p610_villa_id=344\u0026p610_villa_name=Ylang\u002520Ylang\u0026p610_rooms=4\u0026dialogCs=kGzf8KTDgZkX6D-9BToECF59v_SD2WvhFNjWGrHgv_DeLT9_7Oy99qPrXXh86KXWLLTU2fFzI8ES4m__NJ7M_g',{title:'Inquire',height:'650',width:'920',maxWidth:'960',modal:true,dialog:null,title: $v('P610_VILLA_NAME')},'t-Dialog-page--standard '+'enquire-modal',apex.jQuery('#dest_rates'));" class="btn btn-primary">INQUIRE</a>-->
                                        </td>
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