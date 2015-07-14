<?php

class Controller_Manage_LogOut extends Controller_Abstract
{
    public function get()
    {
        $session = new Model_Session();
        $session->logout();

        $local = new Model_LocalConfig();
        $baseRoute = $local->getBaseRoute();
        header("location: /$baseRoute");
    }
}