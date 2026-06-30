<div class="title">
  <p>If you want to recover your forgotten password, please fill out your email address here and we will send you a copy of your password via email. Thank you.</p>
</div>
<div class="content">
  <div class="simple-rounded-box">
    <div class="title"><h1 style="padding-left:10px;">Forgot Password</h1></div>
      <?php if(isset($errors) && $errors !=''){ ?>
       		<div class="error top"><?php echo $errors; ?></div>
            <?php } ?>
            <?php if(isset($success) && $success !=''){ ?>
       		<div class="error top smsg"><?php echo $success; ?></div>
            <?php } ?>
    <form style="padding-left:10px;" method="post" id="frmforgot" name="frmforgot" action="">
      <ol class="forms">
        <li>
          <label for="companyName">Email Address:</label>
          <input type="text" value="" id="txtemail" name="txtemail">
        </li>        
      </ol>
      <div class="clear"></div>
      <div class="form-button-left">
        <input type="submit" value="Recover Password" name="btnforgot" class="save" id="btnforgot">
      </div>
      <div class="clear"></div>
    </form>
  </div>
</div>
