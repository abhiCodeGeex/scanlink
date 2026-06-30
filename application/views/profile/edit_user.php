<?php

$code_profile_list = DB::query(Database::SELECT, "SELECT name,code_profile_name,id,created_at,activation_start_date,activation_end_date,expired_at,DATEDIFF(activation_start_date,curdate()) AS interval_from_start_date,DATEDIFF(activation_end_date,curdate()) AS interval_from_end_date,if(expired_at > now(),if(expired_at > DATE_ADD(now(),INTERVAL 30 DAY),'green','orange'),'red') as myclass FROM profiles where client_id = " . $_SESSION['client_id'] . " and update_or_not = '1' and type_id >0 GROUP By id ORDER BY code_profile_name")->execute()->as_array();

$assignvalue = DB::select('enable_admin_access', 'show_code_profile_id_to_acc_user')->from('users')->where('id', '=', $user_id)->execute()->as_array();

$selected_code_profiles_ids = $assignvalue['0']['show_code_profile_id_to_acc_user'];

$enable_admin_access = $assignvalue['0']['enable_admin_access'];

$selected_code_profiles_value_array = explode(",", $selected_code_profiles_ids);


?>
<?php echo Form::open(URL::base() . 'profile/edit_user/' . $user_id, array('id' => 'frmedituser', 'name' => 'frmedituser', 'method' => 'post')) ?>


<style>
	.green {
		background-color: #c9ffc9 !important;
	}

	.red {
		background-color: #F5BFB8 !important;
	}

	.orange {
		background-color: #FBC085 !important;
	}

	.listing-table tr th {
		background: #f8f8f8;
		background: -moz-linear-gradient(top, #ffffff 0%, #f4f4f4 100%);
		background: -o-linear-gradient(top, #ffffff 0%, #f4f4f4 100%);
		background: -ms-linear-gradient(top, #ffffff 0%, #f4f4f4 100%);
		font-size: 13px;
		padding: 12px 5px;
	}

	.listing-table tr th,
	.listing-table tr td {
		border-right: 1px solid #e5ebe5;
		border-bottom: 1px solid #e5ebe5;
		padding: 10px 5px;
	}

	div.checker span {
		background: url(../images/checkbox-bg.png) no-repeat left top;
	}

	.wrapper {
		width: auto;
		margin: 0 auto;
	}
</style>
<link type="text/css" href="<?php echo URL::base() ?>styles/style.css" rel="stylesheet" media="screen" />
<style>
	.wrapper {
		width: auto;
		margin: 0 auto;
	}
</style>



<table align="left" width="100%" cellspacing="0" cellpadding="3" border="0">
	<tr>
		<td width="35%" class="orange13"><img width="13" height="13" src="<?php print URL::base(); ?>/siteadmin/images/arrow2.jpg">&nbsp; <strong>Edit User</strong></td>
	</tr>
	<tr>
		<td align="center" width="35%" style=" <?php echo $successmsg ? 'color:#35D10A;' : 'color:#F00;'; ?> padding:2px 0px;font-size:12px;"><?php echo $successmsg ? $successmsg : ($errmsg ? $errmsg : ''); ?></td>
	</tr>
	<tr>
		<td>
			<table align="left" width="100%" cellspacing="0" cellpadding="3" border="0" class="border">
				<tbody>
					<tr>
						<td align="left"><strong>&nbsp;Email:</strong></td>
						<td align="left"><?php echo Form::input('txtUseremail', $user_data ? $user_data['email'] : '', array('id' => 'txtUseremail', 'style' => 'width:235px;height:22px;max-length:100;')) ?></td>
					</tr>
					<tr align="left">
						<td align="left"><strong>&nbsp;Password:</strong></td>
						<td align="left"><?php echo Form::input('txtUserpassword', $user_data ? $user_data['password'] : '', array('id' => 'txtUserpassword', 'type' => 'text', 'style' => 'width:235px;height:22px;max-length:100;')) ?></td>
					</tr>

					<tr align="left">
						<td align="left"><strong>&nbsp;Enable Admin Access:</strong></td>
						<td align="left">
							<!-- <label for="enable_admin_access"> -->
							<input type="checkbox" name="enable_admin_access" id="enable_admin_access" value="1" style="width:235px;height:22px;max-length:100;" <?php echo (!empty($enable_admin_access) && $enable_admin_access == 1) ? 'checked' : '' ?>>
							<!-- </label> -->
						</td>
					</tr>


				</tbody>
			</table>
			<button value="Edit User" class="button-save">Save</button>
			<fieldset <?php echo (!empty($enable_admin_access)) ? 'disabled' : '' ?> style="border:none;" class="fieldset">
				<table>
					<tr>
						<td colspan="2" align="left">
							<table cellpadding="6" cellspacing="15" border="0">
								<tr>
									<td align="center">
										<img src="<?php echo URL::base(); ?>images/register_code.jpg" title="Register Codes" border="0" />
										<input type="checkbox" value="1" <?php if ($user_data['access_addcode'] == "1") echo 'checked="checked"'; ?> name="access_addcode" />
									</td>
									<td align="center">
										<img src="<?php echo URL::base(); ?>images/edit_profile.png" title="Edit Profile" border="0" />
										<input type="checkbox" value="1" <?php if ($user_data['access_edit'] == "1") echo 'checked="checked"'; ?> name="access_edit" />
									</td>
									<td align="center">
										<img src="<?php echo URL::base(); ?>images/analytics.png" title="Scanalytics" border="0" />
										<input type="checkbox" value="1" <?php if ($user_data['access_analytics'] == "1") echo 'checked="checked"'; ?> name="access_analytics" />
									</td>
									<td align="center">
										<img src="<?php echo URL::base(); ?>images/form_submission.png" title="Form Submission" border="0" />
										<input type="checkbox" value="1" <?php if ($user_data['access_form_submission'] == "1") echo 'checked="checked"'; ?> name="access_form_submission" />
									</td>
									<td align="center">
										<img src="<?php echo URL::base(); ?>images/download.png" title="Download QR/DM code" border="0" />
										<input type="checkbox" value="1" <?php if ($user_data['access_download'] == "1") echo 'checked="checked"'; ?> name="access_download" />
									</td>
									<td align="center">
										<img src="<?php echo URL::base(); ?>images/order-label.png" title="Order Labels" border="0" />
										<input type="checkbox" value="1" <?php if ($user_data['access_label'] == "1") echo 'checked="checked"'; ?> name="access_label" />
									</td>
									<td align="center">
										<img src="<?php echo URL::base(); ?>images/visitor_log.png" title="View Visitor Log" border="0" />
										<input type="checkbox" value="1" <?php if ($user_data['access_log'] == "1") echo 'checked="checked"'; ?> name="access_log" />
									</td>

									<td align="center" class="delete_section_profile" style="display:<?php echo !empty($enable_admin_access) ? '' : 'none' ?>">
										<img src="<?php echo URL::base(); ?>images/delete_profile.png" title="Delete Profile" border="0" />
										<input type="checkbox" value="1" <?php if ($user_data['access_delete'] == "1") echo 'checked="checked"'; ?> name="access_delete" />
									</td>

								</tr>
							</table>
						</td>
					</tr>
				</table>

				<h3>Select Code Profile</h3>
				<table width="100%" cellpadding="0" cellspacing="0" class="listing-table" id="table">

					<thead>
						<tr>
							<th width="10%" class="left-round" align="center"><strong>Profile No.</strong></th>
							<th width="55%" align="left"><strong class="txt-left">Code Profile Name</strong></th>
							<th width="20%" align="center"><strong class="txt-left">Code Expiry Date</strong></th>
							<th width="20%" align="center"><strong class="txt-left"> <input type="checkbox" id="main_checkbox"> Select </strong></th>
						</tr>
					</thead>
					<tbody id="records" style="font-size: 12px;">
						<?php foreach ($code_profile_list as $key => $code_profile) { ?>
							<?php $code_profile_name = !empty($code_profile['code_profile_name']) ? $code_profile['code_profile_name'] : $code_profile['name'];
							$block_img_icon = "";
							if ($code_profile['activation_start_date'] != "0000-00-00" &&  $code_profile['activation_end_date'] != "0000-00-00") {
								if (($code_profile['interval_from_start_date'] * 1) > 0) {
									$block_img_icon = "<img height='15' src='" . URL::base() . "images/blocked.png' alt='Inactive' title='Inactive'>";
								} elseif (($code_profile['interval_from_end_date'] * 1) < 0) {
									$block_img_icon = "<img height='15' src='" . URL::base() . "images/blocked.png' alt='Inactive' title='Inactive'>";
								}
							}
							?>
							<tr class="<?php echo $code_profile['myclass']; ?>">
								<td><?php echo $block_img_icon; ?><?php echo $code_profile['id']; ?></td>
								<td><?php echo $code_profile_name; ?></td>
								<td><?php echo date('d/m/Y', strtotime($code_profile['expired_at'])); ?></td>
								<td><input type="checkbox" class="checkbox customcheckbox" name="code_profile_id[]" value="<?php echo $code_profile['id'] ?>" <?php echo  in_array($code_profile['id'], $selected_code_profiles_value_array) ? 'checked' : '' ?> /></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</fieldset>


		</td>
	</tr>
</table>

<?php echo form::hidden("clientid", $user_id ? $user_id : ($user_data ? $user_data['clientid'] : '')); ?>
<br>
<br>
<br>
<button value="Edit User" class="button-save">Save</button>

<?php echo Form::close() ?>
<script>
	$(function() {
		$(":text,:checkbox").uniform();
		$("#main_checkbox").click(function() {
			if ((this.checked)) {
				$('.customcheckbox').parent().addClass('checked');
				$('.customcheckbox').prop('checked', this.checked);
			} else {
				$('.customcheckbox').removeAttr('checked');
				$('.customcheckbox').parent().removeClass('checked');
			}
		});

		$("#enable_admin_access").click(function() {
			if ((this.checked)) {
				$(".fieldset").attr('disabled', true);
				$(".delete_section_profile").css('display', '');
			} else {
				$(".fieldset").removeAttr('disabled');
				$(".delete_section_profile").css('display', 'none');
			}

		});
	});
</script>