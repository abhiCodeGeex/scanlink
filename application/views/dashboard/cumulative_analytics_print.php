<script language="javascript">
    $(document).ready(function(){
        $('#header,.top-navigation,#footer,.menu,.player-window-image').remove();
        $('body').css('background','none');
        $('.wrapper').css('width','auto');
        setTimeout(function()
        {
            window.print();
            window.close();
        },4000);
    });
</script>
<style type="text/css">
    .link1 {
        font-size: 15px;
        font-weight: bold;
        color: #6E9113;
    }
</style>

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
        <!-- remain design part end-->
            <?php
            /*$obj= json_decode($jsonCountry);
            $data_arr = $obj->{'DATA'};
            $country_cnt =0;
            for($i=0;$i<count($data_arr);$i++)
            {
                $country_cnt =$country_cnt + $data_arr[$i][2];
            }*/
            ?>
        <!--<div style="float:right;">
            <div class="scanalytics_tot_count"><?php echo $country_cnt;?></div>
            <div class="scanalytics_tot_txt">Total</div>
        </div>-->
        <div class="clear"></div>
        <div class="All" id="divToPrint2">
            <!-- /title -->
            <div class="clear"></div>
                <?php
                //if($profile['enable_form_analytics']==1 && $profile["form_id"]!=0 && count($form_analytics_questions)>0) //form analytics enabled for this profile
                //{
                    //example
                    //$jsonCountry = '{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],"DATA":[["Country","India","21","India ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],"DATA":[["State","Gujarat","19","Gujarat ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["City","Ahmedabad","2","Ahmedabad ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["Device","iphone","10","iphone ^ "],["Device","computer","7","computer ^ "],["Device","ipad","11","ipad ^ "],["Device","ipod","1","ipod ^ "]]}],["City","Rajkot","17","Rajkot ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["Device","iphone","10","iphone ^ "],["Device","computer","7","computer ^ "],["Device","ipad","11","ipad ^ "],["Device","ipod","1","ipod ^ "]]}]]}],["State","Maharashtra","2","Maharashtra ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["City","Mumbai","2","Mumbai ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["Device","iphone","10","iphone ^ "],["Device","computer","7","computer ^ "],["Device","ipad","11","ipad ^ "],["Device","ipod","1","ipod ^ "]]}]]}]]}],["Country","United States","8","United States ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE","DRILL_DATA"],"DATA":[["State","California","8","California ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["City","San Francisco","8","San Francisco ^ ",{"COLUMNS":["CONTEXT","X_BAR_LABEL","X_BAR_VALUE","TOOL_TIP_TITLE"],"DATA":[["Device","iphone","10","iphone ^ "],["Device","computer","7","computer ^ "],["Device","ipad","11","ipad ^ "],["Device","ipod","1","ipod ^ "]]}]]}]]}]]}';
                    ?>
                    <div>
                        <h2 style="padding-left: 50px;font-size: 35px;"><img align="top" src="<?php echo URL::base();?>images/pie_icon.png" width="50" />&nbsp;&nbsp;Form Analytics</h2>
                        <table style="padding-left: 50px;" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td width="52%">
                                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <!--<tr>
                                            <td class="scanalytics_count" width="3%" align="right"><?php echo $country_cnt;?></td>
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
                                <!--<td width="48%" valign="middle" class="scanalytics_percent">
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
                                </td>-->
                            </tr>
                        </table>
                        <div style="height: 50px;clear: both;" >&nbsp;</div>
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
                                        $form_analytics_answers=  Model::factory('formbuilder')->get_analytics_answers($profile["id"],$answer_question_id,$i);
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
                                        $question_option_text.= "<div><span style='font-weight:bold;color:".$color_arr[$color_cnt]."'>".$sumArray[$k]."</span><span class='legend-text'>&nbsp;&nbsp;&nbsp;".$i."</span></div>";
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
                                        $question_option_text.= "<div><span style='font-weight:bold;color:".$color_arr[$color_cnt]."'>".$allsumArray[$b]."</span><span class='legend-text'>&nbsp;&nbsp;&nbsp;".$question_options[$i]."</span></div>";
                                        $color_cnt++;
                                        $b++;
                                    }
                                    $map_color_arr.="]";
                                }
                                ?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Task', '<?php echo $question_text;?>'],
            <?php echo $mydata;?>
            ]);
            var options = {
                title: '<?php //echo $question_text;?>',
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
<div style="page-break-inside:avoid !important;" >
    <div class="analytics-question-text" ><?php echo $question_text;?>
        <br>
            <?php echo $question_option_text;?>
    </div>
    <div id="piechart_3d<?php echo $j;?>" style="width: 220px;height: 200px;float: left; "></div>
    <div class="clear"></div>
</div>
<div class="clear" style="height: 50px;">&nbsp;</div>
    <?php $j++;
    } //end of foreach($form_analytics_questions as $form_analytics_question)
    ?>
            </div>
            <?php
            //}
            ?>
        </div>
    </div>
</div>
