<?php

class Controller_Manage_Tags extends Controller_Abstract
{
    public function get()
    {
        $this->_requireLogin();

        $session = new Model_Session();
        $mandrill = $session->getMandrillApi();
        $tags = $mandrill->getTags();

        $parameters = array_merge(parent::_getTwigParameters(), array(
            'tags_menu_selected'    => true,
            'tags'                  => $tags,
        ));

        echo $this->_getTwig()->render('manage/tags.html.twig', $parameters);
    }
}