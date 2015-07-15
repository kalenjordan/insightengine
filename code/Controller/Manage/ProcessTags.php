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

        $tags = ORM::for_table('insightengine_tags')
            ->where_equal('user_id', $session->getUserId())
            ->order_by_asc('updated_at')
            ->find_many();


        foreach ($tags as $tagData) {
            $tagModel = new Model_Tag();
            $tagModel->loadByTag($session->getUserId(), $tagData['tag']);

            echo "<br>Processing " . $tagData['tag'] . "\r\n";
            $tagModel->processTag();
        }

        $tags = ORM::for_table('insightengine_tags')
            ->where_equal('user_id', $session->getUserId())
            ->where_raw("tag_subject IS NULL OR tag_subject == ''")
            ->order_by_desc('send_count_30_days')
            ->find_many();

        foreach ($tags as $tagData) {
            $tagModel = new Model_Tag();
            $tagModel->loadByTag($session->getUserId(), $tagData['tag']);
            echo "<br>Processing subject line " . $tagData['tag'] . "\r\n";
            $tagModel->processSubjectLine();
        }

        $this->_jsonResponse(array(
            'success'           => true,
            'tags_processed'    => count($tags),
        ));
    }
}