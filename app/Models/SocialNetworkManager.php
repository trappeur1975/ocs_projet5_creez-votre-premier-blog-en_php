<?php
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
    /**
     * Method addSocialNetwork adds the user (as an attribute of this function) to the user table in database
     *
     * @param SocialNetwork $socialNetwork [explicite description]
     *
     * @return integer
     */
    public function addSocialNetwork(SocialNetwork $socialNetwork)
    {
        $errorMessage = null;
        
        // we check that the social network is authorized to be registered on the site (database) 
        $authorizedSocialNetworks = searchDatasFile('socialnetwork');   // see globalFunctions.php file 
        $validateSocialNetwork = validateWordInString($authorizedSocialNetworks, $socialNetwork->getUrl());

        if ($validateSocialNetwork) {
            $db = $this->dbConnect();
            
            $query = $db->prepare('INSERT INTO socialnetwork SET url = :url, 
                                                    user_id = :user_id');
            $result = $query->execute([
                'url' => $socialNetwork->getUrl(),
                'user_id' => $socialNetwork->getUser_id()
                ]);

            if ($result === true) {
                return $db->lastInsertId();
            } else {
                throw new Exception('impossible de creer l enregistrement du socialNetwork en base de donne');
            }
        } else {
            throw new Exception('impossible d\'enregistrer ce socialNetwork');
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
        if ($result === false) {
            throw new Exception('impossible de supprimer le socialNetwork :'.$id);
        }
    }
    
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

    /**
     * Method listSocialNetworksFormSelect method to retrieve an array from soacialNetwork link to a user that we will use in the select 
     *
     * @param array $listSocialNetworksForUser [explicite description]
     *
     * @return array
     */
    public function listSocialNetworksFormSelect(array $listSocialNetworksForUser): array
    {
        $results = [];
        
        foreach($listSocialNetworksForUser as $socialNetwork){
            $results[$socialNetwork->getId()] = $socialNetwork->getUrl(); 
        }

        return $results;
    }

}