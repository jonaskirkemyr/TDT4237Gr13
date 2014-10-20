<?php

namespace tdt4237\webapp\controllers;
use tdt4237\webapp\Auth;

use tdt4237\webapp\Security;

class Controller
{
    protected $app;

    function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
    }

    function render($template, $variables = [])
    {
        if (! Auth::guest()) {
            $variables['isLoggedIn'] = true;
            $variables['isAdmin'] = Auth::isAdmin();
            $variables['loggedInUsername'] = $_SESSION['user'];
            
        }

        $variables["csrf"]=(object)array(
                                            "id"=>Security::tokenID(),
                                            "value"=>Security::tokenValue()
                                            );

        print $this->app->render($template, $variables);
    }
}
