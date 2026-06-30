<script>
	$(document).ready(function() {
		$(".iframe1").colorbox({
			iframe: true,
			width: "770",
			height: "90%"
		});
	});

	function add_form(current_profile_id, form_profile_id) {
		$("#form_exist #progress_bar").show();

		$.ajax({
			type: "GET",
			url: "<?php echo URL::base(); ?>formbuilder/add_existing_form/",
			dataType: "html",
			data: {
				"current_profile_id": current_profile_id,
				"form_profile_id": form_profile_id
			},
			success: function(data) {},
			complete: function() {
				document.getElementById('iframe_frm_builder').contentDocument.location.reload(true);
				setTimeout(function() {
					check_doc_status();
				}, 5000);
			}


		});


	}


	function add_form_from_library(current_profile_id, form_id) {

		$("#form_exist #progress_bar").show();

		$.ajax({
			type: "GET",
			url: "<?php echo URL::base(); ?>formbuilder/add_form_from_library/",
			dataType: "html",
			data: {
				"current_profile_id": current_profile_id,
				"form_id": form_id
			},
			success: function(data) {},
			complete: function() {
				document.getElementById('iframe_frm_builder').contentDocument.location.reload(true);
				setTimeout(function() {
					check_doc_status();
				}, 5000);
			}
		});


	}

	function delete_form_from_library(form_id) {
		if (confirm("Are you sure you want to remove this form?")) {
			$("#form_exist #progress_bar2").show();

			$.ajax({
				type: "GET",
				url: "<?php echo URL::base(); ?>formbuilder/delete_form_from_library/",
				dataType: "html",
				data: {
					'form_id': form_id
				},
				success: function(data) {},
				complete: function() {
					$('#form_' + form_id).remove();
					$('#clear_' + form_id).remove();
					$("#form_exist #progress_bar2").hide();
				}
			});
		}
	}

	function show_preview_form(form_id, form_title) {
		$('#form_title').html(form_title);

		$("#form_exist #progress_bar2").show();

		$.ajax({
			type: "GET",
			url: "<?php echo URL::base(); ?>formbuilder/get_data_for_form_preview/",
			dataType: "html",
			data: {
				'form_id': form_id
			},
			success: function(data) {

				$("#form_content").html(data);
			},
			complete: function() {
				$("#form_exist #progress_bar2").hide();
				$('#form_exist #preview_form').show();
				$('#form_exist #form_listing').hide();
				$("#form_exist :radio").uniform();
				$("#form_exist :checkbox").uniform();
				$("#form_exist :text").uniform();
				$("#form_exist textarea").uniform();

			}
		});


	}

	function go_back() {
		$('#preview_form').hide();
		$('#form_listing').show();

	}

	function login_other_user() {
		var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

		txt_other_user_email = $.trim($('#txt_other_user_email').val());
		txt_other_user_pass = $.trim($('#txt_other_user_pass').val());

		if (txt_other_user_email == "") {
			alert('Enter email address.');
			$('#txt_other_user_email').focus();
			return false;

		} else if (!emailRegexStr.test(txt_other_user_email)) {
			alert('Enter a valid email.');
			$('#txt_other_user_email').focus();
			return false;
		} else if (txt_other_user_pass == "") {
			alert('Enter password.');
			$('#txt_other_user_pass').focus();
			return false;

		} else {
			$("#form_exist #progress_bar2").show();

			$.ajax({
				type: "GET",
				url: "<?php echo URL::base(); ?>welcome/login_from_other_user/",
				dataType: "html",
				data: {
					'email': txt_other_user_email,
					'password': txt_other_user_pass
				},
				success: function(data) {
					$("#form_exist #progress_bar2").hide();
					if (data > 0) {
						user_id = data;
						$.ajax({
							type: "GET",
							url: "<?php echo url::base(); ?>otheruserform/?profile_id=<?php echo $profile_id; ?>&user_id=" + user_id,
							dataType: "html",
							data: {},
							success: function(data1) {
								$("#tabed_content_other").html(data1);
								$("#tabed_content_other #tabs").tabs({
									"selected": 1,
									disabled: [0, 2]
								}); //for tab view /// 

								$("#tabed_content_main").hide();
								$("#tabed_content_other").show();

							}
						});
						/*new_val= $('#new_page').attr('href')+'&user_id='+user_id;
						
						$('#new_page').attr('href',new_val);
						$('#new_page').click();*/
					} else //some error occur like invalid login,etc
						alert(data);

				},

			});

		}

	}



	function check_doc_status() {

		if (document.getElementById('iframe_frm_builder').contentDocument.readyState == "complete") {
			$("#form_exist #progress_bar").hide();
			$.colorbox.close();
		} else {
			setTimeout(function() {
				check_doc_status();
			}, 2000);
		}
	}
</script>

<div style="display:none">
	<a class='forms_existing' href="#form_exist" style="padding: 0;" id="forms_existing_tag">
		<input class="forms_existing" type="button" id="registerbutton" value="Register div" />
	</a>
</div>
<div style='display:none;'>

	<div id='form_exist' style='padding:10px; background:#fff;'>
		<div id="progress_bar">
			<img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait. Applying Form to Profile...
		</div>
		<div id="progress_bar2">
			<img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait...
		</div>


		<div id="form_listing" align="left" style="vertical-align:middle;padding-top: 0px;">

			<fieldset class="existing-form">
				<legend><img src="<?php print URL::base(); ?>images/logo.png" alt="" width="144" /></legend>
				<br />

				<br />

				<div id="tabed_content_main">
					<!-- main content of current user -->
					<div id="tabs">
						<!--The user can select an existing form from another code profile-->
						<ul>
							<li><a href="#tabs-1">Select From existing code profile</a></li>
							<li><a href="#tabs-2">Select from library</a></li>
							<li><a href="#tabs-3">Select from another account</a></li>
						</ul>
						<div id="tabs-1">
							<?php $sr_no = 1;


							foreach ($existing_forms_arr as $existing_forms) {
							?>
								<div class="form-list">
									<div class="sr-no"><?php echo $sr_no; ?></div>
									<div class="form-name">
										<?php if ($existing_forms["form_title"] != "")
											echo $existing_forms["form_title"];
										else
											echo "&nbsp;";
										?>
									</div>
									<div class="form-name">
										Profile # <?php echo $existing_forms["id"]; ?>
									</div>
									<div class="form-select">
										<input type="button" class="save" onclick="add_form('<?php echo $profile_id; ?>','<?php echo $existing_forms["id"]; ?>')" value="SELECT" />
									</div>
								</div>
								<div class="clear">&nbsp;</div>
							<?php $sr_no++;
							} ?>
						</div>
						<div id="tabs-2">
							<!--The user can select a form saved in their Form Library-->
							<?php $sr_no = 1;


							foreach ($form_library_arr as $form_library) {
							?>
								<div class="form-list" id="form_<?php echo $form_library["form_id"]; ?>">
									<div class="sr-no"><?php echo $sr_no; ?></div>
									<div class="form-name" style="width: 64%;">
										<?php if ($form_library["form_title"] != "")
											echo $form_library["form_title"];
										else
											echo "&nbsp;";
										?>
									</div>

									<div class="form-select">
										<img align="absmiddle" src="<?php echo url::base(); ?>images/select_icon.png" alt="Add Form" title="Add Form" onclick="add_form_from_library('<?php echo $profile_id; ?>','<?php echo $form_library["form_id"]; ?>')" />
										&nbsp;<img align="absmiddle" src="<?php echo url::base(); ?>images/preview_form.png" alt="Preview Form" title="Preview Form" onclick="show_preview_form('<?php echo $form_library["form_id"]; ?>','<?php echo $form_library["form_title"]; ?>')" />
										&nbsp;<img align="absmiddle" src="<?php echo url::base(); ?>images/delete_profile.png" alt="Delete Form" title="Delete Form" onclick="delete_form_from_library('<?php echo $form_library["form_id"]; ?>')" />
									</div>
								</div>
								<div class="clear" id="clear_<?php echo $form_library["form_id"]; ?>">&nbsp;</div>
							<?php $sr_no++;
							} ?>
						</div>
						<div id="tabs-3">
							<div id="login_form">
								<div class="clear">&nbsp;</div>
								<div class="label">Email</div>
								<div class="txtbox"><input type="text" name="txt_other_user_email" id="txt_other_user_email" maxlength="255" class="textbox" /></div>
								<div class="clear">&nbsp;</div>
								<div class="label">Password</div>
								<div class="txtbox"><input type="password" name="txt_other_user_pass" id="txt_other_user_pass" maxlength="255" class="textbox" /></div>
								<div class="clear">&nbsp;</div>
								<div style="width: 30%;float: right;"><input type="button" onclick="login_other_user()" class="save" value="Login" /></div>
								<div class="clear"></div>
							</div>
							<a href="<?php echo url::base(); ?>formbuilder/other_user_form?profile_id=<?php echo $profile_id; ?>" id="new_page" class="iframe1" style="display: none;"></a>

						</div>
					</div>
				</div>
				<div id="tabed_content_other" style="display: none;">
					<!-- other content of other user when Select from another account-->

				</div>
			</fieldset>


		</div>

		<div style="display: none" id="preview_form">
			<span class="logo_back">
				<img src="<?php echo url::base(); ?>images/go-back.png" height="40" align="absmiddle" onclick="go_back()" alt="Go Back" title="Go Back" />
				<span onclick="go_back()">&nbsp;Go Back</span>
			</span>
			<div class="clear">&nbsp;</div>
			<h2>Form Preview : <span id="form_title"></span></h2>

			<div id="form_content">

			</div>

			<div class="clear">&nbsp;</div>
			<span class="logo_back">
				<img src="<?php echo url::base(); ?>images/go-back.png" height="40" align="absmiddle" onclick="go_back()" alt="Go Back" title="Go Back" />
				<span onclick="go_back()">&nbsp;Go Back</span>
			</span>
		</div>


	</div>



</div>
<div style="clear: both;"></div>