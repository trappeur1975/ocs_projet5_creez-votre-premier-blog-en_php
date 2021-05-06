<?php
namespace App\Entities;

use Exception;
use App\Models\UserManager;

class Auth {
    
    /**
     * check that the user is logged in and the status is 'administrateur'
     *
     * @return void
     */
    public static function check(){       
        $userManager = new UserManager();   //nouveau
        
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        if(!isset($_SESSION['connection']) or $userManager->getUserSatus($_SESSION['connection'])['status'] !== 'administrateur'){
            // throw new Exception('vous n\'étes identifier sur le site');
            header('Location: /backend/connection?badConnection=true');
        }
    }
}