<div class="footer-login">
  <h1 id="lgbox">Login</h1>
  <?php echo Form::open(URL::base().'welcome/index#lgbox', array('id'=>'signin','method'=>'post')) ?>
  <div class="row">
    <?php echo Form::label('name', 'Email Address') ?>
	<?php echo Form::input('email', isset($post['email']) ? $post['email'] : '') ?>
    <?php if(isset($errors['eadd'])):?>
    	<span style="color: rgb(255, 0, 0); float: left; width: 100%; display: inline-block; font-size: 12px; padding-top: 2px; height: 14px; line-height: 12px;">
			<?php echo $errors['eadd'];?>
    	</span>
    <?php endif;?>
  </div>
  <div class="row">
    <?php echo Form::label('pass', 'Password') ?>
	<?php echo Form::input('password','',array('type'=>'password')) ?>
    <?php if(isset($errors['pass'])):?>
    	<span style="color: rgb(255, 0, 0); float: left; width: 100%; display: inline-block; font-size: 12px; padding-top: 2px; height: 14px; line-height: 12px;">
			<?php echo $errors['pass'];?>
    	</span>
    <?php endif;?>
  </div>
  <div class="row">
    <div class="login-btn">
      <?php echo Form::submit(NULL, 'Login', array('id'=>'signin_submit')) ?>
    </div>
    <ul class="link fl">
      <li class="last_item"><a href="<?php echo URL::base();?>welcome/contact">Need assistance?</a><a class="ntb" href="<?php echo URL::base();?>welcome/forgot">Forgot?</a></li>
      <?php /*?><li class="last_item"><a href="<?php echo URL::base();?>welcome/contact">Contact us</a></li><?php */?>
    </ul>
  </div>
  <?php echo Form::close() ?>
</div>
<!-- footer-login end -->
