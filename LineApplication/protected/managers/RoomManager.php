<?php
class RoomManager{
    const ROOM_EVENT_STOP = 'STOP';
    const ROOM_EVENT_START = 'START';
    const ROOM_EVENT_VOTE = 'VOTE';

    protected $MESSAGES = [
        'OPEN'                  => "遊戲房間已開啟\n1.請加入我(BOT)為好友\n2.並傳送房間代碼至BOT加入遊戲",
        'WAITE_STATUS'          => "遊戲房間狀態: %s, 玩家人數: %d\n開始遊戲請在此房間輸入/start",
        'JOIN'                  => "加入遊戲請輸入以下代碼傳送至我(BOT)",
        'JOIN_COMMAND'          => "/join %s",
        'START_STATUS'          => "遊戲房間狀態: %s, 玩家人數: %d\n遊戲已開始已無法加入遊戲只能觀看",
        'START_NOT_EXIST'       => "遊戲房間未開啟, 請先輸入/open開啟房間並確認所有人加入後再開始",
        'START_LIMIT'           => "遊戲人數最少四人",
        'START_ARLEADY'         => "遊戲已準備好, 角色分配結束",
        'JOIN_ROOM_NOT_EXIST'   => "遊戲房間不存在, 請確認是否複製錯誤",
        'JOIN_ROOM_SUCCESS'     => "已加入遊戲",
        'JOIN_ARLEADY_EXIST'    => "已在遊戲中",
        'JOIN_EXIST'            => "已在其他遊戲中, 請/leave後再加入",
        'LEAVE_NOT_EXIST'       => "你目前無在任何遊戲內",
        'LEAVE_SUCCESS'         => "已離開遊戲",
        'ROLE_CHECKED'          => "您角色為 - %s",
        'KILL_NOT_EXIST'        => "對象不存在",
        'KILL_ARLEADY_EXIT'     => "對象已離開遊戲",
        'KILL_ARLEADY_DEAD'     => "對象已死亡",
        'KILL_CHECKED'          => "殺害對象為 - %s",
        'KILL_SUCCESS'          => "%s被殺死了...",
        'KILL_AGAIN_SUCCESS'    => "%s被另一個人發現後鞭屍...",
        'KILL_AGAIN_FAILED'     => "被%s被鞭屍後的屍體嚇到尿褲子後死亡...",
        'HELP_SUCCESS'          => "%s被剛上廁所的人拯救了...",
        'DO_NOT_ACTION'         => "你無法執行您角色為 - %s",
        'NIGHT_PERSON_ACTION'   => "已有 %d 名夜貓子在夜間行動",
        'NIGHT_COMING'          => "當黑夜來臨了...",
        'MONING_COMING'         => "當清晨來臨了...",
        'DO_NOT_NEXT'           => "黑夜時間還沒到",
        'YOU_ARE_DEAD'          => "你已經死了",
        'RESET_SUCCESS'         => "遊戲房間已重新開始",
        'RESET_FAILED'          => "遊戲未結束無法重新開始",
        'PEEP_SUCCESS'          => "%s身分為%s ",
        'CHECKED_PERSON'        => "對象為 - %s",
        'KILLER_VICTORY'        => "殺手贏了",
        'KILLER_LOST'           => "殺手輸了",
        'VOTE_TO'               => "%s投%s為兇手",
        'VOTE_TOTAL'            => "%s獲得了%d票",
        'VOTE_MESSAGE'          => "=====投票結果=====",
        'VOTE_DEAD'             => "===============\n%s被大家當作兇手綁起來燒死了..",
        'VOTE_ACTION'           => "可以開始投票認為誰是兇手...",
    ];

    protected $ROOM_STATUS = [
        'CREATE'    => 'CREATE',
        'OPEN'      => 'OPEN',
        'START'     => 'START',
        'STOP'      => 'STOP',
        'VOTE'      => 'VOTE',
        'END'       => 'END',
        'JOIN'      => 'JOIN',
    ];

    protected $ROLE_STATUS = [
        'NORMAL'  => 'NORMAL',
        'DEAD'    => 'DEAD',
        'HELP'    => 'HELP',
        'ARREST'  => 'ARREST',
        'LEAVE'   => 'LEAVE'
    ];

    protected $ROLES = [
        'JOIN'      => 'JOIN',
        'KILLER'    => 'KILLER',
        'HELPER'    => 'HELPER',
        'POLICE'    => 'POLICE',
        'VILLAGER'  => 'VILLAGER',
        'PEEPER'    => 'PEEPER'
    ];
    public $parent = null;

    private $lineBotDAO;

    private $role = [
        ['role' => 'KILLER', 'roleName' => '殺手'],
        ['role' => 'PEEPER', 'roleName' => '偷窺者'],
        ['role' => 'HELPER', 'roleName' => '救援'],
        ['role' => 'POLICE', 'roleName' => '警察'],
        ['role' => 'VILLAGER', 'roleName' => '村民'],
    ];
    private $roleName = [
        'KILLER'    => "[殺手]\n可以殺死任何對象\n/kill [玩者代碼] \nexample: /kill 1",
        'HELPER'    => "[救援]\n可以再每回合隨意救活被殺手殺死對象(當然也可以救活自己)\n/help [玩者代碼] \nexample: /help 1",
        'PEEPER'    => "[偷窺者]\n可偷看一人職業",
        'POLICE'    => "[警察]\n無",
        'VILLAGER'  => "[村民]\n無",
    ];
    private $roleStatus = [
        'NORMAL'  => '存活中',
        'DEAD'    => '已死亡',
        'HELP'    => '存活中',
        'ARREST'  => '存活中',
        'LEAVE'   => '已逃離'
    ];
    private $events = [
        'STOP'     => '已動作',
        'START'    => '未動作'
    ];

    public function __construct(){
        $this->lineBotDAO = new LineBotDAO;
    }
    
    public function role($roomId, $message, &$response){
        $message = [ 'type' => 'text', 'text' => '' ];
        $message['text'] = implode(PHP_EOL.PHP_EOL, $this->roleName);
        $response['messages'][] = $message;
    }

    /**
     * Open the room by room
     * 1. Check the room is exist.
     *  1-1 If the room not exist then create this room.
     *  1-2 else return room status.
     * @param unknown $roomId
     * @param unknown $message
     * @param unknown $response
     */
    public function open($roomId, $message, &$response){
        $message = [ 'type' => 'text', 'text' => '' ];
        // If the room not exist then create this room.
        $roomInfo = $this->lineBotDAO->findRoom($roomId);
        if(empty($roomInfo)){
            // Create this room.
            $this->lineBotDAO->setRoom($roomId, $this->ROOM_STATUS['OPEN']);
            // Set room message
            $this->setRoomStatus($roomId, $this->ROOM_STATUS['CREATE'], $response);
        // else set room status message
        }else{
            $this->setRoomStatus($roomId, $roomInfo['status'], $response);
        }
    }

    /**
     * Join the room by user
     * @param unknown $userId
     * @param unknown $command
     * @param unknown $response
     */
    public function join($userId, $command, &$response){
        $message = [ 'type' => 'text', 'text' => '' ];
        $roomId = $command[1];
        $roomInfo = $this->lineBotDAO->findRoom($roomId);
        if(empty($roomInfo)){
            $message['text'] = $this->MESSAGES['JOIN_ROOM_NOT_EXIST'];
            $response['messages'][] = $message;
        }else if($roomInfo['status'] == $this->ROOM_STATUS['OPEN']){
            // Check your status
            $userLiveRoom = $this->lineBotDAO->findRoomUserIsLive($userId);
            if(!empty($userLiveRoom)){
                if($roomId === $userLiveRoom['roomId']){
                    $message['text'] = $response['displayName'].$this->MESSAGES['JOIN_ARLEADY_EXIST'];
                }else{
                    $message['text'] = $this->MESSAGES['JOIN_EXIST'];
                }
                return $response['messages'][] = $message;
            }
            $this->lineBotDAO->setRoomList($roomId, $userId, $response['displayName'], $this->ROLE_STATUS['NORMAL'], $this->ROLE_STATUS['JOIN']);
            // Set join message
            $message['text'] = $response['displayName'].$this->MESSAGES['JOIN_ROOM_SUCCESS'];
            $response['messages'][] = $message;
            // Set status message on room
            $this->setRoomStatus($roomId, $this->ROOM_STATUS['JOIN'], $response);
            // Set role message on room
            $this->setRoomRoleStatus($roomId, $response);
            // Push message for room
            $this->parent->actionPushMessages($roomId, $response['messages']);
        }else if($roomInfo['status'] == $this->ROOM_STATUS['START']){
            // Set status message on room
            $this->setRoomStatus($roomId, $roomInfo['status'], $response);
            // Set role message on room
            $this->setRoomRoleStatus($roomId, $response);
        }
    }


    /**
     * Create bot in room by room
     * @param unknown $roomId
     * @param unknown $command
     * @param unknown $response
     */
    public function create($roomId, $command, &$response){
        $message = [ 'type' => 'text', 'text' => '' ];
        $bot = $command[1];
        $roomInfo = $this->lineBotDAO->findRoom($roomId);
        if(empty($roomInfo)){
            $message['text'] = $this->MESSAGES['JOIN_ROOM_NOT_EXIST'];
            $response['messages'][] = $message;
        }else if($roomInfo['status'] == $this->ROOM_STATUS['OPEN'] && $bot == 'bot'){
            $userId = sha1(date('YmdHis').':'.$roomId.rand(0, 9999));
            $response['displayName'] = 'Bot'.date('His');
            $this->lineBotDAO->setRoomList($roomId, $userId, $response['displayName'], $this->ROLE_STATUS['NORMAL'], $this->ROLE_STATUS['JOIN']);
            // Set join message
            $message['text'] = $response['displayName'].$this->MESSAGES['JOIN_ROOM_SUCCESS'];
            $response['messages'][] = $message;
            // Set status message on room
            $this->setRoomStatus($roomId, $this->ROOM_STATUS['JOIN'], $response);
            // Set role message on room
            $this->setRoomRoleStatus($roomId, $response);
        }
    }

    /**
     * start game
     * @param unknown $roomId
     * @param unknown $message
     * @param unknown $response
     */
    public function start($roomId, $message, &$response){
        $message = [ 'type' => 'text', 'text' => '' ];
        $roomInfo = $this->lineBotDAO->findRoom($roomId);
        if(empty($roomInfo)){
            $message['text'] = $this->MESSAGES['START_NOT_EXIST'];
            $response['messages'][] = $message;
        }else if($roomInfo['status'] == $this->ROOM_STATUS['OPEN']){
            $list = $this->lineBotDAO->findRoomList($roomId);
            $totalPeople = count($list);
            if($totalPeople < 4){
                $message['text'] = $this->MESSAGES['START_LIMIT'];
                $response['messages'][] = $message;
            }else{
                // Change status for this room.
                $this->lineBotDAO->setRoom($roomId, $this->ROOM_STATUS['START']);
                $randomList = $list;
                $setList = [];
                foreach ($list as $row){
                    $setList[$row['id']] = $row;
                }
                shuffle($randomList);
                // Protect limit with role
                $checkProtectedNumber = rand(3, 4);
                foreach ($randomList as $key=>$user){
                    if($checkProtectedNumber < 0){
                        $r_k = ($key+1)%4;
                        $setList[$user['id']]['role'] = $this->role[$r_k]['role'];
                        $setList[$user['id']]['roleName'] = $this->roleName[$this->role[$r_k]['role']];
                        $checkProtectedNumber--;
                    }else{
                        $r_k = (rand(0, 999)*$user['id'])%4;
                        $setList[$user['id']]['role'] = $this->role[$r_k]['role'];
                        $setList[$user['id']]['roleName'] = $this->roleName[$this->role[$r_k]['role']];
                    }
                    $this->lineBotDAO->updateRoomList($roomId, $user['userId'], $setList[$user['id']]['role'], $this->ROLE_STATUS['NORMAL'], self::ROOM_EVENT_START, 'CLEAR');
                }
                // Set role message on room
                $message['text'] = $this->MESSAGES['START_ARLEADY'];
                $response['messages'][] = $message;
                $this->setRoomRoleStatus($roomId, $response);
                $message['text'] = $this->MESSAGES['NIGHT_COMING'];
                $response['messages'][] = $message;
                // Push message for everyone
                foreach ($setList as $user){
                    $message['text'] = sprintf($this->MESSAGES['ROLE_CHECKED'], $user['roleName']);
                    $this->parent->actionPushMessages($user['userId'], [$message]);
                }
            }
        }else if($roomInfo['status'] == $this->ROOM_STATUS['START']){
            // Set status message on room
            $this->setRoomStatus($roomId, $roomInfo['status'], $response);
        }
    }

    /**
     * Leave the room by user
     * @param unknown $userId
     * @param unknown $message
     * @param unknown $response
     */
    public function leave($userId, $message, &$response){
        $message = [ 'type' => 'text', 'text' => '' ];
        $userLiveRoom = $this->lineBotDAO->findRoomUserIsLive($userId);
        if(empty($userLiveRoom)){
            $message['text'] = $this->MESSAGES['LEAVE_NOT_EXIST'];
            $response['messages'][] = $message;
        }else{
            // set leave for room
            $this->lineBotDAO->deleteRoomList($userLiveRoom['roomId'], $userId);
            //$this->lineBotDAO->updateRoomList($userLiveRoom['roomId'], $userId, '', $this->ROLE_STATUS['LEAVE']);
            // Push message for room
            $message['text'] = $userLiveRoom['displayName'].$this->MESSAGES['LEAVE_SUCCESS'];
            $response['messages'][] = $message;
            $this->parent->actionPushMessages($userLiveRoom['roomId'], $response['messages']);
        }
    }

    /**
     * action by user
     * @param unknown $userId
     * @param unknown $command
     * @param unknown $response
     * @return string
     */
    public function action($userId, $command, &$response, $action){
        $message = [ 'type' => 'text', 'text' => '' ];
        $userLiveRoom = $this->lineBotDAO->findRoomUserIsLive($userId);
        if(empty($userLiveRoom)){
            $message['text'] = $this->MESSAGES['LEAVE_NOT_EXIST'];
            $response['messages'][] = $message;
        }else if($action == $this->ROLES['KILLER'] && $userLiveRoom['role'] != $this->ROLES['KILLER']){
            $message['text'] = sprintf($this->MESSAGES['DO_NOT_ACTION'], $userLiveRoom['role']);
            $response['messages'][] = $message;
        }else if($action == $this->ROLES['HELPER'] && $userLiveRoom['role'] != $this->ROLES['HELPER']){
            $message['text'] = sprintf($this->MESSAGES['DO_NOT_ACTION'], $userLiveRoom['role']);
            $response['messages'][] = $message;
        }else if($action == $this->ROLES['PEEPER'] && $userLiveRoom['role'] != $this->ROLES['PEEPER']){
            $message['text'] = sprintf($this->MESSAGES['DO_NOT_ACTION'], $userLiveRoom['role']);
            $response['messages'][] = $message;
        }else if(in_array($userLiveRoom['roomStatus'], [$this->ROOM_STATUS['START'], $this->ROOM_STATUS['VOTE']])){
            if($userLiveRoom['status'] == $this->ROLE_STATUS['DEAD']){
                $message['text'] = $this->MESSAGES['YOU_ARE_DEAD'];
                return $response['messages'][] = $message;
            }
            $actionRoomStatus = ($userLiveRoom['roomStatus'] == $this->ROOM_STATUS['VOTE'])? self::ROOM_EVENT_VOTE: self::ROOM_EVENT_STOP;
            $list = $this->lineBotDAO->findRoomList($userLiveRoom['roomId']);
            $totalPeople = count($list);
            if($command[1] == ''){
                $this->parent->templateMessageManager->$action($userId, $list);
                $message['text'] = '';
                return $response['messages'][] = $message;
            }else if($command[1] < 1 || $command[1] > $totalPeople){
                $message['text'] = $this->MESSAGES['KILL_NOT_EXIST'];
                return $response['messages'][] = $message;
            }
            $setList = $target = [];
            $self = $userLiveRoom;
            $actionCount = ($self['event'] == self::ROOM_EVENT_START)? 1: 0;
            $mustActionCount = 0;
            foreach ($list as $key=>$row){
                $row['killCount'] = 0;
                $row['voteCount'] = 0;
                if($key+1 == $command[1]){
                    if($row['status'] == $this->ROLE_STATUS['LEAVE']){
                        $message['text'] = $this->MESSAGES['KILL_ARLEADY_EXIT'];
                        return $response['messages'][] = $message;
                    }else if($row['status'] == $this->ROLE_STATUS['DEAD']){
                        $message['text'] = $this->MESSAGES['KILL_ARLEADY_DEAD'];
                        return $response['messages'][] = $message;
                    }
                    $target = $row;
                }
                $row['number'] = $key+1;
                if($row['status'] == $this->ROLE_STATUS['NORMAL']){
                    if($row['event'] == self::ROOM_EVENT_STOP){
                        $actionCount++;
                    }
                    if($actionRoomStatus == self::ROOM_EVENT_VOTE){
                        $mustActionCount++;
                    }else if(in_array($row['role'], [$this->ROLES['KILLER'], $this->ROLES['HELPER'], $this->ROLES['PEEPER']])){
                        $mustActionCount++;
                    }
                    $setList[$row['userId']] = &$row;
                }
                unset($row);
            }

            try {
                // transaction start
                $transaction = Yii::app()->db->beginTransaction();

                $this->lineBotDAO->updateRoomList($self['roomId'], $self['userId'], '', '', self::ROOM_EVENT_STOP, $target['userId']);
                $setList[$self['userId']]['toUserId'] = $target['userId'];

                // Push message for room
                $pushMessages = $voteMessage = [];
                if($actionRoomStatus == self::ROOM_EVENT_VOTE){
                    $voteMessage[] = sprintf($this->MESSAGES['VOTE_TO'], $self['displayName'], $target['displayName']);
                }else{
                    $message['text'] = sprintf($this->MESSAGES['NIGHT_PERSON_ACTION'], $actionCount);
                    $pushMessages[] = $message;
                }
                if($mustActionCount <= $actionCount){
                    $mergeMessage = $killMessage = $helpMessage = $peepMessage = [];
                    foreach ($setList as $row){
                        if($actionRoomStatus == self::ROOM_EVENT_VOTE){
                            $setList[$row['toUserId']]['voteCount']++;
                            $this->lineBotDAO->updateRoomList($row['roomId'], $row['userId'], '', '', self::ROOM_EVENT_STOP);
                        }else if($row['role'] == $this->ROLES['KILLER']){
                            if($setList[$row['toUserId']]['power'] != $this->ROLES['HELPER']){
                                $setList[$row['toUserId']]['status'] = $this->ROLE_STATUS['DEAD'];
                                $this->lineBotDAO->updateRoomList($row['roomId'], $row['toUserId'], '', $this->ROLE_STATUS['DEAD']);
                            }
                            if($setList[$row['toUserId']]['killCount'] == 0){
                                $killMessage[] = sprintf($this->MESSAGES['KILL_SUCCESS'], $setList[$row['toUserId']]['displayName']);
                            }else if($setList[$row['toUserId']]['killCount'] > 2){
                                $this->lineBotDAO->updateRoomList($row['roomId'], $row['userId'], '', $this->ROLE_STATUS['DEAD']);
                                $killMessage[] = sprintf($this->MESSAGES['KILL_AGAIN_FAILED'], $setList[$row['toUserId']]['displayName']);
                            }else{
                                $killMessage[] = sprintf($this->MESSAGES['KILL_AGAIN_SUCCESS'], $setList[$row['toUserId']]['displayName']);
                            }
                            $setList[$row['toUserId']]['killCount']++;
                        }else if($row['role'] == $this->ROLES['HELPER'] && $row['toUserId'] !=''){
                            $setList[$row['toUserId']]['power'] = $this->ROLES['HELPER'];
                            $setList[$row['toUserId']]['status'] = $this->ROLE_STATUS['NORMAL'];
                            $this->lineBotDAO->updateRoomList($row['roomId'], $row['toUserId'], '', $this->ROLE_STATUS['NORMAL']);
                            $helpMessage[] = sprintf($this->MESSAGES['HELP_SUCCESS'], $setList[$row['toUserId']]['displayName']);
                        }else if($row['role'] == $this->ROLES['PEEPER'] ){
                            $peepMessage[$row['userId']] = sprintf($this->MESSAGES['PEEP_SUCCESS'], $setList[$row['toUserId']]['displayName'], $this->roleName[$setList[$row['toUserId']]['role']]);
                        }
                    }
                    if($actionRoomStatus == self::ROOM_EVENT_VOTE){
                        $maxUserId = $maxVote = 0;
                        $voteMessage[] = $this->MESSAGES['VOTE_MESSAGE'];
                        foreach ($setList as $row){
                            if($row['status'] == $this->ROLE_STATUS['NORMAL']){
                                $voteMessage[] = sprintf($this->MESSAGES['VOTE_TOTAL'], $row['displayName'], $row['voteCount']);
                                if($row['voteCount'] >= $maxVote){
                                    $maxUserId = $row['userId'];
                                    $maxVote = $row['voteCount'];
                                }
                            }
                        }
                        $voteMessage[] = sprintf($this->MESSAGES['VOTE_DEAD'], $setList[$maxUserId]['displayName']);
                        $this->lineBotDAO->updateRoomList($self['roomId'], $maxUserId, '', $this->ROLE_STATUS['DEAD']);
                    }else{
                        $message['text'] = $this->MESSAGES['MONING_COMING'];
                        $pushMessages[] = $message;
                    }
                    $mergeMessage = array_merge($killMessage, $helpMessage);
                    $message['text'] = implode(PHP_EOL, $mergeMessage);
                    if(!empty($message['text'])) $pushMessages[] = $message;

                    // Check the game is end
                    $all_killer_del = false;
                    $all_normal_del = false;
                    foreach ($setList as $row){
                        if($row['status'] == $this->ROLE_STATUS['NORMAL']){
                            if($row['role'] == $this->ROLES['KILLER']){
                                $all_killer_del = true;
                            }else{
                                $all_normal_del = true;
                            }
                        }
                    }

                    // Change status for this room.
                    if($all_normal_del === true && $all_killer_del == true){
                        if($actionRoomStatus == self::ROOM_EVENT_VOTE){
                            $this->lineBotDAO->setRoom($userLiveRoom['roomId'], $this->ROOM_STATUS['STOP']);
                        }else{
                            $this->lineBotDAO->setRoom($userLiveRoom['roomId'], $this->ROOM_STATUS['VOTE']);
                            $this->lineBotDAO->updateRoomListClear($userLiveRoom['roomId']);
                            $message['text'] = $this->MESSAGES['VOTE_ACTION'];
                            $pushMessages[] = $message;
                        }
                    }else{
                        $this->lineBotDAO->setRoom($userLiveRoom['roomId'], $this->ROOM_STATUS['END']);
                        if($all_killer_del == true){
                            $message['text'] = $this->MESSAGES['KILLER_VICTORY'];
                        }else{
                            $message['text'] = $this->MESSAGES['KILLER_LOST'];
                        }
                        $pushMessages[] = $message;
                    }
                }
                $message['text'] = implode(PHP_EOL, $voteMessage);
                if(!empty($message['text'])) $pushMessages[] = $message;
                $this->parent->actionPushMessages($userLiveRoom['roomId'], $pushMessages);
                foreach ($setList as $row){
                    $this->parent->actionPushMessages($row['userId'], $pushMessages);
                }
                // set return message
                $message['text'] = sprintf($this->MESSAGES['CHECKED_PERSON'], $target['displayName']);
                $response['messages'][] = $message;

                if(!empty($peepMessage)){
                    foreach ($peepMessage as $peep_u=>$peep_m){
                        $message['text'] = $peep_m;
                        $this->parent->actionPushMessages($peep_u, [$message]);
                    }
                }

                // transaction commit
                $transaction->commit();
            }catch (Exception $e){
                // transaction rollback
                $transaction->rollback();
            }
        }else{
            $message['text'] = $this->MESSAGES['DO_NOT_NEXT'];
            $response['messages'][] = $message;
        }
    }

    /**
     * next by room and user
     * @param unknown $userId
     * @param unknown $message
     * @param unknown $response
     */
    public function next($roomId, $message, &$response, $person = 'ROOM'){
        $message = [ 'type' => 'text', 'text' => '' ];
        if($person == 'ROOM'){
            $userLiveRoom = $this->lineBotDAO->findRoom($roomId);
            $userLiveRoom['roomStatus'] = $userLiveRoom['status'];
            $message['text'] = $this->MESSAGES['START_NOT_EXIST'];
        }else if($person == 'USER'){
            $userLiveRoom = $this->lineBotDAO->findRoomUserIsLive($roomId);
            $message['text'] = $this->MESSAGES['LEAVE_NOT_EXIST'];
        }
        if(empty($userLiveRoom)){
            $response['messages'][] = $message;
        }else if($userLiveRoom['roomStatus'] == $this->ROOM_STATUS['STOP']){
            $list = $this->lineBotDAO->findRoomList($userLiveRoom['roomId']);
            foreach ($list as $row){
                $this->lineBotDAO->updateRoomList($roomId, $row['userId'], '', '', self::ROOM_EVENT_START, 'CLEAR');
            }
            // Change status for this room.
            $this->lineBotDAO->setRoom($userLiveRoom['roomId'], $this->ROOM_STATUS['START']);
            $message['text'] = $this->MESSAGES['NIGHT_COMING'];
            $pushMessages['messages'] = [$message];
            $this->setRoomRoleStatus($userLiveRoom['roomId'], $pushMessages);
            $this->parent->actionPushMessages($userLiveRoom['roomId'], $pushMessages['messages']);
        }else{
            $message['text'] = $this->MESSAGES['DO_NOT_NEXT'];
            $response['messages'][] = $message;
        }
    }

    /**
     * next by room and user
     * @param unknown $roomId
     * @param unknown $message
     * @param unknown $response
     */
    public function status($roomId, $message, &$response, $person = 'ROOM'){
        $message = [ 'type' => 'text', 'text' => '' ];
        if($person == 'ROOM'){
            $roomInfo = $this->lineBotDAO->findRoom($roomId);
            $message['text'] = $this->MESSAGES['START_NOT_EXIST'];
        }else if($person == 'USER'){
            $roomInfo = $this->lineBotDAO->findRoomUserIsLive($roomId);
            $message['text'] = $this->MESSAGES['LEAVE_NOT_EXIST'];
        }
        if(empty($roomInfo)){
            $response['messages'][] = $message;
        }else{
            $this->setRoomStatus($roomInfo['roomId'], $this->ROOM_STATUS['OPEN'],  $response);
            $this->setRoomRoleStatus($roomInfo['roomId'], $response);
        }
    }

    /**
     * reset by room
     * @param unknown $roomId
     * @param unknown $message
     * @param unknown $response
     */
    public function reset($roomId, $message, &$response){
        $message = [ 'type' => 'text', 'text' => '' ];
        $roomInfo = $this->lineBotDAO->findRoom($roomId);
        if(empty($roomInfo)){
            $message['text'] = $this->MESSAGES['START_NOT_EXIST'];
            $response['messages'][] = $message;
        }else if($roomInfo['status'] == $this->ROOM_STATUS['END']){
            $this->lineBotDAO->setRoom($roomId, $this->ROOM_STATUS['OPEN']);
            $list = $this->lineBotDAO->findRoomList($userLiveRoom['roomId']);
            foreach ($list as $row){
                $this->lineBotDAO->updateRoomList($row['roomId'], $row['userId'], '', $this->ROLE_STATUS['NORMAL'], 'CLEAR');
            }
            $message['text'] = $this->MESSAGES['RESET_SUCCESS'];
            $response['messages'][] = $message;
        }else{
            $message['text'] = $this->MESSAGES['RESET_FAILED'];
            $response['messages'][] = $message;
        }
    }

    public function setRoomRoleStatus($roomId, &$response){
        $message = [ 'type' => 'text', 'text' => '目前房間人員狀態'.PHP_EOL.'=================='.PHP_EOL ];
        $list = $this->lineBotDAO->findRoomList($roomId);
        foreach ($list as $key=>$user){
            $message['text'] .= sprintf("Player %d. 狀態: %s\n  %s ".PHP_EOL, 
                                    $key+1
                                    , $this->roleStatus[$user['status']]
                                    , $user['displayName']
                                );
        }
        $response['messages'][] = $message;
    }

    public function setRoomStatus($roomId, $status, &$response){
        $message = [ 'type' => 'text', 'text' => '' ];
        $list = $this->lineBotDAO->findRoomList($roomId);
        if($status == $this->ROOM_STATUS['CREATE']){
            $message['text'] = sprintf($this->MESSAGES['OPEN'], $status, count($list));
            $response['messages'][] = $message;
            $message['text'] = sprintf($this->MESSAGES['WAITE_STATUS'], $status, count($list))
                               .PHP_EOL .$this->MESSAGES['JOIN'];
            $response['messages'][] = $message;
            $message['text'] = sprintf($this->MESSAGES['JOIN_COMMAND'], $roomId);
            $response['messages'][] = $message;
        }else if($status == $this->ROOM_STATUS['OPEN']){
            $message['text'] = sprintf($this->MESSAGES['WAITE_STATUS'], $status, count($list))
                               .PHP_EOL .$this->MESSAGES['JOIN'];
            $response['messages'][] = $message;
            $message['text'] = sprintf($this->MESSAGES['JOIN_COMMAND'], $roomId);
            $response['messages'][] = $message;
        }else if($status == $this->ROOM_STATUS['JOIN']){
            $message['text'] = sprintf($this->MESSAGES['WAITE_STATUS'], $this->ROOM_STATUS['OPEN'], count($list));
            $response['messages'][] = $message;
        }else if($status == $this->ROOM_STATUS['START']){
            $message['text'] = sprintf($this->MESSAGES['START_STATUS'], $status, count($list));
            $response['messages'][] = $message;
        }
    }
}