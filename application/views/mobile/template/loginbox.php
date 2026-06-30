<style type="text/css">
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
.login-title{ background:#FFF;
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
.login-box{ background:#fff; padding:10px 20px;;
	-webkit-border-radius: 10px;
	-webkit-border-top-left-radius: 0;
	-moz-border-radius: 10px;
	-moz-border-radius-topleft: 0;
	border-radius: 10px;
	border-top-left-radius: 0;
}
.login-box label {
    color: #656565;
    float: left;
    font-size: 16px;
    padding:7px 0 5px;
}
.login-box input{ border:1px solid #e8e8e8; background:#f6f6f6;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	color:#666;
	text-align:left;
	padding:5px;
	font-size:14px;
	width:95%;
}
.login-box .button, .login-box .button1{
	background: url(../images/btn-bg.png) left top repeat-x;
	text-align:center;
	color:#fff;
	font-size:16px;
	padding:5px 0;
	margin:5px 0;
	cursor:pointer;
	border:1px solid #3189ed;
	width:120px;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
.login-box .button1{ width:170px;}
.forgot-password-box{
 background:#f5f5f5; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; margin:0 10px; width:40%;
}
.assistance{ text-align:right; margin:0; float:right; font-size:13px; color:#6c7277; line-height:18px; padding:12px 5px;}
.assistance a{ text-decoration:underline; color:#6c7277;}

</style>


<div id="lgbox" class="login-title">login</div>
<div class="login-box">
<?php echo Form::open(URL::base().'welcome/index#lgbox', array('id'=>'signin','method'=>'post')) ?>
	<?php echo Form::label('name', 'Email Address') ?>
	<?php echo Form::input('email', isset($post['email']) ? $post['email'] : '') ?>
    <?php if(isset($errors['eadd'])):?>
    	<span style="color: rgb(255, 0, 0); float: left; width: 100%; display: inline-block; font-size: 12px; padding-top: 2px; height: 14px; line-height: 12px;">
			<?php echo $errors['eadd'];?>
    	</span>
    <?php endif;?>
    <?php echo Form::label('pass', 'Password') ?>
	<?php echo Form::input('password','',array('type'=>'password')) ?>
    <?php if(isset($errors['pass'])):?>
    	<span style="color: rgb(255, 0, 0); float: left; width: 100%; display: inline-block; font-size: 12px; padding-top: 2px; height: 14px; line-height: 12px;">
			<?php echo $errors['pass'];?>
    	</span>
    <?php endif;?>
    <?php echo Form::submit(NULL, 'Login', array('id'=>'signin_submit','class'=>'button')) ?>
    <div class="assistance"><a href="#">Need assistance ?</a> | <a href="#">Contact us</a></div>
	<?php echo Form::close() ?>  
</div>
