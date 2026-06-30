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
	public $links=NULL;
	
	
	public function getFilteredResult($entiry, $filter="",$filter_on,$curpage=1,$perpage=10){
		$this->entity = $entiry;
		$this->filter=$filter;
		$this->perpage=$perpage;
		$this->curpage=$curpage;
		$this->filter_on=$filter_on;
		
		$this->countResult();
		$this->countPages();
		
		$this->getResults();
		$this->getLinks();
		
		return array('results' => $this->results, 'links'=>$this->links);
		
	}
	
	public function getResults(){
		
		$ul = ($this->curpage-1) * $this->perpage;
		
		if($this->filter!==""){
			if($this->entity == 'profiles'){
				/*$query = DB::select()->from($this->entity)
				->where($this->filter_on, 'LIKE', $this->filter.'%')
				->where('deleted', '=', 0)
				->order_by($this->filter_on, 'ASC')->limit($this->perpage)->offset($ul);
				*/
				$query = DB::query(Database::SELECT, '
				select * from '.$this->entity.' p
					where p.deleted = 0
					and '.$this->filter_on.' LIKE "'.$this->filter.'%" AND
					and p.type_id between 1 and 8
					or p.id in (select id from profiles p1 where p1.type_id = 9 and p1.update_or_not="1")
					order by '.$this->filter_on.' ASC
					LIMIT '.$ul.' , '.$this->perpage.'
				');
			}else if($this->entity == 'clients'){
				$query = DB::select('clients.*','users.checklist_option','users.customqr_option')->from('clients')
				->join('users')->on('clients.id','=','users.client_id')
			  	->where('users.role',"=",5)
				->and_where($this->filter_on, 'LIKE', $this->filter.'%')
				->order_by($this->filter_on, 'ASC')->limit($this->perpage)->offset($ul);
			}else{
				$query = DB::select()->from($this->entity)
				->where($this->filter_on, 'LIKE', $this->filter.'%')
				->order_by($this->filter_on, 'ASC')->limit($this->perpage)->offset($ul);
			}
			
			
		}else{
			if($this->entity == 'profiles'){
				/*$query = DB::select()->from($this->entity)
				->where('deleted', '=', 0)
				->where('type_id', '!=', 7)
				->order_by($this->filter_on, 'ASC')
				->limit($this->perpage)->offset($ul);
				*/
				$query = DB::query(Database::SELECT, '
				select * from '.$this->entity.' p
					where p.deleted = 0
					and p.type_id between 1 and 8
					or p.id in (select id from profiles p1 where p1.type_id = 9 and p1.update_or_not="1")
					order by '.$this->filter_on.' ASC
					LIMIT '.$ul.' , '.$this->perpage.'
				');
				
			}else if($this->entity == 'clients'){
				$query = DB::select('clients.*','users.checklist_option','users.customqr_option')->from('clients')
				->join('users')->on('clients.id','=','users.client_id')
			  	->and_where('users.role',"=",5)
				->order_by($this->filter_on, 'ASC')->limit($this->perpage)->offset($ul);
			}else{
				$query = DB::select()->from($this->entity)->order_by($this->filter_on, 'ASC')->limit($this->perpage)->offset($ul);
			}			
		}
 		$this->results = $query->execute()->as_array();

	}
	
	public function countResult(){
		$query="";
		if($this->filter!=="" && $this->filter_on!==""){
			if($this->entity == 'profiles'){
				//$query = DB::select('id')->from($this->entity)->where($this->filter_on, 'LIKE', $this->filter.'%')->where('deleted', '=', 0);
				$query = DB::query(Database::SELECT, '
				select * from '.$this->entity.' p
					where p.deleted = 0
					and '.$this->filter_on.' LIKE "'.$this->filter.'%" AND
					and p.type_id between 1 and 8
					or p.id in (select id from profiles p1 where p1.type_id = 9 and p1.update_or_not="1")
					order by id desc
				');
				exit;
			}
			else
			{
				$query = DB::select('id')->from($this->entity)->where($this->filter_on, 'LIKE', $this->filter.'%');
			}
		}else{
			if($this->entity == 'profiles'){
				//$query = DB::select('id')->from($this->entity)->where('deleted', '=', 0);
				$query = DB::query(Database::SELECT, '
				select * from '.$this->entity.' p
					where p.deleted = 0
					and p.type_id between 1 and 8
					or p.id in (select id from profiles p1 where p1.type_id = 9 and p1.update_or_not="1")
					order by id desc
				');
			}
			else
			{
				$query = DB::select('id')->from($this->entity);
			}
		}
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
				$first = array('label'=>'&lt;&lt;','href'=>'getResults(1,\''.$this->filter.'\')','myClass'=>'active');
				array_push($links,$first);
			
				$prev = array('label'=>'&lt;','href'=>'getResults('.($this->curpage-1).',\''.$this->filter.'\')','myClass'=>'active');
				array_push($links,$prev);
			}
			
			for($i=1;$i<=$this->pages;$i++){
				$class="";
				if($this->curpage==$i){
					$class="inactive";
				}else{
					$class="active";
				}
				array_push($links,array('label'=>$i,'href'=>'getResults('.$i.',\''.$this->filter.'\')','myClass'=>$class));
			}
			
			if($this->curpage!=$this->pages){
				$next = array('label'=>'&gt;','href'=>'getResults('.($this->curpage+1).',\''.$this->filter.'\')','myClass'=>'active');
				array_push($links,$next);
			
				$last = array('label'=>'&gt;&gt;','href'=>'getResults('.$this->pages.',\''.$this->filter.'\')','myClass'=>'active');
				array_push($links,$last);
			}
		}
		$this->links=$links;
	}
	
}
?>