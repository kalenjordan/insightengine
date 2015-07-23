<?php

class Model_SessionForCron extends Model_SessionAbstract
{
    public function setKey($key)
    {
        $this->_key = $key;
    }

    public function getKey()
    {
        if (!isset($this->_key)) {
            throw new Exception("No key found on SessionForCron");
        }

        return $this->_key;
    }
}