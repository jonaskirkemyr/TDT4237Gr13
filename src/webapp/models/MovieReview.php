<?php

namespace tdt4237\webapp\models;
use PDO;

class MovieReview
{
    const SELECT_BY_ID          = "SELECT * FROM moviereviews WHERE id = %s";//NOT USED?
    const SELECT_BY_MOVIE_ID    = "SELECT * FROM moviereviews WHERE movieid=:id";
    const INSERT_REVIEW         = "INSERT INTO moviereviews(movieid,author,text) VALUES(:id,:author,:text)";
    const UPDATE_REVIEW         = "UPDATE moviereviews SET author=:author, text=:text WHERE movieid=:id";

    private $id = null;
    private $movieId;
    private $author;
    private $text;

    static $app;

    public function getId()
    {
        return $this->id;
    }

    public function getMovieId()
    {
        return $this->movieId;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setMovieId($id)
    {
        $this->movieId = $id;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    static function make($id, $author, $text)
    {
        $review = new MovieReview();
        $review->id = $id;
        $review->author = $author;
        $review->text = $text;

        return $review;
    }

    /**
     * Insert or save review into db.
     */
    function save()
    {
        $movieId = $this->movieId;
        $author = $this->author;
        $text = $this->text;

        $prepare=null;
        $array=null;



        if ($this->id === null) 
        {
            $prepare=self::$app->db->prepare(self::INSERT_REVIEW,array(PDO::ATTR_CURSOR=>PDO::CURSOR_FWDONLY));
            $array=array(
                        ":id"       => $movieId,
                        ":author"   => $author,
                        ":text"     =>$text
                        );

            $query = "INSERT INTO moviereviews (movieid, author, text) "
                   . "VALUES ('$movieId', '$author', '$text')";
        } 
        else 
        {
            $prepare=self::$app->db->prepare(self::UPDATE_REVIEW,array(PDO::ATTR_CURSOR=>PDO::CURSOR_FWDONLY));
            $array=array(
                        ":author"   => $author,
                        ":text"     => $text,
                        ":id"       => $movieId
                        );
        }

        return $prepare->execute($array);
    }

    static function makeEmpty()
    {
        return new MovieReview();
    }

    /**
     * Fetch all movie reviews by movie id.
     */
    static function findByMovieId($id)
    {
        $prepare=self::$app->db->prepare(self::SELECT_BY_MOVIE_ID,array(PDO::ATTR_CURSOR=>PDO::CURSOR_FWDONLY)); 
        $prepare->execute(array(":id"=>$id));  

        $reviews = [];

        while($row=$prepare->fetch())
        {
            $review = self::makeFromRow($row);
            array_push($reviews, $review);
        }

        return $reviews;
    }

    static function makeFromRow($row) 
    {
        $review = self::make(
            $row['id'],
            $row['author'],
            $row['text']
        );

        return $review;
    }
}
MovieReview::$app = \Slim\Slim::getInstance();
