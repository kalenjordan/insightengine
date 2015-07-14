<?php

class Controller_Manage_Account extends Controller_Abstract
{
    public function get()
    {
        $this->_requireLogin();

        $parameters = array_merge(parent::_getTwigParameters(), array(
            'account_menu_selected'    => true,
        ));

        echo $this->_getTwig()->render('manage/account.html.twig', $parameters);
    }
}