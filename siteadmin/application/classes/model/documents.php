<?php
/**
 * Description of admin
 *
 * @author Atul Kamani
 */
class Model_Documents extends Model{   	
	public function getDocumentsByProfileId($id){
		$documents = DB::select('*')->from('documents')->where('documents.profile_id', '=', $id)
		->execute()
		->as_array();
		
		if($documents){		
			return $documents;
		}else{
			return array();
		}
	}
	
	public function deleteDocumentByProfileId($profile_id){
		DB::delete('documents')->where('profile_id', '=', $profile_id)->execute();
	}
	
	public function getDocumentById($id){
		try{
			$document = DB::select('*')->from('documents')->where('documents.id', '=', $id)
			->execute()
			->as_array();
			
			if($document){		
				return $document[0];
			}else{
				return array();
			}
		}
		catch(Exception $e){}
	}
	
	public function deleteDocumentById($id){
		DB::delete('documents')->where('id', '=', $id)->execute();
	}
	
	public function saveTempDocument($userid,$name,$path){
		try{	
			$client_id = DB::select('client_id')->from('users')->where('id','=',$userid)->execute()->as_array();
			$clientid = $client_id[0]['client_id'];
			$query = DB::insert('documents', array('client_id','user_id','profile_id','name','doc_name','created_at'))
					->values(array($clientid, $userid, 0, $name, $path, date("Y-m-d H:i:s")))
					->execute();
			return $query;
		}
		catch(Exception $e){}
	}
	
	public function saveDocFiles($files,$profile_id){
		try{
			foreach($files as $key=>$file){
				$document = DB::select('*')->from('documents')->where('documents.id', '=', $file)->execute()->as_array();
				
				if($document){
					$old="images/tmp_files/".$document[0]["doc_name"];
					$new1= "../images/doc/".$document[0]["doc_name"];
					$new= "images/doc/".$document[0]["doc_name"];
		
					if(!empty($old) && file_exists($old)){
						if (copy($old,$new1)) {
							@unlink(realpath($old));
							//$document = DB::update('*')->from('documents')->where('documents.id', '=', $id);
							$query = DB::update('documents')->set(array("doc_name"=>$new,"profile_id"=>$profile_id))->where('id', '=', $file)->execute();
						}
					}
				}
			}
		}
		catch(Exception $e){}
	}
	
	public function getTempDocuments(){
		$documents = DB::select('*')->from('documents')
					->where('profile_id', '=', "0")
					->and_where('created_at', '<',date("Y-m-d H:i:s", mktime(date("H"),date("i"),date("s"),date("m"),date("d") - 1, date("Y"))))->execute()->as_array();
					
		if($documents){
			return $documents;
		}else{
			return array();
		}
	}		
}
?>