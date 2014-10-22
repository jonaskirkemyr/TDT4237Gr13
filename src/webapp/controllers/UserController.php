<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\User;
use tdt4237\webapp\Hash;
use tdt4237\webapp\Auth;

use tdt4237\webapp\Security;


class UserController extends Controller
{
    const MIN_PW_LENGTH=6;

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::guest()) 
        {
            $this->render('newUserForm.twig');
        } 
        else 
        {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function create()
    {
        $request = $this->app->request;
        $username = Security::xss($request->post('user'));
        $pass = Security::xss($request->post('pass'));


        $hashed = Hash::make($pass);

        $user = User::makeEmpty();
        $user->setUsername($username);
        $user->setHash($hashed);

        $validationErrors = User::validate($user);

        if (sizeof($validationErrors) > 0 || !Security::checkForm($request) || strlen($pass)<self::MIN_PW_LENGTH) 
        {
            if(sizeof($validationErrors) > 0)
                $errors = join("<br>\n", $validationErrors);
            else if(!Security::checkForm($request))
                $errors="";
            else if(strlen($pass)<self::MIN_PW_LENGTH)
                $errors="Password too short. Needs to be at least ".self::MIN_PW_LENGTH." characters<br/>";

            
            $this->app->flashNow('error', $errors);
            $this->render('newUserForm.twig', [
                                                'username' => $username
                                                ]);
        } 

        else 
        {
            $user->save();
            $this->app->flash('info', 'Thanks for creating a user. Now log in.');
            $this->app->redirect('/login');
        }
        return;
    }

    function all()
    {
        $users = User::all();
        $this->render('users.twig', ['users' => $users]);
    }

    function logout()
    {
        $redirect="/";
        if(Security::checkForm($this->app->request) && $this->app->request->isPost())
        {
            $this->app->flash('info', "Successfully logged out.");
            Auth::logout();
        }
        $this->app->redirect($redirect);
    }

    function show($username)//show
    {
        $username=Security::xss($username);
        $user = User::findByUser($username);

        if(!$user)
              $this->app->redirect('/users');

        $this->render('showuser.twig', [
            'user' => $user,
            'username' => $username
        ]);
    }

    function edit()//sql
    {
        if (Auth::guest()) 
        {
            $this->app->flash('info', 'You must be logged in to edit your profile.');
            $this->app->redirect('/login');
            return;
        }
        $user = Auth::user();

        if (!$user) 
            throw new \Exception("Unable to fetch logged in user's object from db.");

        if (Security::checkForm($this->app->request) && $this->app->request->isPost()) 
        {
            $request = $this->app->request;

            $email=Security::xss($request->post('email'));
            $bio=Security::xss($request->post('bio'));
            $age=Security::xss($request->post('age'));
            
            // Code for image upload.
            $uploadImage = 0;
            $imageFail = 0;
            // See if they want to upload an image.
            if(!empty($_FILES['image']['name'])){
                // Ensure that the image is acceptable.
                if($_FILES['image']['error'] !== UPLOAD_ERR_OK){
                    $imageFail = 1;
                } else {
                    $fileInfo = getimagesize($_FILES['image']['tmp_name']);
                    if($fileInfo === FALSE){
                        $imageFail = 1;
                    } else {
                        if(($fileInfo[2] !== IMAGETYPE_JPEG) && ($fileInfo[2] !== IMAGETYPE_PNG)){
                            $imageFail = 1;
                        } else {
                            // If acceptable, copy it over with our own name to avoid problems.
                            $target_dir = "web/images/profiles/";
                            $file = explode(".", $_FILES['image']['name']);
                            $extension  = end($file);
                            $target_dir = $target_dir . $user->getUserName() . "." . $extension;
                            $user->setImage(Security::xss($user->getUserName() . "." . $extension));  
                            $uploadImage = 1; 
                        }
                    }
                }   
            }

            $user->setEmail($email);
            $user->setBio($bio);
            $user->setAge($age);

            printf($uploadImage);
            if (! User::validateAge($user)) {
                $this->app->flashNow('error', 'Age must be between 0 and 150.');
            } 
            elseif($uploadImage && !move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir)){
                $this->app->flashNow('error', 'Error in image upload.');
            } elseif($imageFail){
                $this->app->flashNow('error', 'Error in image upload. Image must be small and in PNG or JPEG format.');
            }
            else 
            {
                $user->save();
                $this->app->flashNow('info', 'Your profile was successfully saved.');
            }
        }

        $this->render('edituser.twig', [
                                        'user' => $user
                                    
                                        ]);
    }
}
