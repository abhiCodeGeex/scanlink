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
    <h1>Last Login Report<span>
      <?php if ($sf_user->hasCredential('admin')): ?>
      <div class="input-append">
        <form>
          <input class="span2" id="search" name="search" type="text" value="<?php echo $search; ?>">
          <button class="btn" type="submit">Search</button>
          <button class="btn" onclick="document.location.href='<?php echo url_for('client/loginreport?page=' . $cpage); ?>'" type="reset">Clear</button>
        </form>
      </div>
      <?php endif; ?>
      </span></h1>
  </div>
  <div class="content">
    <form name="action_form" id="action_form" class="action_form" action="<?php echo url_for('client/reportaction?page=' . $cpage); ?>" method="post">
      <input type="hidden" name="process" id="process" class="process" />
      <table class="table">
        <thead>
          <tr>
            <?php if ($sf_user->hasCredential('admin')): ?>
            <th width="10%" >
              <input type="checkbox"  class="check_all"  />
            </th>
            <?php endif; ?>
            <th width="40%" >Ip</th>
            <th width="20%" >User</th>
            <th width="20%" >Login Date</th>
            <?php if ($sf_user->hasCredential('admin')): ?>
            <th width="30%">Options</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if (sizeof($reports) > 0): ?>
          <?php foreach ($reports as $report): ?>
          <tr>
            <?php if ($sf_user->hasCredential('admin')): ?>
            <td>
              <input type="checkbox" class="checkbox_list" name="all_ides[]" value="<?php echo $report->getId(); ?>" />
            </td>
            <?php endif; ?>
            <td> <?php echo $report->getIpAdd(); ?> </td>
            <td> <?php echo $report->getAnaUsers()->getUsername(); ?> </td>
            <td> <?php echo date('d-M, Y H:i:s',strtotime($report->getLoginTime())); ?></td>
            <?php if ($sf_user->hasCredential('admin')): ?>
            <td> <i class="icon-remove" ></i> <?php echo link_to("Remove", "client/removereport?id=" . $report->getId() . "&page=" . $cpage, array('class' => 'special_link', 'confirm' => 'Are you sure?', 'absolute' => true)); ?> </td>
            <?php endif; ?>
          </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="2">
              <?php if ($sf_user->hasCredential('admin')): ?>
              <div class="btn-group"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> Action <span class="caret"></span> </a>
                <ul class="dropdown-menu manage_action"  >
                  <li><a href="javascript:void(0);" rel="removed">Remove</a></li>
                </ul>
              </div>
              <?php endif; ?>
            </td>
            <td colspan="3">
              <div class="pagination">
                <?php if ($pager->haveToPaginate()) : ?>
                <ul>
                  <li><?php echo link_to('← Prev', 'client/loginreport?page=' . $pager->getPreviousPage(), '') ?></li>
                  <?php $links = $pager->getLinks(); ?>
                  <?php foreach ($links as $page): ?>
                  <?php $class = ""; ?>
                  <?php if ($page == $pager->getPage()): ?>
                  <?php $class = "active"; ?>
                  <?php endif; ?>
                  <li class="<?php echo $class; ?>" ><?php echo link_to($page, 'client/loginreport?page=' . $page, 'class="current"'); ?></li>
                  <?php endforeach ?>
                  <li><?php echo link_to('Next →', 'client/loginreport?page=' . $pager->getNextPage(), '') ?></li>
                </ul>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php else: ?>
          <tr>
            <td colspan="5"> No record(s) found. </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </form>
  </div>
</div>
