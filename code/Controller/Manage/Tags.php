<?php

class Controller_Manage_Tags extends Controller_Abstract
{
    public function get()
    {
        $this->_requireLogin();
        $session = new Model_Session();

        $tagModel = new Model_Tag();
        $tagModel->setUserId($session->getUserId());

        $recentDate = new \Carbon\Carbon();
        $recentDate->subDays(30);

        $tags = ORM::for_table('insightengine_tags')
            ->where_equal('user_id', $session->getUserId())
            ->order_by_desc('send_count_30_days')
            ->find_many();

        $parameters = array_merge(parent::_getTwigParameters(), array(
            'tags_menu_selected'    => true,
            'tags'                  => $tags,
            'tag_model'             => $tagModel,
        ));

        echo $this->_getTwig()->render('manage/tags.html.twig', $parameters);
    }
}