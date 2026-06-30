<section id="login" >
  <fieldset>
    <legend><span>Login</span></legend>
    <?php if ($form->hasGlobalErrors()): ?>
      <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?php echo $form->renderGlobalErrors() ?> </div>
    <?php endif; ?>
    <?php echo $form->renderFormTag(url_for('auth/login'), array('class' => "form-horizontal")); ?>
    <div class="control-group">
      <?php echo $form['username']->renderLabel('User Name', array('class' => 'control-label')) ?>
      <div class="controls"> <?php echo $form['username']->render() ?> <?php echo $form['username']->renderError() ?> </div>
    </div>
    <div class="control-group">
      <?php echo $form['password']->renderLabel('Password', array('class' => 'control-label')) ?>
      <div class="controls"> <?php echo $form['password']->render() ?> <?php echo $form['password']->renderError() ?> </div>
    </div>
    <div class="controls"> 
      <?php echo $form->renderHiddenFields() ?>
      <button type="submit" class="btn">Login</button>  <a href="<?php echo url_for('auth/forgotpassword') ?>">Forgot Password</a>
    </div>

    </form>
  </fieldset>
</section>
