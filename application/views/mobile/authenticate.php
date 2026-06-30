<style type="text/css">
  body
  {
      background: transparent !important;
  }
  #container{ padding:10px !important; background-image:none; }
  .login-gred {
    float: left;
    padding: 10px;
    width: 390px;
  }
  .login-gred h2.title{
    color: #CCCCCC;
    font-size: 20px;
    font-weight: bold;
    padding: 4px 0px 0px 0px;
  }
  .login-title{ 
                background:#e9e9e9;
                -webkit-border-top-left-radius: 10px;
                -webkit-border-top-right-radius: 10px;
                -moz-border-radius-topleft: 10px;
                -moz-border-radius-topright: 10px;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                width:100px;
                text-align:center;
                color:#000;
                font-size:18px;
                padding:7px 5px;
  }
  .login-box-mobile{ background:#e9e9e9; padding:10px 20px;;
                     -webkit-border-radius: 10px;
                     -webkit-border-top-left-radius: 0;
                     -moz-border-radius: 10px;
                     -moz-border-radius-topleft: 0;
                     border-radius: 10px;
                     border-top-left-radius: 0;
  }
  .login-box-mobile label {
    color: #656565;
    float: left;
    font-size: 16px;
    padding:7px 0 5px;
  }
  .login-box-mobile input{ border:1px solid #cccccc; background:#ffffff !important;
                           -webkit-border-radius: 5px;
                           -moz-border-radius: 5px;
                           border-radius: 5px;
                           color:#666;
                           text-align:left;
                           padding:5px;
                           font-size:14px;
                           width:95%;
                           box-shadow: none !important;
  }
  .login-box-mobile .button, .login-box-mobile .button1{
    width:120px;
  }
  .login-box-mobile .button1{ width:170px;}
  .forgot-password-box{
    background:#f5f5f5; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; margin:0 10px; width:40%;
  }
  .assistance{ text-align:right; margin:0; float:right; font-size:13px; color:#6c7277; line-height:18px; padding:12px 5px;}
  .assistance a{ text-decoration:underline; color:#6c7277;}

</style>


<div id="lgbox" class="login-title">login</div>
<div class="login-box-mobile">
  <?php echo Form::open(URL::base() . 'mobile/login', array('id' => 'signin', 'method' => 'post')); ?>

  <input type="hidden" name="profile" id="profile" value="<?php
  if (isset($errors) && isset($errors['profile'])) {
    echo $errors['profile'];
  } else {
    echo $profile;
  }
  ?>" />
         <?php echo Form::label('pass', 'Password:&nbsp;') ?>
    <?php echo Form::input('password', '', array('type' => 'password', 'class' => 'text-fi')) ?>
    <?php if (isset($errors['pass'])): ?>
    <span style="color: rgb(255, 0, 0); float: left; width: 100%; display: inline-block; font-size: 12px; padding-top: 2px; height: 14px; line-height: 12px;">
    <?php echo $errors['pass']; ?>
    </span>
  <?php endif; ?>
  <div>&nbsp;</div>
  <input type="hidden" name="profile_id" value="<?php echo $profile_id ?>" />
  <?php //echo Form::submit(NULL, 'Login', array('id' => 'signin_submit', 'class' => 'button')) 
  echo Form::submit(NULL, 'Login', array('id' => 'signin_submit', 'class' => 'submit-btn')) ?>
  
  
  
  <!--<div class="assistance"><a href="<?php echo url::base() . "welcome/contact" ?>">Need assistance ?</a> | <a href="<?php echo url::base() . "welcome/contact" ?>">Contact us</a></div>-->
<?php echo Form::close(); ?>  
</div>
