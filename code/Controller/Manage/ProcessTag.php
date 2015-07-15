<?php

class Controller_Manage_ProcessTag extends Controller_Abstract
{
    public function get($tag)
    {
        try {
            $this->_get($tag);
        } catch (Exception $e) {
            $this->_jsonResponse(array(
                'success'       => false,
                'error_message' => "Uh-oh: " . $e->getMessage(),
            ));
        }
    }

    protected function _get($tag)
    {
        $tag = urldecode($tag);
        $this->_requireLogin();

        $session = new Model_Session();
        $tagModel = new Model_Tag();

        $tagModel->loadByTag($session->getUserId(), $tag);
        $tagModel->processTag();
        $tagModel->processSubjectLine();

        $this->_jsonResponse(array(
            'success'                   => true,
            'tag'                       => $tag,
            'biggest_gap_last_30_days'  => $tagModel->getBiggestGap(),
            'last_sent'                 => $tagModel->getLastSent(),
            'last_sent_friendly'        => $tagModel->formatLastSent($tagModel->getLastSent()),
            'is_active'                 => $tagModel->defaultToActive(),
        ));
    }
}