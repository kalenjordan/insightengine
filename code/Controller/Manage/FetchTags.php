<?php

class Controller_Manage_FetchTags extends Controller_Abstract
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
        $this->_requireLogin();

        $session = new Model_Session();

        $user = $session->getUser();
        $tagsFound = $user->fetchTagsFromMandrill();

        $this->_jsonResponse(array(
            'success'       => true,
            'tags_found'    => $tagsFound,
        ));
    }
}