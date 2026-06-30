<script>
	$(document).ready(function() {
		$("#activation_start_date").datepicker({
			dateFormat: 'dd/mm/yy',
			onSelect: function() {
				if (($('#free_code').val() == 0 || ($('#free_code').val() == 1 && $('#renewal_required').val() == 1)) && validate_date_range($(this).val(), $('#hdn_exp_date').val()) == false) {
					alert("Date range can\'t be set until the code is renewed.");
					$(this).val('');
				}
				if ($("#activation_end_date").val() != "") {
					if (is_valid_date($("#activation_end_date").val()) == false) {
						alert("Please select valid end date");
						return false;
					} else if (validate_date_range($(this).val(), $("#activation_end_date").val()) == false) {
						alert("Please select valid date range");
						$(this).val('');
					}
				}
			}
		});
		$("#activation_end_date").datepicker({
			dateFormat: 'dd/mm/yy',
			onSelect: function() {
				if (($('#free_code').val() == 0 || ($('#free_code').val() == 1 && $('#renewal_required').val() == 1)) && validate_date_range($(this).val(), $('#hdn_exp_date').val()) == false) {
					alert("Date range can\'t be set until the code is renewed.");
					$(this).val('');
				}
				if ($("#activation_start_date").val() != "") {
					if (is_valid_date($("#activation_start_date").val()) == false) {
						alert("Please select valid start date");
						return false;
					} else if (validate_date_range($("#activation_start_date").val(), $(this).val()) == false) {
						alert("Please select valid date range");
						$(this).val('');
					}
				}
			}
		});
		$('.clear-dates').on('click', function() {
			$("#activation_start_date,#activation_end_date").val('');
		});
		$('#add_existing').on('click', function() {
			$(".forms_existing").colorbox({
				inline: true,
				width: "770",
				height: "100%",
				onClosed: function() {},
			});
			$("#tabs").tabs(); //for tab view
			$("#tabed_content_main").show();
			$("#tabed_content_other").hide();
			$('#preview_form').hide();
			$('#form_listing').show();
			$("#forms_existing_tag").click();
		});
		//hide help tooltip when mouseleave
		$('.help-div').on('mouseleave', function() {
			$(this).hide();
		});
		// show help tooltip
		$('.help-icon').on('mouseenter', function() {
			$(this).next().show();
		});
		//open help icon
		$('.help-div a').click(function(e) {
			$(this).parent().hide();
			e.preventDefault();
			var url = $(this).attr("href");
			if (url != '') {
				var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
				var result = (url.match(p)) ? RegExp.$1 : false;
				if (result) {
					$('#how_to_pop_up').html('<iframe src="' + url + '" frameborder="0" allowfullscreen wmode="transparent" width="480" height="300" ></iframe>');
					$('.black-tint').show();
					$('.player-window').show(500);
				} else {
					var substr = 'image#';
					if (url.indexOf(substr) !== -1) {
						url = url.replace(substr, "");
						$('.black-tint').show();
						$('.player-window-image').show(500);
						3
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
		});
	});
</script>
<?php
echo $formbuilder_popup;
echo $formbuilder_existing_popup;
echo $add_edit_participant_list_popup;
echo $innermenu; ?>
<?php
if ($add_form_display) {
?>
	<?php echo Form::open(URL::base() . 'survey/index', array('id' => 'frmsurvey', 'name' => 'frmsurvey', 'method' => 'post', 'enctype' => 'multipart/form-data')) ?>
	<input type="hidden" id="profile_id" name="profile_id" value="<?php echo $profile_id; ?>" />
	<input type="hidden" id="hdn_email_arr" name="hdn_email_arr" value="" />
	<input type="hidden" id="hdn_form_name" name="hdn_form_name" value="" />
	<input type="hidden" id="form_email_tag" name="form_email_tag" value="" />
	<input type="hidden" id="hdn_exp_date" name="hdn_exp_date" value="<?php echo $expired_at; ?>" />
	<input type="hidden" id="free_code" name="free_code" value="<?php echo $free_code; ?>" />
	<input type="hidden" id="renewal_required" name="renewal_required" value="<?php echo $renewal_required; ?>" />
	<div class="scanlink-container">
		<section class="black-tint"></section>
		<section class="add-form-right">
			<!-- ADD FORM RIGHT -->
			<section class="iphone-preview">
			</section>
			<div class="clear"></div>
		</section><!-- /ADD FORM RIGHT -->
		<section class="add-form-left" style="width: 460px;">
			<!-- ADD FORM LEFT -->
			<div class="SectionTitleBox">
				<div class="SectionTitle">Code Profile Name</div>
				<ul class="form-view clearfix">
					<li>
						<?php echo Form::input('txt_code_profile_name', isset($post['txt_code_profile_name']) ? $post['txt_code_profile_name'] : '', array('id' => 'txt_code_profile_name', 'class' => 'text-fi', 'style' => 'width:415px;', 'maxlength' => '255')) ?>
						<?php echo Form::label('txt_code_profile_name', isset($errors['txt_code_profile_name']) ? $errors['txt_code_profile_name'] : '', array('class' => 'error', 'generated' => 'true')) ?>
					</li>
				</ul>
				<div class="clear"></div>
				<div class="set-activation-period">
					Set activation period (optional)<br>
					<span class="hint-info">Leave date fields blank if this does not apply</span>
				</div>
				<div class="activation-start-date" style="margin-left:1px;">
					Start Date
					<br>
					<input type="text" id="activation_start_date" name="activation_start_date" value="" style="width:77px;height:16px;" />
				</div>
				<div class="activation-end-date" style="margin-left:1px;">
					End Date
					<br>
					<input type="text" id="activation_end_date" name="activation_end_date" value="" style="width:77px;height:16px;" />
				</div>
				<div>
					<br>
					<img class="clear-dates" src="<?php echo url::base(); ?>images/clear_date.png" style="height:23px;" alt="Clear Dates" title="Clear Dates" />
				</div>
				<div class="clear"></div>
			</div>
			<div class="SectionTitleBox">
				<div class="SectionTitle">Logo
					<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
					<div class="help-div" style="margin-left: 50px;"><a href="https://www.youtube.com/embed/hGrBYsys2Oo?rel=0">Video Tutorial</a>
						<div class="arrow-bg"></div>
					</div>
				</div>
				<ul class="form-view clearfix">
					<li>
						<label>Upload Company Logo: <span class="hint-info">(File type JPG, JPEG, PNG, GIF) (Max file size <?php echo UPLOAD_MAX_FILESIZE; ?>)</span></label>
						<iframe src="<?php echo url::base(); ?>test/logoUpload?profile_id=<?php echo $profile_id; ?>&type_id=<?php echo $type_id; ?>" id="logoUpload" scrolling="no" frameborder="0" width="400" height="50"></iframe>
						<?php if (isset($post["logo"]) && isset($post["logo"]["logo_name"]) && $post["logo"]["logo_name"] != "") { ?>
							<div id="logo_data">
								<img src="<?php echo URL::base() . $post["logo"]["logo_name"]; ?>" height="30" alt="<?php echo $post['txtname'] ?>" style="margin:5px" />
								<img src="<?php echo URL::base(); ?>images/delete2.png" height="15" width="15" alt="Remove Logo" title="Remove Logo" class="tooltip delete_ico delete_img_logo" onClick="deleteLogo('<?php echo $profile_id; ?>','<?php echo URL::base(); ?>')" />
								<input type="hidden" id="uploaded" name="uploaded" value="1" />
							</div>
						<?php } else { ?>
							<input type="hidden" id="uploaded" name="uploaded" value="" />
						<?php
						} ?>
					</li>
				</ul>
				<input type="hidden" name="enable_form" value="1" id="enable_form" />
				<input type="hidden" name="code_type" id="code_type" value="0" /> <!-- code type = qr code -->
			</div>
			<div class="form-builder-box">
				<div class="form-builder-title">Form Builder
					<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
					<div class="help-div" style="margin-left: 117px;"><a href="https://www.youtube.com/embed/CfLEhcgvgrA?rel=0">Video Tutorial</a>
						<div class="arrow-bg"></div>
					</div>
					<div class="activate-for-profile">
						<span><label for="enable_form">Enable</label></span>
						<span><input type="checkbox" name="enable_form" value="1" id="enable_form" /></span>
					</div>
				</div>
				<div class="existing-item">
					<span><input type="checkbox" name="add_existing" id="add_existing" /></span>
					<span><label for="add_existing">Use an existing form</label></span>
				</div>
				<div class="existing-item">
					<span><input type="checkbox" name="enable_form_analytics" value="1" id="enable_form_analytics" /></span>
					<span><label for="enable_form_analytics">Enable Form Analytics <small>(Note: editing this form will not be possible once saved)</small></label></span>
				</div>
				<div class="existing-item">
					<span>Form submission format</span>
					<span><input type="radio" name="form_submission_format" value="0" checked="" id="email_only" /></span>
					<span><label for="email_only">Email only</label></span>
					<span><input type="radio" name="form_submission_format" value="1" id="email_with_pdf" /></span>
					<span><label for="email_with_pdf">Email notification with PDF</label></span>
				</div>
				<div class="clear">&nbsp;</div>
				<div id="iframe_container">
					<iframe id="iframe_frm_builder" width="458" frameborder="no" height="1114" scrolling="no" src="<?php echo URL::base(); ?>formbuilder/survey_form/?profile_id=<?php echo $profile_id; ?>&enable_form_analytics=0"></iframe>
				</div>
				<div class="clear">&nbsp;</div>
			</div>
			<div class="footer-rouded" style="width: 440px;"><span class="expand-reduce">Expand Window</span> <img src="<?php echo URL::base() ?>images/expand_window.png" class="expand-reduce" id="expand_reduce_img" width="25" align="absmiddle" alt=""></div>
			<div class="clear">&nbsp;</div>
			<ul class="form-view clearfix">
				<li>
					<?php echo Form::submit(NULL, '&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;', array('id' => 'save', 'class' => 'save', 'style' => 'width: 150px;', 'onclick' => 'javascript: beforeSave();')) ?>
				</li>
			</ul>
		</section>
		<?php echo Form::close() ?>
		<div id="progressbar">
			<img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Saving Profile...
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			jQuery(".iframe").colorbox({
				iframe: true,
				width: "750",
				height: "600",
				onClosed: function() {
					//			parent.location.reload()
				}
			});
			$("#frmsurvey").validate({
				rules: {
					/*logo_file: { required: true, accept: 'png|jpg|jpeg|gif' },
					uploaded: "required",*/
					txt_code_profile_name: "required"
				},
				messages: {
					/* logo_file: { required: "Please specify logo ", accept: "Only png jpg jpeg or gif file is allowed" },
					 uploaded: "Please specify logo ",*/
					txt_code_profile_name: "Please provide code profile name"
				},
				submitHandler: function(form) {
					activation_start_date = $("#activation_start_date").val();
					activation_end_date = $("#activation_end_date").val();
					hdn_exp_date = $("#hdn_exp_date").val();
					free_code = $("#free_code").val();
					renewal_required = $("#renewal_required").val();
					if (activation_start_date != "" && is_valid_date(activation_start_date) == false) {
						alert("Please select valid start date");
						return false;
					}
					if ((free_code == 0 || (free_code == 1 && renewal_required == 1)) && activation_start_date != "" && validate_date_range(activation_start_date, hdn_exp_date) == false) {
						alert("Date range can\'t be set until the code is renewed.");
						return false;
					}
					if (activation_start_date != "" && activation_end_date == "") {
						alert("Please select end date");
						return false;
					}
					if (activation_end_date != "" && is_valid_date(activation_end_date) == false) {
						alert("Please select valid end date");
						return false;
					}
					if ((free_code == 0 || (free_code == 1 && renewal_required == 1)) && activation_end_date != "" && validate_date_range(activation_end_date, hdn_exp_date) == false) {
						alert("Date range can\'t be set until the code is renewed.");
						return false;
					}
					if (activation_start_date == "" && activation_end_date != "") {
						alert("Please select start date");
						return false;
					}
					if (activation_start_date != "" && activation_end_date != "" && validate_date_range(activation_start_date, activation_end_date) == false) {
						alert("Please select valid date range.");
						return false;
					}
					//check for recipient
					iframe = document.getElementById('iframe_frm_builder');
					form_iframe = iframe.contentDocument.frm_survey;
					email_arr = '';
					form_name = '';
					var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
					var x = iframe.contentDocument.getElementsByName("email_recipient[]");
					var y = iframe.contentDocument.getElementsByName("email_tag");
					var email_tag = y[0].value;
					for (i = 0; i < (x.length); i++) {
						if (x[i].value.split(" ").join("") != "") {
							if (!emailRegexStr.test(x[i].value)) {
								alert('Enter a valid email.');
								return false;
							} else {
								if (email_arr == "")
									email_arr = x[i].value;
								else
									email_arr = email_arr + "," + x[i].value;
							}
						}
					}
					if ($('#enable_form').prop('checked') == true) {
						form_name = form_iframe.form_name.value;
						if (form_name.split(" ").join("") == "") {
							alert("Please enter form name");
							return false;
						}
						if (email_arr == "") {
							alert("Please enter at least one recipient email");
							return false;
						}
					}

					document.getElementById('hdn_email_arr').value = email_arr;
					document.getElementById('hdn_form_name').value = form_name;
					document.getElementById('form_email_tag').value = email_tag;
					//////// end check for recipient
					//check validation for participant function i.e. if participant notifi. email or participant name added user has to add participant name on form builder
					email_recipient_str = "";
					if ($(".email_recipient_participant").length > 0) {
						$(".email_recipient_participant").each(function(index) {
							email_val = $.trim($(this).val());
							if (email_val != "") {
								email_recipient_str = email_recipient_str + "::" + email_val;
							}
						});
					}
					if (email_recipient_str != "" || $(".due_date_span").length > 0) {
						var participant_name_ele = iframe.contentDocument.getElementsByName("participant_name_ele[]");
						if (participant_name_ele.length == "0") //no "participant name" control added to form
						{
							alert("This form has an active participant list but no Participant Name field has been added. Please add a Participant Name field to the form before saving.");
							return false;
						}
					}
					//end check validation for participant function i.e. if participant notifi. email or participant name added user has to add participant name on form builder
					document.getElementById("progressbar").style.display = "block";
					form.submit();
					return true;
				}
			});
		});
		$(function() {
			$('textarea').autogrow();
		});
	</script>
<?php } else { ?>
	<p> You can not add Form/Survey/Checklist.kindly purchase the code.</p>
<?php } ?>