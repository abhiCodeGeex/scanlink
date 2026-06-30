<?php


class Model_Survey extends Model {

  public function insert_survey($survey, $userid, $profile_id,$update_or_not="1") {
		
    $client_id = DB::select('client_id')
            ->from('users')
            ->where('id', '=', $userid)
            ->execute()
            ->as_array();
    $clientid = $client_id[0]['client_id'];
    $type_id = DB::select('id')
            ->from('equipment_types')
            ->where('slag', '=', $survey['type'])
            ->execute()
            ->as_array();
    $typeid = $type_id[0]['id'];



    $dataFill = array(
        "type_id" => $typeid,
        "name" => $survey["txtname"],
        "code_profile_name" => $survey["code_profile_name"],
        "code_type" => $survey['code_type'],
        "form_submission_format" => $survey['form_submission_format'],
        "update_or_not" => $update_or_not,
        "activation_start_date"=>$survey["activation_start_date"],
		  "activation_end_date"=>$survey["activation_end_date"],
        "created_at" => $survey['created_at'],
        "updated_at" => date("Y-m-d H:i:s")
    );


    

    $query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
    $query->execute();

	
	

    return $profile_id;
  }

  public function update_survey($data, $profile_id) {

    $profile = DB::select('*')
            ->from('profiles')
            ->where('id', '=', $profile_id)
            ->execute()
            ->as_array();

    $dataFill = array("name" => $data["txtname"],
		"code_profile_name" => $data["code_profile_name"],
        "form_submission_format" => $data['form_submission_format'],
        "code_type" => $data["code_type"],
        "activation_start_date"=>$data["activation_start_date"],
        "activation_end_date"=>$data["activation_end_date"]
    );
   
    $query = DB::update('profiles')->set($dataFill)->where('id', '=', $profile_id);
    $query->execute();

    
    return $profile_id;
    
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
            ->and_where('profiles.deleted', '=', 0)
            ->and_where('client_id', '=', Session::instance()->get('client_id'))
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