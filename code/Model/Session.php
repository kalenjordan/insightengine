<?php

class Model_Session extends Model_SessionAbstract
{
    public function setKey($key)
    {
        $_SESSION['insightengine_mandrill_api_key'] = $key;
    }

    public function getKey()
    {
        if (!isset($_SESSION['insightengine_mandrill_api_key'])) {
            return null;
        }

        return $_SESSION['insightengine_mandrill_api_key'];
    }
}