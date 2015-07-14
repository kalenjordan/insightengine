<?php

class Controller_Manage_Tags extends Controller_Abstract
{
    public function get()
    {
        $local = new Model_LocalConfig();

        echo $this->_getTwig()->render('manage/tags.html.twig', array(
            'tags_menu_selected'    => true,
            'base_url'      => $local->getBaseUrl()
        ));

    }
}