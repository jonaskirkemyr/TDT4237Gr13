<?php

namespace tdt4237\webapp;

class Security
{
	function __construct(){}

    static function xss($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * get token id to be used in forms and saved in session
     *
     * if token id not set, one is generated and returned
     * @return [string] [the token id]
     */
    static function tokenID()//id to be used in form
    {
    	if(!isset($_SESSION["rtoken"]))
    		$_SESSION["rtoken"]=uniqid();
    	return $_SESSION["rtoken"];
    }


    /**
     * get token value to be used in forms and saved in session
     *
     * if token not set, one is generated and returned
     * @return [string] [the token value]
     */
    static function tokenValue()//value to be used in form
    {
    	if(!isset($_SESSION["rvalue"]))
    		$_SESSION["rvalue"]=base64_encode(openssl_random_pseudo_bytes(32));
    	return $_SESSION["rvalue"];
    }

    /**
     * check input value, give session token value
     * @param  [string] $value [token value to check with session value]
     * @return [boolean]        [whether values match]
     */
    static function checkToken($value)
    {
    	if($value==self::tokenValue())
    		return true;
    	return false;
    }

    /**
     * checks if the form has valid tokens
     * @param  [string] $method [form method, should be either get/post]
     * @return [boolean]         [whether or not valid token in post]
     */
    static function checkForm($param)
    {
    	if($param->params(self::tokenID()) && self::checkToken($param->params(self::tokenID())))
    		return true;
    	return false;
    }


    static function unsetToken()
    {
    	unset($_SESSION["rtoken"]);
    	unset($_SESSION["rvalue"]);
    }

}

?>