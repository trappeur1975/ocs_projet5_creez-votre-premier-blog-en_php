<?php //va interroger la base de donnée pour recuperer des infos concernant la table post
namespace App\Models;

use PDO;
use App\Entities\Media;
use Exception;

/**
 * MediaManager
 * 
 * manage access to the media database table
 */
class MediaManager extends Manager
{
    public function findMediaForPost(int $id) : array
    {
        $db = $this->dbConnect();
        // $query = $db->prepare('SELECT * FROM media WHERE post_id = :id');
        $query = $db->prepare('SELECT * FROM media 
                                INNER JOIN mediaType
                                ON media.mediaType_id = mediaType.id
                                WHERE post_id = :id');
        $query->execute(['id' => $id]);
        $listMediasForPost = $query ->fetchAll(PDO::FETCH_CLASS, Media::class);
        return $listMediasForPost;
    }
}