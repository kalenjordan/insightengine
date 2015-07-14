<?php

class Controller_Manage_Account extends Controller_Abstract
{
    public function get()
    {
        $local = new Model_LocalConfig();

        echo $this->_getTwig()->render('manage/account.html.twig', array(
            'account_menu_selected'    => true,
            'base_url'      => $local->getBaseUrl()
        ));

    }
}