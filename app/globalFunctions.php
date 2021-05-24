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

// envoie d'email
function sendEmail(String $recipientEmail, String $title, String $message){
    $senderEmail = 'From: '.searchDatasFile('email')[1]; //chemin de stockage du fichier uploader (voir fichier globalFunctions.php)
    mail($recipientEmail, $title, $message, $senderEmail);
}

function sendEmailHtml(String$name, String$email, String $message, $emailFrom, $emailTo){
    // Sujet
    $subject = 'BlogNico message de '.$name;

    // message
    $message = '
    <html>
        <head>
            <title>BlogNico message de '.$name.'</title>
        </head>
        <body>
            <p>Message de '.$name.'</p>
            <p>Son adresse email : '.$email.'</p>
            <p>Voici son message :</p>
            <p>'
                .$message.
            '</p>
        </body>
    </html>
    ';

    // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
    $headers[] = 'From: '.$emailFrom;
    $to  = $emailTo;
    
    // Envoi
    mail($to, $subject, $message, implode("\r\n", $headers));
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
        $_SESSION['flash'] = array(
            'errors'=> $errors,
            'type' => $type
        );
            
    }

    function getFalshErrors(){
        if(isset($_SESSION['flash'])){
            echo('<h4> message info </h4>');
            foreach($_SESSION['flash']['errors'] as $error){
                echo('<div class="alert alert-'.$_SESSION['flash']['type'].'">
                    '.$error.'
                    </div>
                ');     
            }
            unset($_SESSION['flash']);
        }
    }  