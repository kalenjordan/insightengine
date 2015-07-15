<?php

class Controller_Manage_ToggleTag extends Controller_Abstract
{
    public function get($tagId)
    {
        try {
            $this->_get($tagId);
        } catch (Exception $e) {
            $this->_jsonResponse(array(
                'success'       => false,
                'error_message' => "Uh-oh: " . $e->getMessage(),
            ));
        }
    }

    protected function _get($tagId)
    {
        $session = new Model_Session();

        $tagRecord = ORM::for_table('insightengine_tags')
            ->where_equal('user_id', $session->getUserId())
            ->where('tag_id', $tagId)
            ->find_one();

        if (! $tagRecord) {
            throw new Exception("Wasn't able to find tag by ID: " . $tagId);
        }

        $isActive = $tagRecord->get('is_active');
        $isActive = ! $isActive;
        $tagRecord->set('is_active', $isActive);
        $tagRecord->save();

        $this->_jsonResponse(array(
            'success'   => true,
            'is_active' => $isActive,
            'tag'       => $tagRecord->get('tag'),
        ));
    }
}