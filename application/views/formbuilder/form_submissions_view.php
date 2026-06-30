<?php if ($errormessage != "") : ?>
	<div class="row-r"><label class="error"><?php echo $errormessage; ?></label></div>
	<div class="clear">&nbsp;</div>
<?php endif; ?>
<?php if ($successmessage != "") : ?>
	<div class="row-r"><label class="success"><?php echo $successmessage; ?></label></div>
	<div class="clear">&nbsp;</div>
<?php endif; ?>

<div id="response_view">
	<form action="" method="post" name="frm_response" id="frm_response">
		<input type="hidden" name="delete_id" id="delete_id" value="" />

		<?php if ($profile_logo == "")
			$profile_logo = 'images/logo.png'; ?>

		<img src="<?php print URL::base() . $profile_logo; ?>" height="108" alt="" />
		<hr />
		<p>
		<div class="label"><b>Profile Number</b> :</div> <?php echo $profile_id; ?>
		</p>
		<div style="clear: both;"></div>
		<p>
		<div class="label"><b>Profile Name</b> :</div> <?php if ($profile["code_profile_name"] != "")
																			echo $profile["code_profile_name"];
																		?>
		</p>
		<div style="clear: both;"></div>
		<p>
			<hr />
		</p>
		<p></p>
		<div style="clear: both;"></div>
		<div class="results">
			<?php /*echo "<pre>";
		print_r($answer_time);
		 echo "</pre>";*/

			echo "<div align='right'>Date/Time: " . $answer_time[0]["date_time_formated"] . "</div>";
			foreach ($questions_arr as $question_item) {
				$question_type_id = $question_item["question_type_id"];

				if ($question_type_id == "10")  // heading
					echo '<h1>' . $question_item["question_text"] . '</h1>';
				elseif ($question_type_id == "12")   // subheading
					echo '<h3>' . $question_item["question_text"] . '</h3>';
				elseif ($question_type_id == "2")  /// question text
				{
					if (isset($question_item["question_text"]))
						echo $question_item["question_text"] . '<br>';
				} else {
					$question_id = $question_item["question_id"];

					$answer_arr = Model_formbuilder::form_builder_answers($cond . " and question_id='" . $question_id . "'");


					foreach ($answer_arr as $answer_item) {
						switch ($question_type_id) {
							case "3": // Mutliple choise
							case "4": // Checkboxes
							case "5": // Drop Down Menu 
								$question_options = Model_formbuilder::get_question_option($question_id);

								$question_option_text = "";
								foreach ($question_options as $question_option) {
									if ($question_option_text == "")
										$question_option_text = $question_option["option_name"];
									else
										$question_option_text = $question_option_text . "<br> " . $question_option["option_name"];
								}
								echo "<b>Options:</b><br> " . $question_option_text . "<br>";
								break;

							case "6": // Number Scale
								$question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

								$question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

								$scale_from = $question_options[0]["option_name"];

								$question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

								$question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

								$scale_to = $question_options[0]["option_name"];

								echo "<b>Range:</b> From " . $scale_from . " To " . $scale_to . "<br>";


								break;


							case "7": // Grid

								if ($answer_item["row_number"] == 0) //show question for once
									$_SESSION["grid"] = '<b>User Input:</b><br>';

								$grid_count = Model_formbuilder::get_count_for_grid_type($question_id, $profile_id, $answer_item["session_id"]);

								$question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

								$row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);


								$question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

								$column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);


								if ($answer_item["row_number"] == 0) // show colums once
								{
									$_SESSION["grid"] .=  "<table border='0' cellpadding='6' cellspacing='0'>";

									$_SESSION["grid"] .=  "<tr>";
									$_SESSION["grid"] .=  "<td>&nbsp;</td>";
									for ($i = 0; $i < count($column_text); $i++) {
										$_SESSION["grid"] .=  "<td align='center'>" . $column_text[$i]["option_name"] . "</td>";
									}

									$_SESSION["grid"] .=  "</tr>";
								}


								for ($i = 0; $i < count($row_text); $i++) {
									if ($answer_item["row_number"] == $i) {
										$_SESSION["grid"] .=  "<tr>";
										$_SESSION["grid"] .=  "<td>" . $row_text[$i]["option_name"] . "</td>";



										for ($j = 0; $j < count($column_text); $j++) {
											if ($answer_item["question_answer"] == $j)
												$checked = "<img width='18' src='" . URL::base() . "images/green_check_mark_icon.png' >";
											else
												$checked = '&nbsp;';

											$_SESSION["grid"] .=  "<td  align='center'>" . $checked . "</td>";
										}

										$_SESSION["grid"] .=  "</tr>";
									}
								}

								if (($grid_count - 1) == $answer_item["row_number"]) // last row;
								{
									$_SESSION["grid"] .=  "</table><br>";


									echo $_SESSION["grid"];
									unset($_SESSION["grid"]);
								}
								break;
						} //end of switch

						if ($question_type_id != 7)  // not grid
						{
							if ($question_type_id == "16") //signature
							{


								$signature_image = explode(",", $answer_item["question_answer"]);
								$signature_name_text = explode(":::", $answer_item["signature_name_text"]);
								$signature_name_text2 = explode(":::", $answer_item["signature_employer_text"]);
								$signature_name_text3 = explode(":::", $answer_item["signature_email_text"]);
								$signature_name_text4 = explode(":::", $answer_item["signature_phone_text"]);

								for ($sig_cnt = 0; $sig_cnt < count($signature_image); $sig_cnt++) {
									if (isset($signature_name_text[$sig_cnt]) && $signature_name_text[$sig_cnt] != "")
										echo "<br>Name<br><b>User Input:</b>" . $signature_name_text[$sig_cnt] . "<br>";

									if (isset($signature_name_text2[$sig_cnt]) && $signature_name_text2[$sig_cnt] != "")
										echo "<br>Employer/Company<br><b>User Input:</b>" . $signature_name_text2[$sig_cnt] . "<br>";

									if (isset($signature_name_text3[$sig_cnt]) && $signature_name_text3[$sig_cnt] != "")
										echo "<br>Email<br><b>User Input:</b>" . $signature_name_text3[$sig_cnt] . "<br>";

									if (isset($signature_name_text4[$sig_cnt]) && $signature_name_text4[$sig_cnt] != "")
										echo "<br>Phone<br><b>User Input:</b>" . $signature_name_text4[$sig_cnt] . "<br>";

									if (isset($question_item["question_text"]))
										echo "<br>" . $question_item["question_text"] . '<br>';

									if (file_exists('images/signature/' . $signature_image[$sig_cnt]) && filesize('images/signature/' . $signature_image[$sig_cnt]) > 0)
										echo '<img src="' . URL::base() . 'images/signature/' . $signature_image[$sig_cnt] . '" /><br>';
								}
							} else if ($question_type_id == "17") //upload button
							{
								if (isset($question_item["question_text"]))
									echo $question_item["question_text"] . '<br>';

								if ($answer_item["question_answer"] != "") {
									$attachment_arr = explode(",", $answer_item["question_answer"]);

									for ($k = 0; $k < count($attachment_arr); $k++) {
										if (file_exists('images/formbuilder_upload/' . $attachment_arr[$k]) && filesize('images/formbuilder_upload/' . $attachment_arr[$k]) > 0)
											echo '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" target="_blank">Click here to view</a><br>';
									}
								}
								echo "<br>";
							} else if ($question_type_id == "18") //participant name
							{
								$participant_arr = explode(':::', $answer_item["question_answer"]);
								$participant_sign_image = explode(',', $answer_item["participant_signature_image"]);
								$participant_employer_text = explode(':::', $answer_item['participant_employer_text']);
								for ($p = 0; $p < count($participant_arr); $p++) {
									if (isset($question_item["question_text"]) && $question_item["question_text"] != "")
										echo $question_item["question_text"] . '<br>';
									else
										echo 'Name<br>';
									echo '<b>User Input:</b> ' . $participant_arr[$p] . "<br>";

									if (isset($participant_employer_text[$p]) && $participant_employer_text[$p] != "")
										echo "<br>Employer/Company<br><b>User Input:</b>" . $participant_employer_text[$p] . "<br>";

									if ($participant_sign_image[$p] != "" && file_exists('images/signature/' . $participant_sign_image[$p]) && filesize('images/signature/' . $participant_sign_image[$p]) > 0) {
										echo '<img src="' . URL::base() . 'images/signature/' . $participant_sign_image[$p] . '" /><br>';
									}
								}
							} else if ($question_type_id == "19") //location function
							{
								if (isset($question_item["question_text"]) && $question_item["question_text"] != "")
									echo $question_item["question_text"] . '<br>';

								echo '<b>User Input:</b> ' . $answer_item["question_answer"] . "<br>";
							} else if ($question_type_id == "22") // SWMS Hazard/Risk
							{
								$has_attachment = false;
								$attachment_array = array();
								if (strpos($answer_item["question_answer"], ':::') !== false) {

									$Hazard = explode(":::", $answer_item["question_answer"]);
								} else {
									$has_attachment = true;
									$attachment_array = explode(",", $answer_item["question_answer"]);
								}
								echo "<br>";
								$i = 0;
								$j = 0;
								while ($i < count($Hazard)) {
									if ($j == 0) {
										if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
											echo $question_item["question_text"] . '<br><br>';
										}
										echo "Task | Activity";
									}
									if ($j == 1) {

										echo "Potential Hazards";
									}
									if ($j == 2) {

										echo "Risk Score (Before)";
									}
									if ($j == 3) {

										if ($has_attachment && !empty($attachment_array)) {
											foreach ($attachment_array as $key => $attachment) {

												echo 'Attachment : <a target="_blank" href="' . url::base() . 'images/formbuilder_upload/' . $attachment . '"> View Attachment</a><br>';
											}
										}
									}
									if ($j == 4) {

										echo "Control Measures";
									}
									if ($j == 5) {

										echo "Risk Score (After)";
									}

									echo "<br><b>User Input:</b> " . $Hazard[$i] . "<br><br>";
									$i++;

									$j++;

									if ($j > 5) {

										$j = 0;
									}
								}
							} else if ($question_type_id == "23") {
								$arr_que = Model::factory('Formbuilder')->form_builder_questions(" and question_id='" . $question_id . "'");
								$arr_doc_title = explode(',', $arr_que[0]['doc_title']);
								$arr_doc_key = explode(',', $arr_que[0]['question_text']);

								if ($answer_item["question_answer"] != "") {
									$attachment_arr = explode(":::", $answer_item["question_answer"]);

									for ($k = 0; $k < count($attachment_arr); $k++) {
										$doc_key = array_search($attachment_arr[$k], $arr_doc_key);
										if (file_exists('images/formbuilder_upload/' . $attachment_arr[$k]) && filesize('images/formbuilder_upload/' . $attachment_arr[$k]) > 0)
											if ($doc_key !== false) {
												echo '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" target="_blank">' . $arr_doc_title[$doc_key] . '</a><br>';
											} else {
												echo '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" target="_blank">Click here to view</a><br>';
											}
									}
								}
								echo "<br>";
							} else if ($question_type_id == "25") {
								echo '<p>' . $question_item["question_text"] . '</p>';
								$checkinform_data = explode(":::", $answer_item["question_answer"]);

								echo '<p>Visitor name : ' . (!empty($checkinform_data[0]) ? $checkinform_data[0] : '') . '</p>';
								echo '<p>Visitor phone : ' . (!empty($checkinform_data[1]) ? $checkinform_data[1] : '') . '</p>';
								echo '<p>Date : ' . (!empty($checkinform_data[2]) ? $checkinform_data[2] : '') . '</p>';
								echo '<p>Time : ' . (!empty($checkinform_data[3]) ? $checkinform_data[3] : '') . '</p>';
								echo '<p>Venue Name : ' . (!empty($checkinform_data[4]) ? $checkinform_data[4] : '') . '</p>';
								echo '<p>Venue Address : ' . (!empty($checkinform_data[5]) ? $checkinform_data[5] : '') . '</p>';
								echo '<p>Location Description/Type: ' . (!empty($checkinform_data[6]) ? $checkinform_data[6] : '') . '</p>';

								if (!empty($checkinform_data[6]) && $checkinform_data[6] == 'Vehicle') {
									echo '<p>Vehicle Registration ID : ' . (!empty($checkinform_data[7]) ? $checkinform_data[7] : '') . '</p>';
								} else if (!empty($checkinform_data[6]) && $checkinform_data[6] == 'Other') {
									echo '<p>Other details : ' . (!empty($checkinform_data[7]) ? $checkinform_data[7] : '') . '</p>';
								}
								// echo '<b>User Input:</b> ' . str_replace(':::', '<br>', $answer_item["question_answer"]) . "<br><br>";

							} else
								echo '<b>User Input:</b> ' . str_replace(':::', '<br>', $answer_item["question_answer"]) . "<br><br>";
						} //end of if($question_type_id!=7 ) 
					} //end of foreach($answer_arr as $answer_item)

					if (count($answer_arr) == 0 && $question_type_id != "14" && $question_type_id != "11" && $question_type_id != 13 && $question_type_id != "16"  && $question_type_id != "17") {

						switch ($question_type_id) {
							case "3": // Mutliple choise
							case "4": // Checkboxes
							case "5": // Drop Down Menu 
								$question_options = Model_formbuilder::get_question_option($question_id);

								$question_option_text = "";
								foreach ($question_options as $question_option) {
									if ($question_option_text == "")
										$question_option_text = $question_option["option_name"];
									else
										$question_option_text = $question_option_text . "<br> " . $question_option["option_name"];
								}
								echo "<b>Options:</b><br> " . $question_option_text . "<br>";
								break;

							case "6": // Number Scale
								$question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

								$question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

								$scale_from = $question_options[0]["option_name"];

								$question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

								$question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

								$scale_to = $question_options[0]["option_name"];

								echo "<b>Range:</b> From " . $scale_from . " To " . $scale_to . "<br>";

								break;

							case "7": // Grid
								echo  '<b>User Input:</b><br>';

								$question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

								$row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);


								$question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

								$column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

								echo "<table border='0' cellpadding='6' cellspacing='0'>";

								echo "<tr>";
								echo "<td>&nbsp;</td>";
								for ($i = 0; $i < count($column_text); $i++) {
									echo "<td align='center'>" . $column_text[$i]["option_name"] . "</td>";
								}

								echo "</tr>";

								for ($i = 0; $i < count($row_text); $i++) {
									echo "<tr>";
									echo "<td>" . $row_text[$i]["option_name"] . "</td>";

									for ($j = 0; $j < count($column_text); $j++) {
										echo "<td  align='center'>&nbsp;</td>";
									}

									echo "</tr>";
								}

								echo "</table><br>";

								break;
							case "18": // participant name
								if (isset($question_item["question_text"]) && $question_item["question_text"] != "")
									echo $question_item["question_text"] . '<br>';
								else
									echo "Name<br>";
								break;
							case "19": // location function
								if (isset($question_item["question_text"]) && $question_item["question_text"] != "")
									echo $question_item["question_text"] . '<br>';

								break;
						}

						if ($question_type_id != 7 && $question_type_id != 20 && $question_type_id != 21) //not grid, web link button and Document Button 
							echo  '<b>User Input:</b><br><br>';
					}
				}
			}
			?>

		</div>
		<?php if ($total_pages > 1) { ?>

			<table cellspacing="0" cellpadding="0" width="100%" align="center">
				<tr>
					<td align="center" id="paging" colspan="6">

						<?php
						if ($page_no != 1) //not first page
						{
							echo '<a class="pagelink " href="' . $current_url . '&page_no=1"> << </a>';
							echo '<a class="pagelink " href="' . $current_url . '&page_no=' . ($page_no - 1) . '"> < </a>';
						}
						for ($pg = 1; $pg <= $total_pages; $pg++) {
							if ($page_no == $pg)
								echo '<a class="pagelink current" href="javascript:;">' . $pg . '</a>';
							else
								echo '<a class="pagelink " href="' . $current_url . '&page_no=' . ($pg) . '">' . $pg . '</a>';
						}

						if ($page_no != $total_pages) //not last page
						{
							echo '<a class="pagelink " href="' . $current_url . '&page_no=' . ($page_no + 1) . '"> > </a>';
							echo '<a class="pagelink " href="' . $current_url . '&page_no=' . ($total_pages) . '"> >> </a>';
						}

						?>


					</td>
				</tr>
			</table>
		<?php
		} ?>
		<br />
	</form>
</div>
<script>
	$(document).ready(function() {
		$('.delete_icon').on('click', function() {
			if (confirm("Are you sure to delete this?")) {
				$('#delete_id').val($(this).attr('id'));
				$('#frm_response').submit();
			}
		});


	});
</script>