<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Security;

class IndexController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $request = $this->app->request;
      //  $msg = Security::xss($request->get('msg'));

        $variables = []; 

       // print_r($msg);

       /* if ($msg) {
            $variables['flash']['info'] = $msg;
        }*/


        $this->render('index.twig', $variables);
    }
}
