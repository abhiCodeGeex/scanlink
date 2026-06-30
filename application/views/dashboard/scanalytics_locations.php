<?php
if (!$expried_or_not) {
?>

  <link type="text/css" href="<?php echo URL::base() . 'siteadmin/styles/site.css' ?>" rel="stylesheet" media="screen" />
  <style>
    .row11 {
      width: 260px !important;
    }

    #uniform-sortby span {
      text-align: left;
    }
  </style>
  <script>
    //$(function(){
    //$('select').uniform();
    //});
  </script>
<?php } ?>
<p><?php /* This is the Dashboard Manager Area. You can manage your profile(s) here. */ ?></p>
<?php echo $innermenu; ?>
<?php
if (!$expried_or_not) {
?>
  <div class="my-qr-codes">
    <div class="title">
      <div class="fright-60">
        <div class="title-select">
          <form action="" method="post" name="sortby_form" id="sortby_form">
            <?php
            $analyticsby = array('allTime' => 'All Time', 'month' => 'Monthly', 'week' => 'Weekly', 'day' => 'Daily', 'twoHours' => 'Last Two Hours');
            ?>
            <h3><?php echo 'Profile ' . $profile['id'] . '. ' . ucwords($profile['code_profile_name']); ?></h3>
            <div class="export" style="float: left; width: 150px;">
              <a class="link-button" href="<?php echo URL::base() . 'dashboard/scanalytics/' . $profile["id"]; ?>">Back</a>
            </div>
            <ul class="form-view">

              <div class="" style="float: left; width: 670px; text-align: right; padding-top: 7px;">
                <select name="sortby" id="sortby" onchange="sortby_form.submit();" class="text-fi">
                  <option value="">--Sort By--</option>
                  <option value="city_name" <?php echo $orderby == "city_name" ? "selected='selected'" : ""; ?>> Location </option>
                  <option value="created_at" <?php echo $orderby == "created_at" ? "selected='selected'" : ""; ?>> Scan Order </option>
                </select>
              </div>
            </ul>
            <div class="export" style="float: right; width: 150px;">
              <a class="link-button" href="<?php echo URL::base() . 'dashboard/scanalytics_export/' . $profile["id"] . '/' . $orderby; ?>">Export Analytics</a>
            </div>
          </form>
        </div>
        <div class="clear">&nbsp;</div>
      </div>
      <!-- /fright -->
    </div>
    <!-- remain design start -->

    <!-- remain design end-->
    <div class="listing">
      <table width="100%" cellpadding="0" cellspacing="0" class="listing-table" id="table">
        <thead>
          <tr>
            <th class="left-round" align="center"><strong>Sr No</strong></th>
            <th><strong class="txt-left">Date Time</strong></th>
            <th><strong class="txt-left">Ip address</strong></th>
            <th><strong class="txt-left">Location</strong></th>
            <th align="center"><strong class="txt-left">Coordinates</strong></th>
            <th align="center"><strong class="txt-left">Devics Name</strong></th>
            <th align="center"><strong class="txt-left">Platform Name</strong></th>
            <th align="center"><strong class="txt-left">Screen Size</strong></th>
            <th align="center"><strong class="txt-left">Browser Name</strong></th>
            <th align="center"><strong class="txt-left">Browser Version</strong></th>
            <th><strong class="txt-left">Scan Type</strong></th>
            <th class="right-round"><strong class="txt-left">OPTIONS</strong></th>
          </tr>
        </thead>
        <tbody id="records">

          <?php
          $i = 1;

          $results = DB::select('*')->from('ana_item_analytics')
            ->where('id_item', '=', $profile["id"])
            ->order_by('id', 'desc')
            ->execute()
            ->as_array();
          if ($results == 0 || $results == "Null") {
            echo 'No record available profile id=' . $profile["id"];
          } else {
            foreach ($results as $row) { //if($i==0)

              //print_r($analytics);

          ?>

              <tr>
                <td style="font-size: 12px;"><?php echo $i;
                                              $i++; ?></td>
                <td style="font-size: 12px;"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?> </td>
                <td style="font-size: 12px;"><?php echo $row['ip_add']; ?></td>
                <td style="font-size: 12px;"><?php echo $row['city_name']; ?>,<?php echo $row['region_name']; ?>,<?php echo $row['country_name']; ?> </td>
                <td style="font-size: 12px;"><?php echo $row['latitude'] . ', ' . $row['longitude']; ?></td>
                <td style="font-size: 12px;"><?php //echo $row['id_platform'] ;

                                              $resultdevics = DB::select('*')->from('ana_platforms')
                                                ->where('id', '=', $row["id_platform"])
                                                ->execute();
                                              foreach ($resultdevics as $rowdevics) {
                                                echo $rowdevics["platform_name"];
                                              }

                                              ?></td>
                <td style="font-size: 12px;"><?php //echo $row['id_device'] ;

                                              $resultplatform = DB::select('*')->from('ana_devices')
                                                ->where('id', '=', $row["id_device"])
                                                ->execute();
                                              foreach ($resultplatform as $paltform) {
                                                echo $paltform["device_name"];
                                              }

                                              ?></td>
                <td style="font-size: 12px;"><?php echo $row['screen_size']; ?></td>
                <td style="font-size: 12px;"><?php //echo $row['id_browser'] ;
                                              if (is_numeric($row["id_browser"])) { //echo $
                                                $resultbrower = DB::select('*')->from('ana_browsers')
                                                  ->where('id', '=', $row["id_browser"])
                                                  ->execute();
                                                foreach ($resultbrower as $browser) {
                                                  echo $browser["browser_name"];
                                                }
                                              } else {
                                                echo $row["id_browser"];
                                              }

                                              ?></td>
                <td style="font-size: 12px;"><?php echo $row['browser_version']; ?></td>
                <td style="font-size: 12px;"><?php echo $row['scan_type'] == "gps" ? "GPS" : "IP"; ?></td>
                <td style="font-size: 12px;"> <a style="font-size: 12px;" href="<?php echo URL::base() . 'dashboard/scanalytics_showlocation/' . $profile["id"] . "/" . $row['id']; ?>">Show on map</a></td>
              </tr>
          <?php
            }
          } ?>
          <!--<tr>
            <td colspan="6">No record available.</td>
          </tr>-->

        </tbody>
      </table>
    </div>
  </div>
  <script>
    $(document).ready(function() {
      //getResults(1, '');
      $(function() {
        itemsPerPage = 50;
        paginatorStyle = 12;
        paginatorPosition = 'bottom';
        enableGoToPage = false;
        showIfSinglePage = false;
        $("#records").pagination();
        $(".paginator").parent().attr("colspan", '12');
      });
    });
  </script>
<?php } else { ?>
  <p>You can not perform this action on expired profile.</p>
<?php } ?>