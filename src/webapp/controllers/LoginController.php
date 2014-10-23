<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\User;

use tdt4237\webapp\Auth;
use tdt4237\webapp\Hash;
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
            $this->render('login.twig');
        }
    }

    function login()
    {
        $request = $this->app->request;
        $user = Security::xss($request->post('user'));
        $pass = Security::xss($request->post('pass'));

        if(isset($_SESSION["login_attempts"])){
        	$current_time = time();
        	$old_login_attempts = $_SESSION["login_attempts"];
        	$new_login_attempts = array($old_login_attempts[1], $old_login_attempts[2], $current_time);
        	$_SESSION["login_attempts"] = $new_login_attempts;
        	if( (($current_time - $old_login_attempts[0]) < 30) && 
        		(($current_time - $old_login_attempts[1]) < 30) && 
        		(($current_time - $old_login_attempts[2]) < 30) ){
        			$this->app->flash('error', 'Too many login attempts! Please wait.');
           			$this->app->redirect('/login');
        	}
        } else {
        	$login_attempts = array(time(), 0, 0);
        	$_SESSION["login_attempts"] = $login_attempts;
        }

        if(Security::checkForm($request) && Auth::checkCredentials($user, $pass)) 
        {
            $_SESSION['user'] = $user;

            $isAdmin = Auth::user()->isAdmin();

            session_regenerate_id(true);//regenerate token and session of each login
            Security::unsetToken();

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


    function forgotpw()
    {
        if (Auth::check()) 
        {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        } 
        else 
        {
            $this->render('forgotPW.twig');
        }
    }

    function submit() 
    {   

        if(isset($_SESSION["forgot_attempts"])){
            $current_time = time();
            $old_forgot_attempts = $_SESSION["forgot_attempts"];
            $_SESSION["forgot_attempts"]=$current_time;
        
            if( (($current_time - $old_forgot_attempts) < 1)){
                    $this->app->flash('info', 'Too many attempts! Please wait.');
                    $this->app->redirect('/');
            }
        } else {
            $login_attempts = time();
            $_SESSION["forgot_attempts"] = $login_attempts;
        }


        $request=$this->app->request;
        $username=Security::xss($request->post("user"));
        $user=User::findByUser($username);

        if(Security::checkForm($request) && $user!=null && !empty($user->getEmail()))
        {
    
            $pw=Security::randomToken();

            $user->setHash(Hash::make($pw));
            $user->save();

            $str = 
<<<EOD
<html>
<b>Subject: Password Change</b><br>\n
<br>\n
Hello $username,<br>\n
your password was recently changed!<br>\n
Next time on login, please use the password: $pw<br>\n
<br>\n
You should change your password. This can be done under "Edit user".<br>\n
<br>\n
<b>Sincerely,<br>\n
GROUP13</b><br>\n
<br>\n
This is an autogenerated email.<br>\n
</html>
EOD;

    echo $str;

   // $this->app->flash('info', '::::::::::::::::::EMAIL::::::::::::::::::<br/><br/>'.$str);
    //$this->app->redirect('/'); 



        }
        else//error
        {
            $this->app->flash('info', 'An error occured');
            $this->app->redirect('/'); 
        }
    }

}
