<?php
class Controller_Checklistitem extends Controller_Template_Master {
	public function action_checklistCheck(){
		$checklist_id = $this->request->param('id');
		$ip = $_SERVER['REMOTE_ADDR'];
		$session_id = Session::instance()->id();
		$data = array('id'=>$checklist_id, 'ip'=>$ip, 'datetime'=>date('Y-m-d H:i:s'), 'session_id'=>$session_id);
		Model::factory('checklistitem')->checklistItemCheck($data);
		exit;
	}
	
	public function action_checklistUncheck(){
		$checklist_id = $this->request->param('id');
		$ip = $_SERVER['REMOTE_ADDR'];
		$session_id = Session::instance()->id();
		$data = array('id'=>$checklist_id, 'ip'=>$ip, 'datetime'=>date('Y-m-d H:i:s'), 'session_id'=>$session_id);
		
		Model::factory('checklistitem')->checklistItemUncheck($data);
		exit;
	}
}
?>