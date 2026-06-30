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
	
	public function getChecklistItemDataByProfileID($id,$dura){
		$query = "SELECT `checklist_item`.*, (select count(cic.`checklist_item_id`) FROM `checklist_item_checked` cic where cic.`checklist_item_id` = `checklist_item`.`id`";
		if($dura == 'month'){
			$query .= " and MONTH( cic.`datetime` ) = MONTH('".date('Y-m-d H:i:s')."')";
		}
		if($dura == 'week'){
			$query .= " and WEEKOFYEAR(cic.`datetime`)=WEEKOFYEAR('".date('Y-m-d H:i:s')."')";
		}
		if($dura == 'day'){
			$query .= " and DATE(cic.`datetime`) = DATE('".date('Y-m-d H:i:s')."')";
		}
		/*if($dura == 'twoHours'){
			$query .= " and cic.`datetime` >= NOW() - INTERVAL 2 HOUR";
		}*/
		$query .= ") as 'cnt' FROM `checklist_item` where `checklist_item`.`profile_id` = ".$id;
		/*$query = "SELECT `checklist_item`.*,count(`checklist_item_checked`.`checklist_item_id`) as 'cnt' FROM `checklist_item` left join `checklist_item_checked` on `checklist_item`.`id` = `checklist_item_checked`.`checklist_item_id` where `checklist_item`.`profile_id` = ".$id;
		if($dura == 'month'){
			$query .= " and MONTH( `checklist_item_checked`.`datetime` ) = MONTH( NOW( ) )";
		}
		if($dura == 'week'){
			$query .= " and WEEKOFYEAR(`checklist_item_checked`.`datetime`)=WEEKOFYEAR(NOW())";
		}
		if($dura == 'day'){
			$query .= " and DATE(`checklist_item_checked`.`datetime`) = DATE(NOW())";
		}
		if($dura == 'twoHours'){
			$query .= " and `checklist_item_checked`.`datetime` >= NOW() - INTERVAL 2 HOUR";
		}
		*/
		//$query .= " group by `checklist_item_checked`.`checklist_item_id`";
		$checklist_items = DB::query(Database::SELECT,$query)->execute()->as_array();
/*		$checklist_items = 	DB::select()->from('checklist_item')
						->join('checklist_item_checked','LEFT')->on('checklist_item_checked.checklist_item_id', '=','checklist_item.id')
						->where('checklist_item.profile_id', '=', $id)
						->group_by('checklist_item_checked.checklist_item_id')
						->execute()
						->as_array(); */
		return $checklist_items;
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