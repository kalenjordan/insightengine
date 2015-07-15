<?php

class Model_User
{
    protected $_userId;
    protected $_userData;

    public function loadByUserId($userId)
    {
        $this->_userId = $userId;
        $this->_userData = ORM::for_table('insightengine_users')
            ->where_equal('user_id', $userId)
            ->find_one();

        return $this;
    }

    public function loadByApiKey($apiKey)
    {
        $this->_userData = ORM::for_table('insightengine_users')
            ->where_equal('mandrill_api_key', $apiKey)
            ->find_one();

        if (! $this->_userData) {
            throw new Exception("Couldn't load user by api key: $apiKey");
        }

        return $this;
    }

    public function getUsername()
    {
        $userData = $this->getUserData();
        if (! isset($userData['username'])) {
            throw new Exception("Couldn't find username in user record");
        }

        return $userData['username'];
    }

    public function isActive()
    {
        $userData = $this->getUserData();
        if (! isset($userData['is_active'])) {
            throw new Exception("Couldn't find is_active in user record");
        }

        return $userData['is_active'];
    }

    public function getUserId()
    {
        $userData = $this->getUserData();
        if (! isset($userData['user_id'])) {
            throw new Exception("Couldn't find user_id in user record");
        }

        return $userData['user_id'];
    }

    public function getUserData()
    {
        if (! isset($this->_userData)) {
            throw new Exception("The user model hasn't been loaded yet");
        }

        return $this->_userData;
    }
}