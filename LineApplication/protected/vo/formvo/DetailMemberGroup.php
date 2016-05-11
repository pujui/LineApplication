<?php
class DetailMemberGroup{
	
	public $userId;
	
	public $creditTotal;
	
	public $debitTotal;
	
	public function setData($row){
		if(empty($row) || !is_array($row)){
			return false;
		}
		$this->userId = $row['owner'];
		$this->creditTotal = $row['creditTotal'];
		$this->debitTotal = $row['debitTotal'];
		return true;
	}
	
}