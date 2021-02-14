<?php //va interroger la base de donnée pour recuperer des infos concernant la table user
namespace App\Models;

use PDO;
use App\Entities\User;
use Exception;

/**
 * UserManager
 * 
 * manage access to the post database table
 */
class UserManager extends Manager
{
    public function findByUserLogin(string $login)
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM user WHERE login = :login');
        $query->execute(['login' => $login]);
        $query->setFetchMode(PDO::FETCH_CLASS, User::class);
        $user = $query->fetch();
        if($user === false){
            throw new Exception('aucun user ne correspond a ce login');
        }
        return $user;
    }

}