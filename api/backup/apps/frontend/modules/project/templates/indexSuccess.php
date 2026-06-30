<script>
  $(document).ready(function(){
    $('.check_all').live('click', function(){
      if($(this).attr('checked') == "checked"){
        $('.project_checkbox').each(function(){
          $(this).attr('checked',true);
        });
      }else{
        $('.project_checkbox').each(function(){
          $(this).attr('checked',false);
        });
      }
    });
		
    /***
    manage action
     **/
    $('.manage_action a').live('click', function(){
      var actionFlag = true;
      $('.project_checkbox').each(function(){
        if($(this).attr('checked') == "checked")
          actionFlag = false;
      });
      if(actionFlag){
        alert("Please select atleast single project to perform this action.");
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
    <h1>Manage project<span> <i class="icon-plus-sign"></i> <?php echo link_to("Create", "project/new"); ?></span></h1>
  </div>

  <div class="content">
    <form name="action_form" id="action_form" class="action_form" action="<?php echo url_for('project/action?page=' . $cpage); ?>" method="post">
      <input type="hidden" name="process" id="process" class="process" />
      <table class="table">
        <thead>
          <tr>
            <th width="2%" >
              <input type="checkbox"  class="check_all"  />
            </th>
            <th>Project Name</th>
            <th>Url</th>
            <?php if ($sf_user->hasCredential('admin')): ?>
              <th>Project Owner</th>
            <?php endif; ?>
            <th width="25%">Options</th>
          </tr>
        </thead>
        <tbody>
          <?php
          //print_r($projects->toArray());
          if (sizeof($projects) > 0):
            ?>
            <?php foreach ($projects as $project): ?>
              <tr>
                <td>
                  <input type="checkbox" class="project_checkbox" name="projects[]" value="<?php echo $project->getId(); ?>" />
                </td>
                <td> <?php echo $project->getProjectName(); ?> </td>
                <td> <?php echo $project->getUrl(); ?> </td>
                <?php if ($sf_user->hasCredential('admin')): ?>
                  <td> <?php echo $project->getAnaUsers()->getFirstName(); ?> </td>
                <?php endif; ?>
                <td> <i class="icon-file" ></i> <?php echo link_to("Edit", "project/edit?id=" . $project->getId()); ?> | <i class="icon-remove" ></i> <?php echo link_to("Remove", "project/remove?id=" . $project->getId() . "&page=" . $cpage, array('class' => 'special_link', 'confirm' => 'Are you sure?', 'absolute' => true)); ?> |
                  <?php if ($project->getStatus() == 1): ?>
                    <i class="icon-thumbs-up"></i> <?php echo link_to("Active", "project/changeStatus?id=" . $project->getId() . "&status=0&page=" . $cpage); ?>
                  <?php else: ?>
                    <i class="icon-thumbs-down"></i> <?php echo link_to("Inactive", "project/changeStatus?id=" . $project->getId() . "&status=1&page=" . $cpage); ?>
                  <?php endif; ?>
              </tr>
            <?php endforeach; ?>
            <tr>
              <td colspan="2">
                <div class="btn-group"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> Action <span class="caret"></span> </a>
                  <ul class="dropdown-menu manage_action"  >
                    <li><a href="javascript:void(0);" rel="actived" >Active</a></li>
                    <li><a href="javascript:void(0);" rel="inactived" >Inctive</a></li>
                    <li><a href="javascript:void(0);" rel="removed">Remove</a></li>
                  </ul>
                </div>
              </td>
              <td colspan="3">
                <div class="pagination">
                  <?php if ($pager->haveToPaginate()) : ?>
                    <ul>
                      <li><?php echo link_to('← Prev', 'project/index?page=' . $pager->getPreviousPage(), '') ?></li>
                      <?php $links = $pager->getLinks(); ?>
                      <?php foreach ($links as $page): ?>
                        <?php $class = ""; ?>
                        <?php if ($page == $pager->getPage()): ?>
                          <?php $class = "active"; ?>
                        <?php endif; ?>
                        <li class="<?php echo $class; ?>" ><?php echo link_to($page, 'project/index?page=' . $page, 'class="current"'); ?></li>
                      <?php endforeach ?>
                      <li><?php echo link_to('Next →', 'project/index?page=' . $pager->getNextPage(), '') ?></li>
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
