<?php
class LineBotDAO extends BaseDAO{

    public function addAccessLog($data){
        $sql = "INSERT INTO LineBot.access_log
                    (displayName
                    , replyToken
                    , `type`
                    , `timestamp`
                    , sourceType
                    , userId
                    , messageId
                    , messageType
                    , messageText
                    , createTime
                    , `log`
                    )
                VALUES
                    (:displayName
                    , :replyToken
                    , :type
                    , :timestamp
                    , :sourceType
                    , :userId
                    , :messageId
                    , :messageType
                    , :messageText
                    , NOW()
                    , :log ) ";
        $this->bindQuery($sql, array(
            ':displayName'  => (string)$data['displayName'],
            ':replyToken'   => (string)$data['replyToken'],
            ':type'         => (string)$data['type'],
            ':timestamp'    => (string)$data['timestamp'],
            ':sourceType'   => (string)$data['source']['type'],
            ':userId'       => (string)$data['source'][$data['source']['type'].'Id'],
            ':messageId'    => (string)$data['message']['id'],
            ':messageType'  => (string)$data['message']['type'],
            ':messageText'  => (string)$data['message']['text'],
            ':log'          => json_encode($data),
        ));
    }

    public function findUser($userId){
        return $this->getCommand(
                    "SELECT * FROM LineBot.user WHERE userId=:userId LIMIT 1",
                    [':userId' => (string)$userId]
                )
                ->queryRow();
    }

    public function findRoom($roomId){
        return $this->getCommand(
                    "SELECT * FROM LineBot.room WHERE roomId=:roomId LIMIT 1",
                    [':roomId' => (string)$roomId]
                )
                ->queryRow();
    }

    public function findRoomList($roomId){
        return $this->getCommand(
                    "SELECT * FROM LineBot.room_list WHERE roomId=:roomId ORDER BY `id`",
                    [':roomId' => (string)$roomId]
                )
                ->queryAll();
    }

    public function findRoomUserIsLive($userId){
        return $this->getCommand(
                    "SELECT RL.*, R.status as roomStatus
                     FROM LineBot.room_list RL
                     INNER JOIN LineBot.room R ON RL.roomId=R.roomId
                     WHERE RL.userId=:userId AND R.status!='END' AND RL.status != 'LEAVE'
                    ",
                    [':userId' => (string)$userId]
                )
                ->queryRow();
    }
    
    public function setUser($user){
        $sql = "INSERT INTO LineBot.user (userId, `mode`, `createTime`) VALUES (:userId, :mode, NOW())
                ON DUPLICATE KEY UPDATE `mode`=:mode, updateTime=NOW() ";
        $this->bindQuery($sql, [
            ':userId'   => (string)$user['userId'],
            ':mode'     => (string)$user['mode']
        ]);
    }
    
    public function setRoom($roomId, $status){
        $sql = "INSERT INTO LineBot.room (roomId, `status`, `createTime`) VALUES (:roomId, :status, NOW())
                ON DUPLICATE KEY UPDATE `status`=:status, updateTime=NOW() ";
        $this->bindQuery($sql, [
            ':roomId'   => (string)$roomId,
            ':status'   => (string)$status,
        ]);
    }
    
    public function setRoomList($roomId, $userId, $displayName, $status = '', $role = ''){
        $sql = "INSERT INTO LineBot.room_list (roomId, `userId`, `status`, `displayName`, `createTime`) VALUES (:roomId, :userId, :status, :displayName, NOW())
                ON DUPLICATE KEY UPDATE `status`=:status, role=:role, displayName=:displayName, updateTime=NOW() ";
        $this->bindQuery($sql, [
            ':roomId'       => (string)$roomId,
            ':userId'       => (string)$userId,
            ':status'       => (string)$status,
            ':role'         => (string)$role,
            ':displayName'  => (string)$displayName,
        ]);
    }
    
    public function updateRoomList($roomId, $userId, $role = '', $status = '', $event = '', $toUserId = ''){
        $bind = [
            ':userId'       => (string)$userId,
        ];
        $set = $where = '';
        if($role != ''){
            $set .= ',role=:role'; 
            $bind[':role'] = (string)$role;
        }
        if($event != ''){
            $set .= ',event=:event'; 
            $bind[':event'] = (string)$event;
        }
        if($toUserId != ''){
            $set .= ',toUserId=:toUserId'; 
            $bind[':toUserId'] = (string)$toUserId;
        }
        if($status != ''){
            $set .= ',status=:status'; 
            $bind[':status'] = (string)$status;
        }
        if($roomId != ''){
            $where .= ' AND roomId=:roomId '; 
            $bind[':roomId'] = (string)$roomId;
        }
        $sql = "UPDATE LineBot.room_list
                SET updateTime=NOW() {$set}
                WHERE 1=1 AND userId=:userId {$where} ";
        $this->bindQuery($sql, $bind);
    }
}