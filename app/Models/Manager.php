<?php
namespace App\Models;

use PDO;

/**
 * Manager
 * 
 * manages access to the database
 */
class Manager // connection a la base de donnee
{    
    
    // pour le traitement des fichiers uploader
    private $validExtensionsFileUploader = [
        ['image', 'jpeg', 'gif', 'png','JPG','jpg'], //[type de fichier, extensions de fichiers valide pour ce type de fichier]
        ['video', 'youtube', 'vimeo', 'netfif'],
        ['document', 'txt', 'pdf','xls']
    ];

    /**
     * Method dbConnect
     *
     * connection to the database
     * 
     * @return PDO connection to the database
     */
    protected function dbConnect() : PDO
    {
        // NE PAS OUBLIER "charset=utf8" POUR REGLER LES PROBLEME D ENCODAGE
        $db = new PDO('mysql:host=localhost;dbname=p5_ocs_blog_php; charset=utf8', 'root', '');
        return $db;
    }

    // verifie que l'extension d un fichier (uploadé) appartient bien au tableau des extensions de fichiers autorisé (private $validExtensionsFileUploader) => pour le traitement des fichiers uploader
    protected function validExtension(array $file, String $fileType){
        $trouver = false;

        $fileName = $file ['name'];
        $fileInfos = pathinfo($fileName);
        $extension_upload = $fileInfos['extension'];
        
        foreach($this->validExtensionsFileUploader as $infos){    
            if($infos[0] == $fileType){
                foreach ($infos as $valeur){  
                    if($valeur == $extension_upload){
                        $trouver = true;
                        break;	//pour sortir de la boucle " foreach ($infos as $valeur)"
                    }
                }
            }
    
            if($trouver == true){ 
                break;	//pour sortir de la boucle "foreach($this->validExtensionsFileUploader as $infos)"
            }
        }
    
        return $trouver;	
    }
    
    // function pour valider un fichier qui vient d etre uploader pour qu'il puisse etre ou non enregistrer en bdd
    protected function validateFileForUpload(array $file, String $fileType, int $maxFileSize) {
        if($file ['size'] <= $maxFileSize and $this->validExtension($file, $fileType)){
            return true;
        } else {
            return false;
        }
    }
    
    // function generique pour enregistrer un fichier (pour nous un media)
    public function uploadFile(array $file, String $storagePath, String $newNameUploaderFile){
        $fileName = $file ['name'];
        $fileInfos = pathinfo($fileName);
        $extension_upload = $fileInfos['extension'];
        
        $from = $file ['tmp_name']; //chemin temporaire de stockage du fichier uploader + son nom
        $to = $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de destination du fichier uploader + son nom (on renomme le fichier avec $newNameUploaderFile eton rajoute son extension avec $extension_upload)
        
        move_uploaded_file( $from, $to);
        return $to;
    }
 
}