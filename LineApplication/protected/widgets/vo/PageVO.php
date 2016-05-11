<?php
class PageVO{
	
	public $total = 0;
	
	public $limit = 10;
	
	public $page = 1;
	
	public $params = array();
	
	public $start = 0;
	
	public function createStartRange(){
		$this->start = ($this->page > 1)? ($this->page-1)*$this->limit: 0;
		if($this->start > $this->total){
			$this->start = 0;
		}
	}
}