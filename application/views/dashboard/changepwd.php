<p><?php /* This is the Dashboard Manager Area. You can manage your profile(s) here. */ ?></p>

  <?php echo $innermenu;?>
	
  <?php if(isset($successmsg) && $successmsg):?>
  <div class="error top smsg"><?php echo $successmsg;?></div>
  <?php endif;?>


        <section class="add-form-left change-pwd-box"><!-- FORM -->
					<?php echo Form::open(URL::base().'dashboard/changepwd', array('id'=>'changepwd','method'=>'post')) ?>
        	<h2>Change Password</h2>
					
        	<ul class="form-view clearfix">
            <li>
							<?php echo Form::label('name', 'New Password') ?> 
							<?php echo Form::input('txtnewpass', '',array('type'=>'password','id'=>'txtnewpass','class'=>'text-fi')) ?>
							<?php echo Form::label('txtnewpass', isset($errors['txtnewpass']) ? $errors['txtnewpass']:'', array('class'=>'error','generated'=>'true')) ?>
            </li>
            <li>
            	<?php echo Form::label('cnewpass', 'Confirm New Password') ?>
							<?php echo Form::input('txtconfirmpass', '',array('type'=>'password','id'=>'txtconfirmpass','class'=>'text-fi')) ?>
              <?php echo Form::label('txtconfirmpass', isset($errors['txtconfirmpass']) ? $errors['txtconfirmpass']:'', array('class'=>'error','generated'=>'true')) ?>
            </li>
            <li>
							<?php echo Form::submit(NULL, 'Change Password', array('id'=>'btnChangePass')) ?>
						</li>
          </ul>
          <?php echo Form::close() ?>
        </section><!-- /FORM -->


<script type="text/javascript">
$(document).ready(function(){
	$("#changepwd").validate({
		rules: {
			txtoldpass: { required: true},
			txtnewpass: {required: true, minlength:5,maxlength:12},
			txtconfirmpass: {required: true, equalTo: "#txtnewpass"}
		},
		/*errorPlacement: function(error, element) {			
		   	error.appendTo(element.parent().next());
    	},*/
		messages: {
			txtoldpass: "Old Password field can't left blank..!",
			txtnewpass: { 
				required: "New Password field can't left blank..!",
				minlength: "Password must between 5-12 character..!",
				maxlength: "Password must between 5-12 character..!"
			},
			txtconfirmpass: { 
				required: "Confirm Password can't left blank..!",
				equalTo: "Confirm Password must be same as New Password..!"
			}      
		}
	});
});
</script>