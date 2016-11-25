<?php
/**
 * room/group
 *  /open
 *  /start
 *  /status
 *  /next
 * user
 *  /kill
 *  /peep
 *  /help
 *  /vote
 *  /status
 *  /next
 * @author PuJui
 *
 */
class LineBotController extends FrameController{

    const TOKEN = 'Authorization: Bearer +EcHH6lvAf/A5uW512v+RANnVU/+tRQaMJkS4KkxtuAnmUjtwz9aiIx2V/5rYeH3k7vjxh4t549kvUUvZfSQc1KVDobOM7izPQgzMWqym+7NXH9xvcym0DlriDnGWZQ5Fy5XFA1m/I1WajRZHx9xyQdB04t89/1O/w1cDnyilFU=';

    public $templateMessageManager;

    public function actionTest($message = ''){
        $sentenceCase = new SentenceCase();
        echo $sentenceCase->getResult($message);
    }
    
    public function actionPush($id = '', $message = ''){
        $header = [
            'Content-Type: application/json',
            self::TOKEN
        ];
        $postData = [
            'to' => $id,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message,
                ]
            ]
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/message/push');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        $result = curl_exec($ch);
        curl_close($ch);
        //echo $result;
    }
    
    public function actionLeave($id = '', $message = ''){
        $header = [
            'Content-Type: application/json',
            self::TOKEN
        ];
        $postData = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.line.me/v2/bot/{$message}/{$id}/leave");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function actionProfile($userId = '', $r = ''){
        $header = [
            'Content-Type: application/json',
            self::TOKEN
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/profile/'.$userId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        if($r === '1') return json_decode($result, true);
        echo $result;
    }
    
    public function actionHook(){
        $input = json_decode(file_get_contents('php://input'), TRUE);
        $response = [ 'result' => false ];
        if(empty($input) || !is_array($input)){
            $this->exitHook($response);
        }
        $lineBotDAO = new LineBotDAO;
        $response = [
            'replyToken' => '', 
            'messages'   => [],
        ];
        $userId = $type = $message = '';
        $userData = [];
        foreach ($input as $key=>&$data){
            if($key == 0){
                // The message type are user or room.
                $type = $data['source']['type'];
                // The message id are user or room.
                $userId = $data['source'][$type.'Id'];
                // The message content.
                $message = $data['message']['text'];
                // Reply this message token.
                $response['replyToken'] = $data['replyToken'];
                // Get user profile
                $userData = $this->actionProfile($userId, '1');
                // Set user name
                $response['displayName'] = $userData['displayName'];
            }
            $data['displayName'] = $userData['displayName'];
            $lineBotDAO->addAccessLog($data);
        }
        $this->setUserMode($userId, $message, $response);

        $sentenceCase = new SentenceCase();
        $message = $sentenceCase->getResult($message);

        $command = explode(' ', trim($message));
        $roomManager = new RoomManager;
        $roomManager->parent = $this;
        $this->templateMessageManager = new TemplateMessageManager;
        $this->templateMessageManager->parent = $this;

        if($type == 'room' || $type == 'group'){
            if($message == '/open'){
                $roomManager->open($userId, $message, $response);
            }else if($message == '/start'){
                $setlist = $roomManager->start($userId, $message, $response);
            }else if($command[0] == '/create'){
                $setlist = $roomManager->create($userId, $command, $response);
            }else if($command[0] == '/role'){
                $roomManager->role($userId, $message, $response);
            }else if($command[0] == '/status'){
                $roomManager->status($userId, $message, $response);
            }else if($command[0] == '/reset'){
                $roomManager->reset($userId, $message, $response);
            }else if($command[0] == '/next'){
                $roomManager->next($userId, $command, $response);
            }else if($command[0] == '/close'){
                $roomManager->close($userId, $command, $response);
            }else if($command[0] == '/leave'){
                $roomManager->close($userId, $command, $response, 'anger');
                //$this->actionLeave($userId, $type);
            }
        }else if($command[0] == '/join'){
            $roomManager->join($userId, $command, $response);
        }else if($command[0] == '/leave'){
            $roomManager->leave($userId, $command, $response);
        }else if($command[0] == '/kill'){
            $roomManager->action($userId, $command, $response, 'KILLER');
        }else if($command[0] == '/vote'){
            $roomManager->action($userId, $command, $response, 'VOTE');
        }else if($command[0] == '/next'){
            $roomManager->next($userId, $command, $response, 'USER');
        }else if($command[0] == '/help'){
            $roomManager->action($userId, $command, $response, 'HELPER');
        }else if($command[0] == '/peep'){
            $roomManager->action($userId, $command, $response, 'PEEPER');
        }else if($command[0] == '/status'){
            $roomManager->status($userId, $message, $response, 'USER');
        }
        $this->exitHook($response);
    }

    private function setUserMode($userId, $message, &$response){
        $lineBotDAO = new LineBotDAO;
        $userInfo = $lineBotDAO->findUser($userId);
        if(empty($userInfo)){
            //$response['message']['text'] = self::MESSAGE_BOT_SETTING;
        }
        $user = ['userId' => $userId, 'mode' => 'test'];
        $lineBotDAO->setUser($user);
    }

    public function actionPushMessages($id = '', $messages = []){
        $header = [
            'Content-Type: application/json',
            self::TOKEN
        ];
        $postMessages = [ 'to' => $id, 'messages'  => $messages ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/message/push');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postMessages));
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function actionPushTemplateButonMessages($id = '', $title = '', $text = '', $messages = []){
        $header = [
            'Content-Type: application/json',
            self::TOKEN
        ];
        $template = [
                        'type'      => 'template',
                        'altText'   => '該樣板只能手機看到',
                        'template'  => [
                            'type'  => 'buttons', 'title' => $title, 'text'  => $text,
                            'actions' => []
                        ]
                    ];
        $b_list = array_chunk($messages, 16);
        $count = 1;
        foreach ($b_list as $s_list){
            $postData = [];
            $list = array_chunk($s_list, 4);
            foreach ($list as $row){
                $template['template']['title'] = sprintf($title, $count);
                $template['template']['actions'] = $row;
                $postData[] = $template;
                $count++;
            }
            $postMessages = [ 'to' => $id, 'messages'  => $postData ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.line.me/v2/bot/message/push');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postMessages));
            $result = curl_exec($ch);
            curl_close($ch);
        }
    }

    private function exitHook($response){
        echo json_encode($response);
        exit;
    }
}