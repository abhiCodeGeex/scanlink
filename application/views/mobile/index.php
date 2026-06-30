<?php if (!isset($_REQUEST['ask_for_location'])) { // apply below style when view in mobile not for preview in profile
?>
    <style>
        @media only screen and (min-device-width : 768px)

        /*for bigger font in mobile page for tablet,pc,laptop*/
            {
            .MobileBottomText h3 {
                font-size: 32px !important;
                line-height: 34px;
            }

            #main_div_mobile p,
            #main_div_mobile p span {
                font-size: 28px !important;
                line-height: 30px;
            }

            .frm_builder h1 {
                font-size: 32px !important;
                line-height: 34px;
            }

            .frm_builder h3 {
                font-size: 30px !important;
                ;
                line-height: 32px;
            }

            .frm_builder {
                font-size: 28px !important;
                line-height: 30px;
            }

            .frm_builder .button,
            .locate-me,
            #map-link {
                font-size: 14px !important;
            }

            .swiper-container,
            .swiper-slide {
                font-size: 28px !important;
                line-height: 30px;
            }


        }
    </style>
<?php
} //end of apply style when view in mobile not for preview in profile
?>

<style scoped>
    #dialog {
        z-index: 99999 !important;
        padding: 10px 5px;
    }

    #dialog-box {
        text-align: center !important;
        opacity: .6 !important;
        background-color: #000 !important;
        width: 100% !important;
        height: 100% !important;
        left: 0 !important;
        opacity: 0.6 !important;
        position: fixed !important;
        top: 0 !important;
        z-index: 999 !important;

    }

    .btnClass,
    .btnClass a {
        background: #5CCD00;
        background: -moz-linear-gradient(top, #5CCD00 0%, #4AA400 100%);
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #5CCD00), color-stop(100%, #4AA400));
        background: -webkit-linear-gradient(top, #5CCD00 0%, #4AA400 100%);
        background: -o-linear-gradient(top, #5CCD00 0%, #4AA400 100%);
        background: -ms-linear-gradient(top, #5CCD00 0%, #4AA400 100%);
        background: linear-gradient(top, #5CCD00 0%, #4AA400 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#5CCD00', endColorstr='#4AA400', GradientType=0);
        padding: 10px 0px;
        color: #fff;
        font-family: 'Helvetica Neue', sans-serif;
        font-size: 20px !important;
        border-radius: 5px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border: 1px solid #459A00;
        text-decoration: none;
        min-width: 200px !important;
        cursor: pointer;
        display: inline-block;
    }

    .powerby a {
        color: #57C200 !important;
        text-decoration: none;
    }

    .clear {
        clear: both;
    }

    #uniform-proceed_to_coupon {
        font-size: 20px;
    }

    .videoWrapper {
        position: relative;
        padding-bottom: 56.25%;
        /* 16:9 */
        padding-top: 25px;
        height: 0;
    }

    .videoWrapper video {
        width: 100%;
    }

    .videoWrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .popup_content {
        font-size: 18px;
    }

    canvas {
        border: 1px solid #DDDDDD;

        background-image: url("<?php echo URL::base(); ?>form-builder/images/writing-hand-icon.png");
        background-repeat: no-repeat;
        background-position: 99% 97%;
        -ms-touch-action: none;
        -ms-user-select: none;
        user-select: none;

    }

    .button.clear {
        background: none;
        height: auto;
        background-color: #808080;
        cursor: pointer;

        border-radius: 5px;
        border: 1px solid #808080;
        color: #ffffff;
        padding: 5px;
        margin-top: 8px;
    }

    .sigPad {
        margin: 0;
        padding: 0;
        height: 250px;
    }

    .sigWrapper {
        clear: both;
        height: 200px;
        -ms-touch-action: none;
        -ms-user-select: none;
        user-select: none;
    }

    body {
        background-image: none;
        background-color: #ffffff;
    }
</style>


<!-- below 2 lines used for location fun in form builder -->
<input type="hidden" id="latitude" value="-34.942043" /> <!-- default location  Australia-->
<input type="hidden" id="longitude" value="138.585168" /> <!-- default location Australia -->

<div id="main_div_mobile">
    <?php

    $valid_in_activation_date_range = '1';

    //below code to check whether user can view profile other than logo based on activation start and end date
    if ($profile['activation_start_date'] != '0000-00-00' && $profile['activation_end_date'] != '0000-00-00') {
        $activation_start_date = new DateTime(date('Y-m-d', strtotime($profile['activation_start_date'])));
        $activation_end_date = new DateTime(date('Y-m-d', strtotime($profile['activation_end_date'])));
        $curr_date = new DateTime(date('Y-m-d', strtotime('now')));

        $interval = $activation_start_date->diff($curr_date);
        $interval_from_start_date = $interval->format('%R%a'); //in days

        $interval = $activation_end_date->diff($curr_date);
        $interval_from_end_date = $interval->format('%R%a'); //in days

        if ($interval_from_start_date < 0) {
            $valid_in_activation_date_range = '0';
        }

        if ($interval_from_end_date > 0) {
            $valid_in_activation_date_range = '0';
        }
    }

    $signature_flag = 0;
    $location_fun_count = 0;

    if ($valid_in_activation_date_range == '1') {
        if (count($videos) != 0) {
    ?>
            <?php
            $i = 0;
            foreach ($videos as $key => $video) : $i++;
                $videoUrlString = explode("&", $video['video_name']);
                $youtubeVideoId = $videoUrlString[0];
            ?>

                <?php // ?rel=0     for disable related videos,
                //&modestbranding=1 for remove youtube logo on controls
                ?>
                <div class="videoWrapper">
                    <?php if (strpos($youtubeVideoId, '.') != false) : ?>
                        <video width="auto" height="auto" controls>
                            <source src="<?php echo url::base() . 'images/video/' . $youtubeVideoId ?>" type="video/mp4">
                        </video>
                    <?php else : ?>
                        <!-- Copy & Pasted from YouTube -->
                        <iframe src="https://www.youtube.com/embed/<?php echo $youtubeVideoId; ?>?rel=0" frameborder="0" allowfullscreen wmode="transparent"></iframe>
                    <?php endif; ?>
                    <!-- Copy & Pasted from YouTube -->
                    <!-- <iframe src="https://www.youtube.com/embed/<?php echo $youtubeVideoId; ?>?rel=0" frameborder="0" allowfullscreen wmode="transparent"></iframe> -->
                </div>
                <?php /* <a href="<?php echo 'http://www.youtube.com/watch?v=' . $video['video_name']; ?>" target="_blank"><?php echo $video['title']; ?></a> */ ?>

            <?php endforeach; ?>
        <?php
        } ?>


        <?php
        foreach ($profile as $key => $value) {
            $profile[$key] = trim($value);
        }
        /* echo "<pre>";
    print_r ($profile);exit; */ ?>
        <div id="progress_bar_mobile">
            <img src="<?php echo url::base(); ?>images/spinner_gray.gif" align="absmiddle" /> Please Wait...
        </div>


        <div class="MobileBottomText">
            <?php if (isset($profile['name']) && $profile['name'] != '') {
            ?>
                <h3>
                    <?php
                    if ($profile['type_id'] == '1') {
                        echo 'Make / Model';
                    } elseif ($profile['type_id'] == '2') {
                        echo 'Location name';
                    } elseif ($profile['type_id'] == '3' && $profile['show_name']) {
                        echo 'Name';
                    } elseif ($profile['type_id'] == '4') {
                        echo 'Product name';
                    } elseif ($profile['type_id'] == '5') {
                        echo 'Title';
                    } elseif ($profile['type_id'] == '6' || $profile['type_id'] == '8') {
                        echo 'Name';
                    } ?>
                </h3>
                <p><?php echo $profile['name']; ?></p>
            <?php
            } ?>

            <?php if ($profile['position'] != '') {
            ?>
                <h3>Position</h3>
                <p><?php echo $profile['position']; ?></p>
            <?php
            } ?>

            <?php if ($profile['identification'] != '') {
            ?>
                <h3>ID</h3>
                <p><?php echo $profile['identification']; ?></p>
            <?php
            } ?>

            <?php if ($profile['serial_no'] != '') {
            ?>
                <h3>Serial No.</h3>
                <p><?php echo $profile['serial_no']; ?></p>
            <?php
            } ?>

            <?php $desc_contents = trim(str_replace('&nbsp;', '', strip_tags($profile['description'])));
            if ($desc_contents != '') {
                if ($profile['show_description'] && $profile['type_id'] != '8') { // not to display misc profile
                    echo '<h3>Description</h3>';
                }

                echo '<p>' . $profile['description'] . '</p>';
            } ?>

            <?php if ($profile['address'] != '') {
            ?>
                <?php if ($profile['show_address']) {
                ?>
                    <h3>Address</h3>
                <?php
                } ?>
                <p><?php echo $profile['address']; ?></p>

                <p>&nbsp;<a href="#" id="map-link" onClick="javascript:return getMapLink('<?php echo $profile['address']; ?>')" class="gray-btn gray-mob-btn">View Map</a></p>
            <?php
            } ?>



            <?php if ($profile['notes'] != '') {
            ?>
                <h3>Notes</h3>
                <p><?php echo $profile['notes']; ?></p>
            <?php
            } ?>


            <?php if (isset($contact) && is_array($contact) && !empty($contact)) {
            ?>
                <?php foreach ($contact as $mycontact) {
                ?>
                    <?php if ($mycontact['name_company'] != '') {
                    ?>
                        <h3>Contact</h3>
                        <p><?php echo $mycontact['name_company']; ?></p>
                    <?php
                    } ?>
                    <?php if ($mycontact['telephone'] != '') {
                    ?>
                        <h3>Telephone</h3>
                        <p><a href="tel:<?php echo $mycontact['telephone']; ?>"><?php echo $mycontact['telephone']; ?></a></p>
                    <?php
                    } ?>
                <?php
                } ?>
            <?php
            } ?>

            <?php if ($profile['telephone'] != '') {
            ?>
                <?php if ($profile['show_telephone']) {
                ?>
                    <h3>Telephone</h3>
                <?php
                } ?>
                <p><a href="tel:<?php echo $profile['telephone']; ?>"><?php echo $profile['telephone']; ?></a></p>
            <?php
            } ?>

            <?php if ($profile['mobile'] != '') {
            ?>
                <?php if ($profile['show_mobile']) {
                ?>
                    <h3>Mobile</h3>
                <?php
                } ?>
                <p><a href="tel:<?php echo $profile['mobile']; ?>"><?php echo $profile['mobile']; ?></a></p>
            <?php
            } ?>

            <?php if ($profile['email'] != '') {
            ?>
                <?php if ($profile['show_email']) {
                ?>
                    <h3>Email</h3>
                <?php
                } ?>
                <p><a href="mailto:<?php echo $profile['email']; ?>"><?php echo $profile['email']; ?></a></p>
            <?php
            } ?>

            <?php if ($profile['url'] != '') {
            ?>
                <?php if ($profile['show_url']) {
                ?>
                    <h3>Website</h3>
                <?php
                } ?>
                <p><a href="<?php echo $profile['url']; ?>" target="_blank"><?php echo $profile['url']; ?></a></p>
            <?php
            } ?>
        </div>


        <?php

        // form builder start

        if ($profile['form_is_enable'] == 1 && $profile['form_id'] != 0 && count($questions_arr) > 0) {
            echo '<form name="frm" id="frm" class="frm_builder" action="' . URL::base() . 'mobile/save_form_builder_data" enctype="multipart/form-data"  method="post">';
            echo '<input type="hidden" name="form_builder" value="1">';
            echo '<input type="hidden" name="form_id" value="' . $profile['form_id'] . '">';
            echo '<input type="hidden" name="profile_id" value="' . $profile['id'] . '">';
            echo '<input type="hidden" name="profile_name" value="' . $profile['code_profile_name'] . '">';

            $input_count = 0;

            foreach ($questions_arr as $questions) {
                $question_id = $questions['question_id'];
                $question_text = $questions['question_text'];
                $is_mandatory = $questions['is_mandatory'];
                $button_link_url = $questions['button_link_url'];
                $button_colour = $questions['button_colour'];
                $doc_title = $questions['doc_title'];
                $include_name = $questions['include_name'];
                $include_employer = $questions['include_employer'];
                $include_email = $questions['include_email'];
                $include_phone = $questions['include_phone'];
                $participant_include_signature = $questions['participant_include_signature'];
                $participant_include_employer = $questions['participant_include_employer'];


                switch ($questions['question_type_id']) {
                    case '1': // Text Field
                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        echo '<input type="text" name="question_' . $question_id . '" class="text-box-black"><br>';
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        ++$input_count;
                        break;
                    case '2': // Text

                        echo '<br>' . $question_text;
                        //echo '<input type="hidden" name="questions[]" value="'.$question_id.'" class="text-box-black">';
                        break;
                    case '3': // Multiple choice

                        $question_options = Model_formbuilder::get_question_option($question_id);

                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        foreach ($question_options as $question_option) {
                            $question_option_text = $question_option['option_name'];
                            echo "<input type='radio' name='question_" . $question_id . "' value='" . htmlentities($question_option_text, ENT_QUOTES) . "' />&nbsp;" . $question_option_text . '<br>';
                        }
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        ++$input_count;

                        break;
                    case '4': // Checkboxes

                        $question_options = Model_formbuilder::get_question_option($question_id);
                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        foreach ($question_options as $question_option) {
                            $question_option_text = $question_option['option_name'];
                            echo "<input type='checkbox' name='question_" . $question_id . "[]' value='" . htmlentities($question_option_text, ENT_QUOTES) . "' />&nbsp;" . $question_option_text . '<br>';
                        }
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        ++$input_count;

                        break;
                    case '5': // Drop Down Menu
                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';
                        $question_options = Model_formbuilder::get_question_option($question_id, '', 'option_name');


                        echo "<select name='question_" . $question_id . "' style='width:150px;'>";
                        echo "<option value=''>Select</option>";

                        //$question_options = Model_formbuilder::get_question_option($question_id);

                        foreach ($question_options as $question_option) {
                            $question_option_text = $question_option['option_name'];
                            echo "<option value='" . htmlentities($question_option_text, ENT_QUOTES) . "'>" . $question_option_text . '</option>';
                        }

                        echo '</select>';

                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        echo '<br>';
                        ++$input_count;
                        break;
                    case '6': // Number Scale
                        $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                        $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                        $scale_from = $question_options[0]['option_name'];

                        $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                        $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                        $scale_to = $question_options[0]['option_name'];

                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        for ($i = $scale_from; $i <= $scale_to; ++$i) {
                            echo "<input type='radio' name='question_" . $question_id . "' value='" . $i . "' />&nbsp;" . $i . '<br>';
                        }
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        ++$input_count;
                        break;
                    case '7': // Grid

                        $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                        $row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                        $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                        $column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        echo "<table border='0' cellpadding='6' cellspacing='0'>";
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
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        ++$input_count;
                        break;
                    case '8': // Date
                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        echo '<input type="text" class="dtpicker_date" id="dtpicker_date' . $question_id . '" name="question_' . $question_id . '" readonly placeholder="Select Date" ><br>';
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        ++$input_count;
                        break;
                    case '9': // time
                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        echo '<input type="text" class="datepicker dtpicker_time" id="dtpicker_time' . $question_id . '"  name="question_' . $question_id . '" readonly placeholder="Select Time" ><br>';
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        ++$input_count;
                        break;
                    case '10': // heading
                        echo "<h1 style='font-size:20px;margin:0;'>" . $question_text . '</h1>';
                        break;
                    case '11': // image

                        if ($question_text != '') {
                            //echo '<br><div align="'.$questions["image_align"].'"><img src="'.URL::base() .'images/form_builder_uploaded_images/'.$question_text. '"  style="max-height: 200px;max-width:250px;" title="'.$questions["image_title"].'" ></div>';
                            echo '<br><div align="' . $questions['image_align'] . '"><img src="' . URL::base() . 'images/form_builder_uploaded_images/' . $question_text . '"  style="width: 100%;max-width:100%;" title="' . $questions['image_title'] . '" ></div>';
                        }

                        break;
                    case '12': // sub heading
                        echo "<h3  style='font-size:16px;margin:0;'>" . $question_text . '</h3>';
                        break;
                    case '13': // Line Divider
                        echo '<hr>';
                        break;
                    case '14': // Blank Space
                        echo '<br><br>';
                        break;
                    case '15': // comments
                        echo '<br><textarea name="question_' . $question_id . '"  rows="3" class="text-box-black"></textarea>';
                        ++$input_count;
                        echo '<br>';
                        break;
                    case '16': // Signature

                        $signature_flag = 1;

                        if ($include_name == '1') {
                            echo 'Name';
                            if ($is_mandatory == '1') {
                                echo '<span class="mandatory_field" style="display:inline;">*</span>';
                            }

                            echo '<br><input type="text" name="sub_ques_' . $question_id . '[]" value="" /><br>';
                        }

                        if ($include_employer == '1') {
                            echo 'Employer/Company';
                            if ($is_mandatory == '1') {
                                echo '<span class="mandatory_field" style="display:inline;">*</span>';
                            }

                            echo '<br><input type="text" name="sub_ques2_' . $question_id . '[]" value="" /><br>';
                        }

                        if ($include_email == '1') {
                            echo 'Email';
                            if ($is_mandatory == '1') {
                                echo '<span class="mandatory_field" style="display:inline;">*</span>';
                            }

                            echo '<br><input type="text" name="sub_ques3_' . $question_id . '[]" value="" /><br>';
                        }
                        if ($include_phone == '1') {
                            echo 'Phone';
                            if ($is_mandatory == '1') {
                                echo '<span class="mandatory_field" style="display:inline;">*</span>';
                            }
                            echo '<br><input type="text" name="sub_ques4_' . $question_id . '[]" value="" /><br>';
                        }

                        if ($question_text != '') {
                            echo $question_text;
                        }

                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        if ($question_text != '') {
                            echo '<br>';
                        }

                        echo '<div class="sigPad">
                        <div class="sig sigWrapper">
                            <div class="typed"></div>
                            <canvas class="pad" id="sign_' . $question_id . '">Your browser does not support the HTML5 canvas tag.</canvas>


                            <input type="hidden" id="signature" name="question_' . $question_id . '[]" class="output">
                            <input type="hidden" name="sign_width" value="" id="sign_width">

                        </div>


                        <button class="button clear clearButton" data-action="clear"  type="button">Clear Signature</button>

                        </div>';

                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        echo '<div align="right" class="add_another_file  add_another_sign" onclick="javascript:add_another_signature(' . $question_id . ',&quot;' . $question_text . '&quot;,this,&quot;' . $include_name . '&quot;,&quot;' . $include_employer . '&quot;,&quot;' . $include_email . '&quot;,&quot;' . $include_phone . '&quot;,&quot;' . $is_mandatory . '&quot;)">Add Another</div>';

                        echo '<input type="hidden" name="sign_width_' . $question_id . '" id="sign_width_' . $question_id . '" value="" />';

                        echo '<br>';

                        ++$input_count;
                        break;

                    case '17': // file upload

                        if ($question_text != '') {
                            echo $question_text;
                        }

                        echo '<br><input type="file" name="question_' . $question_id . '[]"  />';
                        echo '<div align="right" class="add_another_file" onclick="javascript:add_another_file(' . $question_id . ',this)">Add Another</div>';

                        ++$input_count;

                        break;

                    case '18': // participant name

                        if ($question_text != '') {
                            echo $question_text;
                        } else {
                            echo 'Name';
                        }

                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br><input type="text" name="question_' . $question_id . '[]" class="auto_complete text-box-black"><br>';

                        if ($participant_include_employer == '1') {
                            echo 'Employer/Company';
                            if ($is_mandatory == '1') {
                                echo '<span class="mandatory_field" style="display:inline;">*</span>';
                            }

                            echo '<br><input type="text" name="parti_que_' . $question_id . '[]" value="" /><br>';
                        }
                        if ($participant_include_signature == '1') {
                            $signature_flag = 1;
                            echo 'Signature';
                            if ($is_mandatory == '1') {
                                echo '<span class="mandatory_field" style="display:inline;">*</span>';
                            }

                            echo '<div class="sigPad">
                                        <div class="sig sigWrapper">
                                          <div class="typed"></div>
                                          <canvas class="pad" id="sign_' . $question_id . '">Your browser does not support the HTML5 canvas tag.</canvas>


                                          <input type="hidden" id="signature" name="sign_que_' . $question_id . '[]" class="output">
                                          <input type="hidden" name="p_sign_width" value="" id="p_sign_width">

                                        </div>


                                            <button class="button clear clearButton" data-action="clear"  type="button">Clear Signature</button>

                                     </div>';

                            echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                            echo '<input type="hidden" name="p_sign_width_' . $question_id . '" id="p_sign_width_' . $question_id . '" value="" />';
                            echo '<br>';
                        }

                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        echo '<div align="right" class="add_another_file" onclick="javascript:add_another_participant(' . $question_id . ',&quot;' . $question_text . '&quot;,' . $participant_include_signature . ',' . $participant_include_employer . ',' . $is_mandatory . ',this)">Add Another</div>';

                        ++$input_count;
                        break;

                    case '19': // location function

                        if ($question_text != '') {
                            echo $question_text;
                        }

                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br><input type="text" id="question_' . $question_id . '" name="question_' . $question_id . '" style="width:90%" class="text-box-black"><br>';
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        echo '<div id="locate_me_' . $question_id . '" class="locate-me" >MAP</div>';
                        ++$input_count;
                        ++$location_fun_count;
                        break;

                    case '20': //web link button

                        echo '<div class="weblink_doc_btn_br"></div><a href="' . $button_link_url . '" target="_blank" >';
                        echo '<div style="background-color:#' . $button_colour . ';" class="web_link_button" >' . $question_text . '</div>';
                        echo '</a>';

                        break;

                    case '21': //Document Button

                        //echo '<br><a href="'.URL::base() .'images/form_builder_uploaded_images/'.$question_text. '" target="_blank">';
                        echo '<div class="weblink_doc_btn_br"></div><a href="' . URL::base() . 'download_file.php?filename=' . $question_text . '" target="_blank" >';
                        echo '<div style="background-color:#' . $button_colour . ';" class="web_link_button" >' . $doc_title . '</div>';
                        echo '</a>';

                        break;

                    case '22': // SWMS Hazard/Risk
                        if ($question_text != '') {
                            echo '<br>' . $question_text;
                        }

                        if ($is_mandatory == '1') {
                            $mandatory_hazard = '<span class="mandatory_field" style="display:inline;">*</span>';
                        } else {
                            $mandatory_hazard = '';
                        }
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        echo '<br><br>Task | Activity' . $mandatory_hazard . '<br><textarea rows="1" class="hazard_textarea" name="question_' . $question_id . '[]" ></textarea><br><br>';
                        echo 'Potential Hazards' . $mandatory_hazard . '<br><textarea rows="1" class="hazard_textarea" name="question_' . $question_id . '[]" ></textarea><br><br>';
                        echo 'Risk Score (Before)' . $mandatory_hazard . '<br>';
                        echo '<select name="question_' . $question_id . '[]" style="width:150px;">';
                        echo '<option value="">Select</option>';
                        echo '<option value="1">1</option>';
                        echo '<option value="2">2</option>';
                        echo '<option value="3">3</option>';
                        echo '<option value="4">4</option>';
                        echo '<option value="5">5</option>';
                        echo '</select></br></br>';

                        //New Added Code Start
                        echo '<div class="file-upload-control">';
                        echo '<input type="file" class="multiple_file_upload" name="hwms_question_' . $question_id . '[]" multiple data-question_id=' . $question_id . '>
                    <span class="row upload-img-row upload_file_preview_' . $question_id . '">
                    <input type="hidden" name="hwms_file_' . $question_id . '[]"></span>';
                        echo '</div>';
                        //New Added Code End

                        echo '<br><br>Control Measures' . $mandatory_hazard . '<br><textarea rows="1" class="hazard_textarea" name="question_' . $question_id . '[]" ></textarea><br><br>';
                        echo 'Risk Score (After)' . $mandatory_hazard . '<br>';
                        echo '<select name="question_' . $question_id . '[]" style="width:150px;">';
                        echo '<option value="">Select</option>';
                        echo '<option value="1">1</option>';
                        echo '<option value="2">2</option>';
                        echo '<option value="3">3</option>';
                        echo '<option value="4">4</option>';
                        echo '<option value="5">5</option>';
                        echo '</select><br>';
                        echo '<div align="right" class="add_another_file" onclick="javascript:add_another_hazard(' . $question_id . ',&quot;' . $question_text . '&quot;,this,' . $is_mandatory . ',' . $input_count . ')">Add Another</div>';
                        ++$input_count;
                        break;

                    case '23': //document menu

                        $select_value = explode(',', $question_text);

                        $select_txt = explode(',', $doc_title);

                        if ($is_mandatory == '1') {
                            echo '<span class="mandatory_field" style="display:inline;">*</span><br>';
                        } else {
                            echo '<br>';
                        }

                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        echo '<select name="question_' . $question_id . '[]" class="document-menu-select" multiple="">';
                        echo '<option value="" disabled="">Select Attachment</option>';
                        $i = 0;
                        foreach ($select_value as $value) {
                            echo '<option value="' . $value . '">' . $select_txt[$i] . '</option>';
                            ++$i;
                        }
                        echo '</select><br>';
                        ++$input_count;
                        break;
                    case '24': //Add recipient

                        $mandatory_hazard = "";
                        if (!empty($is_mandatory)) {
                            $mandatory_hazard = '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo "<div class='add_recipient' style='height:auto;text-align:left;padding-left:10px; border:none;'>";
                        echo 'Send email notifications to ' . $mandatory_hazard . '<br>';
                        echo "<div class='rounded' >";
                        echo "<input style='width:90%' id='email_recipient_additional_" . $question_id . "' class='email_recipient_additional' name='email_recipient_additional_" . $question_id . "[]' type='text' data-field=" . $is_mandatory . ">";
                        echo '<input type="hidden"  name="email_mandatory_' . $question_id . '"  id="email_mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        echo '</div>';
                        echo "<div class='add_another_file' align='right' onclick='javascript:add_another_email_recipient(" . $question_id . ", this," . $is_mandatory . ");'>Add Another</div>";
                        echo '</div>';
                        echo '<br>';

                        ++$input_count;
                        break;
                    case '25': //covid checkin form

                        $span_required_field = '<span style="color:red">*</span>';

                        echo '<div style="padding: 10px; background-color: #' . $questions['covid_bg_color'] . '">';
                        echo '<p style="color:#' . $questions['covid_text_color'] . '">' . $question_text . '</p>';
                        echo "<div class='rounded' >";
                        echo '<div class="covid-checkinform">';

                        echo '<p>';
                        echo '<span style="color:#' . $questions['covid_text_color'] . '">Visitor name' . $span_required_field . ' </span><br>';
                        echo '	<input class="input-field name" type="text" name="covid_question_' . $question_id . '[]" required/>';
                        echo '</p>';

                        echo '<p>';
                        echo '<span style="color:#' . $questions['covid_text_color'] . '">Visitor phone' . $span_required_field . '</span><br>';
                        echo '	<input class="input-field contact" type="number" name="covid_question_' . $question_id . '[]"  required/>';
                        echo '</p>';

                        echo '<p>';
                        echo '<span style="color:#' . $questions['covid_text_color'] . '">Date' . $span_required_field . '</span><br>';
                        echo '	<input class="input-field email dtpicker_date" type="text" name="covid_question_' . $question_id . '[]"  required/>';
                        echo '</p>';

                        echo '<p>';
                        echo '<span style="color:#' . $questions['covid_text_color'] . '">Time' . $span_required_field . '</span><br>';
                        echo '<input class="input-field map dtpicker_time" type="text" name="covid_question_' . $question_id . '[]" required/>';
                        echo '</p>';

                        echo '<p>';
                        echo '<span style="color:#' . $questions['covid_text_color'] . '">Venue name' . $span_required_field . '</span><br>';
                        echo '<input class="input-field " type="text" name="covid_question_' . $question_id . '[]" required/>';
                        echo '</p>';

                        echo '<p>';
                        echo '<span style="color:#' . $questions['covid_text_color'] . '">Venue Address' . $span_required_field . '</span><br>';
                        echo '<input class="input-field " type="text" name="covid_question_' . $question_id . '[]" required/>';
                        echo '<p>';


                        echo '<p style="color:#' . $questions['covid_text_color'] . '">Location Description/Type' . $span_required_field . '</p>';

                        $location_description_dropdown = unserialize(LOCATION_DESCRIPTION);

                        $covid_text_color = "'" . $questions['covid_text_color'] . "'";

                        echo '<select name="covid_question_' . $question_id . '[]" class="location_desc_option" onchange="javascript:selectChangeVehicleOptions(this.value,' . $question_id . ',' . $covid_text_color . ')" >';
                        echo '<option value="">Select</option>';
                        foreach ($location_description_dropdown as $key => $location_description) {
                            echo '<option value="' . $location_description . '" data="' . $location_description . '">' . $location_description . '</option>';
                            echo '<p>Location address</p>';
                        }
                        echo '</select>';


                        echo '<p class="vehicle_no" id="vehicle_no_' . $question_id . '"></p>';

                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        // echo $html;
                        ++$input_count;
                        ++$location_fun_count;
                        break;
                } //end switch case
            } //end for each
            if ($input_count > 0) {
                echo "<br><input class='submit-btn' type='submit' value='Submit'>";
            }

            echo '</form>';
            echo '<br>';
        } // end if $profile["form_is_enable"]==1

        // form builder end
        ?>
        <?php $photo_count_to_swipe = 0;
        if (is_array($pictures) && count($pictures) > 0) : ?>
            <div style="width: 100%">
                <div class="swiper-main">
                    <div class="swiper-container swiper1">
                        <div class="swiper-wrapper">
                            <?php
                            foreach ($pictures as $key => $val) :
                                $photo_count_to_swipe++; ?>
                                <div class="swiper-slide" style="text-align: center">
                                    <a href="<?php echo URL::base() . $val['picture_name']; ?>">
                                        <img src="<?php echo URL::base() . $val['picture_name']; ?>" style="max-height: 166px;max-width:250px;" alt="<?php if ($val['txt_footer'] != '') {
                                                                                                                                                            echo $val['txt_footer'];
                                                                                                                                                        } ?>" />
                                    </a>
                                    <?php if ($val['txt_footer'] != '') {
                                        echo '<br>' . $val['txt_footer'];
                                    } ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="pagination pagination1"></div>
            </div>
        <?php endif; ?>

        <?php
        /* echo "<pre>";
    print_r($documents);
    echo "</pre>"; */
        ?>

        <div class="MobileInnerList">
            <ul class="documentList">
                <?php if (isset($documents) && is_array($documents) && count($documents) > 0) {
                ?>
                    <?php
                    $i = 0;
                    foreach ($documents as $key => $document) : $i++;

                        $btn_color = ($document['btn_color'] != '') ? '#' . $document['btn_color'] : '#007A01';
                        $txt_align = $document['txt_align']; ?>
                        <li style="background:<?php echo $btn_color; ?>;text-align:<?php echo $txt_align; ?>">
                            <a href="<?php echo URL::base() . $document['doc_name']; ?>" target="_blank"><?php echo $document['name'] != '' ? $document['name'] : $document['doc_name']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php
                } else {
                ?>
                <?php
                } ?>
            </ul>
        </div>

        <div class="MobileInnerList">
            <ul class="WeblinkList">
                <?php if (isset($weblink) && is_array($weblink) && !empty($weblink)) {
                ?>
                    <?php foreach ($weblink as $key => $myweblink) {
                        $link_button_color = ($myweblink['link_button_color'] != '') ? '#' . $myweblink['link_button_color'] : '#007A01';
                        $link_button_align = $myweblink['link_button_align']; ?>
                        <?php if ($myweblink['link_button'] == '1') : ?>
                            <li style="background:<?php echo $link_button_color; ?>;text-align:<?php echo $link_button_align; ?>"><a href="<?php echo $myweblink['link_button_url']; ?>" target="_blank"><?php echo $myweblink['link_button_text']; ?></a></li>
                        <?php endif; ?>
                    <?php
                    } ?>
                <?php
                } ?>

            </ul>
        </div>

        <?php if (isset($profile['display_share_link']) && $profile['display_share_link'] == '1') {
        ?>
            <div class="shareNav-mob">
                <!--<a href="http://www.facebook.com/share.php?u=<?php echo $profile['shorturl']; ?>" target="_blank" class="shareFB">Facebook</a>-->
                <a href="http://www.facebook.com/share.php?u=<?php echo $current_url; ?>" target="_blank" class="shareFB">Facebook</a>
                <a href="http://twitter.com/share?text=<?php echo $profile['name'] . '  ' . $profile['shorturl']; ?>" target="_blank" class="shareTWT">Twitter</a>
                <a href="mailto:?subject=Visit this link&amp;body=Hi, I found this information for you! Have a nice day :) : <?php echo $profile['shorturl']; ?>" target="_blank" class="shareEML">Email</a>
            </div>
        <?php
        } ?>


        <?php if ($photo_count_to_swipe > 0) {
        ?>
            <script type="text/javascript">
                $(function() {

                    //Main Swiper
                    var swiper = new Swiper('.swiper1', {
                        loop: true,
                        grabCursor: true,
                        captionAndToolbarHideOnSwipe: false,
                        captionAndToolbarHide: false,
                        captionAndToolbarAutoHideDelay: 0,
                        captionAndToolbarFlipPosition: true
                    });
                });


                (function(window, PhotoSwipe) {

                    document.addEventListener('DOMContentLoaded', function() {

                        var options = {
                                captionAndToolbarHideOnSwipe: false,
                                captionAndToolbarHide: false,
                                captionAndToolbarAutoHideDelay: 0,
                                captionAndToolbarFlipPosition: true
                            },
                            instance = PhotoSwipe.attach(window.document.querySelectorAll('.swiper-wrapper a'), options);

                    }, false);


                }(window, window.Code.PhotoSwipe));


                $(window).bind("load", function() {

                    //$('.swiper-slide').css('width','320px');
                    $('.swiper-container').css('width', '320px');
                    $(window).trigger('resize');
                    //console.log($('.swiper-slide').css('width'));
                });
            </script>
        <?php
        } //end of if($photo_count_to_swipe>0)
        ?>


        <script type="text/javascript">
            var currentdate = new Date();
            var varYear = currentdate.getFullYear();
            var varMonth = currentdate.getMonth() + 1;
            var varDate = currentdate.getDate();
            var dateTime = varYear + "-" + varMonth + "-" + varDate + " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();



            function setCookie(pLabel, pVal, pMinutes) {
                var tExpDate = new Date();
                tExpDate.setTime(tExpDate.getTime() + (pMinutes * 60 * 1000));
                document.cookie = pLabel + "=" + escape(pVal) +
                    ((pMinutes == null) ? "" : ";expires=" + tExpDate.toGMTString());
            }

            function getCookie(c_name) {
                var i, x, y, ARRcookies = document.cookie.split(";");
                for (i = 0; i < ARRcookies.length; i++) {
                    x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
                    y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
                    x = x.replace(/^\s+|\s+$/g, "");
                    if (x == c_name) {
                        return unescape(y);
                    }
                }
            }

            function numbersonly(e) {
                var unicode = e.charCode ? e.charCode : e.keyCode
                if (unicode != 8) { //if the key isn't the backspace key (which we should allow)
                    if (unicode < 48 || unicode > 57) //if not a number
                        return false //disable key press
                }
            }

            function checkEmail(email) {
                var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,6})+$/;
                if (!filter.test(email)) {
                    return false;
                } else {
                    return true;
                }
            }


            $(function() {

                var currentdate = new Date();
                var dateTime = currentdate.getFullYear() + "-" + currentdate.getMonth() + "-" + currentdate.getDate() + " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();
                //alert (dateTime);

                <?php //if ($profile["enable_data_collection"] == "1" && (!Session::instance()->get('This is main question tool box itemsfo') || trim(Session::instance()->get('user_info')) != $profile["id"])) {
                ?>
                <?php if ($profile['enable_data_collection'] == '1' && (!Session::instance()->get('user_info') || trim(Session::instance()->get('user_info')) != $profile['id'])) {
                ?>


                    setTimeout(function() {
                        $('.ui-dialog-titlebar-close').hide();
                        //$('#dialog-box').css('width',$(document).width()+'%');
                        //$('#dialog-box').css('height',$(document).height()+'%');
                    }, 500);

                    if (!getCookie("isContactDialog")) {
                        //setCookie("isContactDialog", true, 10)
                        $(function() {
                            $('#dialog-box').show();
                            $("#dialog").dialog({
                                autoOpen: true,
                                show: "blind",
                                draggable: false,
                                position: 'center',
                                //title:'Exclusive Offers'
                            }).dialog("widget").find(".ui-dialog-title").hide();
                        });
                    }
                    $('.ui-dialog-close').live('click', function() {

                        <?php
                        if ($profile['set_up_compulsory'] == 1) {
                        ?>
                            <?php if ($profile['data_collection_name']) {
                            ?>
                                if ($('#data_collection_name').val() == "") {
                                    alert("Please Enter a name.");
                                    $('#data_collection_name').focus();
                                    return false;
                                }
                            <?php
                            } ?>
                            <?php if ($profile['data_collection_surname']) {
                            ?>
                                if ($('#data_collection_surname').val() == "") {
                                    alert("Please Enter a surname.");
                                    $('#data_collection_surname').focus();
                                    return false;
                                }
                            <?php
                            } ?>
                            <?php if ($profile['data_collection_mobile']) {
                            ?>
                                if ($('#mobile_number').val() == "") {
                                    alert("Please Enter a mobile number.");
                                    $('#mobile_number').focus();
                                    return false;
                                }
                            <?php
                            } ?>
                            <?php if ($profile['data_collection_email']) {
                            ?>
                                if ($('#email_address').val() == "") {
                                    alert("Please Enter a email address.");
                                    $('#email_address').focus();
                                    return false;
                                }
                            <?php
                            } ?>

                        <?php
                        } ?>


                        if ($('#mobile_number').length > 0) {
                            if ($('#mobile_number').val() !== "") {
                                if ($('#mobile_number').val().length != 10) {
                                    alert("Invalid contact number. Please enter a valid 10 digit mobile phone number.");
                                    $('#mobile_number').focus();
                                    return false;
                                }
                            }
                        }
                        //alert (typeof($('#email_address').length));
                        if ($('#email_address').length > 0) {
                            if ($('#email_address').val() !== "") {
                                if (!checkEmail($('#email_address').val())) {
                                    alert("Invalid email address");
                                    $('#email_address').focus();
                                    return false;
                                }
                            }
                        }




                        var currentdate = new Date();
                        var varYear = currentdate.getFullYear();
                        var varMonth = currentdate.getMonth() + 1;
                        var varDate = currentdate.getDate();
                        var dateTime = varYear + "-" + varMonth + "-" + varDate + " " + currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds();



                        $.ajax({
                            type: 'post',
                            url: '<?php echo url::site('mobile/addUserInfo'); ?>',
                            data: {
                                profile_id: $('#profile_id').val(),
                                mobile_number: $('#mobile_number').val(),
                                email_address: $('#email_address').val(),
                                name: $('#data_collection_name').val(),
                                surname: $('#data_collection_surname').val(),
                                datetime: dateTime

                            },
                            success: function(data) {
                                console.log(data);
                            },
                            error: function(e) {

                            }
                        })



                        $('#dialog-box').hide();
                        $("#dialog").dialog("close");
                        return false;
                    });
                <?php
                } ?>


            });

            var mymap = null;

            function getMapLink(adr) {
                var maplink = "http://maps.google.com?q=" + adr;
                if (mymap == null || mymap.closed) {
                    mymap = window.open('' + maplink, 'mywin', 'left=20,top=20,width=500,height=500,toolbar=1,resizable=0,location=0,status=0');
                }
                return true;
            }
        </script>
        <script type="text/javascript">
            /*var myphotoswipe = null;
$(document).ready(function(){

  $('input.prettyCheckable').prettyCheckable({
    color: 'yellow'
  });

  myphotoswipe = $(".gallery a").photoSwipe({ enableMouseWheel: false , enableKeyboard: false, allowUserZoom:false });
  $(".gallery li").hide();
  $(".gallery li:first").show();

  $("#photoswipe").click(function(){
    myphotoswipe.show(0);
  });


});

function showGallery(){
  myphotoswipe.show(0);
}

$('.prettyCheckable').live('change', function(){
  if($(this).is(':checked')){
    $.getJSON('<?php echo URL::base(); ?>checklistitem/checklistCheck/'+$(this).val(),function(data){});
  }else{
    $.getJSON('<?php echo URL::base(); ?>checklistitem/checklistUncheck/'+$(this).val(),function(data){});
  }
});*/
        </script>

        <!-- <div id="form-builder-dialog-box" style="display: none;">
            <div id="dialog1" title="Basic dialog" style="text-align: center">
                <?php echo '<div >' . $form_submit_success . '</div>'; ?><br><br>
                <div id="close_dialog">Close</div>
            </div>
        </div> -->

        <div id="form-builder-dialog-box" style="display: none;">
            <div id="dialog1" title="Basic dialog" class="success-popup">
                <div class="check-icon">
                    <svg version="1.1" width="50" height="50" fill="#FFF" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                        <g>
                            <g>
                                <path d="M497.36,69.995c-7.532-7.545-19.753-7.558-27.285-0.032L238.582,300.845l-83.522-90.713
			c-7.217-7.834-19.419-8.342-27.266-1.126c-7.841,7.217-8.343,19.425-1.126,27.266l97.126,105.481
			c3.557,3.866,8.535,6.111,13.784,6.22c0.141,0.006,0.277,0.006,0.412,0.006c5.101,0,10.008-2.026,13.623-5.628L497.322,97.286
			C504.873,89.761,504.886,77.54,497.36,69.995z" />
                            </g>
                        </g>
                        <g>
                            <g>
                                <path d="M492.703,236.703c-10.658,0-19.296,8.638-19.296,19.297c0,119.883-97.524,217.407-217.407,217.407
			c-119.876,0-217.407-97.524-217.407-217.407c0-119.876,97.531-217.407,217.407-217.407c10.658,0,19.297-8.638,19.297-19.296
			C275.297,8.638,266.658,0,256,0C114.84,0,0,114.84,0,256c0,141.154,114.84,256,256,256c141.154,0,256-114.846,256-256
			C512,245.342,503.362,236.703,492.703,236.703z" />
                            </g>
                        </g>
                    </svg>

                </div>
                <?php echo '<div><h2>' . $form_submit_success . ' </h2></div>'; ?>
                <div id="close_dialog">Close</div>
            </div>
        </div>


        <div id="dialog-box" style="display: none;">
            <form name="frm_save_contact" id="frm_save_contact">

                <input type="hidden" name="cid" value="<?php //echo $cid;
                                                        ?>" />
                <input type="hidden" name="mid" value="<?php //echo $mid;
                                                        ?>" />
                <input type="hidden" name="profile_id" id="profile_id" value="<?php echo $profile['id']; ?>" />

                <div id="dialog" title="Basic dialog">
                    <?php if ($profile['data_collection_content'] != '') : ?>
                        <p class="popup_content"><?php echo $profile['data_collection_content']; ?></p>
                    <?php else : ?>
                        <p class="popup_content">Register your mobile number to receive exclusive weekly offers.</p>
                    <?php endif; ?>
                    <?php if ($profile['data_collection_name']) : ?>
                        <p style="text-align:center;">

                            <?php echo Form::input('name', '', array('type' => 'text', 'placeholder' => 'Name', 'id' => 'data_collection_name')); ?>

                            <?php /* <input type="tel" placeholder="Mobile Number"  id="mobile_number" onKeyPress="return numbersonly(event);" name="mobile" width="100%"  maxlength="10" > */ ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($profile['data_collection_surname']) : ?>
                        <p style="text-align:center;">
                            <?php echo Form::input('surname', '', array('type' => 'text', 'placeholder' => 'Surname', 'id' => 'data_collection_surname')); ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($profile['data_collection_mobile']) : ?>
                        <p style="text-align:center;">

                            <?php echo Form::input('mobile', '', array('type' => 'tel', 'placeholder' => 'Mobile Number', 'id' => 'mobile_number', 'onKeyPress' => 'return numbersonly(event);', 'maxlength' => 10)); ?>

                            <?php /* <input type="tel" placeholder="Mobile Number"  id="mobile_number" onKeyPress="return numbersonly(event);" name="mobile" width="100%"  maxlength="10" > */ ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($profile['data_collection_email']) : ?>
                        <p style="text-align:center;">
                            <input type="text" placeholder="Email Address" id="email_address" name="email_address" width="100%" maxlength="255" />
                        </p>
                    <?php endif; ?>

                    <div id="add_contact" style="text-align:center;" class="ui-dialog-close">PROCEED</div>


                </div>
            </form>
        </div>

        <script>
            // form builder related jQuery
            $(document).ready(function() {

                $(".dtpicker_date").datepicker({
                    dateFormat: 'dd/mm/yy'
                });

                $(".dtpicker_time").timepicker({
                    timeFormat: 'HH:mm:ss'
                });

                $(".submit-btn").click(function() {
                    var is_mandatory = 0;
                    for (i = 0; i < document.frm.elements.length; i++) {
                        if (document.frm.elements[i].name.indexOf("sign_que_") > "-1") {
                            form_element = document.frm.elements[i];

                            ele_name = document.frm.elements[i].name;
                            ele_name_temp = ele_name.split('sign_que_');

                            temp_question_id = ele_name_temp[1].replace('[]', '');
                            temp_question_id = temp_question_id.replace(/_row[0-9]*/ig, '');

                            if (document.getElementById('mandatory_' + temp_question_id))
                                is_mandatory = document.getElementById('mandatory_' + temp_question_id).value;

                            if (document.frm.elements[i].id == "signature") {
                                ques_id = document.frm.elements[i].name.split("[]").join('');
                                quest_id_temp = ques_id.split('sign_que_');
                                temp_ques_id = quest_id_temp[1];
                                p_sign_width = $('#p_sign_width').val();

                                if (form_element.value.split(" ").join('') == "") {
                                    alert("Please provide signature.");
                                    return false;
                                } else {
                                    document.getElementById('p_sign_width_' + temp_ques_id).value = p_sign_width;
                                }

                            }
                        }

                        if (document.frm.elements[i].name.indexOf("parti_que_") > "-1") {

                            form_element = document.frm.elements[i];

                            ele_name = document.frm.elements[i].name;
                            ele_name_temp = ele_name.split('parti_que_');

                            temp_question_id = ele_name_temp[1].replace('[]', '');
                            temp_question_id = temp_question_id.replace(/_row[0-9]*/ig, '');

                            is_mandatory = 0;
                            if (document.getElementById('mandatory_' + temp_question_id))
                                is_mandatory = document.getElementById('mandatory_' + temp_question_id).value;

                            if (form_element.type == "text" && is_mandatory == "1") {
                                if (form_element.value.split(" ").join('') == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }
                        }

                        if (document.frm.elements[i].name.indexOf("question_") > "-1") {
                            //ele_name = document.frm.elements[i].name.split('question_');
                            form_element = document.frm.elements[i];
                            //alert(form_element.nodeName);  TEXTAREA

                            ele_name = document.frm.elements[i].name;
                            ele_name_temp = ele_name.split('question_');

                            temp_question_id = ele_name_temp[1].replace('[]', '');
                            temp_question_id = temp_question_id.replace(/_row[0-9]*/ig, '');
                            is_mandatory = 0;
                            if (document.getElementById('mandatory_' + temp_question_id))
                                is_mandatory = document.getElementById('mandatory_' + temp_question_id).value;

                            if (form_element.type == "text" && typeof(is_mandatory) != 'undefined' && is_mandatory == "1") {
                                if (form_element.value.split(" ").join('') == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }

                            if (document.frm.elements[i].id == "signature") {
                                ques_id = document.frm.elements[i].name.split("[]").join('');
                                quest_id_temp = ques_id.split('question_');

                                temp_ques_id = quest_id_temp[1];

                                sign_width = $('#sign_width').val();
                                if (form_element.value.split(" ").join('') == "" && is_mandatory == "1") {
                                    alert("Please provide signature.");
                                    return false;
                                } else {
                                    document.getElementById('sign_width_' + temp_ques_id).value = sign_width;
                                }

                            }


                            if (form_element.type == "select-multiple" && is_mandatory == "1") {
                                if (form_element.value.split(" ").join('') == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }

                            if (form_element.type == "select-one" && is_mandatory == "1") {
                                if (form_element.value.split(" ").join('') == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }


                            if (form_element.type == "checkbox" && is_mandatory == "1") {

                                //for all mandatory
                                if (form_element.checked == false) {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }


                            if (form_element.type == "radio" && is_mandatory == "1") {
                                radio_arr = document.getElementsByName(form_element.name);


                                radio_checked = 0;
                                for (j = 0; j < radio_arr.length; j++) {
                                    if (radio_arr[j].checked == true) {
                                        radio_checked = 1;
                                    }
                                }

                                if (radio_checked == 0) {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }

                            }


                            if (form_element.type == "file") {
                                if (form_element.value.split(" ").join('') != "") {
                                    file_name = form_element.value.split(" ").join('');
                                    file_name_arr = file_name.split(".");
                                    ext = file_name_arr[file_name_arr.length - 1];

                                    if (ext.toLowerCase() != "doc" && ext.toLowerCase() != "docx" && ext.toLowerCase() != "pdf" && ext.toLowerCase() != "jpg" && ext.toLowerCase() != "jpeg" && ext.toLowerCase() != "png" && ext.toLowerCase() != "gif") {
                                        alert("Upload doc, pdf or image file only");
                                        return false;
                                    }
                                }
                            }
                        }


                        if (document.frm.elements[i].name.indexOf("email_recipient_additional_") > "-1") {

                            ques_id = document.frm.elements[i].name.split("[]").join('');
                            quest_id_temp = ques_id.split('email_recipient_additional_');
                            temp_ques_id = quest_id_temp[1];
                            if (document.getElementById('mandatory_' + temp_ques_id))
                                is_mandatory = document.getElementById('mandatory_' + temp_ques_id).value;

                            if (typeof(form_element) != 'undefined' && form_element.type == "text" && is_mandatory == "1") {
                                var ele_name = document.frm.elements[i].name;
                                if (document.getElementById(ele_name).value == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }
                        }

                        if (document.frm.elements[i].name.indexOf("email_recipient_additional_") > -1) {
                            ques_id = document.frm.elements[i].name.split("[]").join('');
                            quest_id_temp = ques_id.split('email_recipient_additional_');
                            temp_ques_id = quest_id_temp[1];
                            if (document.getElementById('email_mandatory_' + temp_ques_id))
                                is_mandatory = document.getElementById('email_mandatory_' + temp_ques_id).value;
                            if (is_mandatory == 1) {
                                var ele_name = document.frm.elements[i].name.replace("[]", '');
                                if (document.getElementById(ele_name).value == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }
                        }


                        if (document.frm.elements[i].name.indexOf("sub_ques_") > "-1") {

                            form_element = document.frm.elements[i];

                            ele_name = document.frm.elements[i].name;
                            ele_name_temp = ele_name.split('sub_ques_');

                            temp_question_id = ele_name_temp[1].replace('[]', '');
                            temp_question_id = temp_question_id.replace(/_row[0-9]*/ig, '');

                            if (document.getElementById('mandatory_' + temp_question_id))
                                is_mandatory = document.getElementById('mandatory_' + temp_question_id).value;

                            if (form_element.type == "text" && is_mandatory == "1") {
                                if (form_element.value.split(" ").join('') == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }
                        }
                        if (document.frm.elements[i].name.indexOf("sub_ques2_") > "-1") {

                            form_element = document.frm.elements[i];

                            ele_name = document.frm.elements[i].name;
                            ele_name_temp = ele_name.split('sub_ques2_');

                            temp_question_id = ele_name_temp[1].replace('[]', '');
                            temp_question_id = temp_question_id.replace(/_row[0-9]*/ig, '');

                            if (document.getElementById('mandatory_' + temp_question_id))
                                is_mandatory = document.getElementById('mandatory_' + temp_question_id).value;

                            if (form_element.type == "text" && is_mandatory == "1") {
                                if (form_element.value.split(" ").join('') == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }
                        }
                        if (document.frm.elements[i].name.indexOf("sub_ques3_") > "-1") {

                            form_element = document.frm.elements[i];

                            ele_name = document.frm.elements[i].name;
                            ele_name_temp = ele_name.split('sub_ques3_');

                            temp_question_id = ele_name_temp[1].replace('[]', '');
                            temp_question_id = temp_question_id.replace(/_row[0-9]*/ig, '');

                            if (document.getElementById('mandatory_' + temp_question_id))
                                is_mandatory = document.getElementById('mandatory_' + temp_question_id).value;

                            if (form_element.type == "text" && is_mandatory == "1") {
                                if (form_element.value.split(" ").join('') == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }
                        }

                        if (document.frm.elements[i].name.indexOf("sub_ques4_") > "-1") {

                            form_element = document.frm.elements[i];

                            ele_name = document.frm.elements[i].name;
                            ele_name_temp = ele_name.split('sub_ques4_');

                            temp_question_id = ele_name_temp[1].replace('[]', '');
                            temp_question_id = temp_question_id.replace(/_row[0-9]*/ig, '');

                            if (document.getElementById('mandatory_' + temp_question_id))
                                is_mandatory = document.getElementById('mandatory_' + temp_question_id).value;

                            if (form_element.type == "text" && is_mandatory == "1") {
                                if (form_element.value.split(" ").join('') == "") {
                                    alert("Please ensure all items marked with '*' are complete");
                                    return false;
                                }
                            }
                        }


                        if (document.frm.elements[i].name.indexOf("covid_question_") > '-1') {
                            var fieldname = document.frm.elements[i].name;
                            form_element = document.frm.elements[i];

                            if (form_element.value.split(" ").join('') == "") {
                                alert("Please ensure all items marked with '*' are complete");
                                return false;
                            }
                        }
                    }
                    $("#progress_bar_mobile").show();
                    $("#frm").submit();

                });


            });
        </script>
        <?php
        if ($form_submit_success != '') {
        ?>
            <script>
                $(function() {
                    $('#form-builder-dialog-box').show();
                    $("#dialog1").dialog({
                        autoOpen: true,
                        show: "blind",
                        draggable: false,
                        position: 'center',
                        show: 'fadeIn',
                        hide: {
                            effect: 'fadeIn',
                            duration: 200 //at your convenience
                        }

                    }).dialog("widget").find(".ui-dialog-title").hide();

                    $('#close_dialog').on('click', function() {
                        $('#form-builder-dialog-box').hide();
                        $("#dialog1").dialog("close");
                    });
                });
            </script>
        <?php
        } //end of if($form_submit_success!="")
    } //end of if($valid_in_activation_date_range=="1")
    else { //hide code number
        ?>
        <script>
            $(function() {
                $('.top-navigation').hide();
            });
        </script>
    <?php
    }
    ?>
</div> <!-- end of main_div_mobile -->

<script>
    //file upload control
    $(function() {

        date_curr = new Date();
        hours1 = date_curr.getHours();
        mins1 = date_curr.getMinutes();
        seconds1 = date_curr.getSeconds();

        random_str = hours1 + "" + mins1 + "" + seconds1;


        $("input:file").uniform();

        $(".auto_complete").autocomplete({
            source: "<?php echo url::base(); ?>formbuilder/get_participant_list/?profile_id=<?php echo $profile['id']; ?>&random_str=" + random_str,
            minLength: 1
        });


    });

    function selectChangeVehicleOptions(value, question_id, label_color = "000") {
        if (value == 'Vehicle') {
            $("#vehicle_no_" + question_id).html('<br><span style="color:#' + label_color + '">Vehicle Registration ID <span style="color:red">*</span></span></br><input class="input-field" type="text" name="covid_question_' + question_id + '[]" />')
        } else if (value == 'Other') {
            $("#vehicle_no_" + question_id).html('<br><span style="color:#' + label_color + '"> Please specify other <span style="color:red">*</span></span></br><input class="input-field" type="text" name="covid_question_' + question_id + '[]" />');
        } else {
            $("#vehicle_no_" + question_id).html('');
        }
    }

    function add_another_participant(question_id, question_text, include_sign, include_emp, is_mandatory, obj) {
        if (question_text != "") {
            que_text = question_text;
        } else {
            que_text = "Name";
        }
        if (is_mandatory == 1) {
            mandatory = '<span class="mandatory_field" style="display:inline;">*</span>';
        } else {
            mandatory = '';
        }
        date_curr = new Date();
        hours1 = date_curr.getHours();
        mins1 = date_curr.getMinutes();
        seconds1 = date_curr.getSeconds();
        random_str = hours1 + "" + mins1 + "" + seconds1;

        if (include_emp == '1') {
            sign_emp = 'Employer/Company' + mandatory + '<br><input type="text" name="parti_que_' + question_id + '[]" value="" /><br>';
        } else {
            sign_emp = "";
        }
        if (include_sign == '1') {
            sign_html = 'Signature<br>';
            sign_html += '<div class="sigPad" >';
            sign_html += '<div class="sig sigWrapper">';
            sign_html += '<div class="typed"></div>';
            sign_html += '<canvas class="pad" id="sign_' + random_str + '">Your browser does not support the HTML5 canvas tag.</canvas>';
            sign_html += '<input type="hidden" id="signature" name="sign_que_' + question_id + '[]" class="output">';
            sign_html += '</div>';
            sign_html += '<button class="button clear clearButton" data-action="clear"  type="button">Clear Signature</button>';
            sign_html += '</div>';
        } else {
            sign_html = "";
        }

        que_text += mandatory + '<br>';
        $(obj).before(que_text + '<input type="text" name="question_' + question_id + '[]" class="auto_complete text-box-black"><br>' + sign_emp + sign_html);

        if (include_sign == '1') {
            $("#sign_" + random_str).attr('height', 200);
            resizeCanvasWithId("sign_" + random_str);

            $('.sigPad').signaturePad({
                drawOnly: true
            });
        }

        $("input:file").uniform();

        $(".auto_complete").autocomplete({
            source: "<?php echo url::base(); ?>formbuilder/get_participant_list/?profile_id=<?php echo $profile['id']; ?>&random_str=" + random_str,
            minLength: 1
        });
    }

    function add_another_email_recipient(question_id, obj, is_mandatory) {
        $(obj).before(
            '<br><div class="rounded"><input style="width:90%" id="email_recipient_additional_' + question_id + '" class="email_recipient_additional" name="email_recipient_additional_' + question_id + '[]" type="text" data-field="' + is_mandatory + '"></div>'
        );
    }

    function add_another_hazard(question_id, question_text, obj, is_mandatory) {
        var question_id = question_id + 1;

        if (is_mandatory == 1) {
            mandatory = '<span class="mandatory_field" style="display:inline;">*</span>';
        } else {
            mandatory = '';
        }


        $(obj).before(
            '<br>' + question_text +
            '<input type="hidden" name="mandatory_' + question_id + '" id="mandatory_' + question_id + '" value="' + is_mandatory + '" >' +
            '<br><br>Task | Activity' + mandatory + '<br><textarea rows="1" class="hazard_textarea" name="question_' + question_id + '[]" ></textarea><br><br>' +
            'Potential Hazards' + mandatory + '<br><textarea rows="1" class="hazard_textarea" name="question_' + question_id + '[]" ></textarea><br><br>' +
            'Risk Score (Before)' + mandatory + '<br><select name="question_' + question_id + '[]" style="width:150px;"><option value="">Select</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select><br><div class="file-upload-control"><div class="uploader uploader-file hover"><input type="file" class="multiple_file_upload" name="hwms_question_' + question_id + '[]" multiple="" data-question_id="' + question_id + '"><span class="filename" style="user-select: none;">No file selected</span><span class="action" style="user-select: none;">Choose File</span></div></div><span class="row upload-img-row upload_file_preview_' + question_id + '"><input type="hidden" name="hwms_file_' + question_id + '[]"></span>' +
            '<br><br>Control Measures' + mandatory + '<br><textarea rows="1" class="hazard_textarea" name="question_' + question_id + '[]" ></textarea><br><br>' +
            'Risk Score (After)' + mandatory + '<br><select name="question_' + question_id + '[]" style="width:150px;"><option value="">Select</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select><br>'
        );
        $("select").uniform();

    }

    function add_another_file(question_id, obj) {
        $(obj).before('<input type="file" name="question_' + question_id + '[]"  />');
        $("input:file").uniform();
    }

    function add_another_signature(question_id, question_text, obj, include_name, include_phone, include_employer, include_email, is_mandatory) {
        if ($('.pad').length < 28) {
            date_curr = new Date();
            hours1 = date_curr.getHours();
            mins1 = date_curr.getMinutes();
            seconds1 = date_curr.getSeconds();

            if (is_mandatory == 1) {
                mandatory = '<span class="mandatory_field" style="display:inline;">*</span>';
            } else {
                mandatory = '';
            }

            if (include_name == '1') {
                name1 = 'Name' + mandatory + '<br><input type="text" name="sub_ques_' + question_id + '[]" value="" /><br>';
            } else {
                name1 = '';
            }
            if (include_employer == '1') {
                name2 = 'Employer/Company' + mandatory + '<br><input type="text" name="sub_ques2_' + question_id + '[]" value="" /><br>';
            } else {
                name2 = '';
            }
            if (include_email == '1') {
                name3 = 'Email' + mandatory + '<br><input type="text" name="sub_ques3_' + question_id + '[]" value="" /><br>';
            } else {
                name3 = '';
            }
            if (include_phone == '1') {
                name4 = 'Phone' + mandatory + '<br><input type="text" name="sub_ques4_' + question_id + '[]" value="" /><br>';
            } else {
                name4 = '';
            }

            random_str = hours1 + "" + mins1 + "" + seconds1;

            sign_html = '<div class="sigPad" >';
            sign_html += '            <div class="sig sigWrapper">';
            sign_html += '              <div class="typed"></div>';
            sign_html += '              <canvas class="pad" id="sign_' + random_str + '">Your browser does not support the HTML5 canvas tag.</canvas>';
            sign_html += '              <input type="hidden" id="signature" name="question_' + question_id + '[]" class="output">';
            sign_html += '            </div>';
            sign_html += '              <button class="button clear clearButton" data-action="clear"  type="button">Clear Signature</button>';
            sign_html += '</div>';

            $(obj).before(name1 + name2 + name3 + question_text + mandatory + '<br>' + sign_html);

            $("#sign_" + random_str).attr('height', 200);
            resizeCanvasWithId("sign_" + random_str);


            $('.sigPad').signaturePad({
                drawOnly: true
            });

        } else //max limit reached
            $('.add_another_sign').hide();
    }
</script>



<!-- google map for location function -->
<?php if (isset($location_fun_count) && $location_fun_count > 0) {
?>
    <div id="google_map_div" style="width:100%;display:none;">
        <div id="googleMap" style="width:100%;height:400px;"></div>
    </div>


    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBIlkNZMrMpvi0207s670Csx8DOP2pYsc"></script>
    <script>
        var cur_ele_id;
        var map;
        var lat_current;
        var lng_current;
        var myCenter;

        var marker;
        var infowindow;

        $(function() {
            $('.locate-me').on('click', function() {

                tmp = $(this).attr('id').split('locate_me_');
                inp_ele = 'question_' + tmp[1];
                cur_ele_id = inp_ele;

                //$('#google_map_div').css({'visibility':'visible','height':'100%'});
                $('#google_map_div').show();
                google.maps.event.trigger(map, 'resize');
                if (marker != null)
                    map.setCenter(marker.getPosition());
                else
                    map.setCenter(new google.maps.LatLng("-34.942043", "138.585168"));

                $('#main_div_mobile').hide();
                $('.BannerImage').hide();
                $('.top-navigation').hide();
            });
        });


        function map_initialize() {
            lat_current = document.getElementById('latitude').value;
            lng_current = document.getElementById('longitude').value;

            if (lat_current == "-34.942043" && lng_current == "138.585168") {
                zoom_level = 3;
            } else {
                zoom_level = 15;
            }

            myCenter = new google.maps.LatLng(lat_current, lng_current);

            var mapProp = {
                center: myCenter,
                zoom: zoom_level,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

            var remove_poi = [{
                "featureType": "poi",
                "elementType": "labels",
                "stylers": [{
                    "visibility": "off"
                }]
            }];

            map.setOptions({
                styles: remove_poi
            });

            google.maps.event.addListener(map, 'click', function(event) {
                placeMarker(event.latLng);
            });

            if (lat_current != "-34.942043" && lng_current != "138.585168")
                placeMarker(myCenter);

        }

        function placeMarker(location) {

            if (marker != null) {
                marker.setMap(null);
            }

            marker = new google.maps.Marker({
                position: location,
                map: map,
            });


            var myOptions = {
                disableAutoPan: false,
                alignBottom: true,
                zIndex: null,
                boxClass: "info-windows",
                pane: "floatPane",
                enableEventPropagation: false,
                infoBoxClearance: "10px",
                position: marker.position
            };

            infowindow = new google.maps.InfoWindow(myOptions);

            geocode(location);

        }

        function geocode(latlng) {
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                latLng: latlng
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {

                        currAddress = (getFullAddress(results[0])).replace(/^\s\s*/, '').replace(/\s\s*$/, ''); //to get full address
                        currAddress = "";
                        if (currAddress == "") //donot get full address then use component
                            currAddress = (getAddressComponent(results[0], 'street_number', false) + " " + getAddressComponent(results[0], 'route', false) + ", " + getAddressComponent(results[0], 'locality', false) + " " + getAddressComponent(results[0], 'administrative_area_level_1', true) + " " + getAddressComponent(results[0], 'postal_code', false)).replace(/^\s\s*/, '').replace(/\s\s*$/, '');

                        currLat = latlng.lat();
                        currLong = latlng.lng();

                        marker.setPosition(latlng);


                        currAddress1 = currAddress.replace("'", "\\'");

                        var content = '<div style="width:170px;height:150px;">';
                        content += '<b>' + currAddress.replace(",", "<br/>") + '</b><br/><a href="javascript: ;" onclick="select_location(\'' + currAddress1 + '\'); return false;">Select this location</a><br/>OR<br><a href="javascript: ;" onclick="return_mobile_form(); return false;">Return to mobile form</a>';
                        content += '</div>';


                        infowindow.setContent(content);
                        infowindow.open(map, marker);

                    }
                }
            });
        }

        function getAddressComponent(result, component, shortName) {
            var arrAddress = result.address_components;
            var returnvar;

            // iterate through address_component array
            for (var i = 0; i < arrAddress.length; i++)
                if (arrAddress[i].types[0] == component) {
                    if (shortName)
                        return arrAddress[i].short_name;
                    else
                        return arrAddress[i].long_name;
                }


            return '';
        }

        function getFullAddress(result) {
            return result.formatted_address;

        }

        function select_location(address) {
            $('#' + cur_ele_id).val(address);
            //$('#google_map_div').css({'visibility':'hidden','height':'0'});
            $('#google_map_div').hide();
            $('#main_div_mobile').show();
            $('.BannerImage').show();
            $('.top-navigation').show();
            $('#' + cur_ele_id).focus();
        }

        function return_mobile_form() {
            //$('#google_map_div').css({'visibility':'hidden','height':'0'});
            $('#google_map_div').hide();
            $('#main_div_mobile').show();
            $('.BannerImage').show();
            $('.top-navigation').show();
            $('#' + cur_ele_id).focus();
        }

        google.maps.event.addDomListener(window, 'resize', map_initialize);
        google.maps.event.addDomListener(window, 'load', map_initialize);
    </script>
<?php
} ?>
<!-- end of google map for location function -->

<?php //call analytic when page is loaded but not when form is submitted
if ($form_submit_success == '') {
?>
    <script>
        $(function() {
            $(this).wpanalytics("<?php echo $profile['analytic_key']; ?>");
        });
    </script>
<?php
} ?>
<?php
//below code needed if any signature panel exists
if ($signature_flag == '1') {
?>

    <script src="<?php echo URL::base(); ?>js/jquery.signaturepad.js"></script>
    <script>
        $(document).ready(function() {
            resizeCanvas();

            $('.sigPad').signaturePad({
                drawOnly: true
            });

            width = Math.ceil(($(window).innerWidth() * 86) / 100);


            $('.pad').attr('height', 200);
            // $('.pad').attr('width',width);

            $('#sign_width').val(width); //store width in hidden
            $('#p_sign_width').val(width); //store width in hidden

        });


        function resizeCanvas() {

            width = Math.ceil(($(window).innerWidth() * 86) / 100);

            $('.pad').each(function(index) {

                var c = document.getElementById($(this).attr('ID'));

                if (c.getContext) {
                    var ctx = c.getContext("2d");

                    var imgD = ctx.getImageData(0, 0, c.width - 1, c.height - 1);

                    //$('.pad').attr('width',width);
                    c.width = width;

                    setTimeout(function() {
                        ctx.putImageData(imgD, 0, 0);
                    }, 200);
                }
            });

            $('#sign_width').val(width); //store width in hidden
            $('#p_sign_width').val(width); //store width in hidden
        }

        function resizeCanvasWithId(obj_id) {

            width = Math.ceil(($(window).innerWidth() * 86) / 100);

            var c = document.getElementById(obj_id);

            if (c.getContext) {
                var ctx = c.getContext("2d");

                var imgD = ctx.getImageData(0, 0, c.width - 1, c.height - 1);

                $('#' + obj_id).attr('width', width);

                setTimeout(function() {
                    ctx.putImageData(imgD, 0, 0);
                }, 200);
            }

        }

        window.onresize = resizeCanvas;
    </script>
<?php
} ?>
<style>
    .upload-img-row {
        width: 100%;
        display: inline-block;
    }

    .upload-img-row span.pip {
        display: inline-block;
        position: relative;
        margin: 10px 10px 0 0;
    }

    .upload-img-row span.pip .remove_image {
        position: absolute;
        right: -5px;
        top: -5px;
        border-radius: 100%;
        background: #737373;
        width: 20px;
        height: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff !important;
        font-size: 12px;
    }

    .file-upload-control {
        position: relative;
    }

    div.file-upload-control span.filename {
        width: 172px;
        padding: 0;
    }

    .file-upload-control span.action {
        float: right !important;
        background-image: url('<?php echo URL::base(); ?>form-builder/images/camera.png') !important;
        height: 56px !important;
        width: 56px !important;
        background-position: top right !important;
        background-size: 75%;
        background-repeat: no-repeat;
        text-indent: -99999px !important;
        margin-top: 0;
        cursor: pointer;
    }

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

    .covid-checkin-header .covid-checkinform {
        padding: 15px;
    }

    .covid-checkin-header .covid-checkinform input {
        border: 1px solid #ddd;
        padding: 10px 10px 10px 40px;
        margin-bottom: 15px;
        width: calc(100% - 50px);
        border-radius: 6px;
        font-weight: normal;
        background-position: 10px center;
        background-repeat: no-repeat;
        background-size: 20px;
    }

    .covid-checkin-header .covid-checkinform .name {
        background-image: url("<?php echo URL::base(); ?>/images/user.png");
        background-repeat: no-repeat;
    }

    .covid-checkin-header .covid-checkinform .contact {
        background-image: url("<?php echo URL::base(); ?>/images/smartphone.png");
        background-repeat: no-repeat;
    }

    .covid-checkin-header .covid-checkinform .email {
        background-image: url("<?php echo URL::base(); ?>/images/email.png");
        background-repeat: no-repeat;
    }

    .covid-checkin-header .covid-checkinform .map {
        background-image: url("<?php echo URL::base(); ?>/images/placeholder.png");
        background-repeat: no-repeat;
    }

    .covid-checkin-header .covid-checkinform .btn {
        background-color: #808080;
        font-family: Arial, Helvetica, sans-serif;
        display: inline-block;
        color: #fff;
        padding: 10px 30px;
        border-radius: 6px;
    }

    .covid-checkinform .input-field {
        width: 90%;
        max-width: 90%;
    }
</style>