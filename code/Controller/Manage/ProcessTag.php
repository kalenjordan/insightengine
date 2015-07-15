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

        $user = new Model_User();
        $user->loadByApiKey($session->getKey());
        if (! $user->isActive()) {
            throw new Exception("Not logged in yet");
        }

        $tagModel->loadByTagId($session->getUserId(), $tag);
        $tagModel->processTag();
        if (! $tagModel->getSubject()) {
            $tagModel->processSubjectLine();
        }

        $this->_jsonResponse(array(
            'success'                   => true,
            'tag'                       => $tag,
            'biggest_gap_last_30_days'  => $tagModel->getBiggestGap(),
            'last_sent'                 => $tagModel->getLastSent(),
            'last_sent_friendly'        => $tagModel->formatLastSent($tagModel->getLastSent()),
            'is_active'                 => $tagModel->defaultToActive(),
            'subject'                   => $tagModel->getSubject(),
            'summary'                   => $tagModel->getSummary($tagModel->getTagRecord()),
            'last_sent_status'          => $tagModel->lastSentStatus($tagModel->getTagRecord()),
        ));
    }
}