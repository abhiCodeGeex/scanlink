<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Userinfo extends Model{
	public function insert_user_info($data){
			
		$id = DB::insert('user_info', array('user_name','user_email', 'profile_id'))
            ->values(array($data['user_name'], $data['user_email'], $data["profile_id"]))
            ->execute();
		
        return $id[0];
	  }

	public function getUserinfoByProfileId($id){
		$user_info=DB::select('*')->from('user_info')
		->where('profile_id', '=', $id)
		->order_by('entry_date')
		->execute()
		->as_array();
		
		if($user_info):
			return $user_info;
		else:
			return array();
		endif;
	}

}
?>