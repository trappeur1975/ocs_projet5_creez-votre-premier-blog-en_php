<?php //va interroger la base de donnée pour recuperer des infos concernant la table user
namespace App\Models;

use PDO;
use Exception;
use App\Entities\UserType;

/**
 * UserTypeManager
 * 
 * manage access to the userType database table
 */
class UserTypeManager extends Manager
{
    
    /**
     * Method getUserType which displays the content of a userType 
     *
     * @param integer $id id of the userType we want to display
     *
     * @return User the content of the userType
     */
    public function getUserType(int $id)
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM userType WHERE id = :id');
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, UserType::class);
        $userType = $query->fetch();
        if($userType === false){
            throw new Exception('aucun user ne correspond a cet ID');
        }
        return $userType;
    }
    
    /**
     * Method getListUserTypes which returns the list of userType (as an object of type userType) 
     *
     * @return UserType[] 
     */
    public function getListUserTypes()
    {
        $db = $this->dbConnect();    
        $query = $db->query('SELECT * FROM userType');
        $listUserTypes = $query ->fetchAll(PDO::FETCH_CLASS, UserType::class);
        return $listUserTypes;
    }

    // methode pour recuperer un tableau des userTypes que l on va utiliser dans le select
    public function listSelect(): array
    {
        $userTypes = $this->getListUserTypes();

        $results = [];
        foreach($userTypes as $userType){
            $results[$userType->getId()] = $userType->getStatus();
        }
        return $results;
    }
}