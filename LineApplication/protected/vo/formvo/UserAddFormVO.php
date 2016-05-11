<?php
class UserAddFormVO{

    public $account;

    public $name;

    public $isActive;

    public $password;

    public $confirmPassword;

    public $errorCode;

    /**
     * 設定Form 資料
     * @param array $row
     * @return boolean
     *     if return value is true when success
     */
    public function setData($row){
        if(empty($row) || !is_array($row)){
            return false;
        }
        $this->account = $row['account'];
        $this->name = $row['name'];
        $this->password = $row['password'];
        $this->confirmPassword = $row['confirmPassword'];
        $this->isActive = $row['isActive'];
        return true;
    }
}