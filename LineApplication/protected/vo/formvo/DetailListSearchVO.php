<?php
class DetailListSearchVO{
    
    /**
     * 發票開始時間至結束時間
     * @var unknown
     */
    public $start, $end;
    
    public $receipt;
    
    public $time;
    
    public $year, $month;
    
    public $ownerId;
    
    public $sort;
    
    public $rsort;
    
    public $field;
    
    public $sortType = array(
        'a' => 'd',
        'd' => 'a',
    );

    public $sortFieldType = array(
        'date'   => true,
        'credit' => true,
        'debit'  => true,
        'owner'  => true,
    );
    
}