<?php
/**
 * room
 *  /open 開新房間
 * @author PuJui
 *
 */
class LineBotController extends FrameController{

    const MESSAGE_ROOM_SETTING = "請選擇你要使用系統方式:\n/open 開啟遊戲防\n/start 開始遊戲\n/status 查詢房間狀態\n";
    const MESSAGE_BOT_SETTING = "請選擇你要使用系統方式:\n/join [room token] 加入遊戲防\n/leave [player number] 離開遊戲防\n/kill [player number] 殺死目標\n/help [player number] 拯救目標\n/arrest [player number] 逮捕目標\n";
    
    const TOKEN = 'Authorization: Bearer +EcHH6lvAf/A5uW512v+RANnVU/+tRQaMJkS4KkxtuAnmUjtwz9aiIx2V/5rYeH3k7vjxh4t549kvUUvZfSQc1KVDobOM7izPQgzMWqym+7NXH9xvcym0DlriDnGWZQ5Fy5XFA1m/I1WajRZHx9xyQdB04t89/1O/w1cDnyilFU=';

    protected $keyword = [
    ];

    public function actionJsonPush($id = '', $message = ''){
        $header = [
            'Content-Type: application/json',
            self::TOKEN
        ];
        /*
        $postData = [
            'to' => $id,
            'messages' => [
                [
                    'type' => 'template',
                    'altText' => 'this is a buttons template',
                    'template' => [
                        'type' => 'buttons',
                        'thumbnailImageUrl' => 'https://example.com/bot/images/image.jpg',
                        'title' => 'Menu',
                        'text' => 'Please select',
                        'actions' => [
                            ['type' => 'message', 'label' => 'open', 'text' => '/open']
                        ]
                    ]
                ]
            ]
        ];*/
        $postData = [
            'to' => $id,
            'messages' => [
                [
                    'type' => 'template',
                    'altText' => 'Are you sure?',
                    'template' => [
                        'type' => 'confirm',
                        'text' => 'Please select',
                        'actions' => [
                            ['type' => 'message', 'label' => 'Open', 'text' => '/open'],
                            ['type' => 'message', 'label' => 'Start', 'text' => '/start'],
                            ['type' => 'message', 'label' => 'Status', 'text' => '/status']
                        ]
                    ]
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
        echo $result;
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

        $command = explode(' ', trim($message));
        $roomManager = new RoomManager;
        $roomManager->parent = $this;
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
            }
        }else if($command[0] == '/join'){
            $roomManager->join($userId, $command, $response);
        }else if($command[0] == '/leave'){
            $roomManager->leave($userId, $command, $response);
        }else if($command[0] == '/kill'){
            $roomManager->kill($userId, $command, $response, 'KILLER');
        }else if($command[0] == '/next'){
            $roomManager->next($userId, $command, $response);
        }else if($command[0] == '/help'){
            $roomManager->kill($userId, $command, $response, 'HELPER');
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

    private function exitHook($response){
        echo json_encode($response);
        exit;
    }
}