<?php
class TemplateMessageManager{

    public $parent = null;
    
    public function KILLER($userId, $list){
        $messages = [];
        $action = ['type' => 'message', 'label' => 'open', 'text' => '/kill'];
        foreach ($list as $number=>$row){
            if($row['status'] == 'NORMAL'){
                $action['label'] = sprintf('Player %d. %s', $number+1, $row['displayName']);
                $action['text'] = sprintf('/kill %d', $number+1);
                $messages[] = $action;
            }
        }
        $this->parent->actionPushTemplateButonMessages($userId, '虐殺名單-%d', '選擇對象', $messages);
    }
}