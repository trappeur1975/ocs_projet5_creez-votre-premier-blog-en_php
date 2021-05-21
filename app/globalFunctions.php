<?php

const CONFIGFILE = '../app/configSite.txt';

/**
 * apply the nl2br and htmlentities function on content to secure content from outside 
 *
 * @param string $content content a priori coming from outside (form, database, ...) 
 *
 * @return void
 */
function formatHtml(string $content) {
    return nl2br(htmlentities($content));
}

// gestion de fichier
    // cherche a recuperer (sous forme d un tableau) les données d une ligne dans un fichier si elle existe
    function searchDatasFile(String $typeDatasSought){
        $file = fopen(CONFIGFILE, 'r');
        $datasFind = [];
        while(!feof($file)){
            $line = fgets($file);
            $datas = explode('|', $line);
            if($datas[0] == $typeDatasSought){
                $datasFind = $datas;
                break;	//to exit the foreach loop 
            }
        }
        fclose($file);
        
        return $datasFind;
    }

    //cherche dans un tableau de donnée ($datas) si une donnée existe (dataSought)
    function validateData(array $datas, String $dataSought){
        $find = false;
        foreach ($datas as $data){    
            if($data == $dataSought){
                $find = true;
                break;	//to exit the foreach loop 
            }
        }      
        return $find;
    }

    //cherche si un des mots se trouvant dans un tableau ($datas) existe bien dans une chaine de caractére
    function validateWordInString(array $datas, String $stringCaractere){  
        $find = false;
        foreach($datas as $data){
            if(strpos($stringCaractere, $data)){
                $find = true;
                break;
            }
        }
        return $find;
    }

//------------message flash---------------
    function setFlashErrors(array $errors, $type='danger'){
        $_SESSION['errors'] = array(
            'errors'=> $errors,
            'type' => $type
        );
            
    }

    function getFalshErrors(){
        if(isset($_SESSION['errors'])){
            foreach($_SESSION['errors']['errors'] as $error){
                echo('<div class="alert alert-'.$_SESSION['errors']['type'].'">
                    '.$error.'
                    </div>
                ');     
            }
            unset($_SESSION['errors']);
        }
    }


    // creer un message flash
    // $type mettre le type de bootstrap (success, warning, ou danger par exemple)
    // function setFlashMessage($message, $type='danger'){
    //     $_SESSION['flash'] = array(
    //         'message'=> $message,
    //         'type' => $type
    //     );
            
    // }

    // afficher un message flash
    // function getFlashMessage(){
    //     if(isset($_SESSION['flash'])){
    //         echo('<div class="alert alert-'.$_SESSION['flash']['type'].'">
    //                 <!-- <a class="close">fermer</a> -->
    //                 '.$_SESSION['flash']['message'].'
    //             </div>
    //         ');
    //         unset($_SESSION['flash']);
    //     }
    // }

    