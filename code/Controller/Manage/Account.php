<?php

class Controller_Manage_Account extends Controller_Abstract
{
    public function get()
    {
        echo $this->_getTwig()->render('manage/account.html.twig', array(
            'local_config'  => 'test',
            'account_menu_selected'    => true,
        ));

    }
}