<div id="sf_admin_container">
  <div class="dashboard">
    <h1>New Item</h1>
  </div>
  <section class="content" > <?php echo form_tag_for($form, 'item/create', array('name' => 'itemForm', 'id' => 'itemForm', 'class' => 'form-horizontal')); ?>
    <div class="control-group"> <?php echo $form['id_project']->renderLabel('Project', array('class' => 'control-label')) ?>
      <div class="controls"> <?php echo $form['id_project']->render(); ?> <?php echo $form['id_project']->renderError() ?> </div>
    </div>
    <div class="control-group"> <?php echo $form['title']->renderLabel('Title', array('class' => 'control-label')) ?>
      <div class="controls"> <?php echo $form['title']->render(); ?> <?php echo $form['title']->renderError() ?> </div>
    </div>
    <div class="control-group">
      <div class="controls"> <?php echo $form->renderHiddenFields() ?>
        <button type="submit" class="btn">Create</button>
        Or
        <button type="button" onclick="document.location.href='<?php echo url_for('item/index'); ?>'" class="btn btn-warning">Cancel</button>
      </div>
    </div>
    <?php echo '</form>'; ?> </section>
</div>
