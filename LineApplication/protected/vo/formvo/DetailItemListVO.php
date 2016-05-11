<?php
class DetailItemListVO{
	
	public $itemId;
	
	public $creditTotal;
	
	public $debitTotal;
	
	public function setData($row){
		if(empty($row) || !is_array($row)){
			return false;
		}
		$this->itemId = $row['itemId'];
		$this->creditTotal = $row['creditTotal'];
		$this->debitTotal = $row['debitTotal'];
		return true;
	}
	
}