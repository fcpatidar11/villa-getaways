$(document).ready(function() {
    
    $(".header-form select.location").selectpicker();
    $(".header-form select.region").selectpicker();
    
    $('#destination_footer').on('change', function() {
        var destinationId = $(this).val();
        // Toggle the visibility of the location select field
        console.log(destinationId, 'destinationId')
        $('.loader').show();
        if(destinationId) {
            // Perform AJAX request to fetch locations
            $.ajax({
                url: ajax_url.ajaxurl,
                type: 'POST',
                data: {action: "destination_location", type : "destination", destination_id : $(this).val()},
                success: function(response) {
                    $('.loader').hide();
                    if (destinationId === '') {
                        $('#location_footer').hide(); // Hide the location select field
                    } else {
                        $('#location_footer').removeClass("hide");
                        $('#location_footer').addClass("show"); // Show the location select field
                    }
                    // Update the location dropdown with fetched data
                    var response = JSON.parse(response);
                    $('#location_footer').html(response.html);
                },
                error: function (error) {
                    $('.loader').hide();
                }
            });    
        }
    });
    
    $('#recommend-destination').on('change', function() {
        var destinationId = $(this).val();
        // Toggle the visibility of the location select field
        console.log(destinationId, 'destinationId')
        $('.loader').show();
        if(destinationId) {
            // Perform AJAX request to fetch locations
            $.ajax({
                url: ajax_url.ajaxurl,
                type: 'POST',
                data: {action: "destination_location", type : "destination", destination_id : $(this).val()},
                success: function(response) {
                    $('.loader').hide();
                    // if (destinationId === '') {
                    //     $('#recommend-location').hide(); // Hide the location select field
                    // } else {
                    //     $('#recommend-location').removeClass("hide");
                    //     $('#recommend-location').addClass("show"); // Show the location select field
                    // }
                    // Update the location dropdown with fetched data
                    var response = JSON.parse(response);
                    $('#recommend-location').html(response.html);
                },
                error: function (error) {
                    $('.loader').hide();
                }
            });    
        }else {
            $('#recommend-location').html('<option value="">Select Option</option>');
        }
    });
    
    $(".header-form select.destination").on("selectmenuselect", function( event, ui ) {
        $(".regions-select-id").css('display', 'block');
        $(".header-form select.region").selectpicker();
        $(".header-form select.region").html('');
        $('.header-form select.region').selectpicker('refresh');
        if( $(this).val() ) {
            $.ajax({
                type : "POST",
                url : ajax_url.ajaxurl,
                data : {action: "destination_location", type : "destination", destination_id : $(this).val()},
                success: function(data) {
                    // $(".header-form select.location").html(data);
                    // $('.header-form select.location').selectpicker('refresh');
                    var response = JSON.parse(data); // Parse the JSON response
                    $(".header-form select.location").html(response.html);
                    $('.header-form select.location').selectpicker('refresh');
                    
                    var noRegion = response.no_region; // Get the true/false value
                    if(noRegion) {
                        $(".regions-select-id").css('display', 'none');
                    }
                },
                error:function(error){
                    console.log('message Error' + JSON.stringify(error));
                }                    
            });
        }
    });
    
    $('.header-form select.location').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        var selected = $(e.currentTarget).val();
        $.ajax({
            type : "POST",
            url : ajax_url.ajaxurl,
            data : {action: "destination_location", type : "location", location_id : selected},
            success: function(data) {
                // $(".header-form select.region").html(data);
                // $('.header-form select.region').selectpicker('refresh');
                var response = JSON.parse(data); // Parse the JSON response
                $(".header-form select.region").html(response.html);
                $('.header-form select.region').selectpicker('refresh');
            },
            error:function(error){
                console.log('message Error' + JSON.stringify(error));
            }                    
        });
    });
    
    $(".home-form select.location").selectpicker();
    $(".home-form select.region").selectpicker();

    $(".home-form select.destination").on("selectmenuselect", function( event, ui ) {
        // $(".home-form select.region").html(data);
        // $('.home-form select.region').selectpicker('refresh');
        $(".regions-select-id").css('display', 'block');
        $(".home-form select.region").selectpicker();
        $(".home-form select.region").html('');
        $('.home-form select.region').selectpicker('refresh');

        if($(this).val()) {
            $.ajax({
                type : "POST",
                url : ajax_url.ajaxurl,
                data : {action: "destination_location", type : "destination", destination_id : $(this).val()},
                success: function(data) {
                    // $(".home-form select.location").html(data);
                    // $('.home-form select.location').selectpicker('refresh');
                    var response = JSON.parse(data); // Parse the JSON response
                    $(".home-form select.location").html(response.html);
                    $('.home-form select.location').selectpicker('refresh');
                    var noRegion = response.no_region; // Get the true/false value
                    if(noRegion) {
                        $(".regions-select-id").css('display', 'none');
                    }
                },
                error:function(error){
                    console.log('message Error' + JSON.stringify(error));
                }                    
            });
        }
    });
    
    $('.home-form select.location').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        var selected = $(e.currentTarget).val();
        $(".regions-select-id").css('display', 'block');
        $.ajax({
            type : "POST",
            url : ajax_url.ajaxurl,
            data : {action: "destination_location", type : "location", location_id : selected},
            success: function(data) {
                var response = JSON.parse(data);
                if( response && response.html ) {
                    $(".header-form select.region").selectpicker();
                    $(".regions-select-id").css('display', 'block');
                    // $(".home-form select.region").html(data);
                    // $('.home-form select.region').selectpicker('refresh');
                    
                    // Parse the JSON response
                    $(".home-form select.region").html(response.html);
                    $('.home-form select.region').selectpicker('refresh');
                } else {
                    $(".regions-select-id").css('display', 'none');
                }
            },
            error:function(error){
                console.log('message Error' + JSON.stringify(error));
            }                    
        });
    });
    
    $("form#filter-by-bedrooms select#filterBarRooms, form#filter-by-bedrooms select#filterPriceRecommended").on("selectmenuselect", function( event, ui ) {
        $("form#filter-by-bedrooms").submit();
    });
    
    // $("form#filter-by-price select#filterPriceRecommended").on("selectmenuselect", function( event, ui ) {
    //     $("form#filter-by-price").submit();
    // });
    
    $("form#filter-by-bedrooms #filterSidebar_to").on("change", function() {
        //$("form#filter-by-dates").submit();
        $("form#filter-by-bedrooms").submit();
    });
    
    $("form#inquiry #dest_checkOut, form#inquiry #inquiry_room").on("change", function() {
        let id = $('#villa_id').val();
        let dest_checkIn = $('#dest_checkIn').val();
        let dest_checkOut = $('#dest_checkOut').val();
        let bedrooms = $('#inquiry_room').val();
        let percentage = $('#villa_percentage').val();
        console.log("test");
        if(dest_checkIn && dest_checkOut){
            // Split the date string by hyphen to extract day, month, and year components
            var fromDateParts = dest_checkIn.split("-");
            var toDateParts = dest_checkOut.split("-");
        
            // Create Date objects using the extracted date components
            var fromDate = new Date(
              fromDateParts[2],
              fromDateParts[1] - 1,
              fromDateParts[0]
            );
            var toDate = new Date(
              toDateParts[2],
              toDateParts[1] - 1,
              toDateParts[0]
            );
            
            
            if( toDate > fromDate ) {
                $.ajax({
                    type : "POST",
                    url : ajax_url.ajaxurl,
                    data : {action: "fetch_rates_of_single_villa", id, dest_checkIn, dest_checkOut, bedrooms, percentage},
                    success: function(ajaxResponse) {
                        
                        if(ajaxResponse.success) {
                            // Get a reference to the rates div
                            var ratesDiv = document.querySelector('.rates-block');
                            // Check if rates div exists and percentage is 0
                            if (!document.querySelector('.rates-block') && percentage == "0") {
                                var ratesDiv = document.createElement('div');
                                ratesDiv.className = 'rates-block';
                
                                var ratesSpan = document.createElement('span');
                                ratesSpan.textContent = "Rates: ";
                                ratesDiv.appendChild(ratesSpan);
                
                                var ratesValueSpan = document.createElement('span');
                                ratesValueSpan.textContent = "US$ " + ajaxResponse.data.rates + " per night";
                                ratesDiv.appendChild(ratesValueSpan);
                
                                var nightsSpan = document.createElement('span');
                                nightsSpan.textContent = "Total " + ajaxResponse.data.diff + " night" + (parseInt(ajaxResponse.data.diff) > 1 ? "s" : "") + ":";
                                ratesDiv.appendChild(nightsSpan);
                
                                var totalSpan = document.createElement('span');
                                totalSpan.textContent = "US$ " + ajaxResponse.data.total;
                                ratesDiv.appendChild(totalSpan);
                
                                document.querySelector('.rates').appendChild(ratesDiv);
                            }
                            // Check if rates div exists and percentage is not 0
                            else if (!document.querySelector('.rates-block') && percentage != "0") {
                                var ratesDiv = document.createElement('div');
                                ratesDiv.className = 'rates-block';
                
                                var ratesSpan = document.createElement('span');
                                ratesSpan.textContent = "Rates: ";
                                ratesDiv.appendChild(ratesSpan);
                
                                var ratesValueSpan = document.createElement('span');
                                ratesValueSpan.textContent = "US$ " + ajaxResponse.data.rates + " per night";
                                ratesDiv.appendChild(ratesValueSpan);
                
                                var taxSpan = document.createElement('span');
                                taxSpan.textContent = "Tax " + percentage + "% :";
                                ratesDiv.appendChild(taxSpan);
                
                                var taxValueSpan = document.createElement('span');
                                taxValueSpan.textContent = "$ " + ajaxResponse.data.tax + " tax";
                                ratesDiv.appendChild(taxValueSpan);
                
                                var nightsSpan = document.createElement('span');
                                nightsSpan.textContent = "Total " + ajaxResponse.data.diff + " night" + (parseInt(ajaxResponse.data.diff) > 1 ? "s" : "") + ":";
                                ratesDiv.appendChild(nightsSpan);
                
                                var totalSpan = document.createElement('span');
                                totalSpan.textContent = "US$ " + ajaxResponse.data.total;
                                ratesDiv.appendChild(totalSpan);
                
                                document.querySelector('.rates').appendChild(ratesDiv);
                            }
                            else {
                                if (percentage != 0) {
                                    var ratesSpan = ratesDiv.querySelector('span:nth-of-type(2)');
                                    
                                    var taxesSpan = ratesDiv.querySelector('span:nth-of-type(4)');
                                    taxesSpan.textContent = "US$ " + ajaxResponse.data.tax;
                                    
                                    var nightsSpan = ratesDiv.querySelector('span:nth-of-type(5)');
                                    var totalSpan = ratesDiv.querySelector('span:nth-of-type(6)');
                                    // Update the number of nights
                                    
                                }else {
                                    var ratesSpan = ratesDiv.querySelector('span:nth-of-type(2)');
                                    
                                    var nightsSpan = ratesDiv.querySelector('span:nth-of-type(3)');
                                    
                                    // Update the total value
                                    var totalSpan = ratesDiv.querySelector('span:nth-of-type(4)');
                                }
                                
                                
                                ratesSpan.textContent = "US$ " + ajaxResponse.data.rates + " per night";
                
                                if (parseInt(ajaxResponse.data.diff) > 1) {
                                  nightsSpan.textContent = "Total " + ajaxResponse.data.diff + " night(s):";
                                }else {
                                    nightsSpan.textContent = "Total " + ajaxResponse.data.diff + " night:";
                                }
                                
                                totalSpan.textContent = "US$ " + ajaxResponse.data.total;
                            }
                        }
        
                    },
                    error:function(error){
                        console.log('message Error' + JSON.stringify(error));
                    }                    
                });     
            }
               
        }
        
        //$("form#inquiry").submit();
    });
    $("form#inquiry #inquiry_room").on("keyup", function() {
        let room = $(this).val();
        // if(room) {
        //     $("form#inquiry").submit();
        // }
    });
});