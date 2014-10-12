<?php

namespace tdt4237\webapp\models;

use tdt4237\webapp\Hash;

class User
{
    const INSERT_QUERY = "INSERT INTO users(user, pass, email, age, bio, isadmin) VALUES(':user', ':pass', ':email' , ':age' , ':bio', ':isadmin')";
    const UPDATE_QUERY = "UPDATE users SET email=':email', age=':age', bio=':bio', isadmin=':isadmin' WHERE id=':id'";
    const FIND_BY_NAME = "SELECT * FROM users WHERE user=':user'";
    const DEL_USER     = "DELETE FROM users WHERE user=':user'";

    const MIN_USER_LENGTH = 3;

    protected $id = null;
    protected $user;
    protected $pass;
    protected $email;
    protected $bio = 'Bio is empty.';
    protected $age;
    protected $isAdmin = 0;

    static $app;

    function __construct()
    {
    }

    static function make($id, $username, $hash, $email, $bio, $age, $isAdmin)
    {
        $user = new User();
        $user->id = $id;
        $user->user = $username;
        $user->pass = $hash;
        $user->email = $email;
        $user->bio = $bio;
        $user->age = $age;
        $user->isAdmin = $isAdmin;

        return $user;
    }

    static function makeEmpty()
    {
        return new User();
    }

    /**
     * Insert or update a user object to db.
     */
    function save()
    {
        if ($this->id === null) {
            $query = sprintf(self::INSERT_QUERY,
                $this->user,
                $this->pass,
                $this->email,
                $this->age,
                $this->bio,
                $this->isAdmin
            );
        } else {
            $query = sprintf(self::UPDATE_QUERY,
                $this->email,
                $this->age,
                $this->bio,
                $this->isAdmin,
                $this->id
            );
        }

        return self::$app->db->exec($query);
    }

    function getId()
    {
        return $this->id;
    }

    function getUserName()
    {
        return $this->user;
    }

    function getPasswordHash()
    {
        return $this->pass;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getBio()
    {
        return $this->bio;
    }

    function getAge()
    {
        return $this->age;
    }

    function isAdmin()
    {
        return $this->isAdmin === "1";
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setUsername($username)
    {
        $this->user = $username;
    }

    function setHash($hash)
    {
        $this->pass = $hash;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setBio($bio)
    {
        $this->bio = $bio;
    }

    function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * The caller of this function can check the length of the returned 
     * array. If array length is 0, then all checks passed.
     *
     * @param User $user
     * @return array An array of strings of validation errors
     */
    static function validate(User $user)
    {
        $validationErrors = [];

        if (strlen($user->user) < self::MIN_USER_LENGTH) {
            array_push($validationErrors, "Username too short. Min length is " . self::MIN_USER_LENGTH);
        }

        if (preg_match('/^[A-Za-z0-9_]+$/', $user->user) === 0) {
            array_push($validationErrors, 'Username can only contain letters and numbers');
        }

        return $validationErrors;
    }

    static function validateAge(User $user)
    {
        $age = $user->getAge();

        if ($age >= 0 && $age <= 150) {
            return true;
        }

        return false;
    }

    /**
     * Find user in db by username.
     *
     * @param string $username
     * @return mixed User or null if not found.
     */
    static function findByUser($username)
    { 
        $prepare=self::$app->db->prepare(self::FIND_BY_NAME,array(PDO::ATTR_CURSOR=>PDO::CURSOR_FWDONLY));
        $prepare->execute(array(":user"=>$username));

        $row=$prepare->fetch(PDO::FETCH_ASSOC);

        if($row == false) 
            return null;

        return User::makeFromSql($row);
    }

    static function deleteByUsername($username)
    {
        $prepare=self::$app->db->prepare(self::DEL_USER,array(PDO::ATTR_CURSOR=>PDO::CURSOR_FWDONLY));
        return $prepare->execute(array(":user"=>$username));
    }

    static function all()
    {
        $query = "SELECT * FROM users";
        $results = self::$app->db->query($query);

        $users = [];

        foreach ($results as $row) {
            $user = User::makeFromSql($row);
            array_push($users, $user);
        }

        return $users;
    }

    static function makeFromSql($row)
    {
        return User::make(
            $row['id'],
            $row['user'],
            $row['pass'],
            $row['email'],
            $row['bio'],
            $row['age'],
            $row['isadmin']
        );
    }
}
User::$app = \Slim\Slim::getInstance();
