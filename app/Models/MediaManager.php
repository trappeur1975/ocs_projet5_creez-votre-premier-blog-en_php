<?php
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
        // OBLIGATE TO DEFINE THE DATA WE want because with *, it takes id of media type (, mediaType.id) 
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

    /**
     * Method addMediaImage adds the media Image (as an attribute of this function) to the media table in bdd 
     *
     * @param Media $mediaImage media that you want to save in the database 
     * @param $fileConfig site configuration file which contains information concerning media images in particular 
     * @param $fileUploader file (media image) which has been uploaded on the site and which we want to save from the server in the folder (public> media) 
     * 
     * @return Media the content of the media
     */    
    public function addMediaImage($mediaImage, $fileConfig, $fileUploader)
    {
        $fileName = $fileUploader ['name'];
        $fileInfos = pathinfo($fileName);
        $fileTypeUpload = $fileInfos['extension'];
        
        // we check that the type of the uploader file is authorized to be saved on the site (database and server) 
        $authorizedFileTypes = searchDatasFile('image');   // see globalFunctions.php file 
        $validateFile = validateData($authorizedFileTypes, $fileTypeUpload);  // see globalFunctions.php file 

        $authorizedMaxFileSize = searchDatasFile('maxFileSizeImage')[1]; // maximum file size uploader allowed 
        $validateFileSize = $fileUploader['size'] <= $authorizedMaxFileSize;

        if($validateFile AND $validateFileSize){

            // on sauvegarde en base de donnee
            $db = $this->dbConnect();
            
            $query = $db->prepare('INSERT INTO media SET path = :path, 
                                                        alt = :alt,
                                                        statutActif = :statutActif,
                                                        mediaType_id = :mediaType_id,
                                                        post_id = :post_id,
                                                        user_id = :user_id');
            $result = $query->execute([
                'path' => $mediaImage->getPath(),
                'alt' => $mediaImage->getAlt(),
                'statutActif'=> $mediaImage->getStatutActif(),
                'mediaType_id' => $mediaImage->getMediaType_id(),
                'post_id' => $mediaImage->getPost_id(),
                'user_id' => $mediaImage->getUser_id()
                ]);
            
            //we transfer the uploader file to the site from its temporary storage folder to its final storage folder
            if($result === true){
                $from = $fileUploader ['tmp_name']; //temporary storage path of the uploader file + its name 
                $to = $mediaImage->getPath();
              
                move_uploaded_file( $from, $to);
                return $to;
            }else {
                throw new Exception('impossible d\'enregistrer le media Image en base de donne et sur le serveur');
            }
        } else {
            throw new Exception('impossible de creer l enregistrement du media Image (peut etre l extension du fichier, son poids, ...)');
        }
    }
    
    /**
     * Method addMediaVideo adds the Video media (as an attribute of this function) to the bdd media table 
     *
     * @param Media $mediaVideo
     *
     * @return void
     */
    public function addMediaVideo(Media $mediaVideo)
    {
        // we check that the type of the uploader file is authorized to be saved on the site (database and server) 
        $authorizedFileTypes = searchDatasFile('video');   //voir fichier globalFunctions.php
        $validateVideo = validateWordInString($authorizedFileTypes, $mediaVideo->getPath());

        if($validateVideo){
            $db = $this->dbConnect();
            $query = $db->prepare('INSERT INTO media SET path = :path, 
                                                        alt = :alt,
                                                        statutActif = :statutActif,
                                                        mediaType_id = :mediaType_id,
                                                        post_id = :post_id,
                                                        user_id = :user_id');
            $result = $query->execute([
                'path' => $mediaVideo->getPath(),
                'alt' => $mediaVideo->getAlt(),
                'statutActif'=> $mediaVideo->getStatutActif(),
                'mediaType_id' => $mediaVideo->getMediaType_id(),
                'post_id' => $mediaVideo->getPost_id(),
                'user_id' => $mediaVideo->getUser_id()
                ]);
            if($result === false){
                throw new Exception('impossible d\'enregistrer le media video en base de donnee');
            }
        } else {
            throw new Exception('impossible d\'enregistrer le media video');
        }
    }

    /**
     * Method deleteMedia delete a media 
     *
     * @param int $id media id to delete 
     *
     * @return void
     */
    public function deleteMedia(int $id) : void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('DELETE FROM media WHERE id = :id');
        $result = $query->execute(['id' => $id]);
        if($result === false){
            throw new Exception('impossible de supprimer le media :'.$id.'peut être il n\'existe pas');
        }
    }

    /**
     * Method getMediasForPost method that returns the list of media linked to a post 
     *
     * @param int $idPost id of the post which we want to retrieve the linked media 
     *
     * @return Media[]  all media of a post
     */
    public function getListMediasForPost(int $idPost): array
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM media WHERE post_id = :id');
        $query->execute(['id' => $idPost]);

        $listMediasForPost = $query ->fetchAll(PDO::FETCH_CLASS, Media::class);

        return $listMediasForPost;
    }

    /**
     * Method getMediasForUser method that returns the list of media linked to a user 
     *
     * @param int $idUser the id user whose medias we want to retrieve 
     *
     * @return Media[]
     */

    public function getListMediasForUser(int $idUser): array
    {
        $db = $this->dbConnect();

        $query = $db->prepare('SELECT * FROM media WHERE user_id = :id');
        $query->execute(['id' => $idUser]);

        $listMediasForUser = $query ->fetchAll(PDO::FETCH_CLASS, Media::class);

        return $listMediasForUser;
    }


    /**
     * Method getListMediasForUserForType method that returns the list of media for a user for the type (example mediaType "image" + "video")
     *
     * @param array $mediasUser media list of a user 
     * @param array  $idsMediaType list of id of the mediaTypes we want to have 
     *
     * @return Media[]
     */
    public function getListMediasForUserForType(array $mediasUser, array $idsMediaType): array
    {
        $results = [];
        foreach($idsMediaType as $idMediaType){
            foreach($mediasUser as $mediaUser){
                if($mediaUser->getMediaType_id() ===  $idMediaType){
                    $results[] =  $mediaUser; 
                }
            }
        }
        return $results;
    }

    /**
     * Method listMediasFormSelect method to retrieve an array of media linked to a user that we will use in the select 
     *
     * @param array $listMediasForUser
     *
     * @return array
     */
    public function listMediasFormSelect(array $listMediasForUser): array
    {
        $results = [];
        
        foreach($listMediasForUser as $media){
            $results[$media->getId()] = $media->getPath(); 
        }

        return $results;
    }

    /**
     * Method getIdOftListMediasActifForPost method to retrieve the id (having an active status) of the function getListMediasForPost (Post $ post) of this class 
     *
     * @param int $idPost
     *
     * @return array
     */
    public function getIdOftListMediasActifForPost(int $idPost): array
    {
        $medias = $this->getListMediasForPost($idPost);
    
        $results = [];
        
        foreach($medias as $media){
            // we record the media id of the author of the post to highlight them if it is media are in "active status = true" 
            if ($media->getStatutActif() === true){
                $results[] = $media->getId(); 
            }
            
        }
        
        return $results;
    }
  
    /**
     * Method updateStatutActifMedia method to change the Active status of a media 
     *
     * @param $idMedia
     * @param $newStatutActif
     *
     * @return void
     */
    public function updateStatutActifMedia($idMedia,$newStatutActif): void
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

    /**
     * Method updatePostIdMedia to change the post_id of a media 
     *
     * @param $idMedia
     * @param $newPostId
     *
     * @return void
     */
    public function updatePostIdMedia($idMedia,$newPostId): void
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
    // public function findMediasForPost(int $id): array
    // {
    //     $db = $this->dbConnect();
    //     $query = $db->prepare('SELECT * FROM media 
    //                             INNER JOIN mediaType
    //                             ON media.mediaType_id = mediaType.id
    //                             WHERE media.post_id = :id');
    //     $query->execute(['id' => $id]);
    //     $listMediasForPost = $query ->fetchAll(PDO::FETCH_CLASS, Media::class);
    //     return $listMediasForPost;
    // }

}