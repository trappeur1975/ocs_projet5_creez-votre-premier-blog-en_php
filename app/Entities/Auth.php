<?php
namespace App\Entities;

use Exception;

class Auth {
    
    /**
     * check that the user is logged in 
     *
     * @return void
     */
    public static function check(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        
        if(!isset($_SESSION['connection'])){
            // throw new Exception('vous n\'étes identifier sur le site');
            header('Location: /backend/connection?badConnection=true');
        }
    }
}