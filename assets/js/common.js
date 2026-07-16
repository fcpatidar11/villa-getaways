// CUSTOM JQUERY UI START

$(function() {
    $(".header-form select.destination").selectmenu();
    $(".home-form select.destination").selectmenu();
    
    // $(".header-form select.location").selectmenu();
    // $(".home-form select.location").selectmenu();
    
    // $(".header-form select.region").selectmenu();
    // $(".home-form select.region").selectmenu();
    
    $(".header-form select.bedrooms").selectmenu();
    $(".home-form select.bedrooms").selectmenu();
    
    $("#destination-top").selectmenu();
    $("#bedrooms-top").selectmenu();
    $(".contact_destination").selectmenu();
    // fliter bar
    $("#filterBarRooms").selectmenu();
    $("#filterBarPricing").selectmenu();
    $("#filterPriceRecommended").selectmenu();
    // dialog
    $("#help_dlg_city").selectmenu();
    $("#help_dlg_bedrooms").selectmenu();
    $("#help_dlg_priceRange").selectmenu();
    $("#help_dlg_people").selectmenu();
    $("#after_something_dlg_city").selectmenu();
    $("#after_something_dlg_bedrooms").selectmenu();
    $("#after_something_dlg_priceRange").selectmenu();
    $("#after_something_dlg_people").selectmenu();
});



// PICKTURE SLIDER START

jQuery('#pictures-demo').slippry({
    // general elements & wrapper
    slippryWrapper: '<div class="sy-box pictures-slider" />', // wrapper to wrap everything, including pager

    // options
    adaptiveHeight: false, // height of the sliders adapts to current slide
    captions: false, // Position: overlay, below, custom, false

    // pager
    pager: false,

    // controls
    controls: false,
    autoHover: false,

    // transitions
    transition: 'kenburns', // fade, horizontal, kenburns, false
    kenZoom: 15540,
    speed: 10000 // time the transition takes (ms)
});

function getThreeDayFromToday(selectedDate){
    var parts = selectedDate.split("-");
    var fromDate = new Date(parts[2], parts[1] - 1, parts[0]);
    // Add three days to the selected date
    var toDate = new Date(fromDate.getTime() + 3 * 24 * 60 * 60 * 1000);
    // Format the resulting date objects as date strings
    var fromDateString = $.datepicker.formatDate("dd-mm-yy", fromDate);
    var toDateString = $.datepicker.formatDate("dd-mm-yy", toDate);
    
    return toDateString;
}
  function formatDate(dateStr) {
        var date = new Date(dateStr);
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear().toString().slice(-2); // Get last 2 digits of the year
        return day + '-' + month + '-' + year;
    }

// SLIDER JS START
$(document).ready(function() {
    
    var dateFormat = "dd-mm-yy";
    
    $("#search-fromdate").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        dateFormat: dateFormat,
        minDate: "today"
    });
    $("#search-todate").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        dateFormat: dateFormat,
        minDate: "+1"
    });
    $("#header-fromdate").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        dateFormat: dateFormat,
        minDate: "today"
    });
    $("#header-todate").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        dateFormat: dateFormat,
        minDate: "+1"
    });
    
    var from = $("#from, #from-top, #filterSidebar_from, #help_dlg_arrival, #after_something_dlg_arrival").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        minDate: "today",
        dateFormat: dateFormat
    });
    
    var to = $("#to, #to-top, #filterSidebar_to,  #help_dlg_departure, #after_something_dlg_departure").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        changeYear: true,
        minDate: "+1",
        // numberOfMonths: 1,
        dateFormat: dateFormat
    });
    
    $("#header-fromdate").datepicker("option", "onSelect", function(selectedDate, inst) {
        // Parse the selected date string into a Date object
        let toDateString = getThreeDayFromToday(selectedDate);
        // Set the to-date input value to the calculated date string
        $("#header-todate").val(toDateString);
        
        $("#header-todate").datepicker("option", "minDate", selectedDate);
        setTimeout(function() {
            $("#header-todate").datepicker('show');
        }, 100);
    });
    
    $(window).scroll(function() {
        if ($(document).scrollTop() > 100) {
            $('header').addClass('shrink');
        } else {
            $('header').removeClass('shrink');
        }
    });


    $('.location-slider').owlCarousel({
        loop: true,
        margin: 0,
        center: true,
        items: 1,
        nav: true,
        dots: false,
        dotsData: false,
        responsiveClass: true,
        responsiveRefreshRate: true,
        smartSpeed: 1000,
        autoplay: true,
        autoPlaySpeed: 5000,
        autoPlayTimeout: 5000,
        autoplayHoverPause: false
    });

    $('.welcome-slider').owlCarousel({
        loop: true,
        margin: 0,
        center: true,
        items: 1,
        nav: true,
        dots: false,
        dotsData: false,
        responsiveClass: true,
        responsiveRefreshRate: true,
        smartSpeed: 1000,
        autoplay: true,
        autoPlaySpeed: 5000,
        autoPlayTimeout: 5000,
        autoplayHoverPause: false
    });

    function switchOwl(slider) {
        var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        if (width >= 768 && slider.data('owlCarousel') == undefined) {
            slider.owlCarousel({
                navigation: true,
                slideSpeed: 300,
                paginationSpeed: 400,
                singleItem: true
            });
        } else if (width < 768 && slider.data('owlCarousel') != undefined) {
            slider.data('owlCarousel').destroy();
        }
    }

    $("document").ready(function() {
        var owl = $("#carousel");
        switchOwl(owl);
        $(window).on('resize', function() {
            switchOwl(owl);
        });
    });


    $(".welcome-slider .owl-dots").wrap("<div class='owl-dots-wrapper'><div>");

    $(".btn-searh").click(function() {
        $(".top-destination-form").toggleClass("show");
    });

    $('.card-img-slider-2').owlCarousel({
        loop: true,
        margin: 0,
        lazyLoad:true,
        center: true,
        items: 1,
        nav: true,
        dots: false,
        dotsData: false,
        responsiveClass: true,
        responsiveRefreshRate: true,
        smartSpeed: 1000,
        navText: [
            '<svg width="61" height="60" viewBox="0 0 61 60" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M45.3844 7.07625L42.4294 3.75L15.3844 30L42.4294 56.25L45.3844 52.9237L21.7669 30L45.3844 7.07625Z" fill="#fff"/></svg>',
            '<svg width="61" height="61" viewBox="0 0 61 61" fill="" xmlns="http://www.w3.org/2000/svg"><path d="M15.3844 7.64095L18.3394 4.3147L45.3844 30.5647L18.3394 56.8147L15.3844 53.4884L39.0019 30.5647L15.3844 7.64095Z" fill="#fff"/></svg>'
        ],
    });
    
      $('.card-img-slider-1').owlCarousel({
        loop: true,
        margin: 0,
        lazyLoad:true,
        center: true,
        items: 1,
        nav: true,
        dots: false,
        dotsData: false,
        responsiveClass: true,
        responsiveRefreshRate: true,
        smartSpeed: 1000,
        navText: [
            '<svg width="61" height="60" viewBox="0 0 61 60" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M45.3844 7.07625L42.4294 3.75L15.3844 30L42.4294 56.25L45.3844 52.9237L21.7669 30L45.3844 7.07625Z" fill="#000"/></svg>',
            '<svg width="61" height="61" viewBox="0 0 61 61" fill="" xmlns="http://www.w3.org/2000/svg"><path d="M15.3844 7.64095L18.3394 4.3147L45.3844 30.5647L18.3394 56.8147L15.3844 53.4884L39.0019 30.5647L15.3844 7.64095Z" fill="#000"/></svg>'
        ],
    });
    
    
    // Validation on Search From
    function validateSearchForm(type) {
        let cssClass = "." + type;
        var destination = $(cssClass + "-form select.destination").val();
        if (destination == "" || destination == null) {
            $(cssClass + "-form .show-errors").css("display", "block");
            $(cssClass + "-form .show-errors").find("span").html("Please select destination");
            return false;
        }
        
        /*
        var location = $(cssClass + "-form select.location").val();
        if (location == "" || location == null) {
            $(cssClass + "-form .show-errors").css("display", "block");
            $(cssClass + "-form .show-errors").find("span").html("Please select location");
            return false;
        }
        
        var from = $(cssClass + "-form select.from").val();
        if (from == "" || from == null) {
            $(cssClass + "-form .show-errors").css("display", "block");
            $(cssClass + "-form .show-errors").find("span").html("Please select from date");
            return false;
        }
        
        var to = $(cssClass + "-form select.to").val();
        if (to == "" || to == null) {
            $(cssClass + "-form .show-errors").css("display", "block");
            $(cssClass + "-form .show-errors").find("span").html("Please select to date");
            return false;
        }
        
        var bedrooms = $(cssClass + "-form select.bedrooms").val();
        if (bedrooms == "" || bedrooms == null) {
            $(cssClass + "-form .show-errors").css("display", "block");
            $(cssClass + "-form .show-errors").find("span").html("Please select number of bedrooms");
            return false;
        }
        */
    
        $(cssClass + "-form .show-errors").css("display", "none");
        $(cssClass + "-form .show-errors").find("span").html("");
        return true;
    }

    $( ".header-form form.search_form" ).submit(function( event ) {
        if( validateSearchForm('header') )
            $(this).submit();
        else
            event.preventDefault();
    });
    $( ".home-form form.search_form" ).submit(function( event ) {
        if( validateSearchForm('home') )
            $(this).submit();
        else
            event.preventDefault();
    });
    
    $( "form#search_villa_form" ).submit(function( event ) {
        event.preventDefault();
        let villa_id = $("#search_villa_id").val();
        if(villa_id !== "" && villa_id.length > 0) {
            let formAction = $(this).attr('action');
            window.location.replace(formAction + "/" + villa_id);
        } else {
            event.preventDefault();
        }
    });
    
    function toggleFavorite(villa_id, location_name, villa_name) {
    	refreshFavCounter();
    }
    
    function readCookie(name) {
        let key = name + "=";
        let cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            let cookie = cookies[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1, cookie.length);
            }
            if (cookie.indexOf(key) == 0) {
                return cookie.substring(key.length, cookie.length);
            }
        }
        return null;
    }
    
    function refreshFavCounter() {
        let __favorites_villas = readCookie('__favorites_villas'),
        $favCounter = $('.fav-counter');
        if (__favorites_villas) {
            let __favoritesVillas = __favorites_villas.split("|").map( item => parseInt(item) );
            $favCounter.text(__favoritesVillas.length)
            __favoritesVillas.length ? $favCounter.show() : $favCounter.hide();
            highlightFavVilla(__favoritesVillas);
        } else {
            highlightFavVilla(__favoritesVillas = []);
            $('.fav-counter').text(null).hide();
        }
    }
    
    function highlightFavVilla(__favoritesVillas) {
        $('.villa_fav').each(function(idx, item) {
            let $villaFav = $(this),
            villa_id  = parseInt($villaFav.find('a').data('id'));
            // if (__favoritesVillas.indexOf(villa_id) !== -1) {
            if(__favoritesVillas.includes(villa_id)) {
                $villaFav.addClass('favorite');
            } else {
                $villaFav.removeClass('favorite');
                let parent = $villaFav.parents('.single-villa-box');
                let {pathname} = window.location;
                if(pathname == "/favourites/") {
                    parent.remove();
                }
            }
        });
    }
    
    // Save
    $('body').on('click', '.villa_fav > a', function(e) {
        e.preventDefault();
        const event = new Date('01 Jan 1971 00:00:00 PDT');
        let villa_id = parseInt($(this).data('id'));
        let __favorites_villas = readCookie('__favorites_villas');
        if (__favorites_villas) {
            let __favoritesVillas = __favorites_villas.split("|").map( item => parseInt(item) );
            if(!__favoritesVillas.includes(villa_id)) {
                __favoritesVillas.push(villa_id);
                __set = new Set(__favoritesVillas)
                __favoritesVillas = [...__set];
                let __favorites_villas_cookie = __favoritesVillas.join('|');
                document.cookie = '__favorites_villas=' + __favorites_villas_cookie + '; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
            }else {
                __favoritesVillas = __favoritesVillas.filter(item => item !== villa_id);
               let __favorites_villas_cookie = __favoritesVillas.join('|');
                document.cookie = '__favorites_villas=' + __favorites_villas_cookie + '; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
            }
        } else {
            document.cookie = '__favorites_villas=' + villa_id + '; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
        }
        refreshFavCounter();
    });
    refreshFavCounter();
    const star = document.getElementById("star");
    if(star) {
        star.addEventListener("touchstart", myFunction);
        function myFunction(e) {
            $('#star').tooltip('show')
        }
    }
    
});
//  Modal on load delay JavaScript
$(window).load(function() {
    // var form_activeWidget;
    
    // $("#inquiry_form").validate({
    //       rules: {
    //         date_from: {
    //           required: true,
    //         },
    //         date_to: {
    //           required: true,
    //         }
    //       },
    //       submitHandler: function(form) {
    //         form.submit();
    //       }
    // });
    
    // var dateFormat = "dd/mm/yy",
    // beginDate = $( "#reservation3_from" )
    // .datepicker({
    //     defaultDate: "+1w",
    //     changeMonth: true,
    //     numberOfMonths: 3,
    //     minDate: 'today',
    //     dateFormat: dateFormat,
    //     onClose: function() {
    //         $(this).valid();
    //     }
    // })
    // .on( "change", function() {
    //     endDate.datepicker( "option", "minDate", getDate( this ) );
    // }),
    // endDate = $( "#reservation3_to" ).datepicker({
    //     defaultDate: "+1w",
    //     changeMonth: true,
    //     numberOfMonths: 3,
    //     minDate: '+1d',
    //     dateFormat:dateFormat,
    //     onClose: function() {
    //         $(this).valid();
    //     }
    // })
    // .on( "change", function() {
    //     beginDate.datepicker( "option", "maxDate", getDate( this ) );
    // });
    
    // $('#inquiry_form input').keydown(function(e)
    // {
    //     if(e.which > 46) $(this).addClass('filled');
    // });
    
    // $('#inquiry_form input').keyup(function(e)
    // {
    //     if($(this).val() == '')  $(this).removeClass('filled');
    //     else $(this).addClass('filled');
    // });
    
    // $('#inquiry_form input').change(function()
    // {
    //     if($(this).val() == '')  $(this).removeClass('filled');
    //     else $(this).addClass('filled');
    // });
    
    // $('#inquiry_form select').change(function()
    // {
    //     if($(this).val() == '')  $(this).removeClass('filled');
    //     else $(this).addClass('filled');
    // });
    
    // $('#inquiry_form input').each(function(){
    //     if($(this).val() == '')  $(this).removeClass('filled');
    //     else $(this).addClass('filled');
    // })
    
    // function getDate( element ) {
    //     var date;
    //     try {
    //         date = $.datepicker.parseDate( dateFormat, element.value );
    //     } catch( error ) {
    //         date = null;
    //     }
    //     return date;
    // }
    
    // function showCalendar() {
    //     $('#datepickerHelper').datepicker('show')
    // }
    
    
    setTimeout(function() {
        $('#help_dlg').modal('show');
    }, 1000);

    // Initialize Swiper
    // var swiper = new Swiper('.swiper-container', {
    //     loop: false,
    //     autoplay: false,
    //     slidesPerView: 'auto',
    //     spaceBetween: 20,
    //     simulateTouch: true,
        
    //     breakpoints: {
    //         767: {
    //             spaceBetween: 20,
    //             centeredSlides: false,
    //             slidesPerView: 3
    //         },
    //         992: {
    //             spaceBetween: 20,
    //             centeredSlides: false,
    //             slidesPerView: 3
    //         },
    //         9999: {
    //             spaceBetween: 20,
    //             centeredSlides: false,
    //             slidesPerView: 6
    //         }
    //     }
    // });

});

$(document).ready(function(){
        
    $('.count-box').click(function() {
        var $gallery = $('.view-gallery a').attr('data-gallery');
        var $gallery_array = $gallery.split(',');
        var $gallery_list = new Array();

        for ($i = 0; $i < $gallery_array.length; $i++) {
            var $image = $gallery_array[$i];
            var $image_url = $image.replace(/'/g, '');
            var $image_obj = {
                'src': $image_url,
                'thumb': $image_url
            };
            $gallery_list.push($image_obj);
        }

        lightGallery($(this)[0], {
            dynamic: true,
            dynamicEl: $gallery_list
        })
    });
    
    // $('#property-image-gallery .gallery').slick({
    //     slidesToShow: 1,
    //     slidesToScroll: 1,
    //     asNavFor: null,
    //     centerMode: true,
    //     centerPadding:'0',
    //     infinite: true,
    //     mobileFirst:true,
    //     autoplay:true,
    //     arrows: true,
    //     nextArrow: '<a href="javascript:;" class="slick-next"></a>',
    //     prevArrow: '<a href="javascript:;" class="slick-prev"></a>',
    //     responsive: [
    //         {
    //           breakpoint: 1028,
    //           settings: {
    //               slidesToShow: 1,
    //               slidesToScroll: 1,
    //               arrows: true,
    //               asNavFor: null,
    //               centerMode: true,
    //               centerPadding:'25%',
    //               infinite: true,
    //               nextArrow: '<a href="javascript:;" class="slick-next"></a>',
    //               prevArrow: '<a href="javascript:;" class="slick-prev"></a>'
    //           }
    //         },
    //          {
    //           breakpoint: 767,
    //           settings: {
    //               slidesToShow: 1,
    //               slidesToScroll: 1,
    //               arrows: true,
    //               asNavFor: null,
    //               centerMode: true,
    //               centerPadding:'25%',
    //               infinite: true,
    //               nextArrow: '<a href="javascript:;" class="slick-next"></a>',
    //               prevArrow: '<a href="javascript:;" class="slick-prev"></a>'
    //           }
    //         },
    //         {
    //           breakpoint: 480,
    //           settings: {
    //             slidesToShow: 1,
    //             slidesToScroll: 1,
    //             centerMode: true,
    //             centerPadding:'0',
    //             infinite: true,
    //             arrows: true,
    //             nextArrow: '<a href="javascript:;" class="slick-next"></a>',
    //             prevArrow: '<a href="javascript:;" class="slick-prev"></a>'
    //           }
    //         }
    //     ]
    // });
    
    
var slideWrapper = $(".gallery"),
iframes = slideWrapper.find('iframe'),
lazyImages = slideWrapper.find('.slide-image'),
lazyCounter = 0;

// POST commands to YouTube or Vimeo API
function postMessageToPlayer(player, command){
  if (player == null || command == null) return;
  player.contentWindow.postMessage(JSON.stringify(command), "*");
}

// When the slide is changing
function playPauseVideo(slick, control){
  var currentSlide, slideType, startTime, player, video;
  currentSlide = slick.find(".slick-current");
  slideType = currentSlide.attr("class").split(" ")[1];
  player = currentSlide.find("iframe").get(0);
  startTime = currentSlide.data("video-start");

  if (slideType === "vimeo") {
    switch (control) {
      case "play":
        if ((startTime != null && startTime > 0 ) && !currentSlide.hasClass('started')) {
          currentSlide.addClass('started');
          postMessageToPlayer(player, {
            "method": "setCurrentTime",
            "value" : startTime
          });
        }
        postMessageToPlayer(player, {
          "method": "play",
          "value" : 1
        });
        break;
      case "pause":
        postMessageToPlayer(player, {
          "method": "pause",
          "value": 1
        });
        break;
    }
  } else if (slideType === "youtube") {
    switch (control) {
      case "play":
        postMessageToPlayer(player, {
          "event": "command",
          "func": "mute"
        });
        postMessageToPlayer(player, {
          "event": "command",
          "func": "playVideo"
        });
        break;
      case "pause":
        postMessageToPlayer(player, {
          "event": "command",
          "func": "pauseVideo"
        });
        break;
    }
  } else if (slideType === "video") {
    video = currentSlide.children("video").get(0);
    if (video != null) {
      if (control === "play"){
        video.play();
      } else {
        video.pause();
      }
    }
  }
}

// Resize player
// function resizePlayer(iframes, ratio) {
//   if (!iframes[0]) return;
//   var win = $(".main-slider"),
//       width = win.width(),
//       playerWidth,
//       height = win.height(),
//       playerHeight,
//       ratio = ratio || 16/9;

//   iframes.each(function(){
//     var current = $(this);
//     if (width / ratio < height) {
//       playerWidth = Math.ceil(height * ratio);
//       current.width(playerWidth).height(height).css({
//         left: (width - playerWidth) / 2,
//          top: 0
//         });
//     } else {
//       playerHeight = Math.ceil(width / ratio);
//       current.width(width).height(playerHeight).css({
//         left: 0,
//         top: (height - playerHeight) / 2
//       });
//     }
//   });
// }

// DOM Ready

// Initialize
slideWrapper.on("init", function(slick){
slick = $(slick.currentTarget);
setTimeout(function(){
  playPauseVideo(slick,"play");
}, 1000);
//resizePlayer(iframes, 16/9);
});
slideWrapper.on("beforeChange", function(event, slick) {
    slick = $(slick.$slider);
    playPauseVideo(slick,"pause");
});
slideWrapper.on("afterChange", function(event, slick) {
    slick = $(slick.$slider);
    playPauseVideo(slick,"play");
});
slideWrapper.on("lazyLoaded", function(event, slick, image, imageSource) {
    lazyCounter++;
    if (lazyCounter === lazyImages.length){
      lazyImages.addClass('show');
      // slideWrapper.slick("slickPlay");
    }
});

//start the slider
slideWrapper.slick({
    // fade:true,
    autoplaySpeed:4000,
    lazyLoad:"progressive",
    speed:600,
    arrows:false,
    centerMode: true,
    centerPadding:'0',
    infinite: true,
    mobileFirst:true,
    //autoplay:true,
    arrows: true,
    nextArrow: '<a href="javascript:;" class="slick-next"></a>',
    prevArrow: '<a href="javascript:;" class="slick-prev"></a>',
    speed:600,
    responsive: [
        {
          breakpoint: 1028,
          settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              arrows: true,
              asNavFor: null,
              centerMode: true,
              centerPadding:'15%',
              infinite: true,
              nextArrow: '<a href="javascript:;" class="slick-next"></a>',
              prevArrow: '<a href="javascript:;" class="slick-prev"></a>'
          }
        },
    ]
});

// Resize event
// $(window).on("resize.slickVideoPlayer", function(){  
//   resizePlayer(iframes, 16/9);
// });
    
      //start the slider
    //   slideWrapper.slick({
    //     // fade:true,
    //     slidesToShow: 1,
    //     slidesToScroll: 1,
    //     //autoplaySpeed:4000,
    //     lazyLoad:"progressive",
    //     centerMode: true,
    //     centerPadding:'0',
    //     infinite: true,
    //     mobileFirst:true,
    //     //autoplay:true,
    //     arrows: true,
    //     nextArrow: '<a href="javascript:;" class="slick-next"></a>',
    //     prevArrow: '<a href="javascript:;" class="slick-prev"></a>',
    //     speed:600,
    //     responsive: [
    //         {
    //           breakpoint: 1028,
    //           settings: {
    //               slidesToShow: 1,
    //               slidesToScroll: 1,
    //               arrows: true,
    //               asNavFor: null,
    //               centerMode: true,
    //               centerPadding:'15%',
    //               infinite: true,
    //               nextArrow: '<a href="javascript:;" class="slick-next"></a>',
    //               prevArrow: '<a href="javascript:;" class="slick-prev"></a>'
    //           }
    //         },
        //      {
        //       breakpoint: 767,
        //       settings: {
        //           slidesToShow: 1,
        //           slidesToScroll: 1,
        //           arrows: true,
        //           asNavFor: null,
        //           centerMode: true,
        //           centerPadding:'25%',
        //           infinite: true,
        //           nextArrow: '<a href="javascript:;" class="slick-next"></a>',
        //           prevArrow: '<a href="javascript:;" class="slick-prev"></a>'
        //       }
        //     },
        //     {
        //       breakpoint: 480,
        //       settings: {
        //         slidesToShow: 1,
        //         slidesToScroll: 1,
        //         centerMode: true,
        //         centerPadding:'0',
        //         infinite: true,
        //         arrows: true,
        //         nextArrow: '<a href="javascript:;" class="slick-next"></a>',
        //         prevArrow: '<a href="javascript:;" class="slick-prev"></a>'
        //       }
        //     }
        //]
        //arrows:false,
        //dots:true,
        //cssEase:"cubic-bezier(0.87, 0.03, 0.41, 0.9)"
      //});
    
    
    
    $('body').on('click', '.slick-arrow', function(e) {
        e.stopPropagation();
    });
});



// const descriptionElement = document.getElementById('single-villa-des');
// const maxHeight = 600; // Maximum height in pixels before "Read More" is shown

// const descriptionText = descriptionElement.innerHTML.trim();

// // Create a temporary element to measure the height of the content
// const tempElement = document.createElement('div');
// tempElement.style.position = 'absolute';
// tempElement.style.visibility = 'hidden';
// tempElement.style.width = descriptionElement.offsetWidth + 'px';
// tempElement.innerHTML = descriptionText;
// document.body.appendChild(tempElement);

// if (tempElement.offsetHeight > maxHeight) {
//   let truncatedText = '';
//   const words = descriptionText.split(' ');

//   for (let i = 0; i < words.length; i++) {
//     tempElement.innerHTML = truncatedText + ' ' + words[i];

//     if (tempElement.offsetHeight > maxHeight) {
//       break;
//     }

//     truncatedText += ' ' + words[i];
//   }

//   const remainingText = descriptionText.slice(truncatedText.length).trim();

//   const truncatedContent = document.createElement('div');
//   truncatedContent.innerHTML = `<p>${truncatedText.trim()}...</p>`;

//   const remainingContent = document.createElement('div');
//   remainingContent.innerHTML = `<p>${remainingText}</p>`;
//   remainingContent.style.display = 'none';

//   const readMoreButton = document.createElement('button');
//   readMoreButton.classList.add('read-more-single');
//   readMoreButton.textContent = 'Read More';

//   readMoreButton.addEventListener('click', function () {
//     if (remainingContent.style.display === 'none') {
//       remainingContent.style.display = 'block';
//       readMoreButton.textContent = 'Read Less';
//     } else {
//       remainingContent.style.display = 'none';
//       readMoreButton.textContent = 'Read More';
//     }
//   });

//   descriptionElement.innerHTML = '';
//   descriptionElement.appendChild(truncatedContent);
//   descriptionElement.appendChild(remainingContent);
//   descriptionElement.appendChild(readMoreButton);
// }

// // Remove the temporary element
// document.body.removeChild(tempElement);



const descriptionElement = document.getElementById('single-villa-des');
const readMoreBtn = document.querySelector(".read-more-single");
if(readMoreBtn) {
    readMoreBtn.addEventListener('click', toggleDescription);
}

function toggleDescription() {
  descriptionElement.classList.toggle('show-full');
  readMoreBtn.textContent = descriptionElement.classList.contains('show-full') ? 'Read Less' : 'Read More';
}




  




