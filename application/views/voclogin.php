<div >
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <h2 class="page-title-green">VOCC Login</h2>

    
    <?php if(isset($errors)){ ?>
    <div class="message-voc">
        <label class="error"><?php echo $errors; ?></label>
                    <br/><br/>
    </div>
    <?php } 
    
    if(isset($success)){ ?>
    <div class="message-voc">
        <label class="success"><?php echo $success; ?></label>
                    <br/><br/>
    </div>
    <?php } ?>
    
    <?php echo Form::open(URL::base().'voclogin', array('class'=>'', 'id'=>'frmlogin','name'=>'frmlogin','method'=>'post')) ?>
    <div class="green-bg-box">
        <div class="login-bx">
            <div style="float: left;width: 35%">Email</div>
            <div style="float: left;"><input type="text" name="email" id="email" maxlength="255" class="text-fi"></div>
        </div>
        <div class="login-bx">
            <div style="float: left;width: 35%">Password</div>
            <div style="float: left;"><input type="password" name="password" id="password" maxlength="255" class="text-fi"></div>
        </div>
                
    
    </div>
    <div class="voc-forgot-pwd">
        <div class="forgot-text"><a href="<?php echo URL::base();?>voclogin/forgot">Forgot your password?</a></div>
        <div style="float:right;width: 107px;">
            <input type="button" name="submit1" id="submit1" value="Login" class="voc-login-btn">
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <?php echo Form::close() ?>
</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<script>
$(function(){
    $('#submit1').on('click',function(){
        email = $.trim($('#email').val()); 
        password = $.trim($('#password').val()); 
       
        var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
       
        if(email=="")
        {
            alert('Please enter email.');
            return false;
        }
        if (!emailRegexStr.test(email)) 
        {
            alert('Please enter a valid email.');
            return false;
        }
        
        if(password=="")
        {
            alert('Please enter password.');
            return false;
        }
        
        $('#frmlogin').submit();
    });
});
</script>