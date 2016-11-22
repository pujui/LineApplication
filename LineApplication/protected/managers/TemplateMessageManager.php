<?php
class TemplateMessageManager{

    public $parent = null;
    
    public function KILLER($userId, $list){
        $messages = [];
        $action = ['type' => 'message', 'label' => 'open', 'text' => '/kill'];
        foreach ($list as $number=>$row){
            if($row['status'] == $this->ROLE_STATUS['NORMAL']){
                $action['label'] = sprintf('Player %d. %s', $number, $row['displayName']);
                $action['text'] = sprintf('/kill %d', $number);
                $messages[] = $action;
            }
        }
        var_dump($messages);
        $this->parent->actionPushTemplateButonMessages($userId, '虐殺名單', '選擇對象', $messages);
    }
}