<?php

class Controller_Abstract
{
    protected function _getTwig()
    {
        $loader = new Twig_Loader_Filesystem(dirname(dirname(dirname(__FILE__))) . '/template');
        $debug = true;
        $twig = new Twig_Environment($loader, array(
            'debug' => $debug,
        ));
        if ($debug) {
            $twig->addExtension(new Twig_Extension_Debug());
        }
        return $twig;
    }
}