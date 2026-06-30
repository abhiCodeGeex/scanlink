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
    <h1>Manage Items </h1>
  </div>
  <div class="content">
    <form name="action_form" id="action_form" class="action_form" action="<?php echo url_for('item/action?page=' . $cpage); ?>" method="post">
      <input type="hidden" name="process" id="process" class="process" />
      <table class="table">
        <thead>
          <tr>
            <th width="5%" >
              <input type="checkbox"  class="check_all"  />
            </th>
            <th width="25%" >Url</th>
            <th width="20%" >Project</th>
            <th width="20%" >No. Of Clicks</th>
            <th width="30%">Options</th>
          </tr>
        </thead>
        <tbody>
          <?php if (sizeof($items) > 0): ?>
            <?php foreach ($items as $item): ?>
              <tr>
                <td>
                  <input type="checkbox" class="checkbox_list" name="all_ides[]" value="<?php echo $item->getId(); ?>" />
                </td>
                <td> <span title="<?php echo $item->getUrl(); ?>"> <?php echo substr($item->getUrl(), 0 , 20).'...'; ?> </span></td>
                <td> <?php echo $item->getAnaProjects()->getProjectName(); ?> </td>
                <td class="td-right" >  <?php echo $item->getNoOfClicks(); ?></td>
                <td>
                  <i class="icon-remove" ></i> <?php echo link_to("Remove", "item/remove?id=" . $item->getId() . "&page=" . $cpage, array('class' => 'special_link', 'confirm' => 'Are you sure?', 'absolute' => true)); ?> <i class="icon-signal" ></i> <?php echo link_to("Analytics", "item/showanalytics?id=" . $item->getId() . "&page=" . $cpage, array('class' => 'special_link', 'absolute' => true)); ?>
                  <i class="icon-tasks" ></i> <?php echo link_to("View Chart", "item/chart?id=" . $item->getId()); ?>
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
              <td colspan="3">
                <div class="pagination">
                  <?php if ($pager->haveToPaginate()) : ?>
                    <ul>
                      <li><?php echo link_to('← Prev', 'item/index?page=' . $pager->getPreviousPage(), '') ?></li>
                      <?php $links = $pager->getLinks(); ?>
                      <?php foreach ($links as $page): ?>
                        <?php $class = ""; ?>
                        <?php if ($page == $pager->getPage()): ?>
                          <?php $class = "active"; ?>
                        <?php endif; ?>
                        <li class="<?php echo $class; ?>" ><?php echo link_to($page, 'item/index?page=' . $page, 'class="current"'); ?></li>
                      <?php endforeach ?>
                      <li><?php echo link_to('Next →', 'item/index?page=' . $pager->getNextPage(), '') ?></li>
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
