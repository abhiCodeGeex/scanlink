<script>
  $(document).ready(function(){
    $('.check_all').live('click', function(){
      if($(this).attr('checked') == "checked"){
        $('.checkbox_list').each(function(){
          $(this).attr('checked',true);
        });
      }else{
        $('.checkbox_list').each(function(){
          $(this).attr('checked',false);
        });
      }
    });
		
    /***
    manage action
     **/
    $('.manage_action a').live('click', function(){
      var actionFlag = true;
      $('.checkbox_list').each(function(){
        if($(this).attr('checked') == "checked")
          actionFlag = false;
      });
      if(actionFlag){
        alert("Please select atleast single item to perform this action.");
        return false;
      }
      var action = $(this).attr('rel');
      if(action == "removed"){
        if(!confirm("Are you sure you want to remove all?"))
          return false;
      }
      $('.process').val(action);
      $('#action_form').submit();
    });
		
  });
</script>
<div id="sf_admin_container">
  <div class="dashboard">
    <h1>View Analytics <span><?php echo link_to("Analytics Chart","item/chart?id=".$itemId); ?></span></h1>
  </div>
  <div class="content">
    <form name="action_form" id="action_form" class="action_form" action="<?php echo url_for('item/analyticsaction?itemid='.$itemId.'&page=' . $cpage); ?>" method="post">
      <input type="hidden" name="process" id="process" class="process" />
      <table class="table">
        <thead>
          <tr>
            <th width="5%" >
              <input type="checkbox"  class="check_all"  />
            </th>
            <th>Country</th>
            <th>City</th>
            <th>Device</th>
            <th>Browser</th>
            <th>Platform</th>
            <th width="30%">Options</th>
          </tr>
        </thead>
        <tbody>
          <?php if (sizeof($rows) > 0): ?>
          <?php foreach ($rows as $row): ?>
          <tr>
            <td>
              <input type="checkbox" class="checkbox_list" name="all_ides[]" value="<?php echo $row->getId(); ?>" />
            </td>
            <td> <?php echo $row->getCountryName(); ?> </td>
            <td> <?php echo $row->getCityName(); ?> </td>
            <td> <?php echo $row->getIdDevice() > 0? $row->getAnaDevices()->getDeviceName(): "-"; ?> </td>
            <td> <?php echo $row->getIdBrowser() > 0 ? $row->getAnaBrowsers()->getBrowserName(): "-"; ?> </td>
            <td> <?php echo $row->getIdPlatform() > 0 ? $row->getAnaPlatforms()->getPlatformName(): "-"; ?> </td>
            <td>
              <i class="icon-remove" ></i> <?php echo link_to("Remove", "item/analyticsremove?itemid=".$itemId."&id=" . $row->getId() . "&page=" . $cpage, array('class' => 'special_link', 'confirm' => 'Are you sure?', 'absolute' => true)); ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="2">
              <div class="btn-group"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> Action <span class="caret"></span> </a>
                <ul class="dropdown-menu manage_action"  >
                  <li><a href="javascript:void(0);" rel="removed">Remove</a></li>
                </ul>
              </div>
            </td>
            <td colspan="5">
              <div class="pagination">
                <?php if ($pager->haveToPaginate()) : ?>
                <ul>
                  <li><?php echo link_to('← Prev', 'item/showanalytics?id='.$itemId.'&page=' . $pager->getPreviousPage(), '') ?></li>
                  <?php $links = $pager->getLinks(); ?>
                  <?php foreach ($links as $page): ?>
                  <?php $class = ""; ?>
                  <?php if ($page == $pager->getPage()): ?>
                  <?php $class = "active"; ?>
                  <?php endif; ?>
                  <li class="<?php echo $class; ?>" ><?php echo link_to($page, 'item/showanalytics?id='.$itemId.'&page=' . $page, 'class="current"'); ?></li>
                  <?php endforeach ?>
                  <li><?php echo link_to('Next →', 'item/showanalytics?id='.$itemId.'&page=' . $pager->getNextPage(), '') ?></li>
                </ul>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php else: ?>
          <tr>
            <td colspan="7"> No record(s) found. </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </form>
  </div>
</div>
