<?php
namespace App\Entities;

use Exception;
use App\Models\UserManager;

class Auth {
    
    /**
     * checks if the status of the user (connect) (=> $ user Status) is valid (among different status => $ statutes) to access a function or part of the site
     *
     * @param array $statutes different statuses possible 
     * @param $userStatus $userStatus user status 
     *
     * @return boolean
     */
    private static function validator(array $statutes, $userStatus){
        $validStatus = false;

        foreach($statutes as $status){
            if ($status === $userStatus) {
                $validStatus = true;
                break;
            }
        }
        return $validStatus;
    }
    
    /**
     * Method sessionStart
     */
    public static function sessionStart(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['connection'])) {
            $userManager = new UserManager();
            $userLogged = $userManager->getUser($_SESSION['connection']);

            return $userLogged;
        }
    }

    
 
    /**
     * Method check verifies that the user is connected and that his status allows him to access a feature or part of the site 
     *
     * @param array $authorizedStatutes different statuses possible 
     *
     */
    public static function check(array $authorizedStatutes){       
        $userManager = new UserManager();   //new
        $AuthorizedAccess = false;
        
        $userLogged = self::sessionStart();
     
        if (!isset($_SESSION['connection'])) {
            header('Location: /backend/connection?badConnection=true');
        }

        $AuthorizedAccess = self::validator($authorizedStatutes, $userManager->getUserSatus($_SESSION['connection'])['status']);

        if ( $AuthorizedAccess !== true) {
            
            header('Location: /backend/connection?badConnection=true');
        }
        
        return $userLogged;
    }

}