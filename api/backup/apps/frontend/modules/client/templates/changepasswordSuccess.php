<?php //echo $form; ?>
<div id="sf_admin_container">
	<div class="dashboard">
    <h1>Profile</h1>
  </div>
  <section class="content">
	<form class='form-horizontal' action="#">
  	<div class="control-group">
    		<label class="control-label">Username</label>
        <div class="controls">
          <input type="text"  value="<?php echo $userdetail->getUsername(); ?>" disabled="disabled" />
        </div>
    </div>
    <div class="control-group">
    		<label class="control-label">Email</label>
        <div class="controls">
          <input type="text"  value="<?php echo $userdetail->getEmail(); ?>" disabled="disabled" />
        </div>
    </div>
  </form>
  </section>
  <div class="dashboard">
    <h1>Change Password</h1>
  </div>
  <section class="content">

    <?php if ($changePasswordForm->hasGlobalErrors()): ?>
    <?php echo $changePasswordForm->renderGlobalErrors() ?>
    <?php endif; ?>
    <?php echo $changePasswordForm->renderFormTag(url_for('client/SavePassword'), array("id" => "changePassword", "name" => "changePassword" ,'class' => 'form-horizontal')); ?>
    <div class="control-group">
        <?php echo $changePasswordForm['password']->renderLabel('Password', array('class' => 'control-label')) ?>
        <div class="controls">
          <?php echo $changePasswordForm['password']->render(); ?>
          <?php echo $changePasswordForm['password']->renderError() ?>
        </div>
    </div>
    
    <div class="control-group">
        <?php echo $changePasswordForm['new_password']->renderLabel('New Password', array('class' => 'control-label')) ?>
        <div class="controls">
          <?php echo $changePasswordForm['new_password']->render(); ?>
          <?php echo $changePasswordForm['new_password']->renderError() ?>
        </div>
    </div>
    
      <div class="control-group">
        <?php echo $changePasswordForm['confirm_password']->renderLabel('Confirm Password', array('class' => 'control-label')) ?>
        <div class="controls">
          <?php echo $changePasswordForm['confirm_password']->render(); ?>
          <?php echo $changePasswordForm['confirm_password']->renderError() ?>
        </div>
    </div>
    <?php echo $changePasswordForm->renderHiddenFields() ?>
    <button type="submit" class="btn">Change Password</button>
     <?php echo '</form>'; ?> 
</section>    