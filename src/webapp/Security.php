<?php

namespace tdt4237\webapp;

class Security
{
	function __construct(){}

    static function xss($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

?>