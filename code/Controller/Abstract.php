<?php

class Controller_Abstract
{
    protected function _requireLogin()
    {
        $local = new Model_LocalConfig();
        $baseRoute = $local->getBaseRoute();
        $session = new Model_Session();

        if (! $session->isLoggedIn()) {
            die("Uh-oh, looks like you're not logged in yet!  <a href='/$baseRoute#login'>Login</a>");
        }
    }

    protected function _getTwigParameters()
    {
        $local = new Model_LocalConfig();
        $session = new Model_Session();
        $user = $session->getUser();

        return array(
            'session'   => $session,
            'base_url'  => $local->getBaseUrl(),
            'user'      => $user,
        );
    }

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

    protected function _jsonResponse($response)
    {
        header('Content-type: application/json');
        echo json_encode($response);
        return $this;
    }
}