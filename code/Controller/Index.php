<?php

class Controller_Index extends Controller_Abstract
{
    public function get()
    {
        $local = new Model_LocalConfig();

        echo $this->_getTwig()->render('index.html.twig', array(
            'local_config'  => 'test',
            'base_url'      => $local->getBaseUrl()
        ));

    }
}