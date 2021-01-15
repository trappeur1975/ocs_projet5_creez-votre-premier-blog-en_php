<?php
namespace Ocs\Blog\Model;

class Manager // connection a la base de donnee
{
    protected function dbConnect()
    {
        // NE PAS OUBLIER "charset=utf8" POUR REGLER LES PROBLEME D ENCODAGE
        $db = new \PDO('mysql:host=localhost;dbname=p5_ocs_blog_php; charset=utf8', 'root', '');
        return $db;
    }
}