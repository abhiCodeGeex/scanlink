<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Video extends Model{   	
	public function getVideoByProfileId($id){
		$videos = DB::select('*')->from('video')->where('video.profile_id', '=', $id)
		->execute()
		->as_array();
		
		if($videos){		
			return $videos;
		}else{
			return array();
		}
	}
	
	public function deleteVideoByProfileId($profile_id){
		DB::delete('video')->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function getVideoById($id){
		try{
			$video = DB::select('*')->from('video')->where('video.id', '=', $id)
			->execute()
			->as_array();
			
			if($video){		
				return $video[0];
			}else{
				return array();
			}
		}
		catch(Exception $e){}
	}
	
	public function getVideoByName($id){
		try{
			$video = DB::select('video_name')->from('video')->where('video.id', '=', $id)
			->execute()
			->as_array();
			
			if($video){		
				$videoname = $video[0]['video_name'];
				$videoRs = DB::select('id')->from('video')->where('video.video_name', '=', $videoname)->execute()->as_array();
				return $videoRs;
			}else{
				return array();
			}
		}
		catch(Exception $e){}
	}
	
	public function getAllVideos(){
		$videos = DB::select('*')->from('video')
		->execute()
		->as_array();
		
		if($videos){		
			return $videos;
		}else{
			return array();
		}
	}
	
	
	
	public function getVideosByClient($userid){
		try{
			$client_id = DB::select('client_id')
			->from('users')
			->where('id','=',$userid)
			->execute()
			->as_array();
			
			$videos = DB::select('*')->from('video')
			->where('client_id','=',$client_id[0]['client_id'])
			->execute()
			->as_array();
			
			if($videos){		
				return $videos;
			}else{
				return array();
			}
		}
		catch(Exception $e){}
		
	}
	
	public function deleteVideoById($id){
		DB::delete('video')->where('id', '=', $id)->execute();
	}
	
	
	
}
?>