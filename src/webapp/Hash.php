<?php

namespace tdt4237\webapp;
//use mcrypt;

class Hash
{
    function __construct()
    {
    }

    /**
     * Hashes a password
     * @param  [string] $plaintext [password to hash, max 72 char]
     * @return [string]            [string 60characters that will be hashed]
     */
    static function make($plaintext) 
    {
        $options=[
                "cost"=>11,
                "salt"=>self::saltFunc(32)
                ];//look at openssl_digest

        return password_hash($plaintext,PASSWORD_BCRYPT,$options);
    }


    /**
     * Verify that given hash matches given password
     * @param  [string] $plaintext [password to check]
     * @param  [string] $hash      [hash retrieved from db (created by func make)]
     * @return [boolean]            [true if match, false otherwise]
     */
    static function check($plaintext, $hash)
    { 
        return password_verify($plaintext,$hash);
    }


    /** 
     * Creates random salt
     * @param  integer $size [size of the IV]
     * @return [boolean/vector]        [false on failure, initialization vector otherwise]
     */
    static function saltFunc($size=22)
    {
        return openssl_random_pseudo_bytes($size);
       // return mcrypt_create_iv(22,MCRYPT_DEV_URANDOM);
       //include mcrypt! - for now, no salt
    }
}
  