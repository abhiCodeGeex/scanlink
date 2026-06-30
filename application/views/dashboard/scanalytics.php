<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


<?php
if (!$expried_or_not) {
    ?>
    <?php

    ?>
    <script language="javascript">
        function myFunction()
        {

            var win = window.open('', '_blank','location=no, menubar=yes, status=yes,toolbar=no, scrollbars=yes, resizable=yes');
            var handler = function() {
				win.print();
               // win.close();
            };
            if(win.addEventListener)
                win.addEventListener('load', handler, false);
            else if(win.attachEvent)
                win.attachEvent('onload', handler, false);

            var content = '<html><head>'+
                '<link type="text/css" href="https://scanlink.com.au/styles/style.css" rel="stylesheet" media="print" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/style.css" rel="stylesheet" media="screen" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/ui/jquery.ui.core.css" rel="stylesheet" media="print" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/ui/jquery.ui.core.css" rel="stylesheet" media="screen" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/ui/jquery.ui.theme.css" rel="stylesheet" media="print" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/ui/jquery.ui.theme.css" rel="stylesheet" media="screen" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/ui/jquery.ui.tabs.css" rel="stylesheet" media="print" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/ui/jquery.ui.tabs.css" rel="stylesheet" media="screen" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/coin-slider-styles.css" rel="stylesheet" media="print" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/coin-slider-styles.css" rel="stylesheet" media="screen" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/chart/style/ddchart.css" rel="stylesheet" media="print" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/chart/style/ddchart.css" rel="stylesheet" media="screen" />'+
                '<link type="text/css" href="https://scanlink.com.au/styles/chart/style/theme.css" rel="stylesheet" media="print" />'+
				'<link type="text/css" href="http://scanlink.com.au/styles/chart/style/theme.css" rel="stylesheet" media="screen" /></head><body onload="window.print()">';
            content += '<style type="text/css">#switcher, .gray-text, .print-btn{display:none;}</style>';
            content += document.getElementById("divToPrint").innerHTML ;
            content += document.getElementById("divToPrint2").innerHTML ;
            content += "</body>";
            content += "</html>";


            win.document.write(content);
            win.document.close();
        }

		function myFunction2()
        {
            var win = window.open('<?php echo URL::base(); ?>dashboard/scanalytics_print/<?php echo $profile['id']; ?>', '_blank','width=300, location=no, menubar=yes, status=yes,toolbar=no, scrollbars=yes, resizable=no');

        }

        $(document).ready(function(){
            $("#switcher").themeswitcher({
                imgpath: '<?php echo URL::base(); ?>styles/chart/images/',
                loadTheme: "start"
            });


				$("#chart_div_country").ddBarChart({
						 chartDataLink: "<?php echo url::base(); ?>/graphengine/?pid=<?php echo $profile['id']; ?>",
						 action: 'init',
						 xOddClass: "ui-state-active",
						 xEvenClass: "ui-state-default",
						 yOddClass: "ui-state-active",
						 yEvenClass: "ui-state-default",
						 xWrapperClass: "ui-widget-content",
						 chartWrapperClass: "ui-widget-content",
						 chartBarClass: "ui-state-focus ui-corner-top",
						 chartBarHoverClass: "ui-state-highlight",
						 callBeforeLoad: function (){$('#loading-Notification_static').fadeIn(500);},
						 callAfterLoad: function (){$('#loading-Notification_static').stop().fadeOut(0);},
						 tooltipSettings: {extraClass: "ui-widget ui-widget-content ui-corner-all"}
				});
            $("#chart_div_device").ddBarChart({
                chartDataLink: "<?php echo url::base(); ?>/graphengine/devics.php?pid=<?php echo $profile['id']; ?>",
                action: 'init',
                xOddClass: "ui-state-active",
                xEvenClass: "ui-state-default",
                yOddClass: "ui-state-active",
                yEvenClass: "ui-state-default",
                xWrapperClass: "ui-widget-content",
                chartWrapperClass: "ui-widget-content",
                chartBarClass: "ui-state-focus ui-corner-top",
                chartBarHoverClass: "ui-state-highlight",
                callBeforeLoad: function (){$('#loading-Notification_static').fadeIn(500);},
                callAfterLoad: function (){$('#loading-Notification_static').stop().fadeOut(0);},
                tooltipSettings: {extraClass: "ui-widget ui-widget-content ui-corner-all"}
            });
            $("#chart_div_browser").ddBarChart({
                chartDataLink: "<?php echo url::base(); ?>/graphengine/bowser.php?pid=<?php echo $profile['id']; ?>",
                action: 'init',
                xOddClass: "ui-state-active",
                xEvenClass: "ui-state-default",
                yOddClass: "ui-state-active",
                yEvenClass: "ui-state-default",
                xWrapperClass: "ui-widget-content",
                chartWrapperClass: "ui-widget-content",
                chartBarClass: "ui-state-focus ui-corner-top",
                chartBarHoverClass: "ui-state-highlight",
                callBeforeLoad: function (){$('#loading-Notification_static').fadeIn(500);},
                callAfterLoad: function (){$('#loading-Notification_static').stop().fadeOut(0);},
                tooltipSettings: {extraClass: "ui-widget ui-widget-content ui-corner-all"}
            });

        });
    </script>
    <style type="text/css">
        .link1 {
            font-size: 15px;
            font-weight: bold;
            color: #6E9113;
        }
    </style>
<?php
}?>
<?php echo $innermenu; ?>
<?php
if (!$expried_or_not) {
        ?>
    <div class="contentpart" >
        <div class="my-qr-codes">
            <!-- remain design part start-->
            <h2>Div</h2>
            <div style="float: left" id="divToPrint"><h3 style="margin: 0px;"><?php echo 'Profile '.$profile['id'].'. '.ucwords($profile['code_profile_name']); ?></h3></div>

            <div class="btn-inline" style="float: right;">
                <ul class="scananalytic-buttons">
                    <li><input type="button" onclick="javascript: window.location.href='<?php echo URL::base(); ?>dashboard/scanalytics_map/<?php echo $profile['id']; ?>'" value="Location Map" /></li>
                    <li><input type="button" onclick="javascript: window.location.href='<?php echo URL::base(); ?>dashboard/scanalytics_locations/<?php echo $profile['id']; ?>'" value="Location List" /></li>
                    <li><a class="link-button" href="<?php echo URL::base().'dashboard/scanalytics_export/'.$profile['id']; ?>">Export Analytics</a></li>

                    <?php
$back_link = URL::base();
        switch ($profile['type_id']) {
        case 1:
            $back_link .= 'dashboard/index';
            break;
        case 2:
            $back_link .= 'dashboard/location';
            break;
        case 3:
            $back_link .= 'dashboard/asset';
            break;
        case 4:
            $back_link .= 'dashboard/product';
            break;
        case 5:
            $back_link .= 'dashboard/procedure';
            break;
        case 8:
            $back_link .= 'dashboard/misc';
            break;
        case 9:
            $back_link .= 'dashboard/code';
            break;
        case 10:
            $back_link .= 'dashboard/survey';
            break;
        case 11:
            $back_link .= 'dashboard/exhibit';
            break;
    } ?>
                    <li><a class="link-button" href="<?php echo $back_link; ?>">Return to list</a></li>
                </ul>
            </div>
            <!-- remain design part end-->

            <?php
$obj = json_decode($jsonCountry);

        $data_arr = isset($obj) ? $obj->{'DATA'} : 0;
        $country_cnt = 0;

        for ($i = 0; $i < count($data_arr); ++$i) {
            $country_cnt = $country_cnt + $data_arr[$i][2];
        } ?>

            <div class="clear"></div>
            <div class="All" id="divToPrint2">
                <!-- /title -->
                <div class="clear"></div>
                <div id="switcher" style="position:relative; height:5%;float:left;"></div>
                <button onclick="myFunction2()" class="print-btn" style="float:left;margin-left:10px;"><img src="<?php echo url::base().'images/print.png'; ?>"  /></button>&nbsp;
                <a href='<?php echo URL::base(); ?>dashboard/scanalytics_print_pdf/<?php echo $profile['id']; ?>' target="_blank"><button class="print-btn">Download PDF</button></a>
                <div class="clear"></div>
                <br/>

                <div align="center" class="gray-text"><strong>Click on the bar graph for more detailed analytics</strong></div>

					 <div class="scanalytics_tot_div">
                  <?php $results = DB::select('*')->from('ana_item_analytics')->where('id_item', '=', $profile['id'])
        ->execute();
        //print_r($results);?>
                        <div class="row" style="display:flex;">
                                <div class="scanalytics-box" style="margin-right: 15px;">
                                    <b>Total Form Submission </b>
                                    <div class="scanalytics_tot_count"><?php echo $form_submission_count; /*$country_cnt=count($results); echo $country_cnt;*/ ?></div>
                                </div>
                                <div class="scanalytics-box">
                                    <b>Analytics Count</b>
                                    <div class="scanalytics_tot_count"><?php echo $country_cnt = count($results); ?></div>
                                </div>
                        </div>
                         
					 	<!-- <div class="scanalytics_tot_txt" style="margin-top: 15px;">Total</div> -->
					 </div>

                <div class="clear"></div>
                <div class="c-form">
                    <div align="center" style="position:relative; margin-top:20px;">
                        <div class="ui-widget" style="height: 500px !important;">
                            <div style="position:relative; width:100%;height:95%;">

                                <div id="chart_div_country" style="position:relative; height:95%; width:100%;"></div>

                            </div>
                        </div>
                        <div class="ui-widget" style="height: 500px !important;">
                            <div style="position:relative; width:100%;height:95%;">

                                <div id="chart_div_device" style="position:relative; height:95%; width:100%;"></div>

                            </div>
                        </div>
                        <div class="ui-widget" style="height: 500px !important;">
                            <div style="position:relative; width:100%;height:95%;">

                                <div id="chart_div_browser" style="position:relative; height:95%; width:100%;"></div>

                            </div>
                        </div>
                    </div>
                </div>


				<?php
if ($profile['enable_form_analytics'] == 1 && $profile['form_id'] != 0 && count($form_analytics_questions) > 0) { //form analytics enabled for this profile
        //example
        //    $jsonCountry = '{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],"DATA":[["Country","India","21","India ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],"DATA":[["State","Gujarat","19","Gujarat ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["City","Ahmedabad","2","Ahmedabad ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["Device","iphone","10","iphone ^ "],["Device","computer","7","computer ^ "],["Device","ipad","11","ipad ^ "],["Device","ipod","1","ipod ^ "]]}],["City","Rajkot","17","Rajkot ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["Device","iphone","10","iphone ^ "],["Device","computer","7","computer ^ "],["Device","ipad","11","ipad ^ "],["Device","ipod","1","ipod ^ "]]}]]}],["State","Maharashtra","2","Maharashtra ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["City","Mumbai","2","Mumbai ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["Device","iphone","10","iphone ^ "],["Device","computer","7","computer ^ "],["Device","ipad","11","ipad ^ "],["Device","ipod","1","ipod ^ "]]}]]}]]}],["Country","United States","8","United States ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],"DATA":[["State","California","8","California ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["City","San Francisco","8","San Francisco ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["Device","iphone","10","iphone ^ "],["Device","computer","7","computer ^ "],["Device","ipad","11","ipad ^ "],["Device","ipod","1","ipod ^ "]]}]]}]]}]]}';
        ?>
					<div >
						<h2 style="padding-left: 50px;font-size: 35px;"><img align="top" src="<?php echo URL::base(); ?>images/pie_icon.png" width="50" />&nbsp;&nbsp;Form Analytics</h2>
						<a href="<?php echo URL::base().'formbuilder/export_form_analytics/'.$profile['id']; ?>"><div class="export-analytics-btn">EXPORT ANALYTICS</div></a>

						<table style="padding-left: 50px;" width="100%" cellspacing="0" cellpadding="0" border="0">
							<tr>
								<td width="52%">
									<table width="100%" cellspacing="0" cellpadding="0" border="0">
										<tr>
											<td class="scanalytics_count" width="3%" align="right"><?php echo $country_cnt; ?></td>
											<td width="1%">&nbsp;</td>
											<td class="scanalytics_text" align="left"> Scans</td>
										</tr>
										<tr>
											<td class="scanalytics_count" width="3%" align="right"><?php echo $form_submission_count; ?></td>
											<td width="1%">&nbsp;</td>
											<td class="scanalytics_text" align="left"> Form Submissions</td>
										</tr>
									</table>
								</td>
								<td width="48%" valign="middle" class="scanalytics_percent">
									<?php
if ($country_cnt == 0) {
            if ($form_submission_count == 0) {
                echo '0%';
            } else {
                echo '100%';
            }
        } else {
            echo number_format((($form_submission_count * 100) / $country_cnt), 2).'%';
        } ?>
								</td>
							</tr>
						</table>
						<div style="height: 50px;" >&nbsp;</div>

						<?php $j = 0;
            $color_arr = array('#3366CC', '#DC3912', '#FF9900', '#109618', '#990099', '#0099C6', '#DD4477', '#66AA00', '#B82E2E', '#316395');

            foreach ($form_analytics_questions as $form_analytics_question) {
                $mydata = '';
                $map_color_arr = '';

                $answer_question_id = $form_analytics_question['question_id'];

                $question_options = explode(':::', $form_analytics_question['question_text']);

                $current_question_order = 0;
                $current_ele_question_order_arr = Model::factory('formbuilder')->get_order_of_question($answer_question_id);
                if ($current_ele_question_order_arr) {
                    $current_question_order = $current_ele_question_order_arr['question_order'];
                }

                $main_question = Model_formbuilder::get_question_by_answer_question_id($answer_question_id, $profile['id'], $current_question_order);

                $question_text = $main_question[0]['question_text'];

                if ($form_analytics_question['question_type_id'] == 6) { //number scale
                $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                $question_options = Model_formbuilder::get_question_option($answer_question_id, $question_option_type_id);

                    $scale_from = $question_options[0]['option_name'];

                    $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                    $question_options = Model_formbuilder::get_question_option($answer_question_id, $question_option_type_id);

                    $scale_to = $question_options[0]['option_name'];

                    $color_cnt = 0;
                    $question_option_text = '';

                    for ($i = $scale_from; $i <= $scale_to; ++$i) {
                        $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile['id'], $answer_question_id, $i);

                        if ($mydata == '') {
                            $mydata = "['".$i."',".count($form_analytics_answers).']';
                        } else {
                            $mydata .= ",['".$i."',".count($form_analytics_answers).']';
                        }

                        if (!isset($color_arr[$color_cnt])) {
                            $color_cnt = $color_cnt % 10;
                        }

                        if ($map_color_arr == '') {
                            $map_color_arr = "['".$color_arr[$color_cnt]."'";
                        } else {
                            $map_color_arr .= ",'".$color_arr[$color_cnt]."'";
                        }

                        $question_option_text .= "<div ><span style='font-weight:bold;color:".$color_arr[$color_cnt]."'>".count($form_analytics_answers)."</span><span class='legend-text'>&nbsp;&nbsp;&nbsp;".$i.'</span></div>';
                        ++$color_cnt;
                    }
                    $map_color_arr .= ']';
                } else {
                    $color_cnt = 0;
                    $question_option_text = '';

                    for ($i = 0; $i < count($question_options); ++$i) {
                        $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile['id'], $answer_question_id, addslashes($question_options[$i]));

                        if ($mydata == '') {
                            $mydata = "['".addslashes($question_options[$i])."',".count($form_analytics_answers).']';
                        } else {
                            $mydata .= ",['".addslashes($question_options[$i])."',".count($form_analytics_answers).']';
                        }

                        if (!isset($color_arr[$color_cnt])) {
                            $color_cnt = $color_cnt % 10;
                        }

                        if ($map_color_arr == '') {
                            $map_color_arr = "['".$color_arr[$color_cnt]."'";
                        } else {
                            $map_color_arr .= ",'".$color_arr[$color_cnt]."'";
                        }

                        $question_option_text .= "<div ><span style='font-weight:bold;color:".$color_arr[$color_cnt]."'>".count($form_analytics_answers)."</span><span class='legend-text'>&nbsp;&nbsp;&nbsp;".$question_options[$i].'</span></div>';

                        ++$color_cnt;
                    }
                    $map_color_arr .= ']';
                } ?>

						    <script type="text/javascript">
						      google.load("visualization", "1", {packages:["corechart"]});
						      google.setOnLoadCallback(drawChart);
						      function drawChart() {
						        var data = google.visualization.arrayToDataTable([
						          ['Task', '<?php echo $question_text; ?>'],
						          <?php echo $mydata; ?>

						        ]);

						        var options = {
						          title: '<?php echo $question_text; ?>',
						          is3D: true,
								 // legend:{position: 'left'},
								  legend:'none',
								  chartArea:{width:'200',height:'200'},
								  width:'220',
								  height:'200',
								  sliceVisibilityThreshold:0,
								  colors:<?php echo $map_color_arr; ?>
						        };

						        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d<?php echo $j; ?>'));

						        chart.draw(data, options);
						      }
						    </script>

							<div class="analytics-question-text" ><?php echo $question_text; ?>
								<br>
								<?php echo $question_option_text; ?>
							</div>
							<div id="piechart_3d<?php echo $j; ?>" style="width: 220px;height: 200px;float: left;"></div>
							<div class="clear">&nbsp;</div>
							<div class="clear">&nbsp;</div>
							<div class="clear">&nbsp;</div>
						<?php ++$j;
            } //end of foreach($form_analytics_questions as $form_analytics_question)
        ?>



					</div>
				<?php
        } ?>
            </div>
        </div>
    </div>
<?php
    } else {
        ?>
    <p>You can not perform this action on expired profile.</p>
<?php
    }?>
