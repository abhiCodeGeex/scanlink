<?php
if (!isset($_REQUEST["html"])) {

    $arr_country = json_decode($jsonCountry, true);

    $data_country_arr = array();
    $data_country_val_arr = array();

    $country_arr = $arr_country["DATA"];
    for ($i = 0; $i < count($country_arr); $i++) {
        $country_name = $country_arr[$i][1];
        $country_val = $country_arr[$i][2];

        array_push($data_country_arr, $country_name);
        array_push($data_country_val_arr, $country_val);

    }

    $arr_device = json_decode($jsonDevice, true);

    $data_device_arr = array();
    $data_device_val_arr = array();

    $device_arr = $arr_device["DATA"];
    for ($i = 0; $i < count($device_arr); $i++) {
        $device_name = $device_arr[$i][1];
        $device_val = $device_arr[$i][2];

        array_push($data_device_arr, $device_name);
        array_push($data_device_val_arr, $device_val);

    }

    $arr_browser = json_decode($jsonBrowser, true);

    $data_browser_arr = array();
    $data_browser_val_arr = array();

    $browser_arr = $arr_browser["DATA"];
    for ($i = 0; $i < count($browser_arr); $i++) {
        $browser_name = $browser_arr[$i][1];
        $browser_val = $browser_arr[$i][2];

        array_push($data_browser_arr, $browser_name);
        array_push($data_browser_val_arr, $browser_val);

    }
    ?>
<?php require_once 'jpgraph/src/jpgraph.php';
    require_once 'jpgraph/src/jpgraph_bar.php';

    require_once 'jpgraph/src/jpgraph_pie.php';
    require_once 'jpgraph/src/jpgraph_pie3d.php';

///////////////////////////////
    /// for COUNTRY
    ///////////////////////////////
    $data1y = $data_country_val_arr;

// Create the graph. These two calls are always required
    $graph = new Graph(800, 500);

    $graph->SetScale("textlin");

    $graph->SetShadow();
    $graph->img->SetMargin(40, 30, 20, 40);
    $graph->xaxis->SetTickLabels($data_country_arr);
    $graph->xaxis->SetFont(FF_ARIAL, FS_BOLD);
    $graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL);

// Create the bar plots
    $b1plot = new BarPlot($data1y);
    $b1plot->SetFillColor("orange");
    $b1plot->SetLegend('Country');

// Create the grouped bar plot
    $gbplot = new GroupBarPlot(array($b1plot));

// ...and add it to the graPH
    $graph->Add($gbplot);

//$graph->title->Set("Example 21");
    //$graph->xaxis->title->Set("X-title");
    //$graph->yaxis->title->Set("Y-title");

// Display the graph
    $file_name = $profile['id'] . "country.png";
    $graph->Stroke($file_name);
//save to file
    $filename_country = "images/tmp_files/$file_name";
    $img = $graph->img->Stream($filename_country);

///////////////////////////////
    /// for DEVICE
    ///////////////////////////////

    $data1y = $data_device_val_arr;

// Create the graph. These two calls are always required
    $graph = new Graph(800, 500);
    $graph->SetScale("textlin");

    $graph->SetShadow();
    $graph->img->SetMargin(40, 30, 20, 40);
    $graph->xaxis->SetTickLabels($data_device_arr);
    $graph->xaxis->SetFont(FF_ARIAL, FS_BOLD);
    $graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL);

// Create the bar plots
    $b1plot = new BarPlot($data1y);
    $b1plot->SetLegend('Device');

// Create the grouped bar plot
    $gbplot = new GroupBarPlot(array($b1plot));

// ...and add it to the graPH
    $graph->Add($gbplot);

//$graph->title->Set("Example 21");
    //$graph->xaxis->title->Set("X-title");
    //$graph->yaxis->title->Set("Y-title");

// Display the graph
    $file_name = $profile['id'] . "device.png";
    $graph->Stroke($file_name);
//save to file
    $filename_device = "images/tmp_files/$file_name";
    $img = $graph->img->Stream($filename_device);

///////////////////////////////
    /// for BROWSER
    ///////////////////////////////

    $data1y = $data_browser_val_arr;

// Create the graph. These two calls are always required
    $graph = new Graph(800, 500);
    $graph->SetScale("textlin");

    $graph->SetShadow();
    $graph->img->SetMargin(40, 30, 20, 40);
    $graph->xaxis->SetTickLabels($data_browser_arr);
    $graph->xaxis->SetFont(FF_ARIAL, FS_BOLD);
    $graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL);

// Create the bar plots
    $b1plot = new BarPlot($data1y);
    $b1plot->SetLegend('Browser');

// Create the grouped bar plot
    $gbplot = new GroupBarPlot(array($b1plot));

// ...and add it to the graPH
    $graph->Add($gbplot);

//$graph->title->Set("Example 21");
    //$graph->xaxis->title->Set("X-title");
    //$graph->yaxis->title->Set("Y-title");

// Display the graph
    $file_name = $profile['id'] . "browser.png";
    $graph->Stroke($file_name);
//save to file
    $filename_browser = "images/tmp_files/$file_name";
    $img = $graph->img->Stream($filename_browser);

    ?>

<script language="javascript">

	<?php if (!isset($_REQUEST["html"])) {
        ?>
        $(window).load(function() {

            setTimeout(function(){


                $(".pirchart-div").each(function() {

                    //if($(this).siblings('img').attr('ismap')=="ismap")
                      //  $(this).siblings('img').remove();

                      if($(this).closest('table').siblings('img').attr('ismap')=="ismap")
                        $(this).closest('table').siblings('img').remove();
                });

                $('#html').val('<html><body>'+$(".contentpart").html()+'</body></html>');
                $('#frm_pdf').submit();


            },4000);

        });


        <?php
}?>
        $(document).ready(function(){

			$('#header,.top-navigation,#footer,.menu,.player-window-image').remove();
			$('body').css('background','none');
			$('.wrapper').css('width','auto');

        });
    </script>

    <div class="contentpart" >


        <div class="my-qr-codes">
            <!-- remain design part start-->
            <div style="float: left;font-family:arial;" id="divToPrint"><h3 style="margin: 0px;"><?php echo 'Profile ' . $profile['id'] . '. ' . ucwords($profile['code_profile_name']); ?></h3></div>


            <!-- remain design part end-->
            <?php
$obj = json_decode($jsonCountry);

    $data_arr = $obj->{'DATA'};
    $country_cnt = 0;

    for ($i = 0; $i < count($data_arr); $i++) {
        $country_cnt = $country_cnt + $data_arr[$i][2];
    }
    ?>
				<div style="float:right;width: 100px;">
				 	<div style="border:3px solid #a2a2a2;font-size:27px;padding:8px 6px;text-align: center;font-family:arial;"><?php echo $country_cnt; ?></div>
				 	<div style="font-size:20px;min-width: 73px;text-align: center;font-family:arial;">Total</div>
				</div>
            <div style="clear:both;"></div>
            <div class="All" id="divToPrint2">
                <!-- /title -->
                <div style="clear:both;"></div>

                <div class="c-form"  >
                    <div align="left" style="position:relative; margin-top:20px;">
                        <div class="ui-widget" style="height: 500px !important;page-break-inside: avoid;">
                            <img src="<?php echo URL::base() . $filename_country; ?>">
                        </div>
                        <div class="ui-widget" style="height: 500px !important;page-break-inside: avoid;">
                            <img src="<?php echo URL::base() . $filename_device; ?>">
                        </div>
                        <div class="ui-widget" style="height: 500px !important;page-break-inside: avoid;">
                            <img src="<?php echo URL::base() . $filename_browser; ?>">
                        </div>
                    </div>
                </div>


				<?php
if ($profile['enable_form_analytics'] == 1 && $profile["form_id"] != 0 && count($form_analytics_questions) > 0) //form analytics enabled for this profile
    {
        ?>
					<div >
						<h2 style="padding-left: 10px;font-size: 35px;font-family:arial;"><img align="top" src="<?php echo URL::base(); ?>images/pie_icon.png" width="50" />&nbsp;&nbsp;Form Analytics</h2>

						<table style="padding-left: 10px;" width="100%" cellspacing="0" cellpadding="0" border="0">
							<tr>
								<td width="41%">
									<table width="100%" cellspacing="0" cellpadding="0" border="0">
										<tr>
                                                                                        <td style="color:#1371ff;font-size: 17px;font-family:arial;" width="3%" align="right"><?php echo $country_cnt; ?></td>
											<td width="1%">&nbsp;</td>
											<td style="color:#414a41;font-size: 17px;font-weight: bold;font-family:arial;" align="left"> Scans</td>
										</tr>
										<tr>
											<td style="color:#1371ff;font-size: 17px;font-family:arial;" width="3%" align="right"><?php echo $form_submission_count; ?></td>
											<td width="1%">&nbsp;</td>
											<td style="color:#414a41;font-size: 17px;font-weight: bold;font-family:arial;" align="left"> Form Submissions</td>
										</tr>
									</table>
								</td>
								<td width="58%" valign="middle" style="color:#0066ff;font-size: 33px;font-weight: bold;font-family:arial;">
									<?php
if ($country_cnt == 0) {
            if ($form_submission_count == 0) {
                echo "0%";
            } else {
                echo "100%";
            }

        } else {
            echo number_format((($form_submission_count * 100) / $country_cnt), 2) . "%";
        }

        ?>
								</td>
							</tr>
						</table>
						<div style="height: 50px;clear: both;" >&nbsp;</div>

						<?php $j = 0;
        $color_arr = array('#3366CC', '#DC3912', '#FF9900', '#109618', '#990099', '#0099C6', '#DD4477', '#66AA00', '#B82E2E', '#316395', '#3366CC', '#DC3912', '#FF9900', '#109618', '#990099', '#0099C6', '#DD4477', '#66AA00', '#B82E2E', '#316395');

        foreach ($form_analytics_questions as $form_analytics_question) {
            $mydata = "";
            $map_color_arr = "";

            $answer_question_id = $form_analytics_question["question_id"];

            $question_options = explode(":::", $form_analytics_question["question_text"]);

            $current_question_order = 0;
            $current_ele_question_order_arr = Model::factory('formbuilder')->get_order_of_question($answer_question_id);
            if ($current_ele_question_order_arr) {
                $current_question_order = $current_ele_question_order_arr["question_order"];
            }

            $main_question = Model_formbuilder::get_question_by_answer_question_id($answer_question_id, $profile["id"], $current_question_order);

            $question_text = $main_question[0]["question_text"];

            if ($form_analytics_question["question_type_id"] == 6) //number scale
            {
                $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                $question_options = Model_formbuilder::get_question_option($answer_question_id, $question_option_type_id);

                $scale_from = $question_options[0]["option_name"];

                $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                $question_options = Model_formbuilder::get_question_option($answer_question_id, $question_option_type_id);

                $scale_to = $question_options[0]["option_name"];

                $color_cnt = 0;
                $question_option_text = "";

                for ($i = $scale_from; $i <= $scale_to; $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile["id"], $answer_question_id, $i);

                    if (count($form_analytics_answers) > 0) {
                        if ($mydata == "") {
                            $mydata = count($form_analytics_answers);
                        } else {
                            $mydata .= "," . count($form_analytics_answers);
                        }

                    }

                    if (!isset($color_arr[$color_cnt])) {
                        $color_cnt = $color_cnt % 10;
                    }

                    //if(count($form_analytics_answers)>0)
                    //{
                    if ($map_color_arr == "") {
                        $map_color_arr = $color_arr[$color_cnt];
                    } else {
                        $map_color_arr .= "," . $color_arr[$color_cnt];
                    }

                    //}

                    $question_option_text .= "<div><span style='font-weight:bold;color:" . $color_arr[$color_cnt] . "'>" . count($form_analytics_answers) . "</span><span class='legend-text'>&nbsp;&nbsp;&nbsp;" . $i . "</span></div>";
                    $color_cnt++;
                }

            } else {
                $color_cnt = 0;
                $question_option_text = "";

                for ($i = 0; $i < count($question_options); $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile["id"], $answer_question_id, addslashes($question_options[$i]));

                    if (count($form_analytics_answers) > 0) {
                        if ($mydata == "") {
                            $mydata = count($form_analytics_answers);
                        } else {
                            $mydata .= "," . count($form_analytics_answers);
                        }

                    }

                    if (!isset($color_arr[$color_cnt])) {
                        $color_cnt = $color_cnt % 10;
                    }

                    if (count($form_analytics_answers) > 0) {
                        if ($map_color_arr == "") {
                            $map_color_arr = $color_arr[$color_cnt];
                        } else {
                            $map_color_arr .= "," . $color_arr[$color_cnt];
                        }

                    }

                    $question_option_text .= "<div><span style='font-weight:bold;color:" . $color_arr[$color_cnt] . "'>" . count($form_analytics_answers) . "</span><span class='legend-text'>&nbsp;&nbsp;&nbsp;" . $question_options[$i] . "</span></div>";

                    $color_cnt++;
                }

            }

            //for pie chart
            if ($mydata != "") {
                // Some data

                if ($map_color_arr != "") {
                    $color_arr_new = explode(",", $map_color_arr);
                }

                $data = explode(",", $mydata);

                // Create the Pie Graph.
                $graph = new PieGraph(300, 200, 'auto');
                $graph->SetShadow();

                // Create
                $p1 = new PiePlot3D($data);

                //$p1->SetLegends(array("Jan (%d)","Feb","Mar","Apr","May","Jun","Jul"));
                //$targ=array("pie3d_csimex1.php?v=1","pie3d_csimex1.php?v=2","pie3d_csimex1.php?v=3","pie3d_csimex1.php?v=4","pie3d_csimex1.php?v=5","pie3d_csimex1.php?v=6");
                //$alts=array("val=%d","val=%d","val=%d","val=%d","val=%d","val=%d");
                ///$p1->SetCSIMTargets($targ,$alts);

                // Use absolute labels
                $p1->SetLabelType(1);
                //$p1->value->SetFormat("%d kr");
                $p1->SetLabelType("PIE_VALUE_ABS");

                // Move the pie slightly to the left
                $p1->SetCenter(0.4, 0.5);

                $graph->Add($p1);

                $p1->SetSliceColors($color_arr_new);
                $p1->value->SetColor("#ffffff");
                $p1->SetLabelPos(0.6);

                // Send back the HTML page which will call this script again
                // to retrieve the image.

                $file_name1 = $profile['id'] . "$j.png";
                $graph->StrokeCSIM($file_name1);
                //save to file
                $filename1 = "images/tmp_files/$file_name1";
                $img = $graph->img->Stream($filename1);
            } else {
                $filename1 = "";
            }
            ?>

                                                <table style="width:100%">
							<tr class="pirchart-div" style="page-break-inside:avoid !important;" >
                                                                <td style="color:#414a41;font-size:17px;padding-left: 10px;width: 40%;font-family:arial;word-wrap: break-word; " ><?php echo $question_text; ?>
									<br>
									<?php echo $question_option_text; ?>
								</td>
								<td id="piechart_3d<?php echo $j; ?>" >
                                                                    <?php if ($filename1 != "") {?>
                                                                        <img src="<?php echo URL::base() . $filename1; ?>">
                                                                    <?php
}?>
                                                                </td>
							</tr>
                                                        <tr>
                                                            <td style="clear: both;height: 50px;">&nbsp;</td>
                                                        </tr>
                                                </table>

						<?php $j++;
        } //end of foreach($form_analytics_questions as $form_analytics_question)
        ?>



					</div>
				<?php
}
    ?>
            </div>
        </div>
    </div>
    <form name="frm_pdf" id="frm_pdf" action="<?php echo URL::base(); ?>dashboard/scanalytics_print_pdf/<?php echo $profile["id"]; ?>" method="post">
        <input type="hidden" name="html" id="html" >
    </form>
<?php
}