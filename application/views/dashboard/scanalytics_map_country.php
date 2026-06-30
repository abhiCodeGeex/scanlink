<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBIlkNZMrMpvi0207s670Csx8DOP2pYsc"></script>
<?php $pid= $profile['id']; 
	echo $tid;
	$link = $_SERVER['PHP_SELF'];
 $link_array = explode('/',$link);
 $tid = end($link_array);
	 $results = DB::select('*')->from('ana_item_analytics')->where('id', '=', $tid)
	                               ->execute(); 
							 $someArray =$results; ?>
<script type="text/javascript">
  var map;
  var infowindow;
  var tmpArray = [];
  function LoadMap(){
     var jsonData =JSON.parse('[<?php $i=1; $arrayvalue=count($someArray);  foreach ($someArray as $key => $value) {   ?>{"latitude":"<?php echo $value["latitude"];?>","longitude":"<?php echo $value["longitude"];?>","created":"<?php echo $value["created_at"];?>","device":"<?php echo $value["id_device"];?>","browser":"<?php echo $value["id_browser"];?>","scan_type":"<?php echo $value["scan_type"];?>"}<?php if($arrayvalue== $i){}else{echo ",";} $i++;} ?>]');
    //console.log(jsonData.locations);return ;
    if(jsonData.length > 0){
      var myLatlng = new google.maps.LatLng(jsonData[0].latitude, jsonData[0].longitude);
    }else{
      var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
    }
    			
    var mapOptions = {
      zoom: 4,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    			
    map = new google.maps.Map(document.getElementById('map'), mapOptions);
    			
    var i;
    for (i = 0; i < jsonData.length; i++) {  
    			
      if( jsonData[i].scan_type == "gps" ){
        var image = '<?php echo URL::base() . "images/gps-map-marker.png"; ?>';	
      }else{
        var image = '<?php echo URL::base() . "images/non-gps-map-marker.png"; ?>';	
      }
    			
    			
      var contentString = '<div class="infowindow" >';
      contentString += '<p><strong>Time</strong>:' + jsonData[i].created + '</p>';
      if(jsonData[i].device.length > 0){
      //  contentString += '<p><strong>Device</strong>:';
        
        //  contentString += " "+jsonData[i].device + "-" + jsonData[i].device;
       
      }
    				
      if(jsonData[i].browser.length > 0){
        //contentString += '<p><strong>Browser</strong>:';
       
        //  contentString += " "+jsonData[i].browser + "-" + jsonData[i].browser;
       
      }
    					
      contentString +='<p><strong>Cords</strong>:' + jsonData[i].latitude+ ', '+ jsonData[i].longitude+'</p>';
      contentString +='</div>';
      contentString +='</div>';
      infowindow = new google.maps.InfoWindow({ content: contentString });
      var marker = new google.maps.Marker({
        position: new google.maps.LatLng(jsonData[i].latitude, jsonData[i].longitude),
        map: map,
        icon: image,
        infowindow: infowindow
      });
      //infowindow.open(map, marker);
      google.maps.event.addListener(marker, 'click', function() { this.infowindow.open(map, this); });
    }	
  }
    		

  $(document).ready(function(){
    LoadMap();		
  });
    			
</script>
<style>
  .infowindow {
    font-size:13px;
  }
  .infowindow strong {
    margin:5px;
  }
</style>
<p><?php /* This is the Dashboard Manager Area. You can manage your profile(s) here. */ ?></p>
<?php echo $innermenu; ?>
<?php
if (!$expried_or_not) {
  ?>
  <div class="contentpart">
  	<h3><?php echo 'Profile ' . $profile['id'] . '. '.ucwords($profile['code_profile_name']) ;?></h3>
    <div class="my-qr-codes">
    	
      <h3 style=" width:480px; float:left;">Analytics by Country - <?php echo $country; ?> &nbsp; </h3>
      <div class="ana-info" style="float: right">
        <span><img src="<?php echo URL::base() . "images/non-gps-map-marker.png"; ?>" width="30"  /> Non GPS Location</span>
        &nbsp;&nbsp;
        <span><img src="<?php echo URL::base() . "images/gps-map-marker.png"; ?>" width="30"  /> Scan GPS Location</span>

        <div style="float: right; margin-left: 10px;" class="export">
          <?php //echo "<pre>"; print_r ($profile); echo "</pre>"; ?>
          <a href="<?php echo URL::base() ?>dashboard/scanalytics_map/<?php echo $profile['id']; ?>" class="link-button">Back</a>
        </div>

      </div>


      <div class="clear">&nbsp;</div>
      <div id="map" style="width: 100%; height: 400px;"></div>



    </div>
  </div>

<?php } else { ?>
  <p>You can not perform this action on expired profile.</p>
<?php } ?>
