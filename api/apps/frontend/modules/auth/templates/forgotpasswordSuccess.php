<section id="login" >
  <fieldset>
    <legend><span>Forgot Password</span></legend>
    <?php if ($form->hasGlobalErrors()): ?>
      <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo $form->renderGlobalErrors() ?> </div>
    <?php endif; ?>
    <?php echo $form->renderFormTag(url_for('auth/forgotpassword'), array('class' => "form-horizontal")); ?>
    <div class="control-group">
      <?php echo $form['email']->renderLabel('Email', array('class' => 'control-label')) ?>
      <div class="controls"> <?php echo $form['email']->render() ?> <?php echo $form['email']->renderError() ?> </div>
    </div>
    <div class="control-group">
      <div class="controls">           
        <?php echo $form->renderHiddenFields() ?>
        <button type="submit" class="btn">Get Password</button> <a href="<?php echo url_for('auth/login') ?>">Back To Login</a>
      </div>
    </div>       
    </form>
  </fieldset>
</section>