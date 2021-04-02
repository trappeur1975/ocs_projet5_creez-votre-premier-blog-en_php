<?php //va interroger la base de donnée pour recuperer des infos concernant la table post
namespace App\Models;

use PDO;
use Exception;
use App\Entities\Post;
use App\Entities\User;
use App\Entities\Media;

/**
 * MediaManager
 * 
 * manage access to the media database table
 */
class MediaManager extends Manager
{    
  
    /**
     * Method getListMedias which returns the list of media (as an object of type Media) 
     *
     * @return Media[] 
     */
    public function getListMedias()
    {
        $db = $this->dbConnect();

        $query = $db->prepare('SELECT * FROM mediaType 
                                INNER JOIN media
                                ON mediaType.id = media.mediaType_id');
        /*OBLIGER DE le code ci dessus car avec le code ci dessous on ne recupere pas les id de la table "media" mais ceux de la table "mediaType"
        $query = $db->prepare('SELECT * FROM media 
                                INNER JOIN mediaType
                                ON media.mediaType_id = mediaType.id');*/
        $query->execute();
        $listMedias = $query ->fetchAll(PDO::FETCH_CLASS, Media::class);
        return $listMedias;
    }
    
    /**
     * Method getMedia which displays the content of a media 
     *
     * @param integer $id id of the media we want to display
     *
     * @return Media the content of the media
     */
    public function getMedia(int $id)
    {
        $db = $this->dbConnect();
        //OBLIGER DE DE DEFINIR LES DONNEES QUE L'on souhaite car avec *, cela prend id de media type (, mediaType.id)
        $query = $db->prepare('SELECT media.id As id, media.path, media.alt, media.statutActif, media.mediaType_id, media.post_id, mediaType.type FROM media 
                                INNER JOIN mediaType
                                ON media.mediaType_id = mediaType.id
                                WHERE media.id = :id');
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Media::class);
        $media = $query->fetch();
        if($media === false){
            throw new Exception('aucun media ne correspond a cet ID');
        }
        return $media;
    }

// ----------------------------- methode specifique --------------------------

    // ajoute le media (en attribut de cette fonction) a la table media en bdd
    // public function addMedia(Media $media)
    // {
    //     $db = $this->dbConnect();
        
    //     $query = $db->prepare('INSERT INTO post SET path = :path, 
    //                                               alt = :alt,
    //                                               type = :type,
    //                                               post_id = :post_id,
    //                                               user_id = :user_id');
    //     $result = $query->execute([
    //         'path' => $post->getPath(),
    //         'alt' => $post->getAlt(),
    //         'mediaType_id' => $post->getType(),
    //         'post_id' => $post->getDateCreate()->format('Y-m-d H:i:s'),
    //         'dateChange' => $post->getDateChange(),
    //         'user_id' => $post->getUser_id()
    //         ]);

    //     if($result === true){
    //         return $db->lastInsertId();
    //     } else {
    //         throw new Exception('impossible de de creer l enregistrement du post');
    //     }
    // }

    /**
     * Method getMediasForPost method that returns the list of media linked to a post 
     *
     * @param int $idPost id of the post which we want to retrieve the linked media 
     *
     * @return Media[]  all media of a post
     */
    public function getListMediasForPost(Post $post): array
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT media.id As id, media.path, media.alt, media.statutActif, media.mediaType_id, media.post_id, mediaType.type FROM media 
                                INNER JOIN mediaType
                                ON media.mediaType_id = mediaType.id
                                WHERE media.post_id = :id');
        $query->execute(['id' => $post->getId()]);
        $listMediasForPost = $query ->fetchAll(PDO::FETCH_CLASS, Media::class);

        // dd($listMediasForPost);

        return $listMediasForPost;
    }

    // return le premier media d un post si celui ci en a au moins un sinon return un tableau vide
    public function getFirstMediaPost(Post $post)
    {
        $medias = $this->getListMediasForPost($post);

        // on verifie que le post contient bien des media
        if ($medias != [])
        {
            $media =  $medias[0];
            return $media;
        } else {
            return [];
        }
    }

    // OK MAIS  OBLIGER DE SUPPRIMER "mediatype.id," DANS MON SELECT CAR SINON MES ID DE media.id seront ceux de mediatype.id  => voir si dessous
    /**
     * Method getMediasForUser method that returns the list of media linked to a user 
     *
     * @param User $User the user whose media we want to retrieve 
     *
     * @return Media[]
     */
    public function getListMediasForUser(User $user): array
    {
        $db = $this->dbConnect();

        // LA SOLUTION
        $query = $db->prepare('SELECT media.id As id, media.path, media.alt, media.statutActif, media.mediaType_id, media.post_id, media.user_id, mediatype.id As mediaTypeId, mediatype.type FROM media 
                                INNER JOIN mediaType
                                ON media.mediaType_id = mediaType.id
                                WHERE media.user_id = :id');
        $query->execute(['id' => $user->getid()]);

        $listMediasForUser = $query ->fetchAll(PDO::FETCH_CLASS, Media::class);
  
        return $listMediasForUser;
    }

    // methode pour recuperer un tableau de media lier a un utilisateur que l on va utiliser dans le select
    public function listSelect(User $user): array
    {
        $medias = $this->getListMediasForUser($user);
        $results = [];
        
        foreach($medias as $media){
            $results[$media->getId()] = $media->getPath(); 
        }

        return $results;
    }

    // methode pour recuperer les id de la function getListMediasForPost(Post $post) de cette classe
    public function getIdOftListMediasForPost($post): array
    {
        $medias = $this->getListMediasForPost($post);
    
        $results = [];
        
        foreach($medias as $media){
            // on enregistre les id des medias de l'auteur du post pour les mettre en sur brillance que si c'est media sont en "statutActif = true"
            if ($media->getStatutActif() === true){
                $results[] = $media->getId(); 
            }
            
        }
        
        return $results;
    }

    // methode pour changer le statutActif d'un media
    public function UpdateStatutActifMedia($idMedia,$newStatutActif): void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('UPDATE media SET statutActif = :statutActif WHERE id = :id');
        $result = $query->execute([
            'statutActif' => $newStatutActif,
            'id' => $idMedia
        ]);

        if($result === false){
            throw new Exception('impossible de d effectuer le changement de statutActif demander sur les medias');
        }
    }

    // pour changer le post_id d'un media
    public function UpdatePostIdMedia($idMedia,$newPostId): void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('UPDATE media SET post_id = :postid WHERE id = :id');
        $result = $query->execute([
            'postid' => $newPostId,
            'id' => $idMedia
        ]);

        if($result === false){
            throw new Exception('impossible de d effectuer le changemenent de post_id demander sur le des medias');
        }
    }

// ---------------- ! ATTENTION pour le front (pour afficher le contenu d un post) ancienne methode voir si toujours d actualité ou si on ne peux pas la remplacer par une nouvelle methode (qui sontbau dessus)
    /**
     * Method findMediasForPost
     *
     * @param int $id id of the post in which you are looking for your media 
     *
     * @return array all media of a post
     */
    public function findMediasForPost(int $id): array
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM media 
                                INNER JOIN mediaType
                                ON media.mediaType_id = mediaType.id
                                WHERE media.post_id = :id');
        $query->execute(['id' => $id]);
        $listMediasForPost = $query ->fetchAll(PDO::FETCH_CLASS, Media::class);
        return $listMediasForPost;
    }

}