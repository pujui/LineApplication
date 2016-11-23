<?php
class SentenceCase{
    
    private $patterns = [
        '/open' => ['/我/i', '/開|要/i', '/房/i',],
        '/start' => ['/我/i', '/開|要/i', '/遊戲/i',],
        '/kill' => ['/我/i', '/要|想/i', '/殺/i',],
        '/peep' => ['/我/i', '/要|想/i', '/看/i',],
        '/help' => ['/我/i', '/要|想/i', '/救/i',],
    ];
	
    public function getResult($message){
        
        foreach ($this->patterns as $command=>$patterns){
            $status = true;
            foreach ($patterns as $pattern){
                if(!preg_match($pattern, $message)){
                    $status = false;
                    break;
                }
            }
            if($status) return $command;
        }
        return $message;
    }
}