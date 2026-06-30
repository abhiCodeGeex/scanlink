<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<?php
if (!$expried_or_not) {
    ?>
    <script language="javascript">
        function myFunction2()
        {
            var win = window.open('<?php echo URL::base() ?>dashboard/cumulative_analytics_print/?cumulative_code_profile=<?php echo $cumulative_profile_id;?>', '_blank','width=300, location=no, menubar=yes, status=yes,toolbar=no, scrollbars=yes, resizable=no');
        }

        $(document).ready(function(){
            $('#export_cumulative_analytics').click(function(){
                $(".expried").colorbox({inline:true, width:"35%"});
                $("#export_cumulative").click();
            });
            $("#export_cumulative_frmbtn").click(function(){
                $("#export_cumulative_form").submit();
                $("#name").val("");
                $("#description").val("");
                parent.jQuery.fn.colorbox.close();
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
<?php } ?>
<?php echo $innermenu; ?>
<?php
if (!$expried_or_not) {
    ?>
    <div class="contentpart" >
        <div class="my-qr-codes">
            <!-- remain design part start-->
            <div style="float: left" id="divToPrint">
                <h3 style="margin: 0px;">
                    <?php
                    foreach($all_profile as $all) {
                        echo 'Profile ' . $all['id'] . '. ' . ucwords($all['code_profile_name']).'<br>';
                    }
                    ?>
                </h3>
            </div>

            <div class="btn-inline" style="float: right;">
                <ul class="scananalytic-buttons">
                    <!--<li><input type="button" onclick="javascript: window.location.href='<?php echo URL::base(); ?>dashboard/scanalytics_map/<?php print $profile['id']; ?>'" value="Location Map" /></li>
                    <li><input type="button" onclick="javascript: window.location.href='<?php echo URL::base(); ?>dashboard/scanalytics_locations/<?php print $profile['id']; ?>'" value="Location List" /></li>
                    <li><a class="link-button" href="<?php echo URL::base() . 'dashboard/scanalytics_export/' . $profile["id"]; ?>">Export Analytics</a></li>-->

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

                    }
                    ?>
                    <li><a class="link-button" href="<?php echo $back_link; ?>">Return to list</a></li>
                </ul>
            </div>
            <!-- remain design part end-->

            <div class="clear"></div>
            <div class="All" id="divToPrint2">
                <!-- /title -->
                <div class="clear"></div>


                <div class="" style="float:right;width: 18%;margin-top: 55px;">
                <button onclick="myFunction2()" class="print-btn" style="float:left;margin-left:10px;"><img src="<?php echo url::base() . 'images/print.png'; ?>"  /></button>&nbsp;
                <a href='<?php echo URL::base()?>dashboard/cumulative_analytics_print_pdf/?cumulative_code_profile=<?php echo $cumulative_profile_id;?>' target="_blank"><button class="print-btn">Download PDF</button></a>
                </div>
                <div class="clear"></div>
                <div>
                    <h2 style="padding-left: 50px;font-size: 35px;"><img align="top" src="<?php echo URL::base();?>images/pie_icon.png" width="50" />&nbsp;&nbsp;Form Analytics</h2>
                    <!--<a href="<?php echo URL::base() . 'formbuilder/export_form_cumulative_analytics/?cumulative_code_profile=' . $cumulative_profile_id; ?>">-->
                    <div id="export_cumulative_analytics" class="export-analytics-btn">EXPORT ANALYTICS</div>
                    <!--</a>-->
                    <table style="padding-left: 50px;" width="100%" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="52%">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <!--<tr>
                                        <td class="scanalytics_count" width="3%" align="right"><?php //echo $country_cnt;?></td>
                                        <td width="1%">&nbsp;</td>
                                        <td class="scanalytics_text" align="left"> Scans</td>
                                    </tr>-->
                                    <?php
                                        $form_submission_count = 0;
                                        foreach($form_submission_count_arr as $form_submission) {
                                            $form_submission_count += intval($form_submission);
                                        }
                                    ?>
                                    <tr>
                                        <td class="scanalytics_count" width="3%" align="right"><?php echo $form_submission_count;?></td>
                                        <td width="1%">&nbsp;</td>
                                        <td class="scanalytics_text" align="left"> Form Submissions</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="48%" valign="middle" class="scanalytics_percent">
                                <?php
                                /*if($country_cnt==0)
                                    {
                                        if($form_submission_count==0)
                                            echo "0%";
                                        else
                                            echo "100%";
                                    }
                                    else
                                    echo number_format((($form_submission_count*100)/$country_cnt),2)."%";*/
                                ?>
                            </td>
                        </tr>
                    </table>
                    <?php

                    //print_r($form_analytics_questions);

                    ?>
                    <div style="height: 50px;" >&nbsp;</div>
                        <?php $j=0;
                        $a = 0;
                        $color_arr = array('#3366CC','#DC3912','#FF9900','#109618','#990099','#0099C6','#DD4477','#66AA00','#B82E2E','#316395');
                        $dropdown_count_arr = array();
                        $radio_count_arr = array();
                        $scale_count_arr = array();
                        foreach($form_analytics_questions as $multi_analytics_questions){
                            foreach($multi_analytics_questions as $multi_analytics_question)
                            {
                                $profile_id = $multi_analytics_question['profile_id'];
                                $answer_question_id = $multi_analytics_question["question_id"];
                                $question_options = explode(":::",$multi_analytics_question["question_text"]);

                                if($multi_analytics_question["question_type_id"]==6) //number scale
                                {
                                    $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                                    $question_options = Model_formbuilder::get_question_option($answer_question_id,$question_option_type_id);
                                    $scale_from = $question_options[0]["option_name"];
                                    $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                                    $question_options = Model_formbuilder::get_question_option($answer_question_id,$question_option_type_id);
                                    $scale_to = $question_options[0]["option_name"];

                                    for($i=$scale_from;$i<=$scale_to;$i++)
                                    {
                                        $form_analytics_answers=  Model::factory('formbuilder')->get_analytics_answers($profile_id,$answer_question_id,$i);
                                        $scale_count_arr[$profile_id][] = count($form_analytics_answers);
                                    }
                                }
                                else if($multi_analytics_question["question_type_id"]==5) //dropdown
                                {
                                    for($i=0;$i<count($question_options);$i++)
                                    {
                                        $form_analytics_answers=  Model::factory('formbuilder')->get_analytics_answers($profile_id,$answer_question_id,addslashes($question_options[$i]) );
                                        $drop_count_arr[$profile_id][] = count($form_analytics_answers);
                                    }
                                }
                                else {
                                    for($i=0;$i<count($question_options);$i++)
                                    {
                                        $form_analytics_answers=  Model::factory('formbuilder')->get_analytics_answers($profile_id,$answer_question_id,addslashes($question_options[$i]) );
                                        $all_count_arr[$profile_id][] = count($form_analytics_answers);
                                    }
                                }
                            }
                        }
                       /*echo "<pre>";
                        print_r($drop_count_arr);
                        print_r($all_count_arr);
                        print_r($scale_count_arr);
                        echo "</pre>";*/
                        $b=0;
                        foreach($form_analytics_questions[0] as $form_analytics_question)
                        {
                            $mydata= "";
                            $map_color_arr= "";
                            $answer_question_id = $form_analytics_question["question_id"];
                            $question_options = explode(":::",$form_analytics_question["question_text"]);
                            $current_question_order = 0;
                            $current_ele_question_order_arr = Model::factory('formbuilder')->get_order_of_question($answer_question_id);

                            if($current_ele_question_order_arr)
                            {
                                $current_question_order = $current_ele_question_order_arr["question_order"];
                            }

                            $main_question = Model_formbuilder::get_question_by_answer_question_id($answer_question_id,$profile["id"],$current_question_order);
                            $question_text = $main_question[0]["question_text"];
                            error_reporting(0);
                            if($form_analytics_question["question_type_id"]==6) //number scale
                            {
                                $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                                $question_options = Model_formbuilder::get_question_option($answer_question_id,$question_option_type_id);
                                $scale_from = $question_options[0]["option_name"];
                                $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                                $question_options = Model_formbuilder::get_question_option($answer_question_id,$question_option_type_id);
                                $scale_to = $question_options[0]["option_name"];
                                $color_cnt=0;
                                $question_option_text = "";

                                $sumArray = array();
                                foreach ($scale_count_arr as $subArray) {
                                    foreach ($subArray as $id=>$value) {
                                        $sumArray[$id]+=$value;
                                    }
                                }
                                $k = 0;
                                for($i=$scale_from;$i<=$scale_to;$i++)
                                {
                                    //$form_analytics_answers=  Model::factory('formbuilder')->get_analytics_answers($profile["id"],$answer_question_id,$i);
                                    if($mydata=="")
                                        $mydata = "['".$i."',".$sumArray[$k]."]";
                                    else
                                        $mydata .= ",['".$i."',".$sumArray[$k]."]";
                                    if(!isset($color_arr[$color_cnt]))
                                        $color_cnt = $color_cnt%10;
                                    if($map_color_arr=="")
                                        $map_color_arr= "['".$color_arr[$color_cnt]."'";
                                    else
                                        $map_color_arr .= ",'".$color_arr[$color_cnt]."'";
                                    $question_option_text.= "<div ><span style='font-weight:bold;color:".$color_arr[$color_cnt]."'>".$sumArray[$k]."</span><span class='legend-text'>&nbsp;&nbsp;&nbsp;".$i."</span></div>";
                                    $color_cnt++;
                                    $k++;
                                }
                                $map_color_arr.="]";
                            }
                            else if($form_analytics_question["question_type_id"]==5) { //dropdown
                                $color_cnt=0;
                                $question_option_text = "";

                                $dropsumArray = array();
                                foreach ($drop_count_arr as $subArray) {
                                    foreach ($subArray as $id=>$value) {
                                        $dropsumArray[$id]+=$value;
                                    }
                                }

                                for($i=0;$i<count($question_options);$i++)
                                {
                                    $form_analytics_answers=  Model::factory('formbuilder')->get_analytics_answers($profile["id"],$answer_question_id,addslashes($question_options[$i]) );
                                    if($mydata=="")
                                        $mydata = "['".addslashes($question_options[$i])."',".$dropsumArray[$a]."]";
                                    else
                                        $mydata .= ",['".addslashes($question_options[$i])."',".$dropsumArray[$a]."]";
                                    if(!isset($color_arr[$color_cnt]))
                                        $color_cnt = $color_cnt%10;
                                    if($map_color_arr=="")
                                        $map_color_arr= "['".$color_arr[$color_cnt]."'";
                                    else
                                        $map_color_arr .= ",'".$color_arr[$color_cnt]."'";
                                    $question_option_text.= "<div ><span style='font-weight:bold;color:".$color_arr[$color_cnt]."'>".$dropsumArray[$a]."</span><span class='legend-text'>&nbsp;&nbsp;&nbsp;".$question_options[$i]."</span></div>";
                                    $color_cnt++;
                                    $a++;
                                }
                                $map_color_arr.="]";
                            }
                            else
                            {
                                $color_cnt=0;
                                $question_option_text = "";

                                $allsumArray = array();
                                foreach ($all_count_arr as $subArray) {
                                    foreach ($subArray as $id=>$value) {
                                        $allsumArray[$id]+=$value;
                                    }
                                }

                                for($i=0;$i<count($question_options);$i++)
                                {
                                    $form_analytics_answers=  Model::factory('formbuilder')->get_analytics_answers($profile["id"],$answer_question_id,addslashes($question_options[$i]) );
                                    if($mydata=="")
                                        $mydata = "['".addslashes($question_options[$i])."',".$allsumArray[$b]."]";
                                    else
                                        $mydata .= ",['".addslashes($question_options[$i])."',".$allsumArray[$b]."]";
                                    if(!isset($color_arr[$color_cnt]))
                                        $color_cnt = $color_cnt%10;
                                    if($map_color_arr=="")
                                        $map_color_arr= "['".$color_arr[$color_cnt]."'";
                                    else
                                        $map_color_arr .= ",'".$color_arr[$color_cnt]."'";
                                    $question_option_text.= "<div ><span style='font-weight:bold;color:".$color_arr[$color_cnt]."'>".$allsumArray[$b]."</span><span class='legend-text'>&nbsp;&nbsp;&nbsp;".$question_options[$i]."</span></div>";
                                    $color_cnt++;
                                    $b++;
                                }
                                $map_color_arr.="]";
                            }
                            ?>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
						          ['Task', '<?php echo $question_text;?>'],
						          <?php echo $mydata;?>

						        ]);

    var options = {
        title: '<?php echo $question_text;?>',
        is3D: true,
        // legend:{position: 'left'},
        legend:'none',
        chartArea:{width:'200',height:'200'},
        width:'220',
        height:'200',
        sliceVisibilityThreshold:0,
        colors:<?php echo $map_color_arr;?>
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart_3d<?php echo $j;?>'));
    chart.draw(data, options);

    }
</script>
<div class="analytics-question-text" ><?php echo $question_text;?>
    <br>
        <?php echo $question_option_text;?>
</div>
<div id="piechart_3d<?php echo $j;?>" style="width: 220px;height: 200px;float: left;"></div>
<div class="clear">&nbsp;</div>
<div class="clear">&nbsp;</div>
<div class="clear">&nbsp;</div>
    <?php $j++;
    } //end of foreach($form_analytics_questions as $form_analytics_question)
    ?>
                </div>
            </div>
        </div>
    </div>
    
<div style="display:none">
    <a class='expried' href="#export_cumulative_analytic" style="padding: 0;" id="export_cumulative">
        <input class="expried" type="button" id="export_cumulativebutton" value="expried div" />
    </a>
</div>
<div style='display:none;'>
    <div id='export_cumulative_analytic' style='padding:10px; background:#fff;'>
        <div style="float: left;width: 50%;margin-top: 10px;">
            <form id="export_cumulative_form" name="export_cumulative_form" method="post" action="<?php echo URL::base() . 'formbuilder/export_form_cumulative_analytics/';?>">
                <input type="hidden" name="cumulative_code_profile" value="<?php echo $cumulative_profile_id ?>">
                <span style="color:#414a41;font-size: 16px;">Name</span><br><input type="text" id="name" name="name"><br>
                <span style="color:#414a41;font-size: 16px;">Description</span><br>
                <textarea style="margin-bottom: 10px" id="description" name="description" cols="23" rows="3"></textarea>
                <input id="export_cumulative_frmbtn" type="button" value="EXPORT ANALYTICS">
            </form>
        </div>
    </div>
</div>
<?php } else { ?>
    <p>You can not perform this action on expired profile.</p>
<?php } ?>