<?php
if ($post['tiles_order'] == "")
	$tiles_order_ori = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
else
	$tiles_order_ori = explode(",", $post['tiles_order']);
?>
<script>
	function add_another_voc_user() {
		sr_no = $('.sr_no').length + 1;

		html = '<div class="sr_no" id="voc_user_div_' + sr_no + '">';
		html += '	<div  class="voc_user_details">';
		html += '   	<label>Email:</label>';
		html += '   	<input type="text" name="voc_user_email[]" class="text-fi" value="" maxlength="255">';
		html += '	</div>';
		html += '	<div class="voc_user_details">';
		html += '   	<label>Password:</label>';
		html += '   	<input type="password" name="voc_user_password[]" class="text-fi" value="" maxlength="255">';
		html += '	</div>';
		html += '	<div class="remove_voc_user"><a onclick="remove_voc_user(' + sr_no + ')" href="javascript:;">Remove</a></div>';
		html += '</div>';


		$('.add_another_voc_user').before(html);
	}

	function remove_voc_user(id) {
		$('#voc_user_div_' + id).remove();
	}

	function add_other_recipient() {
		sr_no = $('.recipient_sr_no').length + 1;

		html = '<div class="recipient_sr_no" id="voc_recipient_div_' + sr_no + '">';
		html += '	<div class="voc_user_details">';
		html += '		<label>Recipients Email:</label>';
		html += '		<input type="text" name="voc_recipient_email[]" class="text-fi" value="" maxlength="255">';
		html += '	</div>';
		html += '	<div class="remove_voc_recipient"><a onclick="remove_voc_recipient(' + sr_no + ')" href="javascript:;">Remove</a></div>';
		html += '</div>';


		$('.add_another_voc_recipient').before(html);
	}

	function remove_voc_recipient(id) {
		$('#voc_recipient_div_' + id).remove();
	}

	function add_another_file(max_upload_size) {
		date_curr = new Date();

		hours = date_curr.getHours();
		mins = date_curr.getMinutes();
		seconds = date_curr.getSeconds();

		random_id = hours + "" + mins + "" + seconds;
		cntrl_name = "txt" + random_id;

		html = '<div class="voc_user_details" style="width:50%;float:left;">'
		html += '	<label>Document Name</label>';
		html += '	<input type="text" id="' + cntrl_name + '" name="doc_name[]" style="width:200px" class="doc_name text-fi" value="" maxlength="200">';
		html += '</div>';
		html += '<div class="voc_user_details" style="width:50%;float:left;">';
		html += '	<label>Expiry Date</label>';
		html += '	<input type="text" name="doc_expiry_date[]" style="width:216px" class="doc_expiry text-fi" value="" maxlength="10">';
		html += '</div>';
		html += '<div class="clear"></div>';
		html += '<div>';
		html += '	<label>Upload a Document: <span class="hint-info">(File type DOC, DOCX, PDF, GIF, JPEG, PNG) (Max file size ' + max_upload_size + ')</span></label>';
		html += '</div>';
		html += '<div class="voc_doc_upload"><input type="file" name="document_file[]"  /></div>';

		$('.add_another_voc_file').before(html);
		$('#' + cntrl_name).maxlength({
			slider: true,
			maxCharacters: 50
		});
		$("input:file").uniform();

		$('.doc_expiry').datepicker({
			dateFormat: 'dd/mm/yy',
			changeYear: true,
			changeMonth: true
		});
	}

	$(document).ready(function() {

		$('.doc_name').maxlength({
			slider: true,
			maxCharacters: 50
		});

		$('#voc_dob').datepicker({
			dateFormat: 'dd/mm/yy',
			changeYear: true,
			changeMonth: true,
			yearRange: "1915:<?php echo date('Y'); ?>"
		});

		$('.doc_expiry').datepicker({
			dateFormat: 'dd/mm/yy',
			changeYear: true,
			changeMonth: true
		});

		$('.voc_email_sign1_bold').on('click', function() {

			if ($(this).hasClass('voc_email_feature_selected')) {
				$(this).removeClass('voc_email_feature_selected');
				$('#voc_email_sign1_bold').val('0');
			} else {
				$(this).addClass('voc_email_feature_selected');
				$('#voc_email_sign1_bold').val('1');
			}

		});

		$('.voc_email_sign1_italic').on('click', function() {

			if ($(this).hasClass('voc_email_feature_selected')) {
				$(this).removeClass('voc_email_feature_selected');
				$('#voc_email_sign1_italic').val('0');
			} else {
				$(this).addClass('voc_email_feature_selected');
				$('#voc_email_sign1_italic').val('1');
			}

		});

		$('.voc_email_sign1_underline').on('click', function() {

			if ($(this).hasClass('voc_email_feature_selected')) {
				$(this).removeClass('voc_email_feature_selected');
				$('#voc_email_sign1_underline').val('0');
			} else {
				$(this).addClass('voc_email_feature_selected');
				$('#voc_email_sign1_underline').val('1');
			}

		});

		$('.voc_email_sign2_bold').on('click', function() {

			if ($(this).hasClass('voc_email_feature_selected')) {
				$(this).removeClass('voc_email_feature_selected');
				$('#voc_email_sign2_bold').val('0');
			} else {
				$(this).addClass('voc_email_feature_selected');
				$('#voc_email_sign2_bold').val('1');
			}

		});

		$('.voc_email_sign2_italic').on('click', function() {

			if ($(this).hasClass('voc_email_feature_selected')) {
				$(this).removeClass('voc_email_feature_selected');
				$('#voc_email_sign2_italic').val('0');
			} else {
				$(this).addClass('voc_email_feature_selected');
				$('#voc_email_sign2_italic').val('1');
			}

		});

		$('.voc_email_sign2_underline').on('click', function() {

			if ($(this).hasClass('voc_email_feature_selected')) {
				$(this).removeClass('voc_email_feature_selected');
				$('#voc_email_sign2_underline').val('0');
			} else {
				$(this).addClass('voc_email_feature_selected');
				$('#voc_email_sign2_underline').val('1');
			}

		});

		$('.preview-mail').on('click', function() {
			$('.preview-mail-list').toggle();
		});

		$('.preview-mail-list div').on('click', function() {
			$('.preview-mail-list').hide();
			div_id = $(this).attr('id');
			profile_id = $('#profile_id').val();

			$('#details_pop_up').html('<iframe src="<?php echo URL::base() ?>voc/email_template_view?profile_id=' + profile_id + "&section=" + div_id + '" frameborder="0" allowfullscreen wmode="transparent" width="700" height="500" ></iframe>');

			$('.black-tint').show();
			$('.player-window-new').show(500);
		});

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
				height: "90%",
				onClosed: function() {

				},
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


<?php echo $formbuilder_popup;
echo $formbuilder_existing_popup;
echo $add_edit_participant_list_popup;
echo $innermenu; ?>

<?php
if ($add_form_display) {
?>
	<?php echo Form::open(URL::base() . 'voc/index', array('id' => 'frmvoc', 'name' => 'frmvoc', 'method' => 'post', 'enctype' => 'multipart/form-data')) ?>
	<input type="hidden" id="profile_id" name="profile_id" value="<?php echo $profile_id; ?>" />
	<input type="hidden" id="hdn_email_arr" name="hdn_email_arr" value="" />
	<input type="hidden" id="hdn_form_name" name="hdn_form_name" value="" />
	<input type="hidden" id="form_email_tag" name="form_email_tag" value="" />
	<input type="hidden" id="hdn_order" name="hdn_order" value="<?php echo implode(",", $tiles_order_ori); ?>" />
	<input type="hidden" id="hdn_exp_date" name="hdn_exp_date" value="<?php echo $expired_at; ?>" />
	<input type="hidden" id="free_code" name="free_code" value="<?php echo $free_code; ?>" />
	<input type="hidden" id="renewal_required" name="renewal_required" value="<?php echo $renewal_required; ?>" />
	<div class="scanlink-container">
		<section class="add-form-left connectedSortable">
			<!-- ADD FORM LEFT -->

			<div class="SectionTitleBox">
				<div class="SectionTitle">Code Profile Name</div>
				<ul class="form-view clearfix">
					<li>
						<?php echo Form::input('txt_code_profile_name', isset($post['txt_code_profile_name']) ? $post['txt_code_profile_name'] : '', array('id' => 'txt_code_profile_name', 'class' => 'text-fi', 'style' => 'width:450px;', 'maxlength' => '255')) ?>
						<?php echo Form::label('txt_code_profile_name', isset($errors['txt_code_profile_name']) ? $errors['txt_code_profile_name'] : '', array('class' => 'error', 'generated' => 'true')) ?>
					</li>
				</ul>

				<div class="clear"></div>
				<div class="set-activation-period">
					Set activation period (optional)<br>
					<span class="hint-info">Leave date fields blank if this does not apply</span>
				</div>
				<div class="activation-start-date">
					Start Date
					<br>
					<input type="text" id="activation_start_date" name="activation_start_date" value="" />
				</div>
				<div class="activation-end-date">
					End Date
					<br>
					<input type="text" id="activation_end_date" name="activation_end_date" value="" />
				</div>
				<div>
					<br>
					<img class="clear-dates" src="<?php echo url::base(); ?>images/clear_date.png" alt="Clear Dates" title="Clear Dates" />
				</div>
				<div class="clear"></div>
			</div>

			<div class="SectionTitleBox">
				<div class="SectionTitle">Additional User Access Login
					<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />


				</div>
				<div class="form-view clearfix">
					<div class="voc_user_details">
						<label>Email:</label>
						<input type="text" name="voc_user_email[]" class="text-fi" value="" maxlength="255">
					</div>
					<div class="voc_user_details">
						<label>Password:</label>
						<input type="password" name="voc_user_password[]" class="text-fi" value="" maxlength="255">
					</div>
					<div class="add_another_voc_user">
						<a onclick="add_another_voc_user()" href="javascript:;">
							Add Another
							<img src="<?php echo URL::base() ?>images/expand_window.png" id="expand_reduce_img" width="18" align="absmiddle" alt="">
						</a>
					</div>
					<div class="clear"></div>
				</div>

				<div class="clear"></div>
			</div>

			<div class="SectionTitleBox">
				<div class="SectionTitle">Email Notification Settings
					<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />


				</div>
				<div class="form-view clearfix">
					<div class="voc_user_details">
						<label>Recipients Email:</label>
						<input type="text" name="voc_recipient_email[]" class="text-fi" value="" maxlength="255">
					</div>
					<div class="add_another_voc_recipient">
						<a onclick="add_other_recipient()" href="javascript:;">
							Add Another
							<img src="<?php echo URL::base() ?>images/expand_window.png" id="expand_reduce_img" width="18" align="absmiddle" alt="">
						</a>
					</div>
					<div class="clear">&nbsp;</div>

					<div class="voc_user_details">
						<label style="display:inline;">Email Text hyperlink URL:</label>
						<div style="float:right;">(The default URL is http://www.myskills.gov.au)</div>
						<div class="clear"></div>
						<input type="text" name="voc_email_url" class="text-fi" value="">

					</div>
					<div class="voc_user_details">
						<label style="display:inline;">Email Text:</label>
						<div style="float:right;">(The default text is "Click here to find a service provider near you")</div>
						<div class="clear"></div>
						<input type="text" name="voc_email_text" class="text-fi" value="">

					</div>

					<div class="voc_user_details">
						<label style="display:inline;">Email Signature:</label>
						<div class="email_sign_features">
							<div style="float:right">(The default signature is "ScanLink Support Team")</div>

							<input type="hidden" name="voc_email_sign1_bold" id="voc_email_sign1_bold" value="0">
							<input type="hidden" name="voc_email_sign1_italic" id="voc_email_sign1_italic" value="0">
							<input type="hidden" name="voc_email_sign1_underline" id="voc_email_sign1_underline" value="0">
						</div>
						<div class="clear"></div>
						<textarea name="voc_email_sign_line1" id="voc_email_sign_line1" rows="1" class=""></textarea>
						<script>
							CKEDITOR.replace('voc_email_sign_line1', {
								toolbar: 'custom',
								enterMode: CKEDITOR.ENTER_BR
							});
						</script>
					</div>

					<div class="voc_user_details" style="display:none">
						<label style="display:inline;">Email Signature Line 2:</label>
						<div class="email_sign_features">
							<div class="voc_email_sign2_bold">B</div>
							<div class="voc_email_sign2_italic">I</div>
							<div class="voc_email_sign2_underline">U</div>

							<input type="hidden" name="voc_email_sign2_bold" id="voc_email_sign2_bold" value="0">
							<input type="hidden" name="voc_email_sign2_italic" id="voc_email_sign2_italic" value="0">
							<input type="hidden" name="voc_email_sign2_underline" id="voc_email_sign2_underline" value="0">
						</div>
						<div class="clear"></div>
						<input type="text" name="voc_email_sign_line2" class="text-fi" value="" maxlength="255">

					</div>

					<div class="voc_user_details">
						<img src="<?php echo URL::base() ?>images/notice_for_preview.png" align="absmiddle">&nbsp;Remember to save this profile before previewing email notifications

						<div class="preview-mail"><a href="javascript:;">Preview</a></div>
						<div class="clear"></div>
						<div class="preview-mail-list">
							<div id="thirty_days_notification">30 day expiry email notification</div>
							<div id="expired_notification">Expired email notification</div>
						</div>
					</div>
				</div>

				<div class="clear"></div>
			</div>

			<!-- start of tiles with order -->
			<?php


			$tiles_order = array();

			//remove placeholder order
			for ($j = 0; $j < count($tiles_order_ori); $j++) {
				if ($tiles_order_ori[$j] != "9")
					array_push($tiles_order, $tiles_order_ori[$j]);
			}


			$form_builder_index = array_search('8', $tiles_order);


			for ($t = 0; $t < count($tiles_order); $t++) {
				switch ($tiles_order[$t]) {
					case "1": //logo
			?>
						<div class="SectionTitleBox sortables" order-id="1">
							<div class="SectionTitle">Logo
								<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
								<div class="help-div" style="margin-left: 50px;"><a href="https://www.youtube.com/embed/hGrBYsys2Oo?rel=0">Video Tutorial</a>
									<div class="arrow-bg"></div>
								</div>
								<img src="<?php echo url::base(); ?>images/drag.png" class="drag-icon" />
							</div>
							<ul class="form-view clearfix">
								<li>
									<label>Upload Company Logo: <span class="hint-info">(File type JPEG, PNG, GIF) (Max file size <?php echo UPLOAD_MAX_FILESIZE; ?>)</span></label>
									<iframe src="<?php echo url::base(); ?>test/logoUpload?profile_id=<?php echo $profile_id; ?>&type_id=<?php echo $type_id; ?>" id="logoUpload" scrolling="no" frameborder="0" width="400" height="50"></iframe>

								</li>
							</ul>
						</div>

					<?php
						break;
					case "2": //title bar
					?>
						<div class="SectionTitleBox sortables" order-id="2">
							<div class="SectionTitle">Title Bar
								<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />

								<img src="<?php echo url::base(); ?>images/drag.png" class="drag-icon" />
								<div class="enable_title_bar"><input type="checkbox" value="1" name="voc_title_bar_enable" checked="checked" id="voc_title_bar_enable"> Enable</div>
							</div>

							<div class="form-view clearfix">
								<div class="voc_user_details">
									<label>Text:</label>
									<textarea name="voc_title_bar_text" id="voc_title_bar_text"></textarea>
									<script>
										CKEDITOR.replace('voc_title_bar_text', {
											toolbar: 'custom1',
											enterMode: CKEDITOR.ENTER_BR
										});
									</script>
								</div>


								<div class="voc_user_details">
									<label>Title Bar Background Colour:</label>
									<input type="text" name="voc_title_bar_colour" style="width:100px" id="voc_title_bar_colour" class="text-fi color" value="777777">

								</div>
							</div>

						</div>

					<?php
						break;
					case "3": //Profile Picture
					?>

						<div class="SectionTitleBox sortables" order-id="3">
							<div class="SectionTitle">Profile Picture
								<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />

								<img src="<?php echo url::base(); ?>images/drag.png" class="drag-icon" />
							</div>
							<ul class="form-view clearfix">
								<li>
									<label>Upload a picture: <span class="hint-info">(File type JPEG, PNG, GIF) (Max file size <?php echo UPLOAD_MAX_FILESIZE; ?>)</span></label>
									<iframe src="<?php echo url::base(); ?>test/pictureUpload_voc?profile_id=<?php echo $profile_id; ?>&type_id=<?php echo $type_id; ?>" id="imageUpload_voc" scrolling="no" frameborder="0" height="50" width="400"></iframe>
								</li>
							</ul>

						</div>

					<?php
						break;
					case "4": //Profile Information
					?>
						<div class="SectionTitleBox sortables" order-id="4">
							<div class="SectionTitle">Profile Information
								<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />

								<img src="<?php echo url::base(); ?>images/drag.png" class="drag-icon" />
							</div>
							<div class="form-view clearfix">
								<div class="voc_user_details" style="width:50%;float:left;">
									<label>First Name</label>
									<input type="text" name="voc_first_name" id="voc_first_name" style="width:200px" class="text-fi" value="" maxlength="200">
								</div>
								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Last Name</label>
									<input type="text" name="voc_last_name" id="voc_last_name" style="width:216px" class="text-fi" value="" maxlength="200">
								</div>
								<div class="clear"></div>

								<div class="voc_user_details">
									<label>Address</label>
									<input type="text" name="voc_address" id="voc_address" class="text-fi" value="" maxlength="200">
								</div>

								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Town</label>
									<input type="text" name="voc_town" id="voc_town" style="width:200px" class="text-fi" value="" maxlength="50">
								</div>
								<div class="voc_user_details" style="width:50%;float:left;">
									<label>State/Territory</label>
									<input type="text" name="voc_state" id="voc_state" style="width:216px" class="text-fi" value="" maxlength="50">
								</div>
								<div class="clear"></div>

								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Telephone No.</label>
									<input type="text" name="voc_phone" id="voc_phone" style="width:200px" class="text-fi" value="" maxlength="30">
								</div>
								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Date of Birth</label>
									<input type="text" name="voc_dob" id="voc_dob" style="width:216px" class="text-fi" value="" maxlength="10">
								</div>
								<div class="clear"></div>

								<div class="voc_user_details">
									<label>Known Allergies/Medical Conditions</label>
									<textarea rows="3" name="voc_known_allergies" id="voc_known_allergies"></textarea>
								</div>

								<div class="voc_user_details" style="width:50%;">
									<label>Blood Type</label>
									<input type="text" name="voc_blood_type" id="voc_blood_type" style="width:200px" class="text-fi" value="" maxlength="5">
								</div>

								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Next of Kin</label>
									<input type="text" name="voc_next_of_kin" id="voc_next_of_kin" style="width:200px" class="text-fi" value="" maxlength="50">
								</div>
								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Contact Telephone No.</label>
									<input type="text" name="voc_contact_phone" id="voc_contact_phone" style="width:216px" class="text-fi" value="" maxlength="30">
								</div>
								<div class="clear"></div>

								<div class="voc_user_details">
									<label>Employer</label>
									<input type="text" name="voc_employer" id="voc_employer" class="text-fi" value="" maxlength="200">
								</div>

								<div class="voc_user_details">
									<label>Address</label>
									<input type="text" name="voc_emp_address" id="voc_emp_address" class="text-fi" value="">
								</div>

								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Town</label>
									<input type="text" name="voc_emp_town" id="voc_emp_town" style="width:200px" class="text-fi" value="" maxlength="50">
								</div>
								<div class="voc_user_details" style="width:50%;float:left;">
									<label>State/Territory</label>
									<input type="text" name="voc_emp_state" id="voc_emp_state" style="width:216px" class="text-fi" value="" maxlength="50">
								</div>
								<div class="clear"></div>

								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Telephone No.</label>
									<input type="text" name="voc_emp_phone" id="voc_emp_phone" style="width:200px" class="text-fi" value="" maxlength="30">
								</div>
								<div class="clear"></div>
							</div>
						</div>
					<?php
						break;
					case "5": // documents
					?>
						<div class="SectionTitleBox sortables" order-id="5">
							<div class="SectionTitle">Document Upload
								<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />

								<img src="<?php echo url::base(); ?>images/drag.png" class="drag-icon" />
							</div>
							<div class="form-view clearfix">
								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Document Name</label>
									<input type="text" name="doc_name[]" style="width:200px" class="doc_name text-fi" value="" maxlength="200">
								</div>
								<div class="voc_user_details" style="width:50%;float:left;">
									<label>Expiry Date</label>
									<input type="text" name="doc_expiry_date[]" style="width:216px" class="doc_expiry text-fi" value="" maxlength="10">
								</div>
								<div class="clear"></div>

								<div>
									<label>Upload a Document: <span class="hint-info">(File type DOC, DOCX, PDF, GIF, JPEG, PNG) (Max file size <?php echo UPLOAD_MAX_FILESIZE; ?>)</span></label>
								</div>
								<div class="voc_doc_upload">
									<input type="file" name="document_file[]">
								</div>

								<div class="add_another_voc_file">
									<a onclick="add_another_file('<?php echo UPLOAD_MAX_FILESIZE; ?>')" href="javascript:;">
										Add Another<img src="<?php echo URL::base() ?>images/expand_window.png" id="expand_reduce_img" width="18" align="absmiddle" alt="">
									</a>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					<?php
						break;

					case "6": //Set Code Type
					?>
						<div class="SectionTitleBox sortables" order-id="6">
							<div class="SectionTitle">Set Code Type
								<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
								<div class="help-div" style="margin-left: 130px;"><a href="https://www.youtube.com/embed/jCeyQOfm7uc?rel=0">Video Tutorial</a>
									<div class="arrow-bg"></div>
								</div>
							</div>
							<ul class="form-view clearfix">
								<li>
									<label style="float: left;">Code Type:</label>
									<div style="float: left; width: 350px;">
										<?php echo Form::radio('code_type', '0', ((!isset($post["code_type"]) ||  (isset($post["code_type"]) && $post["code_type"] == 0)) ? true : false), array('class' => 'radi')) ?>
										<?php echo Form::label('code_type_qr', 'QR Code', array('style' => 'display: inline;')) ?>
										<?php echo Form::radio('code_type', '1', ((isset($post["code_type"]) && $post["code_type"] == 1) ? true : false), array('class' => 'radi')) ?>
										<?php echo Form::label('code_type_dm', 'Data Matrix', array('style' => 'display: inline;')) ?>
									</div>
								</li>
							</ul>
						</div>
					<?php
						break;
					case "7": //User Access Security
					?>
						<div class="SectionTitleBox sortables" order-id="7">
							<div class="SectionTitle">User Access Security
								<img src="<?php echo url::base(); ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
								<div class="help-div" style="margin-left: 184px;"><a href="https://www.youtube.com/embed/KcXJnxuMVyc?rel=0">Video Tutorial</a>
									<div class="arrow-bg"></div>
								</div>
							</div>
							<ul class="form-view clearfix">
								<li>
									<label style="float: left;">Password protect?:</label>
									<div style="float: left; width: 350px;">
										<?php echo Form::radio('protect', '1', ((isset($post["protect"]) && $post["protect"] == 1) ? true : false), array('class' => 'radi')) ?>
										<?php echo Form::label('protectyes', 'Yes', array('style' => 'display: inline;')) ?>
										<?php echo Form::radio('protect', '0', ((!isset($post["protect"]) ||  (isset($post["protect"]) && $post["protect"] == 0)) ? true : false), array('class' => 'radi')) ?>
										<?php echo Form::label('protectno', 'No', array('style' => 'display: inline;')) ?>
									</div>
								</li>

								<li>
									<label style="float: left;">Password:</label>
									<div>
										<?php echo Form::input('password', isset($post['password']) ? $post['password'] : '', array('id' => 'password', 'class' => 'text-fi')) ?>
										<?php echo Form::label('password', isset($errors['password']) ? $errors['password'] : '', array('class' => 'error', 'generated' => 'true')) ?>
									</div>
								</li>

							</ul>
						</div>
						<?php
						break;

					case "8": //form builder
						if ((count($tiles_order) - 1) != $form_builder_index) //form builder is not last in orders i.e. not in  right part
						{
						?>
							<div class="sortables" order-id="8">
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
										<iframe id="iframe_frm_builder" width="448" frameborder="no" height="1114" scrolling="no" src="<?php echo URL::base(); ?>formbuilder/index/?profile_id=<?php echo $profile_id; ?>&enable_form_analytics=0"></iframe>
									</div>

									<div class="clear">&nbsp;</div>

								</div>
								<div class="footer-rouded"><span class="expand-reduce">Expand Window</span> <img src="<?php echo URL::base() ?>images/expand_window.png" class="expand-reduce" id="expand_reduce_img" width="25" align="absmiddle" alt=""> </div>
								<div class="clear">&nbsp;</div>
							</div>
			<?php
						} //end if( (count($tiles_order)-1)!=$form_builder_index)

						break;
				} //end switch ($tiles_order[$t])

			} //end for loop
			?>



			<!-- end of tiles with order -->


			<ul class="form-view clearfix">
				<li>
					<input name="save" id="save" type="submit" class="save" value="&nbsp;&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;&nbsp;" onclick='javascript: beforeSave();'>
				</li>

			</ul>
		</section>

		<section class="add-form-right connectedSortable">
			<!-- ADD FORM RIGHT -->

			<section class="iphone-preview">
			</section>

			<div class="clear"></div>

			<?php
			if ((count($tiles_order) - 1) == $form_builder_index) //i.e. form builder is last in orders
			{
			?>
				<div class="sortables" order-id="8">
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
							<iframe id="iframe_frm_builder" width="448" frameborder="no" height="1114" scrolling="no" src="<?php echo URL::base(); ?>formbuilder/index/?profile_id=<?php echo $profile_id; ?>&enable_form_analytics=0"></iframe>
						</div>

						<div class="clear">&nbsp;</div>

					</div>
					<div class="footer-rouded"><span class="expand-reduce">Expand Window</span> <img src="<?php echo URL::base() ?>images/expand_window.png" class="expand-reduce" id="expand_reduce_img" width="25" align="absmiddle" alt=""> </div>
					<div class="clear">&nbsp;</div>
				</div>
			<?php
			} //end of if( (count($tiles_order)-1)==$form_builder_index)

			?>
			<!--placeholder order-->
			<div class="sortables" style="height: 50px;" order-id="9"></div>


			<?php /* <section class="button-section">
    <a class="green-btn">Download PDF</a>
    <a class="gray-btn">Order Label</a>
    </section> */ ?>

		</section><!-- /ADD FORM RIGHT -->

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
		});
	</script>
	<script type="text/javascript">
		$(document).ready(function() {

			$('.add-form-left #iframe_frm_builder').attr('width', '498');



			//adjust width of certain ele when iframe loaded depends on left or right part
			$('#iframe_frm_builder').load(function() {
				var ifrm = document.getElementById('iframe_frm_builder');

				var doc = ifrm.contentDocument ? ifrm.contentDocument : ifrm.contentWindow.document;

				var drop_here_title = doc.getElementById('drop-here-title');
				var div_drop_area = doc.getElementById('div_drop_area');

				if ($('.add-form-right #iframe_frm_builder').length)
					$(drop_here_title).css('width', 440);

				if ($('.add-form-left #iframe_frm_builder').length) {
					$(drop_here_title).css('width', 468);
					$(div_drop_area).css('width', 480);
				}

			}); //end of adjust width of certain ele when iframe loaded depends on left or right part   



			//re-order tiles
			$("#frmvoc").sortable({
				connectWith: ".connectedSortable",
				items: "div.sortables",
				sort: function() {
					// gets added unintentionally by droppable interacting with sortable
					// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
					//$( this ).removeClass( "ui-state-default" );

				},
				start: function(event, ui) {

					// handle ckeditor issue
					var textareaId = ui.item.find('textarea').attr('id');

					if (typeof textareaId != 'undefined' && textareaId == "txtdesc") {
						var editorInstance = CKEDITOR.instances[textareaId];
						if (editorInstance) {
							editorInstance.destroy();
							CKEDITOR.remove(textareaId);
						}
					}

					if (typeof textareaId != 'undefined' && textareaId == "txtdesc2") {
						var editorInstance = CKEDITOR.instances[textareaId];
						if (editorInstance) {
							editorInstance.destroy();
							CKEDITOR.remove(textareaId);
						}
					}

					if (typeof textareaId != 'undefined' && textareaId == "voc_title_bar_text") {
						var editorInstance = CKEDITOR.instances[textareaId];
						if (editorInstance) {
							editorInstance.destroy();
							CKEDITOR.remove(textareaId);
						}
					}
				},
				stop: function(event, ui) {


					// handle ckeditor issue
					var textareaId = ui.item.find('textarea').attr('id');
					if (typeof textareaId != 'undefined' && textareaId == "txtdesc") {
						CKEDITOR.replace(textareaId);
					}

					if (typeof textareaId != 'undefined' && textareaId == "txtdesc2") {
						CKEDITOR.replace(textareaId);
					}

					if (typeof textareaId != 'undefined' && textareaId == "voc_title_bar_text") {
						CKEDITOR.replace(textareaId, {
							toolbar: 'custom1',
							enterMode: CKEDITOR.ENTER_BR
						});
					}
				},
				update: function(event, ui) {
					var tilesOrder = $(this).sortable('toArray', {
						attribute: 'order-id'
					}).toString();


					//save order in hidden
					document.getElementById('hdn_order').value = tilesOrder;

					$('.add-form-right #iframe_frm_builder').attr('width', '448');
					$('.add-form-left #iframe_frm_builder').attr('width', '498');

					//adjust width of certain ele when iframe loaded depends on left or right part
					$('#iframe_frm_builder').load(function() {
						var ifrm = document.getElementById('iframe_frm_builder');

						var doc = ifrm.contentDocument ? ifrm.contentDocument : ifrm.contentWindow.document;

						var drop_here_title = doc.getElementById('drop-here-title');
						var div_drop_area = doc.getElementById('div_drop_area');

						if ($('.add-form-right #iframe_frm_builder').length) {
							$(drop_here_title).css('width', 440);
							$(div_drop_area).css('width', 430);
						}

						if ($('.add-form-left #iframe_frm_builder').length) {
							$(drop_here_title).css('width', 468);
							$(div_drop_area).css('width', 480);
						}


					}); //end of adjust width of certain ele when iframe loaded depends on left or right part   
				}

			});
			//re-order tiles end

			$("#frmvoc").validate({
				rules: {
					logo_file: {
						accept: 'png|jpg|jpeg|gif'
					},
					txt_code_profile_name: "required"
				},
				errorElement: "label",
				messages: {
					logo_file: {
						accept: "Only png jpg jpeg or gif file is allowed"
					},
					txt_code_profile_name: "Please provide code profile name"
				},
				submitHandler: function(form) {
					var fileArray = document.getElementsByName('docu_file[]');
					var txtArray = document.getElementsByName('docu_name[]');
					for (var i = 0; i < fileArray.length; i++) {
						if (fileArray[i].value.split(" ").join("") != "" && txtArray[i].value.split(" ").join("") == "") {
							alert("Please enter document name for uploaded document.");
							txtArray[i].focus();
							return false;
						}
					}

					activation_start_date = $("#activation_start_date").val();
					activation_end_date = $("#activation_end_date").val();
					hdn_exp_date = $("#hdn_exp_date").val();
					free_code = $("#free_code").val();
					renewal_required = $("#renewal_required").val();

					voc_first_name = $.trim($('#voc_first_name').val());
					voc_last_name = $.trim($('#voc_last_name').val());

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



					var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

					voc_user_email_arr = document.getElementsByName('voc_user_email[]');
					voc_user_password_arr = document.getElementsByName('voc_user_password[]');


					for (i = 0; i < (voc_user_email_arr.length); i++) {
						if (voc_user_email_arr[i].value.split(" ").join("") != "") {
							if (!emailRegexStr.test(voc_user_email_arr[i].value)) {
								alert('Enter a valid user email.');
								return false;
							} else {
								if (voc_user_password_arr[i].value.split(" ").join("") == "") {
									alert('Please enter password.');
									return false;
								}

								if (voc_user_password_arr[i].value.length < 6) {
									alert('Please enter minimum 6 characters password.');
									return false;
								}
							}

						}
					}

					voc_recipient_email_arr = document.getElementsByName('voc_recipient_email[]');

					voc_recipient_email_cnt = 0;
					for (i = 0; i < (voc_recipient_email_arr.length); i++) {
						if (voc_recipient_email_arr[i].value.split(" ").join("") != "") {
							if (!emailRegexStr.test(voc_recipient_email_arr[i].value)) {
								alert('Enter a valid recipient email.');
								return false;
							} else {
								voc_recipient_email_cnt++;
							}

						}
					}

					if (voc_recipient_email_cnt == 0) {
						alert('Enter at least one recipient email.');
						return false;
					}


					if ($('#voc_title_bar_enable').prop('checked') == true) {
						voc_title_bar_text_val = CKEDITOR.instances['voc_title_bar_text'].getData();
						if (voc_title_bar_text_val.split(" ").join("") == "") {
							alert('Please enter title bar text.');
							return false;
						}
					}



					if (voc_first_name == "") {
						alert("Please enter first name.");
						$('#voc_first_name').focus();
						return false;
					}

					if (voc_last_name == "") {
						alert("Please enter last name.");
						$('#voc_last_name').focus();
						return false;
					}


					doc_name_arr = document.getElementsByName('doc_name[]');
					document_file_arr = document.getElementsByName('document_file[]');

					for (i = 0; i < (doc_name_arr.length); i++) {
						if (doc_name_arr[i].value.split(" ").join("") != "" && document_file_arr[i].value.split(" ").join("") == "") {
							alert('Please upload file.');
							return false;
						}

						if (doc_name_arr[i].value.split(" ").join("") == "" && document_file_arr[i].value.split(" ").join("") != "") {
							alert('Please enter document name.');
							return false;
						}

						if (document_file_arr[i].value.split(" ").join("") != "") {
							file_name_arr = document_file_arr[i].value.split(".");
							ext = file_name_arr[file_name_arr.length - 1];

							if (ext.toLowerCase() != "doc" && ext.toLowerCase() != "docx" && ext.toLowerCase() != "pdf" && ext.toLowerCase() != "jpeg" && ext.toLowerCase() != "jpg" && ext.toLowerCase() != "gif" && ext.toLowerCase() != "png") {
								alert("Upload .doc, .docx, .pdf, .jpeg, .png or .gif file only");
								return false;
							}

						}
					}


					//check for recipient
					iframe = document.getElementById('iframe_frm_builder');
					form_iframe = iframe.contentDocument.frm;
					email_arr = '';
					form_name = '';


					var x = iframe.contentDocument.getElementsByName("email_recipient[]");

					email_tag = "";
					var y = iframe.contentDocument.getElementsByName("email_tag");
					if (y != "") {
						email_tag = y[0].value;
					}

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
	<p> You can not add VOC.kindly purchase the code.</p>
<?php } ?>