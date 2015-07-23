<?php

abstract class Model_SessionAbstract
{
    protected $_key;
    protected $_mandrill;
    protected $_user;

    abstract public function setKey($key);
    abstract public function getKey();

    public function logOut()
    {
        $_SESSION['insightengine_mandrill_api_key'] = null;
    }

    public function isLoggedIn()
    {
        return ($this->getKey() != null);
    }


    public function getUser()
    {
        if (isset($this->_user)) {
            return $this->_user;
        }

        $this->_user = new Model_User();
        $this->_user->loadByApiKey($this->getKey());

        return $this->_user;
    }

    public function getUsername()
    {
        return $this->getUser()->getUsername();
    }

    public function getUserId()
    {
        return $this->getUser()->getUserId();
    }

    // Not sure if this is always the case
    public function getEmail()
    {
        return $this->getUser()->getUsername();
    }

    public function getMandrillApi()
    {
        if (isset($this->_mandrill)) {
            return $this->_mandrill;
        }

        if (! $this->getKey()) {
            throw new Exception("No api key is set on the session yet");
        }

        $mandrill = new Model_Mandrill();
        $mandrill->setKey($this->getKey());

        $this->_mandrill = $mandrill;
        return $this->_mandrill;
    }
}