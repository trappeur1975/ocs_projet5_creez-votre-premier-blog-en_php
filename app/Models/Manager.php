<?php
namespace App\Models;

use PDO;

/**
 * Manager
 * 
 * manages access to the database
 */
class Manager // connection a la base de donnee
{    
    /**
     * Method dbConnect
     *
     * connection to the database
     * 
     * @return PDO connection to the database
     */
    protected function dbConnect() : PDO
    {
        $db = new PDO('mysql:host=localhost;dbname=p5_ocs_blog_php; charset=utf8', 'root', ''); // DO NOT FORGET "charset = utf8" TO ADJUST THE ENCODING PROBLEMS 
        return $db;
    }

}