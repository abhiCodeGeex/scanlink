<?php

defined('SYSPATH') or die('No direct script access.');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/phpwkhtmltopdf/vendor/autoload.php';

use mikehaertl\wkhtmlto\Pdf;

class Controller_Formbuilder extends Controller_Template_Master
{
    public $clientlogin = false;
    public function before()
    {

        parent::before();

        if ($this->session->get('clientuserid')) :
            $this->clientlogin = true;
        endif;

        // Default Head view file
        $this->template->head = View::factory('template/form_builder_head');

        // Set the default Page Title
        $this->template->head->title = 'FormBuilder';

        // All CSS Stylesheets
        $this->template->head->stylesheets = array(array('href' => 'form-builder/css/style.css', 'media' => 'screen'), array('href' => 'form-builder/css/uniform.default.css', 'media' => 'screen'));

        // All JS Libraries and Scripts
        $this->template->head->javascripts = array('ckeditor/ckeditor.js', 'form-builder/js/jquery-1.9.1.js', 'form-builder/js/jquery-ui.js', 'form-builder/js/jquery.uniform.js', 'js/jscolor/jscolor.js');
    }

    public function action_index()
    {
        // Default Content template
        $profile_id = $_REQUEST["profile_id"];
        $enable_form_analytics = $_REQUEST["enable_form_analytics"];

        $recipient_email_arr = array();
        $form_questions_arr = array();
        $form_name = "";

        $profile_arr = Model::factory('profiles')->getProfileById($profile_id);

        $form_id = $profile_arr['form_id'];
        $form_name = $profile_arr['form_title'];
        $email_subject = $profile_arr['form_email_tag'];

        if ($form_id > 0) {
            $recipient_email_arr = Model::factory('Formbuilder')->form_builder_recipient_list($form_id);

            $form_questions_arr = Model::factory('Formbuilder')->get_profile_question($profile_id);
        }

        $question_type_0_arr = Model::factory('Formbuilder')->get_question_type(0);
        $question_type_1_arr = Model::factory('Formbuilder')->get_question_type(1);
        //$question_type_2_arr = Model::factory('Formbuilder')->get_question_type(2); //old one

        $question_type_image = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=11"); //for image
        $question_type_2_arr = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id!=11 and question_type_id!=16 and question_type_id!=17 and question_type_id!=18 and question_type_id!=19 and question_type_id!=20 and question_type_id!=21"); // exclude image,signature,file upload, participant name, location function, web link and document button

        $question_type_signature = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=16"); //for signature

        $question_type_file_upload = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=17"); //for file upload

        $question_type_participant = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=18"); //for participant name

        $question_type_location_fun = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=19"); //for location function

        $question_type_web_link = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=20"); //for web link

        $question_type_document_btn = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=21"); //for document button

        array_push($question_type_1_arr, $question_type_image[0], $question_type_signature[0], $question_type_file_upload[0], $question_type_participant[0], $question_type_location_fun[0], $question_type_web_link[0], $question_type_document_btn[0]);

        $this->template->currentPage = 'formbuilder';

        $this->template->content = View::factory('formbuilder/index')
            ->bind("client_login", $this->clientlogin)
            ->bind("question_type_0_arr", $question_type_0_arr)
            ->bind("question_type_1_arr", $question_type_1_arr)
            ->bind("question_type_2_arr", $question_type_2_arr)
            ->bind("profile_id", $profile_id)
            ->bind("recipient_email_arr", $recipient_email_arr)
            ->bind("form_questions_arr", $form_questions_arr)
            ->bind("form_name", $form_name)
            ->bind("email_subject", $email_subject)
            ->bind("enable_form_analytics", $enable_form_analytics);
    }

    public function action_survey_form()
    {
        // Default Content template
        $profile_id = $_REQUEST["profile_id"];
        $enable_form_analytics = $_REQUEST["enable_form_analytics"];

        $recipient_email_arr = array();
        $form_questions_arr = array();
        $form_name = "";

        $profile_arr = Model::factory('profiles')->getProfileById($profile_id);

        $form_id = $profile_arr['form_id'];
        $form_name = $profile_arr['form_title'];
        $email_subject = $profile_arr['form_email_tag'];


        if ($form_id > 0) {
            $recipient_email_arr = Model::factory('Formbuilder')->form_builder_recipient_list($form_id);

            $form_questions_arr = Model::factory('Formbuilder')->get_profile_question($profile_id);
        }

        $question_type_0_arr = Model::factory('Formbuilder')->get_question_type(0);
        $question_type_1_arr = Model::factory('Formbuilder')->get_question_type(1);
        //$question_type_2_arr = Model::factory('Formbuilder')->get_question_type(2); //old one

        $question_type_image = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=11"); //for image
        $question_type_2_arr = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id!=11 and question_type_id!=16 and question_type_id!=17 and question_type_id!=18 and question_type_id!=19 and question_type_id!=20 and question_type_id!=21"); // exclude image,signature,file upload, participant name, location function, web link and document button

        $question_type_signature = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=16"); //for signature

        $question_type_file_upload = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=17"); //for file upload

        $question_type_participant = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=18"); //for participant name

        $question_type_location_fun = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=19"); //for location function

        $question_type_web_link = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=20"); //for web link

        $question_type_document_btn = Model::factory('Formbuilder')->get_question_type(2, " and question_type_id=21"); //for document button

        array_push($question_type_1_arr, $question_type_image[0], $question_type_signature[0], $question_type_file_upload[0], $question_type_participant[0], $question_type_location_fun[0], $question_type_web_link[0], $question_type_document_btn[0]);

        $this->template->currentPage = 'formbuilder';
        $this->template->content = View::factory('formbuilder/survey_form')
            ->bind("client_login", $this->clientlogin)
            ->bind("question_type_0_arr", $question_type_0_arr)
            ->bind("question_type_1_arr", $question_type_1_arr)
            ->bind("question_type_2_arr", $question_type_2_arr)
            ->bind("profile_id", $profile_id)
            ->bind("recipient_email_arr", $recipient_email_arr)
            ->bind("recipient_email_arr", $recipient_email_arr)
            ->bind("form_questions_arr", $form_questions_arr)
            ->bind("form_name", $form_name)
            ->bind("email_subject", $email_subject)
            ->bind("enable_form_analytics", $enable_form_analytics);
    }

    public function action_other_user_form()
    {
        // Default Content template

        // All CSS Stylesheets
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'));

        // All JS Libraries and Scripts
        $this->template->head->javascripts = array('js/jquery.min.js', 'js/jquery.validate.min.js', 'js/jquery.colorbox.js', 'js/jquery.autogrow-textarea.js', 'js/coin-slider.min.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', 'js/jscolor/jscolor.js', 'js/jquery.maxlength-min.js', 'js/deleteLogo.js', 'js/profile_page_common.js');

        $user_id = $_REQUEST["user_id"];
        $profile_id = $_REQUEST["profile_id"];

        $form_library_arr = Model::factory('Formbuilder')->get_library_forms_other_user($user_id);

        $this->template->content = View::factory('template/library_forms_other_user')
            ->bind('form_library_arr', $form_library_arr)
            ->bind('profile_id', $profile_id);
    }

    public function action_active_formbuilder()
    {
        $this->session->set('code_amount', "1");
        $this->session->set('per_code_amount', "5");
        $this->session->set('form_builder_profile_id', $_REQUEST["profile_id"]);
        //$this->session->set('form_builder_referer',$this->request->referrer());
        $this->session->set('form_builder_referer', $_REQUEST["current_url"]);

        $this->request->redirect('profile/billing_for_formbuilder');
    }

    public function action_reorder_element()
    {
        $profile_id = $_REQUEST["profile_id"];
        $current_ele_id = $_REQUEST["current_ele_id"];
        $next_ele_id = isset($_REQUEST["next_ele_id"]) ? $_REQUEST["next_ele_id"] : '';

        $current_ele_question_arr = explode("question_div_", $current_ele_id);

        $current_ele_question_id = "";
        if (count($current_ele_question_arr) > 1) {
            $current_ele_question_id = $current_ele_question_arr[1];
        } elseif (count($current_ele_question_arr) == 1) {
            $current_ele_question_id = $current_ele_question_arr[0];
        }

        if ($next_ele_id != "") {
            $next_ele_question_arr = explode("question_div_", $next_ele_id);

            $next_ele_question_id = "";
            if (count($next_ele_question_arr) > 1) {
                $next_ele_question_id = $next_ele_question_arr[1];
            } elseif (count($next_ele_question_arr) == 1) {
                $next_ele_question_id = $next_ele_question_arr[0];
            }
        } else {
            $next_ele_question_id = 0;
        }

        if ($current_ele_question_id != "") {

            $current_ele_question_order_arr = Model::factory('formbuilder')->get_order_of_question($current_ele_question_id);
            if ($current_ele_question_order_arr) {
                $current_ele_question_order = $current_ele_question_order_arr["question_order"];
            }

            if ($next_ele_question_id > 0) {
                $next_ele_question_order_arr = Model::factory('formbuilder')->get_order_of_question($next_ele_question_id);
                if ($next_ele_question_order_arr) {
                    $next_ele_question_order = $next_ele_question_order_arr["question_order"];
                }
            } else {
                $next_ele_question_order = 0;
            }

            if ($next_ele_question_order != 0 && $current_ele_question_order > $next_ele_question_order) {
                $current_ele_question_order_new = $next_ele_question_order; //temp

                $qry = "UPDATE `form_builder_question` SET question_order=question_order+1 WHERE question_order >='" . $next_ele_question_order . "' and question_order <'" . $current_ele_question_order . "'  and profile_id ='" . $profile_id . "'";

                Model::factory('formbuilder')->update_order_of_question($qry);

                $qry = "UPDATE `form_builder_question` SET question_order='" . $current_ele_question_order_new . "' WHERE question_id='" . $current_ele_question_id . "' and profile_id ='" . $profile_id . "'";
                Model::factory('formbuilder')->update_order_of_question($qry);
            }

            if ($next_ele_question_order != 0 && $current_ele_question_order < $next_ele_question_order) {
                $current_ele_question_order_new = $next_ele_question_order - 1; //temp

                $qry = "UPDATE `form_builder_question` SET question_order=question_order-1 WHERE question_order <'" . $next_ele_question_order . "' and question_order >'" . $current_ele_question_order . "'  and profile_id ='" . $profile_id . "'";

                Model::factory('formbuilder')->update_order_of_question($qry);

                $qry = "UPDATE `form_builder_question` SET question_order='" . $current_ele_question_order_new . "' WHERE question_id='" . $current_ele_question_id . "' and profile_id ='" . $profile_id . "'";
                Model::factory('formbuilder')->update_order_of_question($qry);
            }

            if ($next_ele_question_order == 0) {
                $max_order = Model::factory('formbuilder')->get_max_order($profile_id);

                $current_ele_question_order_new = $max_order["max_order"];
                if ($max_order["max_order"] == 0) {
                    $current_ele_question_order_new = 1;
                }

                $qry = "UPDATE `form_builder_question` SET question_order=question_order-1 WHERE question_order >'" . $current_ele_question_order . "'  and profile_id ='" . $profile_id . "'";

                Model::factory('formbuilder')->update_order_of_question($qry);

                $qry = "UPDATE `form_builder_question` SET question_order='" . $current_ele_question_order_new . "' WHERE question_id='" . $current_ele_question_id . "' and profile_id ='" . $profile_id . "'";
                Model::factory('formbuilder')->update_order_of_question($qry);
            }
        }
        exit;
    }

    public function action_render_element()
    {
        $form_name = $_REQUEST["form_name"];
        $email_arr = explode(",", $_REQUEST["email_arr"]);
        $enable_form = $_REQUEST["enable_form"];

        $profile_id = $_REQUEST["profile_id"];
        $mandatory_field = $_REQUEST["mandatory_field"];
        $user_id = $this->session->get('clientuserid');

        $include_name = $_REQUEST["include_name"];
        $include_employer = $_REQUEST["include_employer"];
        $include_email = $_REQUEST["include_email"];
        $include_phone = $_REQUEST["include_phone"];

        $covid_check_in_fullname = !empty($_REQUEST['covid_fullname']) ? $_REQUEST['covid_fullname'] : '';
        $covid_check_in_contact = !empty($_REQUEST['covid_contact']) ? $_REQUEST['covid_contact'] : '';
        $covid_check_in_email = !empty($_REQUEST['covid_email']) ? $_REQUEST['covid_email'] : '';
        $covid_check_in_location = !empty($_REQUEST['covid_location']) ? $_REQUEST['covid_location'] : '';

        $covid_bg_color = !empty($_REQUEST['covid_bg_color']) ? $_REQUEST['covid_bg_color'] : '';
        $covid_text_color = !empty($_REQUEST['covid_text_color']) ? $_REQUEST['covid_text_color'] : '';

        $participant_include_signature = $_REQUEST['participant_include_signature'];
        $participant_include_employer = $_REQUEST['participant_include_employer'];

        $is_logchecked = isset($_REQUEST['is_logchecked']) ? $_REQUEST['is_logchecked'] : '';
        $log_columntitle = isset($_REQUEST['log_columntitle']) ? $_REQUEST['log_columntitle'] : '';

        if ($mandatory_field == "true") {
            $mandatory_field = 1;
        } else {
            $mandatory_field = 0;
        }

        //current form id
        $form_id = Model::factory('Formbuilder')->get_form_id($profile_id);

        $form_id_current = $form_id[0]["form_id"];

        if ($form_id_current == 0) {
            //get maximum form id
            $new_form_id = Model::factory('Formbuilder')->get_form_id_max();

            //add entry in form builder library
            Model::factory('Formbuilder')->form_builder_library_add($new_form_id, $user_id, $form_name);

            //update form fields
            $datafill = array();
            $datafill['form_id'] = $new_form_id;
            $datafill['form_title'] = $form_name;
            $datafill['form_is_enable'] = $enable_form;
            Model::factory('profiles')->updateProfile($profile_id, $datafill);

            $form_id_current = $new_form_id;
        } else {
            $datafill = array();
            $datafill['form_title'] = $form_name;
            $datafill['form_is_enable'] = $enable_form;
            Model::factory('profiles')->updateProfile($profile_id, $datafill);
        }


        // remove old email recipient
        Model::factory('Formbuilder')->form_builder_recipient_delete($form_id_current);
        for ($i = 0; $i < count($email_arr); $i++) {
            Model::factory('Formbuilder')->form_builder_recipient_add($form_id_current, $email_arr[$i]);
        }

        // now entries for question
        $question_type_id = $_REQUEST["question_type_id"];
        $question_id = 0;

        $user_input = $_REQUEST["user_input"];

        if (isset($_REQUEST["question_id"]) && trim($_REQUEST["question_id"])) {
            $question_id = $_REQUEST["question_id"];
        }

        $scale_from = $_REQUEST["scale_from"];
        $scale_to = $_REQUEST["scale_to"];

        $row_text = explode(",", $_REQUEST["row_text"]);
        $column_text = explode(",", $_REQUEST["column_text"]);

        $image_title = $_REQUEST["image_title"];
        $image_align = $_REQUEST["image_align"];

        $button_link_url = $_REQUEST["button_link_url"];
        $button_colour = $_REQUEST["button_colour"];
        $doc_title = $_REQUEST["doc_title"];

        $form_builder_image_temp = $this->session->get('form_builder_image_temp');
        $form_builder_doc_temp = $this->session->get('form_builder_doc_temp');
        $form_builder_doc_multi_temp = $this->session->get('form_builder_doc_multi_temp');

        //if($question_type_id=="18") //i.e. participant name
        //$mandatory_field = 1;


        // save data
        if ($question_id > 0) //edit mode
        {
            if (($question_type_id >= 1 && $question_type_id <= 9 && $question_type_id != 2) || $question_type_id == 15 || $question_type_id == 23) // Text Field
            {

                $datafill = array();
                $datafill['is_logchecked'] = $is_logchecked;
                $datafill['log_columntitle'] = $log_columntitle;
                $datafill['is_mandatory'] = $mandatory_field;
                $datafill['question_text'] = $user_input;

                Model::factory('Formbuilder')->form_builder_question_update($question_id, $datafill);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            }
            if ($question_type_id == 11) // for image type
            {
                $datafill = array();

                if ($form_builder_image_temp != "") {
                    $datafill['question_text'] = $form_builder_image_temp;
                } else //get current data
                {
                    $question_obj = Model::factory('Formbuilder')->get_question_by_question_id($question_id);

                    if (count($question_obj) > 0) {
                        $form_builder_image_temp = $question_obj[0]["question_text"];
                    }
                }

                $datafill['image_title'] = $image_title;
                $datafill['image_align'] = $image_align;

                Model::factory('Formbuilder')->form_builder_question_update($question_id, $datafill);

                $this->session->delete('form_builder_image_temp');
                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 20) // web link button
            {
                $datafill = array();
                $datafill['question_text'] = $user_input;
                $datafill['button_link_url'] = $button_link_url;
                $datafill['button_colour'] = $button_colour;

                Model::factory('Formbuilder')->form_builder_question_update($question_id, $datafill);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 21) // Document Button
            {
                $datafill = array();

                if ($form_builder_doc_temp != "") {
                    $datafill['question_text'] = $form_builder_doc_temp;
                } else //get current data
                {
                    $question_obj = Model::factory('Formbuilder')->get_question_by_question_id($question_id);

                    if (count($question_obj) > 0) {
                        $form_builder_doc_temp = $question_obj[0]["question_text"];
                    }
                }

                $datafill['doc_title'] = $doc_title;
                $datafill['button_colour'] = $button_colour;

                Model::factory('Formbuilder')->form_builder_question_update($question_id, $datafill);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 16) // Signature Panel
            {
                $datafill = array();
                $datafill['question_text'] = $user_input;
                $datafill['include_name'] = $include_name;
                $datafill['include_employer'] = $include_employer;
                $datafill['include_email'] = $include_email;
                $datafill['include_phone'] = $include_phone;
                $datafill['is_mandatory'] = $mandatory_field;

                Model::factory('Formbuilder')->form_builder_question_update($question_id, $datafill);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 18) // Participant name
            {
                $datafill = array();
                $datafill['question_text'] = $user_input;
                $datafill['participant_include_signature'] = $participant_include_signature;
                $datafill['participant_include_employer'] = $participant_include_employer;
                $datafill['is_mandatory'] = $mandatory_field;

                Model::factory('Formbuilder')->form_builder_question_update($question_id, $datafill);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 23) // Document Menu
            {
                $datafill = array();

                if ($form_builder_doc_multi_temp != "") {
                    $datafill['question_text'] = $form_builder_doc_multi_temp;
                } else //get current data
                {
                    $question_obj = Model::factory('Formbuilder')->get_question_by_question_id($question_id);

                    if (count($question_obj) > 0) {
                        $form_builder_doc_multi_temp = $question_obj[0]["question_text"];
                    }
                }

                $datafill['is_mandatory'] = $mandatory_field;
                $datafill['doc_title'] = $doc_title;

                Model::factory('Formbuilder')->form_builder_question_update($question_id, $datafill);

                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 25) {
                $datafill = array();


                // $datafill['covid_check_in_fullname'] = $covid_check_in_fullname;
                // $datafill['covid_check_in_contact'] = $covid_check_in_contact;
                // $datafill['covid_check_in_email'] = $covid_check_in_email;
                // $datafill['covid_check_in_location'] = $covid_check_in_location;


                $covid_checkin_form['covid_bg_color'] = $covid_bg_color;
                $covid_checkin_form['is_logchecked'] = $is_logchecked;
                $covid_checkin_form['covid_text_color'] = $covid_text_color;
                $covid_checkin_form['question_text'] = $user_input;

                Model::factory('Formbuilder')->form_builder_question_update($question_id, $covid_checkin_form);
            } else {
                $datafill = array();
                $datafill['question_text'] = $user_input;
                $datafill['is_mandatory'] = $mandatory_field;
                $datafill['is_logchecked'] = $is_logchecked;
                $datafill['log_columntitle'] = $log_columntitle;

                Model::factory('Formbuilder')->form_builder_question_update($question_id, $datafill);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            }
        } else // add mode
        {
            if ($question_type_id == 1) // Text Field
            {
                $question_id = Model::factory('Formbuilder')->form_builder_question_save($profile_id, $user_input, $question_type_id, '', '', $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id_current, 0, 0, 0, 0, 0, $is_logchecked, $log_columntitle);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 11) // for image type
            {
                $question_id = Model::factory('Formbuilder')->form_builder_question_save($profile_id, $form_builder_image_temp, $question_type_id, $image_title, $image_align, $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id_current);

                $this->session->delete('form_builder_image_temp');
                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 21) // Document Button
            {
                $question_id = Model::factory('Formbuilder')->form_builder_question_save($profile_id, $form_builder_doc_temp, $question_type_id, '', '', $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id_current);

                $this->session->delete('form_builder_image_temp');
                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 16) // Signature Panel
            {
                $question_id = Model::factory('Formbuilder')->form_builder_question_save($profile_id, $user_input, $question_type_id, '', '', $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id_current, $include_name, $include_employer, $include_email, $_REQUEST['include_phone']);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 18) // Participant name
            {
                $question_id = Model::factory('Formbuilder')->form_builder_question_save($profile_id, $user_input, $question_type_id, '', '', $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id_current, 0, 0, 0, $participant_include_signature, $participant_include_employer);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 23) // Document Menu
            {
                $question_id = Model::factory('Formbuilder')->form_builder_question_save($profile_id, $form_builder_doc_multi_temp, $question_type_id, '', '', $mandatory_field, $button_link_url, '', $doc_title, $form_id_current);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
            } else if ($question_type_id == 25) {
                $covid_checkin_form = array();

                // $covid_checkin_form['covid_check_in_fullname'] = $covid_check_in_fullname;
                // $covid_checkin_form['covid_check_in_contact'] = $covid_check_in_contact;
                // $covid_checkin_form['covid_check_in_email'] = $covid_check_in_email;
                // $covid_checkin_form['covid_check_in_location'] = $covid_check_in_location;

                $covid_checkin_form['is_logchecked'] = $is_logchecked;
                $covid_checkin_form['covid_bg_color'] = $covid_bg_color;
                $covid_checkin_form['covid_text_color'] = $covid_text_color;

                $question_id = Model::factory('Formbuilder')->form_builder_question_save($profile_id, $user_input, $question_type_id, '', '', $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id_current, 0, 0, 0, $participant_include_signature, $participant_include_employer, 0, 0, 0, $covid_checkin_form);
            } else {
                $question_id = Model::factory('Formbuilder')->form_builder_question_save($profile_id, $user_input, $question_type_id, '', '', $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id_current);
            }
        }

        switch ($question_type_id) {
            case "3": // Mutliple choise

                //remove old options
                Model::factory('Formbuilder')->form_builder_question_options_remove($question_id);

                $question_option_type_id = 0; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                $option_text = explode(":::", $user_input);
                for ($i = 0; $i < count($option_text); $i++) {
                    Model::factory('Formbuilder')->form_builder_question_option_add($question_id, $option_text[$i], $question_option_type_id);
                }

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
                break;
            case "4": // Checkboxes

                //remove old options
                Model::factory('Formbuilder')->form_builder_question_options_remove($question_id);

                $question_option_type_id = 0; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                $option_text = explode(":::", $user_input);
                for ($i = 0; $i < count($option_text); $i++) {
                    Model::factory('Formbuilder')->form_builder_question_option_add($question_id, $option_text[$i], $question_option_type_id);
                }
                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
                break;
            case "5": // Drop Down Menu

                //remove old options
                Model::factory('Formbuilder')->form_builder_question_options_remove($question_id);

                $question_option_type_id = 0; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                $option_text = explode(":::", $user_input);
                for ($i = 0; $i < count($option_text); $i++) {
                    Model::factory('Formbuilder')->form_builder_question_option_add($question_id, $option_text[$i], $question_option_type_id);
                }
                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');
                break;

            case "6": // Number Scale

                //remove old options
                Model::factory('Formbuilder')->form_builder_question_options_remove($question_id);

                $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                Model::factory('Formbuilder')->form_builder_question_option_add($question_id, $scale_from, $question_option_type_id);

                $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                Model::factory('Formbuilder')->form_builder_question_option_add($question_id, $scale_to, $question_option_type_id);

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');

                break;

            case "7": // Grid

                //remove old options
                Model::factory('Formbuilder')->form_builder_question_options_remove($question_id);

                $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                for ($i = 0; $i < count($row_text); $i++) {
                    Model::factory('Formbuilder')->form_builder_question_option_add($question_id, $row_text[$i], $question_option_type_id);
                }

                $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                for ($i = 0; $i < count($column_text); $i++) {
                    Model::factory('Formbuilder')->form_builder_question_option_add($question_id, $column_text[$i], $question_option_type_id);
                }

                $this->session->delete('form_builder_doc_temp');
                $this->session->delete('form_builder_doc_multi_temp');

                break;
        }

        // end save data

        $this->template = View::factory('form_builder_control')
            ->bind("form_builder_image_temp", $form_builder_image_temp)
            ->bind("question_type_id", $question_type_id)
            ->bind("user_input", $user_input)
            ->bind("scale_from", $scale_from)
            ->bind("scale_to", $scale_to)
            ->bind("row_text", $row_text)
            ->bind("column_text", $column_text)
            ->bind("image_title", $image_title)
            ->bind("image_align", $image_align)
            ->bind("question_id", $question_id)
            ->bind("button_link_url", $button_link_url)
            ->bind("button_colour", $button_colour)
            ->bind("doc_title", $doc_title)
            ->bind("form_builder_doc_multi_temp", $form_builder_doc_multi_temp)
            ->bind("include_name", $include_name)
            ->bind("include_employer", $include_employer)
            ->bind("include_email", $include_email)
            ->bind("include_phone", $include_phone)
            ->bind("participant_include_signature", $participant_include_signature)
            ->bind("participant_include_employer", $participant_include_employer)
            ->bind("covid_bg_color", $covid_bg_color)
            ->bind("covid_text_color", $covid_text_color);
        // ->bind("covid_check_in_email", $covid_check_in_email)
        // ->bind("covid_check_in_location", $covid_check_in_location);
    }

    public function action_remove_element()
    {
        $question_id = $_REQUEST["question_id"];

        Model::factory('Formbuilder')->form_builder_question_remove($question_id);
    }

    public function action_get_participant_list()
    {
        $term = $_REQUEST["term"];
        $profile_id = $_REQUEST["profile_id"];

        $participant_arr = Model::factory('formbuilder')->get_participant_list_with_cond(" name like '" . $term . "%' and profile_id='" . $profile_id . "' ");

        echo '[';
        $str = "";
        if (count($participant_arr) > 0) {
            foreach ($participant_arr as $participant) {
                if ($str == "") {
                    $str = '"' . $participant["name"] . '"';
                } else {
                    $str .= ',"' . $participant["name"] . '"';
                }
            }
        }
        echo $str;
        echo ']';

        exit;
    }

    public function action_get_data_for_form_preview()
    {
        $form_id = $_REQUEST["form_id"];
        $questions_arr = Model::factory('Formbuilder')->get_profile_question_by_form_id($form_id);

        if (count($questions_arr) > 0) {
            foreach ($questions_arr as $questions) {
                $question_id = $questions["question_id"];
                $question_text = $questions["question_text"];
                $is_mandatory = $questions["is_mandatory"];
                $button_link_url = $questions["button_link_url"];
                $button_colour = $questions["button_colour"];
                $doc_title = $questions["doc_title"];
                $include_name = $questions["include_name"];
                $include_employer = $questions["include_employer"];
                $include_email = $questions["include_email"];
                $include_phone = $questions["include_phone"];

                switch ($questions["question_type_id"]) {
                    case "1": // Text Field
                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        echo '<input type="text" name="question_' . $question_id . '" class="text-box-black"><br><br>';
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        break;
                    case "2": // Text

                        echo $question_text;
                        //echo '<input type="hidden" name="questions[]" value="'.$question_id.'" class="text-box-black">';
                        break;
                    case "3": // Mutliple choise

                        $question_options = Model_formbuilder::get_question_option($question_id);

                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        foreach ($question_options as $question_option) {
                            $question_option_text = $question_option["option_name"];
                            echo "<input type='radio' name='question_" . $question_id . "' value='" . htmlentities($question_option_text, ENT_QUOTES) . "' />&nbsp;" . $question_option_text . "<br>";
                        }
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        echo "<br>";
                        break;
                    case "4": // Checkboxes

                        $question_options = Model_formbuilder::get_question_option($question_id);
                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        foreach ($question_options as $question_option) {
                            $question_option_text = $question_option["option_name"];
                            echo "<input type='checkbox' name='question_" . $question_id . "[]' value='" . htmlentities($question_option_text, ENT_QUOTES) . "' />&nbsp;" . $question_option_text . "<br>";
                        }
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        echo "<br>";
                        break;
                    case "5": // Drop Down Menu
                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        echo "<select name='question_" . $question_id . "' style='width:150px;'>";
                        echo "<option value=''>Select</option>";

                        $question_options = Model_formbuilder::get_question_option($question_id);

                        foreach ($question_options as $question_option) {
                            $question_option_text = $question_option["option_name"];
                            echo "<option value='" . htmlentities($question_option_text, ENT_QUOTES) . "'>" . $question_option_text . "</option>";
                        }

                        echo "</select>";

                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        echo "<br><br>";

                        break;
                    case "6": // Number Scale
                        $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                        $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                        $scale_from = $question_options[0]["option_name"];

                        $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                        $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                        $scale_to = $question_options[0]["option_name"];

                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        for ($i = $scale_from; $i <= $scale_to; $i++) {
                            echo "<input type='radio' name='question_" . $question_id . "' value='" . $i . "' />&nbsp;" . $i . "<br>";
                        }
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        echo "<br>";

                        break;
                    case "7": // Grid

                        $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                        $row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                        $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                        $column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

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
                                echo "<td  align='center'><input type='radio' name='question_" . $question_id . "_row" . $i . "' value='" . $j . "' /></td>";
                            }

                            echo "</tr>";
                        }

                        echo "</table>";
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        echo "<br>";

                        break;
                    case "8": // Date
                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        echo '<input type="text" class="datepicker dtpicker_date" id="dtpicker_date" name="question_' . $question_id . '" placeholder="Select Date" ><br><br>';
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        break;
                    case "9": // time
                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br>';

                        echo '<input type="text" class="datepicker dtpicker_time" id="dtpicker_time"  name="question_' . $question_id . '" placeholder="Select Time" ><br><br>';
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        break;
                    case "10": // heading
                        echo "<br><h1 style='font-size:20px;margin:0;'>" . $question_text . "</h1>";
                        break;
                    case "11": // image

                        if ($question_text != "") {
                            echo '<br><div align="' . $questions["image_align"] . '"><img src="' . URL::base() . 'images/form_builder_uploaded_images/' . $question_text . '"  style="max-height: 200px;max-width:250px;" title="' . $questions["image_title"] . '" ></div><br>';
                        }
                        echo "<br>";
                        break;
                    case "12": // sub heading
                        echo "<br><h3  style='font-size:16px;margin:0;'>" . $question_text . "</h3>";
                        break;
                    case "13": // Line Divider
                        echo "<br><hr>";
                        break;
                    case "14": // Blank Space
                        echo "<br><br>";
                        break;
                    case "15": // comments
                        echo '<br><textarea name="question_' . $question_id . '"  rows="3" class="text-box-black"></textarea>';

                        echo "<br><br>";
                        break;
                    case "16": // Signature

                        $signature_flag = 1;
                        echo '<br>';

                        if ($include_name == '1') {
                            echo 'Name<br><input type="text" name="sub_ques_' . $question_id . '[]" value="" /><br>';
                        }
                        if ($include_employer == '1') {
                            echo 'Employer/Company<br><input type="text" name="sub_ques2_' . $question_id . '[]" value="" /><br>';
                        }
                        if ($include_email == '1') {
                            echo 'Email<br><input type="text" name="sub_ques3_' . $question_id . '[]" value="" /><br>';
                        }
                        if ($question_text != "") {
                            echo $question_text . "<br>";
                        }
                        if ($include_phone == '1') {
                            echo 'Phone<br><input type="text" name="sub_ques4_' . $question_id . '[]" value="" /><br>';
                        }
                        if ($question_text != "") {
                            echo $question_text . "<br>";
                        }

                        /*echo '<div id="signature-pad" class="m-signature-pad">
                        <div class="m-signature-pad--body">
                        <canvas class="canvas_sign" height="100" >Your browser does not support the HTML5 canvas tag.</canvas>
                        </div>
                        <button class="button clear" data-action="clear" type="button">Clear Signature</button>
                        <input type="hidden" name="question_'.$question_id.'" id="signature" value="" />
                        </div>';*/

                        echo '<iframe id="iframe_sign_question_' . $question_id . '" src="' . URL::base() . 'signature_iframe.php?question_id=' . $question_id . '" scrolling="no" width="100%" height="250"  marginwidth="0" frameborder="no"></iframe>';
                        echo '<div align="right" class="add_another_file  add_another_sign" >Add Another</div>';
                        echo '<input type="hidden" name="question_' . $question_id . '" id="signature" value="" />';
                        echo '<input type="hidden" name="sign_width_' . $question_id . '" id="sign_width_' . $question_id . '" value="" />';
                        echo "<br><br>";

                        break;

                    case "17": // file upload

                        echo '<br>';

                        if ($question_text != "") {
                            echo $question_text . "<br>";
                        }

                        echo '<input type="file" name="question_' . $question_id . '[]"  />';
                        echo '<div align="right" class="add_another_file" >Add Another</div>';

                        echo "<br><br>";
                        break;

                    case "18": // participant name

                        if ($question_text != "") {
                            echo $question_text;
                        } else {
                            echo 'Name';
                        }

                        echo '<span class="mandatory_field" style="display:inline;">*</span><br>';

                        echo '<input type="text" name="question_' . $question_id . '" class="auto_complete text-box-black"><br><br>';
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';

                        break;
                    case "19": // location function

                        if ($question_text != "") {
                            echo $question_text;
                        }

                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span>';
                        }

                        echo '<br><input type="text" id="question_' . $question_id . '" name="question_' . $question_id . '" style="width:90%" class="text-box-black"><br>';
                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        echo '<div id="locate_me_' . $question_id . '" class="locate-me" >Locate Me</div>';

                        break;
                    case "20": //web link button

                        echo '<br><a href="' . $button_link_url . '" target="_blank">';
                        echo '<div style="background-color:#' . $button_colour . ';" class="web_link_button" >' . $question_text . '</div>';
                        echo '</a>';

                        break;

                    case "21": //Document Button

                        echo '<br><a href="' . URL::base() . 'images/form_builder_uploaded_images/' . $question_text . '" target="_blank">';
                        echo '<div style="background-color:#' . $button_colour . ';" class="web_link_button" >' . $doc_title . '</div>';
                        echo '</a>';

                        break;

                    case "22": // SWMS Hazard/Risk
                        if ($question_text != "") {
                            echo '<br>' . $question_text;
                        }

                        if ($is_mandatory == "1") {
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
                        echo '</select>';
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
                        echo '<div align="right" class="add_another_file" onclick="javascript:add_another_hazard(' . $question_id . ',&quot;' . $question_text . '&quot;,this,' . $is_mandatory . ')">Add Another</div>';

                        break;

                    case "23": //document menu

                        $select_value = explode(",", $question_text);

                        $select_txt = explode(",", $doc_title);

                        if ($is_mandatory == "1") {
                            echo '<span class="mandatory_field" style="display:inline;">*</span><br>';
                        } else {
                            echo '<br>';
                        }

                        echo '<input type="hidden" name="mandatory_' . $question_id . '" id="mandatory_' . $question_id . '" value="' . $is_mandatory . '" >';
                        echo '<select name="question_' . $question_id . '[]" style="width:150px;" multiple="">';
                        echo '<option value="" disabled="">Select Attachment</option>';
                        $i = 0;
                        foreach ($select_value as $value) {
                            echo '<option value="' . $value . '">' . $select_txt[$i] . '</option>';
                            $i++;
                        }
                        echo '</select><br>';

                        break;
                } //end switch case
            } //end for each
        }
        exit;
    }

    public function action_update_enable_form()
    {
        $profile_id = $_REQUEST["profile_id"];
        $enable_form = $_REQUEST["enable_form"];

        Model::factory('Formbuilder')->form_builder_profile_enable_form_update($profile_id, $enable_form);
    }

    public function action_add_existing_form()
    {
        $current_profile_id = $_REQUEST["current_profile_id"];
        $form_profile_id = $_REQUEST["form_profile_id"];

        $questions_arr = Model::factory('Formbuilder')->get_profile_question($form_profile_id);
        $form_id = Model::factory('Formbuilder')->get_form_id($current_profile_id);

        $form_id_current = $form_id[0]["form_id"];
        if ($form_id_current == 0) {
            //get maximum form id
            $new_form_id = Model::factory('Formbuilder')->get_form_id_max();

            $datafill = array();
            $datafill['form_id'] = $new_form_id;

            Model::factory('profiles')->updateProfile($current_profile_id, $datafill);

            $form_id_current = $new_form_id;
        }

        foreach ($questions_arr as $questions) {
            $question_id = $questions["question_id"];
            $question_text = $questions["question_text"];
            $question_type_id = $questions["question_type_id"];
            $image_title = $questions["image_title"];
            $image_align = $questions["image_align"];
            $mandatory_field = $questions["is_mandatory"];
            $button_link_url = $questions["button_link_url"];
            $button_colour = $questions["button_colour"];
            $doc_title = $questions["doc_title"];
            $include_name = $questions["include_name"];
            $include_employer = $questions["include_employer"];
            $include_email = $questions["include_email"];
            $include_phone = $questions["include_phone"];
            $participant_include_signature = $questions["participant_include_signature"];
            $participant_include_employer = $questions["participant_include_employer"];
            $is_logchecked = $questions["is_logchecked"];
            $log_columntitle = $questions["log_columntitle"];

            $new_question_id = Model::factory('Formbuilder')->form_builder_question_save($current_profile_id, $question_text, $question_type_id, $image_title, $image_align, $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id_current, $include_name, $include_employer, $include_email, $include_phone, $participant_include_signature, $participant_include_employer, $is_logchecked, $log_columntitle);

            $question_options_arr = Model::factory('Formbuilder')->get_question_option($question_id);

            foreach ($question_options_arr as $question_options) {
                $option_name = $question_options["option_name"];
                $question_option_type_id = $question_options["question_option_type_id"];
                Model::factory('Formbuilder')->form_builder_question_option_add($new_question_id, $option_name, $question_option_type_id);
            }
        }
    }

    public function action_add_form_from_library()
    {
        $current_profile_id = $_REQUEST["current_profile_id"];
        $form_id_to_import = $_REQUEST["form_id"];

        $questions_arr = Model::factory('Formbuilder')->get_profile_question_by_form_id($form_id_to_import);
        $form_id = Model::factory('Formbuilder')->get_form_id($current_profile_id);

        $form_id_current = $form_id[0]["form_id"];
        if ($form_id_current == 0) {
            //get maximum form id
            $new_form_id = Model::factory('Formbuilder')->get_form_id_max();

            $datafill = array();
            $datafill['form_id'] = $new_form_id;

            Model::factory('profiles')->updateProfile($current_profile_id, $datafill);

            $form_id_current = $new_form_id;
        }

        foreach ($questions_arr as $questions) {
            $question_id = $questions["question_id"];
            $question_text = $questions["question_text"];
            $question_type_id = $questions["question_type_id"];
            $image_title = $questions["image_title"];
            $image_align = $questions["image_align"];
            $mandatory_field = $questions["is_mandatory"];
            $button_link_url = $questions["button_link_url"];
            $button_colour = $questions["button_colour"];
            $doc_title = $questions["doc_title"];
            $include_name = $questions["include_name"];
            $include_employer = $questions["include_employer"];
            $include_email = $questions["include_email"];
            $include_phone = $questions["include_phone"];
            $participant_include_signature = $questions["participant_include_signature"];
            $participant_include_employer = $questions["participant_include_employer"];
            $is_logchecked = $questions["is_logchecked"];
            $log_columntitle = $questions["log_columntitle"];

            $new_question_id = Model::factory('Formbuilder')->form_builder_question_save($current_profile_id, $question_text, $question_type_id, $image_title, $image_align, $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id_current, $include_name, $include_employer, $include_email, $include_phone, $participant_include_signature, $participant_include_employer, $is_logchecked, $log_columntitle);

            $question_options_arr = Model::factory('Formbuilder')->get_question_option($question_id);

            foreach ($question_options_arr as $question_options) {
                $option_name = $question_options["option_name"];
                $question_option_type_id = $question_options["question_option_type_id"];
                Model::factory('Formbuilder')->form_builder_question_option_add($new_question_id, $option_name, $question_option_type_id);
            }
        }
    }

    public function action_delete_form_from_library()
    {
        $form_id = $_REQUEST["form_id"];

        //check whether this form is deleted from code then delete entries from questions,options and library else just update flag
        $form_builder_library_arr = Model::factory('Formbuilder')->get_form_builder_library_entry($form_id);
        if (count($form_builder_library_arr) > 0) {
            if ($form_builder_library_arr[0]["user_id"] == $this->session->get('clientuserid')) //form creator same as current user
            {
                $is_deleted = $form_builder_library_arr[0]["is_deleted"];
                $form_builder_library_id = $form_builder_library_arr[0]["form_builder_library_id"];

                if ($is_deleted == 1) {
                    Model::factory('Formbuilder')->remove_form_and_questions($form_id);
                } else {
                    $datafill = array();
                    $datafill['is_deleted_from_library'] = 1;
                    Model::factory('Formbuilder')->form_builder_library_update($form_builder_library_id, $datafill);
                }
            }
        }
        exit;
    }

    public function action_add_participant()
    {
        $profile_id = $_REQUEST["current_profile_id"];
        $name = $_REQUEST["name"];
        $due_date_arr = explode("/", $_REQUEST["due_date"]);

        $due_date = $due_date_arr[2] . "-" . $due_date_arr[1] . "-" . $due_date_arr[0];

        Model::factory('Formbuilder')->add_participant($profile_id, $name, $due_date);

        $add_edit_participant_list_arr = Model::factory('Formbuilder')->get_participant_list($profile_id);

        //echo json_encode(array_values($add_edit_participant_list_arr));

        $this->template = View::factory('form_builder_participant_list')
            ->bind("profile_id", $profile_id)
            ->bind("add_edit_participant_list_arr", $add_edit_participant_list_arr);
    }

    public function action_edit_participant()
    {
        $name = $_REQUEST["name"];
        $participant_id = $_REQUEST["participant_id"];
        $due_date_arr = explode("/", $_REQUEST["due_date"]);

        $due_date = $due_date_arr[2] . "-" . $due_date_arr[1] . "-" . $due_date_arr[0];

        Model::factory('Formbuilder')->edit_participant($participant_id, $name, $due_date);

        exit;
    }

    public function action_import_participant()
    {
        $profile_id = $_REQUEST["profile_id"];

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";

        $objPHPEx = new PHPExcel();

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);

        $objPHPEx = $objReader->load($_FILES["file"]["tmp_name"]);

        $sheet = $objPHPEx->getActiveSheet(0);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        for ($row = 1; $row <= $highestRow; $row++) {

            $name = trim($sheet->getCellByColumnAndRow(0, $row)->getValue());
            $due_date_val = trim($sheet->getCellByColumnAndRow(1, $row)->getValue());

            $date = PHPExcel_Shared_Date::ExcelToPHP($due_date_val); // 1007596800 (Unix time)
            $due_date = date('Y-m-d', $date);

            if ($name != "" && $due_date != "") {
                Model::factory('Formbuilder')->add_participant($profile_id, $name, $due_date);
            }
        }

        $add_edit_participant_list_arr = Model::factory('Formbuilder')->get_participant_list($profile_id);

        //echo json_encode(array_values($add_edit_participant_list_arr));
        $this->template = View::factory('form_builder_participant_list')
            ->bind("profile_id", $profile_id)
            ->bind("add_edit_participant_list_arr", $add_edit_participant_list_arr);
    }

    public function action_submit_participant()
    {
        $profile_id = $_REQUEST["profile_id"];

        if (count($_REQUEST["email_recipient_participant"]) > 0) {
            Model::factory('Formbuilder')->remove_participant_recipient($profile_id);
        }

        for ($i = 0; $i < count($_REQUEST["email_recipient_participant"]); $i++) {
            Model::factory('Formbuilder')->add_participant_recipient($profile_id, $_REQUEST["email_recipient_participant"][$i]);
        }

        exit;
    }

    public function action_remove_participant()
    {
        $profile_id = $_REQUEST["current_profile_id"];

        $add_edit_participant_list_arr = Model::factory('Formbuilder')->remove_participant($profile_id);

        $add_edit_participant_list_arr = Model::factory('Formbuilder')->get_participant_list($profile_id);

        $this->template = View::factory('form_builder_participant_list')
            ->bind("profile_id", $profile_id)
            ->bind("add_edit_participant_list_arr", $add_edit_participant_list_arr);
    }

    public function action_remove_participant_user()
    {
        $participant_id = $_REQUEST["participant_id"];
        $profile_id = $_REQUEST["profile_id"];

        Model::factory('Formbuilder')->remove_participant_user($participant_id);

        $add_edit_participant_list_arr = Model::factory('Formbuilder')->get_participant_list($profile_id);

        $this->template = View::factory('form_builder_participant_list')
            ->bind("profile_id", $profile_id)
            ->bind("add_edit_participant_list_arr", $add_edit_participant_list_arr);
    }

    public function action_form_submissions()
    {
        // set title tag
        $this->template->head->title = "Form Submissions";
        if (!$this->session->get('clientuserid') && !$this->session->get('adminuserid')) :
            $this->request->redirect(URL::base() . 'welcome/index');
        endif;
        // Default Head view file
        $this->template->head = View::factory('template/head');

        // Set the default Page Title
        $this->template->head->title = 'Welcome to ScanLink';

        // All CSS Stylesheets
        $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen') /* , array('href'=>'styles/default.css','media'=>'screen'), array('href'=>'styles/colorbox.css','media'=>'screen') */, array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/colorbox.css', 'media' => 'screen'));
        $actionnm = $this->request->action();
        // All JS Libraries and Scripts
        if ($actionnm != 'changepwd') :
            if ($actionnm == "scanalytics") {
                $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js', "js/chart/js/jquery-ui.js", "js/chart/js/jquery.themeswitcher.js", "js/chart/js/jquery.tooltip.js", "js/chart/js/jquery.ddchart.js");
                $this->template->head->stylesheets = array(array('href' => 'styles/style.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.core.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.theme.css', 'media' => 'screen'), array('href' => 'styles/ui/jquery.ui.tabs.css', 'media' => 'screen'), array('href' => 'styles/coin-slider-styles.css', 'media' => 'screen'), array('href' => 'styles/chart/style/ddchart.css', 'media' => 'screen'), array('href' => 'styles/chart/style/theme.css', 'media' => 'screen'));
            } else
            //$this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'js/dashboard/jquery-ui-1.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
            {
                $this->template->head->javascripts = array('js/jquery.min.js', 'js/dashboard/superfish.js', 'js/dashboard/tooltip.js', 'form-builder/js/jquery-ui.js', 'js/dashboard/custom.js', 'js/dashboard/script.js', 'js/jquery.validate.min.js', 'js/dashboard/jquery.colorbox.js', 'js/functions.js', 'js/ui/jquery.ui.core.js', 'js/ui/jquery.ui.widget.js', 'js/ui/jquery.ui.tabs.js', "js/paginator.js", "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
            }
        else :
            $this->template->head->javascripts = array('js/dashboard/jquery-1.4.2.js', 'js/jquery.validate.min.js', "js/coin-slider.min.js", 'js/jquery.uniform.js', 'js/custome.js', 'js/modernizr.custom.60079.js');
        endif;

        // Default Header view file
        $this->template->headers = View::factory('template/headers');

        // Default menu
        $this->template->headers->menu = View::factory('template/menu');
        if ($this->session->get('clientuserid')) :
            $this->template->headers->menu->logoutlnk = $this->session->get('clientusername');
        endif;
        $this->template->headers->menu->menu = $this->menu;
        /* WP_MS START  */
        $this->template->headers->menu->menuSubmenu = $this->menuSubmenu;
        /* WP_MS END  */
        $this->template->headers->menu->currentactive = $this->request->controller() . "/" . $this->request->action();
        // Default Content template
        $this->template->footer = View::factory('template/footer');

        if ($this->template->footer) :
            // All JS Libraries and Scripts
            $this->template->footer->scripts = array();
        endif;

        $this->userid = $this->session->get('clientuserid');

        $profile_id = $this->request->param("id");

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        if (strtotime($profile['expired_at']) < strtotime(date("Y-m-d H:i:s")) && ($profile['free_code'] == 0 || ($profile['free_code'] == 1 && $profile['renewal_required'] == 1))) {
            $expried_or_not = true;
        }

        $user_data = Model::factory('user')->getUserByID($this->session->get('clientuserid'));

        if ($user_data["is_sub_user"] == 1 && $user_data["access_form_submission"] == 0) {
            //if ($profile['user_id'] != $this->userid) {
            $this->session->set("errormessage", "Oops! You have no access to view form submissions");
            $this->request->redirect('dashboard/index');
            exit;
        }

        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        $question_header = Model::factory('formbuilder')->form_builder_questions_submissions($profile_id);

        // Default Content template
        $this->template->content = View::factory('formbuilder/form_submissions')
            ->bind('profile_id', $profile_id)
            ->bind('errormessage', $errormessage)
            ->bind('successmessage', $successmessage)
            ->bind('form_answer', $form_answer)
            ->bind('profile', $profile)
            ->bind('expried_or_not', $expried_or_not)
            ->bind('question_header', $question_header)
            ->bind('user_data', $user_data);

        $this->template->content->innermenu = View::factory('template/innermenu')
            ->bind('equiptypes', $this->types)
            ->bind('activemenu', $param)
            ->bind('colorcode_display', $colorcode_display);
    }

    //delete form builder answer
    public function action_form_builder_response_delete()
    {
        $delete_id = $_POST["delete_id"];
        $profile_id = $_POST["profile_id"];

        if ($delete_id != "") {
            Model::factory('formbuilder')->delete_answer_by_session_id($profile_id, $_REQUEST["delete_id"]);
        }

        print(json_encode("success"));
        exit;
    }
    //end of delete form builder answer

    public function action_form_submissions_view()
    {
        // set title tag
        $this->template->head->title = "Form Submissions";
        $this->template->head->stylesheets = array(array('href' => 'styles/form-details.css', 'media' => 'screen'));

        $this->template->headers = "";
        $this->template->footer = "";

        $app_user_name = explode("::", $this->request->query("app_user_name"));
        $profile_id = $this->request->query("profile_id");

        //delete form builder answer
        if (isset($_REQUEST["delete_id"]) && $_REQUEST["delete_id"] != "") {
            Model::factory('formbuilder')->delete_answer_by_session_id($profile_id, $_REQUEST["delete_id"]);
            $this->session->set("successmessage", "Entry deleted successfully.");
        }
        //end of delete form builder answer

        $logo = Model::factory('logo')->getLogoByProfileId($profile_id);

        $profile_logo = "";

        if (!empty($logo) && isset($logo["logo_name"]) && file_exists($logo["logo_name"])) {
            $profile_logo = $logo["logo_name"];
        }

        $cond = " and profile_id='" . $profile_id . "' ";
        $session_id = $this->request->query("session_id");
        if ($session_id != "") {
            $cond .= " and session_id='" . $session_id . "'";
        }

        /*if(count($app_user_name)>0)
        {
        if($app_user_name[0]!="")
        $cond .= " and app_user_firstname='".addslashes($app_user_name[0])."' ";
        else
        $cond .= " and app_user_firstname='' ";

        if($app_user_name[1]!="")
        $cond .= " and app_user_lastname='".addslashes($app_user_name[1])."' ";
        else
        $cond .= " and app_user_lastname='' ";

        }

        $from_date = $this->request->query("from_date");
        $to_date = $this->request->query("to_date");

        if($from_date!="")
        $cond .= " and DATE_FORMAT(date_time,'%Y-%m-%d')>=STR_TO_DATE('".$from_date."', '%d/%m/%Y' )";

        if($to_date!="")
        $cond .= " and DATE_FORMAT(date_time,'%Y-%m-%d')<=STR_TO_DATE('".$to_date."', '%d/%m/%Y' )";*/

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        /* for paging */
        $offset = 0;
        $page_size = 10;
        $page_no = $this->request->query("page_no");
        $current_url = URL::base() . $this->request->uri() . URL::query();

        $session_count = Model::factory('formbuilder')->get_session_id_count($cond);

        if ($page_no == "") {
            $page_no = 1;
        }

        $total_pages = ceil($session_count / $page_size);

        $offset = $page_size * ($page_no - 1);

        /*$session_arr = Model::factory('formbuilder')->get_session_id_for_form_answer_with_limit($cond,$offset,$page_size);

        foreach($session_arr as $session_item)
        {
        $answer_arr[] = Model::factory('formbuilder')->form_builder_answers($cond." and session_id='".$session_item["session_id"]."'");
        }*/

        //$answer_arr[] = Model::factory('formbuilder')->form_builder_answers($cond,' form_builder_answer_id ASC');
        $answer_time = Model::factory('formbuilder')->form_builder_answers_time($cond);

        $questions_arr = Model::factory('Formbuilder')->get_profile_question($profile_id);

        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }

        // Default Content template
        $this->template->content = View::factory('formbuilder/form_submissions_view')
            ->bind('profile_id', $profile_id)
            ->bind('errormessage', $errormessage)
            ->bind('successmessage', $successmessage)
            ->bind('profile', $profile)
            ->bind('answer_time', $answer_time)
            ->bind('questions_arr', $questions_arr)
            ->bind('total_pages', $total_pages)
            ->bind('page_no', $page_no)
            ->bind('current_url', $current_url)
            ->bind('cond', $cond)
            ->bind('profile_logo', $profile_logo);
    }

    public function action_export_old()
    {
        // set title tag
        $this->template->head->title = "Form Submission Log Export";

        $profile_id = $this->request->param("id");

        $cond = " and profile_id='" . $profile_id . "' ";

        if (isset($_REQUEST["app_user_firstname"]) && $_REQUEST["app_user_firstname"]) {
            $cond .= " and app_user_firstname='" . addslashes($_REQUEST["app_user_firstname"]) . "' ";
        } else if ($_REQUEST["download_all"] == 0) {
            $cond .= " and app_user_firstname='' ";
        }

        if (isset($_REQUEST["app_user_lastname"]) && $_REQUEST["app_user_lastname"]) {
            $cond .= " and app_user_lastname='" . addslashes($_REQUEST["app_user_lastname"]) . "' ";
        } else if ($_REQUEST["download_all"] == 0) {
            $cond .= " and app_user_lastname='' ";
        }

        $from_date = $this->request->query("from_date");
        $to_date = $this->request->query("to_date");

        if ($from_date != "") {
            $cond .= " and DATE_FORMAT(date_time,'%Y-%m-%d')>=STR_TO_DATE('" . $from_date . "', '%d/%m/%Y' )";
        }

        if ($to_date != "") {
            $cond .= " and DATE_FORMAT(date_time,'%Y-%m-%d')<=STR_TO_DATE('" . $to_date . "', '%d/%m/%Y' )";
        }

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);
        //echo $profile_id;exit;

        $questions = Model::factory('formbuilder')->get_profile_question_for_export($profile_id);

        $session_arr = Model::factory('formbuilder')->get_session_id_for_form_answer($cond);

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";

        $workbook = new PHPExcel();

        $sheet = $workbook->getActiveSheet();

        $workbook->setActiveSheetIndex(0);
        $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $profile["form_title"]);

        $workbook->getActiveSheet()->mergeCells('A1:C1');

        $rows = array();

        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        $workbook->getActiveSheet()->getColumnDimension('A')->setWidth(20);

        $workbook->getActiveSheet()->setCellValue('A2', 'Submission Date and Time');
        $workbook->getActiveSheet()->setCellValue('B2', 'User Name');
        $workbook->getActiveSheet()->setCellValue('C2', 'Email');
        $workbook->getActiveSheet()->setCellValue('D2', 'Mobile');

        $workbook->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $workbook->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $workbook->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $workbook->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        $column = 'E';
        foreach ($questions as $question) {
            $temp_col_for_answers = $column;
            $temp_col_for_answers1 = $column;

            $question_id = $question["question_id"];
            $question_text = $question["question_text"];
            $question_type_id = $question["question_type_id"];

            $workbook->getActiveSheet()->setCellValue($column . '2', $question_text); //PRINT     QUESTION
            $workbook->getActiveSheet()->getStyle($column . '2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $next_question = Model::factory('formbuilder')->get_next_question_id($question_id, $profile_id);

            if ($next_question[0]["question_type_id"] == 7) //grid
            {
                $grid_row = $question_option = Model::factory('formbuilder')->get_question_option($next_question[0]["question_id"], 5);
                $grid_column = $question_option = Model::factory('formbuilder')->get_question_option($next_question[0]["question_id"], 6);

                $temp_col = $column;
                foreach ($grid_row as $row) {
                    foreach ($grid_column as $col) {
                        $workbook->getActiveSheet()->setCellValue($temp_col . '3', $row["option_name"] . "::" . $col["option_name"]);

                        $workbook->getActiveSheet()->getStyle($temp_col . '3')->getFont()->setBold(true);

                        $temp_col++;
                    }
                }

                $question_count = count($grid_row) * count($grid_column);
            } else {
                $question_options = Model::factory('formbuilder')->get_question_option($next_question[0]["question_id"]);

                $option_cnt = 1;
                $temp_col = $column;

                foreach ($question_options as $question_option) {
                    if ($next_question[0]["question_type_id"] == 6 && $option_cnt == 1) //number scale
                    {
                        $cell_content = "From : " . $question_option["option_name"];
                    } elseif ($next_question[0]["question_type_id"] == 6 && $option_cnt == count($question_options)) //number scale
                    {
                        $cell_content = "To : " . $question_option["option_name"];
                    } else {
                        $cell_content = $question_option["option_name"];
                    }

                    $workbook->getActiveSheet()->setCellValue($temp_col . '3', $cell_content);
                    $workbook->getActiveSheet()->getStyle($temp_col . '3')->getFont()->setBold(true);

                    $option_cnt++;
                    $temp_col++;
                }
                $question_count = count($question_options);
            }

            if ($question_count > 0) {
                $from_col = $column . '2';

                for ($j = 0; $j < ($question_count - 1); $j++) {
                    $column++;
                }

                $to_col = ($column--) . '2';

                $workbook->setActiveSheetIndex(0)->mergeCells($from_col . ':' . $to_col);
            }

            $workbook->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);

            /* ---------------------- Answers --------------- */

            //for merge columns
            $row_num = 5;

            if ($question_count > 0) {
                $from_col = $temp_col_for_answers;

                for ($j = 0; $j < ($question_count - 1); $j++) {
                    $temp_col_for_answers++;
                }

                $to_col = $temp_col_for_answers--;

                foreach ($session_arr as $session_item) {
                    if ($next_question[0]["question_type_id"] != 7) //grid
                    {
                        $workbook->setActiveSheetIndex(0)->mergeCells($from_col . $row_num . ':' . $to_col . $row_num);
                    }

                    $row_num++;
                }
            }
            //end for merge columns

            $row_num = 5;
            foreach ($session_arr as $session_item) {
                $answers = Model::factory('formbuilder')->form_builder_answers(" and profile_id='" . $profile_id . "' and question_id='" . $next_question[0]["question_id"] . "' and session_id='" . $session_item["session_id"] . "'");

                if (count($answers) > 0) {
                    $workbook->getActiveSheet()->setCellValue("A" . $row_num, $answers[0]["date_time_formated"]);
                    $workbook->getActiveSheet()->setCellValue("B" . $row_num, $answers[0]["app_user_firstname"] . " " . $answers[0]["app_user_lastname"]);
                    $workbook->getActiveSheet()->setCellValue("C" . $row_num, $answers[0]["app_user_email"]);
                    $workbook->getActiveSheet()->setCellValue("D" . $row_num, $answers[0]["app_user_mobile"]);

                    //echo $next_question[0]["question_type_id"]."<br>";
                    $temp_col = $temp_col_for_answers1;
                    foreach ($answers as $answer) {
                        if ($next_question[0]["question_type_id"] == 7) //grid
                        {

                            $temp_counter = 0;
                            foreach ($grid_column as $col) {
                                if ($temp_counter == $answer["question_answer"]) {
                                    $workbook->getActiveSheet()->setCellValue($temp_col . $row_num, "YES");
                                }

                                $temp_col++;
                                $temp_counter++;
                            }
                            //$workbook->getActiveSheet()->setCellValue($temp_col_for_answers1.$row_num, $answer["question_answer"]);
                        } else {
                            $workbook->getActiveSheet()->setCellValue($temp_col_for_answers1 . $row_num, $answer["question_answer"]);
                        }
                    }
                }

                $row_num++;
            }

            /* ---------------------- Answers end--------------- */

            $column++;
        }
        //exit;

        $workbook->getActiveSheet()->getStyle('A2:' . ($column) . '2')->getFont()->setBold(true);

        //set background color
        $workbook->getActiveSheet()->getStyle('A2:' . ($column) . '2')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'F7E1D2')));
        $workbook->getActiveSheet()->getStyle('A2:' . ($column) . '2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        $writer->save('php://output');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="form_submission_log.xls"');
        header('Cache-Control: max-age=0');
        exit;
    }

    public function addressByColumnAndRow($col, $row)
    {
        return $this->getColFromNumber($col) . $row;
    }

    public function getColFromNumber($num)
    {
        $numeric = ($num) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num) / 26);
        if ($num2 > 0) {
            return $this->getColFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }

    public function action_export_old1()
    {
        // ini_set('memory_limit', '8196M');
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        // set title tag
        $this->template->head->title = "Form Submission Log Export";

        $profile_id = $this->request->param("id");

        $from_date = $this->request->query("from_date");
        $to_date = $this->request->query("to_date");

        $question_header = Model::factory('formbuilder')->form_builder_questions_submissions($profile_id);

        $entity = "form_builder_answers";
        $filter = "";
        if ($from_date != "") {
            $filter["from_date"] = $from_date;
        }

        if ($to_date != "") {
            $filter["to_date"] = $to_date;
        }

        $filter_on = "date_time";
        $curpage = 1;
        $perpage = 1048576; // Excel Worksheet limit
        $params = array("profile_id" => $profile_id);
        $filterby = "asc";
        $group_by = "session_id";

        //$starttime = microtime(true); // Top of page
        //$result_list = Model::factory('paging')->get_form_result($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby, $group_by);
        $result_list = Model::factory('paging')->get_form_result_for_export($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby, $group_by);
        // $endtime = microtime(true); // Bottom of page
        // printf("Page loaded in %f seconds", $endtime - $starttime );

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";

        $workbook = new PHPExcel();

        $sheet = $workbook->getActiveSheet();

        $workbook->setActiveSheetIndex(0);

        // Draw title
        $profile_name = "Profile " . $profile_id . ". ";
        if ($profile["code_profile_name"] != "") {
            $profile_name = $profile_name . $profile["code_profile_name"];
        }

        $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $profile_name);

        $address = $this->addressByColumnAndRow(count($question_header), 1);
        $workbook->getActiveSheet()->mergeCells('A1:' . $address);

        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

        // Draw header columns
        $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Date and Time');


        for ($colIndex = 0; $colIndex < count($question_header); $colIndex++) {
            if ($question_header[$colIndex]['question_type_id'] != 25) {
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, 2, $question_header[$colIndex]['log_columntitle']);
            } else {
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 2, 2, 'Name');
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 3, 2, 'Phone');
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 4, 2, 'Venue Address');
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 5, 2, 'Location Description');
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 6, 2, 'Vehicle Reg No');
            }
        }


        $address = $this->addressByColumnAndRow(count($question_header), 2);
        $workbook->getActiveSheet()->getStyle('A2:' . $address)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'F7E1D2')));
        $workbook->getActiveSheet()->getStyle('A2:' . $address)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $workbook->getActiveSheet()->getStyle('A2:' . $address)->getFont()->setBold(true);

        // Draw body
        for ($rowIndex = 0; $rowIndex < count($result_list["results"]); $rowIndex++) {
            $resultRow = $result_list["answers"][$rowIndex];


            // date column
            $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, $rowIndex + 3, $result_list["results"][$rowIndex]["date_time_formated"]);

            // question log column
            for ($colIndex = 0; $colIndex < count($question_header); $colIndex++) {
                $covid_visitor_log = '';
                for ($cellIndex = 0; $cellIndex < count($resultRow); $cellIndex++) {
                    $cellData = $resultRow[$cellIndex];
                    if ($question_header[$colIndex]['question_type_id'] == 25) {
                        $covid_visitor_log = $cellData['question_answer'];
                        if (strpos($covid_visitor_log, ':::') != false) {
                            $covid_visitor_exploded = explode(':::', $covid_visitor_log);
                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 3, $covid_visitor_exploded[0]);
                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 4, $covid_visitor_exploded[1]);
                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 5, $covid_visitor_exploded[5]);
                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 6, $covid_visitor_exploded[6]);
                        }
                    } else {

                        if ($cellData["question_id"] == $question_header[$colIndex]["question_id"]) {
                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 3, $cellData["question_answer"]);
                            break;
                        }
                    }
                }
            }
        }
        // Auto size
        for ($colIndex = 0; $colIndex <= count($question_header); $colIndex++) {
            $workbook->getActiveSheet()->getColumnDimension($this->getColFromNumber($colIndex))->setAutoSize(true);
        }


        ob_end_clean();
        $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="form_submission_log.xls"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function action_export()
    {
        // ini_set('memory_limit', '8196M');
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        // set title tag
        $this->template->head->title = "Form Submission Log Export";

        $profile_id = $this->request->param("id");

        $from_date = $this->request->query("from_date");
        $to_date = $this->request->query("to_date");

        $question_header = Model::factory('formbuilder')->form_builder_questions_submissions($profile_id);

        $entity = "form_builder_answers";
        $filter = "";
        if ($from_date != "") {
            $filter["from_date"] = $from_date;
        }

        if ($to_date != "") {
            $filter["to_date"] = $to_date;
        }

        $filter_on = "date_time";
        $curpage = 1;
        $perpage = 1048576; // Excel Worksheet limit
        $params = array("profile_id" => $profile_id);
        $filterby = "asc";
        $group_by = "session_id";

        //$starttime = microtime(true); // Top of page
        //$result_list = Model::factory('paging')->get_form_result($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby, $group_by);
        $result_list = Model::factory('paging')->get_form_result_for_export($entity, $filter, $filter_on, $curpage, $perpage, $params, $filterby, $group_by);
        // $endtime = microtime(true); // Bottom of page
        // printf("Page loaded in %f seconds", $endtime - $starttime );

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";

        $workbook = new PHPExcel();

        $sheet = $workbook->getActiveSheet();

        $workbook->setActiveSheetIndex(0);

        // Draw title
        $profile_name = "Profile " . $profile_id . ". ";
        if ($profile["code_profile_name"] != "") {
            $profile_name = $profile_name . $profile["code_profile_name"];
        }

        $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $profile_name);

        $address = $this->addressByColumnAndRow(count($question_header), 1);
        $workbook->getActiveSheet()->mergeCells('A1:' . $address);

        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

        // Draw header columns
        $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Date and Time');


        for ($colIndex = 0; $colIndex < count($question_header); $colIndex++) {
            if ($question_header[$colIndex]['question_type_id'] != 25) {
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, 2, $question_header[$colIndex]['log_columntitle']);
            } else {
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, 2, 'Name');
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 2, 2, 'Phone');
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 3, 2, 'Venue Address');
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 4, 2, 'Location Description');
                $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 5, 2, 'Vehicle Reg No');
            }
        }

        $address = $this->addressByColumnAndRow(count($question_header), 2);
        $address = "G2";
        $workbook->getActiveSheet()->getStyle('A2:' . $address)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'F7E1D2')));
        $workbook->getActiveSheet()->getStyle('A2:' . $address)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $workbook->getActiveSheet()->getStyle('A2:' . $address)->getFont()->setBold(true);

        // Draw body
        for ($rowIndex = 0; $rowIndex < count($result_list["results"]); $rowIndex++) {
            $resultRow = $result_list["answers"][$rowIndex];

            // date column
            $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, $rowIndex + 3, $result_list["results"][$rowIndex]["date_time_formated"]);

            // question log column
            for ($colIndex = 0; $colIndex < count($question_header); $colIndex++) {
                $covid_visitor_log = '';
                for ($cellIndex = 0; $cellIndex < count($resultRow); $cellIndex++) {
                    $cellData = $resultRow[$cellIndex];

                    if ($question_header[$colIndex]['question_type_id'] == 25) {

                        $covid_visitor_log = $cellData['question_answer'];

                        if (strpos($covid_visitor_log, ':::') !== false) {
                            $covid_visitor_exploded = explode(':::', $covid_visitor_log);

                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 3, $covid_visitor_exploded[0]);

                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 2, $rowIndex + 3, $covid_visitor_exploded[1]);

                            // $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 3, $rowIndex + 5, $covid_visitor_exploded[2]);

                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 3, $rowIndex + 3, $covid_visitor_exploded[5]);

                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 4, $rowIndex + 3, $covid_visitor_exploded[6]);
                        }
                    } else {

                        //.print_r($cellData["question_answer"]);
                        if ($cellData["question_id"] == $question_header[$colIndex]["question_id"]) {
                            $workbook->getActiveSheet()->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 3, $cellData["question_answer"]);
                            break;
                        }
                    }
                }
            }
        }

        // exit;
        // Auto size
        for ($colIndex = 0; $colIndex <= count($question_header); $colIndex++) {
            $workbook->getActiveSheet()->getColumnDimension($this->getColFromNumber($colIndex))->setAutoSize(true);
        }

        ob_end_clean();
        $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="form_submission_log.xls"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function action_download()
    {
        // set title tag
        $this->template->head->title = "Form Submission Log Export";

        $profile_id = $this->request->param("id");

        $cond = " and profile_id='" . $profile_id . "' ";

        $session_id = $this->request->query("session_id");
        //$counterloop = $this->request->query("counter_loop");
        if ($session_id != "") {
            $cond .= " and session_id='" . $session_id . "'";
        }

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        $logo = Model::factory('logo')->getLogoByProfileId($profile_id);

        $profile_logo = "images/logo.png";

        if (!empty($logo) && isset($logo["logo_name"]) && file_exists($logo["logo_name"])) {
            $profile_logo = $logo["logo_name"];
        }

        $answer_time = Model::factory('formbuilder')->form_builder_answers_time($cond);

        $questions_arr = Model::factory('Formbuilder')->get_profile_question($profile_id);

        //$html_content = '<img src="'. URL::base().$profile_logo.'" height="108" alt=""  /><br><hr>&nbsp;';

        //$html_content .= '<div><b>Profile Number</b> : '.$profile_id.'</div>';

        //$html_content .= '<div><b>Profile Name</b> : '.$profile["code_profile_name"].'</div>';
        //$html_content .= '<hr>';

        //$html_content .= '&nbsp;<table width="100%"><tr><td>&nbsp;</td></tr><tr><td align="right" >Date/Time: '.$answer_time[0]["date_time_formated"].'</td></tr></table>';

        $html_content = '<img src="' . URL::base() . $profile_logo . '" height="108" alt=""  /><br><hr>&nbsp;';

        $html_content .= '<div><b>Profile Number</b> : ' . $profile_id . '</div>';

        $html_content .= '<div><b>Profile Name</b> : ' . $profile["code_profile_name"] . '</div>';
        $html_content .= '<hr>';

        $html_content .= '&nbsp;<table width="100%"><tr><td>&nbsp;</td></tr><tr><td align="right" >Date/Time: ' . $answer_time[0]["date_time_formated"] . '</td></tr></table>';
        foreach ($questions_arr as $question_item) {
            $question_type_id = $question_item["question_type_id"];

            if ($question_type_id == "10") // heading
            {
                $html_content .= '<h1>' . $question_item["question_text"] . '</h1>';
            } elseif ($question_type_id == "12") // subheading
            {
                $html_content .= '<h3>' . $question_item["question_text"] . '</h3>';
            } elseif ($question_type_id == "2") /// question text
            {
                if (isset($question_item["question_text"])) {
                    $html_content .= '<br>' . $question_item["question_text"] . '<br>';
                }
            } else {
                $question_id = $question_item["question_id"];

                $answer_arr = Model::factory('Formbuilder')->form_builder_answers($cond . " and question_id='" . $question_id . "'");

                foreach ($answer_arr as $answer_item) {

                    switch ($question_type_id) {
                        case "3": // Mutliple choise
                        case "4": // Checkboxes
                        case "5": // Drop Down Menu
                            $question_options = Model_formbuilder::get_question_option($question_id);

                            $question_option_text = "";
                            foreach ($question_options as $question_option) {
                                if ($question_option_text == "") {
                                    $question_option_text = htmlspecialchars($question_option["option_name"]);
                                } else {
                                    $question_option_text = $question_option_text . '<br>' . htmlspecialchars($question_option["option_name"]);
                                }
                            }
                            //$html_content .=  "<b>Options:</b> ".$question_option_text."<br>";
                            break;

                        case "6": // Number Scale
                            $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $scale_from = $question_options[0]["option_name"];

                            $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $scale_to = $question_options[0]["option_name"];

                            //$html_content .=  "<b>Range:</b> From ".$scale_from." To ".$scale_to."<br>";

                            break;

                        case "7": // Grid

                            if ($answer_item["row_number"] == 0) //show question for once
                            {
                                $_SESSION["grid"] = '<b>User Input:</b><br>';
                            }

                            $grid_count = Model_formbuilder::get_count_for_grid_type($question_id, $profile_id, $answer_item["session_id"]);

                            $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            if ($answer_item["row_number"] == 0) // show colums once
                            {
                                $_SESSION["grid"] .= '<table border="0" cellpadding="6" width="50%" cellspacing="0">';

                                $_SESSION["grid"] .= '<tr>';
                                $_SESSION["grid"] .= '<td>&nbsp;</td>';
                                for ($i = 0; $i < count($column_text); $i++) {
                                    $_SESSION["grid"] .= '<td align="center">' . $column_text[$i]["option_name"] . '</td>';
                                }

                                $_SESSION["grid"] .= '</tr>';
                            }

                            for ($i = 0; $i < count($row_text); $i++) {
                                if ($answer_item["row_number"] == $i) {
                                    $_SESSION["grid"] .= '<tr>';
                                    $_SESSION["grid"] .= '<td>' . $row_text[$i]["option_name"] . '</td>';

                                    for ($j = 0; $j < count($column_text); $j++) {
                                        if ($answer_item["question_answer"] == $j) {
                                            $checked = '<img width="18" src="' . URL::base() . 'images/green_check_mark_icon.png" >';
                                        } else {
                                            $checked = '&nbsp;';
                                        }

                                        $_SESSION["grid"] .= '<td  align="center">' . $checked . '</td>';
                                    }

                                    $_SESSION["grid"] .= '</tr>';
                                }
                            }

                            if (($grid_count - 1) == $answer_item["row_number"]) // last row;
                            {
                                $_SESSION["grid"] .= '</table><br>';

                                $html_content .= $_SESSION["grid"];
                                unset($_SESSION["grid"]);
                            }
                            break;
                    } //end of switch

                    if ($question_type_id != 7) // not grid
                    {
                        if ($question_type_id == "16") //signature
                        {

                            $signature_image = explode(",", $answer_item["question_answer"]);
                            $signature_name_text = explode(":::", $answer_item["signature_name_text"]);
                            $signature_name_text2 = explode(":::", $answer_item["signature_employer_text"]);
                            $signature_name_text2 = explode(":::", $answer_item["signature_employer_text"]);
                            $signature_name_text3 = explode(":::", $answer_item["signature_email_text"]);
                            $signature_name_text4 = explode(":::", $answer_item["signature_phone_text"]);

                            for ($sig_cnt = 0; $sig_cnt < count($signature_image); $sig_cnt++) {
                                if (isset($signature_name_text[$sig_cnt]) && $signature_name_text[$sig_cnt] != "") {
                                    $html_content .= "<br>Name<br><b>User Input:</b>" . $signature_name_text[$sig_cnt] . "<br>";
                                }

                                if (isset($signature_name_text2[$sig_cnt]) && $signature_name_text2[$sig_cnt] != "") {
                                    $html_content .= "<br>Employer/Company<br><b>User Input:</b>" . $signature_name_text2[$sig_cnt] . "<br>";
                                }
                                if (isset($signature_name_text3[$sig_cnt]) && $signature_name_text3[$sig_cnt] != "") {
                                    $html_content .= "<br>Email<br><b>User Input:</b>" . $signature_name_text3[$sig_cnt] . "<br>";
                                }
                                if (isset($signature_name_text4[$sig_cnt]) && $signature_name_text4[$sig_cnt] != "") {
                                    $html_content .= "<br>Phone<br><b>User Input:</b>" . $signature_name_text4[$sig_cnt] . "<br>";
                                }

                                if (isset($question_item["question_text"])) {
                                    $html_content .= '<br>' . $question_item["question_text"] . '<br>';
                                }

                                if (file_exists('images/signature/' . $signature_image[$sig_cnt]) && filesize('images/signature/' . $signature_image[$sig_cnt]) > 0) {
                                    $html_content .= '<img src="' . URL::base() . 'images/signature/' . $signature_image[$sig_cnt] . '" /><br>';
                                }
                            }
                        } else if ($question_type_id == "17") //upload button
                        {
                            if (isset($question_item["question_text"])) {
                                $html_content .= '<br>' . $question_item["question_text"] . '<br>';
                            }

                            if ($answer_item["question_answer"] != "") {
                                $attachment_arr = explode(",", $answer_item["question_answer"]);

                                for ($k = 0; $k < count($attachment_arr); $k++) {
                                    if (file_exists('images/formbuilder_upload/' . $attachment_arr[$k]) && filesize('images/formbuilder_upload/' . $attachment_arr[$k]) > 0) {
                                        $html_content .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" >Click here to view</a><br>';
                                    }
                                }
                            }
                            $html_content .= "<br>";
                        } else if ($question_type_id == "18") //participant name
                        {

                            $participant_arr = explode(':::', $answer_item["question_answer"]);
                            $participant_sign_image = explode(',', $answer_item["participant_signature_image"]);
                            $participant_employer_text = explode(':::', $answer_item['participant_employer_text']);

                            for ($p = 0; $p < count($participant_arr); $p++) {
                                if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                    $html_content .= "<br>" . $question_item["question_text"] . '<br>';
                                } else {
                                    $html_content .= '<br>Name<br>';
                                }

                                $html_content .= '<b>User Input:</b> ' . $participant_arr[$p] . "<br><br>";

                                if (isset($participant_employer_text[$p]) && $participant_employer_text[$p] != "") {
                                    $html_content .= "<br>Employer/Company<br><b>User Input:</b>" . $participant_employer_text[$p] . "<br>";
                                }

                                if ($participant_sign_image[$p] != "" && file_exists('images/signature/' . $participant_sign_image[$p]) && filesize('images/signature/' . $participant_sign_image[$p]) > 0) {
                                    $html_content .= '<img src="' . URL::base() . 'images/signature/' . $participant_sign_image[$p] . '" /><br>';
                                }
                            }
                        } else if ($question_type_id == "19") //location function
                        {
                            if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                $html_content .= "<br>" . $question_item["question_text"] . '<br>';
                            }

                            $html_content .= '<b>User Input:</b> ' . $answer_item["question_answer"] . "<br>";
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

                            $html_content .= "<br>";
                            $i = 0;
                            $j = 0;
                            while ($i < count($Hazard)) {
                                if ($j == 0) {
                                    if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                        $html_content .= "<br>" . $question_item["question_text"] . '<br><br>';
                                    }
                                    $html_content .= "Task | Activity";
                                }
                                if ($j == 1) {
                                    $html_content .= "Potential Hazards";
                                }

                                if ($j == 2) {
                                    $html_content .= "Risk Score (Before)";
                                }

                                if ($j == 3) {

                                    if ($has_attachment && !empty($attachment_array)) {
                                        foreach ($attachment_array as $key => $attachment) {

                                            $html_content .= 'Attachment : <a target="_blank" href="' . url::base() . 'images/formbuilder_upload/' . $attachment . '"> View Attachment</a><br>';
                                        }
                                    }
                                }

                                if ($j == 4) {
                                    $html_content .= "Control Measures";
                                }

                                if ($j == 5) {
                                    $html_content .= "Risk Score (After)";
                                }


                                $html_content .= "<br><b>User Input:</b> " . $Hazard[$i] . "<br><br>";
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
                                    if (file_exists('images/formbuilder_upload/' . $attachment_arr[$k]) && filesize('images/formbuilder_upload/' . $attachment_arr[$k]) > 0) {
                                        if ($doc_key !== false) {
                                            $html_content .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" >' . $arr_doc_title[$doc_key] . '</a><br>';
                                        } else {
                                            $html_content .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" >Click here to view</a><br>';
                                        }
                                    }
                                }
                            }
                            $html_content .= "<br>";
                        } else if ($question_type_id == "25") {
                            $html_content .= '<p>' . $question_item["question_text"] . '</p>';
                            $checkinform_data = explode(":::", $answer_item["question_answer"]);
                            $html_content .= '<p>Visitor name : ' . (!empty($checkinform_data[0]) ? $checkinform_data[0] : '') . '</p>';
                            $html_content .= '<p>Visitor phone : ' . (!empty($checkinform_data[1]) ? $checkinform_data[1] : '') . '</p>';
                            $html_content .= '<p>Checkin Date : ' . (!empty($checkinform_data[2]) ? $checkinform_data[2] : '') . '</p>';
                            $html_content .= '<p>Time : ' . (!empty($checkinform_data[3]) ? $checkinform_data[3] : '') . '</p>';
                            $html_content .= '<p>Venue Address : ' . (!empty($checkinform_data[4]) ? $checkinform_data[4] : '') . '</p>';
                            $html_content .= '<p>Location address : ' . (!empty($checkinform_data[5]) ? $checkinform_data[5] : '') . '</p>';
                            if (!empty($checkinform_data[5]) && $checkinform_data[5] == 'Visitor') {
                                $html_content .= 'Vehicle Registration ID : ' . (!empty($checkinform_data[6]) ? $checkinform_data[6] : '') . '</p>';
                            } else if (!empty($checkinform_data[5]) && $checkinform_data[5] == 'Other') {
                                $html_content .= 'Other details : ' . (!empty($checkinform_data[6]) ? $checkinform_data[6] : '') . '</p>';
                            }


                            // echo '<b>User Input:</b> ' . str_replace(':::', '<br>', $answer_item["question_answer"]) . "<br><br>";

                        } else {
                            $html_content .= '<b>User Input:</b> ' . str_replace(":::", "<br>", htmlspecialchars($answer_item["question_answer"])) . "<br>";
                        }
                    } //end of if($question_type_id!=7 )
                } //end of foreach($answer_arr as $answer_item)

                if (count($answer_arr) == 0 && $question_type_id != "14" && $question_type_id != "11" && $question_type_id != 13 && $question_type_id != "16" && $question_type_id != "17") {
                    switch ($question_type_id) {
                        case "3": // Mutliple choise
                        case "4": // Checkboxes
                        case "5": // Drop Down Menu
                            $question_options = Model_formbuilder::get_question_option($question_id);

                            $question_option_text = "";
                            foreach ($question_options as $question_option) {
                                if ($question_option_text == "") {
                                    $question_option_text = $question_option["option_name"];
                                } else {
                                    $question_option_text = $question_option_text . "<br> " . $question_option["option_name"];
                                }
                            }
                            //$html_content .= "<b>Options:</b><br> ".$question_option_text."<br>";
                            break;

                        case "6": // Number Scale
                            $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $scale_from = $question_options[0]["option_name"];

                            $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $scale_to = $question_options[0]["option_name"];

                            //$html_content .= "<b>Range:</b> From ".$scale_from." To ".$scale_to."<br>";

                            break;

                        case "7": // Grid
                            $html_content .= '<b>User Input:</b><br>';

                            $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $html_content .= "<table border='0' cellpadding='6' cellspacing='0'>";

                            $html_content .= "<tr>";
                            $html_content .= "<td>&nbsp;</td>";
                            for ($i = 0; $i < count($column_text); $i++) {
                                $html_content .= "<td align='center'>" . $column_text[$i]["option_name"] . "</td>";
                            }

                            $html_content .= "</tr>";

                            for ($i = 0; $i < count($row_text); $i++) {
                                $html_content .= "<tr>";
                                $html_content .= "<td>" . $row_text[$i]["option_name"] . "</td>";

                                for ($j = 0; $j < count($column_text); $j++) {
                                    $html_content .= "<td  align='center'>&nbsp;</td>";
                                }

                                $html_content .= "</tr>";
                            }

                            $html_content .= "</table><br>";

                            break;
                        case "18": // participant name
                            if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                $html_content .= $question_item["question_text"] . '<br>';
                            } else {
                                $html_content .= "Name<br>";
                            }

                            break;
                        case "19": // location function
                            if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                $html_content .= $question_item["question_text"] . '<br>';
                            }

                            break;
                    }

                    if ($question_type_id != 7 && $question_type_id != 20 && $question_type_id != 21) //not grid, web link button and Document Button
                    {
                        $html_content .= '<b>User Input:</b><br><br>';
                    }
                }
            }
        }

        //$html_content = str_replace(URL::base(), ABS_PATH, $html_content); //used when test in local
        //echo $html_content;exit;

        // create new PDF document

        // ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        // require "PHPExcel/PHPExcel/Shared/PDF/tcpdf_6/tcpdf.php";

        // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // $pdf->SetPrintHeader(false);
        // $pdf->SetPrintFooter(true);
        // // set margins
        // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // // set auto page breaks
        // $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // // set image scale factor
        // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // // set font
        // $fontname = $pdf->addTTFfont('calibri.ttf', 'TrueTypeUnicode', '', 32);
        // $pdf->SetFont($fontname, '', 12);

        // //$pdf->SetFont('helvetica', '', 12);

        // // add a page
        // $pdf->AddPage();

        // // output the HTML content
        // // echo '<pre>';
        // // print_r($html_content);
        // // echo '</pre>';
        // // exit;
        // $html_content = str_replace("'", "&#039;", $html_content);

        // $pdf->writeHTML($html_content, true, 0, true, true);

        // $pdf->Output('form_submission_response.pdf', 'D');

        $this->download_formsubmission_log_pdf($html_content, 'form_submission_response.pdf');
    }

    /**
     * Download Pdf using wkpdf plugin
     * 
     */
    public function download_formsubmission_log_pdf($pdf_content, $pdf_name)
    {
        $pdf = new Pdf(array(
            'binary' => '/usr/local/bin/wkhtmltopdf',
            'margin-top'    => 10,
            'margin-right'  => 10,
            'margin-bottom' => 10,
            'margin-left'   => 10,
            'zoom' => 1.00
        ));
        $pdf->addPage($pdf_content);

        if (!$pdf->send($pdf_name)) {
            $error = $pdf->getError();
            mail('shaileshk@webplanex.com', 'Form Submission log error ', $error);
        }
    }

    public function action_downloadpdf()
    {
        // set title tag
        $this->template->head->title = "Form Submission Log Export";

        //$html_content = '<img src="'. URL::base().$profile_logo.'" height="108" alt=""  /><br><hr>&nbsp;';

        //$html_content .= '<div><b>Profile Number</b> : '.$profile_id.'</div>';

        //$html_content .= '<div><b>Profile Name</b> : '.$profile["code_profile_name"].'</div>';
        //$html_content .= '<hr>';

        //$html_content .= '&nbsp;<table width="100%"><tr><td>&nbsp;</td></tr><tr><td align="right" >Date/Time: '.$answer_time[0]["date_time_formated"].'</td></tr></table>';

        //$arr = array('3652-240420161527', '9067-240420161623', '3699-240420161710', '5187-280420212253');
        $arr1 = $this->request->param("array");
        $arr = explode(",", $arr1);
        $html_content = "";
        foreach ($arr as $items) {
            $profile_id = $this->request->param("id");

            $cond = " and profile_id='" . $profile_id . "' ";
            $session_id = $items;
            //$counterloop = $this->request->query("counter_loop");
            if ($session_id != "") {
                $cond .= " and session_id='" . $session_id . "'";
            }

            $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

            $logo = Model::factory('logo')->getLogoByProfileId($profile_id);

            $profile_logo = "images/logo.png";

            if (!empty($logo) && isset($logo["logo_name"]) && file_exists($logo["logo_name"])) {
                $profile_logo = $logo["logo_name"];
            }

            $answer_time = Model::factory('formbuilder')->form_builder_answers_time($cond);

            $questions_arr = Model::factory('Formbuilder')->get_profile_question($profile_id);
            $html_content .= '<img src="' . URL::base() . $profile_logo . '" height="108" alt=""  /><br><hr>&nbsp;';

            $html_content .= '<div><b>Profile Number</b> : ' . $profile_id . '</div>';

            $html_content .= '<div><b>Profile Name</b> : ' . $profile["code_profile_name"] . '</div>';
            $html_content .= '<hr>';

            $html_content .= '&nbsp;<table width="100%"><tr><td>&nbsp;</td></tr><tr><td align="right" >Date/Time: ' . $answer_time[0]["date_time_formated"] . '</td></tr></table>';
            foreach ($questions_arr as $question_item) {
                $question_type_id = $question_item["question_type_id"];

                if ($question_type_id == "10") // heading
                {
                    $html_content .= '<h1>' . $question_item["question_text"] . '</h1>';
                } elseif ($question_type_id == "12") // subheading
                {
                    $html_content .= '<h3>' . $question_item["question_text"] . '</h3>';
                } elseif ($question_type_id == "2") /// question text
                {
                    if (isset($question_item["question_text"])) {
                        $html_content .= '<br>' . $question_item["question_text"] . '<br>';
                    }
                } else {
                    $question_id = $question_item["question_id"];

                    $answer_arr = Model::factory('Formbuilder')->form_builder_answers($cond . " and question_id='" . $question_id . "'");

                    foreach ($answer_arr as $answer_item) {
                        switch ($question_type_id) {
                            case "3": // Mutliple choise
                            case "4": // Checkboxes
                            case "5": // Drop Down Menu
                                $question_options = Model_formbuilder::get_question_option($question_id);

                                $question_option_text = "";
                                foreach ($question_options as $question_option) {
                                    if ($question_option_text == "") {
                                        $question_option_text = htmlspecialchars($question_option["option_name"]);
                                    } else {
                                        $question_option_text = $question_option_text . '<br>' . htmlspecialchars($question_option["option_name"]);
                                    }
                                }
                                //$html_content .=  "<b>Options:</b> ".$question_option_text."<br>";
                                break;

                            case "6": // Number Scale
                                $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $scale_from = $question_options[0]["option_name"];

                                $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $scale_to = $question_options[0]["option_name"];

                                //$html_content .=  "<b>Range:</b> From ".$scale_from." To ".$scale_to."<br>";

                                break;

                            case "7": // Grid

                                if ($answer_item["row_number"] == 0) //show question for once
                                {
                                    $_SESSION["grid"] = '<b>User Input:</b><br>';
                                }

                                $grid_count = Model_formbuilder::get_count_for_grid_type($question_id, $profile_id, $answer_item["session_id"]);

                                $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                if ($answer_item["row_number"] == 0) // show colums once
                                {
                                    $_SESSION["grid"] .= '<table border="0" cellpadding="6" width="50%" cellspacing="0">';

                                    $_SESSION["grid"] .= '<tr>';
                                    $_SESSION["grid"] .= '<td>&nbsp;</td>';
                                    for ($i = 0; $i < count($column_text); $i++) {
                                        $_SESSION["grid"] .= '<td align="center">' . $column_text[$i]["option_name"] . '</td>';
                                    }

                                    $_SESSION["grid"] .= '</tr>';
                                }

                                for ($i = 0; $i < count($row_text); $i++) {
                                    if ($answer_item["row_number"] == $i) {
                                        $_SESSION["grid"] .= '<tr>';
                                        $_SESSION["grid"] .= '<td>' . $row_text[$i]["option_name"] . '</td>';

                                        for ($j = 0; $j < count($column_text); $j++) {
                                            if ($answer_item["question_answer"] == $j) {
                                                $checked = '<img width="18" src="' . URL::base() . 'images/green_check_mark_icon.png" >';
                                            } else {
                                                $checked = '&nbsp;';
                                            }

                                            $_SESSION["grid"] .= '<td  align="center">' . $checked . '</td>';
                                        }

                                        $_SESSION["grid"] .= '</tr>';
                                    }
                                }

                                if (($grid_count - 1) == $answer_item["row_number"]) // last row;
                                {
                                    $_SESSION["grid"] .= '</table><br>';

                                    $html_content .= $_SESSION["grid"];
                                    unset($_SESSION["grid"]);
                                }
                                break;
                        } //end of switch

                        if ($question_type_id != 7) // not grid
                        {
                            if ($question_type_id == "16") //signature
                            {

                                $signature_image = explode(",", $answer_item["question_answer"]);
                                $signature_name_text = explode(":::", $answer_item["signature_name_text"]);
                                $signature_name_text2 = explode(":::", $answer_item["signature_employer_text"]);

                                for ($sig_cnt = 0; $sig_cnt < count($signature_image); $sig_cnt++) {
                                    if (isset($signature_name_text[$sig_cnt]) && $signature_name_text[$sig_cnt] != "") {
                                        $html_content .= "<br>Name<br><b>User Input:</b>" . $signature_name_text[$sig_cnt] . "<br>";
                                    }

                                    if (isset($signature_name_text2[$sig_cnt]) && $signature_name_text2[$sig_cnt] != "") {
                                        $html_content .= "<br>Employer/Company<br><b>User Input:</b>" . $signature_name_text2[$sig_cnt] . "<br>";
                                    }

                                    if (isset($question_item["question_text"])) {
                                        $html_content .= '<br>' . $question_item["question_text"] . '<br>';
                                    }

                                    if (file_exists('images/signature/' . $signature_image[$sig_cnt]) && filesize('images/signature/' . $signature_image[$sig_cnt]) > 0) {
                                        $html_content .= '<img src="' . URL::base() . 'images/signature/' . $signature_image[$sig_cnt] . '" /><br>';
                                    }
                                }
                            } else if ($question_type_id == "17") //upload button
                            {
                                if (isset($question_item["question_text"])) {
                                    $html_content .= '<br>' . $question_item["question_text"] . '<br>';
                                }

                                if ($answer_item["question_answer"] != "") {
                                    $attachment_arr = explode(",", $answer_item["question_answer"]);

                                    for ($k = 0; $k < count($attachment_arr); $k++) {
                                        if (file_exists('images/formbuilder_upload/' . $attachment_arr[$k]) && filesize('images/formbuilder_upload/' . $attachment_arr[$k]) > 0) {
                                            $html_content .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" >Click here to view</a><br>';
                                        }
                                    }
                                }
                                $html_content .= "<br>";
                            } else if ($question_type_id == "18") //participant name
                            {

                                $participant_arr = explode(':::', $answer_item["question_answer"]);
                                $participant_sign_image = explode(',', $answer_item["participant_signature_image"]);
                                $participant_employer_text = explode(':::', $answer_item['participant_employer_text']);

                                for ($p = 0; $p < count($participant_arr); $p++) {
                                    if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                        $html_content .= "<br>" . $question_item["question_text"] . '<br>';
                                    } else {
                                        $html_content .= '<br>Name<br>';
                                    }

                                    $html_content .= '<b>User Input:</b> ' . $participant_arr[$p] . "<br><br>";

                                    if (isset($participant_employer_text[$p]) && $participant_employer_text[$p] != "") {
                                        $html_content .= "<br>Employer/Company<br><b>User Input:</b>" . $participant_employer_text[$p] . "<br>";
                                    }

                                    if ($participant_sign_image[$p] != "" && file_exists('images/signature/' . $participant_sign_image[$p]) && filesize('images/signature/' . $participant_sign_image[$p]) > 0) {
                                        $html_content .= '<img src="' . URL::base() . 'images/signature/' . $participant_sign_image[$p] . '" /><br>';
                                    }
                                }
                            } else if ($question_type_id == "19") //location function
                            {
                                if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                    $html_content .= "<br>" . $question_item["question_text"] . '<br>';
                                }

                                $html_content .= '<b>User Input:</b> ' . $answer_item["question_answer"] . "<br>";
                            } else if ($question_type_id == "22") // SWMS Hazard/Risk
                            {
                                $Hazard = explode(":::", $answer_item["question_answer"]);

                                $html_content .= "<br>";
                                $i = 0;
                                $j = 0;
                                while ($i < count($Hazard)) {
                                    if ($j == 0) {
                                        if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                            $html_content .= "<br>" . $question_item["question_text"] . '<br><br>';
                                        }
                                        $html_content .= "Task | Activity";
                                    }
                                    if ($j == 1) {
                                        $html_content .= "Potential Hazards";
                                    }

                                    if ($j == 2) {
                                        $html_content .= "Risk Score (Before)";
                                    }

                                    if ($j == 3) {
                                        $html_content .= "Control Measures";
                                    }

                                    if ($j == 4) {
                                        $html_content .= "Risk Score (After)";
                                    }

                                    $html_content .= "<br><b>User Input:</b> " . $Hazard[$i] . "<br><br>";
                                    $i++;

                                    $j++;

                                    if ($j > 4) {
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
                                        if (file_exists('images/formbuilder_upload/' . $attachment_arr[$k]) && filesize('images/formbuilder_upload/' . $attachment_arr[$k]) > 0) {
                                            if ($doc_key !== false) {
                                                $html_content .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" >' . $arr_doc_title[$doc_key] . '</a><br>';
                                            } else {
                                                $html_content .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" >Click here to view</a><br>';
                                            }
                                        }
                                    }
                                }
                                $html_content .= "<br>";
                            }
                        } else if ($question_type_id == "25") {
                            echo '<p>' . $question_item["question_text"] . '</p>';
                            $checkinform_data = explode(":::", $answer_item["question_answer"]);
                            echo '<p>Visitor name : ' . (!empty($checkinform_data[0]) ? $checkinform_data[0] : '') . '</p>';
                            echo '<p>Visitor phone : ' . (!empty($checkinform_data[1]) ? $checkinform_data[1] : '') . '</p>';
                            echo '<p>Checkin Date : ' . (!empty($checkinform_data[2]) ? $checkinform_data[2] : '') . '</p>';
                            echo '<p>Time : ' . (!empty($checkinform_data[3]) ? $checkinform_data[3] : '') . '</p>';
                            echo '<p>Venue Address : ' . (!empty($checkinform_data[4]) ? $checkinform_data[4] : '') . '</p>';
                            echo '<p>Location address : ' . (!empty($checkinform_data[5]) ? $checkinform_data[5] : '') . '</p>';
                            if (!empty($checkinform_data[5]) && $checkinform_data[5] == 'Visitor') {
                                echo 'Vehicle Registration ID : ' . (!empty($checkinform_data[6]) ? $checkinform_data[6] : '') . '</p>';
                            } else if (!empty($checkinform_data[5]) && $checkinform_data[5] == 'Other') {
                                echo 'Other details : ' . (!empty($checkinform_data[6]) ? $checkinform_data[6] : '') . '</p>';
                            }

                            // echo '<b>User Input:</b> ' . str_replace(':::', '<br>', $answer_item["question_answer"]) . "<br><br>";

                        } else {
                            $html_content .= '<b>User Input:</b> ' . str_replace(":::", "<br>", htmlspecialchars($answer_item["question_answer"])) . "<br>";
                        } //end of if($question_type_id!=7 )
                    } //end of foreach($answer_arr as $answer_item)

                    if (count($answer_arr) == 0 && $question_type_id != "14" && $question_type_id != "11" && $question_type_id != 13 && $question_type_id != "16" && $question_type_id != "17") {
                        switch ($question_type_id) {
                            case "3": // Mutliple choise
                            case "4": // Checkboxes
                            case "5": // Drop Down Menu
                                $question_options = Model_formbuilder::get_question_option($question_id);

                                $question_option_text = "";
                                foreach ($question_options as $question_option) {
                                    if ($question_option_text == "") {
                                        $question_option_text = $question_option["option_name"];
                                    } else {
                                        $question_option_text = $question_option_text . "<br> " . $question_option["option_name"];
                                    }
                                }
                                //$html_content .= "<b>Options:</b><br> ".$question_option_text."<br>";
                                break;

                            case "6": // Number Scale
                                $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $scale_from = $question_options[0]["option_name"];

                                $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $scale_to = $question_options[0]["option_name"];

                                //$html_content .= "<b>Range:</b> From ".$scale_from." To ".$scale_to."<br>";

                                break;

                            case "7": // Grid
                                $html_content .= '<b>User Input:</b><br>';

                                $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $html_content .= "<table border='0' cellpadding='6' cellspacing='0'>";

                                $html_content .= "<tr>";
                                $html_content .= "<td>&nbsp;</td>";
                                for ($i = 0; $i < count($column_text); $i++) {
                                    $html_content .= "<td align='center'>" . $column_text[$i]["option_name"] . "</td>";
                                }

                                $html_content .= "</tr>";

                                for ($i = 0; $i < count($row_text); $i++) {
                                    $html_content .= "<tr>";
                                    $html_content .= "<td>" . $row_text[$i]["option_name"] . "</td>";

                                    for ($j = 0; $j < count($column_text); $j++) {
                                        $html_content .= "<td  align='center'>&nbsp;</td>";
                                    }

                                    $html_content .= "</tr>";
                                }

                                $html_content .= "</table><br>";

                                break;
                            case "18": // participant name
                                if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                    $html_content .= $question_item["question_text"] . '<br>';
                                } else {
                                    $html_content .= "Name<br>";
                                }

                                break;
                            case "19": // location function
                                if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                    $html_content .= $question_item["question_text"] . '<br>';
                                }

                                break;
                        }

                        if ($question_type_id != 7 && $question_type_id != 20 && $question_type_id != 21) //not grid, web link button and Document Button
                        {
                            $html_content .= '<b>User Input:</b><br><br>';
                        }
                    }
                }
            }
        }
        //$html_content = str_replace(URL::base(), ABS_PATH, $html_content); //used when test in local
        //echo $html_content;exit;

        // create new PDF document

        // ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        // require "PHPExcel/PHPExcel/Shared/PDF/tcpdf_6/tcpdf.php";

        // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // $pdf->SetPrintHeader(false);
        // $pdf->SetPrintFooter(true);
        // // set margins
        // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // // set auto page breaks
        // $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // // set image scale factor
        // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // // set font
        // $fontname = $pdf->addTTFfont('calibri.ttf', 'TrueTypeUnicode', '', 32);
        // $pdf->SetFont($fontname, '', 12);

        // //$pdf->SetFont('helvetica', '', 12);

        // // add a page
        // $pdf->AddPage();

        // // output the HTML content

        // $html_content = str_replace("'", "&#039;", $html_content);

        // $pdf->writeHTML($html_content, true, false, true, false, '');

        // $pdf->Output('allform_submission_response.pdf', 'D');
        $this->download_formsubmission_log_pdf($html_content, 'allform_submission_response.pdf');
    }

    public function action_export_participant()
    {
        // set title tag
        $this->template->head->title = "Participant List";

        $profile_id = $this->request->param("id");

        $add_edit_participant_list_arr = Model::factory('Formbuilder')->get_participant_list($profile_id);
        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);
        $logo = Model::factory('logo')->getLogoByProfileId($profile_id);

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";

        $workbook = new PHPExcel();

        $workbook->setActiveSheetIndex(0);

        $workbook->getActiveSheet()->getRowDimension('1')->setRowHeight(58);
        $workbook->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
        $workbook->getActiveSheet()->getColumnDimension('A')->setWidth(20);

        $workbook->getActiveSheet()->mergeCells('A1:B1');

        $workbook->getActiveSheet()->getStyle('A2:D2')->getFont()->setBold(true);
        $workbook->getActiveSheet()->getStyle('A2:D2')->getFont()->setSize(14);
        $workbook->getActiveSheet()->getStyle('A1:B1')->getFont()->setSize(16);
        $workbook->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $workbook->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $workbook->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $workbook->getActiveSheet()->getColumnDimension('D')->setWidth(20);

        $workbook->getActiveSheet()->setCellValue('A1', 'Profile ' . $profile_id . '. ' . $profile['code_profile_name']);
        $workbook->getActiveSheet()->setCellValue('A2', 'Participant Name ');
        $workbook->getActiveSheet()->setCellValue('B2', 'Employer/Company');
        $workbook->getActiveSheet()->setCellValue('C2', 'Due Date');
        $workbook->getActiveSheet()->setCellValue('D2', 'Form Received or overdue');

        $workbook->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $workbook->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $workbook->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $workbook->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        if (count($add_edit_participant_list_arr) > 0) {
            $row_num = 3;
            foreach ($add_edit_participant_list_arr as $participant) {
                //echo $participant["name"]."--".$participant["due_date_formated"]."--act".$participant["is_participated"]."--".$participant["diff_date"]."<br>";
                $status = "";
                if ($participant["is_participated"] == 1) {
                    $status = "Form Received";
                } else {
                    if ($participant["diff_date_created"] > 0) {
                        $status = "Overdue";
                    }
                }

                $workbook->getActiveSheet()->setCellValue("A" . $row_num, $participant["name"]);
                $workbook->getActiveSheet()->setCellValue("B" . $row_num, $participant["employer_cmp"]);
                $workbook->getActiveSheet()->setCellValue("C" . $row_num, $participant["due_date_formated"]);
                $workbook->getActiveSheet()->setCellValue("D" . $row_num, $status);
                $row_num++;
            }
        }

        //for logo in excel

        if (!empty($logo)) {
            if (file_exists($logo['logo_name'])) {
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('Logo');
                $objDrawing->setDescription('Logo');
                $logo = $logo['logo_name']; // Provide path to your logo file
                $objDrawing->setPath($logo);
                $objDrawing->setOffsetX(30); // setOffsetX works properly
                $objDrawing->setOffsetY(300); //setOffsetY has no effect
                $objDrawing->setCoordinates('C1');
                $objDrawing->setHeight(75); // logo height
                $objDrawing->setWorksheet($workbook->getActiveSheet());
            }
        }
        //for logo in excel

        //set background color
        $workbook->getActiveSheet()->getStyle('A2:C2')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'F7E1D2')));
        $workbook->getActiveSheet()->getStyle('A2:C2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        ob_end_clean();
        $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="participant_list.xls"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function action_export_form_analytics()
    {
        // set title tag
        $this->template->head->title = "Form Analytics";

        $profile_id = $this->request->param("id");

        $form_analytics_questions = Model::factory('formbuilder')->get_analytics_questions($profile_id);

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);
        $code_profile_name = $profile["code_profile_name"];

        if ($profile["user_id"] != $this->session->get('clientuserid')) {
            $this->session->set("errormessage", "You can export analytics of your own profile only.");
            $this->request->redirect('dashboard');
            exit;
        }

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";

        $workbook = new PHPExcel();

        $sheet = $workbook->getActiveSheet();

        $workbook->setActiveSheetIndex(0);
        $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Profile ' . $profile_id . "." . $code_profile_name);

        $workbook->getActiveSheet()->getRowDimension('1')->setRowHeight(28.5);

        $rows = array();

        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $workbook->getActiveSheet()->getColumnDimension('A')->setWidth(20);

        $row_num = 3;
        foreach ($form_analytics_questions as $form_analytics_question) {
            $answer_question_id = $form_analytics_question["question_id"];

            $question_options = explode(":::", $form_analytics_question["question_text"]);

            $current_question_order = 0;
            $current_ele_question_order_arr = Model::factory('formbuilder')->get_order_of_question($answer_question_id);
            if ($current_ele_question_order_arr) {
                $current_question_order = $current_ele_question_order_arr["question_order"];
            }

            $main_question = Model::factory('formbuilder')->get_question_by_answer_question_id($answer_question_id, $profile_id, $current_question_order);

            $question_text = $main_question[0]["question_text"];

            $workbook->getActiveSheet()->setCellValue('A' . $row_num, $question_text);
            $workbook->getActiveSheet()->getStyle('A' . $row_num)->getFont()->setBold(true);

            if ($form_analytics_question["question_type_id"] == 6) //number scale
            {
                $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                $question_options = Model::factory('formbuilder')->get_question_option($answer_question_id, $question_option_type_id);

                $scale_from = $question_options[0]["option_name"];

                $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                $question_options = Model::factory('formbuilder')->get_question_option($answer_question_id, $question_option_type_id);

                $scale_to = $question_options[0]["option_name"];

                $total_response_count = 0;
                for ($i = $scale_from; $i <= $scale_to; $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id, $answer_question_id, $i);

                    $total_response_count = $total_response_count + count($form_analytics_answers);
                }

                for ($i = $scale_from; $i <= $scale_to; $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id, $answer_question_id, $i);

                    $workbook->getActiveSheet()->setCellValue('B' . $row_num, $i);
                    $workbook->getActiveSheet()->setCellValue('C' . $row_num, count($form_analytics_answers));

                    if ($total_response_count != "0") {
                        $percent = (count($form_analytics_answers) * 100) / $total_response_count;
                    } else {
                        $percent = "0";
                    }

                    $percent = round($percent, 1);

                    $workbook->getActiveSheet()->setCellValue('D' . $row_num, $percent . "%");

                    $workbook->getActiveSheet()->getStyle('B' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getActiveSheet()->getStyle('C' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $workbook->getActiveSheet()->getStyle('D' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $row_num++;
                }
            } else {
                $total_response_count = 0;
                for ($i = 0; $i < count($question_options); $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id, $answer_question_id, addslashes($question_options[$i]));
                    $total_response_count = $total_response_count + count($form_analytics_answers);
                }

                for ($i = 0; $i < count($question_options); $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id, $answer_question_id, addslashes($question_options[$i]));

                    $workbook->getActiveSheet()->setCellValue('B' . $row_num, $question_options[$i]);
                    $workbook->getActiveSheet()->setCellValue('C' . $row_num, count($form_analytics_answers));

                    if ($total_response_count != "0") {
                        $percent = (count($form_analytics_answers) * 100) / $total_response_count;
                    } else {
                        $percent = "0";
                    }

                    $percent = round($percent, 1);

                    $workbook->getActiveSheet()->setCellValue('D' . $row_num, $percent . "%");

                    $workbook->getActiveSheet()->getStyle('B' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getActiveSheet()->getStyle('C' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $workbook->getActiveSheet()->getStyle('D' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $row_num++;
                }
            }

            $row_num++;
        } //end of foreach($form_analytics_questions as $form_analytics_question)
        //exit;

        $workbook->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $workbook->getActiveSheet()->getColumnDimension('B')->setWidth(15);

        $workbook->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $workbook->getActiveSheet()->getColumnDimension('D')->setWidth(15);

        ob_end_clean();
        $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="form_analytics.xls"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function action_export_form_cumulative_analytics()
    {
        // set title tag
        $this->template->head->title = "Form Analytics";
        $cumulative_profile_id = $_REQUEST['cumulative_code_profile'];
        $name = $_REQUEST['name'];
        $description = $_REQUEST['description'];
        $profile_id_arr = explode(",", $cumulative_profile_id);
        $profile = Model::factory('profiles')->getprofileById($profile_id_arr[0]);
        //$profile_id= $this->request->param("id");
        //$form_analytics_questions=  Model::factory('formbuilder')->get_analytics_questions($profile_id);
        //$profile =  Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        foreach ($profile_id_arr as $profile_id) {
            $all_profile[] = Model::factory('profiles')->getprofileById($profile_id);
            $formbuilder_ans = Model::factory('formbuilder')->get_profile_answers($profile_id);

            if ($formbuilder_ans) {
                $form_submission_count_arr[$profile_id] = count($formbuilder_ans) . '<br>';
            }

            $form_analytics_questions[] = Model::factory('formbuilder')->get_analytics_questions($profile_id);
        }

        //$code_profile_name = $profile["code_profile_name"];

        if ($profile["user_id"] != $this->session->get('clientuserid')) {
            $this->session->set("errormessage", "You can export analytics of your own profile only.");
            $this->request->redirect('dashboard');
            exit;
        }

        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'vendor/');
        require "PHPExcel/PHPExcel.php";

        $workbook = new PHPExcel();
        $sheet = $workbook->getActiveSheet();

        $workbook->setActiveSheetIndex(0);
        $code_profile_name = "";
        $rowHeight = 0;
        foreach ($all_profile as $all) {
            $code_profile_name .= "\nProfile " . $all['id'] . ". " . ucwords($all['code_profile_name']);
            $rowHeight += 28.5;
        }
        $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $name);
        $workbook->getActiveSheet()->setCellValueByColumnAndRow(1, 1, $description);
        $workbook->getActiveSheet()->mergeCells('B1:D1');
        $workbook->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
        //$workbook->getActiveSheet()->getStyle('B1')->getAlignment()->setWrapText(true);
        $workbook->getActiveSheet()->setCellValueByColumnAndRow(0, 2, $code_profile_name);
        $workbook->getActiveSheet()->getRowDimension('1')->setRowHeight(20.5);
        $workbook->getActiveSheet()->getRowDimension('2')->setRowHeight($rowHeight);

        $rows = array();
        //$workbook->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $workbook->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $workbook->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
        $workbook->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
        $workbook->getActiveSheet()->getStyle('B1')->getFont()->setSize(14);
        $workbook->getActiveSheet()->getColumnDimension('A')->setWidth(20);

        $row_num = 3;

        $a = 0;
        $a1 = 0;
        $dropdown_count_arr = array();
        $radio_count_arr = array();
        $scale_count_arr = array();

        foreach ($form_analytics_questions as $multi_analytics_questions) {
            foreach ($multi_analytics_questions as $multi_analytics_question) {
                $profile_id = $multi_analytics_question['profile_id'];
                $answer_question_id = $multi_analytics_question["question_id"];
                $question_options = explode(":::", $multi_analytics_question["question_text"]);

                if ($multi_analytics_question["question_type_id"] == 6) //number scale
                {
                    $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                    $question_options = Model_formbuilder::get_question_option($answer_question_id, $question_option_type_id);
                    $scale_from = $question_options[0]["option_name"];
                    $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                    $question_options = Model_formbuilder::get_question_option($answer_question_id, $question_option_type_id);
                    $scale_to = $question_options[0]["option_name"];

                    for ($i = $scale_from; $i <= $scale_to; $i++) {
                        $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id, $answer_question_id, $i);
                        $scale_count_arr[$profile_id][] = count($form_analytics_answers);
                    }
                } else if ($multi_analytics_question["question_type_id"] == 5) //dropdown
                {
                    for ($i = 0; $i < count($question_options); $i++) {
                        $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id, $answer_question_id, addslashes($question_options[$i]));
                        $drop_count_arr[$profile_id][] = count($form_analytics_answers);
                    }
                } else {
                    for ($i = 0; $i < count($question_options); $i++) {
                        $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id, $answer_question_id, addslashes($question_options[$i]));
                        $all_count_arr[$profile_id][] = count($form_analytics_answers);
                    }
                }
            }
        }
        $b = 0;
        $b1 = 0;
        foreach ($form_analytics_questions[0] as $form_analytics_question) {
            $answer_question_id = $form_analytics_question["question_id"];
            $question_options = explode(":::", $form_analytics_question["question_text"]);
            $current_question_order = 0;
            $current_ele_question_order_arr = Model::factory('formbuilder')->get_order_of_question($answer_question_id);
            if ($current_ele_question_order_arr) {
                $current_question_order = $current_ele_question_order_arr["question_order"];
            }

            $main_question = Model::factory('formbuilder')->get_question_by_answer_question_id($answer_question_id, $profile_id_arr[0], $current_question_order);
            $question_text = strip_tags($main_question[0]["question_text"]);
            $workbook->getActiveSheet()->setCellValue('A' . $row_num, $question_text);
            $workbook->getActiveSheet()->getStyle('A' . $row_num)->getFont()->setBold(true);
            error_reporting(0);
            if ($form_analytics_question["question_type_id"] == 6) //number scale
            {
                $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                $question_options = Model::factory('formbuilder')->get_question_option($answer_question_id, $question_option_type_id);
                $scale_from = $question_options[0]["option_name"];
                $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column
                $question_options = Model::factory('formbuilder')->get_question_option($answer_question_id, $question_option_type_id);
                $scale_to = $question_options[0]["option_name"];
                $total_response_count = 0;

                $sumArray = array();
                foreach ($scale_count_arr as $subArray) {
                    foreach ($subArray as $id => $value) {
                        $sumArray[$id] += $value;
                    }
                }

                $k1 = 0;
                for ($i = $scale_from; $i <= $scale_to; $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id_arr[0], $answer_question_id, $i);
                    $total_response_count = $total_response_count + $sumArray[$k1];
                    $k1++;
                }
                $k = 0;
                for ($i = $scale_from; $i <= $scale_to; $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id_arr[0], $answer_question_id, $i);
                    $workbook->getActiveSheet()->setCellValue('B' . $row_num, $i);
                    $workbook->getActiveSheet()->setCellValue('C' . $row_num, $sumArray[$k]);
                    if ($total_response_count != "0") {
                        $percent = ($sumArray[$k] * 100) / $total_response_count;
                    } else {
                        $percent = "0";
                    }

                    $percent = round($percent, 1);
                    $workbook->getActiveSheet()->setCellValue('D' . $row_num, $percent . "%");
                    $workbook->getActiveSheet()->getStyle('B' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getActiveSheet()->getStyle('C' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $workbook->getActiveSheet()->getStyle('D' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $row_num++;
                    $k++;
                }
            } else if ($form_analytics_question["question_type_id"] == 5) // dropdown
            {
                $total_response_count = 0;

                $dropsumArray = array();
                foreach ($drop_count_arr as $subArray) {
                    foreach ($subArray as $id => $value) {
                        $dropsumArray[$id] += $value;
                    }
                }
                for ($i = 0; $i < count($question_options); $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id_arr[0], $answer_question_id, addslashes($question_options[$i]));
                    $total_response_count = $total_response_count + $dropsumArray[$a1];
                    $a1++;
                }
                for ($i = 0; $i < count($question_options); $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id_arr[0], $answer_question_id, addslashes($question_options[$i]));
                    $workbook->getActiveSheet()->setCellValue('B' . $row_num, $question_options[$i]);
                    $workbook->getActiveSheet()->setCellValue('C' . $row_num, $dropsumArray[$a]);
                    if ($total_response_count != "0") {
                        $percent = ($dropsumArray[$a] * 100) / $total_response_count;
                    } else {
                        $percent = "0";
                    }

                    $percent = round($percent, 1);
                    $workbook->getActiveSheet()->setCellValue('D' . $row_num, $percent . "%");
                    $workbook->getActiveSheet()->getStyle('B' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getActiveSheet()->getStyle('C' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $workbook->getActiveSheet()->getStyle('D' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $row_num++;
                    $a++;
                }
            } else {
                $total_response_count = 0;

                $allsumArray = array();
                foreach ($all_count_arr as $subArray) {
                    foreach ($subArray as $id => $value) {
                        $allsumArray[$id] += $value;
                    }
                }

                for ($i = 0; $i < count($question_options); $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id_arr[0], $answer_question_id, addslashes($question_options[$i]));
                    $total_response_count = $total_response_count + $allsumArray[$b1];
                    $b1++;
                }
                for ($i = 0; $i < count($question_options); $i++) {
                    $form_analytics_answers = Model::factory('formbuilder')->get_analytics_answers($profile_id_arr[0], $answer_question_id, addslashes($question_options[$i]));
                    $workbook->getActiveSheet()->setCellValue('B' . $row_num, $question_options[$i]);
                    $workbook->getActiveSheet()->setCellValue('C' . $row_num, $allsumArray[$b]);
                    if ($total_response_count != "0") {
                        $percent = ($allsumArray[$b] * 100) / $total_response_count;
                    } else {
                        $percent = "0";
                    }

                    $percent = round($percent, 1);
                    $workbook->getActiveSheet()->setCellValue('D' . $row_num, $percent . "%");
                    $workbook->getActiveSheet()->getStyle('B' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    $workbook->getActiveSheet()->getStyle('C' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $workbook->getActiveSheet()->getStyle('D' . $row_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $row_num++;
                    $b++;
                }
            }
            $row_num++;
        } //end of foreach($form_analytics_questions as $form_analytics_question)
        //exit;

        $workbook->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $workbook->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $workbook->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $workbook->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        ob_end_clean();
        $writer = PHPExcel_IOFactory::createWriter($workbook, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="cumulative_code_profile_analytics.xls"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer->save('php://output');
        exit;
    }
    public function action_getCumulativeProfile()
    {

        $profiles = $_POST['profiles'];

        if (count($profiles) < 2) {
            $msg = '<span style="float: left; width: 80%; font-size: 14px; margin-top: 10px;">Please select more than one profile</span>';
            print(json_encode($msg));
        } else {
            $first_profile = $profiles[0];
            $formbuilder_que = Model::factory('formbuilder')->get_cumulative_question_option($first_profile);

            $first_profile_que_count = count($formbuilder_que);

            $first_profile_question_text = Model::factory('formbuilder')->get_cumulative_question_text($first_profile);
            $first_profile_question_text_count = count($first_profile_question_text);

            $total_fields_first_profile = Model::factory('formbuilder')->get_total_fields_count($first_profile);

            //print_r($total_fields_first_profile);
            $all_fields = $total_fields_first_profile[0]['total_fields'];
            $exist_field = $first_profile_que_count;

            $pos = array_search($first_profile, $profiles);
            unset($profiles[$pos]);

            $unmatch_profile = array();
            $matched_profile = array();

            foreach ($profiles as $profile) {
                $formbuilder_que_other = Model::factory('formbuilder')->get_cumulative_question_option($profile);
                if ($first_profile_que_count != count($formbuilder_que_other)) {
                    $unmatch_profile[] = $profile;
                } else {
                    $matched_profile[] = $profile;
                }
            }

            if ($all_fields != $exist_field) {
                array_push($unmatch_profile, $first_profile);
            }
            /*echo $first_profile_question_text_count.'....'.$first_profile_que_count;

            if($first_profile_question_text_count != $first_profile_que_count) {
            if(!in_array($first_profile, $unmatch_profile)) {
            array_push($unmatch_profile, $first_profile);
            }
            }*/

            if ($first_profile_que_count == 0 && !in_array($first_profile, $unmatch_profile)) {
                array_push($unmatch_profile, $first_profile);
            }
            $find_total_fields = array();
            $find_matched_que_text = array();
            $find_matched_question = array();
            foreach ($matched_profile as $matched) {

                $find_total_fields[$matched] = Model::factory('formbuilder')->get_total_fields_count($matched);

                $find_matched_que_text[$matched] = Model::factory('formbuilder')->get_cumulative_question_text_count($matched);

                $find_matched_question[$matched] = Model::factory('formbuilder')->get_cumulative_question_option($matched);

                if ($find_matched_que_text[$matched][0]['profile_id'] == "" && !in_array($matched, $unmatch_profile)) {
                    array_push($unmatch_profile, $matched);
                }
            }

            $matched_arr_keys = array_keys($find_matched_question);
            $match_profile_arr = array();
            for ($i = 0; $i < count($matched_arr_keys); $i++) {

                foreach ($find_total_fields as $id => $total_count) {
                    if ($total_count[0]['total_fields'] != $all_fields) {
                        if (!in_array($id, $unmatch_profile)) {
                            array_push($unmatch_profile, $id);
                        }
                    }
                }

                foreach ($find_matched_que_text[$matched_arr_keys[$i]] as $matched_text) {

                    if ($matched_text['question_text_count'] != $first_profile_question_text_count) {
                        if (!in_array($matched_text['profile_id'], $unmatch_profile)) {
                            array_push($unmatch_profile, $matched_text['profile_id']);
                        }
                    }
                }

                foreach ($find_matched_question[$matched_arr_keys[$i]] as $arr) {
                    if ($arr['radio'] != $formbuilder_que[0]['radio']) {
                        if (!in_array($arr['profile_id'], $unmatch_profile)) {
                            array_push($unmatch_profile, $arr['profile_id']);
                        }
                    } else if ($arr['dropdown'] != $formbuilder_que[0]['dropdown']) {
                        if (!in_array($arr['profile_id'], $unmatch_profile)) {
                            array_push($unmatch_profile, $arr['profile_id']);
                        }
                    } else if ($arr['scale'] != $formbuilder_que[0]['scale']) {
                        if (!in_array($arr['profile_id'], $unmatch_profile)) {
                            array_push($unmatch_profile, $arr['profile_id']);
                        }
                    } else {
                        if (!in_array($arr['profile_id'], $match_profile_arr)) {
                            array_push($match_profile_arr, $arr['profile_id']);
                        }
                    }
                }
            }
            foreach ($first_profile_question_text as $que_text) {
                $first_que_text[] = strip_tags($que_text['question_text']);
            }

            foreach ($formbuilder_que as $first) {
                if ($first['question_type_id'] == 3) { // multiplechoises
                    $first_radio_arr[] = $first;
                } else if ($first['question_type_id'] == 4) { // checkbox
                    $first_checkbox_arr[] = $first;
                } else if ($first['question_type_id'] == 5) { // dropdown
                    $first_drop_arr[] = $first;
                } else if ($first['question_type_id'] == 6) { // scale
                    $first_scale_arr[] = $first;
                } else if ($first['question_type_id'] == 7) { // grid
                    $first_grid_arr[] = $first;
                } else if ($first['question_type_id'] != 2) {
                    $first_other_field_arr[] = $first['question_type_id'];
                }
            }
            // get first profile's other fields
            if (!empty($first_other_field_arr)) :
                foreach ($first_other_field_arr as $other) {
                    $results = Model::factory('formbuilder')->get_cumulative_profile_other_que_type($other);
                    $get_other_fields_arr[] = $results[0]['question_type'];
                }
            endif;
            //                print_r($get_other_fields_arr);

            foreach ($match_profile_arr as $match_pr) {
                $match_question[$match_pr] = Model::factory('formbuilder')->get_cumulative_question_option($match_pr);
            }

            if (!empty($match_question)) :
                $match_question_key = array_keys($match_question);
                $get_all_que_text = array();
                $get_all_que_text_arr = array();
                $get_all_other_field = array();
                $get_all_other_field_arr = array();
                for ($i = 0; $i < count($match_question_key); $i++) {
                    $a = 0;
                    foreach ($match_question[$match_question_key[$i]] as $match_arr) {
                        $get_all_que_text[$match_arr['profile_id']] = Model::factory('formbuilder')->get_cumulative_question_text($match_arr['profile_id']);
                        if (!empty($get_all_que_text[$match_arr['profile_id']])) {
                            $get_all_que_text_arr[$match_arr['profile_id']] = $get_all_que_text[$match_arr['profile_id']];
                        } else {
                            if (!in_array($match_arr['profile_id'], $unmatch_profile)) {
                                array_push($unmatch_profile, $match_arr['profile_id']);
                            }
                        }

                        if ($match_arr['question_type_id'] == 3) { // multiple choise
                            $get_profile_radio_option[][$match_arr['profile_id']] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($match_arr['question_id']);
                        } else if ($match_arr['question_type_id'] == 4) { // checkbox
                            $get_profile_checkbox_option[][$match_arr['profile_id']] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($match_arr['question_id']);
                        } else if ($match_arr['question_type_id'] == 5) { // dropdown
                            $get_profile_drop_option[][$match_arr['profile_id']] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($match_arr['question_id']);
                        } else if ($match_arr['question_type_id'] == 6) { // scale
                            $get_profile_scale_option[][$match_arr['profile_id']] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($match_arr['question_id']);
                        } else if ($match_arr['question_type_id'] == 7) { // grid
                            $get_profile_grid_option[][$match_arr['profile_id']] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($match_arr['question_id']);
                        } else if ($match_arr['question_type_id'] != 2) {
                            $other_field_arr[$match_arr['profile_id']][] = $match_arr['question_type_id'];
                        }
                        $a++;
                    }
                }
            endif;
            // get matched profile's other fields
            if (!empty($other_field_arr)) :
                foreach ($other_field_arr as $match_id => $match_other) {
                    foreach ($match_other as $question_type) {
                        $results = Model::factory('formbuilder')->get_cumulative_profile_other_que_type($question_type);
                        $get_matched_other_fields_arr[$match_id][] = $results[0]['question_type'];
                    }
                }
            endif;

            if (!empty($first_radio_arr)) : // Multiple choise
                for ($r = 0; $r < count($first_radio_arr); $r++) {
                    $get_first_radio[] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($first_radio_arr[$r]['question_id']);
                }
            endif;
            if (!empty($first_checkbox_arr)) : // checkbox
                for ($r = 0; $r < count($first_checkbox_arr); $r++) {
                    $get_first_checkbox[] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($first_checkbox_arr[$r]['question_id']);
                }
            endif;
            if (!empty($first_drop_arr)) : // dropdown
                for ($r = 0; $r < count($first_drop_arr); $r++) {
                    $get_first_drop[] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($first_drop_arr[$r]['question_id']);
                }
            endif;
            if (!empty($first_scale_arr)) : // scale
                for ($r = 0; $r < count($first_scale_arr); $r++) {
                    $get_first_scale[] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($first_scale_arr[$r]['question_id']);
                }
            endif;
            if (!empty($first_grid_arr)) : // grid
                for ($r = 0; $r < count($first_grid_arr); $r++) {
                    $get_first_grid[] = Model::factory('formbuilder')->get_cumulative_option_from_question_id($first_grid_arr[$r]['question_id']);
                }
            endif;

            function in_array_r($needle, $haystack, $strict = false)
            {
                foreach ($haystack as $item) {
                    if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
                        return true;
                    }
                }
                return false;
            }
            $k = 0;
            $success_arr = array($first_profile);
            $all_profile_text_arr = array();

            //print_r($get_other_fields_arr);
            //print_r($get_matched_other_fields_arr);
            if (!empty($get_matched_other_fields_arr)) :
                foreach ($get_matched_other_fields_arr as $mid => $main_arr) {
                    foreach ($main_arr as $que_type) {

                        if (in_array_r($que_type, $get_other_fields_arr)) {
                            if (!in_array($mid, $success_arr)) {
                                array_push($success_arr, $mid);
                            }
                        } else {

                            if (!in_array($mid, $unmatch_profile)) {
                                array_push($unmatch_profile, $mid);
                            }
                        }
                    }
                }
            endif;
            error_reporting(0);
            if (!empty($get_all_que_text_arr)) :
                for ($i = 0; $i < count($matched_arr_keys); $i++) {
                    if (!empty($get_all_que_text_arr)) :
                        foreach ($get_all_que_text_arr[$matched_arr_keys[$i]] as $profile_que_text) {
                            $all_profile_text_arr[$profile_que_text['profile_id']][] = strip_tags($profile_que_text['question_text']);
                        }
                    endif;
                    //
                }
                //print_r($all_profile_text_arr);

                foreach ($all_profile_text_arr as $profile_key => $value) {
                    foreach ($value as $value_text) {
                        foreach ($first_que_text as $first_text) {
                            if (in_array_r($first_text, $value)) {
                                if (!in_array($profile_key, $success_arr)) {
                                    array_push($success_arr, $profile_key);
                                }
                            } else {
                                if (!in_array($profile_key, $unmatch_profile)) {
                                    array_push($unmatch_profile, $profile_key);
                                }
                            }
                        }
                    }
                    //print_r($value);
                }
            endif;
            //print_r($first_que_text);
            //print_r($all_profile_text_arr);
            if (!empty($get_profile_radio_option)) : // Multiple choise
                foreach ($get_profile_radio_option as $main_arr) {
                    foreach ($main_arr as $pid => $second_arr) {
                        foreach ($second_arr as $key => $val) {
                            if (in_array_r($val['option_name'], $get_first_radio)) {
                                // return true (profile match)
                                if (!in_array($pid, $success_arr)) {
                                    array_push($success_arr, $pid);
                                }
                            } else {
                                // return false (profile not match)
                                if (!in_array($pid, $unmatch_profile)) {
                                    array_push($unmatch_profile, $pid);
                                }
                            }
                        }
                    }
                    $k++;
                }
            endif;
            if (!empty($get_profile_checkbox_option)) : // Checkbox
                foreach ($get_profile_checkbox_option as $main_arr) {
                    foreach ($main_arr as $pid => $second_arr) {
                        foreach ($second_arr as $key => $val) {
                            if (in_array_r($val['option_name'], $get_first_checkbox)) {
                                if (!in_array($pid, $success_arr)) {
                                    array_push($second_arr, $pid);
                                }
                            } else {
                                if (!in_array($pid, $unmatch_profile)) {
                                    array_push($unmatch_profile, $pid);
                                }
                            }
                        }
                    }
                }
            endif;

            if (!empty($get_profile_drop_option)) : // Dropdown
                foreach ($get_profile_drop_option as $main_arr) {
                    foreach ($main_arr as $pid => $second_arr) {
                        foreach ($second_arr as $key => $val) {
                            if (in_array_r($val['option_name'], $get_first_drop)) {
                                // return true (profile match)
                                if (!in_array($pid, $success_arr)) {
                                    array_push($success_arr, $pid);
                                }
                            } else {
                                // return false (profile not match)
                                if (!in_array($pid, $unmatch_profile)) {
                                    array_push($unmatch_profile, $pid);
                                }
                            }
                        }
                    }
                    $k++;
                }
            endif;

            if (!empty($get_profile_scale_option)) : // Scale
                foreach ($get_profile_scale_option as $main_arr) {
                    foreach ($main_arr as $pid => $second_arr) {
                        foreach ($second_arr as $key => $val) {
                            if (in_array_r($val['option_name'], $get_first_scale)) {
                                // return true (profile match)
                                if (!in_array($pid, $success_arr)) {
                                    array_push($success_arr, $pid);
                                }
                            } else {
                                // return false (profile not match)
                                if (!in_array($pid, $unmatch_profile)) {
                                    array_push($unmatch_profile, $pid);
                                }
                            }
                        }
                    }
                    $k++;
                }
            endif;

            if (!empty($get_profile_grid_option)) : // Grid
                foreach ($get_profile_grid_option as $main_arr) {
                    foreach ($main_arr as $pid => $second_arr) {
                        foreach ($second_arr as $key => $val) {
                            if (in_array_r($val['option_name'], $get_first_grid)) {
                                // return true (profile match)
                                if (!in_array($pid, $success_arr)) {
                                    array_push($success_arr, $pid);
                                }
                            } else {
                                // return false (profile not match)
                                if (!in_array($pid, $unmatch_profile)) {
                                    array_push($unmatch_profile, $pid);
                                }
                            }
                        }
                    }
                    $k++;
                }
            endif;

            if (!empty($unmatch_profile) && count($unmatch_profile) > 0) {
                $err_flag_arr_str = implode(",", $unmatch_profile);
                $msg = "Cumulative analytics cannot be calculated because code profile No. '" . $err_flag_arr_str . "' not compatible with the selected code profile group<br>";
                $msg .= "Cumulative reports can only be generated for code profiles with identical forms.";
                print(json_encode($msg));
            } else {
                $success_str = implode(',', $success_arr);

                print(json_encode(array("success" => $success_str)));
            }
        }
        exit;
    }
    public function action_get_missing_user_email_data()
    {
        set_time_limit(0);
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');
        $profiles = Model::factory('formbuilder')->get_missing_mail_id_record();

        $profile_id = array_map(function ($v) {
            return $v['id'];
        }, $profiles);

        // foreach($profile_id as $profile){
        //     $form_id  = Model::factory('Formbuilder')->get_form_id($profile);
        // }

        if (!empty($profile_id) && count($profile_id)) {
            $formbuilder_answer = Model::factory('formbuilder')->get_session_id_by_profile_id($profile_id);
            foreach ($formbuilder_answer as $form_builder_ans) {

                $this->find_missing_download($form_builder_ans['profile_id'], $form_builder_ans['session_id']);
            }
            exit;
        }
    }

    public function find_missing_download($profile_id, $session_id)
    {
        // set title tag
        $this->template->head->title = "Form Submission Log Export";

        $profile_id = $profile_id;

        $cond = " and profile_id='" . $profile_id . "' ";

        $session_id = $session_id;
        if ($session_id != "") {
            $cond .= " and session_id='" . $session_id . "'";
        }

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        $logo = Model::factory('logo')->getLogoByProfileId($profile_id);

        $profile_logo = "images/logo.png";

        if (!empty($logo) && isset($logo["logo_name"]) && file_exists($logo["logo_name"])) {
            $profile_logo = $logo["logo_name"];
        }

        $answer_time = Model::factory('formbuilder')->form_builder_answers_time($cond);

        $questions_arr = Model::factory('Formbuilder')->get_profile_question($profile_id);

        $html_content = '<img src="' . URL::base() . $profile_logo . '" height="108" alt=""  /><br><hr>&nbsp;';

        $html_content .= '<div><b>Profile Number</b> : ' . $profile_id . '</div>';

        $html_content .= '<div><b>Profile Name</b> : ' . $profile["code_profile_name"] . '</div>';
        $html_content .= '<hr>';

        $html_content .= '&nbsp;<table width="100%"><tr><td>&nbsp;</td></tr><tr><td align="right" >Date/Time: ' . $answer_time[0]["date_time_formated"] . '</td></tr></table>';
        foreach ($questions_arr as $question_item) {
            $question_type_id = $question_item["question_type_id"];

            if ($question_type_id == "10") // heading
            {
                $html_content .= '<h1>' . $question_item["question_text"] . '</h1>';
            } elseif ($question_type_id == "12") // subheading
            {
                $html_content .= '<h3>' . $question_item["question_text"] . '</h3>';
            } elseif ($question_type_id == "2") /// question text
            {
                if (isset($question_item["question_text"])) {
                    $html_content .= '<br>' . $question_item["question_text"] . '<br>';
                }
            } else {
                $question_id = $question_item["question_id"];

                $answer_arr = Model::factory('Formbuilder')->form_builder_answers($cond . " and question_id='" . $question_id . "'");

                foreach ($answer_arr as $answer_item) {

                    switch ($question_type_id) {
                        case "3": // Mutliple choise
                        case "4": // Checkboxes
                        case "5": // Drop Down Menu
                            $question_options = Model_formbuilder::get_question_option($question_id);

                            $question_option_text = "";
                            foreach ($question_options as $question_option) {
                                if ($question_option_text == "") {
                                    $question_option_text = htmlspecialchars($question_option["option_name"]);
                                } else {
                                    $question_option_text = $question_option_text . '<br>' . htmlspecialchars($question_option["option_name"]);
                                }
                            }
                            //$html_content .=  "<b>Options:</b> ".$question_option_text."<br>";
                            break;

                        case "6": // Number Scale
                            $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $scale_from = $question_options[0]["option_name"];

                            $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $scale_to = $question_options[0]["option_name"];

                            //$html_content .=  "<b>Range:</b> From ".$scale_from." To ".$scale_to."<br>";

                            break;

                        case "7": // Grid
                            if (false) {

                                if ($answer_item["row_number"] == 0) //show question for once
                                {
                                    $_SESSION["grid"] = '<b>User Input:</b><br>';
                                }

                                $grid_count = Model_formbuilder::get_count_for_grid_type($question_id, $profile_id, $answer_item["session_id"]);

                                $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                                $column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                                if ($answer_item["row_number"] == 0) // show colums once
                                {
                                    $_SESSION["grid"] .= '<table border="0" cellpadding="6" width="50%" cellspacing="0">';

                                    $_SESSION["grid"] .= '<tr>';
                                    $_SESSION["grid"] .= '<td>&nbsp;</td>';
                                    for ($i = 0; $i < count($column_text); $i++) {
                                        $_SESSION["grid"] .= '<td align="center">' . $column_text[$i]["option_name"] . '</td>';
                                    }

                                    $_SESSION["grid"] .= '</tr>';
                                }

                                for ($i = 0; $i < count($row_text); $i++) {
                                    if ($answer_item["row_number"] == $i) {
                                        $_SESSION["grid"] .= '<tr>';
                                        $_SESSION["grid"] .= '<td>' . $row_text[$i]["option_name"] . '</td>';

                                        for ($j = 0; $j < count($column_text); $j++) {
                                            if ($answer_item["question_answer"] == $j) {
                                                $checked = '<img width="18" src="' . URL::base() . 'images/green_check_mark_icon.png" >';
                                            } else {
                                                $checked = '&nbsp;';
                                            }

                                            $_SESSION["grid"] .= '<td  align="center">' . $checked . '</td>';
                                        }

                                        $_SESSION["grid"] .= '</tr>';
                                    }
                                }

                                if (($grid_count - 1) == $answer_item["row_number"]) // last row;
                                {
                                    $_SESSION["grid"] .= '</table><br>';

                                    $html_content .= $_SESSION["grid"];
                                    unset($_SESSION["grid"]);
                                }
                            }
                            break;
                    } //end of switch

                    if ($question_type_id != 7) // not grid
                    {
                        if ($question_type_id == "16") //signature
                        {

                            $signature_image = explode(",", $answer_item["question_answer"]);
                            $signature_name_text = explode(":::", $answer_item["signature_name_text"]);
                            $signature_name_text2 = explode(":::", $answer_item["signature_employer_text"]);
                            $signature_name_text2 = explode(":::", $answer_item["signature_employer_text"]);
                            $signature_name_text3 = explode(":::", $answer_item["signature_email_text"]);
                            $signature_name_text4 = explode(":::", $answer_item["signature_phone_text"]);

                            for ($sig_cnt = 0; $sig_cnt < count($signature_image); $sig_cnt++) {
                                if (isset($signature_name_text[$sig_cnt]) && $signature_name_text[$sig_cnt] != "") {
                                    $html_content .= "<br>Name<br><b>User Input:</b>" . $signature_name_text[$sig_cnt] . "<br>";
                                }

                                if (isset($signature_name_text2[$sig_cnt]) && $signature_name_text2[$sig_cnt] != "") {
                                    $html_content .= "<br>Employer/Company<br><b>User Input:</b>" . $signature_name_text2[$sig_cnt] . "<br>";
                                }
                                if (isset($signature_name_text3[$sig_cnt]) && $signature_name_text3[$sig_cnt] != "") {
                                    $html_content .= "<br>Email<br><b>User Input:</b>" . $signature_name_text3[$sig_cnt] . "<br>";
                                }
                                if (isset($signature_name_text4[$sig_cnt]) && $signature_name_text4[$sig_cnt] != "") {
                                    $html_content .= "<br>Phone<br><b>User Input:</b>" . $signature_name_text4[$sig_cnt] . "<br>";
                                }

                                if (isset($question_item["question_text"])) {
                                    $html_content .= '<br>' . $question_item["question_text"] . '<br>';
                                }

                                if (file_exists('images/signature/' . $signature_image[$sig_cnt]) && filesize('images/signature/' . $signature_image[$sig_cnt]) > 0) {
                                    $html_content .= '<img src="' . URL::base() . 'images/signature/' . $signature_image[$sig_cnt] . '" /><br>';
                                }
                            }
                        } else if ($question_type_id == "17") //upload button
                        {
                            if (isset($question_item["question_text"])) {
                                $html_content .= '<br>' . $question_item["question_text"] . '<br>';
                            }

                            if ($answer_item["question_answer"] != "") {
                                $attachment_arr = explode(",", $answer_item["question_answer"]);

                                for ($k = 0; $k < count($attachment_arr); $k++) {
                                    if (file_exists('images/formbuilder_upload/' . $attachment_arr[$k]) && filesize('images/formbuilder_upload/' . $attachment_arr[$k]) > 0) {
                                        $html_content .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" >Click here to view</a><br>';
                                    }
                                }
                            }
                            $html_content .= "<br>";
                        } else if ($question_type_id == "18") //participant name
                        {

                            $participant_arr = explode(':::', $answer_item["question_answer"]);
                            $participant_sign_image = explode(',', $answer_item["participant_signature_image"]);
                            $participant_employer_text = explode(':::', $answer_item['participant_employer_text']);

                            for ($p = 0; $p < count($participant_arr); $p++) {
                                if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                    $html_content .= "<br>" . $question_item["question_text"] . '<br>';
                                } else {
                                    $html_content .= '<br>Name<br>';
                                }

                                $html_content .= '<b>User Input:</b> ' . $participant_arr[$p] . "<br><br>";

                                if (isset($participant_employer_text[$p]) && $participant_employer_text[$p] != "") {
                                    $html_content .= "<br>Employer/Company<br><b>User Input:</b>" . $participant_employer_text[$p] . "<br>";
                                }

                                if ($participant_sign_image[$p] != "" && file_exists('images/signature/' . $participant_sign_image[$p]) && filesize('images/signature/' . $participant_sign_image[$p]) > 0) {
                                    $html_content .= '<img src="' . URL::base() . 'images/signature/' . $participant_sign_image[$p] . '" /><br>';
                                }
                            }
                        } else if ($question_type_id == "19") //location function
                        {
                            if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                $html_content .= "<br>" . $question_item["question_text"] . '<br>';
                            }

                            $html_content .= '<b>User Input:</b> ' . $answer_item["question_answer"] . "<br>";
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

                            $html_content .= "<br>";
                            $i = 0;
                            $j = 0;
                            while ($i < count($Hazard)) {
                                if ($j == 0) {
                                    if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                        $html_content .= "<br>" . $question_item["question_text"] . '<br><br>';
                                    }
                                    $html_content .= "Task | Activity";
                                }
                                if ($j == 1) {
                                    $html_content .= "Potential Hazards";
                                }

                                if ($j == 2) {
                                    $html_content .= "Risk Score (Before)";
                                }

                                if ($j == 3) {

                                    if ($has_attachment && !empty($attachment_array)) {
                                        foreach ($attachment_array as $key => $attachment) {

                                            $html_content .= 'Attachment : <a target="_blank" href="' . url::base() . 'images/formbuilder_upload/' . $attachment . '"> View Attachment</a><br>';
                                        }
                                    }
                                }

                                if ($j == 4) {
                                    $html_content .= "Control Measures";
                                }

                                if ($j == 5) {
                                    $html_content .= "Risk Score (After)";
                                }


                                $html_content .= "<br><b>User Input:</b> " . $Hazard[$i] . "<br><br>";
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
                                    if (file_exists('images/formbuilder_upload/' . $attachment_arr[$k]) && filesize('images/formbuilder_upload/' . $attachment_arr[$k]) > 0) {
                                        if ($doc_key !== false) {
                                            $html_content .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" >' . $arr_doc_title[$doc_key] . '</a><br>';
                                        } else {
                                            $html_content .= '<a href="' . URL::base() . 'images/formbuilder_upload/' . $attachment_arr[$k] . '" >Click here to view</a><br>';
                                        }
                                    }
                                }
                            }
                            $html_content .= "<br>";
                        } else {
                            $html_content .= '<b>User Input:</b> ' . str_replace(":::", "<br>", htmlspecialchars($answer_item["question_answer"])) . "<br>";
                        }
                    } //end of if($question_type_id!=7 )
                } //end of foreach($answer_arr as $answer_item)

                if (count($answer_arr) == 0 && $question_type_id != "14" && $question_type_id != "11" && $question_type_id != 13 && $question_type_id != "16" && $question_type_id != "17") {
                    switch ($question_type_id) {
                        case "3": // Mutliple choise
                        case "4": // Checkboxes
                        case "5": // Drop Down Menu
                            $question_options = Model_formbuilder::get_question_option($question_id);

                            $question_option_text = "";
                            foreach ($question_options as $question_option) {
                                if ($question_option_text == "") {
                                    $question_option_text = $question_option["option_name"];
                                } else {
                                    $question_option_text = $question_option_text . "<br> " . $question_option["option_name"];
                                }
                            }
                            //$html_content .= "<b>Options:</b><br> ".$question_option_text."<br>";
                            break;

                        case "6": // Number Scale
                            $question_option_type_id = 1; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $scale_from = $question_options[0]["option_name"];

                            $question_option_type_id = 2; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $question_options = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $scale_to = $question_options[0]["option_name"];

                            //$html_content .= "<b>Range:</b> From ".$scale_from." To ".$scale_to."<br>";

                            break;

                        case "7": // Grid
                            $html_content .= '<b>User Input:</b><br>';

                            $question_option_type_id = 5; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $row_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $question_option_type_id = 6; //0=Option, 1=From #, 2=To #, 3=From label, 4=To Label, 5=Row, 6=Column

                            $column_text = Model_formbuilder::get_question_option($question_id, $question_option_type_id);

                            $html_content .= "<table border='0' cellpadding='6' cellspacing='0'>";

                            $html_content .= "<tr>";
                            $html_content .= "<td>&nbsp;</td>";
                            for ($i = 0; $i < count($column_text); $i++) {
                                $html_content .= "<td align='center'>" . $column_text[$i]["option_name"] . "</td>";
                            }

                            $html_content .= "</tr>";

                            for ($i = 0; $i < count($row_text); $i++) {
                                $html_content .= "<tr>";
                                $html_content .= "<td>" . $row_text[$i]["option_name"] . "</td>";

                                for ($j = 0; $j < count($column_text); $j++) {
                                    $html_content .= "<td  align='center'>&nbsp;</td>";
                                }

                                $html_content .= "</tr>";
                            }

                            $html_content .= "</table><br>";

                            break;
                        case "18": // participant name
                            if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                $html_content .= $question_item["question_text"] . '<br>';
                            } else {
                                $html_content .= "Name<br>";
                            }

                            break;
                        case "19": // location function
                            if (isset($question_item["question_text"]) && $question_item["question_text"] != "") {
                                $html_content .= $question_item["question_text"] . '<br>';
                            }

                            break;
                    }

                    if ($question_type_id != 7 && $question_type_id != 20 && $question_type_id != 21) //not grid, web link button and Document Button
                    {
                        $html_content .= '<b>User Input:</b><br><br>';
                    }
                }
            }
        }

        $uploaded_image_arr =  $this->download_formsubmission_log_pdf_missing_email($html_content, 'form_submission_response' . uniqid() . '.pdf');

        include_once 'mailsend.php';
        $fromemail = 'info@scanlink.com.au';
        $baseurl = URL::base();

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        $form_id  = Model::factory('Formbuilder')->get_form_id($profile_id);

        $recipient_email_arr = Model::factory('Formbuilder')->form_builder_recipient_list($form_id[0]['form_id']);

        $profile_logo = 'images/logo.png';
        $logo = Model::factory('logo')->selectOnlyLogoByProfileId($profile_id);

        if (!empty($logo) && isset($logo['logo_name']) && file_exists($logo['logo_name'])) {
            $profile_logo = $logo['logo_name'];
        }

        foreach ($recipient_email_arr as $recipient_email) {
            $recipient_email = 'shaileshk@webplanex.com';
            if ($profile['form_submission_format'] == 1) {
                client_mail_for_formbuilder_answer_as_pdf($fromemail, $baseurl, $recipient_email['recipient_email'], $html_content, $profile_id, $profile['code_profile_name'], $profile_logo, $uploaded_image_arr);
            } else {
                client_mail_for_formbuilder_answer($fromemail, $baseurl, $recipient_email['recipient_email'], $html_content, $profile_id, $profile_name, $profile_logo, $uploaded_image_arr);
            }
        }
    }
    public function download_formsubmission_log_pdf_missing_email($pdf_content, $pdf_name)
    {
        echo '<br>' . $pdf_name . '</br>';
        try {
            $pdf = new Pdf(array(
                'binary' => '/usr/local/bin/wkhtmltopdf',
                'margin-top'    => 10,
                'margin-right'  => 10,
                'margin-bottom' => 10,
                'margin-left'   => 10,
                'zoom' => 1.00
            ));
            $pdf->addPage($pdf_content);
            if (!$pdf->saveAs('images/formbuilder_upload/new_upload/' . $pdf_name)) {
                $error = $pdf->getError();
                mail('shaileshk@webplanex.com', 'Pdf Generation Error', $error);
            }
            return $pdf_name;
        } catch (\Exception $e) {
            echo '<pre>';
            print_r($e);
            echo '</pre>';
            exit;
        }
    }
    public function action_sendgrid_test_mail()
    {
        include_once 'mailsend.php';
        $fromemail = 'info@scanlink.com.au';
        $to = "shaileshk@webplanex.com";
        $subject = "SendGrid Test Mail";
        $body = "This is test sendgrid mail body";
        sendHtmlMailWithSendGrid($to, $subject, $body, $fromemail);
    }

    public function action_form_submissions_view_test()
    {
        // set title tag
        $this->template->head->title = "Form Submissions";
        $this->template->head->stylesheets = array(array('href' => 'styles/form-details.css', 'media' => 'screen'));

        $this->template->headers = "";
        $this->template->footer = "";

        $app_user_name = explode("::", $this->request->query("app_user_name"));
        $profile_id = $this->request->query("profile_id");

        //delete form builder answer
        if (isset($_REQUEST["delete_id"]) && $_REQUEST["delete_id"] != "") {
            Model::factory('formbuilder')->delete_answer_by_session_id($profile_id, $_REQUEST["delete_id"]);
            $this->session->set("successmessage", "Entry deleted successfully.");
        }
        //end of delete form builder answer

        $logo = Model::factory('logo')->getLogoByProfileId($profile_id);

        $profile_logo = "";

        if (!empty($logo) && isset($logo["logo_name"]) && file_exists($logo["logo_name"])) {
            $profile_logo = $logo["logo_name"];
        }

        $cond = " and profile_id='" . $profile_id . "' ";
        $session_id = $this->request->query("session_id");
        if ($session_id != "") {
            $cond .= " and session_id='" . $session_id . "'";
        }

        $profile = Model::factory('profiles')->getProfileByIdForMobile($profile_id);

        /* for paging */
        $offset = 0;
        $page_size = 10;
        $page_no = $this->request->query("page_no");
        $current_url = URL::base() . $this->request->uri() . URL::query();

        $session_count = Model::factory('formbuilder')->get_session_id_count($cond);

        if ($page_no == "") {
            $page_no = 1;
        }

        $total_pages = ceil($session_count / $page_size);

        $offset = $page_size * ($page_no - 1);

        $answer_time = Model::factory('formbuilder')->form_builder_answers_time($cond);

        $questions_arr = Model::factory('Formbuilder')->get_profile_question($profile_id);

        $errormessage = "";
        if ($this->session->get("errormessage") != "") {
            $errormessage = $this->session->get("errormessage");
            $this->session->delete("errormessage");
        }
        $successmessage = "";
        if ($this->session->get("successmessage") != "") {
            $successmessage = $this->session->get("successmessage");
            $this->session->delete("successmessage");
        }
        // Default Content template
        $content = View::factory('formbuilder/form_submissions_view')
            ->bind('profile_id', $profile_id)
            ->bind('errormessage', $errormessage)
            ->bind('successmessage', $successmessage)
            ->bind('profile', $profile)
            ->bind('answer_time', $answer_time)
            ->bind('questions_arr', $questions_arr)
            ->bind('total_pages', $total_pages)
            ->bind('page_no', $page_no)
            ->bind('current_url', $current_url)
            ->bind('cond', $cond)
            ->bind('profile_logo', $profile_logo)->render();

      $headers = "MIME-Version: 1.0" . "\r\n"; 
      $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
      
      // Additional headers 
      $headers .= 'From: Webplanex<test@webplanex.com>' . "\r\n"; 

      mail('shaileshk@webplanex.com','Mail content',$content, $headers);
    }
}
