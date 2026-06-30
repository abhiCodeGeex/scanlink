<div class="container">
  <div class="slider"><img src="<?php echo URL::base();?>images/banner-im.jpg" alt="" /></div>
  <div class="contant">
    <h1>Welcome to ScanLink</h1>
    <table align="center" width="650" cellspacing="15" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td align="center" nowrap="" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="55%" valign="top"><!--col002--> 
        <!--padding-->
        
        <table width="100%" cellspacing="0" cellpadding="2" border="0">
          <tbody>
          <?php if(isset($errors) && $errors != ''): ?>
           <div style="border:solid 2px #f00;width:312px;margin:0 auto;padding:7px 10px 7px 10px;border-radius:6px;-moz-border-radius:6px;-webkit-border-radius:6px;color:#f00;font-weight:bold;text-align:center;margin-bottom:10px;background:url(images/error-icon.png) no-repeat 15px 3px;font-size:12px;"><!--<img src="images/error-icon.png" style="height:10px;" />--><?php echo $errors;?></div>
            <?php endif ?>
            <tr>
              <td align="center"><div class="common-form standard-form offscreen" id="signin_menu">
                  <?php echo Form::open(URL::base().'welcome/login', array('id'=>'signin','method'=>'post')) ?>
                    <p class="textbox">
                      <?php echo Form::label('name', 'Email Address') ?>
                      <?php echo Form::input('email', $post ? $post['email'] : '') ?>
                    </p>
                    <p class="textbox">
                      <?php echo Form::label('pass', 'Password') ?>
                      <?php echo Form::input('password','',array('type'=>'password')) ?>
                    </p>
                    <p class="remember">
                      <?php echo Form::submit(NULL, 'Sign in', array('id'=>'signin_submit')) ?>
                    </p>
                  <?php echo Form::close() ?>
                </div></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>
    
  </div>
</div>
