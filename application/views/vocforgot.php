<div >
    <div>&nbsp;</div>
    <div>&nbsp;</div>
    <h2 class="page-title-green">VOCC Forgot Password</h2>

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
    
    <?php echo Form::open(URL::base().'voclogin/forgot', array('class'=>'', 'id'=>'frmlogin','name'=>'frmlogin','method'=>'post')) ?>
    <div class="green-bg-box forgot">
        <div class="login-bx">
            <div style="float: left;width: 35%">Email</div>
            <div style="float: left;"><input type="text" name="email" maxlength="255" id="email" class="text-fi"></div>
        </div>
    </div>
    <div class="voc-forgot-pwd">
        <div class="forgot-text"><a href="<?php echo URL::base();?>voclogin">VOCC Login</a></div>
        <div style="float:right;width: 107px;">
            <input type="button" name="submit1" id="submit1" value="Submit" class="voc-login-btn">
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
        
        $('#frmlogin').submit();
    });
});
</script>
