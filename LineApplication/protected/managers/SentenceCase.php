<?php
class SentenceCase{
    
    private $patterns = [
        "/^[\x4e00-\x9fa5]/u" => '/start',
    ];
	
    public function getResult($message){
        foreach ($this->patterns as $pattern=>$command){
            if(preg_match($pattern, $message)){
                return $command;
            }
        }
        return $message;
    }
}