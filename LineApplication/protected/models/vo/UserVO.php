<?php
class UserVO{
    
    /**
     * user id
     * @var integer
     */
    public $userId;
    
    /**
     * 帳號
     * @var string
     */
    public $account;

    /**
     * 名稱
     * @var string
     */
    public $name;
    
    /**
     * 使用者sha1 密碼
     * @var string
     */
    public $password;
    
    /**
     * 密碼加密的key
     * @var string
     */
    public $privateKey;
    
    /**
     * 使用者active
     * @var datetime
     */
    public $isActive;
    
    /**
     * 建立時間
     * @var datetime
     */
    public $createTime;
    
    /**
     * 更新時間
     * @var datetime
     */
    public $updateTime;
    
    /**
     * 設定DB 資料
     * @param array $row
     * @return boolean
     *     if return value is true when success
     */
    public function setData($row){
        if(empty($row) || !is_array($row)){
            return false;
        }
        $this->userId = $row['userId'];
        $this->name = $row['name'];
        $this->account = $row['account'];
        $this->password = $row['password'];
        $this->privateKey = $row['privateKey'];
        $this->isActive = $row['isActive'];
        $this->createTime = $row['createTime'];
        $this->updateTime = $row['updateTime'];
        return true;
    }
}