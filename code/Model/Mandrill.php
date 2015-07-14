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

    public function getTags()
    {
        $tags = $this->_getMandrillApi()->tags->getList();
        return $tags;
    }

    public function fetchTagTimeSeries($tag)
    {
        $timeSeries = $this->_getMandrillApi()->tags->timeSeries($tag);
        return $timeSeries;
    }

    public function fetchLastMessage($tag)
    {
        $messages = $this->_getMandrillApi()->messages->search('*', null, null, array($tag), null, null, 1);
        if (! isset($messages[0])) {
            return null;
        }

        return $messages[0];
    }

    public function fetchTagInfo($tag)
    {
        $info = $this->_getMandrillApi()->tags->info($tag);
        return $info;
    }

    public function getSentInLast30Days($tag)
    {
        $info = $this->_getMandrillApi()->tags->info($tag);
        $sent30Days = $info['stats']['last_30_days']['sent'];

        return $sent30Days;
    }
}