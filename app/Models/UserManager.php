<?php //va interroger la base de donnée pour recuperer des infos concernant la table user
namespace App\Models;

use PDO;
use Exception;
use App\Entities\Post;
use App\Entities\User;

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
     * Method deleteUser delete a user 
     *
     * @param int $id user id to delete 
     *
     * @return void
     */
    public function deleteUser(int $id) : void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('DELETE FROM user WHERE id = :id');
        $result = $query->execute(['id' => $id]);
        if($result === false){
            throw new Exception('impossible de supprimer l utilisateur :'.$id.'peut être il n\'existe pas');
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
                                                validate = :validate,
                                                userType_id = :userType_id
                            WHERE id = :id');
        $result = $query->execute([
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'slogan' => $user->getSlogan(),
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
            'validate' => $user->getValidate()->format('Y-m-d H:i:s'),
            // 'validate' => $user->getValidate(),
            'userType_id' => $user->getUserType_id(),
            'id' => $user->getId()
        ]);
        
        if($result === false){
            throw new Exception('impossible de modifier le user'.$user->getId());
        }
    }

// --------------------------------------------------------------------------------------
    // ajoute le user (en attribut de cette fonction) a la table post en bdd
    public function addUser(User $user)
    {
        $db = $this->dbConnect();
        
        $query = $db->prepare('INSERT INTO user SET firstName = :firstName, 
                                                    lastName = :lastName,
                                                    email = :email,
                                                    picture = :picture,
                                                    logo = :logo,
                                                    slogan = :slogan,
                                                    socialNetworks = :socialNetworks,
                                                    login = :login,
                                                    password = :password,
                                                    validate = :validate');
        $result = $query->execute([
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'picture' => $user->getPicture()->format('Y-m-d H:i:s'),
            'logo' => $user->getLogo(),
            'slogan' => $user->getSlogan(),
            'socialNetworks' => $user->getSocialNetworks(),
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
            'validate' => $user->getValidate()
            ]);

        if($result === true){
            return $db->lastInsertId();
        } else {
            throw new Exception('impossible de de creer l enregistrement du user');
        }
    }

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

    // methode pour recuperer un tableau des users que l on va utiliser dans le select
    public function listSelect(): array
    {
        $users = $this->getListUsers();
        $results = [];
        foreach($users as $user){
            $results[$user->getId()] = $user->getLastName();
        }
        return $results;
    }

// ------------------------------MODIFICATION----------------------------------------------

    // recuperer l'user d'un post
    public function getUserPost(Post $post)
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT user_id FROM post WHERE id = :id');
        $query->execute(['id' => $post->getId()]);

        $userId = $query->fetch()["user_id"];
        $user = $this->getUser($userId);

        if($user === false){
            throw new Exception('aucun user ne correspond a cet post');
        }
        return $user;
    }

}