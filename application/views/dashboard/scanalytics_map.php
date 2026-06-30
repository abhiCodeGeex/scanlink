<?php
if (!$expried_or_not) {
    $tid = $profile['id'];
    echo $tid;

    $results = DB::select('*')->from('ana_item_analytics')->where('id_item', '=', $tid)
        ->execute()->as_array();
    $someArray = $results;
    // $output = array();
    // foreach ($someArray as $key => $value) {
    // $seg="{'latitue':".$value["latitude"] . ",'longitude': " . $value["longitude"] . "}";
    //array_push($output, $seg);
    // }
    // $someJSON = json_encode($output);
    // echo $someJSON;
    ?>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBIlkNZMrMpvi0207s670Csx8DOP2pYsc"></script>
<script type="text/javascript">
  $(document).ready(function(){

    var jsonData =JSON.parse('[<?php $i = 1;
    $arrayvalue = count($someArray);foreach ($someArray as $key => $value) {?>{"latitude":"<?php echo $value["latitude"]; ?>","longitude":"<?php echo $value["longitude"]; ?>","country_name":"<?php echo $value["id"]; ?>","scan_type":"<?php echo $value["scan_type"]; ?>"}<?php if ($arrayvalue == $i) {} else {echo ",";}
        $i++;}?>]');


    if(jsonData.length > 0){
      var myLatlng = new google.maps.LatLng(jsonData[0]["latitude"], jsonData[0]["longitude"]);
    }else{
      var myLatlng = new google.maps.LatLng(0,0);
    }

    var myLatlng = new google.maps.LatLng(0,0);

    var mapOptions = {
      zoom: 0,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById('map'), mapOptions);

    var infowindow = new google.maps.InfoWindow();
    var marker, i;
    for (i = 0; i < jsonData.length; i++) {
      if( jsonData[i].scan_type == "gps" ){
        var image = '<?php echo URL::base() . "images/gps-map-marker.png"; ?>';
      }else{
        var image = '<?php echo URL::base() . "images/non-gps-map-marker.png"; ?>';
      }
      console.log(jsonData,'JsonData');
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(jsonData[i].latitude, jsonData[i].longitude),
        map: map,
        icon:image
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          document.location.href = '<?php echo URL::base(); ?>dashboard/scanalytics_map_country/<?php print $profile['id'];?>/'+encodeURIComponent(jsonData[i].country_name)
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  });
</script>
<?php }?>
<p><?php /* This is the Dashboard Manager Area. You can manage your profile(s) here. */?></p>
<?php echo $innermenu; ?>
<?php
if (!$expried_or_not) {
    ?>
<div class="contentpart">
  <div class="my-qr-codes">
    <h3 style=" width:50%; float:left;">Map Analytics &nbsp; - <?php echo 'Profile ' . $profile['id'] . '. ' . ucwords($profile['code_profile_name']); ?></h3>

    <div class="ana-info" style="float: right">
      <span><img src="<?php echo URL::base() . "images/non-gps-map-marker.png"; ?>" width="30"  /> Non GPS Location</span>
      &nbsp;&nbsp;
      <span><img src="<?php echo URL::base() . "images/gps-map-marker.png"; ?>" width="30"  /> Scan GPS Location</span>

      <div style="float: right; margin-left: 10px;" class="export">
        <?php //echo "<pre>"; print_r ($profile); echo "</pre>"; ?>
       <?php /* <a class="link-button" href="<?php echo URL::base() . 'dashboard/2/' . $profile["id"]; ?>">Back</a> */?>
        <a class="link-button"  href="<?php echo URL::base() . 'dashboard/scanalytics/' . $profile["id"]; ?>">Back</a>
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