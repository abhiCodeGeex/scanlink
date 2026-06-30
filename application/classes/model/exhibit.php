<?php

/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Exhibit extends Model {

  public function insertExhibit($data, $userid,$profile_id,$update_or_not="1") {
    $client_id = DB::select('client_id')
            ->from('users')
            ->where('id', '=', $userid)
            ->execute()
            ->as_array();


    $clientid = $client_id[0]['client_id'];

    $type_id = DB::select('id')
            ->from('equipment_types')
            ->where('slag', '=', 'exhibit')
            ->execute()
            ->as_array();

    $typeid = $type_id[0]['id'];
	
	
	
	$dataFill = array(
			"type_id"=>$typeid,
			"name" => $data["txtname"],
			"name2" => $data["txtname2"],
			"code_profile_name" => $data["code_profile_name"],
			"tiles_order" => $data["tiles_order"],
			"description"=>$data["txtdesc"],
                        
                        "form_submission_format"=>$data["form_submission_format"],
            
			"description2"=>$data["txtdesc2"],
			"protect"=>$data['protect'],
			"password"=>$data['password'],
			"show_header"=>$data['show_header'],
			"code_type"=>$data['code_type'],
			"buttonbackcolor"=>$data['txtbuttonbackcolor'],
			"buttonfontcolor"=>$data['txtbuttonfontcolor'],
			"display_share_link"=>$data['display_share_link'],
			"enable_data_collection" => $data["enable_data_collection"], 
			"set_up_compulsory" => $data["set_up_compulsory"],
			"data_collection_mobile" => $data["data_collection_mobile"], 
			"data_collection_email" => $data["data_collection_email"], 
			"data_collection_name" => $data["data_collection_name"],
			"data_collection_surname" => $data["data_collection_surname"],
			"data_collection_content"=>$data["data_collection_content"],
			"data_collection_btn_text"=>$data["data_collection_btn_text"],
			"data_collection_btn_color"=>$data["data_collection_btn_color"],
			"activation_start_date"=>$data["activation_start_date"],
			"activation_end_date"=>$data["activation_end_date"],
			"update_or_not"=>$update_or_not,
			"created_at"=>$data['created_at'],
			"updated_at"=>date("Y-m-d H:i:s")
					);
					
		/*
		"link_button"=>$data['link_button'],
			"link_button_text"=>$data['link_button_text'],
			"link_button_url"=>$data['link_button_url'],
			"link_button_color"=>$data['link_button_color'],
		*/
	$query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
	$query->execute();
	
	//first delete all web link 
	DB::delete('weblink')->where('profile_id', '=', $profile_id)->execute();
	
	//insert web link 
	if ( is_array($data["link_button_active"]) &&   !empty($data["link_button_active"])) {
		foreach($data["link_button_active"] as $key=>$weblink_data){
			
			if($weblink_data=="yes")
			{
			
					DB::insert('weblink', 
										array(
											'profile_id',
											'link_button',
											'link_button_text',
											'link_button_url',
											'link_button_color',
											'link_button_align',
											'created_at'
										)
									)
		      						->values(
										array(
											$profile_id,
											'1',
											$data['link_button_text'][$key],
											$data['link_button_url'][$key],
											$data['link_button_color'][$key],
											$data['link_button_align'][$key],
											date("Y-m-d H:i:s")
										)
									)
									->execute();
			}
		        
		}
	}
	
    /*
	$profile_id = DB::insert('profiles', array('client_id', 'user_id', 'type_id', 'name', 'description', 'protect', 'password','show_header', 'code_type', 'created_at', 
	'buttonbackcolor', 'buttonfontcolor', 'enable_data_collection', 'data_collection_mobile', 'data_collection_email', 'data_collection_name', 
	'data_collection_content','link_button', 'link_button_text', 'link_button_url', 'link_button_color'))
	
	
            ->values(array($clientid, $userid, $typeid, $data['txtname'], $data['txtdesc'], $data['protect'], $data['password'], $data['show_header'], $data['code_type'], $data['created_at'], 
			$data['txtbuttonbackcolor'], $data['txtbuttonfontcolor'], $data['enable_data_collection'], $data['data_collection_mobile'], $data['data_collection_email'],$data['data_collection_name'], 
			$data['data_collection_content'],$data['link_button'], $data['link_button_text'], $data['link_button_url'], $data['link_button_color']))
            ->execute();
    */

    if (!empty($data["videos"])) {
      $query = DB::insert('video', array('client_id', 'user_id', 'profile_id', 'title', 'video_name', 'created_at'));
      foreach ($data["videos"] as $video) {

        $query->values(array($clientid, $userid, $profile_id, $video['title'], $video['id'], $data['created_at']));
      }
      $query->execute();
    }
	
	if (!empty($data["videos2"])) {
      $query = DB::insert('video', array('client_id', 'user_id', 'profile_id', 'title', 'video_name', 'created_at','is_extra'));
      foreach ($data["videos2"] as $video) {

        $query->values(array($clientid, $userid, $profile_id, $video['title'], $video['id'], $data['created_at'],"1"));
      }
      $query->execute();
    }

    if (!empty($data["checklist_items"])) {
      $query = DB::insert('checklist_item', array('checklist_item', 'profile_id', 'datetime'));
      foreach ($data["checklist_items"] as $checklist_item) {
        $query->values(array($checklist_item, $profile_id, date('Y-m-d H:i:s')));
      }
      $query->execute();
    }

    return $profile_id;
  }

  public function updateExhibit($data, $profile_id) {

    /* 			if($data["logo_file"]!==false){
      DB::update('logo')->set(array("logo_name"=>$data["logo_file"]))->where('logo.profile_id', '=', $profile_id)->execute();
      } */

    $profile = DB::select('*')
            ->from('profiles')
            ->where('id', '=', $profile_id)
            ->execute()
            ->as_array();

    $dataFill = array(
        "name" => $data["txtname"],
        "name2" => $data["txtname2"],
        "code_profile_name" => $data["code_profile_name"],
        "form_submission_format"=>$data["form_submission_format"],
        "tiles_order" => $data["tiles_order"],
        "description" => $data["txtdesc"],
        "description2" => $data["txtdesc2"],
        "name_company" => $data["txtcompname"],
        "protect" => $data["protect"],
        "password" => $data["password"],
        "show_header" => $data["show_header"],
        "code_type" => $data["code_type"],
        "buttonbackcolor" => $data["txtbuttonbackcolor"],
        "buttonfontcolor" => $data["txtbuttonfontcolor"],
		"display_share_link"=>$data['display_share_link'],
        "enable_data_collection" => $data["enable_data_collection"],
		"set_up_compulsory" => $data["set_up_compulsory"],
        "data_collection_mobile" => $data["data_collection_mobile"],
        "data_collection_email" => $data["data_collection_email"],
        "data_collection_name" => $data["data_collection_name"],
        "data_collection_surname" => $data["data_collection_surname"],
        "data_collection_content" => $data["data_collection_content"],
		"data_collection_btn_text"=>$data["data_collection_btn_text"],
		"data_collection_btn_color"=>$data["data_collection_btn_color"],
		"activation_start_date"=>$data["activation_start_date"],
		"activation_end_date"=>$data["activation_end_date"],
			"activation_end_date"=>$data["activation_end_date"],
    );
	/*
	'link_button' => $data['link_button'],
		'link_button_text' => $data['link_button_text'],
		'link_button_url' => $data['link_button_url'],
		'link_button_color' => $data['link_button_color']
	*/
    $query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
    $query->execute();
	
	//first delete all web link 
	DB::delete('weblink')->where('profile_id', '=', $profile_id)->execute();
	//insert web link 
	if ( is_array($data["link_button_active"]) &&   !empty($data["link_button_active"])) {
		foreach($data["link_button_active"] as $key=>$weblink_data){
			
			if($weblink_data=="yes")
			{
			
					DB::insert('weblink', 
										array(
											'profile_id',
											'link_button',
											'link_button_text',
											'link_button_url',
											'link_button_color',
											'link_button_align',
											'created_at'
										)
									)
		      						->values(
										array(
											$profile_id,
											'1',
											$data['link_button_text'][$key],
											$data['link_button_url'][$key],
											$data['link_button_color'][$key],
											$data['link_button_align'][$key],
											date("Y-m-d H:i:s")
										)
									)
									->execute();
			}
		        
		}
	}

    /* 			if(!empty($data["picture_file"])){
      $query = DB::insert('picture', array('client_id','user_id','profile_id','picture_name','created_at'));
      foreach($data["picture_file"] as $file){
      $query->values(array($profile[0]["client_id"], $profile[0]["user_id"], $profile_id, $file, $data['created_at']));
      }
      $query->execute();
      } */

    if (!empty($data["videos"])) {
      $query = DB::insert('video', array('client_id', 'user_id', 'profile_id', 'title', 'video_name', 'created_at'));

      foreach ($data["videos"] as $video) {

        $query->values(array($profile[0]["client_id"], $profile[0]["user_id"], $profile_id, $video['title'], $video['id'], $data['created_at']));
      }
      $query->execute();
    }
	
	if (!empty($data["videos2"])) {
      $query = DB::insert('video', array('client_id', 'user_id', 'profile_id', 'title', 'video_name', 'created_at','is_extra'));

      foreach ($data["videos2"] as $video) {

        $query->values(array($profile[0]["client_id"], $profile[0]["user_id"], $profile_id, $video['title'], $video['id'], $data['created_at'],"1"));
      }
      $query->execute();
    }
	
    if (!empty($data["checklist_item_ids"])) {
      foreach ($data["checklist_item_ids"] as $key => $checklist_item_id) {
        $query = DB::update('checklist_item')->set(array('checklist_item' => $data["checklist_items"][$key]))->where('id', '=', $checklist_item_id);
        $query->execute();
        unset($data["checklist_items"][$key]);
      }
    }

    if (!empty($data["checklist_items"])) {
      $query = DB::insert('checklist_item', array('checklist_item', 'profile_id', 'datetime'));
      foreach ($data["checklist_items"] as $checklist_item) {
        $query->values(array($checklist_item, $profile_id, date('Y-m-d H:i:s')));
      }
      $query->execute();
    }

    /* 			if(!empty($data["docu_file"])){
      $query = DB::insert('documents', array('client_id','user_id','profile_id','name','doc_name','created_at'));
      foreach($data["docu_file"] as $file){
      $query->values(array($profile[0]["client_id"], $profile[0]["user_id"], $profile_id, $file['name'], $file['path'], $data['created_at']));
      }
      $query->execute();
      } */
  }

  public function getProfilesByType($typeid, $userid) {
    return DB::select()
                    ->from('profiles')
                    ->where('user_id', '=', $userid)
                    ->where('type_id', '=', $typeid)
                    ->execute()
                    ->as_array();
  }

  public function getTypeBySlag($slag) {
    return DB::select('id')
                    ->from('equipment_types')
                    ->where('slag', '=', $slag)
                    ->execute()
                    ->as_array();
  }

  public function getProfilesById($id) {
    $data = DB::select()
            ->from('profiles')
            ->where('id', '=', $id)
            ->execute()
            ->as_array();

    if ($data) {
      return $data[0];
    }
  }

  public function getQrimgById($id) {
    $data = DB::select('qrimg_name')
            ->from('qrimage')
            ->where('profile_id', '=', $id)
            ->execute()
            ->as_array();
    //print_r($data);exit;
    if ($data) {
      return $data[0]['qrimg_name'];
    }
  }

}

?>