<div id="sf_admin_container">
  <div class="dashboard">
    <h1>New Browser</h1>
  </div>
  <section class="content" > <?php echo form_tag_for($form, 'browser/create', array('name' => 'browserForm', 'id' => 'browserForm', 'class' => 'form-horizontal')); ?>
    <div class="control-group"> <?php echo $form['browser_name']->renderLabel('Browser Name', array('class' => 'control-label')) ?>
      <div class="controls"> <?php echo $form['browser_name']->render(); ?> <?php echo $form['browser_name']->renderError() ?> </div>
    </div>
    <div class="control-group">
      <div class="controls"> <?php echo $form->renderHiddenFields() ?>
        <button type="submit" class="btn">Create</button>
        Or
        <button type="button" onclick="document.location.href='<?php echo url_for('browser/index'); ?>'" class="btn btn-warning">Cancel</button>
      </div>
    </div>
    <?php echo '</form>'; ?> </section>
</div>
