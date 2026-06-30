<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_formbuilder extends Model
{


    public function get_question_type($type, $cond = "")
    {

        if ($cond != "") {
            $query = DB::query(Database::SELECT, "SELECT *									
									FROM form_builder_question_types 
									where is_active='1' and type='" . $type . "' " . $cond . " ");

            $question_type = $query->execute()->as_array();
        } else {
            $question_type = DB::select('*')->from('form_builder_question_types')
                ->where('type', '=', $type)
                ->where('is_active', '=', 1)
                ->execute()
                ->as_array();
        }



        if ($question_type) {
            return $question_type;
        } else {
            return array();
        }
    }

    public function get_form_id($profile_id)
    {
        $form_id = DB::select('form_id')->from('profiles')
            ->where('id', '=', $profile_id)
            ->execute()
            ->as_array();

        if ($form_id) {
            return $form_id;
        } else {
            return array();
        }
    }

    public function get_existing_forms($profile_id, $user_id)
    {
        $form_arr = DB::select('id', 'form_id', 'form_title')->from('profiles')
            ->where('user_id', '=', $user_id)
            ->where('id', '!=', $profile_id)
            ->where('form_id', '>', 0)
            ->execute()
            ->as_array();



        if ($form_arr) {
            return $form_arr;
        } else {
            return array();
        }
    }

    public function get_existing_forms_client_id($profile_id, $user_id, $show_code_profile_id)
    {

        $query = "SELECT `id`, `form_id`, `form_title` FROM `profiles` WHERE `client_id` = '" . $user_id . "' AND `id` != '" . $profile_id . "' AND `id` in (" . $show_code_profile_id . ")  AND `form_id` > 0";

        $form_arr = DB::query(Database::SELECT, $query)->execute()->as_array();


        if ($form_arr) {
            return $form_arr;
        } else {
            return array();
        }
    }

    public function get_form_id_max()
    {
        ///transaction start

        $db = Database::instance();
        $db->begin();
        $form_max_id = 1;
        try {

            DB::query(Database::UPDATE, "update temp_tbl_for_form_max_id set form_max_id = form_max_id+1 ")->execute();

            $temp_tbl_for_form_max_id = DB::select('*')->from('temp_tbl_for_form_max_id')
                ->execute()
                ->as_array();


            if (count($temp_tbl_for_form_max_id) > 0)
                $form_max_id =    $temp_tbl_for_form_max_id[0]["form_max_id"];


            $db->commit();
        } catch (Database_Exception $e) {
            // failed. Rolling back changes...
            $db->rollback();
        }
        return $form_max_id;
        ///transaction end


    }


    function form_builder_question_save($profile_id, $user_input, $question_type_id, $image_title, $image_align, $mandatory_field, $button_link_url, $button_colour, $doc_title, $form_id, $include_name = 0, $include_employer = 0, $include_email = 0, $include_phone = 0, $participant_include_signature = 0, $participant_include_employer = 0, $is_logchecked = 0, $log_columntitle = "")
    {
        //get max order
        $max_order = $this->get_max_order($profile_id);
        // echo $is_logchecked;
        $question_order = $max_order["max_order"] + 1;
        if ($max_order["max_order"] == 0)
            $question_order = 1;

        $question_id = DB::insert(
            'form_builder_question',
            array(
                'profile_id',
                'question_text',
                'question_type_id',
                'image_title',
                'image_align',
                'question_order',
                'button_link_url',
                'button_colour',
                'doc_title',
                'include_name',
                'include_employer',
                'include_email',
                'include_phone',
                'participant_include_signature',
                'participant_include_employer',
                'is_mandatory',
                'form_id',
                'is_logchecked',
                'log_columntitle'

            )
        )
            ->values(
                array(
                    $profile_id,
                    $user_input,
                    $question_type_id,
                    $image_title,
                    $image_align,
                    $question_order,
                    $button_link_url,
                    $button_colour,
                    $doc_title,
                    $include_name,
                    $include_employer,
                    $include_email,
                    $include_phone,
                    $participant_include_signature,
                    $participant_include_employer,
                    $mandatory_field,
                    $form_id,
                    $is_logchecked,
                    $log_columntitle
                )
            )
            ->execute();

        $question_id = $question_id[0];
        return $question_id;
    }

    function form_builder_question_option_add($question_id, $option_name, $question_option_type_id)
    {
        DB::insert(
            'form_builder_question_options',
            array(
                'question_id',
                'option_name',
                'question_option_type_id',
            )
        )
            ->values(
                array(
                    $question_id,
                    $option_name,
                    $question_option_type_id
                )
            )
            ->execute();
    }

    function form_builder_question_update($question_id, $data)
    {
        DB::update('form_builder_question')->set($data)
            ->where('question_id', '=', $question_id)
            ->execute();
    }

    function form_builder_question_update_form_id($profile_id, $data)
    {
        DB::update('form_builder_question')->set($data)
            ->where('profile_id', '=', $profile_id)
            ->where('is_deleted', '=', "0")
            ->execute();
    }

    function form_builder_question_remove($question_id)
    {
        DB::delete('form_builder_question')
            ->where('question_id', '=', $question_id)
            ->execute();

        DB::delete('form_builder_question_options')
            ->where('question_id', '=', $question_id)
            ->execute();


        DB::delete('form_builder_answers')
            ->where('question_id', '=', $question_id)
            ->execute();
    }

    function form_builder_question_options_remove($question_id)
    {
        DB::delete('form_builder_question_options')
            ->where('question_id', '=', $question_id)
            ->execute();
    }



    function form_builder_recipient_add($form_id, $recipient_email)
    {
        DB::insert(
            'form_builder_recipient',
            array(
                'form_id',
                'recipient_email'
            )
        )
            ->values(
                array(
                    $form_id,
                    $recipient_email
                )
            )
            ->execute();
    }

    function form_builder_recipient_delete($form_id)
    {
        DB::query(Database::DELETE, "delete from form_builder_recipient where form_id='" . $form_id . "'")
            ->execute();
    }

    function form_builder_recipient_list($form_id)
    {
        $recipient_email_arr = DB::select('recipient_email')->from('form_builder_recipient')
            ->where('form_id', '=', $form_id)
            ->execute()
            ->as_array();

        if ($recipient_email_arr) {
            return $recipient_email_arr;
        } else {
            return array();
        }
    }



    function updateQuestion($profileTitle, $proId)
    {
        DB::query(Database::UPDATE, "update profiles set `form_title` = '" . $profileTitle . "' where `id` = '" . $proId . "'")->execute();
    }

    function form_builder_questions_submissions($profileid)
    {
        $questions = DB::select('*')->from('form_builder_question')
            ->where("profile_id", "=", $profileid)
            ->where("is_deleted", "=", 0)
            ->where("is_logchecked", "=", 1)
            ->order_by("question_order", "asc")
            ->execute()
            ->as_array();


        if ($questions) {
            return $questions;
        } else {
            return array();
        }
    }

    public function get_profile_question($profileid)
    {
        $questions = DB::select('*')->from('form_builder_question')
            ->where("profile_id", "=", $profileid)
            ->where("is_deleted", "=", 0)
            ->where("form_id", "!=", 0)
            ->order_by("question_order", "asc")
            ->execute()
            ->as_array();

        //form_id =0 used to prevent any unsaved form

        if ($questions) {
            return $questions;
        } else {
            return array();
        }
    }

    public function get_profile_question_by_form_id($form_id)
    {
        $questions = DB::select('*')->from('form_builder_question')
            ->where("form_id", "=", $form_id)
            ->order_by("question_order", "asc")
            ->execute()
            ->as_array();

        if ($questions) {
            return $questions;
        } else {
            return array();
        }
    }

    public function get_profile_question_for_export($profileid)
    {
        $questions = DB::select('*')->from('form_builder_question')
            ->where("profile_id", "=", $profileid)
            ->where("is_deleted", "=", 0)
            ->where("question_type_id", "=", 1)
            ->where("is_logchecked", "=", 1)
            ->order_by("question_order", "asc")
            ->execute()
            ->as_array();

        if ($questions) {
            return $questions;
        } else {
            return array();
        }
    }

    public function get_profile_answers($profileid)
    {
        $answers = DB::select('date_time', 'session_id', 'app_user_firstname', 'app_user_lastname', 'app_user_email', 'app_user_mobile')->from('form_builder_answers')
            ->where("profile_id", "=", $profileid)
            ->order_by("date_time", "desc")
            ->group_by("session_id")
            ->execute()
            ->as_array();

        if ($answers) {
            return $answers;
        } else {
            return array();
        }
    }

    public static function get_question_by_question_id($question_id)
    {
        $questions = DB::select('*')->from('form_builder_question')
            ->where("question_id", "=", $question_id)
            ->execute()
            ->as_array();

        if ($questions) {
            return $questions;
        } else {
            return array();
        }
    }

    public static function get_cumulative_question_option($profile_id)
    {
        if ($profile_id != "") {
            //return  DB::query(Database::SELECT, "SELECT fbq.question_id, fbqo.option_name, fbqo.question_option_type_id FROM form_builder_question fbq LEFT JOIN form_builder_question_options fbqo ON fbq.question_id = fbqo.question_id where fbq.profile_id = '".$profile_id."' and fbq.is_deleted = 0 and fbqo.question_option_type_id IN (0,1,2)")->execute()->as_array();
            $query = "select question_id, profile_id, question_text, question_type_id,"
                . "(select count(*) from form_builder_question where profile_id = '" . $profile_id . "' and question_type_id=3 and is_deleted=0) as radio,"
                . "(select count(*) from form_builder_question where profile_id = '" . $profile_id . "' and question_type_id=5 and is_deleted=0) as dropdown,"
                . "(select count(*) from form_builder_question where profile_id = '" . $profile_id . "' and question_type_id=6 and is_deleted=0) as scale "
                . "from form_builder_question  where profile_id = '" . $profile_id . "'  and is_deleted=0";
            return  DB::query(Database::SELECT, $query)->execute()->as_array();
        } else {
            return array();
        }
    }

    public function get_cumulative_profile_other_que_type($type_id)
    {
        if ($type_id != "") {
            $query = "select question_type from form_builder_question_types where question_type_id='" . $type_id . "'";

            return DB::query(Database::SELECT, $query)->execute()->as_array();
        } else {
            return array();
        }
    }

    public function get_total_fields_count($profile_id)
    {
        $query = DB::query(Database::SELECT, "select count(*) as total_fields from form_builder_question"
            . " where profile_id='" . $profile_id . "' and is_deleted=0");
        $count = $query->execute()->as_array();
        if ($count) {
            return $count;
        } else {
            return array();
        }
    }

    public function get_cumulative_question_text($profile_id)
    {
        $query = DB::query(Database::SELECT, "SELECT * FROM form_builder_question 
            where profile_id='" . $profile_id . "' and is_deleted=0 and question_type_id=2");

        $question = $query->execute()->as_array();

        if ($question) {
            return $question;
        } else {
            return array();
        }
    }

    public function get_cumulative_question_text_count($profile_id)
    {
        $query = DB::query(Database::SELECT, "SELECT count(*) as question_text_count, profile_id FROM form_builder_question 
            where profile_id='" . $profile_id . "' and is_deleted=0 and question_type_id=2");

        $question = $query->execute()->as_array();

        if ($question) {
            return $question;
        } else {
            return array();
        }
    }

    public static function get_cumulative_option_from_question_id($question_id)
    {
        $query = "select option_name from form_builder_question_options where question_id='" . $question_id . "'";
        return DB::query(Database::SELECT, $query)->execute()->as_array();
    }

    public static function get_question_option($question_id, $question_option_type_id = 0, $order_by = "")
    {
        if ($question_option_type_id != 0) {
            $question_options = DB::select('*')->from('form_builder_question_options')
                ->where("question_id", "=", $question_id)
                ->where("question_option_type_id", "=", $question_option_type_id)
                ->execute()
                ->as_array();
        } else {
            // $question_options = DB::select('*')->from('form_builder_question_options')
            //     ->where("question_id", "=", $question_id)
            //     ->execute()
            //     ->as_array();
            $question_options = DB::select('*')->from('form_builder_question_options')
                ->where("question_id", "=", $question_id);

            if (!empty($order_by)) {
                $question_options->order_by($order_by, "asc");
            }

            $question_options = $question_options->execute()->as_array();
        }

        if ($question_options) {
            return $question_options;
        } else {
            return array();
        }
    }

    public static function get_question_types_by_question_type_id($question_type_id)
    {

        $question_types = DB::select('*')->from('form_builder_question_types')
            ->where("question_type_id", "=", $question_type_id)
            ->execute()
            ->as_array();


        if ($question_types) {
            return $question_types;
        } else {
            return array();
        }
    }

    public static function get_question_by_answer_question_id($question_id, $profile_id, $current_question_order)
    {
        $query = DB::query(Database::SELECT, "SELECT *									
									FROM form_builder_question 
									where profile_id='" . $profile_id . "' and is_deleted=0 and question_order< " . $current_question_order . "  and question_type_id =2 
									ORDER BY question_order desc limit 0,1");

        $question = $query->execute()->as_array();

        if ($question) {
            return $question;
        } else {
            return array();
        }
    }

    public static function get_question_by_answer_question_type_id($question_type_id, $profile_id)
    {

        $query = DB::query(Database::SELECT, "SELECT *									
									FROM form_builder_question 
									where profile_id='" . $profile_id . "' and is_deleted=0 and question_type_id ='" . $question_type_id . "' 
									ORDER BY question_order desc limit 0,1");

        $question = $query->execute()->as_array();

        if ($question) {
            return $question[0];
        } else {
            return array();
        }
    }

    public static function get_question_by_answer_question_type_id_header($profile_id)
    {

        $query = DB::query(Database::SELECT, "SELECT *									
									FROM form_builder_question 
									where profile_id='" . $profile_id . "' and is_deleted=0 and (question_type_id ='10' or question_type_id ='12' ) 
									ORDER BY question_order asc ");

        $question = $query->execute()->as_array();

        if ($question) {
            return $question;
        } else {
            return array();
        }
    }

    public function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    function save_form_builder_answers($question_id, $profile_id, $question_answer, $row_number = 0, $session_id = "", $app_user_firstname = "", $app_user_lastname = "", $app_user_email = "", $app_user_mobile = "", $p_signature_image = '', $p_emp_name = '')
    {

        $signature_name_text = "";
        $signature_name_text2 = "";
        $signature_name_text3 = "";
        $signature_name_text4 = "";
        $question_answer_text = $question_answer;


        // $xml = simplexml_load_file('http://www.geoplugin.net/xml.gp?ip=' . $this->getRealIpAddr());

        // $data = array();
        // foreach ($xml as $key => $value) {
        //     array_push($data, $value);
        // }
        // $timezone_name = isset($data[19]) ? $data[19] : 'Asia/Kolkata';

        // $date_time = new DateTime('now', new DateTimeZone($timezone_name));
        // $date = $date_time->format('Y-m-d H:i:s');

        $date = date('Y-m-d H:i:s');

        //mail('shaileshk@webplanex.com', 'Current time', $date);

        $question_answer_arr = explode('###', $question_answer);

        if (is_array($question_answer_arr)) {
            if (count($question_answer_arr) > 1) {
                $signature_name_text4 = $question_answer_arr[4];
                $signature_name_text3 = $question_answer_arr[3];
                $signature_name_text2 = $question_answer_arr[2];
                $signature_name_text = $question_answer_arr[1];
                $question_answer_text = $question_answer_arr[0];
            } else
                $question_answer_text = $question_answer_arr[0];
        }

        $ins = DB::insert(
            'form_builder_answers',
            array(
                'question_id',
                'profile_id',
                'question_answer',
                'row_number',
                'signature_name_text',
                'signature_employer_text',
                'signature_email_text',
                'signature_phone_text',
                'participant_signature_image',
                'participant_employer_text',
                'session_id',
                'date_time',
                'app_user_firstname',
                'app_user_lastname',
                'app_user_email',
                'app_user_mobile'
            )
        )
            ->values(
                array(
                    $question_id,
                    $profile_id,
                    $question_answer_text,
                    $row_number,
                    $signature_name_text,
                    $signature_name_text2,
                    $signature_name_text3,
                    $signature_name_text4,
                    $p_signature_image,
                    $p_emp_name,
                    $session_id,
                    $date,
                    $app_user_firstname,
                    $app_user_lastname,
                    $app_user_email,
                    $app_user_mobile
                )
            )
            ->execute();

        return $ins;
    }

    function get_question_answer($question_id, $profile_id)
    {
        $answer = DB::select('*')->from('form_builder_answers')
            ->where("question_id", "=", $question_id)
            ->and_where("profile_id", "=", $profile_id)
            ->execute()
            ->as_array();

        if ($answer) {
            return $answer;
        } else {
            return array();
        }
    }

    function delete_question_by_profile($profile_id)
    {
        $form_id             = $this->get_form_id($profile_id);

        //delete form recipients
        $this->form_builder_recipient_delete($form_id[0]["form_id"]);

        //remove signature image
        $question = DB::select('*')->from('form_builder_question')
            ->where("profile_id", "=", $profile_id)
            ->execute()
            ->as_array();

        if (count($question) > 0) {
            for ($i = 0; $i < count($question); $i++) {
                if ($question[$i]["question_type_id"] == "16") {
                    $answer = DB::select('*')->from('form_builder_answers')
                        ->where("question_id", "=", $question[$i]["question_id"])
                        ->execute()
                        ->as_array();

                    if (count($answer) > 0) {
                        for ($j = 0; $j < count($answer); $j++) {
                            if ($answer[$j]["question_answer"] != "")
                                unlink('images/signature/' . $answer[$j]["question_answer"]);
                        }
                    }
                }
            }
        }
        //end of remove signature image

        //remove answer but retain question as will be needed in form builder library
        DB::query(Database::DELETE, "delete from form_builder_answers where profile_id='" . $profile_id . "'")->execute();

        //check whether this form is deleted from library then delete entries from questions,options and library else just update flag
        $form_builder_library_arr = $this->get_form_builder_library_entry($form_id);
        if (count($form_builder_library_arr) > 0) {
            $is_deleted_from_library = $form_builder_library_arr[0]["is_deleted_from_library"];

            if ($is_deleted_from_library == 1) {
                $question = DB::select('*')->from('form_builder_question')
                    ->where("profile_id", "=", $profile_id)
                    ->execute()
                    ->as_array();

                if (count($question) > 0) {
                    for ($i = 0; $i < count($question); $i++)
                        DB::query(Database::DELETE, "delete from form_builder_question_options where question_id='" . $question[$i]["question_id"] . "'")->execute();
                }
                DB::query(Database::DELETE, "delete from form_builder_question where profile_id='" . $profile_id . "'")->execute();
                DB::query(Database::DELETE, "delete from form_builder_library where form_id='" . $form_id[0]["form_id"] . "'")->execute();
            } else {

                DB::query(Database::UPDATE, "update form_builder_question set is_deleted=1 where profile_id='" . $profile_id . "'")->execute();

                DB::query(Database::UPDATE, "update form_builder_library set is_deleted=1 where form_id='" . $form_id[0]["form_id"] . "'")->execute();
            }
        }
    }



    function form_builder_profile_enable_form_update($profile_id, $enable_form)
    {
        DB::query(Database::UPDATE, "update `profiles` set `form_is_enable` ='" . $enable_form . "' where `id` = '" . $profile_id . "'")->execute();
    }


    public static function form_builder_answers($cond)
    {
        $cond = "1=1" . $cond;


        $query = DB::query(Database::SELECT, "SELECT *,	DATE_FORMAT(date_time,'%d/%m/%Y %H:%i') as date_time_formated								
									FROM form_builder_answers 
									where " . $cond . " 
									 ");

        $answers = $query->execute()->as_array();

        if ($answers) {
            return $answers;
        } else {
            return array();
        }
    }

    public static function form_builder_questions($cond)
    {
        $cond = "1=1" . $cond;


        $query = DB::query(Database::SELECT, "SELECT * FROM form_builder_question 
									where " . $cond . " 
									 ");

        $questions = $query->execute()->as_array();

        if ($questions) {
            return $questions;
        } else {
            return array();
        }
    }

    function form_builder_answers_time($cond)
    {
        $cond = "1=1" . $cond;


        $query = DB::query(Database::SELECT, "SELECT DATE_FORMAT(date_time,'%d/%m/%Y %H:%i') as date_time_formated									
									FROM form_builder_answers 
									where " . $cond . " 
									limit 0,1 ");

        $answers = $query->execute()->as_array();

        if ($answers) {
            return $answers;
        } else {
            return array();
        }
    }

    function get_session_id_for_form_answer_with_limit($cond, $offset = 0, $page_size = 10)
    {
        $cond = "1=1" . $cond;


        $query = DB::query(Database::SELECT, "SELECT DISTINCT session_id																	
									FROM form_builder_answers 
									where " . $cond . " 
									 ORDER BY date_time desc limit " . $offset . "," . $page_size . " ");

        $answers = $query->execute()->as_array();

        if ($answers) {
            return $answers;
        } else {
            return array();
        }
    }

    public function get_session_id_for_form_answer($cond)
    {
        $cond = "1=1" . $cond;


        $query = DB::query(Database::SELECT, "SELECT DISTINCT session_id																	
									FROM form_builder_answers 
									where " . $cond . " 
									 ORDER BY date_time asc");

        $answers = $query->execute()->as_array();

        if ($answers) {
            return $answers;
        } else {
            return array();
        }
    }

    public function get_session_id_count($cond)
    {
        $cond = "1=1" . $cond;


        $query = DB::query(Database::SELECT, "SELECT DISTINCT session_id																	
									FROM form_builder_answers 
									where " . $cond . " 
									 ORDER BY date_time desc");

        $answers = $query->execute()->as_array();

        if ($answers) {
            return count($answers);
        } else {
            return 0;
        }
    }


    public static function get_count_for_grid_type($question_id, $profile_id, $session_id)
    {


        $query = DB::query(Database::SELECT, "SELECT form_builder_answer_id																	
									FROM form_builder_answers 
									where question_id='" . $question_id . "' and  profile_id='" . $profile_id . "' and session_id='" . $session_id . "'
									 ORDER BY date_time desc");

        $answers = $query->execute()->as_array();

        if ($answers) {
            return count($answers);
        } else {
            return 0;
        }
    }

    function delete_answer_by_session_id($profile_id, $session_id)
    {
        //remove signature image
        $query = DB::query(Database::SELECT, "SELECT *,(select question_type_id from form_builder_question where question_id=frm_ans.question_id) as question_type_id									
									FROM form_builder_answers  frm_ans
									where profile_id='" . $profile_id . "' and session_id='" . $session_id . "'");


        $answer = $query->execute()->as_array();



        if (count($answer) > 0) {
            for ($i = 0; $i < count($answer); $i++) {
                if ($answer[$i]["question_type_id"] == "16") {
                    if ($answer[$i]["question_answer"] != "" && file_exists('images/signature/' . $answer[$i]["question_answer"]))
                        unlink('images/signature/' . $answer[$i]["question_answer"]);
                }

                if ($answer[$i]["question_type_id"] == "17") {
                    if ($answer[$i]["question_answer"] != "" && file_exists('images/formbuilder_upload/' . $answer[$i]["question_answer"]))
                        unlink('images/formbuilder_upload/' . $answer[$i]["question_answer"]);
                }
            }
        }
        //end of remove signature image

        DB::query(Database::DELETE, "delete from form_builder_answers where profile_id='" . $profile_id . "' and session_id='" . $session_id . "' ")->execute();
    }

    public static function get_next_question_id($question_id, $profile_id)
    {

        $query = DB::query(Database::SELECT, "SELECT *									
									FROM form_builder_question 
									where profile_id='" . $profile_id . "' and is_deleted=0 and question_id> " . $question_id . "  and question_type_id not in(2,10,11,12,13,14)
									ORDER BY question_order asc limit 0,1");

        $question = $query->execute()->as_array();

        if ($question) {
            return $question;
        } else {
            return array();
        }
    }

    public static function get_analytics_questions($profile_id)
    {

        $query = DB::query(Database::SELECT, "SELECT *
					FROM `form_builder_question`
					WHERE `question_type_id`
					IN ( 3, 5, 6 ) and profile_id ='" . $profile_id . "' and is_deleted=0 ORDER BY question_order asc
					");

        $question = $query->execute()->as_array();

        if ($question) {
            return $question;
        } else {
            return array();
        }
    }

    public static function get_analytics_answers($profile_id, $question_id, $answer_compare)
    {

        $query = DB::query(Database::SELECT, "SELECT *
					FROM `form_builder_answers`
					WHERE profile_id ='" . $profile_id . "'
					AND question_id = '" . $question_id . "' and question_answer='" . $answer_compare . "'
					");

        $answer = $query->execute()->as_array();

        if ($answer) {
            return $answer;
        } else {
            return array();
        }
    }

    public static function get_max_order($profile_id)
    {

        $query = DB::query(Database::SELECT, "SELECT max(question_order) as max_order
					FROM `form_builder_question`
					WHERE profile_id ='" . $profile_id . "'  and is_deleted=0 
					");

        $max_order = $query->execute()->as_array();

        if ($max_order) {
            return $max_order[0];
        } else {
            return array();
        }
    }

    public function get_order_of_question($question_id)
    {

        $query = DB::query(Database::SELECT, "SELECT question_order
					FROM `form_builder_question`
					WHERE question_id ='" . $question_id . "'
					");

        $order = $query->execute()->as_array();

        if ($order) {
            return $order[0];
        } else {
            return array();
        }
    }

    public function update_order_of_question($qry)
    {
        DB::query(Database::UPDATE, $qry)->execute();
    }

    public function remove_form_and_questions($form_id)
    {
        $question = DB::select('*')->from('form_builder_question')
            ->where("form_id", "=", $form_id)
            ->execute()
            ->as_array();

        if (count($question) > 0) {
            for ($i = 0; $i < count($question); $i++)
                DB::query(Database::DELETE, "delete from form_builder_question_options where question_id='" . $question[$i]["question_id"] . "'")->execute();
        }
        DB::query(Database::DELETE, "delete from form_builder_question where form_id='" . $form_id . "'")->execute();
        DB::query(Database::DELETE, "delete from form_builder_library where form_id='" . $form_id . "'")->execute();
    }


    public function get_participant_list($profile_id)
    {

        $query = DB::query(Database::SELECT, "SELECT *,DATE_FORMAT(due_date,'%d/%m/%Y') as due_date_formated,DATEDIFF(due_date,participated_date) AS diff_date , DATEDIFF(curdate(),due_date) AS diff_date_created 								
									FROM participant 
									where profile_id=" . $profile_id . " 
									 ");
        $participant_arr = $query->execute()->as_array();

        if ($participant_arr) {
            return $participant_arr;
        } else {
            return array();
        }
    }

    public function get_participant_list_with_cond($cond, $group_by = "")
    {
        if ($group_by == "") {
            $query = DB::query(Database::SELECT, "SELECT *									
									FROM participant 
									where " . $cond . " 
									 ");
        } else {
            $query = DB::query(Database::SELECT, "SELECT *									
									FROM participant 
									where " . $cond . " group by " . $group_by . "
									 ");
        }
        $participant_arr = $query->execute()->as_array();

        if ($participant_arr) {
            return $participant_arr;
        } else {
            return array();
        }
    }

    function add_participant($profile_id, $name, $due_date)
    {
        $id = DB::insert(
            'participant',
            array(
                'profile_id',
                'name',
                'due_date'
            )
        )
            ->values(
                array(
                    $profile_id,
                    $name,
                    $due_date
                )
            )
            ->execute();
    }

    function edit_participant($participant_id, $name, $due_date)
    {
        DB::query(Database::UPDATE, "update participant set `name` = '" . $name . "',`due_date` = '" . $due_date . "' where `participant_id` = '" . $participant_id . "'")->execute();
    }

    function remove_participant($profile_id)
    {
        DB::query(Database::DELETE, "delete from participant where profile_id='" . $profile_id . "'")->execute();
    }

    function remove_participant_user($participant_id)
    {
        DB::query(Database::DELETE, "delete from participant where participant_id='" . $participant_id . "'")->execute();
    }


    function update_participant_status($name, $profile_id, $p_emp_name = "")
    {

        //DB::query(Database::UPDATE, "update participant set `is_participated` = '1' where `profile_id` = '" . $profile_id . "' and `name` = '" . $name . "'")->execute();
        DB::query(Database::UPDATE, "update participant set `is_participated` = '1',`employer_cmp` = '" . $p_emp_name . "',participated_date='" . date('Y-m-d') . "' where `profile_id` = '" . $profile_id . "' and `name` = '" . $name . "'  and is_participated=0")->execute();

        $query = DB::query(Database::SELECT, "SELECT *								
									FROM participant
									where `profile_id` = '" . $profile_id . "' and `name` = '" . $name . "' ");

        $participant_arr = $query->execute()->as_array();

        if (count($participant_arr) == 0) // add new participant which are not in list
        {
            $id = DB::insert(
                'participant',
                array(
                    'profile_id',
                    'name',
                    'employer_cmp',
                    'due_date',
                    'participated_date',
                    'is_participated'
                )
            )
                ->values(
                    array(
                        $profile_id,
                        $name,
                        $p_emp_name,
                        date("Y-m-d"),
                        date("Y-m-d"),
                        '1'
                    )
                )
                ->execute();
        }
    }

    public function get_participant_recipient_list($profile_id)
    {

        $query = DB::query(Database::SELECT, "SELECT *								
									FROM participant_recipient 
									where profile_id=" . $profile_id . " 
									 ");
        $participant_recipient_arr = $query->execute()->as_array();

        if ($participant_recipient_arr) {
            return $participant_recipient_arr;
        } else {
            return array();
        }
    }


    function add_participant_recipient($profile_id, $email)
    {

        $id = DB::insert(
            'participant_recipient',
            array(
                'profile_id',
                'email'
            )
        )
            ->values(
                array(
                    $profile_id,
                    $email
                )
            )
            ->execute();
    }

    function remove_participant_recipient($profile_id)
    {

        DB::query(Database::DELETE, "delete from participant_recipient where profile_id='" . $profile_id . "'")->execute();
    }

    function form_builder_library_add($form_id, $user_id, $form_title)
    {
        DB::insert(
            'form_builder_library',
            array(
                'form_id',
                'user_id',
                'form_title'
            )
        )
            ->values(
                array(
                    $form_id,
                    $user_id,
                    $form_title
                )
            )
            ->execute();
    }

    public function get_form_builder_library_id($form_id)
    {
        $form_builder_library_id = DB::select('form_builder_library_id')->from('form_builder_library')
            ->where('form_id', '=', $form_id)
            ->execute()
            ->as_array();

        if ($form_builder_library_id) {
            return $form_builder_library_id;
        } else {
            return array();
        }
    }

    public function get_form_builder_library_entry($form_id)
    {
        $form_builder_library_arr = DB::select('*')->from('form_builder_library')
            ->where('form_id', '=', $form_id)
            ->execute()
            ->as_array();

        if ($form_builder_library_arr) {
            return $form_builder_library_arr;
        } else {
            return array();
        }
    }

    function form_builder_library_update($form_builder_library_id, $data)
    {
        DB::update('form_builder_library')->set($data)
            ->where('form_builder_library_id', '=', $form_builder_library_id)
            ->execute();
    }

    public function get_library_forms($user_id, $profile_id)
    {
        //exclude current form from list
        $form_id             = $this->get_form_id($profile_id);
        $form_id_current = $form_id[0]["form_id"];

        $form_arr = DB::select('form_id', 'form_title')->from('form_builder_library')
            ->where('user_id', '=', $user_id)
            ->where('form_id', '!=', $form_id_current)
            ->where('is_deleted_from_library', '=', "0")
            ->execute()
            ->as_array();

        if ($form_arr) {
            return $form_arr;
        } else {
            return array();
        }
    }

    public function get_library_forms_other_user($user_id)
    {

        $form_arr = DB::select('form_id', 'form_title')->from('form_builder_library')
            ->where('user_id', '=', $user_id)
            ->where('is_deleted_from_library', '=', "0")
            ->execute()
            ->as_array();

        if ($form_arr) {
            return $form_arr;
        } else {
            return array();
        }
    }
    public function get_missing_mail_id_record()
    {
        $form_id = DB::select('id')->from('profiles')
            ->where('client_id', '=', '133')
            ->where('user_id', '=', '145')
            ->execute()
            ->as_array();

        if ($form_id) {
            return $form_id;
        } else {
            return array();
        }
    }
    public function get_session_id_by_profile_id($profile_id)
    {
        $session_id = DB::query(Database::SELECT, "SELECT `profile_id`,`session_id`									
									FROM form_builder_answers
									where `profile_id` in (" . implode(",", $profile_id) . ") AND date_time >= '2021-02-22 00:00:00 AND date_time < 2021-02-23 
                                    00:00:00' limit 0,1000")->execute()->as_array();



        echo '<pre>';
        print_r("SELECT `profile_id`,`session_id`									
									FROM form_builder_answers
									where `profile_id` in (" . implode(",", $profile_id) . ") AND date_time >= '2021-02-22 00:00:00 AND date_time < 2021-02-23 
                                    00:00:00' limit 0,1000");
        exit;

        if ($session_id) {
            return $session_id;
        } else {
            return array();
        }
    }

    public function get_max_order_form_builder_answer()
    {
        $query = DB::query(Database::SELECT, "SELECT max(form_builder_answer_id) as max_order
					FROM form_builder_answers");

        $max_order = $query->execute()->as_array();

        if (!empty($max_order[0]) && $max_order[0]['max_order']) {

            $xml = simplexml_load_file('http://www.geoplugin.net/xml.gp?ip=' . $this->getRealIpAddr());

            $data = array();
            foreach ($xml as $key => $value) {
                array_push($data, $value);
            }
            $timezone_name = !empty($data[19]) ? $data[19] : 'Asia/Kolkata';

            $date_time = new DateTime('now', new DateTimeZone($timezone_name));
            $date = $date_time->format('Y-m-d H:i:s');


            //echo "update form_builder_answers set `date_time` = '" . $date . "' where `form_builder_answer_id` = '" . $max_order[0]['max_order'] . "'";

            DB::query(Database::UPDATE, "update form_builder_answers set `date_time` = '" . $date . "' where `form_builder_answer_id` = '" . $max_order[0]['max_order'] . "'")->execute();
            return array(
                'date' => $date,
                'form_answer_id' => $max_order[0]['max_order'],
                'timezone' => $timezone_name
            );
            // return true;
        } else {
            mail('shaileshk@webplanex.com', 'Timezone update error', 'Max order id get error');
            return array();
        }
    }
}
