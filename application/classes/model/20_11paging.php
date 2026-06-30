<?php

class Model_Paging extends Model{
	
	public $entity="";
	public $perpage="";
	public $count=0;
	public $pages=0;
	public $filter="";
	public $filter_on="";
	public $results=NULL;
	public $curpage=1;
	public $showlinks=5;
	public $params=NULL;
	public $links=NULL;
	
	
	public function getFilteredResult($entiry, $filter="",$filter_on,$curpage=1,$perpage=10,$params=array()){
		$this->entity = $entiry;
		$this->filter=$filter;
		$this->perpage=$perpage;
		$this->curpage=$curpage;
		$this->filter_on=$filter_on;
		$this->params=$params;
		
		$this->countResult();
		$this->countPages();
		
		$this->getResults();
		$this->getLinks();

		
		return array('results' => $this->results, 'links'=>$this->links);
		
	}
	
	public function getResults(){
		
		$ul = ($this->curpage-1) * $this->perpage;
		
		if($this->filter!==""){
			$query = DB::query(Database::SELECT, "SELECT profiles.*, qrimage.id as 'qr_id', qrimage.qrimg_name FROM profiles, qrimage where qrimage.profile_id = profiles.id and profiles.type_id = '".$this->params['type_id']."' and client_id = '".$this->params['client_id']."' and deleted = 0 and ".$this->filter_on." LIKE '".$this->filter."%' order by ".$this->filter_on." ASC limit ".$ul.",".$this->perpage);
			/*$query = DB::select()->from($this->entity)
			->where($this->filter_on, 'LIKE', $this->filter.'%')
			->order_by($this->filter_on, 'ASC')->limit($this->perpage)->offset($ul);*/			
			
		}else{
			//$query = DB::select()->from($this->entity)->order_by($this->filter_on, 'ASC')->limit($this->perpage)->offset($ul);
			$query = DB::query(Database::SELECT, "SELECT profiles.*, qrimage.id as 'qr_id', qrimage.qrimg_name FROM profiles, qrimage where qrimage.profile_id = profiles.id and profiles.type_id = '".$this->params['type_id']."' and client_id = '".$this->params['client_id']."' and deleted = 0 order by ".$this->filter_on." ASC limit ".$ul.",".$this->perpage);
		}
		
		/*foreach($this->params as $key => $val){
			$query->and_where($key,"=",$val);
		}*/	
		
 		$this->results = $query->execute()->as_array();

	}
	
	public function countResult(){
		$query="";
		if($this->filter!=="" && $this->filter_on!==""){
			//$query = DB::select('id')->from($this->entity)->where($this->filter_on, 'LIKE', $this->filter.'%');
			$query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles, qrimage where qrimage.profile_id = profiles.id and profiles.type_id = '".$this->params['type_id']."' and client_id = '".$this->params['client_id']."' and deleted = 0 and ".$this->filter_on." LIKE '".$this->filter."%");
		}else{
			//$query = DB::select('id')->from($this->entity);
			$query = DB::query(Database::SELECT, "SELECT profiles.id FROM profiles, qrimage where qrimage.profile_id = profiles.id and profiles.type_id = '".$this->params['type_id']."' and client_id = '".$this->params['client_id']."' and deleted = 0");
		}
		
		/*foreach($this->params as $key => $val){
			$query->and_where($key,"=",$val);
		}*/		

		
 		$this->count = count($query->execute()->as_array());
	}
	
	public function countPages(){
		$pages = $this->count/$this->perpage;
		$this->pages = is_real($pages)?(int)$pages+1:$pages;
	}
	
	public function getLinks(){
		$links=array();
		
		if($this->pages>1){
			if($this->curpage!=1){
				$first = array('label'=>'&lt;&lt;','href'=>'getResults(1,\''.$this->filter.'\')','myClass'=>'pagelink');
				array_push($links,$first);
			
				$prev = array('label'=>'&lt;','href'=>'getResults('.($this->curpage-1).',\''.$this->filter.'\')','myClass'=>'pagelink');
				array_push($links,$prev);
			}
			
			for($i=1;$i<=$this->pages;$i++){
				$class="";
				if($this->curpage==$i){
					$class="pagelink current";
				}else{
					$class="pagelink";
				}
				array_push($links,array('label'=>$i,'href'=>'getResults('.$i.',\''.$this->filter.'\')','myClass'=>$class));
			}
			
			if($this->curpage!=$this->pages){
				$next = array('label'=>'&gt;','href'=>'getResults('.($this->curpage+1).',\''.$this->filter.'\')','myClass'=>'pagelink');
				array_push($links,$next);
			
				$last = array('label'=>'&gt;&gt;','href'=>'getResults('.$this->pages.',\''.$this->filter.'\')','myClass'=>'pagelink');
				array_push($links,$last);
			}
		}
		$this->links=$links;
	}
	
}
?>