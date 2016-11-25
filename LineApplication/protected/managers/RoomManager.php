<?php
class RoomManager{
    const ROOM_EVENT_STOP = 'STOP';
    const ROOM_EVENT_START = 'START';
    const ROOM_EVENT_VOTE = 'VOTE';

    protected $MESSAGES = [
        'OPEN'                  => "大家好我是遊戲管理者,遊戲房間已經開啟,如果想加入遊戲請先要加入我為好友喔~",
        'JOIN_START'            => "如果確認想『加入遊戲』,請複製『以下指令代碼』私訊給我喔~",
        'JOIN_COMMAND'          => "/join %s",
        'JOIN_ROOM_SUCCESS'     => "加入遊戲成功",
        'JOIN_ROOM_SUCCESS_ALL' => "通告大家%s加入遊戲囉!",
        'JOIN_ROOM_NOT_EXIST'   => "您想加入的遊戲不存在唉,請確認是否複製錯誤了呢~系統是不會錯誤的喔!!",
        'JOIN_ARLEADY_EXIST'    => "您已經加入遊戲了!請問您有事嗎?不要測試我喔",
        'JOIN_EXIST'            => "您已經在其他遊戲中了喔!請不要腳踏兩條船喔!我可以偷偷告訴你輸入/leave可以離開遊戲喔!但是壞掉不負責喔~",
        'WAITE_STATUS'          => "哈囉~遊戲目前情況不能跟你說喔,但可以跟你說目前已加入遊戲人數為%d人",
        'WAITE_STATUS_OPEN'     => "各位房間內的玩家目前房間狀態為『開啟中』,已加入遊戲人數為%d人",
        'WAITE_STATUS_JOIN'     => "哈囉~您已加入遊戲,目前已加入遊戲人數為%d人",
        'WAITE_STATUS_START'    => "哈囉~目前遊戲已經開始無法再加入遊戲,只可以從群組聊天室中觀看遊戲情況,而目前已加入遊戲人數為%d人",
        'START_NOT_EXIST'       => "不好意思啦~還未開起遊戲!想開啟遊戲請大聲跟我說喔~",
        'START_LIMIT'           => "加入由遊戲人數最少要四人不然遊戲會很無聊滴",
        'START'                 => "遊戲準備開始囉~",
        'START_TIME'            => "遊戲%d後開始",
        'START_ARLEADY'         => "各位羔羊們準備受死吧~~哇哈哈哈",
        'NIGHT_COMING'          => "夜晚來臨了...村莊內的人都睡了除了某些怪咖...",
        'LEAVE_NOT_EXIST'       => "您目前無在任何遊戲內",
        'LEAVE_SUCCESS'         => "已經害怕的逃離村莊了",
        'ROLE_CHECKED'          => "您角色為 - %s",
        'DO_NOT_ACTION'         => "你無法執行您角色為 - %s",
        'KILL_NOT_EXIST'        => "你想要殺的對象不存在唉",
        'KILL_ARLEADY_EXIT'     => "對象已離開遊戲",
        'KILL_ARLEADY_DEAD'     => "對象已經被殺死了",
        'KILL_CHECKED'          => "殺害對象為 - %s",
        'KILL_SUCCESS'          => "%s被殺死了...",
        'KILL_AGAIN_SUCCESS'    => "%s被另一個人發現後鞭屍...",
        'KILL_AGAIN_FAILED'     => "被%s被鞭屍後的屍體嚇到尿褲子後死亡...",
        'HELP_SUCCESS'          => "%s被剛上廁所的護士發現救活了...",
        'NIGHT_PERSON_ACTION'   => "已有 %d 名夜貓子在夜間行動",
        'MONING_COMING'         => "當太陽升起來了...有村民發現了並通知大家",
        'DO_NOT_NEXT'           => "黑夜時間還沒到",
        'YOU_ARE_DEAD'          => "您已經死了請等遊戲結束吧",
        'RESET_SUCCESS'         => "遊戲房間已重新開始",
        'RESET_FAILED'          => "遊戲未結束無法重新開始",
        'PEEP_SUCCESS'          => "%s身分為%s ",
        'CHECKED_PERSON'        => "你選擇的對象為 - %s",
        'KILLER_VICTORY'        => "村民被殺手殺光了拉..村莊迎來和平的一天?",
        'KILLER_LOST'           => "殺手終於都死了...村莊迎來和平的一天",
        'VOTE_TO'               => "%s認為%s為兇手",
        'VOTE_TOTAL'            => "%s獲得了%d票",
        'VOTE_MESSAGE'          => "=====投票結果=====",
        'VOTE_DEAD'             => "===============\n%s被大家當作兇手綁起來燒死了..",
        'VOTE_ACTION'           => "大家可以開始投票認為誰是兇手...",
        'DELETE_NOT_EXISTS'     => "遊戲房間不存在刪啥小",
        'DELETE_SUCCESS'        => "遊戲房間幫你刪掉囉",
        'DELETE_FAILED'         => "人都還沒被殺光想跑?",
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
        ['role' => 'KILLER', 'roleName' => '殺人狂'],
        ['role' => 'PEEPER', 'roleName' => '偷窺狂'],
        ['role' => 'HELPER', 'roleName' => '護士'],
        ['role' => 'POLICE', 'roleName' => '波麗士'],
        ['role' => 'VILLAGER', 'roleName' => '村民'],
    ];
    private $roleName = [
        'KILLER'    => "[殺人狂]\n可以殺死任何對象\n/kill",
        'HELPER'    => "[護士]\n可以再每回合隨意救活被殺手殺死對象\n/help",
        'PEEPER'    => "[偷窺狂]\n可偷看一人職業\n/peep",
        'POLICE'    => "[波麗士]\n雖然是警察可是跟村民一樣...只能起鬨投票將認為是兇手的殺死...所以乖乖睡覺吧",
        'VILLAGER'  => "[村民]\n只能起鬨投票將認為是兇手的殺死...所以乖乖睡覺吧",
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
            $this->setRoomStatus($roomId, 'JOIN', $response);
            // Set role message on room
            $this->setRoomRoleStatus($roomId, $response);
            // Push message for room
            $message['text'] = sprintf($this->MESSAGES['JOIN_ROOM_SUCCESS_ALL'], $response['displayName']);
            $this->parent->actionPushMessages($roomId, [$message]);
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
            $this->setRoomStatus($roomId, 'JOIN', $response);
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
                $checkProtectedNumber = rand(2, 3);
                $killerCount = floor(count($randomList)/8);
                $helpCount = $peepCount = $killerCount;
                foreach ($randomList as $key=>$user){
                    if($checkProtectedNumber > 0){
                        $setList[$user['id']]['role'] = $this->role[$key]['role'];
                        $setList[$user['id']]['roleName'] = $this->roleName[$this->role[$key]['role']];
                        $checkProtectedNumber--;
                    }else{
                        $r_k = (rand(0, 999)*$user['id'])%4;
                        if($killerCount>0 && $r_k==0){
                            $killerCount--;
                        }else if($peepCount>0 && $r_k==1){
                            $peepCount--;
                        }else if($helpCount>0 && $r_k==2){
                            $helpCount--;
                        }else if($r_k<3){
                            $r_k = 4;
                        }
                        $setList[$user['id']]['role'] = $this->role[$r_k]['role'];
                        $setList[$user['id']]['roleName'] = $this->roleName[$this->role[$r_k]['role']];
                    }
                    $this->lineBotDAO->updateRoomList($roomId, $user['userId'], $setList[$user['id']]['role'], $this->ROLE_STATUS['NORMAL'], self::ROOM_EVENT_START, 'CLEAR');
                }

                $message['text'] = $this->MESSAGES['START'];
                $this->parent->actionPushMessages($roomId, [$message]);
                for ($i = 3; $i > 0; $i--){
                    sleep(1);
                    $message['text'] = sprintf($this->MESSAGES['START_TIME'], $i);
                    $this->parent->actionPushMessages($roomId, [$message]);
                }

                // Set role message on room
                $message['text'] = $this->MESSAGES['START_ARLEADY'];
                $response['messages'][] = $message;
                //$this->setRoomRoleStatus($roomId, $response);
                $message['text'] = $this->MESSAGES['NIGHT_COMING'];
                $response['messages'][] = $message;
                // Push message for everyone
                foreach ($setList as $user){
                    $message['text'] = sprintf($this->MESSAGES['ROLE_CHECKED'], $this->roleName[$user['role']]);
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
            // Push message for room
            $message['text'] = $userLiveRoom['displayName'].$this->MESSAGES['LEAVE_SUCCESS'];
            $response['messages'][] = $message;
            $this->parent->actionPushMessages($userLiveRoom['roomId'], $response['messages']);
        }
    }

    /**
     * close the room by room
     * @param unknown $roomId
     * @param unknown $message
     * @param unknown $response
     */
    public function close($roomId, $message, &$response, $option = ''){
        $message = [ 'type' => 'text', 'text' => '' ];
        $roomInfo = $this->lineBotDAO->findRoom($roomId);
        if(empty($roomInfo)){
            $message['text'] = $this->MESSAGES['DELETE_NOT_EXISTS'];
            $response['messages'][] = $message;
        }else if($roomInfo['status'] != $this->ROOM_STATUS['START']){
            $list = $this->lineBotDAO->findRoomList($roomInfo['roomId']);
            foreach ($list as $row){
                // set leave for room
                $this->lineBotDAO->deleteRoomList($roomInfo['roomId'], $row['userId']);
            }
            $this->lineBotDAO->deleteRoom($roomInfo['roomId']);
            // Push message for room
            $message['text'] = $this->MESSAGES['DELETE_SUCCESS'];
            $response['messages'][] = $message;
        }else if($roomInfo['status'] == $this->ROOM_STATUS['START']){
            $message['text'] = $this->MESSAGES['DELETE_FAILED'];
            $response['messages'][] = $message;
        }
        if($option == 'anger'){
            $message['type'] = 'sticker';
            $message['packageId'] = '1';
            $message['stickerId'] = '6';
            $this->parent->actionPushMessages($roomId, [$message]);
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
            $message['text'] = sprintf($this->MESSAGES['DO_NOT_ACTION'], $this->roleName[$userLiveRoom['role']]);
            $response['messages'][] = $message;
        }else if($action == $this->ROLES['HELPER'] && $userLiveRoom['role'] != $this->ROLES['HELPER']){
            $message['text'] = sprintf($this->MESSAGES['DO_NOT_ACTION'], $this->roleName[$userLiveRoom['role']]);
            $response['messages'][] = $message;
        }else if($action == $this->ROLES['PEEPER'] && $userLiveRoom['role'] != $this->ROLES['PEEPER']){
            $message['text'] = sprintf($this->MESSAGES['DO_NOT_ACTION'], $this->roleName[$userLiveRoom['role']]);
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
                    $peopleNow = count($setList);
                    foreach ($setList as $row){
                        if($actionRoomStatus == self::ROOM_EVENT_VOTE){
                            $setList[$row['toUserId']]['voteCount']++;
                            $this->lineBotDAO->updateRoomList($row['roomId'], $row['userId'], '', '', self::ROOM_EVENT_STOP);
                        }else if($row['role'] == $this->ROLES['KILLER']){
                            if($setList[$row['toUserId']]['power'] != $this->ROLES['HELPER'] || $peopleNow == 2){
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
                        }else if($row['role'] == $this->ROLES['HELPER'] && $row['toUserId'] !='' && $peopleNow > 2){
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

                        // set return message
                        $message['text'] = sprintf($this->MESSAGES['CHECKED_PERSON'], $target['displayName']);
                        $response['messages'][] = $message;
                    }
                }
                $message['text'] = implode(PHP_EOL, $voteMessage);
                if(!empty($message['text'])) $pushMessages[] = $message;
                $this->parent->actionPushMessages($userLiveRoom['roomId'], $pushMessages);
                foreach ($setList as $row){
                    $this->parent->actionPushMessages($row['userId'], $pushMessages);
                }

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
            $this->lineBotDAO->updateRoomListClear($userLiveRoom['roomId']);
            // Change status for this room.
            $this->lineBotDAO->setRoom($userLiveRoom['roomId'], $this->ROOM_STATUS['START']);
            $message['text'] = $this->MESSAGES['NIGHT_COMING'];
            $pushMessages['messages'] = [$message];
            $this->setRoomRoleStatus($userLiveRoom['roomId'], $pushMessages);
            $list = $this->lineBotDAO->findRoomList($userLiveRoom['roomId']);
            foreach ($list as $row){
                $this->parent->actionPushMessages($row['userId'], $pushMessages['messages']);
            }
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
            $this->lineBotDAO->updateRoomListClear($roomId);
            $message['text'] = $this->MESSAGES['RESET_SUCCESS'];
            $response['messages'][] = $message;
        }else{
            $message['text'] = $this->MESSAGES['RESET_FAILED'];
            $response['messages'][] = $message;
        }
    }

    public function setRoomRoleStatus($roomId, &$response){
        $message = [ 'type' => 'text', 'text' => '而目前房間人員狀態'.PHP_EOL.'=================='.PHP_EOL ];
        $list = $this->lineBotDAO->findRoomList($roomId);
        foreach ($list as $key=>$user){
            $message['text'] .= sprintf("Player %d. 狀態: 『%s』\n===== %s ".PHP_EOL, 
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
            $message['text'] = $this->MESSAGES['OPEN'];
            $response['messages'][] = $message;
            $message['text'] = $this->MESSAGES['JOIN_START'];
            $response['messages'][] = $message;
            $message['text'] = sprintf($this->MESSAGES['JOIN_COMMAND'], $roomId);
            $response['messages'][] = $message;
        }else if($status == $this->ROOM_STATUS['OPEN']){
            $message['text'] = sprintf($this->MESSAGES['WAITE_STATUS_OPEN'], count($list))
                               .PHP_EOL .$this->MESSAGES['JOIN_START'];
            $response['messages'][] = $message;
            $message['text'] = sprintf($this->MESSAGES['JOIN_COMMAND'], $roomId);
            $response['messages'][] = $message;
        }else{
            if(isset($this->MESSAGES['WAITE_STATUS_'.$status])){
                $message['text'] = sprintf($this->MESSAGES['WAITE_STATUS_'.$status], count($list));
            }else{
                $message['text'] = sprintf($this->MESSAGES['WAITE_STATUS'], count($list));
            }
            $response['messages'][] = $message;
        }
    }
}