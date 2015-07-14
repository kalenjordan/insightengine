<?php

class Controller_Manage_CheckMandrillKey extends Controller_Abstract
{
    public function get()
    {
        try {
            $this->_get();
        } catch (Exception $e) {
            $this->_jsonResponse(array(
                'success'       => false,
                'error_message' => "Uh-oh: " . $e->getMessage(),
            ));
        }
    }

    protected function _get()
    {
        $apiKey = isset($_GET['mandrill_api_key']) ? $_GET['mandrill_api_key'] : null;
        $mandrill = new Model_Mandrill();
        $mandrill->setKey($apiKey);
        $username = $mandrill->getUsername();

        $session = new Model_Session();
        $session->setKey($apiKey);

        $this->_jsonResponse(array(
            'success'       => true,
            'username'      => $username,
        ));
    }
}