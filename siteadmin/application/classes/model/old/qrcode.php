<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Qrcode extends Model{   	
	public function saveqrcode($proid, $qrimg){		
		$qrid = DB::insert('qrimage', array('profile_id','qrimg_name','created_at'))
			->values(array($proid,$qrimg,date('Y-m-d H:i:s')))
			->execute();		
		return $qrid[0];
	}
	
	public function deleteQRCodeByProfileId($profile_id){
		DB::delete('qrimage')->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function getQRCodeByProfileId($profile_id){
		$qrimage = DB::select('*')->from('qrimage')->where('qrimage.profile_id', '=', $profile_id)
		->execute()
		->as_array();
		if($qrimage){
			return $qrimage[0];
		}else{
			return array();
		}
	}
	
	public function getQRCodeById($id){
		$qrimage = DB::select('*')->from('qrimage')->where('qrimage.id', '=', $id)
		->execute()
		->as_array();
		if($qrimage){
			return $qrimage[0];
		}else{
			return array();
		}
	}
}
?>