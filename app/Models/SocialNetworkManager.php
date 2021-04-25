<?php //va interroger la base de donnée pour recuperer des infos concernant la table socialNetwork
namespace App\Models;

use PDO;
use App\Entities\SocialNetwork;
use Exception;

/**
 * SocialNetworkManager
 * 
 *  manage access to the socialNetwork database table
 */
class SocialNetworkManager extends Manager
{
     // ajoute le user (en attribut de cette fonction) a la table user en bdd
     public function addSocialNetwork(SocialNetwork $socialNetwork)
     {
         $db = $this->dbConnect();
         
         $query = $db->prepare('INSERT INTO socialnetwork SET url = :url, 
                                                     user_id = :user_id');
         $result = $query->execute([
             'url' => $socialNetwork->getUrl(),
             'user_id' => $socialNetwork->getUser_id()
             ]);
 
         if($result === true){
             return $db->lastInsertId();
         } else {
             throw new Exception('impossible de creer l enregistrement du socialNetwork');
         }
     }

    /**
     * Method deleteSocialNetwork delete a socialNetwork 
     *
     * @param int $id socialNetwork id to delete 
     *
     * @return void
     */
    public function deleteSocialNetwork(int $id) : void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('DELETE FROM socialnetwork WHERE id = :id');
        $result = $query->execute(['id' => $id]);
        if($result === false){
            throw new Exception('impossible de supprimer le socialNetwork :'.$id.'peut être il n\'existe pas');
        }
    }

     // ----------------------------- methode specifique --------------------------
    /**
     * Method getListSocialNetworksForUser method that returns the list of socialnetwork linked to a user 
     *
     * @param int $idUser id of the user which we want to retrieve the linked socialNetworks 
     *
     * @return SocialNetwork[]  all media of a post
     */
    public function getListSocialNetworksForUser(int $idUser): array
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM socialnetwork WHERE user_id = :id');
        $query->execute(['id' => $idUser]);

        $listSocialNetworksForUser = $query ->fetchAll(PDO::FETCH_CLASS, SocialNetwork::class);

        return $listSocialNetworksForUser;
    }

     // methode pour recuperer un tableau de soacialNetwork lier a un utilisateur que l on va utiliser dans le select
     public function listSelect(int $idUser): array
     {
         $socialNetworks = $this->getListSocialNetworksForUser($idUser);
         $results = [];
         
         foreach($socialNetworks as $socialNetwork){
             $results[$socialNetwork->getId()] = $socialNetwork->getUrl(); 
         }
 
         return $results;
     }
}