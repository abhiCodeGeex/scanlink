<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Swmsfileuploader extends Controller_Template_Master
{
    public function action_upload_swms_multiple_file_upload()
    {
        $multiple_file_upload_swms_hazard = array();

        foreach ($_FILES as $key => $value) {
            if (strpos($key, 'hwms_question_') !== false) {
                $key_name = explode('hwms_question_', $key);

                $question_id = $key_name[1];

                $session_id = rand(1111, 9999).'-'.date('dmyHis');

                foreach ($value['name'] as $key1 => $value1) {
                    if ($_FILES[$key]['size'][$key1] > 0) {
                        $arr = pathinfo($_FILES[$key]['name'][$key1]);
                        $ext = $arr['extension'];

                        $filename = $session_id.'_'.$question_id.$key1.'.'.$ext;

                        move_uploaded_file($_FILES[$key]['tmp_name'][$key1], 'images/formbuilder_upload/'.$filename);

                        $display_icon = '';
                        $original_name = '';
                        switch ($ext) {
                            case 'gif':
                                $display_icon = URL::base().'images/formbuilder_upload/'.$filename;
                                $original_name = URL::base().'images/formbuilder_upload/'.$filename;
                                break;
                            case 'jpg':
                                $display_icon = URL::base().'images/formbuilder_upload/'.$filename;
                                $original_name = URL::base().'images/formbuilder_upload/'.$filename;
                                break;
                            case 'png':
                                $display_icon = URL::base().'images/formbuilder_upload/'.$filename;
                                $original_name = URL::base().'images/formbuilder_upload/'.$filename;
                                break;
                            case 'jpeg':
                                $display_icon = URL::base().'images/formbuilder_upload/'.$filename;
                                $original_name = URL::base().'images/formbuilder_upload/'.$filename;
                                break;
                            case 'pdf':
                                $display_icon = URL::base().'images/pdf.jpg';
                                $original_name = URL::base().'images/formbuilder_upload/'.$filename;
                                break;
                            case 'xls':
                                $display_icon = URL::base().'images/excel.jpg';
                                $original_name = URL::base().'images/formbuilder_upload/'.$filename;
                                break;
                            case 'xlsx':
                                $display_icon = URL::base().'images/excel.jpg';
                                $original_name = URL::base().'images/formbuilder_upload/'.$filename;
                                break;
                            case 'docx':
                                $display_icon = URL::base().'images/doc.jpg';
                                $original_name = URL::base().'images/formbuilder_upload/'.$filename;
                                break;
                            case 'doc':
                                $display_icon = URL::base().'images/doc.jpg';
                                $original_name = URL::base().'images/formbuilder_upload/'.$filename;
                                break;
                            default:
                                $display_icon = URL::base().'images/doc.jpg';
                                $original_name = URL::base() . 'images/formbuilder_upload/' . $filename;
                                break;
                        }

                        $multiple_file_upload_swms_hazard[$question_id][] = array(
                            'original_name' => $original_name,
                            'display_icon' => $display_icon,
                        );
                    }
                }
            }
        }
        echo json_encode($multiple_file_upload_swms_hazard);
        exit;
    }

    public function action_remove_swms_multiple_uploaded_file()
    {
        $file_name = end(explode('/', $_POST['file_name']));

        $is_unlink = unlink('images/formbuilder_upload/'.$file_name);
        if ($is_unlink) {
            echo json_encode('file remove successfully.');
        } else {
            echo json_encode('error in remove files.');
        }
        exit;
    }
}
