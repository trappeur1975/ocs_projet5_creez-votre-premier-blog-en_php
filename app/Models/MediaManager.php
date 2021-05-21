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

    /**
     * ajoute le media Image (en attribut de cette fonction) a la table media en bdd
     *
     * @param Media $mediaImage media que l'on souhaite suavegarder en base de donnee
     * @param $fileConfig fichier de configuration du site qui comporte des infos concernant les media images notamment
     * @param $fileUploader fichier (media image) qui a été uploader sur le site et que l on souhaite sauvegarder du le serveur dans le dossier (public > media)
     * 
     * @return Media the content of the media
     */
    public function addMediaImage($mediaImage, $fileConfig, $fileUploader)
    {
        $fileName = $fileUploader ['name'];
        $fileInfos = pathinfo($fileName);
        $fileTypeUpload = $fileInfos['extension'];
        
        //on verifier que le type du fichier uploader est bien autorisé a etre sauvegardé sur le site (base de donnee et serveur)
        $authorizedFileTypes = searchDatasFile('image');   //voir fichier globalFunctions.php
        $validateFile = validateData($authorizedFileTypes, $fileTypeUpload);  //voir fichier globalFunctions.php

        $authorizedMaxFileSize = searchDatasFile('maxFileSizeImage')[1]; //taille maximum du fichier uploader autorise 
        $validateFileSize = $fileUploader['size'] <= $authorizedMaxFileSize; //VERIFIER SI CELA MARCHE AVEC STRING CAS OU LE CHIFFRE DEVIENT DES STRING

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
            
            //on transfert le fichier uploader sur le site de son dossier de stockage temporaire a son dossier de stockage définitif
            if($result === true){
                $from = $fileUploader ['tmp_name']; //chemin temporaire de stockage du fichier uploader + son nom
                $to = $mediaImage->getPath();
              
                move_uploaded_file( $from, $to);
                return $to;
            }else {
                throw new Exception('impossible d\'enregistrer le media Image en base de donne et sur le serveur');
                // $errorMessage = 'impossible de creer l enregistrement du socialNetwork';
            }
        } else {
            throw new Exception('impossible de creer l enregistrement du media Image (peut etre l extension du fichier, son poids, ...)');
        }
    }
    
    // ajoute le media Video (en attribut de cette fonction) a la table media en bdd
    public function addMediaVideo(Media $mediaVideo)
    {
        //on verifier que le type du fichier uploader est bien autorisé a etre sauvegardé sur le site (base de donnee et serveur)
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

    // ----------------------------- methode specifique --------------------------
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

//------------------------------------NOUVEAU--------------------------

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

    // methode pour recuperer un tableau de media lier a un utilisateur que l on va utiliser dans le select
    public function listMediasFormSelect(array $listMediasForUser): array
    {
        $results = [];
        
        foreach($listMediasForUser as $media){
            $results[$media->getId()] = $media->getPath(); 
        }

        return $results;
    }

    // methode pour recuperer les id (ayant un statut actif) de la function getListMediasForPost(Post $post) de cette classe
    public function getIdOftListMediasActifForPost(int $idPost): array
    {
        $medias = $this->getListMediasForPost($idPost);
    
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

    // pour changer le post_id d'un media
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