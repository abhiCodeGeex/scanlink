<div id="sf_admin_container">
  <div class="dashboard">
    <h1>New Client</h1>
  </div>
  <section class="content" > <?php echo form_tag_for($form, 'client/create', array('name' => 'client', 'id' => 'client', 'class' => 'form-horizontal')); ?>
    <div class="control-group">
      <?php echo $form['first_name']->renderLabel('First Name', array('class' => 'control-label')) ?> 
      <div class="controls"> <?php echo $form['first_name']->render(); ?> <?php echo $form['first_name']->renderError() ?> </div>
    </div>
    <div class="control-group">
      <?php echo $form['last_name']->renderLabel('Last Name', array('class' => 'control-label')) ?> 
      <div class="controls"> 
					<?php echo $form['last_name']->render(); ?> 
					<?php echo $form['last_name']->renderError() ?> 
      </div>
    </div>
    <div class="control-group">
      <?php echo $form['email']->renderLabel('Email', array('class' => 'control-label')) ?> 
      <div class="controls"> 
					<?php echo $form['email']->render(); ?> 
					<?php echo $form['email']->renderError() ?> 
      </div>
    </div>
    <div class="control-group">
      <?php echo $form['username']->renderLabel('Username', array('class' => 'control-label')) ?> 
      <div class="controls"> 
					<?php echo $form['username']->render(); ?> 
					<?php echo $form['username']->renderError() ?> 
      </div>
    </div>
    <div class="control-group">
      <?php echo $form['password']->renderLabel('Password', array('class' => 'control-label')) ?> 
      <div class="controls"> 
					<?php echo $form['password']->render(); ?> 
					<?php echo $form['password']->renderError() ?> 
      </div>
    </div>
    <div class="control-group">
     <?php echo $form['confirm_password']->renderLabel('Confirm Password', array('class' => 'control-label')) ?>  
      <div class="controls"> 
					<?php echo $form['confirm_password']->render(); ?> 
					<?php echo $form['confirm_password']->renderError() ?> 
      </div>
    </div>
    <div class="control-group">
      <div class="controls">
      <?php echo $form->renderHiddenFields() ?>
        <button type="submit" class="btn">Create</button> Or <button type="button" onclick="document.location.href='<?php echo url_for('client/index');?>'" class="btn btn-warning">Cancel</button>
      </div>
    </div>
    <?php echo '</form>'; ?> </section>
</div>
