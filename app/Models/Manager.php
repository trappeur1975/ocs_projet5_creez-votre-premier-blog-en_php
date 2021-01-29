<?php
namespace App\Models;

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
    protected function dbConnect()
    {
        // NE PAS OUBLIER "charset=utf8" POUR REGLER LES PROBLEME D ENCODAGE
        $db = new \PDO('mysql:host=localhost;dbname=p5_ocs_blog_php; charset=utf8', 'root', '');
        return $db;
    }
}