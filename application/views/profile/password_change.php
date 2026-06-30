<link type="text/css" href="<?php echo URL::base() ?>/styles/style.css" rel="stylesheet" media="screen" />

<?php
echo Form::open(
   URL::base() . 'passwordchange/update_password',
   array(
      'id' => 'frmchangepassword',
      'name' => 'frmchangepassword',
      'method' => 'post',
      'enctype' => 'multipart/form-data',
      'onsubmit' => 'javascript:return changepasswordValidation();'
   )
)
?>
<?php echo Form::input(
   'editprofile',
   'no',
   array('id' => 'editprofile', 'class' => 'text-fi', 'type' => 'hidden')
); ?>
<section class="add-form-left-register">
   <!-- ADD FORM LEFT START -->
   <div class="SectionTitleBox-orderSummary">
      <h3 style="color:red">Please change password and continue work</h3>
      <!-- <div class="SectionTitleRegister">Change Password</div> -->
      <?php
      if (isset($pwdsuccessmessage)) {
      ?>
         <div class="row-r"><label class="success"><?php echo $pwdsuccessmessage; ?></label></div>
         <div class="clear">&nbsp;</div>
      <?php
      }
      ?>

      <div id="progressbar"> <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait.</div>
      <input type="hidden" name="passsword_change" value="1">
      <ul class="form-view clearfix">
         <li class="registerli">
            <label>Old password:</label>
            <?php echo Form::input(
               'txtpass',
               '',
               array('id' => 'txtpass', 'class' => 'text-fi registertextwidth', 'type' => 'password', 'tabindex' => 10)
            ); ?>
            <?php echo Form::label(
               'txtpass',
               isset($errors['txtpass']) ? $errors['txtpass'] : '',
               array('class' => 'error', 'generated' => 'true')
            ); ?>
         </li>
         <li class="registerli">
            <label>New password:</label>
            <?php echo Form::input(
               'txtnewpass',
               '',
               array('id' => 'txtnewpass', 'class' => 'text-fi registertextwidth', 'type' => 'password', 'tabindex' => 11)
            ); ?>
            <?php echo Form::label(
               'txtnewpass',
               isset($errors['txtnewpass']) ? $errors['txtnewpass'] : '',
               array('class' => 'error', 'generated' => 'true')
            ); ?>
         </li>
         <li class="registerli">
            <label>Confirm new password:</label>
            <?php echo Form::input(
               'txtconfirmpass',
               isset($fields['txtconfirmpass']) ? $fields['txtconfirmpass'] : '',
               array('id' => 'txtconfirmpass', 'class' => 'text-fi registertextwidth', 'type' => 'password', 'tabindex' => 12)
            ); ?>
            <?php echo Form::label(
               'txtconfirmpass',
               isset($errors['txtconfirmpass']) ? $errors['txtconfirmpass'] : '',
               array('class' => 'error', 'generated' => 'true')
            ); ?>
         </li>
         <li class="registerli">
            <div class="button" id="uniform-Submit"><span>Submit<input type="Submit" id="Submit" name="Submit" value="" class="text-fi" style="opacity: 0;"></span></div> <label class="error" generated="true" for="Submit"></label>
         </li>
      </ul>
   </div>
</section>
<?php echo Form::close(); ?>
<script type="text/javascript">
   function changepasswordValidation() {

      if (document.getElementById('txtpass').value == '') {
         alert("Enter a old password.");
         document.getElementById('txtpass').focus();
         return false;
      }
      if (document.getElementById('txtnewpass').value == '') {
         alert("Enter a new password.");
         document.getElementById('txtnewpass').focus();
         return false;
      } else if (document.getElementById('txtnewpass').value.length > 12 || document.getElementById('txtnewpass').value.length < 5) {
         alert("Password must between 5-12 character.");
         document.getElementById('txtnewpass').focus();
         return false;
      }
      if (document.getElementById('txtconfirmpass').value == '') {
         alert("Enter a confirm new password.");
         document.getElementById('txtconfirmpass').focus();
         return false;
      }
      if (document.getElementById('txtnewpass').value != document.getElementById('txtconfirmpass').value) {
         alert("Confirm Password must be same as New Password.");
         document.getElementById('txtconfirmpass').focus();
         return false;
      }

      var element = document.getElementById('progressbar');
      element.style.display = "block";
   }
</script>
<style>
   #content {
      box-shadow: none;
      margin-top: 0;
      width: auto;
   }

   .content-container {
      padding: 0;
   }

   #progressbar {
      top: 35%;
      left: 20%;
   }

   .add-form-left-register {
      margin: 20px 0 0 0;
   }

   .wrapper {
      width: calc(100% - 40px);
   }

   .registertextwidth {
      width: 440px;
   }
</style>