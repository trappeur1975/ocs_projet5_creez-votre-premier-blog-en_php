<?php
namespace App\Entities;

use Exception;
use App\Models\UserManager;

class Auth {

    // verifie si le status de l'user (connecter) (=> $userStatus) et bien valide (parmis different status => $statutes) pour acceder a une fonction ou partie du site
    private static function validator(array $statutes, $userStatus){
        $validStatus = false;

        foreach($statutes as $status){
            if($status === $userStatus){
                $validStatus = true;
                break;
            }
        }
        return $validStatus;
    }

    public static function sessionStart(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        if(isset($_SESSION['connection'])){
            $userManager = new UserManager();
            $userLogged = $userManager->getUser($_SESSION['connection']);

            return $userLogged;
        }

        // return null;
    }

    
    /**
     * verifies that the user is connected and that his status allows him to access a feature or part of the site 
     *
     * @return void
     */
    public static function check(array $authorizedStatutes){       
        $userManager = new UserManager();   //nouveau
        $AuthorizedAccess = false;
        
        $userLogged = self::sessionStart();
        // if(session_status() === PHP_SESSION_NONE){
        //     session_start();
        // }
        
        if(!isset($_SESSION['connection'])){
            header('Location: /backend/connection?badConnection=true');
        }

        $AuthorizedAccess = self::validator($authorizedStatutes, $userManager->getUserSatus($_SESSION['connection'])['status']);

        if( $AuthorizedAccess !== true){
            
            header('Location: /backend/connection?badConnection=true');
        }
        
        return $userLogged;
    }

}