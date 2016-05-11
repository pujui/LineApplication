<?php
class BaseDAO{

    protected $db;

    public function __construct(){
        $this->db = Yii::app()->db;
    }
    
    protected function bindQuery($sql, $params){
        $this->db->createCommand($sql)->execute($params);
    }
    
    protected function getCommand($sql = '', $params = array()){
        $command = $this->db->createCommand($sql);
        if(!empty($params)){
            $command->bindValues($params);
        }
        return $command;
    }
    
    protected function queryRow($sql, $params = array()){
        return $this->getCommand($sql)->queryRow(true, $params);
    }
    
    protected function queryAll($sql, $params = array()){
        return $this->getCommand($sql)->queryAll(true, $params);
    }
}