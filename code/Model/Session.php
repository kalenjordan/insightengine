<?php

class Model_Session
{
    protected $_key;
    protected $_mandrill;

    public function setKey($key)
    {
        $_SESSION['insightengine_mandrill_api_key'] = $key;
    }

    public function logOut()
    {
        $_SESSION['insightengine_mandrill_api_key'] = null;
    }

    public function isLoggedIn()
    {
        return ($this->getKey() != null);
    }

    public function getKey()
    {
        if (!isset($_SESSION['insightengine_mandrill_api_key'])) {
            return null;
        }

        return $_SESSION['insightengine_mandrill_api_key'];
    }

    public function getUsername()
    {
        return $this->getMandrillApi()->getUsername();
    }

    // Not sure if this is always the case
    public function getEmail()
    {
        return $this->getMandrillApi()->getUsername();
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