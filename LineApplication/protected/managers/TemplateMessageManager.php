<?php
class TemplateMessageManager{

    public $parent = null;
    
    public function KILLER($userId, $list){
        $messages = [];
        $action = ['type' => 'message', 'label' => 'open', 'text' => '/kill'];
        foreach ($list as $number=>$row){
            if($row['status'] == 'NORMAL'){
                $action['label'] = sprintf('Player %d. %s', $number, $row['displayName']);
                $action['text'] = sprintf('/kill %d', $number);
                $messages[] = $action;
            }
        }
        return [
                    'type'      => 'template',
                    'altText'   => '該樣板只能手機板看到',
                    'template'  => [
                        'type'  => 'buttons', 'title' => '虐殺名單', 'text'  => '選擇對象',
                        'actions' => $messages
                    ]
                ];
    }
}