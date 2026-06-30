<style type="text/css">
  .link1 {
    font-size: 15px;
    font-weight: bold;
    color: #6E9113;
  }
</style>
<?php echo $innermenu; ?>
<h3>Visitor Log</h3>
<div class="contentpart">
  <div class="my-qr-codes"> 
    <!-- remain design part start-->
    <div style="float: left">
      <h3 style="margin: 0px;"><?php echo 'Profile ' . $profile['id'] . '. ' . ucwords($profile['code_profile_name']); ?></h3>
    </div>
    <div class="btn-inline" style="float: right;">
      <ul class="scananalytic-buttons">
        <li><a class="link-button" href="<?php echo URL::base() . 'dashboard/datacollection_export/' . $profile['id'] ?>">Export</a></li>
        <li><a class="link-button" href="javascript:;" onclick="history.go(-1);return false;">Return to list</a></li>
      </ul>
    </div>
    <!-- remain design part end--->
    <div class="clear"></div>
    <div class="All"> 
      <!-- /title -->
      <div class="clear"></div>
      <div id="switcher" style="position:relative; height:5%; width:100%;"></div>
      <br/>
      <div class="clear"></div>
      <table width="100%" cellspacing="0" cellpadding="0" id="table" class="listing-table">
        <thead>
          <tr>
            <th width="20%" align="center" class="left-round"><strong>Date</strong></th>
            <th width="20%" align="left"><strong>Name</strong></th>
            <th width="20%" align="left"><strong>Surname</strong></th>
            <th width="20%" align="left"><strong>Mobile</strong></th>
            <th width="20%" align="left" class="right-round"><strong>Email</strong></th>
          </tr>
        </thead>
        <tbody id="records">
          <?php if (isset($contacts) && is_array($contacts) && count($contacts)): ?>
            <?php foreach ($contacts as $contact): ?>
              <tr>
                <td align="center" class="left-border"><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></td>
                <td><?php echo stripslashes($contact['name']); ?></td>
                <td><?php echo stripslashes($contact['surname']); ?></td>
                <td><?php echo stripslashes($contact['mobile']); ?></td>
                <td class="right-border"><?php echo stripslashes($contact['email']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>    
            <tr><td colspan="5" align="center"><b>No records found</b></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>