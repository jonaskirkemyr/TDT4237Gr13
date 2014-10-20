<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\Security;

class LoginController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::check()) 
        {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        } 
        else 
        {
            $this->render('login.twig', [
                                        "csrf"=>(object)array(
                                                            "id"=>Security::tokenID(),
                                                            "value"=>Security::tokenValue()
                                                            )
                                        ]);
        }
    }

    function login()
    {
        $request = $this->app->request;
        $user = Security::xss($request->post('user'));
        $pass = Security::xss($request->post('pass'));



        if (Security::checkForm($request) && Auth::checkCredentials($user, $pass)) 
        {
            $_SESSION['user'] = $user;

            $isAdmin = Auth::user()->isAdmin();

            session_regenerate_id(true);

            $this->app->flash('info', "You are now successfully logged in as $user.");
            $this->app->redirect('/');
        } 

        else 
        {
            Security::unsetToken();
            $this->app->flashNow('error', 'Incorrect user/pass combination.');
            $this->render('login.twig', []);
        }
    }
}
