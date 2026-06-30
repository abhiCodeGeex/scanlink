<div id="sf_admin_container">
  <div class="dashboard">
    <h1>New Project</h1>
  </div>
  <section class="content" > <?php echo form_tag_for($form, url_for('project/update?id='.$project->getId()), array('name' => 'projectForm', 'id' => 'projectForm', 'class' => 'form-horizontal')); ?>
    <?php if ($sf_user->hasCredential('admin')): ?>
    	<div class="control-group">
      <?php echo $form['id_user']->renderLabel('Project Owner', array('class' => 'control-label')) ?> 
      <div class="controls">
        <?php echo $form['id_user']->render(); ?>
        <?php echo $form['id_user']->renderError() ?>
      </div>
    </div>
    <?php endif ; ?>
    <div class="control-group">
      <?php echo $form['project_name']->renderLabel('Project Name', array('class' => 'control-label')) ?> 
      <div class="controls"> 
        <?php echo $form['project_name']->render(); ?> 
        <?php echo $form['project_name']->renderError() ?> 
      </div>
    </div>

    <div class="control-group">
      <?php echo $form['description']->renderLabel('Project Description', array('class' => 'control-label')) ?> 
      <div class="controls"> 
        <?php echo $form['description']->render(); ?> 
        <?php echo $form['description']->renderError() ?> 
      </div>
    </div>
    <div class="control-group">
      <?php echo $form['url']->renderLabel('Project URL', array('class' => 'control-label')) ?> 
      <div class="controls"> 
        <?php echo $form['url']->render(); ?> 
        <?php echo $form['url']->renderError() ?> 
      </div>
    </div>
    <div class="control-group">
      <div class="controls">
        <?php echo $form->renderHiddenFields() ?>
        <button type="submit" class="btn">Update</button> Or <button type="button" onclick="document.location.href='<?php echo url_for('project/index'); ?>'" class="btn btn-warning">Cancel</button>
      </div>
    </div>
    <?php echo '</form>'; ?> </section>
</div>
