<script>
	$(function() {
		$(".due_date").datepicker({
			dateFormat: 'dd/mm/yy'
		});
		$('#ui-datepicker-div').css('z-index', 99999999);

		$("input:file").uniform();

		$('#save_and_exit').click(function() {
			var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

			valid = false;
			$(".email_recipient_participant").each(function(index) {
				email_val = $.trim($(this).val());

				if (email_val == "") {
					alert('Enter email address.');
					valid = false;
					return false;

				} else if (!emailRegexStr.test(email_val)) {
					alert('Enter a valid email.');
					valid = false;
					return false;
				} else {
					valid = true;
				}

			});


			if (valid == true) // now check for date validation
			{
				$(".due_date_span").each(function(index) {
					//call validation for due date
					valid = validate_date($.trim($(this).text()));
					if (valid == false)
						return false;
				});
			}

			if (valid == true) {

				$(".progress_div").show();

				frm = $('#partcipant');

				frm.ajaxForm({
					url: "<?php echo URL::base(); ?>formbuilder/submit_participant/",
					dataType: "html",
					success: function(responseText, statusText, xhr, $form) {

						$('#form_participant #file').val('');
						$('.participant-import .uploader .filename').html('');
						$(".progress_div").hide();
						$.colorbox.close();
					},
					error: function(e) {


					}
				}).submit();
			}


		});

		$('.grid').on('click', '.edit_user', function() {
			$(this).closest('tr').hide();
			$(this).closest('tr').next().show();
		});


		$('.grid').on('click', '.save_user', function() {
			$(".progress_div").show();


			name = $(this).closest('tr').find('.username_txt').val();
			due_date = $(this).closest('tr').find('.due_date').val();
			participant_id = $(this).closest('tr').find('#participant_id').val();

			cur_obj = this;

			$.ajax({
				type: "GET",
				url: "<?php echo URL::base(); ?>formbuilder/edit_participant/",
				dataType: "html",
				data: {
					'name': name,
					'due_date': due_date,
					'participant_id': participant_id
				},
				success: function(data) {

					$(cur_obj).closest('tr').prev().find('.username_span').text(name);
					$(cur_obj).closest('tr').prev().find('.due_date_span').text(due_date);

					$(".progress_div").hide();
					$(cur_obj).closest('tr').hide();
					$(cur_obj).closest('tr').prev().show();
				},


			});




		});


		$('.grid').on('click', '.cancel_save', function() {
			$(this).closest('tr').hide();
			$(this).closest('tr').prev().show();
		});

	});

	function validate_date(due_date) {
		due_date_arr = due_date.split('/');

		due_date_day = due_date_arr[0];
		due_date_month = due_date_arr[1];
		due_date_year = due_date_arr[2];

		date_curr = new Date();

		cur_day = date_curr.getDate();
		cur_month = date_curr.getMonth() + 1;
		cur_year = date_curr.getFullYear();


		if (due_date_year < cur_year) {
			alert("Please select due date as today or later");
			return false;
		} else {
			if (due_date_month < cur_month && due_date_year == cur_year) {
				alert("Please select due date as today or later");
				return false;
			}
			if (due_date_day < cur_day && due_date_month == cur_month && due_date_year == cur_year) {
				alert("Please select due date as today or later");
				return false;
			}
		}

		return true;

	}

	function add_participant(current_profile_id) {
		name = $.trim($('#name').val());
		due_date = $.trim($('#due_date').val());

		if (name == "") {
			alert("Please enter participant name");
			return false;
		}
		if (due_date == "") {
			alert("Please select due date");
			return false;
		}

		//call validation for due date
		if (validate_date(due_date)) {
			$(".progress_div").show();

			$.ajax({
				type: "GET",
				url: "<?php echo URL::base(); ?>formbuilder/add_participant/",
				dataType: "html",
				data: {
					"current_profile_id": current_profile_id,
					'name': name,
					'due_date': due_date
				},
				success: function(data) {
					$('#name').val('');
					$('#due_date').val('');
					$('#partcipant .grid').html(data);
					$(".due_date").datepicker({
						dateFormat: 'dd/mm/yy'
					});
					$(".progress_div").hide();
				},


			});
		}



	}

	function import_participant() {


		file_name = $.trim($('#form_participant #file').val());


		if (file_name == "") {
			alert("Please select file to upload");
			return false;
		}

		file_name_arr = file_name.split(".");
		ext = file_name_arr[file_name_arr.length - 1];

		if (ext.toLowerCase() != "xlsx") {
			alert("Upload .xlsx file only");
			return false;
		}

		$(".progress_div").show();

		frm = $('#partcipant');

		frm.ajaxForm({
			url: "<?php echo URL::base(); ?>formbuilder/import_participant/",
			dataType: "html",
			success: function(responseText, statusText, xhr, $form) {

				$('#form_participant #file').val('');
				$('.participant-import .uploader .filename').html('');
				$('#partcipant .grid').html(responseText);
				$(".due_date").datepicker({
					dateFormat: 'dd/mm/yy'
				});
				$(".progress_div").hide();
			},
			error: function(e) {


			}
		}).submit();



	}


	function remove_participant(current_profile_id) {
		if (confirm('Are you sure you want to delete all participants?')) {
			$(".progress_div").show();

			$.ajax({
				type: "GET",
				url: "<?php echo URL::base(); ?>formbuilder/remove_participant/",
				dataType: "html",
				data: {
					"current_profile_id": current_profile_id
				},
				success: function(data) {
					$('#partcipant .grid').html(data);

					$(".progress_div").hide();
				},


			});
		}

	}

	function remove_participant_user(participant_id, profile_id) {

		$(".progress_div").show();

		$.ajax({
			type: "GET",
			url: "<?php echo URL::base(); ?>formbuilder/remove_participant_user/",
			dataType: "html",
			data: {
				"participant_id": participant_id,
				"profile_id": profile_id
			},
			success: function(data) {
				$('#partcipant .grid').html(data);
				$(".due_date").datepicker({
					dateFormat: 'dd/mm/yy'
				});
				$(".progress_div").hide();
			},


		});


	}


	function add_another_recipient(id) {

		var contact_html = '<div class="parent_content" id="email_fld' + id + '">';
		contact_html += '<div class="rounded">';
		contact_html += '<input id="email_recipient_participant" class="email_recipient_participant" name="email_recipient_participant[]" type="text">';
		contact_html += '</div>';
		contact_html += '<div id="remove_ele"><a href="javascript:;" onclick="remove_recipient(email_fld' + id + ')" >Remove</a></div>';
		contact_html += '<div>';

		$('.add-another').html('<a href="javascript:;" onclick="add_another_recipient(' + ((id * 1) + 1) + ')" id="add_another">Add Another</a>');
		$('.add-another').before(contact_html);
	}

	function remove_recipient(obj) {
		$(obj).remove();

	}
</script>
<script type="text/javascript" src="<?php echo url::base(); ?>js/jquery.form.js"></script>
<link media="screen" rel="stylesheet" href="<?php echo url::base(); ?>styles/ui/jquery.ui.theme.css" type="text/css" />
<div style="display:none">
	<a class='participant_list' href="#form_participant" style="padding: 0;" id="participant_list_tag">
		<input class="participant_list" type="button" id="registerbutton" value="Register div" />
	</a>
</div>

<div style='display:none;'>

	<div id='form_participant' style='padding:10px; background:#fff;'>
		<div id="progress_bar" class="progress_div">
			<img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait...
		</div>


		<form action="" name="partcipant" id="partcipant" method="post" enctype="multipart/form-data">
			<input type="hidden" name="profile_id" id="profile_id" value="<?php echo $profile_id; ?>" />
			<div class="green_text">Participant List</div>
			Send email notifications to<br>
			<?php

			if (count($participant_recipient_list_arr) > 0) {
				$rec_cnt = 0;
				foreach ($participant_recipient_list_arr as $participant_recipient) {
			?>
					<div id="email_fld1_<?php echo $participant_recipient["participant_recipient_id"]; ?>">
						<div class="rounded">
							<input id="email_recipient_participant" class="email_recipient_participant" name="email_recipient_participant[]" value="<?php echo $participant_recipient["email"]; ?>" type="text">
						</div>
						<?php
						if ($rec_cnt > 0) {
						?>
							<div id="remove_ele"><a href="javascript:;" onclick="remove_recipient(email_fld1_<?php echo $participant_recipient["participant_recipient_id"]; ?>)">Remove</a></div>
						<?php
						} ?>

					</div>
				<?php $rec_cnt++;
				} //end for
			} else {
				?>
				<div class="rounded">
					<input id="email_recipient_participant" class="email_recipient_participant" name="email_recipient_participant[]" type="text">
				</div>
			<?php
			} //end of if
			?>

			<div class="add-another"><a href="javascript:;" onclick="add_another_recipient('<?php echo count($add_edit_participant_list_arr); ?>')" id="add_another">Add Another</a></div>

			Add Participant<br>

			<div class="rounded">
				Name:&nbsp;<input id="name" name="name" type="text" maxlength="255" style="width: 200px;" />
				Due Date:&nbsp;<input id="due_date" class="due_date" name="due_date" type="text" style="width: 100px;" />
				<div class="green-button" id="Add" onclick="add_participant('<?php echo $profile_id; ?>')" style="width: 120px;">Add</div>
			</div>



			<div class="participant-import">
				<div style="float: left;line-height: 43px;">Upload List&nbsp;</div>
				<div style="float: left;">
					<input id="file" name="file" type="file" />
				</div>
				<div style="float: left;">
					<div class="green-button" id="import" onclick="import_participant()" style="width: 120px;">Upload</div>
				</div>
				<div style="float: left;line-height: 43px;" class="hint-info"> &nbsp;&nbsp;(Upload .xlsx file only)</div>
				<div class="clear"></div>
				<div class="received_div">
					<div class="colorbox_text">OverDue&nbsp;</div>
					<div class="colorcodebox pink" style="width: 40px;">&nbsp;</div>
					<div class="colorbox_text">Received&nbsp;</div>
					<div class="colorcodebox green" style="width: 40px;">&nbsp;</div>
				</div>
			</div>
			<div class="clear"></div>

			<div class="gridheader">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr class="header">
						<th width="30%">Name</th>
						<th width="30%">Employer/Company</th>
						<th colspan="2">Response Due By<br>
							DD/MM/YYY
						</th>
						<th width="14%" colspan="2">Actions</th>
					</tr>
				</table>
			</div>
			<div class="grid">
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<?php
					if (count($add_edit_participant_list_arr) > 0) {

						foreach ($add_edit_participant_list_arr as $participant) {
							$bg_color = "";
							if ($participant["is_participated"] == 1) {
								$bg_color = "#C9FFC9"; //Form submitted

								/*if($participant["diff_date"]>0)
                                                           $bg_color= "#C9FFC9"; //Form submitted
                                                        else //form submitted but after dew date
                                                           $bg_color= "#ffc0cb"; //Overdue
                                                        */
							} else {
								if ($participant["diff_date_created"] > 0)
									$bg_color = "#ffc0cb"; //Overdue
							}
					?>
							<tr <?php if ($bg_color != "") echo 'style="background-color:' . $bg_color . '"'; ?>>
								<td width="30%" <?php if ($participant["is_participated"] == 1) echo 'style="border-bottom: 2px solid #ffffff;"'; ?>><span class="username_span"><?php echo $participant["name"]; ?></span></td>
								<td width="30%" <?php if ($participant["is_participated"] == 1) echo 'style="border-bottom: 2px solid #ffffff;"'; ?>><span class="username_span"><?php echo $participant["employer_cmp"]; ?></span></td>
								<td class="border-column"></td>
								<td <?php if ($participant["is_participated"] == 1) echo 'style="border-bottom: 2px solid #ffffff;"'; ?>><span class="due_date_span"><?php echo $participant["due_date_formated"];
																																																					?></span></td>
								<td class="border-column"></td>
								<td width="14%" <?php if ($participant["is_participated"] == 1) echo 'style="border-bottom: 2px solid #ffffff;"'; ?>>
									<span class="edit_user" style="display: inline;"><img src="<?php echo URL::base(); ?>images/edit.png" width="19" title="Edit Participant" alt="Edit Participant" /></span>&nbsp;
									<span onclick="if(confirm('Are you sure you want to delete this Participant?')){ remove_participant_user('<?php echo $participant["participant_id"] ?>','<?php echo $profile_id; ?>'); }else{ return false; }"> <img src="<?php echo URL::base(); ?>images/delete.png" width="18" border="0" title="Delete Participant" alt="Delete Participant" /></span>
								</td>
							</tr>
							<tr style="display: none;">
								<!-- for edit items inline -->
								<td><input id="name" name="name" class="username_txt inline-textbox" type="text" maxlength="255" style="width: 200px;" value="<?php echo $participant["name"]; ?>" /></td>
								<td class="border-column"></td>
								<td><input id="due_date<?php echo $participant["participant_id"] ?>" class="due_date inline-textbox" name="due_date" type="text" style="width: 100px;" value="<?php echo $participant["due_date_formated"]; ?>" /></td>
								<td class="border-column"></td>
								<td><span class="save_user" style="display: inline;"><img src="<?php echo URL::base(); ?>images/save.png" width="16" title="Save Participant" alt="Save Participant" /></span>
									&nbsp;<span class="cancel_save" style="display: inline;"><img src="<?php echo URL::base(); ?>images/cancel.png" width="16" title="Cancel" alt="Cancel" /></span>
									<input id="participant_id" name="participant_id" type="hidden" value="<?php echo $participant["participant_id"]; ?>" />
								</td>
							</tr>
						<?php
						} //end foreach
					} else {
						?>
						<tr>
							<td colspan="5" style="text-align: center;">No Participant found</td>

						</tr>
					<?php
					}
					?>

				</table>
			</div>
			<div class="clear">&nbsp;</div>
			<div style="text-align: right;">
				<a href="<?php echo URL::base() ?>formbuilder/export_participant/<?php echo $profile_id; ?>" target="_blank">
					<div class="green-button" style="width: 150px;">Download List</div>
				</a>
				<div class="gray-button" id="remove" onclick="remove_participant('<?php echo $profile_id; ?>')" style="width: 150px;">Clear List</div>
				<div class="green-button" id="save_and_exit" style="width: 150px;">Save and exit</div>
			</div>


		</form>



	</div>
</div>
<div style="clear: both;"></div>