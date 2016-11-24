<?php
class SentenceCase{
    
    private $patterns = [
        '/open'     => ['/開/i', '/房/i',],
        '/start'    => ['/始/i', '/遊戲/i',],
        '/status'   => ['/狀|情/i',],
        '/kill'     => ['/殺/i',],
        '/peep'     => ['/看/i',],
        '/help'     => ['/救/i',],
        '/vote'     => ['/投票/i',],
        '/close'     => ['/關閉|刪除/i',],
    ];

    public function getResult($message){
        $mainPatterns = [
            'OK' => ['/我/i']
        ];
        $subMainPatterns = [
            'OK' => ['/要|查|能|刪|關/i']
        ];
        $r = $this->_main($mainPatterns, $message);
        if($r !== FALSE){
            $r = $this->_main($subMainPatterns, $message);
            if($r !== FALSE){
                $r = $this->_main($this->patterns, $message);
                if($r !== FALSE){
                    return $r;
                }
            }
        }
        return $message;
    }

    private function _main($list, $message){
        foreach ($list as $command=>$patterns){
            $status = true;
            foreach ($patterns as $pattern){
                if(!preg_match($pattern, $message)){
                    $status = false;
                    break;
                }
            }
            if($status) return $command;
        }
        return false;
    }

}