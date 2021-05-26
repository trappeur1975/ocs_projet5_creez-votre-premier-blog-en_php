<?php
namespace App\Models;

use PDO;
use Exception;
use App\Entities\Post;
use App\Entities\User;
use \DateTime;

/**
 * UserManager
 * 
 * manage access to the user database table
 */
class UserManager extends Manager
{
    /**
     * Method getListUsers which returns the list of user (as an object of type user) 
     *
     * @return User[] 
     */
    public function getListUsers()
    {
        $db = $this->dbConnect();    
        $query = $db->query('SELECT * FROM user');
        $listUsers = $query ->fetchAll(PDO::FETCH_CLASS, User::class);
        return $listUsers;
    }

    /**
     * Method getUser which displays the content of a user 
     *
     * @param integer $id id of the user we want to display
     *
     * @return User the content of the user
     */
    public function getUser(int $id)
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM user WHERE id = :id');
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, User::class);
        $user = $query->fetch();

        if($user === false){
            throw new Exception('aucun user ne correspond a cet ID');
        }
        return $user;
    }

    /**
     * Method addUser adds the user to the user table in database 
     *
     * @param User $user
     *
     * @return integer
     */
    public function addUser(User $user)
    {
        $db = $this->dbConnect();
        
        $query = $db->prepare('INSERT INTO user SET firstName = :firstName, 
                                                    lastName = :lastName,
                                                    email = :email,
                                                    slogan = :slogan,
                                                    login = :login,
                                                    password = :password,
                                                    validate = :validate,
                                                    userType_id = :userType_id');
        $result = $query->execute([
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'slogan' => $user->getSlogan(),
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
            // 'validate' => $user->getValidate()->format('Y-m-d H:i:s'),
            'validate' => $user->getValidate(),
            'userType_id' => $user->getUserType_id()
            ]);

        if($result === true){
            return $db->lastInsertId();
        } else {
            throw new Exception('impossible d\'enregistrer le user en base de donnee');
        }
    }

    /**
     * Method updateUser update the content of a user 
     *
     * @param User $user user to update 
     *
     * @return void
     */
    public function updateUser(User $user): void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('UPDATE user SET firstName = :firstName, 
                                                lastName = :lastName,
                                                email = :email,
                                                slogan = :slogan,
                                                login = :login,
                                                password = :password,
                                                userType_id = :userType_id
                            WHERE id = :id');
        $result = $query->execute([
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'slogan' => $user->getSlogan(),
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
            'userType_id' => $user->getUserType_id(),
            'id' => $user->getId()
        ]);
        
        if($result === false){
            throw new Exception('impossible de modifier le user'.$user->getId());
        }
    }

    /**
     * Method deleteUser delete a user 
     *
     * @param int $id user id to delete 
     *
     * @return void
     */
    public function deleteUser(int $id) : void
    {
        $user = $this->getUser($id);
        $emailUser= $user->getEmail();
        
        $db = $this->dbConnect();
        $query = $db->prepare('DELETE FROM user WHERE id = :id');
        $result = $query->execute(['id' => $id]);
        if($result === false){
            throw new Exception('impossible de supprimer l utilisateur :'.$id);
        }
        sendEmail($emailUser, 'Supression de votre compte sur BlogNico', 'Votre compte user sur le BlogNico a ete SUPPRIMER');
    }

    /**
     * validates the user whose id is indicated in the function parameter 
     *
     * @param $id of the the we want to validate 
     *
     */
    public function validateUser(int $idUser)
    {
        $user = $this->getUser($idUser);
        
        $dateTime = new Datetime();
        $validate = $dateTime->format('Y-m-d H:i:s');
  
        $db = $this->dbConnect();
        $query = $db->prepare('UPDATE user SET validate = :validate WHERE id = :idUser');
        $result = $query->execute([
            'validate' => $validate,
            'idUser' => $idUser
            ]);

        if($result === false){
            throw new Exception('impossible de valider le user :'.$idUser);
        }

        sendEmail($user->getEmail(), 'Votre compte sur BlogNico VALIDER', 'Votre compte user  sur le BlogNico a ete VALIDER part de l\'administrateur du site');
    }

    /**
     * Method ListUsersWaiteValidate which returns the list of users awaiting validation (as an object of type user) 
     *
     * @return User[] 
     */
    public function listUsersWaiteValidate()
    {
        $db = $this->dbConnect();    
        $query = $db->query('SELECT * FROM user where validate IS NULL');
        $listUsersWaiteValidate = $query->fetchAll(PDO::FETCH_CLASS, User::class);
        return $listUsersWaiteValidate;
    }

    /**
     * Method getUserSatus displays the status of the user whose id is in the function parameter 
     *
     * @param integer $idUser id of the user whose status we want to know 
     *
     * @return String the status of the user
     */
    public function getUserSatus(int $idUser)
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT usertype.status FROM user 
                                INNER JOIN usertype
                                ON user.userType_id = usertype.id
                                WHERE user.id = :idUser');
        $query->execute(['idUser' => $idUser]);
        $query->setFetchMode(PDO::FETCH_CLASS, UserType::class);

        $status = $query->fetch();

        if($status === false){
            throw new Exception('aucun user dont vous rechercher le status ne correspond a cet ID');
        }
        return $status;
    }

    /**
     * Method identicalDataSearch search if a data is already used (present) in the user accounts and if so, review the id which already has this data and if not we return null 
     *
     * @param String $key
     * @param $data $data
     *
     * @return integer
     */
    public function identicalDataSearch(String $key, $data){
        $idUserIidenticalData = null;
        $listUsers = $this->getListUsers();
        $method = 'get'.ucfirst($key);
        foreach( $listUsers as $user){
            if($user->$method() === $data){
                $idUserIidenticalData = $user->getId();
                break;	//to exit the foreach loop 
            }
        }
        return $idUserIidenticalData;
    }

    
    /**
     * Method findByUserLogin
     *
     * @param string $login
     *
     * @return void
     */
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

    /**
     * Method listUsersFormSelect method to retrieve an array of users that we will use in the select 
     *
     * @param array $listUsers [explicite description]
     *
     * @return array
     */
    public function listUsersFormSelect(array $listUsers): array
    {
        $results = [];
        foreach($listUsers as $user){
            $results[$user->getId()] = $user->getLastName();
        }
        return $results;
    }

}