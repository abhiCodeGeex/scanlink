<script>
	function show_color_picker(obj) {
		new jscolor.init();
		var col = new jscolor.color(obj);
		obj.color.showPicker();
	}

	/**
	 * Filament parent page has no jQuery. Always query parent DOM with this iframe's jQuery.
	 */
	function $p(selector) {
		try {
			if (window.parent && window.parent.document) {
				return $(selector, window.parent.document);
			}
		} catch (e) {}
		return $(selector);
	}

	function parentEnableFormChecked() {
		var el = $p('#enable_form').get(0);
		return !!(el && el.checked);
	}

	function notifyParentFormSaved() {
		try {
			window.parent.postMessage({
				type: 'scanlink-form-builder-saved',
				profileId: '<?php echo (int) $profile_id; ?>'
			}, window.location.origin);
		} catch (e) {}
	}

	var sorting_started = 0; // useful when prevent click

	$(function() {
		if ($('#enable_form_analytics').val() == 0) {
			$(".menu-form ul li ul li").draggable({
				appendTo: "body",
				helper: "clone",
				create: function(event, ui) {
					parent_ul_class = $(this).parent().attr('class');
					li_class = parent_ul_class + "-li";
					$(this).css("cursor", "move");
					$(this).attr("class", li_class);
				}
			});

			$("#drop_area ol").droppable({
				activeClass: "ui-state-default",
				hoverClass: "ui-state-hover",
				accept: ":not(.ui-sortable-helper)",
				over: function(event, ui) {

					parent_ul_class = ui.draggable.parent().attr('class');
					li_class = parent_ul_class + "-li";
					$('.ui-draggable-dragging').addClass(li_class);
				},
				drop: function(event, ui) {
					current_html = $(".placeholder").html();

					//$( this ).find( ".placeholder" ).remove();

					parent_ul_class = ui.draggable.parent().attr('class');
					li_class = parent_ul_class + "-li";

					question_type_id = ui.draggable.attr("ID");

					control_name = ui.draggable.text();

					date_curr = new Date();

					hours = date_curr.getHours();
					mins = date_curr.getMinutes();
					seconds = date_curr.getSeconds();

					random_id = hours + "" + mins + "" + seconds;

					counter = 1;

					box_class = parent_ul_class + '-bx';
					question_id = 0;

					html = '';

					if (question_type_id == 25) {
						html = html + '<div class="' + parent_ul_class + '-bx covid-checkin-header" id="' + random_id + '">';
						html = html + '<div class="' + parent_ul_class + '-first-box">';
						html = html + '<h1>' + control_name + '</h1>';
						html = html + '<a href="javascript:;" onclick="remove_div(' + random_id + ')"><img src="<?php echo rtrim(url('/'), '/'); ?>/form-builder/images/close-button.png" border="0" alt="Remove" title="Remove" /></a>';
					} else {
						html = html + '<div class="' + parent_ul_class + '-bx">';
						html = html + '<div class="' + parent_ul_class + '-first-box">';


						html = html + '<h1>' + control_name + '</h1>';
						html = html + '<a href="javascript:;" onclick="remove_div(' + random_id + ')"><img src="<?php echo rtrim(url('/'), '/'); ?>/form-builder/images/close-button.png" border="0" alt="Remove" title="Remove" /></a>';

					}

					if ((question_type_id >= 1 && question_type_id <= 9 && question_type_id != 2) || question_type_id == 16 || question_type_id == 18 || question_type_id == 19 || question_type_id == 22 || question_type_id == 23 || question_type_id == 24) {

						control_name = control_name.trim();
						box_class = box_class.trim();

						html = html + '<h2><span style="text-transform:capitalize;float:left;">Mandatory*</span>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="mandatory' + random_id + '" checked id="mandatory' + random_id + '" />&nbsp;&nbsp;<a href="javascript:;" onclick="append_div(' + random_id + ',&quot;textbox' + random_id + '&quot;,' + question_type_id + ',' + random_id + ',&quot;' + box_class + '&quot;,&quot;' + control_name + '&quot;,&quot;' + question_id + '&quot;)">save</a></h2>';
					} else if (question_type_id == 25) {
						control_name = control_name.trim();
						box_class = box_class.trim();

						html = html + '<div class="covid-checkin-header">';
						html = html + '<h2><a href="javascript:;" onclick="append_div(' + random_id + ',&quot;textbox' + random_id + '&quot;,' + question_type_id + ',' + random_id + ',&quot;' + box_class + '&quot;,&quot;' + control_name + '&quot;,&quot;' + question_id + '&quot;)">save</a></h2>';
						html = html + '</div>';
					} else {
						control_name = control_name.trim();
						box_class = box_class.trim();
						html = html + '<h2><a href="javascript:;" onclick="append_div(' + random_id + ',&quot;textbox' + random_id + '&quot;,' + question_type_id + ',' + random_id + ',&quot;' + box_class + '&quot;,&quot;' + control_name + '&quot;,&quot;' + question_id + '&quot;)">save</a></h2>';
					}

					html = html + '</div>';

					if ((question_type_id >= 1 && question_type_id <= 9 && question_type_id != 2) || question_type_id == 15 || question_type_id == 23) {
						html = html + '<p style="margin-top:50px;display: inline-flex; flex-direction: row; justify-content: flex-start; align-items: center; flex-wrap: wrap; padding: 5px;">';
						html = html + '	<image style="float:left;width:30;height:31;" src="../../images/form_submission.png">';
						html = html + '	<span>Record entry on Form Submission Log&nbsp;&nbsp;&nbsp;&nbsp;</span>';
						html = html + '	<input type="checkbox" name="is_logchecked' + random_id + '" id="is_logchecked' + random_id + '"/>';
						html = html + '	<input type="text" name="log_columntitle' + random_id + '" id="log_columntitle' + random_id + '" placeholder="Enter column title">';
						html = html + '</p>';
					}

					switch (question_type_id) {

						case "1": //text field - not required input box
						case "13": //line divide - not required input box
						case "14": // blank space - not required input box
						case "15": // comments - not required input box
						case "24": // Additional recipients - not required input box

							html = html + '<p><input type="text" id="textbox' + random_id + '" readonly name="textbox' + random_id + '" ></p>';
							break;
						case "2": //text
							html = html + '<textarea rows="1" id="textbox' + random_id + '" name="textbox' + random_id + '"></textarea>';
							break
						case "3": //Multiple choice
						case "4": //check box
						case "5": // Dropdown
							html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" >'; // dummy textbox
							html = html + '<p><input type="text" id="option_cntrl" class="first_ele" name="option_text' + random_id + '[]" placeholder="Enter Your Text"></p>';
							html = html + '<div id="option-list"><p class="add-other-option" ><a href="javascript:;" onclick="add_another_option(' + counter + ',' + random_id + ')">Add Another</a></p></div>';
							break;
						case "6": // number scale
							html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" >'; // dummy textbox
							html = html + '<p class="scale">From&nbsp;&nbsp;<input type="text" id="scale_from" class="first_ele" name="scale_from" maxlength="5" >&nbsp;&nbsp; To&nbsp;&nbsp;<input type="text" id="scale_to" maxlength="5" name="scale_to" ></p>';
							break;
						case "7": // grid
							html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" >'; // dummy textbox
							html = html + '<div class="row-label">Row Label</div><input type="text" class="first_ele" id="" name="row' + random_id + '[]" placeholder="Enter Your Text">';
							html = html + '<div id="grid_row"><p class="add-other-row" ><a href="javascript:;" onclick="add_another_row(' + counter + ',' + random_id + ')">Add Another Row</a></p></div>';
							html = html + '<div class="column-label">Column Label</div><input type="text" id="" name="column' + random_id + '[]" placeholder="Enter Your Text">';
							html = html + '<div id="grid_column"><p class="add-other-column" ><a href="javascript:;" onclick="add_another_column(' + counter + ',' + random_id + ')">Add Another Column</a></p></div>';
							break;
						case "8": // date
						case "9": // time
							html = html + '<p><input type="text" id="textbox' + random_id + '" readonly name="textbox' + random_id + '" ></p>'; // dummy textbox
							break;
						case "11": // image

							html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" >'; // dummy textbox
							html = html + '<div class="image-label">Image Title</div><input type="text" id="image_title' + random_id + '" name="image_title' + random_id + '" placeholder="Enter Your Text">';
							//html = html + '<div class="image-label">Select File</div><input type="file" id="image_file'+random_id+'" name="image_file'+random_id+'" >';
							html = html + '<div class="image-label">Select File<span class="hint-info">(File type JPG, JPEG, PNG, GIF)</span><input id="uploaded_file" type="hidden" value="" name="uploaded_file"></div>';
							html = html + '<iframe src="<?php echo url('/test/temp_image_upload'); ?>?profile_id=<?php echo $profile_id; ?>&random_id=' + random_id + '" id="logoUpload" scrolling="no" frameborder="0" width="94%" style="padding-left:10px" height="70"></iframe>';
							html = html + '<div class="image-label">Alignment :&nbsp;<input type="radio" id="" class="image_align" value="left" checked name="image_align' + random_id + '" >Left&nbsp;<input type="radio" id="" value="center" name="image_align' + random_id + '" >Center&nbsp;<input type="radio" id="" value="right" name="image_align' + random_id + '" >Right</div>';
							break;
						case "16": //signature
							html = html + '<p><input type="text" id="textbox' + random_id + '" name="textbox' + random_id + '" placeholder="Enter Your Text"></p>';
							html = html + '<div class="hazard-class"><div><input class="sign-text include-name" id="include_name"  type="checkbox"> Include Name</div></div>';
							html = html + '<div class="hazard-class"><div><input class="sign-text include-employer" id="include_employer" type="checkbox"> Include Employer/Company</div></div>';
							html = html + '<div class="hazard-class"><div><input class="sign-text include-email" id="include_email" type="checkbox"> Include Email</div></div>';
							html = html + '<div class="hazard-class"><div><input class="sign-text include-phone" id="include_phone" type="checkbox"> Include Phone</div></div>';
							break;
						case "20": // web link button
							html = html + '<input type="text" class="btn_text" id="textbox' + random_id + '" name="textbox' + random_id + '" placeholder="Button Text" ><br>';
							html = html + '<input type="text" class="btn_link" id="btn_link' + random_id + '" name="btn_link' + random_id + '" placeholder="Button Link URL (start with http:// or https://) " ><br>';
							html = html + '<input type="text" class="btn_colour color" id="btn_colour' + random_id + '" name="btn_colour' + random_id + '" onclick="show_color_picker(this)" maxlength="6" value="007A01" >';
							break;
						case "21": // Document Button
							html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" >'; // dummy textbox
							html = html + '<div class="image-label">Document Title</div><input type="text" id="doc_title' + random_id + '" name="doc_title' + random_id + '" placeholder="Enter Your Text">';
							html = html + '<div class="image-label">Select File<span class="hint-info">(File type  DOC, DOCX, PDF, JPG, GIF, JPEG)</span><input id="uploaded_doc' + random_id + '" type="hidden" value="" name="uploaded_doc"></div>';
							html = html + '<iframe src="<?php echo url('/test/temp_doc_upload'); ?>?profile_id=<?php echo $profile_id; ?>&random_id=' + random_id + '" id="docUpload" scrolling="no" frameborder="0" width="94%" style="padding-left:10px" height="70"></iframe>';
							html = html + '<input type="text" class="btn_colour color" id="doc_color' + random_id + '" name="doc_color' + random_id + '" onclick="show_color_picker(this)"  maxlength="6" value="007A01" >';
							break;
						case "22": // SWMS Hazard/Risk

							html = html + '<p><input type="text" id="textbox' + random_id + '" name="textbox' + random_id + '" placeholder="Enter Your Text"></p>';
							html = html + '<div class="image-label">Task | Activity</div>';
							html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';
							html = html + '<div class="image-label">Potential Hazards</div>';
							html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';
							html = html + '<div class="image-label">Risk Score (Before)</div>';
							html = html + '<div style="display: flex; width: 347px;">';
							html = html + '<div class="hazard-class" style="width: 100%;"><div class="selector"><span style="width: 126px;">Select</span></div></div>';

							// new file upload control
							html = html + '<div style="display: flex;align-items: center;min-width: 70px;margin-left:15px;"><img src="<?php echo rtrim(url('/'), '/'); ?>/form-builder/images/camera.png" style="height:25px;width:25px;" />&nbsp;<span stye="margin-top:5px !important;">Photo</span></div>';
							//new file upload control end

							html = html + '</div>';

							html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';
							html = html + '<div class="image-label">Risk Score (After)</div>';
							html = html + '<div class="hazard-class"><div class="selector"><span style="width: 126px;">Select</span></div></div>';
							break;
						case "23": // Document Menu
							html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" >'; // dummy textbox
							html = html + '<div class="image-label">Document Title <small>(Please separate each title with a comma)</small></div><input type="text" id="doc_title' + random_id + '" name="doc_title' + random_id + '" placeholder="Enter Your Text">';
							html = html + '<div class="image-label">Select File<span class="hint-info">(File type  DOC, DOCX, PDF, JPG, GIF)</span><input id="uploaded_doc' + random_id + '" type="hidden" value="" name="uploaded_doc"></div>';
							html = html + '<iframe src="<?php echo url('/test/temp_doc_multi_upload'); ?>?profile_id=<?php echo $profile_id; ?>&random_id=' + random_id + '" id="docUpload" scrolling="no" frameborder="0" width="94%" style="padding-left:10px" height="70"></iframe>';
							break;
						case "18": // Participant name

							html = html + '<p><input type="text" id="textbox' + random_id + '" name="textbox' + random_id + '" placeholder="Enter Your Text"></p>';
							html = html + '<div class="hazard-class"><div><input class="sign-text include-name" id="participant_include_signature" type="checkbox"> Include Signature</div></div>';
							//html = html + '<div class="hazard-class"><div><input class="sign-text include-name" id="participant_include_name"  type="checkbox"> Include Name</div></div>';
							html = html + '<div class="hazard-class"><div><input class="sign-text include-employer" id="participant_include_employer" type="checkbox"> Include Employer/Company</div></div>';
							break;
							// New code 19-03-2021
						case "25":
							html = html + '<textarea rows="1" id="textbox' + random_id + '" name="textbox' + random_id + '" ></textarea>'

							html = html + '<hr>';

							html = html + '<br>';
							html += '<div class="box-main">';

							html += '<div class="row">';
							html += '<div class="col">';
							html = html + '<div class="image-label text-center">Text colour</div>';
							html += '<div class="hazard-class">';

							html += '<input type="text" class="text-fi color {required:false}" value="#000000" name="text_color" id="text_color' + random_id + '" autocomplete="off">';


							html += '</div>';
							html += '</div>';

							html += '<div class="col">';
							html = html + '<div class="image-label text-center">Background color</div>';
							html += '<div class="hazard-class">';

							html += '<input type="text" class="text-fi color {required:false}" value="#ffffff" name="bg_color" id="bg_color' + random_id + '" autocomplete="off">';

							html += '</div>';
							html += '</div>';
							html += '</div>';

							html = html + '<p style="display: inline-flex; flex-direction: row; justify-content: flex-start; align-items: center; flex-wrap: wrap; padding: 10px 0px 20px 0px;">';
							html = html + '	<image style="float:left;width:30;height:31;" src="../../images/form_submission.png">';
							html = html + '	<span>Record entry on Form Submission Log&nbsp;&nbsp;&nbsp;&nbsp;</span>';
							html = html + '	<input type="checkbox" name="is_logchecked' + random_id + '" id="is_logchecked' + random_id + '" checked="checked"/>';

							html = html + '<div class="image-label">Visitor Name</div>';
							html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';

							html = html + '<div class="image-label ">Visitor Phone</div>';
							html = html + '<div class="hazard-class"><div class="text-box-black2 "></div></div>';

							html += '<div class="row">';
							html += '<div class="col">';
							html += '<div class="hazard-class">';
							html = html + '<div class="image-label">Date</div>';
							html = html + '<div class="hazard-class"><div class="text-box-black2 "></div></div>';
							html += '</div>';
							html += '</div>';

							html += '<div class="col">';
							html += '<div class="hazard-class">';
							html = html + '<div class="image-label">Time</div>';
							html = html + '<div class="hazard-class"><div class="text-box-black2 "></div></div>';
							html += '</div>';
							html += '</div>';
							html += '</div>';

							html = html + '<div class="image-label">Venue Name</div>';
							html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';

							html = html + '<div class="image-label">Venue address</div>';
							html = html + '<div class="hazard-class"><div class="text-box-black2 "></div></div>';

							html = html + '<div class="image-label">Location Description/Type	</div>';
							html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';
							html += '</div>';
							break;
						default:
							if (question_type_id != 25) {

								html = html + '<p><input type="text" id="textbox' + random_id + '" name="textbox' + random_id + '" placeholder="Enter Your Text"></p>';
							}
					}

					html = html + '</div>';


					//$( '<div class="'+parent_ul_class+'-box" id="'+random_id+'" >' ).html(html).appendTo( this );
					$('<div class="' + parent_ul_class + '-box" id="' + random_id + '" >').html(html).appendTo($(this).children('.placeholder'));

					// New code 19-03-2021
					if (question_type_id == '25') {
						var colinput = document.getElementById('text_color' + random_id);
						var col = new jscolor.color(colinput);

						var colinput = document.getElementById('bg_color' + random_id);
						var col = new jscolor.color(colinput);
					}

					if (question_type_id == "2" || question_type_id == '25') //enable ckeditor for text type
						CKEDITOR.replace('textbox' + random_id, {
							toolbar: 'custom',
							enterMode: CKEDITOR.ENTER_BR
						});

					$('.first_ele').focus();

					$(".ui-widget-content ol").css('height', $(".ui-widget-content ol").prop('scrollHeight'));

					$('.ui-widget-content').scrollTop($('.ui-widget-content').prop("scrollHeight"));

					setTimeout(function() {
						if ($p('span.expand-reduce').text() == "Reduce Window") {
							ul_scroll_height = $('.ui-droppable').prop('scrollHeight') + 10;
							$('#div_drop_area').css('height', ul_scroll_height);
							$p('#iframe_frm_builder').height(ul_scroll_height + $('.top-part').height());
						}
						$(":checkbox:not(.sign-text)").uniform();
					}, 3000);
				}
			}).sortable({
				items: "div.text-black:not(.hazard-class)",
				sort: function() {
					// gets added unintentionally by droppable interacting with sortable
					// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
					$(this).removeClass("ui-state-default");
				},
				update: function(event, ui) {
					$('.progressbar').show();
					sorting_started = 1;
					var theElementYouDragged = ui.item;

					if ($(theElementYouDragged).attr('ID').indexOf('question_div_') == "-1") // for new added ele
						current_ele_id = $(theElementYouDragged).children('.question_id_span').text();
					else
						current_ele_id = $(theElementYouDragged).attr('ID');

					if ($(theElementYouDragged).next('.text-black').length > 0 && $(theElementYouDragged).next('.text-black').attr('ID').indexOf('question_div_') == "-1") // for new added ele
						next_ele_id = $(theElementYouDragged).next('.text-black').children('.question_id_span').text();
					else
						next_ele_id = $(theElementYouDragged).next('.text-black').attr('ID');

					$.ajax({
						type: "GET",
						url: "<?php echo url('/portal/legacy-form-builder/reorder-element'); ?>",
						dataType: "html",
						data: {
							"profile_id": '<?php echo $profile_id; ?>',
							'current_ele_id': current_ele_id,
							"next_ele_id": next_ele_id
						},
						success: function(data) {
							sorting_started = 0; //as sorting complete
						},
						complete: function() {
							$('.progressbar').hide();
						}
					});
				}
			}); // end droppable
		} // end of if($('#enable_form_analytics').val()==0)
		else { //enable drag, drop and sort for participant name
			$(".menu-form ul li ul li#18").draggable({
				appendTo: "body",
				helper: "clone",
				create: function(event, ui) {
					parent_ul_class = $(this).parent().attr('class');
					li_class = parent_ul_class + "-li";

					$(this).css("cursor", "move");
					$(this).attr("class", li_class);
				}
			});
			$("#drop_area ol").droppable({
				activeClass: "ui-state-default",
				hoverClass: "ui-state-hover",
				accept: ":not(.ui-sortable-helper)",
				over: function(event, ui) {
					parent_ul_class = ui.draggable.parent().attr('class');
					li_class = parent_ul_class + "-li";

					$('.ui-draggable-dragging').addClass(li_class);
				},
				drop: function(event, ui) {
					current_html = $(".placeholder").html();

					//$( this ).find( ".placeholder" ).remove();

					parent_ul_class = ui.draggable.parent().attr('class');
					li_class = parent_ul_class + "-li";

					question_type_id = ui.draggable.attr("ID");

					control_name = ui.draggable.text();

					date_curr = new Date();

					hours = date_curr.getHours();
					mins = date_curr.getMinutes();
					seconds = date_curr.getSeconds();

					random_id = hours + "" + mins + "" + seconds;

					counter = 1;

					box_class = parent_ul_class + '-bx';
					question_id = 0;

					html = '';
					html = html + '<div class="' + parent_ul_class + '-bx">';
					html = html + '<div class="' + parent_ul_class + '-first-box">';
					html = html + '<h1>' + control_name + '</h1>';
					html = html + '<a href="javascript:;" onclick="remove_div(' + random_id + ')"><img src="<?php echo rtrim(url('/'), '/'); ?>/form-builder/images/close-button.png" border="0" alt="Remove" title="Remove" /></a>';

					if (question_type_id >= 1 && question_type_id <= 9 && question_type_id != 2)
						html = html + '<h2><span style="text-transform:capitalize;float:left;">Mandatory</span>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="mandatory' + random_id + '" checked id="mandatory' + random_id + '" />&nbsp;&nbsp;<a href="javascript:;" onclick="append_div(' + random_id + ',&quot;textbox' + random_id + '&quot;,' + question_type_id + ',' + random_id + ',&quot;' + box_class + '&quot;,&quot;' + control_name + '&quot;,&quot;' + question_id + '&quot;)">save</a></h2>';
					else
						html = html + '<h2><a href="javascript:;" onclick="append_div(' + random_id + ',&quot;textbox' + random_id + '&quot;,' + question_type_id + ',' + random_id + ',&quot;' + box_class + '&quot;,&quot;' + control_name + '&quot;,&quot;' + question_id + '&quot;)">save</a></h2>';
					html = html + '</div>';

					if (question_type_id == 18) {
						html = html + '<p><input type="text" id="textbox' + random_id + '" name="textbox' + random_id + '" ></p>';
						html = html + '<div class="hazard-class"><div><input class="sign-text include-name" id="participant_include_signature"  type="checkbox"> Include Signature</div></div>';
						html = html + '<div class="hazard-class"><div><input class="sign-text include-employer" id="participant_include_employer" type="checkbox"> Include Employer/Company</div></div>';
					}




					html = html + '</div>';

					//$( '<div class="'+parent_ul_class+'-box" id="'+random_id+'" >' ).html(html).appendTo( this );
					$('<div class="' + parent_ul_class + '-box" id="' + random_id + '" >').html(html).appendTo($(this).children('.placeholder'));

					$(".ui-widget-content ol").css('height', $(".ui-widget-content ol").prop('scrollHeight'));

					$('.ui-widget-content').scrollTop($('.ui-widget-content').prop("scrollHeight"));

					setTimeout(function() {
						if ($p('span.expand-reduce').text() == "Reduce Window") {
							ul_scroll_height = $('.ui-droppable').prop('scrollHeight') + 10;
							$('#div_drop_area').css('height', ul_scroll_height);
							$p('#iframe_frm_builder').height(ul_scroll_height + $('.top-part').height());
						}
						$(":checkbox").uniform();
					}, 3000);



				}
			}).sortable({
				items: "div.text-black",
				sort: function() {
					// gets added unintentionally by droppable interacting with sortable
					// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
					$(this).removeClass("ui-state-default");
				},
				update: function(event, ui) {
					$('.progressbar').show();
					sorting_started = 1;
					var theElementYouDragged = ui.item;

					if ($(theElementYouDragged).attr('ID').indexOf('question_div_') == "-1") // for new added ele
						current_ele_id = $(theElementYouDragged).children('.question_id_span').text();
					else
						current_ele_id = $(theElementYouDragged).attr('ID');

					if ($(theElementYouDragged).next('.text-black').length > 0 && $(theElementYouDragged).next('.text-black').attr('ID').indexOf('question_div_') == "-1") // for new added ele
						next_ele_id = $(theElementYouDragged).next('.text-black').children('.question_id_span').text();
					else
						next_ele_id = $(theElementYouDragged).next('.text-black').attr('ID');

					$.ajax({
						type: "GET",
						url: "<?php echo url('/portal/legacy-form-builder/reorder-element'); ?>",
						dataType: "html",
						data: {
							"profile_id": '<?php echo $profile_id; ?>',
							'current_ele_id': current_ele_id,
							"next_ele_id": next_ele_id
						},
						success: function(data) {
							sorting_started = 0; //as sorting complete
						},
						complete: function() {
							$('.progressbar').hide();
						}
					});
				}
			}); // end droppable
		} //end of enable drag, drop and sort for participant name

		$(":radio").uniform();
		$(":checkbox").uniform();
		$("select").uniform();
		$(".ui-widget-content ol").css('height', $(".ui-widget-content ol").prop('scrollHeight'));
		iframe_height = $p('#iframe_frm_builder').height();

		var x = document.getElementsByName("email_recipient[]");

		email_recipient_cnt = x.length;

		$p('#iframe_frm_builder').height(iframe_height + (75 * email_recipient_cnt));

		//for expand iframe to max
		$('#iframe_current_height').val($p('#iframe_frm_builder').height()); ///new hidden
		$('#div_drop_area_current_height').val($('#div_drop_area').css('height')); //new hidden

		// Expand/Reduce is handled on the parent portal page (no jQuery there).
		if ($p && $p !== $) {
			$p('body').off('click.slExpandReduce', '.expand-reduce').on('click.slExpandReduce', '.expand-reduce', function() {
				var $label = $p('#sl-expand-reduce-label');
				if (! $label.length) {
					$label = $p(this).closest('.footer-rouded').find('span.expand-reduce').first();
				}
				iframe_height = $p('#iframe_frm_builder').height();

				if ($label.text().trim() == "Expand Window") {
					$p('#iframe_frm_builder').height($(".ui-widget-content ol").prop('scrollHeight') + $('.top-part').height());

					div_drop_area_current_height = $('#div_drop_area_current_height').val().split('px');

					if (div_drop_area_current_height[0] < $('.ui-droppable').prop('scrollHeight'))
						$('#div_drop_area').css('height', ($('.ui-droppable').prop('scrollHeight')));

					$label.text('Reduce Window');
					$p('#expand_reduce_img').attr('src', '<?php echo rtrim(url('/'), '/'); ?>/images/reduce_window.png');

				} else {
					$p('#iframe_frm_builder').height($('#iframe_current_height').val());
					$('#div_drop_area').css('height', $('#div_drop_area_current_height').val());
					$label.text('Expand Window');
					$p('#expand_reduce_img').attr('src', '<?php echo rtrim(url('/'), '/'); ?>/images/expand_window.png');
				}
			});
		}

		$p('#enable_form').on('click', function() {
			if ($(this).prop('checked') == true)
				enable_form = 1;
			else
				enable_form = 0;
			$.ajax({
				type: "GET",
				url: "<?php echo url('/portal/legacy-form-builder/update-enable-form'); ?>",
				dataType: "html",
				data: {
					"profile_id": '<?php echo $profile_id; ?>',
					'enable_form': enable_form
				}

			});
		});

		$('#add_edit_participant_list').on('click', function() {
			if (window.parent && window.parent !== window && typeof window.parent.scanlinkOpenParticipantList === 'function') {
				window.parent.scanlinkOpenParticipantList();
				return;
			}
			try {
				window.parent.postMessage({ type: 'scanlink-open-participants' }, window.location.origin);
			} catch (e) {}
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
		$('.help-div a').on('click', function(e) {
			$(this).parent().hide();
			e.preventDefault();
			var url = $(this).attr("href");
			if (url != '') {
				var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
				var result = (url.match(p)) ? RegExp.$1 : false;

				if (result) {
					// Portal parity: the parent Filament page has no #how_to_pop_up/.player-window
					// overlay — it exposes a shared help-video modal instead. Post the embeddable
					// URL up to it so the in-iframe help icons actually open the tutorial.
					var embed = 'https://www.youtube.com/embed/' + result + '?rel=0';
					try {
						window.parent.postMessage({ type: 'scanlink-open-help-video', url: embed }, window.location.origin);
					} catch (err) {}
				} else {
					return false;
				}
			} else {
				return false;
			}
		});

		//remove href for text tool
		$('.text_block a').attr('href', 'javascript:;');
		$('.text_block a').removeAttr('target');
	});
</script>

<div>
	<form name="frm" id="frm" enctype="multipart/form-data">
		<input type="hidden" name="iframe_current_height" id="iframe_current_height" value="">
		<input type="hidden" name="div_drop_area_current_height" id="div_drop_area_current_height" value="">
		<input type="hidden" name="enable_form_analytics" id="enable_form_analytics" value="<?php echo $enable_form_analytics; ?>" />

		<div class="from-box">

			<div class="top-part">
				<h2>Form Name</h2>
				<div class="rounded">
					<input name="form_name" id="form_name" value="<?php echo $form_name; ?>" type="text">
				</div>
				<h2>Recipients Email</h2>
				<?php

				$ui_widget_content_height = 411;

				$rec_cnt = 0;
				foreach ($recipient_email_arr as $recipient_email) {
					//echo $recipient_email;
				?>
					<div class="parent_content" id="email_fld<?php echo $rec_cnt; ?>">
						<div class="rounded">
							<input id="email_recipient" name="email_recipient[]" value="<?php echo is_array($recipient_email) ? ($recipient_email['recipient_email'] ?? '') : $recipient_email; ?>" type="text">*
						</div>
						<div id="remove_ele"><a href="javascript:;" onclick="remove_recipient(email_fld<?php echo $rec_cnt; ?>)">Remove</a></div>
					</div>
				<?php ++$rec_cnt;
				}

				if (count($recipient_email_arr) == 0) {
				?>
					<div class="rounded">
						<input id="email_recipient" name="email_recipient[]" type="text">*
					</div>
				<?php
				} ?>

				<div class="add-another"><a href="javascript:;" onclick="add_another_recipient('<?php echo count($recipient_email_arr); ?>')" id="add_another">Add Another</a></div>

				<h2 id="email_tag_heading">Email Tag (optional) </h2>
				<div class="rounded">
					<input id="email_tag" name="email_tag" type="text" value="<?php echo !empty($email_subject) ? $email_subject : '' ?>">
				</div>

				<div><input type="button" class="green-btn" id="add_edit_participant_list" value="Add/Edit Participant List" />
					<img src="<?php echo rtrim(url('/'), '/').'/'; ?>images/help_icon.jpg" class="help-icon" style="vertical-align:text-bottom;" />
					<div class="help-div" style="margin-left: 200px;"><a href="https://www.youtube.com/embed/EtnO7FDlzIo?rel=0">Video Tutorial</a>
						<div class="arrow-bg"></div>
					</div>
				</div>

				<h2 class="click-drag">Click and drag an element on to your form</h2>
				<!-- Begin Menu -->
				<div class="menu-form">
					<ul>
						<li><a href="javascript:;" class="green">Question Tools</a>
							<ul class="green">
								<?php foreach ($question_type_0_arr as $question_type) { ?>
									<li id="<?php echo $question_type['question_type_id']; ?>" style="background-color:<?php echo $question_type['question_type_id'] == '25' ? 'rgb(255 0 0)' : '#9bff9b' ?>;color:#fff !important">
										<a href="javascript:;" style="color:<?php echo $question_type['question_type_id'] == '25' ? '#fff' : '#555755' ?>"><?php echo $question_type['question_type']; ?></a>
									</li>
								<?php
								} ?>
							</ul>
						</li>
						<li><a href="javascript:;" class="orange">Format Tools</a>
							<ul class="orange">
								<?php
								foreach ($question_type_1_arr as $question_type) {
								?>
									<li id="<?php echo $question_type['question_type_id']; ?>"><a href="javascript:;"><?php echo $question_type['question_type']; ?></a></a></a></li>
								<?php
								} ?>
							</ul>
						</li>
						<li><a href="javascript:;" class="blue">Answer Tools</a>
							<ul class="blue">
								<?php
								foreach ($question_type_2_arr as $question_type) {
								?>
									<li id="<?php echo $question_type['question_type_id']; ?>"><a href="javascript:;"><?php echo $question_type['question_type']; ?></a></a></a></li>
								<?php
								} ?>
							</ul>
						</li>
					</ul>
				</div>


				<!-- End Menu -->
				<div class="main-arrow">
					<div class="main-arrow-title" id="drop-here-title">
						<span>
							<img src="<?php echo rtrim(url('/'), '/'); ?>/form-builder/images/arrow.jpg" alt="" class="after" />
							Create your form here
							<img src="<?php echo rtrim(url('/'), '/'); ?>/form-builder/images/arrow.jpg" alt="" class="before" />
						</span>
					</div>
				</div>
				<div style="clear: both;"></div>

			</div> <!-- end of <div class="top-part"> -->
			<div style="clear: both;"></div>
			<div id="drop_area">
				<div class="progressbar">
					<img align="absmiddle" src="<?php echo rtrim(url('/'), '/'); ?>/images/spinner_gray.gif"> Please Wait...
				</div>

				<div class="ui-widget-content" id="div_drop_area" style="height: <?php echo $ui_widget_content_height; ?>px;">

					<ol>
						<li class="placeholder">
							<?php if (count($form_questions_arr) > 0) {

								foreach ($form_questions_arr as $questions) {

									$question_id = $questions['question_id'];
									$question_text = $questions['question_text'];
									$question_type_id = $questions['question_type_id'];
									$mandatory_field = $questions['is_mandatory'];
									$button_link_url = $questions['button_link_url'];
									$button_colour = $questions['button_colour'];
									$doc_title = $questions['doc_title'];
									$include_name = $questions['include_name'];
									$include_employer = $questions['include_employer'];
									$include_email = $questions['include_email'];
									$include_phone = $questions['include_phone'];
									$is_logchecked = $questions['is_logchecked'];
									$log_columntitle = $questions['log_columntitle'];
									$covid_bg_color = $questions['covid_bg_color'];
									$covid_text_color = $questions['covid_text_color'];

									$participant_include_signature = $questions['participant_include_signature'];
									$participant_include_employer = $questions['participant_include_employer'];

									if ($mandatory_field == '1') {
										$mandatory_field = 'true';
									} else {
										$mandatory_field = 'false';
									}

									$question_types = \App\Support\LegacyFormBuilderView::get_question_types_by_question_type_id($question_type_id);

									$type = $question_types[0]['type']; //0= Question Tools, 1=Format Tools, 2= Answer Tool,
									$control_name = $question_types[0]['question_type'];

									if ($type == 0) {
										$box_class_name = 'green-bx';
									}

									if ($type == 1 || $question_type_id == '11' || $question_type_id == '16' || $question_type_id == '17' || $question_type_id == '18' || $question_type_id == '19' || $question_type_id == '20' || $question_type_id == '21') {
										$box_class_name = 'orange-bx';
									}

									if ($type == 2 && ($question_type_id != '11' && $question_type_id != '16' && $question_type_id != '17' && $question_type_id != '18' && $question_type_id != '19' && $question_type_id != '20' && $question_type_id != '21')) {
										$box_class_name = 'blue-bx';
									}

									$scale_from = '';
									$scale_to = '';
									$row_comma_separated = '';
									$column_comma_separated = '';

									$random_id = date('hms');

									$image_align = $questions['image_align'];
									$image_title = $questions['image_title'];

									if ($question_type_id == '6') { // number scale
										$question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

										$question_options = \App\Support\LegacyFormBuilderView::get_question_option($question_id, $question_option_type_id);

										$scale_from = $question_options[0]['option_name'];

										$question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

										$question_options = \App\Support\LegacyFormBuilderView::get_question_option($question_id, $question_option_type_id);

										$scale_to = $question_options[0]['option_name'];
									}

									if ($question_type_id == '7') { // grid
										$question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

										$row_text = \App\Support\LegacyFormBuilderView::get_question_option($question_id, $question_option_type_id);

										$row_comma_separated = '';
										for ($m = 0; $m < count($row_text); ++$m) {
											if ($row_comma_separated == '') {
												$row_comma_separated = $row_text[$m]['option_name'];
											} else {
												$row_comma_separated = $row_comma_separated . ',' . $row_text[$m]['option_name'];
											}
										}

										$question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

										$column_text = \App\Support\LegacyFormBuilderView::get_question_option($question_id, $question_option_type_id);

										$column_comma_separated = '';
										for ($m = 0; $m < count($column_text); ++$m) {
											if ($column_comma_separated == '') {
												$column_comma_separated = $column_text[$m]['option_name'];
											} else {
												$column_comma_separated = $column_comma_separated . ',' . $column_text[$m]['option_name'];
											}
										}
									}

									if ($enable_form_analytics == '0') { //edit option only when form builder analytics disabled
										echo '<div id="question_div_' . $question_id . '" class="text-black" title="drag to change order" onclick="javascript:edit_element(&quot;question_div_' . $question_id . '&quot;,&quot;' . rawurlencode($question_text) . '&quot;,' . $question_type_id . ',&quot;' . $box_class_name . '&quot;,&quot;' . $control_name . '&quot;,&quot;question_div_' . $question_id . '&quot;,&quot;' . $scale_from . '&quot;,&quot;' . $scale_to . '&quot;,&quot;' . rawurlencode($row_comma_separated) . '&quot;,&quot;' . rawurlencode($column_comma_separated) . '&quot;,&quot;' . $image_title . '&quot;,&quot;' . $image_align . '&quot;,&quot;' . $mandatory_field . '&quot;, &quot;' . $button_link_url . '&quot;,&quot;' . $button_colour . '&quot;,&quot;' . $doc_title . '&quot;,&quot;' . $include_name . '&quot;,&quot;' . $include_employer . '&quot;,&quot;' . $include_email . '&quot;,&quot;' . $include_phone . '&quot;,&quot;' . $participant_include_signature . '&quot;,&quot;' . $participant_include_employer . '&quot;,&quot;' . $is_logchecked . '&quot;,&quot;' . $log_columntitle . '&quot;,&quot;' . $covid_text_color . '&quot;,&quot;' . $covid_bg_color . '&quot;)">';

										// $covid_bg_color $covid_text_color
									} else {
										echo '<div id="question_div_' . $question_id . '" class="text-black" >';
									}

									echo '	<span class="question_id_span" style="display:none;">' . $question_id . '</span>';

									switch ($question_type_id) {
										case '1': // Text Field
											//echo '<input type="text" name="question_'.$question_id.'" class="text-box-black"><br><br>';
											echo '<div name="question_' . $question_id . '" class="text-box-black" ></div><br>';

											break;
										case '2': // Text

											echo '<div class="text_block" >' . $question_text . '</div><div style="clear: both;"></div>';
											break;
										case '3': // Multiple choice

											$option_text = explode(':::', $question_text);
											for ($i = 0; $i < count($option_text); ++$i) {
												echo "<input type='radio' name='question_" . $question_id . "' value='" . $option_text[$i] . "' />&nbsp;" . $option_text[$i] . '<br>';
											}

											echo '<br>';
											break;
										case '4': // Checkboxes
											$option_text = explode(':::', $question_text);
											for ($i = 0; $i < count($option_text); ++$i) {
												echo "<input type='checkbox' name='question_" . $question_id . "[]' value='" . $option_text[$i] . "' />&nbsp;" . $option_text[$i] . '<br>';
											}

											echo '<br>';
											break;
										case '5': // Drop Down Menu
											$option_text = explode(':::', $question_text);
											echo '<div name="question_' . $question_id . '" class="selector" ><span style="width: 126px;">' . $option_text[0] . '</span></div>';
											echo '<br>';
											break;
										case '6': // Number Scale
											for ($i = $scale_from; $i <= $scale_to; ++$i) {
												echo "<input type='radio' name='question_" . $question_id . "' value='" . $i . "' />&nbsp;" . $i . '<br>';
											}
											echo '<br>';

											break;
										case '7': // Grid

											echo "<table border='0' cellpadding='3' cellspacing='15'>";
											echo '<tr>';
											echo '<td>&nbsp;</td>';
											for ($i = 0; $i < count($column_text); ++$i) {
												echo "<td align='center'>" . $column_text[$i]['option_name'] . '</td>';
											}

											echo '</tr>';

											for ($i = 0; $i < count($row_text); ++$i) {
												echo '<tr>';
												echo '<td>' . $row_text[$i]['option_name'] . '</td>';
												for ($j = 0; $j < count($column_text); ++$j) {
													echo "<td  align='center'><input type='radio' name='question_" . $question_id . '_row' . $i . "' value='" . $j . "' /></td>";
												}

												echo '</tr>';
											}

											echo '</table>';

											echo '<br>';
											break;
										case '8': // Date

											// echo '<input type="text" class="text-box-black" id="dtpicker_date" name="question_'.$question_id.'" placeholder="Date Picker" ><br><br>';
											echo '<div class="text-box-black" id="dtpicker_date" name="question_' . $question_id . '" >Date Picker</div><br>';

											break;
										case '9': // time

											//echo '<input type="text" class="text-box-black" id="dtpicker_time"  name="question_'.$question_id.'" placeholder="Time Picker" ><br><br>';
											echo '<div class="text-box-black" id="dtpicker_time" name="question_' . $question_id . '" >Time Picker</div><br>';

											break;
										case '10': // heading
											echo "<h1 style='font-size:20px;'>" . $question_text . '</h1>';
											break;
										case '11': // image

											if ($question_text != '') {
												echo '<div align="' . $questions['image_align'] . '"><img src="' . rtrim(url('/'), '/').'/images/form_builder_uploaded_images/' . $question_text . '" width="100"  title="' . $questions['image_title'] . '" ></div><br>';
											}
											echo '<br>';
											break;
										case '12': // sub heading
											echo "<h3  style='font-size:16px;'>" . $question_text . '</h3>';
											break;
										case '13': // Line Divider
											echo '<hr>';
											break;
										case '14': // Blank Space
											echo '<br><br>';
											break;
										case '15': // comments
											//echo '<textarea name="dtpicker_date" placeholder="comments" readonly class="text-box-black"></textarea>';
											echo '<div name="div_comments" class="text-box-black">comments</div>';
											break;
										case '16': // Signature
											if ($include_name == '1') {
												echo '<div class="image-label">Name</div>';
												echo '<div class="text-box-black"></div><br>';
											}
											if ($include_employer == '1') {
												echo '<div class="image-label">Employer/Company</div>';
												echo '<div class="text-box-black"></div><br>';
											}
											if ($include_email == '1') {
												echo '<div class="image-label">Email</div>';
												echo '<div class="text-box-black"></div><br>';
											}
											if ($include_phone == '1') {
												echo '<div class="image-label">Phone No</div>';
												echo '<div class="text-box-black"></div><br>';
											}
											if ($question_text != '') {
												echo '<p>' . $question_text . '</p><br>';
											}

											//echo '<textarea name="dtpicker_date" placeholder="Signature" readonly class="text-box-black"></textarea>';
											echo '<div name="div_sign" class="text-box-black">Signature</div>';
											break;
										case '17': // upload button
											if ($question_text != '') {
												echo '<p>' . $question_text . '</p><br>';
											}

											//echo '<input type="button" name="" style="background-color:#007A01;color:#ffffff;" class="text-box-black" value="Upload">';
											echo '<div name="" style="text-align:center; border:none; background-color:#007A01;color:#ffffff;" class="text-box-black" >Upload</div>';

											break;
										case '18': // participant name
											if ($question_text != '') {
												echo '<p>' . $question_text . '</p>';
											} else {
												echo '<p>Name</p>';
											}

											echo '<div name="question_' . $question_id . '" class="text-box-black" ></div>';
											if ($participant_include_employer == '1') {
												echo '<br><div class="image-label">Employer/Company</div>';
												echo '<div class="text-box-black"></div>';
											}
											if ($participant_include_signature == '1') {
												echo '<br><div name="div_sign" class="text-box-black">Signature</div>';
											}
											echo '<input type="hidden" name="participant_name_ele[]"  id="participant_name_ele" />';
											break;
										case '19': // location function
											if ($question_text != '') {
												echo '<p>' . $question_text . '</p><br>';
											}

											echo '<div name="question_' . $question_id . '" class="text-box-black" ></div>';
											echo '<input type="hidden" name="location_ele[]"  id="location_ele" />';
											echo '<div name="" style="text-align:center; border:none;width:140px;margin-top:5px; background-color:#808080;color:#ffffff;" class="text-box-black" >MAP</div>';
											break;
										case '20': // web link button

											echo '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#' . $button_colour . ';color:#ffffff;" class="text-box-black" >' . $question_text . '</div>';

											break;
										case '21': // Document Button

											echo '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#' . $button_colour . ';color:#ffffff;" class="text-box-black" >' . $doc_title . '</div>';

											break;

										case '22': // SWMS Hazard/Risk
											if ($question_text != '') {
												echo '<p>' . $question_text . '</p><br>';
											}

											echo '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#808080;color:#ffffff;" class="text-box-black" >SWMS Hazard/Risk</div>';
											break;

										case '23': // Document Menu

											echo '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#808080;color:#ffffff;" class="text-box-black" >Document Menu</div>';

											break;
										case '24': // Add recipient
											echo "<div class='add_recipient' style='height:auto;text-align:left;padding-left:10px; border:none;'>";
											echo 'Send email notifications to';
											echo "<div class='rounded' >";
											echo "<input style='width:90%' id='email_recipient_additional' class='email_recipient_additional' name='email_recipient__additional[]' required='required' type='text'>";
											echo '</div>';
											echo "<div class='add-another'><a href='javascript:;'>Add Another</a></div>";
											echo '</div>';
											break;
										case '25': //Covid check in form
											//Need to do design
											echo "<div class='checkin-form-btn'>COVID CHECK-IN</div>";
											break;
									} //end switch

									echo '</div>';
								} // end foreach
							}
							?>


						</li>
					</ol>
				</div>
			</div>
		</div>
	</form>

</div>
<script>
	function add_another_recipient(id) {

		var contact_html = '<div class="parent_content" id="email_fld' + id + '">';
		contact_html += '<div class="rounded">';
		contact_html += '<input id="email_recipient" name="email_recipient[]" type="text">*';
		contact_html += '</div>';
		contact_html += '<div id="remove_ele"><a href="javascript:;" onclick="remove_recipient(email_fld' + id + ')" >Remove</a></div>';
		contact_html += '<div>';

		$('.add-another').html('<a href="javascript:;" onclick="add_another_recipient(' + ((id * 1) + 1) + ')" id="add_another">Add Another</a>');
		$('.add-another').before(contact_html);

		drop_area_height = $('.ui-widget-content').height();
		drop_area_height_ol = $('.ui-widget-content ol').height();

		//$('.ui-widget-content').height((drop_area_height-75));
		//$('.ui-widget-content ol').height((drop_area_height_ol-75));

		iframe_height = $p('#iframe_frm_builder').height();

		$p('#iframe_frm_builder').height(iframe_height + 75);

		$('#iframe_current_height').val(($('#iframe_current_height').val() * 1) + 75);

	}

	function remove_recipient(obj) {
		$(obj).remove();

		drop_area_height = $('.ui-widget-content').height();
		drop_area_height_ol = $('.ui-widget-content ol').height();

		//$('.ui-widget-content').height((drop_area_height+75));
		//$('.ui-widget-content ol').height((drop_area_height_ol+75));

		iframe_height = $p('#iframe_frm_builder').height();

		$p('#iframe_frm_builder').height(iframe_height - 65);
		$('#iframe_current_height').val(($('#iframe_current_height').val() * 1) - 65);
	}

	function remove_div(obj) {
		new_height = $(".ui-widget-content ol").prop('scrollHeight') - $('#' + obj).height();

		$('#' + obj).remove();

		$(".ui-widget-content ol").css('height', new_height);
	}


	function append_div(obj, txtbox2, question_type_id, random_id, box_class, control_name, question_id) {

		txtbox = document.getElementById(txtbox2);

		if (! parentEnableFormChecked()) {
			alert("Please enable form");
			return false;
		}
		mandatory_field = $('#mandatory' + obj).is(":checked");

		// basic validation
		var emailRegexStr = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
		form_name = document.getElementById('form_name').value;
		if (form_name.split(" ").join("") == "") {
			alert("Please enter form name");
			return false;
		}
		var x = document.getElementsByName("email_recipient[]");

		if (x.length == 0) {
			alert("Please enter at least one recipient email");
			return false;
		}

		email_rec_cnt = 0;
		email_arr = '';

		include_name = '0';
		include_employer = '0';
		include_email = '0';
		include_phone = '0';

		participant_include_signature = '0';
		participant_include_employer = '0';

		for (i = 0; i < (x.length); i++) {
			if (x[i].value.split(" ").join("") != "") {
				if (!emailRegexStr.test(x[i].value)) {
					alert('Enter a valid email.');
					return false;
				} else {
					if (email_arr == "") {
						email_arr = x[i].value;
					} else {
						email_arr = email_arr + "," + x[i].value;
					}
				}
				email_rec_cnt++;
			}
		}

		if (email_rec_cnt == 0) {
			alert("Please enter at least one recipient email");
			return false;
		}

		if (parentEnableFormChecked()) {
			enable_form = 1;
		} else {
			enable_form = 0;
		}
		user_input = '';

		scale_from = '';
		scale_to = '';

		row_text = '';
		column_text = '';

		image_title = '';
		image_align = '';

		button_link_url = '';
		button_colour = '';
		doc_title = '';
		log_columntitle = '';
		is_logchecked = '0';
		covid_fullname = "";
		covid_contact = "";
		covid_email = "";
		covid_location = "";
		covid_bg_color = "";
		covid_text_color = "";

		if ((question_type_id >= 1 && question_type_id <= 9) || question_type_id == 15 || question_type_id == 23 || question_type_id == 24) {
			log_columntitle = $('#log_columntitle' + obj).val();
			if ($('#is_logchecked' + obj).is(":checked") == true) {
				is_logchecked = '1';
			} else {
				is_logchecked = '0';
			}
			if (is_logchecked == '1' && log_columntitle.split(" ").join("") == "") {
				alert("Please enter column title");
				return false;
			}
		}

		switch (question_type_id) {
			case 1: //text field - not required input box validation
			case 24: //add recipient - not required input box validation
				break;
			case 13: //line divide - not required input box validation
			case 14: // blank space - not required input box validation
			case 15: // comments - not required input box validation
			case 17: // upload button - not required input box validation
				user_input = txtbox.value;
				break;
			case 2:
				val_cke = CKEDITOR.instances[txtbox2].getData();
				if (val_cke.split(" ").join("") == "") {
					alert("Please enter text");
					return false;
				}
				user_input = val_cke;
				break;
			case 3: //Multiple choice
			case 4: //check box
			case 5: // Dropdown
				option_text = "";
				var x = document.getElementsByName("option_text" + random_id + "[]");

				if (x.length == 0) {
					alert("Please enter at least one option");
					return false;
				}

				for (i = 0; i < (x.length); i++) {
					if (x[i].value.split(" ").join("") == "") {
						alert("Please enter option value");
						return false;
					} else {
						if (i == 0)
							option_text = x[i].value;
						else
							option_text = option_text + ":::" + x[i].value;
					}
				}

				user_input = option_text;
				break;
			case 6: // number scale

				scale_from = document.getElementById('scale_from').value;
				scale_to = document.getElementById('scale_to').value;

				if (scale_from.split(" ").join("") == "") {
					alert("Please enter from number");
					return false;
				}
				if (isNaN(scale_from) == true) {
					alert("Please enter valid from number");
					return false;
				}
				if (scale_from.indexOf(".") > "-1") {
					alert("Please enter valid from number");
					return false;
				}

				if (scale_to.split(" ").join("") == "") {
					alert("Please enter to number");
					return false;
				}
				if (isNaN(scale_to) == true) {
					alert("Please enter valid to number");
					return false;
				}
				if (scale_to.indexOf(".") > "-1") {
					alert("Please enter valid to number");
					return false;
				}

				if ((scale_from * 1) > (scale_to * 1)) {
					alert("From value must be less than to value");
					return false;
				}
				user_input = txtbox.value;

				break;
			case 7: // grid

				var x = document.getElementsByName("row" + random_id + "[]");

				if (x.length == 0) {
					alert("Please enter at least one row");
					return false;
				}

				for (i = 0; i < (x.length); i++) {
					if (x[i].value.split(" ").join("") == "") {
						alert("Please enter row value");
						return false;
					} else {
						if (i == 0)
							row_text = x[i].value;
						else
							row_text = row_text + "," + x[i].value;
					}
				}

				var x = document.getElementsByName("column" + random_id + "[]");

				if (x.length == 0) {
					alert("Please enter at least one column");
					return false;
				}

				for (i = 0; i < (x.length); i++) {
					if (x[i].value.split(" ").join("") == "") {
						alert("Please enter column value");
						return false;
					} else {
						if (i == 0)
							column_text = x[i].value;
						else
							column_text = column_text + "," + x[i].value;
					}
				}

				user_input = txtbox.value;
				break;
			case 8: // Date
			case 9: // time
				user_input = txtbox.value;
				break;
			case 11: // image
				uploaded_file = document.getElementById("uploaded_file").value;
				image_title = document.getElementById("image_title" + random_id).value;
				//var image_file=document.getElementById("image_file"+random_id);
				image_align_radio = document.getElementsByName("image_align" + random_id);

				if (uploaded_file == "") {
					alert("Please upload image");
					return false;
				}

				for (var i = 0; i < image_align_radio.length; i++) {
					if (image_align_radio[i].checked) {
						image_align = image_align_radio[i].value;
						break;
					}
				}
				user_input = txtbox.value;
				break;
			case 16: // signature
				if ($('#' + txtbox2).closest('.orange-box').find('.include-name').is(":checked") == true) {
					include_name = '1';
				} else {
					include_name = '0';
				}
				if ($('#' + txtbox2).closest('.orange-box').find('.include-employer').is(":checked") == true) {
					include_employer = '1';
				} else {
					include_employer = '0';
				}
				if ($('#' + txtbox2).closest('.orange-box').find('.include-email').is(":checked") == true) {
					include_email = '1';
				} else {
					include_email = '0';
				}
				if ($('#' + txtbox2).closest('.orange-box').find('.include-email').is(":checked") == true) {
					include_email = '1';
				} else {
					include_email = '0';
				}
				if ($('#' + txtbox2).closest('.orange-box').find('.include-phone').is(":checked") == true) {
					include_phone = '1';
				} else {
					include_phone = '0';
				}
				user_input = txtbox.value;
				break;
			case 18: // Participant name
				if ($('#' + txtbox2).closest('.orange-box').find('.include-name').is(":checked") == true) {
					participant_include_signature = '1';
				} else {
					participant_include_signature = '0';
				}
				if ($('#' + txtbox2).closest('.orange-box').find('.include-employer').is(":checked") == true) {
					participant_include_employer = '1';
				} else {
					participant_include_employer = '0';
				}
				user_input = txtbox.value;
				break;

			case 20: // web link button

				user_input = txtbox.value;
				button_link_url = document.getElementById("btn_link" + random_id).value;
				button_colour = document.getElementById("btn_colour" + random_id).value;

				var myRegExp = /^(?:(?:https?):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;

				if (txtbox.value.split(" ").join("") == "") {
					alert("Please enter button text");
					return false;
				}

				if (button_link_url.split(" ").join("") == "") {
					alert("Please enter button link URL");
					return false;
				}

				button_link_url_arr = button_link_url.split('?');
				if (!myRegExp.test(button_link_url_arr[0])) {
					alert("Please enter valid button link URL");
					return false;
				}

				if (button_colour.split(" ").join("") == "") {
					alert("Please select button colour");
					return false;
				}
				break;

			case 21: // Document Button

				user_input = txtbox.value;
				uploaded_doc = document.getElementById("uploaded_doc" + random_id).value;

				doc_title = document.getElementById("doc_title" + random_id).value;
				button_colour = document.getElementById("doc_color" + random_id).value;

				if (doc_title.split(" ").join("") == "") {
					alert("Please enter document title");
					return false;
				}

				if (uploaded_doc == "") {
					alert("Please upload document");
					return false;
				}

				if (button_colour.split(" ").join("") == "") {
					alert("Please select document colour");
					return false;
				}
				break;

			case 23: // Document Menu
				user_input = txtbox.value;
				uploaded_doc = document.getElementById("uploaded_doc" + random_id).value;

				doc_title = document.getElementById("doc_title" + random_id).value;

				if (doc_title.split(" ").join("") == "") {
					alert("Please enter document title");
					return false;
				}

				if (uploaded_doc == "") {
					alert("Please upload document");
					return false;
				}
				break;
			case 25:

				// New code 19-03-2021
				if ($('#is_logchecked' + obj).is(":checked") == true) {
					is_logchecked = '1';
				} else {
					is_logchecked = '0';
				}
				var textareaid = "textbox" + random_id;
				user_input = CKEDITOR.instances[textareaid].getData();
				covid_text_color = document.getElementById("text_color" + random_id).value;
				covid_bg_color = document.getElementById("bg_color" + random_id).value;
				break;
			default:
				// New code 19-03-2021
				if (question_type_id != 25) {
					if (txtbox.value.split(" ").join("") == "") {
						alert("Please enter text");
						return false;
					}
					user_input = txtbox.value;
				}

		}

		$('#' + obj).removeAttr('class');
		$('#' + obj).addClass('text-black');

		hours1 = date_curr.getHours();
		mins1 = date_curr.getMinutes();
		seconds1 = date_curr.getSeconds();

		random_str = hours1 + "" + mins1 + "" + seconds1;

		$.ajax({
			type: "POST",
			url: "<?php echo url('/portal/legacy-form-builder/render-element'); ?>",
			dataType: "html",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				"profile_id": '<?php echo $profile_id; ?>',
				'random_str': random_str,
				'question_id': question_id,
				"question_type_id": question_type_id,
				"user_input": user_input,
				"scale_from": scale_from,
				"scale_to": scale_to,
				"row_text": row_text,
				"column_text": column_text,
				"image_title": image_title,
				"image_align": image_align,
				"form_name": form_name,
				"email_arr": email_arr,
				"enable_form": enable_form,
				"mandatory_field": mandatory_field,
				"button_link_url": button_link_url,
				"button_colour": button_colour,
				"doc_title": doc_title,
				"include_name": include_name,
				"include_employer": include_employer,
				"include_email": include_email,
				"include_phone": include_phone,
				"participant_include_signature": participant_include_signature,
				"participant_include_employer": participant_include_employer,
				"is_logchecked": is_logchecked,
				"log_columntitle": log_columntitle,
				"covid_text_color": covid_text_color,
				"covid_bg_color": covid_bg_color,
			},
			success: function(data) {
				if (! data || (typeof data === 'string' && data.indexOf('question_id_span') === -1 && data.indexOf('Exception') !== -1)) {
					alert('Could not save this form field. Please try again.');
					return;
				}
				$('#' + obj).show();
				$('#' + obj).html(data);
				$(":radio").uniform();

				$("select").uniform();

				$(".ui-widget-content ol").css('height', $(".ui-widget-content ol").prop('scrollHeight'));

				//remove href for text tool
				$('.text_block a').attr('href', 'javascript:;');
				$('.text_block a').removeAttr('target');

				notifyParentFormSaved();

				setTimeout(function() {
					if ($p('span.expand-reduce').text() == "Reduce Window") {

						ul_scroll_height = $('.ui-droppable').prop('scrollHeight') + 10;
						$('#div_drop_area').css('height', ul_scroll_height);
						$p('#iframe_frm_builder').height(ul_scroll_height + $('.top-part').height());
					}

				}, 3000);

				$(":checkbox:not(.sign-text)").uniform();
			},
			error: function(xhr) {
				var msg = 'Could not save this form field.';
				if (xhr && xhr.responseText) {
					var plain = $('<div>').html(xhr.responseText).text().replace(/\s+/g, ' ').trim();
					if (plain) {
						msg += ' ' + plain.substring(0, 180);
					}
				}
				alert(msg);
			},
			complete: function() {
				var textcolor = covid_text_color;
				var bgcolor = covid_bg_color;
				$('#' + obj).attr('onClick', 'javascript:edit_element("' + obj + '","' + encodeURIComponent(user_input) + '",' + question_type_id + ',"' + box_class + '","' + control_name + '","' + random_id + '","' + scale_from + '","' + scale_to + '","' + row_text + '","' + column_text + '","' + image_title + '","' + image_align + '","' + mandatory_field + '","' + button_link_url + '","' + button_colour + '","' + doc_title + '","' + include_name + '","' + include_employer + '","' + include_email + '","' + include_phone + '","' + participant_include_signature + '","' + participant_include_employer + '","' + is_logchecked + '","' + log_columntitle + '","' + textcolor + '","' + bgcolor + '")');

				$('#' + obj).attr('title', 'drag to change order');
			}

		});

		return true;
	}



	function add_another_option(id, random_id) {

		var option_html = '<div id="option' + id + '">';
		option_html += '<p ><input type="text" id="option_cntrl" name="option_text' + random_id + '[]" placeholder="Enter Your Text"></p>';
		option_html += '<div id="remove_ele"><a href="javascript:;" onclick="remove_option(' + id + ')" >Remove</a></div>';
		option_html += '</div>';

		$('#option-list').html('<p class="add-other-option" ><a href="javascript:;" onclick="add_another_option(' + ((id * 1) + 1) + ',&quot;' + random_id + '&quot;)">Add Another</a></p>');
		$('#option-list').before(option_html);


	}

	function remove_option(obj) {
		$('#option' + obj).remove();
	}

	// grid related funs
	function add_another_row(id, random_id) {

		var option_html = '<div id="row' + id + '">';
		option_html += '<p ><input type="text" id="option_cntrl" name="row' + random_id + '[]" placeholder="Enter Your Text"></p>';
		option_html += '<div id="remove_ele"><a href="javascript:;" onclick="remove_grid_row(' + id + ')" >Remove</a></div>';
		option_html += '</div>';

		$('#grid_row').html('<p class="add-other-row" ><a href="javascript:;" onclick="add_another_row(' + ((id * 1) + 1) + ',' + random_id + ')">Add Another Row</a></p>');
		$('#grid_row').before(option_html);


	}

	function remove_grid_row(obj) {
		$('#row' + obj).remove();
	}

	function add_another_column(id, random_id) {

		var option_html = '<div id="column' + id + '">';
		option_html += '<p ><input type="text" id="option_cntrl" name="column' + random_id + '[]" placeholder="Enter Your Text"></p>';
		option_html += '<div id="remove_ele"><a href="javascript:;" onclick="remove_grid_column(' + id + ')" >Remove</a></div>';
		option_html += '</div>';

		$('#grid_column').html('<p class="add-other-column" ><a href="javascript:;" onclick="add_another_column(' + ((id * 1) + 1) + ',' + random_id + ')">Add Another Column</a></p>');
		$('#grid_column').before(option_html);


	}

	function remove_grid_column(obj) {
		$('#column' + obj).remove();
	}


	//edit mode functions
	function edit_element(ele_obj, ele_val, question_type_id, class_name, control_name, random_id, scale_from, scale_to, row_text, column_text, image_title, image_align, mandatory_field, button_link_url, button_colour, doc_title, include_name, include_employer, include_email, include_phone, participant_include_signature, participant_include_employer, is_logchecked, log_columntitle, text_color, bg_color) {

		console.log(is_logchecked);

		if (sorting_started == 1) //do not fire click event if sorting is in progress
		{
			return;
		}

		ele_val = decodeURIComponent(ele_val);
		ele_val_ori = ele_val; //use in ckeditor
		row_text = decodeURIComponent(row_text);
		column_text = decodeURIComponent(column_text);

		ele_val = ele_val.replace(new RegExp('"', "gm"), "&quot;");
		ele_val = ele_val.replace(new RegExp("'", "gm"), "&apos;");

		if (ele_val == "" && question_type_id == "18") //default value for participant if it is blank
			ele_val = "Name";

		if (mandatory_field == "true")
			chk_checked = 'checked';
		else
			chk_checked = '';

		if (is_logchecked == "1")
			is_logchecked = 'checked';
		else
			is_logchecked = '';

		log_columntitle = decodeURIComponent(log_columntitle);
		log_columntitle = log_columntitle.replace(new RegExp('"', "gm"), "&quot;");
		log_columntitle = log_columntitle.replace(new RegExp("'", "gm"), "&apos;");

		ele_obj = $('#' + ele_obj);
		$(ele_obj).hide();

		doc_uploaded = "";
		if (ele_obj.find('.doc-uploaded').length) {
			doc_uploaded = ele_obj.find('.doc-uploaded').html();
		}

		//alert(ele_html+"-"+question_type_id+"-"+class_name);
		class_name_arr = class_name.split('-');
		color_class = class_name_arr[0];

		date_curr = new Date();
		hours = date_curr.getHours();
		mins = date_curr.getMinutes();
		seconds = date_curr.getSeconds();

		random_id_new = hours + "" + mins + "" + seconds;
		html = "";

		edit_mode = 1;
		question_id = $(ele_obj).find(".question_id_span").text();

		// if (question_type_id != 25) {
		// 	html = '<div id="' + random_id_new + '" class="' + color_class + '-box">';
		// 	html += '<div class="' + color_class + '-bx">';
		// 	html += '		<div class="' + color_class + '-first-box">';
		// 	html += '			<h1>' + control_name + '</h1>';
		// 	html += '			<a onclick="remove_div_and_data(&quot;' + random_id + '&quot;,&quot;' + question_id + '&quot;);remove_div(' + random_id_new + ');" href="javascript:;">';
		// 	html += '				<img src="<?php echo rtrim(url('/'), '/'); ?>/form-builder/images/close-button.png" border="0" alt="Remove" title="Remove" />';
		// 	html += '			</a>';
		// }

		// New code 19-03-2021
		html = '<div id="' + random_id_new + '" class="' + color_class + '-box  ' + (question_type_id == 25 ? " covid-checkin-header" : "") + ' ">';
		html += '<div class="' + color_class + '-bx ' + (question_type_id == 25 ? " covid-checkin-header" : "") + ' ">';
		html += '		<div class="' + color_class + '-first-box">';
		html += '			<h1>' + control_name + '</h1>';
		html += '			<a onclick="remove_div_and_data(&quot;' + random_id + '&quot;,&quot;' + question_id + '&quot;);remove_div(' + random_id_new + ');" href="javascript:;">';
		html += '				<img src="<?php echo rtrim(url('/'), '/'); ?>/form-builder/images/close-button.png" border="0" alt="Remove" title="Remove" />';
		html += '			</a>';

		if ((question_type_id >= 1 && question_type_id <= 9 && question_type_id != 2) || question_type_id == 16 || question_type_id == 18 || question_type_id == 19 || question_type_id == 22 || question_type_id == 24) { //enable mandatory for i/p elements
			html += '			<h2><span style="text-transform:capitalize;float:left;">Mandatory*</span>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="mandatory' + random_id + '" ' + chk_checked + ' id="mandatory' + random_id + '" />&nbsp;&nbsp;<a href="javascript:;" onclick="if(append_div(&quot;' + random_id + '&quot;,&quot;textbox' + random_id + '&quot;,' + question_type_id + ',&quot;' + random_id + '&quot;,&quot;' + class_name + '&quot;,&quot;' + control_name + '&quot;,&quot;' + question_id + '&quot;)) {remove_div(' + random_id_new + ');}">save</a></h2>';
		} else if (question_type_id == 23) {
			html += '			<h2><span style="text-transform:capitalize;float:left;">Mandatory</span>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="mandatory' + random_id + '" ' + chk_checked + ' id="mandatory' + random_id + '" />&nbsp;&nbsp;<a href="javascript:;" onclick="if(append_div(&quot;' + random_id + '&quot;,&quot;textbox' + random_id + '&quot;,' + question_type_id + ',&quot;' + random_id + '&quot;,&quot;' + class_name + '&quot;,&quot;' + control_name + '&quot;,&quot;' + question_id + '&quot;)) {remove_div(' + random_id_new + ');}">save</a></h2>';
		} else {

			html += '<h2><a href="javascript:;" onclick="if(append_div(&quot;' + random_id + '&quot;,&quot;textbox' + random_id + '&quot;,' + question_type_id + ',&quot;' + random_id + '&quot;,&quot;' + class_name + '&quot;,&quot;' + control_name + '&quot;,&quot;' + question_id + '&quot;)) {remove_div(' + random_id_new + ');}">save</a></h2>';
		}

		html += '</div>';

		if ((question_type_id >= 1 && question_type_id <= 9 && question_type_id != 2) || question_type_id == 15 || question_type_id == 23) {
			html = html + '<p style="margin-top:50px;display: inline-flex; flex-direction: row; justify-content: flex-start; align-items: center; flex-wrap: wrap; padding: 5px;">';
			html = html + '	<image style="float:left;width:30;height:31;" src="../../images/form_submission.png">';
			html = html + '	<span>Record text entry on Form Submission Log&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			html = html + '	<input type="checkbox" name="is_logchecked' + random_id + '" ' + is_logchecked + ' id="is_logchecked' + random_id + '"/>';
			html = html + '	<input type="text" name="log_columntitle' + random_id + '" value="' + log_columntitle + '" id="log_columntitle' + random_id + '" placeholder="Enter column title">';
			html = html + '</p>';
		}

		switch (question_type_id) {
			case 1: //text field - not required input box
			case 24: // add recipient
				break;
			case 13: //line divide - not required input box
			case 14: // blank space - not required input box
			case 15: // comments - not required input box
				html = html + '<p><input type="text" id="textbox' + random_id + '" readonly name="textbox' + random_id + '" ></p>';
				break;
			case "2": //text
				html = html + '<textarea rows="1" id="textbox' + random_id + '" value="' + ele_val + '" name="textbox' + random_id + '"></textarea>';

				break
			case 3: //Multiple choice
			case 4: //check box
			case 5: // Dropdown

				radio_arr = ele_val.split(":::");
				counter = radio_arr.length;

				for (k = 0; k < radio_arr.length; k++) {
					html += '<div id="option' + k + '">';
					html += '<p ><input type="text" id="option_cntrl" name="option_text' + random_id + '[]" value="' + radio_arr[k] + '"></p>';
					html += '<div id="remove_ele"><a href="javascript:;" onclick="remove_option(' + k + ')" >Remove</a></div>';
					html += '</div>';
				}


				html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" >'; // dummy textbox

				html = html + '<div id="option-list"><p class="add-other-option" ><a href="javascript:;" onclick="add_another_option(' + counter + ',&quot;' + random_id + '&quot;)">Add Another</a></p></div>';

				break;
			case 6: // number scale

				html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" >'; // dummy textbox
				html = html + '<p class="scale">From&nbsp;&nbsp;<input type="text" value="' + scale_from + '" id="scale_from" name="scale_from" maxlength="5" >&nbsp;&nbsp; To&nbsp;&nbsp;<input type="text" value="' + scale_to + '" id="scale_to" maxlength="5" name="scale_to" ></p>';
				break;
			case 7: // grid

				html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" >'; // dummy textbox
				html = html + '<div class="row-label">Row Label</div>';

				row_text_arr = row_text.split(",");
				counter1 = row_text_arr.length;

				for (k = 0; k < row_text_arr.length; k++) {
					html = html + '<div id="row' + k + '">';
					html = html + '<p ><input type="text" id="option_cntrl" name="row' + random_id + '[]" value="' + row_text_arr[k] + '"></p>';
					html = html + '<div id="remove_ele"><a href="javascript:;" onclick="remove_grid_row(' + k + ')" >Remove</a></div>';
					html = html + '</div>';
				}


				html = html + '<div id="grid_row"><p class="add-other-row" ><a href="javascript:;" onclick="add_another_row(' + counter1 + ',&quot;' + random_id + '&quot;)">Add Another Row</a></p></div>';

				html = html + '<div class="column-label">Column Label</div>';

				column_text_arr = column_text.split(",");
				counter2 = column_text_arr.length;

				for (k = 0; k < column_text_arr.length; k++) {
					html = html + '<div id="column' + k + '">';
					html = html + '<p ><input type="text" id="option_cntrl" name="column' + random_id + '[]" value="' + column_text_arr[k] + '"></p>';
					html = html + '<div id="remove_ele"><a href="javascript:;" onclick="remove_grid_column(' + k + ')" >Remove</a></div>';
					html = html + '</div>';
				}

				html = html + '<div id="grid_column"><p class="add-other-column" ><a href="javascript:;" onclick="add_another_column(' + counter2 + ',&quot;' + random_id + '&quot;)">Add Another Column</a></p></div>';

				break;
			case 8: // date
			case 9: // time
				html = html + '<p><input type="text" id="textbox' + random_id + '" readonly name="textbox' + random_id + '" ></p>'; // dummy textbox
				break;
			case 11: // image

				html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" value="' + ele_val + '">'; // existing filename
				html = html + '<div class="image-label">Image Title</div><input type="text" id="image_title' + random_id + '" name="image_title' + random_id + '" value="' + image_title + '">';
				html = html + '<div class="image-label">Select File<span class="hint-info">(File type JPG, JPEG, PNG, GIF)</span><input id="uploaded_file" type="hidden" value="1" name="uploaded_file"></div>';

				html = html + '<iframe src="<?php echo url('/test/temp_image_upload'); ?>?profile_id=<?php echo $profile_id; ?>&random_id=' + random_id + '" id="logoUpload" scrolling="no" frameborder="0" width="94%" style="padding-left:10px" height="70"></iframe>';
				html = html + '<div class="image-label">Alignment :&nbsp;';

				if (image_align == "left")
					html = html + '<input type="radio" id="" class="image_align" checked value="left"  name="image_align' + random_id + '" >Left&nbsp;';
				else
					html = html + '<input type="radio" id="" class="image_align" value="left"  name="image_align' + random_id + '" >Left&nbsp;';

				if (image_align == "center")
					html = html + '<input type="radio" id="" value="center" checked name="image_align' + random_id + '" >Center&nbsp;';
				else
					html = html + '<input type="radio" id="" value="center" name="image_align' + random_id + '" >Center&nbsp;';

				if (image_align == "right")
					html = html + '<input type="radio" id="" value="right" checked name="image_align' + random_id + '" >Right';
				else
					html = html + '<input type="radio" id="" value="right" name="image_align' + random_id + '" >Right';

				html = html + '</div>';

				break;

			case 16: // signature

				if (include_name == '1') {
					name_chk = "checked";
				} else {
					name_chk = "";
				}
				if (include_employer == '1') {
					emp_chk = "checked";
				} else {
					emp_chk = "";
				}
				if (include_email == '1') {
					email_chk = "checked";
				} else {
					email_chk = "";
				}
				if (include_phone == '1') {
					phone_chk = "checked";
				} else {
					phone_chk = "";
				}

				html = html + '<p><input type="text" id="textbox' + random_id + '" value="' + ele_val + '" name="textbox' + random_id + '" placeholder="Enter Your Text"></p>';
				html = html + '<div class="hazard-class"><div><input class="sign-text include-name" ' + name_chk + ' id="include_name" type="checkbox"> Include Name</div></div>';
				html = html + '<div class="hazard-class"><div><input class="sign-text include-employer" ' + emp_chk + ' id="include_employer" type="checkbox"> Include Employer/Company</div></div>';
				html = html + '<div class="hazard-class"><div><input class="sign-text include-email" ' + email_chk + ' id="include_email" type="checkbox"> Include Email</div></div>';
				html = html + '<div class="hazard-class"><div><input class="sign-text include-phone" ' + phone_chk + ' id="include_phone" type="checkbox"> Include Phone</div></div>';

				break;

			case 18: // Participant name
				if (participant_include_signature == '1') {
					sign_chk = 'checked';
				} else {
					sign_chk = '';
				}
				if (participant_include_employer == '1') {
					part_emp_chk = 'checked';
				} else {
					part_emp_chk = '';
				}
				html = html + '<p><input type="text" id="textbox' + random_id + '" value="' + ele_val + '" name="textbox' + random_id + '" placeholder="Enter Your Text"></p>';
				html = html + '<div class="hazard-class"><div><input class="sign-text include-name" ' + sign_chk + ' id="participant_include_signature" type="checkbox"> Include Signature</div></div>';
				html = html + '<div class="hazard-class"><div><input class="sign-text include-employer" ' + part_emp_chk + ' id="participant_include_employer" type="checkbox"> Include Employer/Company</div></div>';
				break;

			case 20: //web link button

				html = html + '<input type="text" class="btn_text" id="textbox' + random_id + '" name="textbox' + random_id + '" value="' + ele_val + '"  placeholder="Button Text" ><br>';
				html = html + '<input type="text" class="btn_link" id="btn_link' + random_id + '" name="btn_link' + random_id + '" value="' + button_link_url + '"  placeholder="Button Link URL  (start with http:// or https://) " ><br>';
				html = html + '<input type="text" class="btn_colour color" id="btn_colour' + random_id + '" name="btn_colour' + random_id + '" onclick="show_color_picker(this)" maxlength="6" value="' + button_colour + '" style="background-color:#' + button_colour + '" >';

				break;
			case 21: // Document Button

				html = html + '<input type="hidden" id="textbox' + random_id + '" name="textbox' + random_id + '" value="' + ele_val + '">'; // existing filename
				html = html + '<div class="image-label">Document Title</div><input type="text" id="doc_title' + random_id + '" name="doc_title' + random_id + '" value="' + doc_title + '"  placeholder="Enter Your Text">';
				html = html + '<div class="image-label">Select File<span class="hint-info">(File type  DOC, DOCX, PDF, JPG, GIF, JPEG)</span><input id="uploaded_doc' + random_id + '" type="hidden" value="1" name="uploaded_doc"></div>';
				html = html + '<iframe src="<?php echo url('/test/temp_doc_upload'); ?>?profile_id=<?php echo $profile_id; ?>&random_id=' + random_id + '" id="docUpload" scrolling="no" frameborder="0" width="94%" style="padding-left:10px" height="70"></iframe>';
				html = html + '<input type="text" class="btn_colour color" id="doc_color' + random_id + '" name="doc_color' + random_id + '" onclick="show_color_picker(this)"  maxlength="6" value="' + button_colour + '" style="background-color:#' + button_colour + '" >';
				break;

			case 22: // SWMS Hazard/Risk

				html = html + '<p><input type="text" id="textbox' + random_id + '" value="' + ele_val + '" name="textbox' + random_id + '" placeholder="Enter Your Text"></p>';
				html = html + '<div class="image-label">Task | Activity</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';
				html = html + '<div class="image-label">Potential Hazards</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';
				html = html + '<div class="image-label">Risk Score (Before)</div>';

				html = html + '<div style="display: flex; width: 347px;">';
				html = html + '<div class="hazard-class" style="width: 100%;"><div class="selector"><span style="width: 126px;">Select</span></div></div>';

				// new file upload control
				html = html + '<div style="display: flex;align-items: center;min-width: 70px;margin-left:15px;"><img src="<?php echo rtrim(url('/'), '/'); ?>/form-builder/images/camera.png" style="height:25px;width:25px;" />&nbsp;<span stye="margin-top:5px !important;">Photo</span></div>';
				//new file upload control end

				html = html + '</div>';

				html = html + '<div class="image-label">Control Measures</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';
				html = html + '<div class="image-label">Risk Score (After)</div>';
				html = html + '<div class="hazard-class"><div class="selector"><span style="width: 126px;">Select</span></div></div>';
				break;
			case 23: // Document Menu

				html = html + '<input type="hidden" id="textbox' + random_id + '" value="' + ele_val + '" name="textbox' + random_id + '" >'; // dummy textbox
				html = html + '<div class="image-label">Document Title <small>(Please separate each title with a comma)</small></div><input type="text" id="doc_title' + random_id + '" name="doc_title' + random_id + '" value="' + doc_title + '" placeholder="Enter Your Text">';
				html = html + '<div class="image-label">Select File<span class="hint-info">(File type  DOC, DOCX, PDF, JPG, GIF)</span><input id="uploaded_doc' + random_id + '" type="hidden" value="1" name="uploaded_doc"></div>';
				html = html + '<iframe src="<?php echo url('/test/temp_doc_multi_upload'); ?>?profile_id=<?php echo $profile_id; ?>&random_id=' + random_id + '" id="docUpload" scrolling="no" frameborder="0" width="94%" style="padding-left:10px" height="70"></iframe>';

				if (doc_uploaded == "") {
					var mystr = ele_val;
					var myarr = mystr.split(",");

					for (var i = 0; i < myarr.length; i++) {
						html = html + '<a target="_blank" class="image-doc-link" href="<?php echo rtrim(url('/'), '/').'/images/formbuilder_upload/'; ?>' + myarr[i] + '">' + myarr[i] + '</a><br>';
					}
					html = html + '<br>';
				} else {
					html = html + doc_uploaded;
				}

				break;
				// New code 19-03-2021
			case 25:
				html = html + '<textarea rows="1" id="textbox' + random_id + '" name="textbox' + random_id + '" ></textarea>'
				html = html + '<hr>';

				html = html + '<br>';
				html += '<div class="box-main">';

				html += '<div class="row">';
				html += '<div class="col">';
				html = html + '<div class="image-label text-center">Text colour</div>';
				html += '<div class="hazard-class">';
				html += '<input type="text" class="text-fi color {required:false}" value="#' + text_color + '" name="text_color" id="text_color' + random_id + '" autocomplete="off">';

				html += '</div>';
				html += '</div>';

				html += '<div class="col">';
				html = html + '<div class="image-label text-center">Background color</div>';
				html += '<div class="hazard-class">';
				html += '<input type="text" class="text-fi color {required:false}" value="#' + bg_color + '" name="bg_color" id="bg_color' + random_id + '" autocomplete="off">';
				html += '</div>';
				html += '</div>';
				html += '</div>';

				html = html + '<p style="display: inline-flex; flex-direction: row; justify-content: flex-start; align-items: center; flex-wrap: wrap; padding: 10px 0px 20px 0px;">';
				html = html + '	<image style="float:left;width:30;height:31;" src="../../images/form_submission.png">';
				html = html + '	<span>Record entry on Form Submission Log&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				html = html + '	<input type="checkbox" name="is_logchecked' + random_id + '" id="is_logchecked' + random_id + '" ' + (is_logchecked ? "checked" : '') + '/>';

				html = html + '<div class="image-label">Visitor Name</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';

				html = html + '<div class="image-label ">Visitor Phone</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2 "></div></div>';

				html += '<div class="row">';
				html += '<div class="col">';
				html += '<div class="hazard-class">';
				html = html + '<div class="image-label">Date</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2 "></div></div>';
				html += '</div>';
				html += '</div>';

				html += '<div class="col">';
				html += '<div class="hazard-class">';
				html = html + '<div class="image-label">Time</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2 "></div></div>';
				html += '</div>';
				html += '</div>';
				html += '</div>';

				html = html + '<div class="image-label">Venue Name</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';

				html = html + '<div class="image-label">Venue address</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2 "></div></div>';

				html = html + '<div class="image-label">Location Description/Type	</div>';
				html = html + '<div class="hazard-class"><div class="text-box-black2"></div></div>';
				html += '</div>';

				break;

			default:
				if (question_type_id != 25) {

					html = html + '<p><input type="text" id="textbox' + random_id + '" value="' + ele_val + '" name="textbox' + random_id + '" placeholder="Enter Your Text"></p>';
				}
		}

		html += '</div>';


		$(ele_obj).after(html);

		// New code 19-03-2021
		if (question_type_id == '25') {
			var colinput = document.getElementById('text_color' + random_id);
			var col = new jscolor.color(colinput);

			var colinput = document.getElementById('bg_color' + random_id);
			var col = new jscolor.color(colinput);
		}

		if (question_type_id == 2 || question_type_id == 25) //enable ckeditor for text type
		{
			CKEDITOR.replace('textbox' + random_id, {
				toolbar: 'custom',
				enterMode: CKEDITOR.ENTER_BR
			});
			CKEDITOR.instances['textbox' + random_id].setData(ele_val_ori);

		}

		setTimeout(function() {
			if ($p('span.expand-reduce').text() == "Reduce Window") {

				ul_scroll_height = $('.ui-droppable').prop('scrollHeight') + 10;
				$('#div_drop_area').css('height', ul_scroll_height);
				$p('#iframe_frm_builder').height(ul_scroll_height + $('.top-part').height());
			}

		}, 3000);
		$(":checkbox:not(.sign-text)").uniform();

	}

	function remove_div_and_data(obj, question_id) {

		$.ajax({
			type: "GET",
			url: "<?php echo url('/portal/legacy-form-builder/remove-element'); ?>",
			dataType: "html",
			data: {
				"profile_id": '<?php echo $profile_id; ?>',
				'question_id': question_id
			},
			success: function() {
				// Only drop the element from the canvas once the server confirms the
				// soft-delete, so the UI can never desync from the stored form.
				$('#' + obj).remove();
				notifyParentFormSaved();
			},
			error: function() {
				alert('Could not remove this element. Please try again.');
			}
		});
	}
</script>
<style>
	.covid-checkin-header {
		border-color: red;
		border-width: 3px;
		border-radius: 0 0 20px 20px;
		padding-bottom: 15px;
	}

	.covid-checkin-header .green-first-box {
		background-color: red !important;
		margin-bottom: 10px;
	}

	.covid-checkin-header .green-first-box h1,
	.covid-checkin-header .green-first-box h2 a {
		color: #fff;
		font-weight: 600;
		font-size: 16px;
	}

	.covid-checkin-header .row {
		float: left;
		width: 100%;
	}

	.covid-checkin-header .col {
		width: calc(50% - 10px);
		float: left;
	}

	.covid-checkin-header .col input[type=text] {
		width: calc(100% - 30px) !important;
		border-radius: 8px;
	}

	.covid-checkin-header .col .text-box-black2 {
		width: calc(100% - 12px);
	}

	.text-box-black2 {
		width: calc(100% - 12px);
	}

	.covid-checkin-header .col:last-child {
		float: right;
	}

	.hazard-class {
		padding: 5px 0px;
	}

	.covid-checkin-header .col .hazard-class {
		display: inline-block;
		width: 100%;
		padding: 5px 0px;
	}

	.field-color {
		background-color: #b1d1ff;
	}

	.image-label {
		font-family: Arial, Helvetica, sans-serif;
		color: #5f5f5f;
		font-size: 14px;
	}

	.text-center {
		text-align: center;
	}

	.box-main {
		padding: 0 15px;
	}

	.checkin-form-btn {
		background: red;
		color: #fff;
		text-align: center;
		padding: 10px 15px;
		border-radius: 8px;
		text-transform: uppercase;
		font-weight: bold;
	}

	.covid-checkinform .input-field {
		width: 90%;
		max-width: 90%;
	}
</style>