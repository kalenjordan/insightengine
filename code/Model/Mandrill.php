<?php

class Model_Mandrill
{
    protected $_key;
    protected $_mandrillApi;
    protected $_userData;

    public function setKey($key)
    {
        $this->_key = $key;
    }

    protected function _getMandrillApi()
    {
        if (isset($this->_mandrillApi)) {
            return $this->_mandrillApi;
        }

        $this->_mandrillApi = new Mandrill($this->_key);
        return $this->_mandrillApi;
    }

    public function getUserData()
    {
        if (isset($this->_userData)) {
            return $this->_userData;
        }

        $this->_userData = $this->_getMandrillApi()->users->info();
        return $this->_userData;
    }

    public function getUsername()
    {
        $userData = $this->getUserData();
        if (! isset($userData['username'])) {
            throw new Exception("Couldn't find username in Mandrill user data");
        }

        return $userData['username'];
    }
}