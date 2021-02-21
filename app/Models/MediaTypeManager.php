<?php //va interroger la base de donnée pour recuperer des infos concernant la table mediaType
namespace App\Models;

use PDO;
use App\Entities\MediaType;
use Exception;


/**
 * MediaTypeManager
 * 
 * manage access to the mediaType database table
 */
class MediaTypeManager extends Manager
{
    
    /**
     * Method getListMediatypes which returns the list of mediaType (as an object of type Mediatype) 
     *
     * @return Mediatype[] 
     */
    public function getListMediatypes()
    {
        $db = $this->dbConnect();    
        $query = $db->query('SELECT * FROM mediatype');
        $listMediaTypes = $query ->fetchAll(PDO::FETCH_CLASS, MediaType::class);
        return $listMediaTypes;
    }

    /**
     * Method getMediaType which displays the content of a mediaType 
     *
     * @param integer $id id of the mediaType we want to display
     *
     * @return MediaType the content of the mediaType
     */
    public function getMediaType(int $id)
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM mediatype WHERE id = :id');
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, MediaType::class);
        $mediaType = $query->fetch();
        if($mediaType === false){
            throw new Exception('aucun mediaType ne correspond a cet ID');
        }
        return $mediaType;
    }

    /**
     * Method deleteMediaType delete a mediaType 
     *
     * @param int $id id mediaType to delete 
     *
     * @return void
     */
    public function deleteMediaType(int $id) : void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('DELETE FROM mediatype WHERE id = :id');
        $result = $query->execute(['id' => $id]);
        if($result === false){
            throw new Exception('impossible de supprimer le mediaType :'.$id.'peut être il n\'existe pas');
        }
    }
   
    /**
     * Method updateMediaType update the content of a mediaType 
     *
     * @param Post $mediaType mediaType to update 
     *
     * @return void
     */
    public function updateMediaType(MediaType $mediaType): void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('UPDATE mediatype SET type = :type WHERE id = :id');
        $result = $query->execute([
            'type' => $mediaType->getType(),
            'id' => $mediaType->getId()
        ]);
        
        if($result === false){
            throw new Exception('impossible de modifier le mediaType'.$mediaType->getId());
        }
    }

// --------------------------------------------------------------------------------------

    // ajoute le mediaType (en attribut de cette fonction) a la table mediaType en bdd
    public function addMediaType(MediaType $mediaType)
    {
        $db = $this->dbConnect();
        
        $query = $db->prepare('INSERT INTO mediatype SET type = :type');
        $result = $query->execute(['type' => $mediaType->getType()]);
        if($result === true){
            return $db->lastInsertId();
        } else {
            throw new Exception('impossible de de creer l enregistrement du mediaType');
        }
    }

}