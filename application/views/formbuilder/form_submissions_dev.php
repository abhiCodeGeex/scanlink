<?php if ($errormessage != "") : ?>
	<div class="row-r"><label class="error"><?php echo $errormessage; ?></label></div>
	<div class="clear">&nbsp;</div>
<?php endif; ?>
<?php if ($successmessage != "") : ?>
	<div class="row-r"><label class="success"><?php echo $successmessage; ?></label></div>
	<div class="clear">&nbsp;</div>
<?php endif; ?>

<?php echo $innermenu;
$header_array = array();
if (!$expried_or_not) { ?>

	<div id="divToPrint" style="float: left">
		<h3 style="margin: 0px;">Form Submission Log<br></h3>
		Profile <?php echo $profile_id, '. ';
					if ($profile["code_profile_name"] != "") {
						echo $profile["code_profile_name"];
					}

					?>
	</div>
	<style>
		.button_downloads {
			display: block;
			width: 115px;
			height: 25px;
			background: #008901;
			padding: 10px;
			text-align: center;
			border-radius: 5px;
			color: white;
			font-weight: bold;
		}

		.table-responsive {
			display: block;
			width: 100%;
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
			-ms-overflow-style: -ms-autohiding-scrollbar;
		}

		.table-responsive table {
			width: 100%;
			max-width: 100%;
		}
	</style>
	<section class="search-right">
		<!-- PAGE RIGHT SEARCH BOX -->

		<ul class="form-view">
			<?php if (isset($profile) && !empty($profile)) { ?>
				<input type="hidden" value="" name="downloadall" id="downloadsall" />
				<li> <a style="cursor:pointer;" class="button_downloads" onclick="downloadall('9067-240420161623');">DOWNLOAD ALL</a></li>
			<?php } ?>
			<li>
				<label>From Date</label>
				<input type="text" value="" name="from_date" style="width: 76px;" id="from_date" class="text-fi" />
			</li>
			<li>
				<label>To Date</label>
				<input type="text" value="" name="to_date" style="width: 76px;" id="to_date" class="text-fi" />
			</li>

			<?php if (isset($profile) && !empty($profile)) { ?>
				<li><input type="button" id="search_data" name="search_data" onclick="javascript:getResults(1,'date','date_time','desc');" value="Search" /></li>
				<li><input type="button" id="export_data" name="export_data" onclick="javascript:export_data('','',1)" value="Export" /></li>
			<?php } ?>

			<li class="">

				<input type="button" onclick="javascript:history.back();" value="Return to List" />
			</li>
		</ul>
	</section><!-- /PAGE RIGHT SEARCH BOX -->


	<?php if (isset($profile) && !empty($profile)) { ?>
		<?php
		echo Form::open(
			URL::base() . '',
			array(
				'id' => 'frm',
				'name' => 'frm',
				'method' => 'post',
				'enctype' => 'multipart/form-data',
			)
		);

		?>

		<div class="clear"></div>
		<table width="100%" cellspacing="0" cellpadding="0" id="table" class="listing-table">
			<thead>
				<tr>
					<th width="10%" align="center" class="left-round"><strong>No.<strong class="txt-left"></th>
					<th width="10%" align="left"><strong class="txt-left">Date / Time</strong></th>

					<?php
					if (count($question_header) > 0) {

						for ($k = 0; $k < count($question_header); $k++) {
							$header_array[$k][0] = $question_header[$k]['question_id'];
							$header_array[$k][1] = $question_header[$k]['is_logchecked'];

							if ($question_header[$k]['question_type_id'] == 25) { ?>
								<th width="20%" align="center"><strong class="txt-left">Name</strong></th>
								<th width="10%" align="center"><strong class="txt-left">Phone</strong></th>
								<th width="15%" align="center"><strong class="txt-left">Venue Address</strong></th>
								<th width="15%" align="center"><strong class="txt-left">Location Description/Type</strong></th>
								<th width="10%" align="center"><strong class="txt-left">Vehicle Reg No</strong></th>

							<?php } else { ?>

								<th width="<?php echo 20 / count($question_header) ?>%" align="center">
									<strong class="txt-left"><?php echo $question_header[$k]['log_columntitle']; ?><strong class="txt-left">
								</th>
					<?php
							}
						}
					}
					?>
					<th width="20%" class="right-round"><strong class="txt-left">Options</strong></th>
				</tr>
			</thead>
			<tbody id="records">
			</tbody>
			<!-- <tr>
      <td colspan="6" align="center" id="paging"></td>
    </tr> -->
		</table>
		<?php echo Form::close() ?>
		<div class="table-responsive">
			<table width="100%" cellpadding="0" cellspacing="0" id="table">
				<tr>
					<td align="center" id="paging"></td>
				</tr>
			</table>
		</div>
	<?php
	} else {
		echo '<div class="clear"></div><div style="text-align:center;color:#5286BE;">No Form Submissions Found!</div>';
	}
} //end if $expried_or_not
else { ?>
	<p>You can not perform this action on expired profile.</p>
<?php } ?>





<script type="text/javascript">
	$(document).ready(function() {
		getResults(1, '', 'date_time', 'desc');
	});


	$(document).ready(function() {
		$("#from_date,#to_date").datepicker({
			dateFormat: 'dd/mm/yy'
		});
	});

	$('#renew_code').click(function() {

		var isSelected = false;
		$('.selectprofile').each(function() {
			if ($(this).is(':checked')) {
				isSelected = true;
			}
		});
		if (isSelected) {
			$('#frmdashcode').submit();
		} else {
			alert("Please select the plant & equipment to be renew.");
			return false;
		}
		return false;
	});

	function getResults(page, filter, sorton, sortby, group_by) {
		if (sorton == '') {
			sorton = 'date_time';
		}

		page_size = 50;

		if (filter.length > 0) {
			new_filter = '';
			if (filter == "date") {
				if ($("#from_date").val() != "")

					new_filter = new_filter + "from_date=" + encodeURIComponent($("#from_date").val());
				if ($("#to_date").val() != "") {
					if (new_filter == "")
						new_filter = "to_date=" + encodeURIComponent($("#to_date").val());
					else
						new_filter = new_filter + "&to_date=" + encodeURIComponent($("#to_date").val());
				}


			}

			filter = new_filter;

		}

		var request = $.ajax({
			url: '<?php echo URL::base(); ?>test/getFormData',
			type: "POST",
			dataType: "json",
			data: {
				curpage: page,
				perpage: page_size,
				entity: 'form_builder_answers',
				filter: filter,
				filter_on: sorton,
				filter_by: sortby,
				group_by: 'session_id', // 'app_user_firstname,app_user_lastname',
				params: {
					profile_id: <?php echo $profile_id; ?>
				}
			}
		});

		request.done(function(data) {
			var html = "";
			counter = ((page - 1) * page_size) + 1;

			if (data.results.length == 0)
				$('#uniform-export_data').hide();
			else
				$('#uniform-export_data').show();

			var array = [];
			for (var i = 0; i < data.results.length; i++) {
				html += '<tr class="row_number" id="' + data.results[i].session_id + '">' +
					'<td align="center" class="left-border">' + counter + '</td>' +
					'<td class="product-title">' + data.results[i].date_time_formated + ' </td>';

				var header_array = <?php echo json_encode($header_array); ?>;


				for (var k = 0; k < header_array.length; k++) {

					var temp_bool = false;
					for (var p = 0; p < data.answers[i].length; p++) {
						var str1 = data.answers[i][p].question_id;
						var str2 = header_array[k][0];
						if (str1.localeCompare(str2) == 0 && header_array[k][1] == 1) {
							var string = data.answers[i][p].question_answer;
							if (string.indexOf(":::") != -1) {

								temp_bool = true;
								var covid_string = string.split(":::");
								html += '<td align="center" class="product-title">' + (covid_string[0] ? covid_string[0] : "") + '';
								html += '' + (covid_string[1] ? covid_string[1] : '') + '<br>';
								html += '' + (covid_string[5] ? covid_string[5] : '') + '<br>';
								html += '' + (covid_string[6] ? covid_string[6] : '') + '<br>';
								if (covid_string[6] == "Vehicle") {
									html += '' + (covid_string[7] ? covid_string[7] : '') + '<br>';
								} else {
									html += '-</td>';
								}
							} else {
								temp_bool = true;
								html += '<td align="center" class="product-title">' + data.answers[i][p].question_answer + '</td>';
							}
						}
					}
					if (!temp_bool) html += '<td class="product-title"></td>';
				}
				html += '<td class="right-border"><table width="100%" align="center" cellspacing="0" cellpadding="0" class="form-submission-table" >' +
					'<tbody><tr>' +
					'<td align="center" class="border-none"><a href="<?php echo URL::base(); ?>formbuilder/form_submissions_view/?profile_id=' + <?php echo $profile_id; ?> + '&session_id=' + data.results[i].session_id + '&from_date=' + $("#from_date").val() + '&to_date=' + $("#to_date").val() + '&app_user_name=' + data.results[i].app_user_firstname + '::' + data.results[i].app_user_lastname + '" class="tooltip edtplant view" title="View Submission Response"></a></td>' +
					'<td align="center" class="border-none"><a href="javascript:;" onclick="javascript:download_data(\'' + escape(data.results[i].app_user_firstname) + '\',\'' + escape(data.results[i].app_user_lastname) + '\',\'' + escape(data.results[i].session_id) + '\',0)" class="tooltip download" title="Download Submission Response"></a></td>' +
					'<td align="center" class="border-none" style="<?php echo (empty($user_data['is_sub_user']) && $user_data['access_delete']) ? '' : 'display:none' ?>"><a href="javascript:;" onclick="javascript:delete_data(\'' + escape(data.results[i].session_id) + '\',' + page + ',this)" class="tooltip delete" title="Delete Response"></a></td>' +
					'</tr></tbody>' +
					'</table></td>' +
					'</tr>';
				array.push(data.results[i].session_id);
				counter++;
			}

			//alert(array);

			$("#downloadsall").val(array);
			$("#records").html(html);
			$("input:checkbox").uniform();
			var html = [];
			for (var i = 0; i < data.links.length; i++) {
				html.push('<a href="javascript:' + data.links[i].href + '" class="' + data.links[i].myClass + '">' + data.links[i].label + '</a>');
			}
			if (data.results.length > 0) {
				$("#paging").html(html.join(""));
			} else {
				$("#paging").html("<h3>No Results Found</h3>");
			}

		});

		request.fail(function(jqXHR, textStatus) {
			alert("Request failed: " + textStatus);
		});
	}
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".inline").colorbox({
			inline: true,
			width: "25%"
		});
	});

	function export_data(app_user_firstname, app_user_lastname, download_all) {
		location.href = '<?php echo URL::base() ?>formbuilder/export/' + <?php echo $profile_id; ?> + '?app_user_firstname=' + app_user_firstname + '&app_user_lastname=' + app_user_lastname + '&download_all=' + download_all + '&from_date=' + $("#from_date").val() + '&to_date=' + $("#to_date").val();
	}

	function download_data(app_user_firstname, app_user_lastname, session_id, download_all) {


		location.href = '<?php echo URL::base() ?>formbuilder/download/' + <?php echo $profile_id; ?> + '?session_id=' + session_id;

	}

	function downloadall(session_id) {
		var sessionarray = document.getElementById("downloadsall").value;
		//alert(sessionarray[0]);
		location.href = '<?php echo URL::base() ?>formbuilder/downloadpdf/' + <?php echo $profile_id; ?> + '?session_id=' + session_id + '&array=' + sessionarray;
	}

	function download_dataall() {
		//location.href='<?php echo URL::base() ?>formbuilder/download/'+ <?php echo $profile_id; ?>+'?app_user_firstname='+app_user_firstname+'&app_user_lastname='+app_user_lastname+'&download_all='+download_all+'&from_date='+$("#from_date").val()+'&to_date='+$("#to_date").val();
		var sessionarray = document.getElementById("downloadsall").value;
		var str = sessionarray;
		var str_array = str.split(',');
		//location.href='<?php echo URL::base() ?>formbuilder/download/'+ <?php echo $profile_id; ?>+'?session_id='+sessionarray[0];
		for (var i = 0; i < str_array.length; i++) {
			// Trim the excess whitespace.
			str_array[i] = str_array[i].replace(/^\s*/, "").replace(/\s*$/, "");
			// Add additional code here, such as:
			// alert(str_array[i]);
			// setTimeout(function(){ locationdownloads(str_array[i]) }, 1500);
			window.open('<?php echo URL::base() ?>formbuilder/downloadpdf/' + <?php echo $profile_id; ?> + '?session_id=' + str_array[i]);

		}
		//alert(sessionarray);
		//location.href='<?php echo URL::base() ?>formbuilder/download/'+ <?php echo $profile_id; ?>+'?session_id='+session_id;


	}

	function locationdownloads(str) {
		window.location.href = '<?php echo URL::base() ?>formbuilder/download/' + <?php echo $profile_id; ?> + '?session_id=' + str;
	}

	function delete_data(session_id, page, obj) {
		if (confirm("Are you sure to delete this?")) {
			tr_id = $(obj).closest('#' + session_id);


			var request = $.ajax({
				url: '<?php echo URL::base(); ?>formbuilder/form_builder_response_delete',
				type: "POST",
				dataType: "json",
				data: {
					delete_id: session_id,
					profile_id: <?php echo $profile_id; ?>
				}

			});

			request.done(function(data) {
				if (data == "success") {
					//getResults(page,'','date_time','desc');
					alert("Removed Successfully.")
					tr_id.remove();
				}
			});

		}
	}
</script>