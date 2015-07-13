<?php

class Controller_Manage_Tags extends Controller_Abstract
{
    public function get()
    {
        echo $this->_getTwig()->render('manage/tags.html.twig', array(
            'local_config'          => 'test',
            'tags_menu_selected'    => true,
        ));

    }
}