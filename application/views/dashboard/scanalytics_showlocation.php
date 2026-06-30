<?php
if (!$expried_or_not) {
    $pid = $profile['id'];
//echo $pid;
    $link = $_SERVER['PHP_SELF'];
    $link_array = explode('/', $link);
    $tid = end($link_array);
    //echo $tid;
    	$results = DB::select('*')->from('ana_item_analytics')->where('id', '=', $tid)
        ->execute()
        ->as_array();
		
    ?>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBIlkNZMrMpvi0207s670Csx8DOP2pYsc"></script>
<?php
// $google_address = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=Launceston,Tasmania,Australia&key=AIzaSyBBIlkNZMrMpvi0207s670Csx8DOP2pYsc');

    // echo '<pre>';
    // $google_api_result = json_decode($google_address,true);
    // print_r($google_api_result['results'][0]['geometry']['location']);
    // $latitude = $google_api_result['results'][0]['geometry']['location']['lat'];
    // $longitude = $google_api_result['results'][0]['geometry']['location']['lng'];
    // echo '</pre>';
    // exit;

    ?>
<?php foreach ($results as $row) {?>

<script type="text/javascript">
	$(document).ready(function(){
			var jsonData = 1;
			if( jsonData== 1 ){
				var myLatlng = new google.maps.LatLng(<?php echo $row["latitude"]; ?>,<?php echo $row["longitude"]; ?>);
				var mapOptions = {
					zoom: 4,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				var map = new google.maps.Map(document.getElementById('map'), mapOptions);

				var infowindow = new google.maps.InfoWindow();
				var marker, i;
				<?php if ($row["scan_type"] == "gps") {?>
					var image = '<?php echo URL::base() . "images/gps-map-marker.png"; ?>';
					<?php } else {?>
					var image = '<?php echo URL::base() . "images/non-gps-map-marker.png"; ?>';
					<?php }?>

				marker = new google.maps.Marker({
					position: new google.maps.LatLng(<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>),
					map: map,
					icon:image
				});

				google.maps.event.addListener(marker, 'click', (function(marker, i) {
					return function() {
						var contentString = '<div class="infowindow" >';
								contentString += '<p><strong>Time</strong>:' + '<?php echo $row["created_at"]; ?>' + '</p>';
								contentString += '<p><strong>Device</strong>:';
								contentString += " "+'<?php $did = $row["id_device"];
        $resultdevics = DB::select('*')->from('ana_devices')->where('id', '=', $did)
            ->execute();
        foreach ($resultdevics as $rowdevices) {
            echo $rowdevices["device_name"];
        }

        ?>';
								contentString += '<p><strong>Browser</strong>:';
								contentString += " "+'<?php if ($row["browser_version"] == "" || $row["browser_version"] == "?") {echo "NA";} else {
            $bid = $row["id_browser"];
            $resultbrowser = DB::select('*')->from('ana_browsers')->where('id', '=', $bid)
                ->execute();

            foreach ($resultbrowser as $rowbrowser) {
                echo $rowbrowser["browser_name"];
            }

        }?>';
								contentString +='<p><strong>Cords</strong>:' + <?php echo $row["latitude"]; ?>+ ', '+ <?php echo $row["longitude"]; ?>+'</p>';
								contentString +='</div>';
								contentString +='</div>';
						infowindow.setContent(contentString);
						infowindow.open(map, marker);
					}
				})(marker, i));
			}else{
				alert("No data available.");
				var map;
				function initialize() {
				var mapOptions = {
					zoom: 8,
					center: new google.maps.LatLng(-34.397, 150.644),
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map = new google.maps.Map(document.getElementById('map'),
						mapOptions);
				}
				google.maps.event.addDomListener(window, 'load', initialize);
			}
		});
  </script>
  <?php }?>
<?php }?>
<p><?php /* This is the Dashboard Manager Area. You can manage your profile(s) here. */?></p>
<?php echo $innermenu; ?>
<?php
if (!$expried_or_not) {
    ?>
  <div class="contentpart">
  	<h3><?php echo 'Profile ' . $profile['id'] . '. ' . ucwords($profile['code_profile_name']); ?></h3>
    <div class="my-qr-codes">
      <h3 style=" width:50%; float:left;">Map Analytics &nbsp; </h3>
      <div class="ana-info" style="float: right">
        <div style="float: right; margin-left: 10px;" class="export">
          <?php //echo "<pre>"; print_r ($profile); echo "</pre>"; ?>
          <a href="<?php echo URL::base() ?>dashboard/scanalytics_locations/<?php echo $profile['id']; ?>" class="link-button">Back</a>
        </div>

      </div>
      <div class="ALL">
        <div class="title">
        <div class="fright">
          <div class="title-select">
            <div class="export">
              <br />
            </div>
          </div>
          </div>
          <!-- /fright -->
        </div>
      </div>
      <div class="clear">&nbsp;</div>
      <div id="map" style="width: 100%; height: 400px;"></div>
    </div>
  </div>
<?php } else {?>
  <p>You can not perform this action on expired profile.</p>
<?php }?>