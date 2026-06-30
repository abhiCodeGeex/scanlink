<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Voc extends Model {

  public function insertVoc($data, $userid,$profile_id,$update_or_not="1") {
    $client_id = DB::select('client_id')
            ->from('users')
            ->where('id', '=', $userid)
            ->execute()
            ->as_array();


    $clientid = $client_id[0]['client_id'];

    $type_id = DB::select('id')
            ->from('equipment_types')
            ->where('slag', '=', 'voc')
            ->execute()
            ->as_array();

    $typeid = $type_id[0]['id'];
	
		
	$dataFill = array(
			"type_id"=>$typeid,
			 "code_profile_name" => $data["code_profile_name"],
			"tiles_order" => $data["tiles_order"],
			"protect"=>$data['protect'],
                        "form_submission_format"=>$data['form_submission_format'],
			"password"=>$data['password'],
			"show_header"=>$data['show_header'],
			"code_type"=>$data['code_type'],
			"display_share_link"=>$data['display_share_link'],
			"voc_email_url" => $data["voc_email_url"], 
			"voc_email_text" => $data["voc_email_text"], 
                        "voc_email_sign_line1" => $data["voc_email_sign_line1"],
                        "voc_email_sign1_bold" => $data["voc_email_sign1_bold"],
                        "voc_email_sign1_italic" => $data["voc_email_sign1_italic"],
                        "voc_email_sign1_underline" => $data["voc_email_sign1_underline"],
                        "voc_email_sign_line2" => $data["voc_email_sign_line2"],
                        "voc_email_sign2_bold" => $data["voc_email_sign2_bold"],
                        "voc_email_sign2_italic" => $data["voc_email_sign2_italic"],
                        "voc_email_sign2_underline" => $data["voc_email_sign2_underline"],
			"voc_title_bar_enable" => $data["voc_title_bar_enable"], 
			"voc_title_bar_text" => $data["voc_title_bar_text"], 
         "voc_title_bar_colour" => $data["voc_title_bar_colour"], 
			"voc_first_name" => $data["voc_first_name"], 
			"voc_last_name" => $data["voc_last_name"], 
			"voc_address" => $data["voc_address"], 
			"voc_town" => $data["voc_town"], 
			"voc_state" => $data["voc_state"], 
			"voc_phone" => $data["voc_phone"], 
			"voc_dob" => $data["voc_dob"], 
			"voc_known_allergies" => $data["voc_known_allergies"], 
			"voc_blood_type" => $data["voc_blood_type"], 
			"voc_next_of_kin" => $data["voc_next_of_kin"], 
			"voc_contact_phone" => $data["voc_contact_phone"], 
			"voc_employer" => $data["voc_employer"], 
			"voc_emp_address" => $data["voc_emp_address"], 
			"voc_emp_town" => $data["voc_emp_town"], 
			"voc_emp_state" => $data["voc_emp_state"], 
			"voc_emp_phone" => $data["voc_emp_phone"], 
			"activation_start_date"=>$data["activation_start_date"],
			"activation_end_date"=>$data["activation_end_date"],
			"update_or_not"=>$update_or_not,
			"created_at"=>$data['created_at'],
			"updated_at"=>date("Y-m-d H:i:s")
					);
					
	
	$query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
	$query->execute();
	
	
    return $profile_id;
  }

  public function updateVoc($data, $profile_id) {


    $profile = DB::select('*')
            ->from('profiles')
            ->where('id', '=', $profile_id)
            ->execute()
            ->as_array();

    $dataFill = array(
                        "code_profile_name" => $data["code_profile_name"],
			"tiles_order" => $data["tiles_order"],
			"protect"=>$data['protect'],
			"password"=>$data['password'],
			"show_header"=>$data['show_header'],
                        "form_submission_format"=>$data['form_submission_format'],
			"code_type"=>$data['code_type'],
			"display_share_link"=>$data['display_share_link'],
			"voc_email_url" => $data["voc_email_url"], 
			"voc_email_text" => $data["voc_email_text"], 
                        "voc_email_sign_line1" => $data["voc_email_sign_line1"],
                        "voc_email_sign_line1" => $data["voc_email_sign_line1"],
                        "voc_email_sign1_bold" => $data["voc_email_sign1_bold"],
                        "voc_email_sign1_italic" => $data["voc_email_sign1_italic"],
                        "voc_email_sign1_underline" => $data["voc_email_sign1_underline"],
                        "voc_email_sign_line2" => $data["voc_email_sign_line2"],
                        "voc_email_sign2_bold" => $data["voc_email_sign2_bold"],
                        "voc_email_sign2_italic" => $data["voc_email_sign2_italic"],
                        "voc_email_sign2_underline" => $data["voc_email_sign2_underline"],
			"voc_title_bar_enable" => $data["voc_title_bar_enable"], 
			"voc_title_bar_text" => $data["voc_title_bar_text"], 
         "voc_title_bar_colour" => $data["voc_title_bar_colour"], 
			"voc_first_name" => $data["voc_first_name"], 
			"voc_last_name" => $data["voc_last_name"], 
			"voc_address" => $data["voc_address"], 
			"voc_town" => $data["voc_town"], 
			"voc_state" => $data["voc_state"], 
			"voc_phone" => $data["voc_phone"], 
			"voc_dob" => $data["voc_dob"], 
			"voc_known_allergies" => $data["voc_known_allergies"], 
			"voc_blood_type" => $data["voc_blood_type"], 
			"voc_next_of_kin" => $data["voc_next_of_kin"], 
			"voc_contact_phone" => $data["voc_contact_phone"], 
			"voc_employer" => $data["voc_employer"], 
			"voc_emp_address" => $data["voc_emp_address"], 
			"voc_emp_town" => $data["voc_emp_town"], 
			"voc_emp_state" => $data["voc_emp_state"], 
			"voc_emp_phone" => $data["voc_emp_phone"], 
			"activation_start_date"=>$data["activation_start_date"],
			"activation_end_date"=>$data["activation_end_date"],
                        "updated_at"=>date("Y-m-d H:i:s")
    );
	
    $query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
    $query->execute();
	
	
  }
  
  public function updateVocByUser($data, $profile_id) {


    $profile = DB::select('*')
            ->from('profiles')
            ->where('id', '=', $profile_id)
            ->execute()
            ->as_array();

    $dataFill = array(
                        "voc_first_name" => $data["voc_first_name"], 
			"voc_last_name" => $data["voc_last_name"], 
			"voc_address" => $data["voc_address"], 
			"voc_town" => $data["voc_town"], 
			"voc_state" => $data["voc_state"], 
			"voc_phone" => $data["voc_phone"], 
			"voc_dob" => $data["voc_dob"], 
			"voc_known_allergies" => $data["voc_known_allergies"], 
			"voc_blood_type" => $data["voc_blood_type"], 
			"voc_next_of_kin" => $data["voc_next_of_kin"], 
			"voc_contact_phone" => $data["voc_contact_phone"], 
			"voc_employer" => $data["voc_employer"], 
			"voc_emp_address" => $data["voc_emp_address"], 
			"voc_emp_town" => $data["voc_emp_town"], 
			"voc_emp_state" => $data["voc_emp_state"], 
			"voc_emp_phone" => $data["voc_emp_phone"], 
			"updated_at"=>date("Y-m-d H:i:s")
    );
	
    $query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
    $query->execute();
	
	
  }

  public function add_voc_users($profile_id,$email,$password)
  {
  		DB::insert('voc_users', array(
	                      'profile_id',
	                      'email',
	                      'password'
	                          )
	                  )
	                  ->values(
	                          array(
	                              $profile_id,
	                              $email,
	                              $password
	                          )
	                  )
	                  ->execute();
  }
  
  public function remove_voc_users($profile_id)
  {
  		DB::delete('voc_users')->where('profile_id', '=', $profile_id)->execute();
  }
  
  public function add_voc_recipients($profile_id,$email)
  {
  		DB::insert('voc_recipients', array(
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
  
  public function remove_voc_recipients($profile_id)
  {
  		DB::delete('voc_recipients')->where('profile_id', '=', $profile_id)->execute();
  }
  
  public function add_voc_documents($profile_id,$name,$expiry_date)
  {
  		$voc_document_id = DB::insert('voc_documents', array(
	                      'profile_id',
	                      'name',
	                      'expiry_date'
	                       )
	                  )
	                  ->values(
	                          array(
	                              $profile_id,
	                              $name,
	                              $expiry_date
	                              )
	                  )
	                  ->execute();
	                  
	     return $voc_document_id[0];
  }
  
  public function remove_voc_documents($profile_id)
  {
  		DB::delete('voc_documents')->where('profile_id', '=', $profile_id)->execute();
  }
  
  public function remove_voc_document_by_id($voc_document_id)
  {
  		DB::delete('voc_documents')->where('voc_document_id', '=', $voc_document_id)->execute();
  }
  
  public function update_voc_doc_image_name($voc_document_id,$file_name)
  {
		$query = DB::update('voc_documents')->set(array("file_name"=>$file_name))->where('voc_document_id', '=', $voc_document_id)->execute();
		
  }
  
  public function update_voc_documents($voc_document_id,$name,$expiry_date)
  {
                $query = DB::update('voc_documents')->set(array("name"=>$name,"expiry_date"=>$expiry_date))->where('voc_document_id', '=', $voc_document_id)->execute();
  }
  
  public function getDocumentsByProfileId($id)
  {
		                
                $query = DB::query(Database::SELECT, "SELECT *,DATEDIFF(expiry_date,curdate()) AS diff_date_expiry,DATE_FORMAT(expiry_date,'%d/%m/%Y') as expiry_date_formated 								
									FROM voc_documents 
									where profile_id=".$id ." order by voc_document_id asc 
									 ");
                $documents = $query->execute()->as_array();
		
		if($documents){		
			return $documents;
		}else{
			return array();
		}
  }
  
  public function getDocumentsById($voc_document_id)
  {
		$documents = DB::select('*')->from('voc_documents')->where('voc_document_id', '=', $voc_document_id)
                    ->execute()
		->as_array();
		
		if($documents){		
			return $documents[0];
		}else{
			return array();
		}
  }
  
  public function get_voc_user_by_profile_id($profile_id)
  {
		$voc_users = DB::select('*')->from('voc_users')->where('profile_id', '=', $profile_id)
		->order_by('voc_user_id','asc')
		->execute()
		->as_array();
		
		if($voc_users){		
			return $voc_users;
		}else{
			return array();
		}
  }
  
  public function get_voc_user_by_user_details($profile_id,$email)
  {
		$voc_users = DB::select('*')->from('voc_users')
                        ->where('profile_id', '=', $profile_id)
                        ->where('email', '=', $email)
                        ->order_by('voc_user_id','asc')
                        ->execute()
                        ->as_array();
		
		if($voc_users){		
			return $voc_users;
		}else{
			return array();
		}
  }
  
   public function get_voc_user_by_cond($cond)
  {
		$query = DB::query(
                        Database::SELECT, "SELECT *													
							FROM voc_users
							WHERE 1=1 AND 
							".$cond." 
							"
                 );
		
                $voc_users = $query->execute()->as_array();
                
		if($voc_users){		
			return $voc_users;
		}else{
			return array();
		}
  }
  
  
  public function get_voc_recipients_by_profile_id($profile_id)
  {
		$voc_recipients = DB::select('*')->from('voc_recipients')->where('profile_id', '=', $profile_id)
		->order_by('voc_recipient_id','asc')
		->execute()
		->as_array();
		
		if($voc_recipients){		
			return $voc_recipients;
		}else{
			return array();
		}
  }
  
  public function get_voc_recipients_by_email($profile_id,$email)
  {
		$voc_recipients = DB::select('*')->from('voc_recipients')
                    ->where('profile_id', '=', $profile_id)
                    ->where('email', '=', $email)
                    ->order_by('voc_recipient_id','asc')
                    ->execute()
                    ->as_array();
		
		if($voc_recipients){		
			return $voc_recipients;
		}else{
			return array();
		}
  }
  
  public function get_expiry_document_profile_id($cond) 
  {
  	
	$query = DB::query(
                        Database::SELECT, "SELECT distinct profile_id													
							FROM voc_documents
							WHERE 1=1 AND 
							".$cond." and (DATEDIFF(expiry_date,curdate()))=0 or (DATEDIFF(expiry_date,curdate()))=30
							"
        );
	
    $profiles = $query->execute()->as_array();

    if ($profiles):
      return $profiles;
    else:
      return array();
    endif;
  }
  
  public function get_expiry_document_with_condition($cond) 
  {
  	
	$query = DB::query(
                        Database::SELECT, "SELECT *,DATE_FORMAT(expiry_date,'%d/%m/%Y') as expiry_date_formated,DATEDIFF(expiry_date,curdate()) AS diff_date_expiry 													
							FROM voc_documents
							WHERE 1=1 AND 
							".$cond." 
							"
        );
	
    $profiles = $query->execute()->as_array();

    if ($profiles):
      return $profiles;
    else:
      return array();
    endif;
  }
  
}

?>