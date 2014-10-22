<?php



namespace tdt4237\webapp\models;

use PDO;

class Movie
{
    const FIND_MOVIE    = "SELECT * FROM movies WHERE id=:id";
    const FIND_ALL      = "SELECT * FROM movies";


    private $id;
    private $name;
    private $imageUrl;

    static $app;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    static function make($id, $name, $imageUrl)
    {
        $movie = new Movie();
        $movie->id = $id;
        $movie->name = $name;
        $movie->imageUrl = $imageUrl;

        return $movie;
    }

    /**
     * Find a movie by id.
     */
    static function find($id)
    {
        $prepare=self::$app->db->prepare(self::FIND_MOVIE,array(PDO::ATTR_CURSOR=>PDO::CURSOR_FWDONLY));
        if($prepare->execute(array(":id"=>$id)))
        {
            $row=$prepare->fetch(PDO::FETCH_ASSOC);
            
            return self::makeFromRow($row);
        }
        return null;
    }

    /**
     * Fetch all movies.
     */
    static function all()
    {

        $prepare=self::$app->db->prepare(self::FIND_ALL,array(PDO::ATTR_CURSOR=>PDO::CURSOR_FWDONLY));
        $prepare->execute();

        $movies = [];
        while($row=$prepare->fetch(PDO::FETCH_ASSOC))
        {
            $movie = self::makeFromRow($row);
            array_push($movies, $movie);
        }

        return $movies;
    }

    static function makeFromRow($row) {
        $movie = self::make(
            $row['id'],
            $row['name'],
            $row['imageurl']
        );

        return $movie;
    }
}
Movie::$app = \Slim\Slim::getInstance();
