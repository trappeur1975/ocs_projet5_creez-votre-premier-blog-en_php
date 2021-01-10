<?php

namespace Ocs\Blog\Model;

class Manager // connection a la base de donnee
{
    protected function dbConnect()
    {
        $db = new \PDO('mysql:host=localhost;dbname=p5_ocs_blog_php', 'root', '');
        return $db;
    }
}