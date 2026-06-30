<div id="sf_admin_container">
  <div class="dashboard" >
    <h1>Dashboard</h1>
    <table width="100%" class="table">
      <thead>
        <tr class="btn-inverse">
          <th width="95%">Title</th>
          <th width="5%">Count</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($sf_user->hasCredential('admin')): ?>
          <tr>
            <td><?php echo link_to('Client(s)', 'client/index'); ?></td>
            <td><?php echo link_to($userCount, 'client/index'); ?></td>
          </tr>
        <?php endif; ?>
        <tr>
          <td><?php echo link_to('Project(s)', 'project/index'); ?></td>
          <td><?php echo link_to($projectCount, 'project/index'); ?></td>
        </tr>
        <tr>
          <td><?php echo link_to('Item(s)', 'item/index'); ?></td>
          <td><?php echo link_to($itemCount, 'item/index'); ?></td>
        </tr>


      </tbody>
    </table>
  </div>
</div>