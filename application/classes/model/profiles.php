<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Profiles extends Model
{

  public function getProfileById($id)
  {

    $expr  = new Database_Expression('DATE_FORMAT(expired_at, "%d/%m/%Y")');
    $expr1 = new Database_Expression('DATE_FORMAT(activation_start_date, "%d/%m/%Y")');
    $expr2 = new Database_Expression('DATE_FORMAT(activation_end_date, "%d/%m/%Y")');

    $profile = DB::select('*', array($expr, 'expired_at_formatted'), array($expr1, 'activation_start_date_formatted'), array($expr2, 'activation_end_date_formatted'))->from('profiles')
      ->where('profiles.id', '=', $id)
      ->where('client_id', '=', Session::instance()->get('client_id'))
      ->execute()
      ->as_array();



    if ($profile) :
      return $profile[0];
    else :
      return array();
    endif;
  }

  public function get_expiry_date_by_profileid($profile_id)
  {


    $query = DB::query(
      Database::SELECT,
      "SELECT DATEDIFF(expired_at,now()) AS date_diff							
							FROM profiles
							WHERE id = '" . $profile_id . "'"
    );

    $profile = $query->execute()->as_array();

    if ($profile) :
      return $profile[0];
    else :
      return array();
    endif;
  }

  public function get_all_profiles($cond)
  {

    $query = DB::query(
      Database::SELECT,
      "SELECT code_profile_name, voc_first_name, voc_last_name,
							id,user_id, type_id, expired_at,
							DATE_FORMAT(expired_at,'%d/%m/%Y') as expired_at_min,
							DATEDIFF(expired_at,now()) AS date_diff							
							FROM profiles
							WHERE 1=1 AND 
							" . $cond . " 
							"
    );

    $profiles = $query->execute()->as_array();

    if ($profiles) :
      return $profiles;
    else :
      return array();
    endif;
  }

  public function get_expiry_profiles_user_id($cond)
  {

    $query = DB::query(
      Database::SELECT,
      "SELECT distinct user_id													
							FROM profiles
							WHERE 1=1 AND 
							" . $cond . " and (DATEDIFF(expired_at,now()))=0 or (DATEDIFF(expired_at,now()))=3 or (DATEDIFF(expired_at,now()))=30
							"
    );

    $profiles = $query->execute()->as_array();

    if ($profiles) :
      return $profiles;
    else :
      return array();
    endif;
  }

  public function getProfileByIdForMobile($id)
  {

    $expr  = new Database_Expression('DATE_FORMAT(expired_at, "%d/%m/%Y")');
    $expr1 = new Database_Expression('DATE_FORMAT(activation_start_date, "%d/%m/%Y")');
    $expr2 = new Database_Expression('DATE_FORMAT(activation_end_date, "%d/%m/%Y")');


    $profile = DB::select('*', array($expr, 'expired_at_formatted'), array($expr1, 'activation_start_date_formatted'), array($expr2, 'activation_end_date_formatted'))->from('profiles')
      ->where('profiles.id', '=', $id)
      ->execute()
      ->as_array();

    if ($profile) :
      return $profile[0];
    else :
      return array();
    endif;
  }

  public function getProfileByIdmd5($id)
  {
    $profile = DB::query(
      Database::SELECT,
      "SELECT * FROM profiles WHERE md5(profiles.id) = '" . $id . "' "
    )
      ->execute()
      ->as_array();

    if ($profile) :
      return $profile[0];
    else :
      return array();
    endif;
  }

  public function updateProfile($id, $data)
  {
    DB::update('profiles')->set($data)->where('profiles.id', '=', $id)->execute();
  }

  public function getQRByProfileId($id)
  {
    $qr = DB::select('*')->from('qrimage')->where('qrimage.profile_id', '=', $id)
      ->execute()
      ->as_array();

    if ($qr) :
      return $qr[0];
    else :
      return array();
    endif;
  }

  public function getProfileIdByQrId($id)
  {
    $qr = DB::select('profile_id')->from('qrimage')->where('id', '=', $id)
      ->execute()
      ->as_array();

    if ($qr) :
      return $qr[0];
    else :
      return array();
    endif;
  }

  public function getCodeTypeByProfileId($id)
  {
    $qr = DB::select('code_type')->from('profiles')->where('id', '=', $id)
      ->execute()
      ->as_array();

    if ($qr) :
      return $qr[0]["code_type"];
    else :
      return array();
    endif;
  }

  public function deleteprofile($id)
  {
    // Delete profiles
    //DB::update('profiles')->set(array("deleted" => 1))->where('id', '=', $id)->execute();

    //delete logo file
    $logo = DB::select('*')->from('logo')
      ->where('logo.profile_id', '=', $id)
      ->execute()
      ->as_array();
    foreach ($logo as $profile_logo) {
      unlink($profile_logo['logo_name']);
    }

    //delete logo extra file
    $logo_extra = DB::select('*')->from('logo_extra')
      ->where('logo_extra.profile_id', '=', $id)
      ->execute()
      ->as_array();
    foreach ($logo_extra as $profile_logo) {
      unlink($profile_logo['logo_name']);
    }

    //delete qrimage file
    $qrimage = DB::select('*')->from('qrimage')
      ->where('profile_id', '=', $id)
      ->execute()
      ->as_array();
    foreach ($qrimage as $profile_qrimage) {
      unlink($profile_qrimage['qrimg_name']);
    }

    //delete document file
    $documents = DB::select('*')->from('documents')
      ->where('profile_id', '=', $id)
      ->execute()
      ->as_array();
    foreach ($documents as $profile_documents) {
      unlink($profile_documents['doc_name']);
    }

    //delete picture file
    $picture = DB::select('*')->from('picture')
      ->where('profile_id', '=', $id)
      ->execute()
      ->as_array();
    foreach ($picture as $profile_picture) {
      unlink($profile_picture['picture_name']);
    }

    //delete picture extra file
    $picture = DB::select('*')->from('picture_extra')
      ->where('profile_id', '=', $id)
      ->execute()
      ->as_array();
    foreach ($picture as $profile_picture) {
      unlink($profile_picture['picture_name']);
    }

    //delete voc document file
    $documents = DB::select('*')->from('voc_documents')
      ->where('profile_id', '=', $id)
      ->execute()
      ->as_array();
    foreach ($documents as $voc_documents) {
      if ($voc_documents["file_name"] != "" && file_exists("images/voc_documents/" . $voc_documents["file_name"]))
        unlink("images/voc_documents/" . $voc_documents['file_name']);
    }

    //delete voc profile image
    $picture = DB::select('*')->from('voc_profile_image')
      ->where('profile_id', '=', $id)
      ->execute()
      ->as_array();
    foreach ($picture as $profile_picture) {
      unlink($profile_picture['image_name']);
    }


    // Delete Logo
    DB::delete('logo')->where('profile_id', '=', $id)->execute();

    // Delete Logo Extra
    DB::delete('logo_extra')->where('profile_id', '=', $id)->execute();

    // Delete Orders
    DB::delete('orders')->where('profile_id', '=', $id)->execute();

    // Delete picture
    DB::delete('picture')->where('profile_id', '=', $id)->execute();

    // Delete picture extra
    DB::delete('picture_extra')->where('profile_id', '=', $id)->execute();

    // Delete qrimage
    DB::delete('qrimage')->where('profile_id', '=', $id)->execute();

    // Delete documents
    DB::delete('documents')->where('profile_id', '=', $id)->execute();

    // Delete video
    DB::delete('video')->where('profile_id', '=', $id)->execute();

    // Delete visitor log
    DB::delete('contacts')->where('id_profile', '=', $id)->execute();

    // Delete web link
    DB::delete('weblink')->where('profile_id', '=', $id)->execute();

    // Delete profile contact
    DB::delete('profile_contact')->where('profile_id', '=', $id)->execute();

    //delete form builder entries
    Model::factory('formbuilder')->delete_question_by_profile($id);

    //delete participant entries
    Model::factory('formbuilder')->remove_participant($id);
    Model::factory('formbuilder')->remove_participant_recipient($id);

    //delete voc documents
    DB::delete('voc_documents')->where('profile_id', '=', $id)->execute();

    //delete voc profile image
    DB::delete('voc_profile_image')->where('profile_id', '=', $id)->execute();

    //delete voc recipients
    DB::delete('voc_recipients')->where('profile_id', '=', $id)->execute();

    //delete voc users
    DB::delete('voc_users')->where('profile_id', '=', $id)->execute();

    // update profile

    $array = array(
      'code_purchase_id' => 0,
      'type_id' => 0,
      'name' => '',
      'name2' => '',
      'code_profile_name' => '',
      'tiles_order' => '',
      'position' => '',
      'identification' => '',
      'serial_no' => '',
      'description' => '',
      'description2' => '',
      'notes' => '',
      'name_company' => '',
      'telephone' => '',
      'address' => '',
      'gps_coordinates' => '',
      'protect' => '',
      'password' => '',
      'show_header' => '',
      'update_or_not' => '0',
      'shorturl' => '',
      'code_type' => '0',
      'analytic_key' => '',
      'buttonbackcolor' => '',
      'buttonfontcolor' => '',
      'enable_data_collection' => '',
      'set_up_compulsory' => '',
      'data_collection_mobile' => '',
      'data_collection_email' => '',
      'data_collection_name' => '',
      'data_collection_surname' => '',
      'data_collection_content' => '',
      'data_collection_btn_text' => '',
      'data_collection_btn_color' => '',
      'link_button' => '',
      'link_button_text' => '',
      'link_button_url' => '',
      'link_button_color' => '',
      'url' => '',
      'display_share_link' => '',
      'mobile' => '',
      'email' => '',
      'form_id' => '0',
      'form_title' => '',
      'form_active' => '0',
      'form_is_enable' => '0',
      'enable_form_analytics' => '0',
      'activation_start_date' => '0000-00-00',
      'activation_end_date' => '0000-00-00',
      "voc_email_url" => '',
      "voc_email_text" => '',
      "voc_email_sign_line1" => '',
      "voc_email_sign1_bold" => '0',
      "voc_email_sign1_italic" => '0',
      "voc_email_sign1_underline" => '0',
      "voc_email_sign_line2" => '',
      "voc_email_sign2_bold" => '0',
      "voc_email_sign2_italic" => '0',
      "voc_email_sign2_underline" => '0',
      "voc_title_bar_enable" => '1',
      "voc_title_bar_text" => '',
      "voc_title_bar_colour" => '',
      "voc_first_name" => '',
      "voc_last_name" => '',
      "voc_address" => '',
      "voc_town" => '',
      "voc_state" => '',
      "voc_phone" => '',
      "voc_dob" => '0000-00-00',
      "voc_known_allergies" => '',
      "voc_blood_type" => '',
      "voc_next_of_kin" => '',
      "voc_contact_phone" => '',
      "voc_employer" => '',
      "voc_emp_address" => '',
      "voc_emp_town" => '',
      "voc_emp_state" => '',
      "voc_emp_phone" => '',
      "form_submission_format" => '0'
    );

    DB::update('profiles')->set($array)->where('id', '=', $id)->execute();
  }

  public function updateProfileIdForUploadData($profile_id, $type_id)
  {

    $data = array("type_id" => $type_id, "update_or_not" => '1', "updated_at" => date("Y-m-d H:i:s"));
    DB::update('profiles')
      ->set($data)
      ->where('profiles.id', '=', $profile_id)
      ->where('profiles.update_or_not', '=', '0')
      ->execute();
  }

  public function getCountnumberRenewProfile($profileid)
  {

    $data = DB::select(array('COUNT("*")', 'count'), 'is_reseller_code')->from('profiles')
      ->where('id', 'IN', $profileid)
      ->group_by('is_reseller_code')
      ->execute()
      ->as_array();
    return $data;
  }
  public function getProfileByClientId($clientId)
  {
    $codeProfiles = DB::query(
      Database::SELECT,
      "SELECT id AS code_profile_id
                FROM profiles
                WHERE client_id = '" . $clientId . "'"
    )->execute()->as_array();

    // SET SESSION group_concat_max_len = 4500000000; SELECT GROUP_CONCAT(id SEPARATOR ',') AS code_profile_id FROM profiles WHERE client_id = '133'


    $codeProfileIds = array();
    if (!empty($codeProfiles)) {

      $codeProfilesIds = array_map(function ($v) {
        return $v['code_profile_id'];
      }, $codeProfiles);

      $codeProfileIds = join(",", $codeProfilesIds);
    }
    return $codeProfileIds;
  }
  public function getProfileByClientIdSub($clientId)
  {
    $codeProfiles = DB::query(
      Database::SELECT,
      "SELECT is_sub_user,show_code_profile_id_to_acc_user
                FROM users
                WHERE id = '" . $clientId . "'"
    )->execute()->as_array();

    $is_sub_user = "";
    if (!empty($codeProfiles)) {
      $is_sub_user = $codeProfiles[0];
    }
    return $is_sub_user;
  }
}
