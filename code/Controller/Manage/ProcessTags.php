<?php

class Controller_Manage_ProcessTags extends Controller_Abstract
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

        $mandrill = new Model_Mandrill();
        $mandrill->setKey($session->getKey());
        $userId = $session->getUserId();

        $tags = $mandrill->getTags();
        foreach ($tags as $tagData) {
            $tagRecord = ORM::for_table('insightengine_tags')
                ->where_equal('user_id', $userId)
                ->where_equal('tag', $tagData['tag'])
                ->find_one();
            if (! $tagRecord) {
                $tagRecord = ORM::for_table('insightengine_tags')->create(array(
                    'is_active' => 1,
                    'user_id'   => $userId,
                    'tag'       => $tagData['tag'],
                ));
            }

            $tagRecord->set('send_count', $tagData['sent']);
            $tagRecord->set_expr('updated_at', 'NOW()');
            $tagRecord->save();
        }

        $this->_jsonResponse(array(
            'success'           => true,
            'tags_processed'    => count($tags),
        ));
    }
}