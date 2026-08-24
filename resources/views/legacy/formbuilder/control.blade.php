<?php
echo '<span style="display:none;" class="question_id_span">' . $question_id . '</span>';

switch ($question_type_id) {
	case "1": // Text Field
		//echo '<input type="text" class="text-box-black">';
		echo '<div class="text-box-black" ></div>';
		break;
	case "2": // Text

		echo '<div  class="text_block" >' . $user_input . '</div><div style="clear: both;"></div>';
		break;
	case "3": // Mutliple choice

		$option_text = explode(":::", $user_input);
		for ($i = 0; $i < count($option_text); $i++) {
			echo "<input type='radio' name='radio_list' />&nbsp;" . $option_text[$i] . "<br>";
		}
		break;
	case "4": // Checkboxes
		$option_text = explode(":::", $user_input);
		for ($i = 0; $i < count($option_text); $i++) {
			echo "<input type='checkbox' name='checkbox_list' />&nbsp;" . $option_text[$i] . "<br>";
		}
		break;
	case "5": // Drop Down Menu 	
		$option_text = explode(":::", $user_input);

		echo '<div class="selector" ><span style="width: 126px;">' . $option_text[0] . '</span></div>';
		break;
	case "6": // Number Scale

		for ($i = $scale_from; $i <= $scale_to; $i++) {
			echo "<input type='radio' name='radio_scale' value='" . $i . "' />&nbsp;" . $i . "<br>";
		}
		break;
	case "7": // Grid

		echo "<table border='0' cellpadding='3' cellspacing='15'>";
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		for ($i = 0; $i < count($column_text); $i++) {
			echo "<td align='center'>" . $column_text[$i] . "</td>";
		}

		echo "</tr>";


		for ($i = 0; $i < count($row_text); $i++) {
			echo "<tr>";
			echo "<td>" . $row_text[$i] . "</td>";
			for ($j = 0; $j < count($column_text); $j++) {
				echo "<td  align='center'><input type='radio' name='radio_scale" . $i . "' value='row_" . $i . "_col" . $j . "' /></td>";
			}
			echo "</tr>";
		}


		echo "</table>";

		break;
	case "8": // Date

		//echo '<input type="text" name="dtpicker_date" placeholder="Date Picker" readonly class="text-box-black">';
		echo '<div class="text-box-black" name="dtpicker_date" >Date Picker</div><br>';
		break;
	case "9": // time

		//echo '<input type="text" name="dtpicker_date" placeholder="Time Picker" readonly class="text-box-black">';
		echo '<div class="text-box-black" name="dtpicker_time" >Time Picker</div><br>';
		break;
	case "10": // heading
		echo "<h1>" . $user_input . "</h1>";
		break;
	case "11": // image

		if (($form_builder_image_temp ?? '') != "") {
			// Serve from the public storage disk (the upload always lands there); the legacy
			// public/images/ copy can fail on locked-down hosts, leaving a broken image.
			$fbImgFile = $form_builder_image_temp;
			$fbImgDiskPath = 'form-builder/images/' . $fbImgFile;
			$fbImgUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($fbImgDiskPath)
				? rtrim(url('/'), '/') . '/storage/' . $fbImgDiskPath
				: rtrim(url('/'), '/') . '/images/form_builder_uploaded_images/' . $fbImgFile;
			echo '<div align="' . $image_align . '"><img src="' . $fbImgUrl . '" width="100" title="' . $image_title . '" ></div>';
		}

		break;
	case "12": // sub heading
		echo "<h3>" . $user_input . "</h3>";
		break;
	case "13": // Line Divider
		echo "<hr>";
		break;
	case "14": // Blank Space
		echo "<br>";
		break;
	case "15": // comments

		//echo '<textarea name="dtpicker_date" placeholder="comments" readonly class="text-box-black"></textarea>';
		echo '<div name="div_comments" class="text-box-black">comments</div>';
		break;
	case "16": // signature

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
			echo '<div class="image-label">Phone</div>';
			echo '<div class="text-box-black"></div><br>';
		}

		//echo '<textarea name="dtpicker_date" placeholder="Signature" readonly class="text-box-black"></textarea>';
		if ($user_input != "")
			echo "<br><p>" . $user_input . "</p><br>";
		echo '<div name="div_sign" class="text-box-black">Signature</div>';

		break;
	case "17": // upload button

		if ($user_input != "")
			echo "<p>" . $user_input . "</p><br>";
		//echo '<input type="button" name="" style="background-color:#007A01;color:#ffffff;" class="text-box-black" value="Upload">';
		echo '<div name="" style="text-align:center; border:none; background-color:#007A01;color:#ffffff;" class="text-box-black" >Upload</div>';

		break;
	case "18": // participant name
		if ($user_input != "")
			echo "<p>" . $user_input . "</p>";
		echo '<div class="text-box-black" ></div>';
		if ($participant_include_employer == '1') {
			echo '<br><div class="image-label">Employer/Company</div>';
			echo '<div class="text-box-black"></div>';
		}
		if ($participant_include_signature == '1') {
			echo '<br><div name="div_sign" class="text-box-black">Signature</div>';
		}
		echo '<input type="hidden" name="participant_name_ele[]"  id="participant_name_ele" />';
		break;
	case "19": // location function
		if ($user_input != "")
			echo "<p>" . $user_input . "</p><br>";
		echo '<div class="text-box-black" ></div>';
		echo '<input type="hidden" name="location_ele[]"  id="location_ele" />';
		echo '<div name="" style="text-align:center; border:none;width:140px;margin-top:5px; background-color:#808080;color:#ffffff;" class="text-box-black" >MAP</div>';
		break;
	case "20": // web link button

		echo '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#' . $button_colour . ';color:#ffffff;" class="text-box-black" >' . $user_input . '</div>';

		break;

	case "21": // Document Button 

		echo '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#' . $button_colour . ';color:#ffffff;" class="text-box-black" >' . $doc_title . '</div>';
		break;

	case "22": // SWMS Hazard/Risk
		if ($user_input != "")
			echo "<p>" . $user_input . "</p><br>";
		echo '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#808080;color:#ffffff;" class="text-box-black" >SWMS Hazard/Risk</div>';
		break;

	case "23": // Document Menu 

		echo '<div style="height:auto;text-align:left;padding-left:10px; border:none; background-color:#808080;color:#ffffff;" class="text-box-black" >Document Menu</div>';
		echo '<div class="doc-uploaded" style="display:none;">';
		$doc_menu_file_arr = explode(",", (string) ($form_builder_doc_multi_temp ?? $user_input ?? ''));
		foreach ($doc_menu_file_arr as $doc_file) {
			$doc_file = trim((string) $doc_file);
			if ($doc_file === '') {
				continue;
			}
			echo '<a class="image-doc-link" target="_blank" href="' . rtrim(url('/'), '/').'/' . 'images/formbuilder_upload/' . $doc_file . '" >' . $doc_file . '</a><br>';
		}
		echo '<br>';
		echo '</div>';
		break;

	case "24": // Add recipient
		echo "<div class='add_recipient' style='height:auto;text-align:left;padding-left:10px; border:none;'>";
		echo "Send email notifications to <br>";
		echo "<div class='rounded' >";
		echo "<input style='width:90%' id='email_recipient_additional' class='email_recipient_additional' name='email_recipient__additional[]' required='required' type='text'>";
		echo "</div>";
		echo "<div class='add-another'><a href='javascript:;'>Add Another</a></div>";
		echo "</div>";
		break;

	case "25": //Covid check-in
		echo "<div class='checkin-form-btn'>COVID CHECK-IN</div>";
		// $html = '<div class="green-bx covid-checkin-header" id="' . $question_id . '">';
		// $html .=	'<div class="green-first-box">';
		// $html .=	'<h1>Covid check-in</h1>';
		// $html .=	'<a href="javascript:;" onclick="remove_div_and_data(' . $question_id . ')">';
		// $html .=	'<img src="' . rtrim(url('/'), '/').'/' . 'form-builder/images/close-button.png" border="0" alt="Remove" title="Remove"></a><div class="covid-checkin-header">';
		// $html .=	'</div></div>';

		// $html .= '<div class="user-input-text">' . (!empty($user_input) ? $user_input : 'Covid checkin form ') . '</div>';

		// $html .= '<hr>';

		// $html .= '<br>';
		// $html .= '<div class="box-main">';

		// $html .= '<div class="row">';
		// $html .= '<div class="col">';

		// $html .= '<div class="image-label text-center">Text Color</div>';
		// $html .= '<div class="hazard-class">';
		// $html .= '<div class="bg-color-code" style="background-color:#' . (!empty($covid_text_color) ? $covid_text_color : '#ffffff')  . '">';
		// $html .= '<input type="text" class="text-box-black" value="#' . isset($covid_text_color) ? $covid_text_color : 'ffffff' . '" id="text_color' . $question_id . '" class="text-fi" >';
		// $html .= "</div>";
		// $html .= "</div>";

		// $html .= '</div>';
		// $html .= '<div class="col">';

		// $html .= '<div class="image-label text-center">Background color</div>';
		// $html .= '<div class="hazard-class">';
		// $html .= '<div class="bg-color-code" style="background-color:#' . (!empty($covid_bg_color) ? $covid_bg_color : '#000000')  . '">';
		// $html .= '<input type="text" class="text-box-black" value="#' . isset($covid_bg_color) ? $covid_bg_color : '000000' . '" id="bg_color' . $question_id . '" class="text-fi" style="backgroud-color:' . (!empty($covid_bg_color) ? $covid_bg_color : '#000000')  . '">';
		// $html .= "</div>";
		// $html .= "</div>";

		// $html .= '</div>';
		// $html .= '</div>';

		// $html .= '<br>';

		// $html .= '<p style="display: inline-flex; flex-direction: row; justify-content: flex-start; align-items: center; flex-wrap: wrap; padding: 5px;">';
		// $html .= '	<image style="float:left;width:30;height:31;" src="../../images/form_submission.png">';
		// $html .= '	<span>Record text entry on Form Submission Log&nbsp;&nbsp;&nbsp;&nbsp;</span>';

		// $html .= '	<input type="checkbox" name="is_logchecked"/>';
		// $html .= '<div class="covid-checkinform">';

		// $html .= '<div class="image-label">Visitor Name</div>';
		// $html .= '<div class="hazard-class"><div class="text-box-black2"></div></div>';

		// $html .= '<div class="image-label">Visitor Phone</div>';
		// $html .= '<div class="hazard-class"><div class="text-box-black2 "></div></div>';

		// $html .= '<div class="row">';
		// $html .= '<div class="col">';

		// $html .= '<div class="image-label">Date</div>';
		// $html .= '<div class="hazard-class"><div class="text-box-black2 "></div></div>';

		// $html .= '</div>';
		// $html .= '<div class="col">';

		// $html .= '<div class="image-label">Time</div>';
		// $html .= '<div class="hazard-class"><div class="text-box-black2 "></div></div>';

		// $html .= '</div>';
		// $html .= '</div>';

		// $html .= '<div class="image-label">Venue Name</div>';
		// $html .= '<div class="hazard-class"><div class="text-box-black2"></div></div>';

		// $html .= '<div class="image-label">Venue address</div>';
		// $html .= '<div class="hazard-class"><div class="text-box-black2 "></div></div>';

		// $html .= '<div class="image-label">Location Description	</div>';
		// $html .= '<div class="hazard-class"><div class="text-box-black2"></div></div>';
		// $html .= '</div>';
		// echo $html;
		break;
}
?>
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

	.bg-color-code {
		color: #fff;
		border-radius: 8px;
		padding: 10px;
	}

	.user-input-text {
		display: inline-block;
		width: 100%;
	}

	.user-input-text span {
		display: inline-block;
		width: 100%;
		text-align: left;
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
</style>