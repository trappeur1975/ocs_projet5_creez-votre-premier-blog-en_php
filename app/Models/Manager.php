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

    // function pour valider un fichier qui vient d etre uploader pour qu'il puisse etre ou non enregistrer en bdd
    private function validateFileForUpload(array $file, array $extensionsAllowed, int $maxFileSize) {
        $fileName = $file ['name'];
        $fileInfos = pathinfo($fileName);
        if ($file ['size'] <= $maxFileSize and $extension_upload = $fileInfos['extension']){
            return true;
        } else {
            return false;
        }
    }

    // function generique pour enregistrer un fichier (pour nous un media)
    function uploadFile(array $file, String $StoragePath, array $extensionsAllowed, int $maxFileSize, String $newNameUploaderFile){
        if(isset($file) AND $file ['error'] == 0 AND $this->validateFileForUpload($file, $extensionsAllowed, $maxFileSize)){  // si un fichier a ete telecharger, qu il n'y a pas eu d'erreur et qu'il a ete declaré valide pour etre enregistrer en bdd  
            $fileName = $file ['name'];
            $fileInfos = pathinfo($fileName);
            $extension_upload = $fileInfos['extension'];
            
            $from = $file ['tmp_name']; //chemin temporaire de stockage du fichier uploader + son nom
            $to = $StoragePath.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de destination du fichier uploader + son nom (on renomme le fichier avec $newNameUploaderFile eton rajoute son extension avec $extension_upload)
            
            move_uploaded_file( $from, $to);
            return $to;
        }
    }
    
    
    // // function generique pour enregistrer un fichier (pour nous un media)
    // public function uploadFile(array $file, String $StoragePath, array $extensionsAllowed, int $maxFileSize, String $newNameUploaderFile){
    //     if (isset($file) AND $file ['error'] == 0){  // si un fichier a ete telecharger et qu il n'y a pas eu d'erreur
    //         if($file ['size'] <= $maxFileSize){ // si le fichier n'est pas trop gros
    //             $fileName = $file ['name'];
    //             $fileInfos = pathinfo($fileName);
    //             $extension_upload = $fileInfos['extension'];
                
    //             if (in_array($extension_upload, $extensionsAllowed)){ // si l'extension est autorisée on peut valider le fichier et le stocker définitivement
    //                 $from = $file ['tmp_name']; //chemin temporaire de stockage du fichier uploader + son nom
    //                 $to = $StoragePath.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de destination du fichier uploader + son nom (on renomme le fichier avec $newNameUploaderFile eton rajoute son extension avec $extension_upload)
                    
    //                 move_uploaded_file( $from, $to);
    //             }
    //         }
    //     }
    // }


}