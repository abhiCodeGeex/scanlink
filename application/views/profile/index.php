<p><?php /* This is the edit profile page. You can edit user profile detail from here */ ?></p>
<div id="progressbar">
	<img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Deleting Logo...
</div>

<style>
	.registerli {
		min-width: 450px;
	}
</style>

<section id="content-ordersummary">
	<!-- ADD FORM content -->
	<?php

	if ($fields['is_sub_user'] == 0) :
		echo Form::open(
			URL::base() . 'profile/index',
			array(
				'id' => 'frmeditprofile',
				'name' => 'frmeditprofile',
				'method' => 'post',
				'enctype' => 'multipart/form-data',
				'onsubmit' => 'javascript:return editprofilevalidation();'
			)
		)
	?>
		<?php echo Form::input(
			'editprofile',
			'yes',
			array('id' => 'editprofile', 'class' => 'text-fi', 'type' => 'hidden')
		); ?>
		<section class="add-form-right-register">
			<!-- ADD FORM RIGHT START -->
			<div class="SectionTitleBox-orderSummary">
				<div class="SectionTitleRegister">&nbsp;</div>
				<ul class="form-view clearfix">
					<li class="registerli">
						<label>Last name:</label>
						<?php echo Form::input(
							'last_name',
							isset($fields['last_name']) ? $fields['last_name'] : '',
							array('id' => 'last_name', 'class' => 'text-fi registertextwidth', 'tabindex' => 2)
						); ?>
						<?php echo Form::label(
							'last_name',
							isset($errors['last_name']) ? $errors['last_name'] : '',
							array('class' => 'error', 'generated' => 'true')
						) ?>
					</li>
					<li class="registerli">
						<label>Billing address:</label>
						<?php echo Form::input(
							'billing_address',
							isset($fields['billing_address']) ? $fields['billing_address'] : '',
							array('id' => 'billing_address', 'class' => 'text-fi registertextwidth', 'tabindex' => 4)
						); ?>
						<?php echo Form::label(
							'billing_address',
							isset($errors['billing_address']) ? $errors['billing_address'] : '',
							array('class' => 'error', 'generated' => 'true')
						); ?>
					</li>
					<li class="registerli">
						<label>Town:</label>
						<?php echo Form::input(
							'town',
							isset($fields['town']) ? $fields['town'] : '',
							array('id' => 'town', 'class' => 'text-fi registertextwidth', 'tabindex' => 6)
						); ?>
						<?php echo Form::label(
							'town',
							isset($errors['town']) ? $errors['town'] : '',
							array('class' => 'error', 'generated' => 'true')
						); ?>
					</li>
					<li class="registerli">
						<label>Postal code:</label>
						<?php echo Form::input(
							'postal_code',
							isset($fields['postal_code']) && $fields['postal_code'] > 0 ? $fields['postal_code'] : '',
							array('id' => 'postal_code', 'class' => 'text-fi registertextwidth', 'onkeypress' => "return isNumberKey(event)", 'tabindex' => 8)
						); ?>
						<?php echo Form::label(
							'postal_code',
							isset($errors['postal_code']) ? $errors['postal_code'] : '',
							array('class' => 'error', 'generated' => 'true')
						); ?>
					</li>



					<li class="code_profile_footer_logo">
						<label class="label_txt">Footer Logo
							<a href="<?php echo url::base(); ?>images/footer_logo_help.png" class="footer-logo-help"><img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" border="0" style="vertical-align:text-bottom;" /></a>


						</label>
						<label>Upload Company Logo: <span class="hint-info">(File type JPG, JPEG, PNG, GIF) (Max file size 1MB)</span></label>
						<iframe src="<?php echo url::base(); ?>test/logoUpload_footer?" id="logoUpload" scrolling="no" frameborder="0" width="393" height="50"></iframe>

						<?php if ($fields["footer_logo"] != "" && file_exists("images/logo/" . $fields["footer_logo"])) {
						?>
							<div id="logo_data"><img src="<?php echo url::base(); ?>images/logo/<?php echo $fields['footer_logo']; ?>" height="50" />
								<img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" alt="Remove Logo" title="Remove Logo" class="delete_img_logo" onClick="delete_footer_logo('<?php echo URL::base(); ?>')" />
							</div>
						<?php
						} ?>

					</li>

				</ul>
			</div>
		</section><!-- ADD FORM RIGHT END -->

		<?php
		if ($sessionmsg != '') {
		?>
			<div class="row-r" style="margin-left: 10px;"><label class="success"><?php echo $sessionmsg; ?></label></div>
		<?php
		}
		?>

		<section class="add-form-left-register">
			<!-- ADD FORM LEFT START -->
			<div class="SectionTitleBox-orderSummary">
				<div class="SectionTitleRegister">Edit Profile</div>

				<ul class="form-view clearfix">
					<li class="registerli">
						<label>First name:</label>
						<?php echo Form::input(
							'first_name',
							isset($fields['first_name']) ? $fields['first_name'] : '',
							array('id' => 'first_name', 'class' => 'text-fi registertextwidth', 'tabindex' => 1)
						); ?>
						<?php echo Form::label(
							'first_name',
							isset($errors['first_name']) ? $errors['first_name'] : '',
							array('class' => 'error', 'generated' => 'true')
						); ?>
					</li>
					<li class="registerli">
						<label>Company name:</label>
						<?php echo Form::input(
							'company_name',
							isset($fields['company_name']) ? $fields['company_name'] : '',
							array('id' => 'company_name', 'class' => 'text-fi registertextwidth', 'tabindex' => 3)
						); ?>
						<?php echo Form::label(
							'company_name',
							isset($errors['company_name']) ? $errors['company_name'] : '',
							array('class' => 'error', 'generated' => 'true')
						) ?>
					</li>
					<li class="registerli">
						<label>Email(this will also be your username):</label>
						<?php echo Form::input(
							'email',
							isset($fields['email']) ? $fields['email'] : '',
							array('id' => 'email', 'class' => 'text-fi registertextwidth', 'tabindex' => 5)
						); ?>
						<?php echo Form::label(
							'email',
							isset($errors['email']) ? $errors['email'] : '',
							array('class' => 'error', 'generated' => 'true')
						); ?>
					</li>
					<li class="registerli">
						<label>Telephone number:</label>
						<?php echo Form::input(
							'phone',
							isset($fields['phone']) && $fields['phone'] > 0 ? $fields['phone'] : '',
							array('id' => 'phone', 'class' => 'text-fi registertextwidth', 'onkeypress' => "return isNumberKey(event)", 'tabindex' => 7)
						); ?>
						<?php echo Form::label(
							'phone',
							isset($errors['phone']) && $errors['phone'] > 0 ? $errors['phone'] : '',
							array('class' => 'error', 'generated' => 'true')
						); ?>
					</li>

					<li class="registerli">
						<label>Shortcut Title:</label>
						<?php
						echo Form::input(
							'shortcut_title',
							isset($clientfields['shortcut_title']) ? $clientfields['shortcut_title'] : '',
							array('id' => 'shortcut_title', 'class' => 'text-fi registertextwidth', 'tabindex' => 8)
						); ?>
					</li>
					<li class="registerli">
						<label>Shortcut Image 1 (Please use a 180x180px dimension PNG image)</label>
						<?php
						$filename1 = 'images/touch_icons/' . $clientfields['shortcut_image1'];
						if ($clientfields['shortcut_image1'] != "" && file_exists($filename1)) {
						?>
							<img src="<?php echo URL::base() ?>/images/touch_icons/<?php echo $clientfields['shortcut_image1'] . "?" . rand(111111, 9999999); ?>" height="50" width="50"><br>
						<?php
						}
						?>
						<input type="file" name="shortcut_image1" id="shortcut_image1" />

					</li>
					<li class="registerli">
						<label>Shortcut Image 2 (Please use a 192x192px dimension PNG image)</label>
						<?php
						$filename2 = 'images/touch_icons/' . $clientfields['shortcut_image2'];

						if ($clientfields['shortcut_image2'] != "" && file_exists($filename2)) {
						?>
							<img src="<?php echo URL::base() ?>/images/touch_icons/<?php echo $clientfields['shortcut_image2'] . "?" . rand(111111, 9999999); ?>" height="50" width="50"><br>
						<?php
						}
						?>
						<input type="file" name="shortcut_image2" id="shortcut_image2" />
					</li>

					<li class="registerli">
						<div class="">
							<?php echo Form::input(
								'Submit',
								isset($fields['Submit']) ? $fields['Submit'] : '',
								array('id' => 'Submit', 'class' => 'text-fi', 'type' => 'Submit', 'tabindex' => 9)
							); ?>
							<?php echo Form::label(
								'Submit',
								isset($errors['Submit']) ? $errors['Submit'] : '',
								array('class' => 'error', 'generated' => 'true')
							); ?>
						</div>
					</li>
				</ul>
			</div>
		</section><!-- ADD FORM LEFT END -->
	<?php echo Form::close();
	endif; //if($fields['is_sub_user']==0):
	?>





	<div class="clear"></div>

	<?php


	echo Form::open(
		URL::base() . 'profile/index',
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
			<div class="SectionTitleRegister">Change Paswword</div>
			<?php
			if ($pwdsuccessmessage != '') {
			?>
				<div class="row-r"><label class="success"><?php echo $pwdsuccessmessage; ?></label></div>
				<div class="clear">&nbsp;</div>
			<?php
			}
			?>
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
					<?php echo Form::input(
						'Submit',
						isset($fields['Submit']) ? $fields['Submit'] : '',
						array('id' => 'Submit', 'class' => 'text-fi', 'type' => 'Submit')
					); ?>
					<?php echo Form::label(
						'Submit',
						isset($errors['Submit']) ? $errors['Submit'] : '',
						array('class' => 'error', 'generated' => 'true')
					); ?>
				</li>
			</ul>
		</div>
	</section>
	<?php echo Form::close();
	?>


	<!-- ADD FORM RIGHT START -->
	<?php if ($fields['is_sub_user'] == 0) : ?>
		<?php echo Form::open(
			URL::base() . 'profile/index',
			array(
				'id' => 'add_user',
				'name' => 'add_user',
				'method' => 'post',
				'enctype' => 'multipart/form-data',
				'onsubmit' => 'javascript:return add_user_validation();'
			)
		);

		echo Form::input(
			'add_new_user',
			'yes',
			array('id' => 'add_new_user', 'class' => 'text-fi', 'type' => 'hidden')
		); ?>
		<section class="add-form-right-register">
			<div class="SectionTitleBox-orderSummary">
				<div class="SectionTitleRegister">Add new sub user</div>

				<ul class="form-view clearfix">
					<li class="registerli">
						<label>Email:</label>
						<?php echo Form::input(
							'txtemail',
							isset($fields['txtemail']) ? $fields['txtemail'] : '',
							array('id' => 'txtemail', 'class' => 'text-fi registertextwidth', 'type' => 'text', 'tabindex' => 13, 'maxlength' => 255)
						); ?>
						<?php echo Form::label(
							'txtemail',
							isset($errors['txtemail']) ? $errors['txtemail'] : '',
							array('class' => 'error', 'generated' => 'true')
						); ?>
					</li>
					<li class="registerli">
						<label>Password:</label>
						<?php echo Form::input(
							'txtpass1',
							isset($fields['txtpass1']) ? $fields['txtpass1'] : '',
							array('id' => 'txtpass1', 'class' => 'text-fi registertextwidth', 'type' => 'password', 'tabindex' => 14, 'maxlength' => 255)
						); ?>
						<?php echo Form::label(
							'txtpass',
							isset($errors['txtpass1']) ? $errors['txtpass1'] : '',
							array('class' => 'error', 'generated' => 'true')
						); ?>
					</li>

					<!-- <li class="registerli">
						<label for="enable_admin_access"> Enable Admin Access:
							<input type="checkbox" name="enable_admin_access" id="enable_admin_access" value="1">
						</label>

					</li> -->

					<li class="registerli">
						<?php echo Form::input(
							'Submit1',
							isset($fields['Submit1']) ? $fields['Submit1'] : '',
							array('id' => 'Submit1', 'class' => 'text-fi', 'type' => 'Submit')
						); ?>
						<?php echo Form::label(
							'Submit1',
							isset($errors['Submit1']) ? $errors['Submit1'] : '',
							array('class' => 'error', 'generated' => 'true')
						); ?>
					</li>
				</ul>

			</div>
		</section>
	<?php echo Form::close();

	endif; //if($fields['is_sub_user']==0)
	?>



</section>
<div class="clear"></div>
<?php if ($fields['is_sub_user'] == 0) : ?>
	<section style="margin-left: 10px;">
		<div class="SectionTitleBox-orderSummary">
			<div class="SectionTitleRegister">Manage User</div>
		</div>

		<div>
			<table width="100%" cellpadding="0" cellspacing="0" class="listing-table">
				<thead>
					<tr>
						<th width="50%" class="left-round" align="left"><strong>Email</strong></th>
						<th width="50%" class="right-round"><strong class="txt-left">Tools</strong></th>
					</tr>
				</thead>
				<tbody id="records">
				</tbody>
			</table>

			<table width="100%" cellpadding="0" cellspacing="0" class="listing-table">
				<?php
				if (count($users) > 0) {
					foreach ($users as $user) {
						if ($user['status'] == 1) {
							$status_title = "Click to Block";
							$imgact = 'active';
						} else {
							$status_title = "Click to Reactivate";
							$imgact = 'blocked';
						}
				?>
						<tr>
							<td align="left" width="50%"><?php echo $user["email"]; ?></td>
							<td align="center">
								<a href="<?php echo URL::base(); ?>profile/edit_user/<?php echo $user['id'] ?>" class="edit_user" style="display: inline;"><img src="<?php echo URL::base(); ?>images/edit.png" width="20" /></a>&nbsp;
								<a href="<?php echo URL::base(); ?>profile/block_user/<?php echo $user['id'] ?>" onclick="if(confirm('Are you sure you want to <?php echo $imgact == 'active' ? 'block' : 'reactivate'; ?> this user?')){ return true; }else{ return false; }"><img src="<?php echo URL::base(); ?>images/<?php echo $imgact; ?>.png" width="20" title="<?php echo $status_title; ?>" /></a>&nbsp;
								<a href="<?php echo URL::base(); ?>profile/delete_user/<?php echo $user['id'] ?>" onclick="if(confirm('Are you sure you want to delete this user?')){ return true; }else{ return false; }" title="Delete user"> <img src="<?php echo URL::base(); ?>images/delete.png" width="20" border="0" /></a>
							</td>
						</tr>

					<?php }
				} else {
					?>
					<tr>
						<td align="center" colspan="2">No users found</td>
					</tr>
				<?php
				} ?>
			</table>
		</div>
	</section>
<?php
endif; //if($fields['is_sub_user']==0)
?>


<script type="application/javascript">
	function delete_footer_logo(base_URL) {
		if (confirm("Are you sure to remove this Logo ? ")) {

			$("#progressbar").show();

			var request = $.ajax({
				url: base_URL + 'dashboard/deletelogo_footer',
				type: "POST",
				dataType: "json"


			});
			request.done(function(data) {
				$("#progressbar").hide();
				if (data.error == 1) {
					alert(data.message);
					return false;
				} else {
					$("#logo_data").empty();
					alert(data.message);
					return false;
				}
			});


		}
	}

	function editprofilevalidation() {
		if (document.getElementById('first_name').value == '') {
			alert("Enter a first name.");
			document.getElementById('first_name').focus();
			return false;
		}
		if (document.getElementById('last_name').value == '') {
			alert("Enter a last name.");
			document.getElementById('last_name').focus();
			return false;
		}
		if (document.getElementById('company_name').value == '') {
			alert("Enter a company name.");
			document.getElementById('company_name').focus();
			return false;
		}
		if (document.getElementById('billing_address').value == '') {
			alert("Enter a billing address.");
			document.getElementById('billing_address').focus();
			return false;
		}
		if (document.getElementById('email').value == '') {
			alert("Enter a email.");
			document.getElementById('email').focus();
			return false;
		} else {
			var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
			if (!emailRegexStr.test(document.getElementById('email').value)) {
				alert('Enter a valid email.');
				document.getElementById('email').focus();
				return false;
			}
		}
		if (document.getElementById('town').value == '') {
			alert("Enter a town.");
			document.getElementById('town').focus();
			return false;
		}
		if (document.getElementById('phone').value == '') {
			alert("Enter a phone.");
			document.getElementById('phone').focus();
			return false;
		}
		if (document.getElementById('postal_code').value == '') {
			alert("Enter a postal_code.");
			document.getElementById('postal_code').focus();
			return false;
		}
		var shortcut_image1 = document.getElementById('shortcut_image1').value;
		var shortcut_image2 = document.getElementById('shortcut_image2').value;

		if (shortcut_image1 != '') {
			var filename = shortcut_image1;
			if (filename.split('.').pop().toLowerCase() != 'png') {
				alert("Please use png image for Shortcut Image 1");
				return false;
			}
		}
		if (shortcut_image2 != '') {
			var filename2 = shortcut_image2;
			if (filename2.split('.').pop().toLowerCase() != 'png') {
				alert("Please use png image for Shortcut Image 2");
				return false;
			}
		}

	}

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

	}

	function isNumberKey(evt) {
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;

		return true;
	}

	function add_user_validation() {
		if (document.getElementById('txtemail').value.split(' ').join('') == '') {
			alert("Enter a user email.");
			document.getElementById('txtemail').focus();
			return false;
		} else {
			var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
			if (!emailRegexStr.test(document.getElementById('txtemail').value)) {
				alert('Enter a valid email.');
				document.getElementById('txtemail').focus();
				return false;
			}
		}

		if (document.getElementById('txtpass1').value.split(' ').join('') == '') {
			alert("Enter a user password.");
			document.getElementById('txtpass1').focus();
			return false;
		}

		return true;
	}

	$(document).ready(function() {
		jQuery(".edit_user").colorbox({
			iframe: true,
			width: "85%",
			height: "750",
			onClosed: function() {
				parent.location.reload()
			}
		});

		jQuery(".footer-logo-help").colorbox({
			iframe: true,
			width: "400",
			height: "380"

		});
	});
</script>