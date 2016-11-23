<?php
class SentenceCase{
    
    private $patterns = [
        '/^我\s+開\s+遊戲\s*/' => '/start',
        '/^我\s+開\s+房\s*/' => '/open',
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