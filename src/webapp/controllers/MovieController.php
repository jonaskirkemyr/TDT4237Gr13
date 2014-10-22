<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\User;
use tdt4237\webapp\models\Movie;
use tdt4237\webapp\models\MovieReview;
use tdt4237\webapp\Auth;

use tdt4237\webapp\Security;
use tdt4237\webapp\StringFunc;

class MovieController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $movies = Movie::all();

        usort($movies, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        $this->render('movies.twig', ['movies' => $movies]);
    }

    /**
     * Show movie by id.
     */
    function show($id)
    {
        $id=Security::xss($id);
        $this->render('showmovie.twig', [
            'movie' => Movie::find($id),
            'reviews' => MovieReview::findByMovieId($id)
        ]);
    }

    function addReview($id)
    {
        $id=Security::xss($id);

        $request=$this->app->request;
        if(isset($_SESSION["posts"])){
            $current_time = time();
            $old_login_attempts = $_SESSION["posts"];
            $new_login_attempts = array($old_login_attempts[1], $current_time);
            $_SESSION["posts"] = $new_login_attempts;
            if( (($current_time - $old_login_attempts[0]) < 30) && 
                (($current_time - $old_login_attempts[1]) < 30)){
                    $this->app->flash('info', 'Posting too many times! Please wait.');
                    $this->app->redirect('/movies/' . $id);
            }
        } else {
            $login_attempts = array(time(), 0);
            $_SESSION["posts"] = $login_attempts;
        }

        if(!Security::checkForm($this->app->request) && !$this->app->request->isPost())
        {
            printf("test");
            $this->app->redirect('/movies/' . $id);
            return;
        }

        $request = $this->app->request;
        $author = Security::xss($request->post('author'));
        $text = Security::xss($request->post('text'));

        //shortens text before adding to db, don't want db to handle this (?)
        
        $author=(isset($_SESSION["user"]))?User::findByUser($_SESSION["user"])->getUserName():StringFunc::shrtn($author);
        $text=StringFunc::shrtn($text,500);

        $review = MovieReview::makeEmpty();


        $review->setAuthor($author);
        $review->setText($text);
        $review->setMovieId($id);

        $review->save();

        $this->app->flash('info', 'The review was successfully saved.');
        $this->app->redirect('/movies/' . $id);
    }
}
