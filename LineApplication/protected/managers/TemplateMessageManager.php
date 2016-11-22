<?php
class TemplateMessageManager{

    public $parent = null;
    
    public function KILLER($userId, $list){
        $messages = [];
        $action = ['type' => 'message', 'label' => '', 'text' => '/kill %d'];
        foreach ($list as $number=>$row){
            if($row['status'] == 'NORMAL'){
                $action['label'] = sprintf('Player %d. %s', $number+1, $row['displayName']);
                $action['text'] = sprintf('/kill %d', $number+1);
                $messages[] = $action;
            }
        }
        $this->parent->actionPushTemplateButonMessages($userId, '虐殺名單-%d', '選擇對象', $messages);
    }

    public function PEEPER($userId, $list){
        $messages = [];
        $action = ['type' => 'message', 'label' => '', 'text' => '/peep %d'];
        foreach ($list as $number=>$row){
            if($row['status'] == 'NORMAL'){
                $action['label'] = sprintf('Player %d. %s', $number+1, $row['displayName']);
                $action['text'] = sprintf('/peep %d', $number+1);
                $messages[] = $action;
            }
        }
        $this->parent->actionPushTemplateButonMessages($userId, '偷窺名單-%d', '選擇對象', $messages);
    }

    public function HELPER($userId, $list){
        $messages = [];
        $action = ['type' => 'message', 'label' => '', 'text' => '/help %d'];
        foreach ($list as $number=>$row){
            if($row['status'] == 'NORMAL'){
                $action['label'] = sprintf('Player %d. %s', $number+1, $row['displayName']);
                $action['text'] = sprintf('/help %d', $number+1);
                $messages[] = $action;
            }
        }
        $this->parent->actionPushTemplateButonMessages($userId, '救援名單-%d', '選擇對象', $messages);
    }
}