<?php
class Controller_Scanalytics extends Controller {
	public function action_index(){
		$id=$this->request->param("id");
		require_once("classes/shortURL.php");

		$clienturl  = Model::factory('client')->geturl($id);
		$url = URL::base().$clienturl."/".$id;
//		$url="http://www.webplanex.com/";
		$short_url = shortenUrl($url);
		$analytics = CreateAnalytics($short_url['shortLink']);
		//$checklist_item_data = Model::factory('checklistitem')->getChecklistItemDataByProfileID($id);
		//print_r($checklist_item_data);
		//exit;


		$this->template->content = View::factory('dashboard/scanalytics')
		->bind('analytics',$analytics);
	}
}
?>