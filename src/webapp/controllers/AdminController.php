<?php
namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\models\User;

use tdt4237\webapp\Security;

class AdminController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    private function checkLogin()
    {
        if(Auth::check() && Auth::isAdmin())
            return true;
        return false;
    }

    /**
     * redirect user if not logged in, or not authorized
     * @return [boolean] [returns false if redircted]
     */
    private function redirectUser()
    {
        if(!$this->checkLogin())
        {
            $this->app->flash('info', "Not authorized");
            $this->app->redirect('/login');
            return false;
        }
    }



    function index()
    {
        if (Auth::guest()) 
        {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! Auth::isAdmin()) 
        {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }

        $variables = [
                        'users' => User::all()
                    ];
        $this->render('admin.twig', $variables);
    }

    function delete()
    {
        $request = $this->app->request;
        $this->redirectUser();

        if($request->post("delUser")===null || empty($request->post("delUser")) || !Security::checkForm($request)) $this->app->redirect('/login');

        $username=Security::xss($request->post("delUser"));

        if($username!=User::findByUser($_SESSION["user"])->getUserName() && User::deleteByUsername($username) == 1) 
            $this->app->flash('info', "Sucessfully deleted '$username'");
        else
            $this->app->flash('info', "An error ocurred. Unable to delete user.");
        
    
        $this->app->redirect('/admin');
    }   
}
