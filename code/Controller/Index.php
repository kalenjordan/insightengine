<?php

class Controller_Index extends Controller_Abstract
{
    public function get()
    {
        $local = new Model_LocalConfig();

        if ($local->useHttpsOnly() && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "")) {
            $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: $redirect");
        }

        echo $this->_getTwig()->render('index.html.twig', array(
            'local_config'  => 'test',
            'base_url'      => $local->getBaseUrl()
        ));

    }
}