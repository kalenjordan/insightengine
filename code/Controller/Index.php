<?php

class Controller_Index extends Controller_Abstract
{
    public function get()
    {
        echo $this->_getTwig()->render('index.html.twig', array(
            'local_config'  => 'test',
        ));

    }
}