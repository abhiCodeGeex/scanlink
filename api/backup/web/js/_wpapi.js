(function ($) {
  $.variables = {
    options: {
      latitude: '',
      longitude: '',
      url:'',
      response:false,
      urlKey: ''
    }
  //elements: []
  };
		
  $.fn.latlng = function (key) {
    //initialize geo lib.
    // live url
    $.variables.urlKey = 'http://webplanex.com/demo/analytics/web/api.php/item/index';
    // local url
    //$.variables.urlKey = 'http://pc50/sfprojects/analytics/web/api.php/item/index';				
    if(geo_position_js.init()) {
      geo_position_js.getCurrentPosition(success_callback, error_callback, {
        enableHighAccuracy: true
      });
    } else {
      alert("Error! this functionality is not available for this device.");
    }
    function success_callback(p) {
					
						
					
      var lat = p.coords.latitude;
      var lng = p.coords.longitude;
      //:set latitude.
      $.variables.latitude = p.coords.latitude.toFixed(2);
      //:set longitude.
      $.variables.longitude = p.coords.longitude.toFixed(2);
      if( $.variables.longitude != null && $.variables.latitude != null){
							
        //alert($.variables.urlKey +  "?key="+key+"&lat="+$.variables.latitude+"&lng="+$.variables.longitude+"&callback=?");
				
        var url_var = $.variables.urlKey +  "?key="+key+"&lat="+$.variables.latitude+"&lng="+$.variables.longitude+"&callback=?";
        
        $.ajax({
          url: url_var,
          dataType: 'json',
          success: function(result){
          },
          error: function(request, textStatus, errorThrown) {
          },
          complete: function(request, textStatus) { //for additional info
            if($.variables.url != null && $.variables.time != null){
              setTimeout(function(){
                document.location.href = $.variables.url;
              },$.variables.time);
            }
          }
        });
            
            
      /*var jqxhr = $.getJSON( $.variables.urlKey +  "?key="+key+"&lat="+$.variables.latitude+"&lng="+$.variables.longitude+"&callback=?", function(data) {
          //console.log(data);
          alert ('testsesetest');
        if($.variables.url != null && $.variables.time != null){
            setTimeout(function(){
              document.location.href = $.variables.url;
            },time);
          }
        })*/
      }
    }
    function error_callback(p) {
      var url_var = $.variables.urlKey + "?key="+key+"&lat=&lng=&callback=?";
      $.ajax({
        url: url_var,
        dataType: 'json',
        success: function(result){
        },
        error: function(request, textStatus, errorThrown) {
        },
        complete: function(request, textStatus) { //for additional info
          if($.variables.url != null && $.variables.time != null){
            setTimeout(function(){
              document.location.href = $.variables.url;
            },$.variables.time);
          }
        }
      });
      //alert("hi"+ p);
      /*var jqxhr = $.getJSON($.variables.urlKey + "?key="+key+"&lat=&lng=&callback=?", function(data) {
        if($.variables.url != null && $.variables.time != null){
          setTimeout(function(){
            document.location.href = $.variables.url;
          },time);
        }
        console.log(data);
      })*/
      jqxhr.complete();
    }
  };
    
  $.fn.redirect = function (url,time) {
    $.variables.url = url;
    $.variables.time = time;
  }
		
  $.fn.wpanalytics = function (key) {
    //load google map api.
    //$("head").append('<script type="text/javascript" src="http://maps.google.se/maps/api/js?sensor=false"></script>');
    //$("head").append('<script type="text/javascript" src="geo-min.js"></script>');
    //get lat long.
    $.fn.latlng(key);
  };
})(jQuery);