<?php
class Model_checklistitem extends Model{  
	public function getChecklist_itemsByProfileId($id){

		$checklist_items = DB::select('*')->from('checklist_item')->where('checklist_item.profile_id', '=', $id)
		->execute()
		->as_array();
		
		if($checklist_items){		
			return $checklist_items;
		}else{
			return array();
		}	
	}
	
	public function deleteChecklistItemById($id){
		DB::delete('checklist_item')->where('id', '=', $id)->execute();
	}
	
	public function checklistItemCheck($data){
		$query = DB::insert('checklist_item_checked', array('checklist_item_id','ip','datetime','session_id'));
		$query->values($data)->execute();
	}
	
	public function getChecklist_itemsByProfileId_ForCurrentSession($id){
		$session_id = Session::instance()->id();
		$checklist_item_checked = DB::select('checklist_item.id')->from('checklist_item_checked')
							->join('checklist_item')->on('checklist_item.id', '=','checklist_item_checked.checklist_item_id')
							->where('checklist_item.profile_id', '=', $id)
							->where('checklist_item_checked.session_id', '=', $session_id)
							->execute()
							->as_array();
		
		$data = array();					
		if(!empty($checklist_item_checked)){
			foreach($checklist_item_checked as $val){
				array_push($data,$val['id']);
			}
		}
		return $data;
	}
	
	public function checklistItemUncheck($data){
		$checklist_item_checked = DB::select('*')->from('checklist_item_checked')
							->where('checklist_item_checked.checklist_item_id', '=', $data['id'])
							->where('checklist_item_checked.ip', '=', $data['ip'])
							->where('checklist_item_checked.session_id', '=', $data['session_id'])
							->execute()
							->as_array();
							

		if(!empty($checklist_item_checked)){
			DB::delete('checklist_item_checked')->where('checklist_item_checked.id', '=', $checklist_item_checked[0]['id'])->execute();	

		}else{
			echo "not found";
		}
		exit;

	}
	
	
}
?>