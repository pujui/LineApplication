<?php
class SentenceCase{
    
    private $patterns = [
        "/開啟/u" => '/start',
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