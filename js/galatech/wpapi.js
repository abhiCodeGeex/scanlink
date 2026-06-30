(function ($) {
    $.variables = {
        options: {
            latitude: '',
            longitude: '',
            url: '',
            response: false,
            urlKey: '',
						keyValues: '',
        }
        //elements: []
    };

    $.fn.latlng = function (key) {
        //initialize geo lib.
        // live url
       
        $.variables.urlKey = 'https://www.scanlink.com.au/api/web/api.php/item/index';
		
		
        if (geo_position_js.init()) {
            geo_position_js.getCurrentPosition(success_callback, error_callback, {
                enableHighAccuracy: true,
                timeout: 1000
              });
        } else {
            alert("Error! this functionality is not available for this device.");
        }

        function success_callback(p) {
            var lat = p.coords.latitude;
            var lng = p.coords.longitude;
            
            //below 3 lines used for location fun in form builder
				document.getElementById('latitude').value = lat;
				document.getElementById('longitude').value = lng;
				if(document.getElementById('google_map_div'))				
					map_initialize();
				
            //:set latitude.
            $.variables.latitude = p.coords.latitude.toFixed(2);
            //:set longitude.
            $.variables.longitude = p.coords.longitude.toFixed(2);
            if ($.variables.longitude != null && $.variables.latitude != null) {
								if( typeof $.variables.keyValues == "string"){
									var key = new Array($.variables.keyValues);
								}else{
									var key = $.variables.keyValues;
								}
																
								for( var k = 0; k < key.length; k++){
									var url_var = $.variables.urlKey + "?key=" + key[k] + "&lat=" + $.variables.latitude + "&lng=" + $.variables.longitude + "&callback=?";

									$.ajax({
											url: url_var,
											dataType: 'json',
											async:false,
											success: function (result) {},
											error: function (request, textStatus, errorThrown) {},
											complete: function (request, textStatus) { //for additional info
													if ($.variables.url != null && $.variables.time != null) {
															setTimeout(function () {
																	document.location.href = $.variables.url;
															}, $.variables.time);
													}
											}
									});
								}
            }
        }

        function error_callback(p) {
        				//alert(p.code); //PERMISSION_DENIED=1, POSITION_UNAVAILABLE=2, TIMEOUT=3 - got 3 in android, got 1 in iPod

										
        				
						if( typeof $.variables.keyValues == "string"){
							var key = new Array($.variables.keyValues);
						}else{
							var key = $.variables.keyValues;
						}
						
						 
						
						for( var k = 0; k < key.length; k++){
							var url_var = $.variables.urlKey + "?key=" + key[k] + "&lat=&lng=&callback=?";
							
							$.ajax({
									url: url_var,
									dataType: 'json',
									async:false,
									success: function (result) {},
									error: function (request, textStatus, errorThrown) {},
									complete: function (request, textStatus) { //for additional info
											if ($.variables.url != null && $.variables.time != null) {
													setTimeout(function () {
															document.location.href = $.variables.url;
													}, $.variables.time);
											}
									}
							});
						}
            //jqxhr.complete();
        }
    };

    $.fn.redirect = function (url, time) {
        $.variables.url = url;
        $.variables.time = time;
    }
		

    $.fn.wpanalytics = function (key) {
    		$.variables.keyValues = key;
       $.fn.latlng(key);
    };
})(jQuery);
