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

/**
 * Method sendEmail
 *
 * @param String $recipientEmail
 * @param String $title
 * @param String $message
 *
 * @return void
 */
function sendEmail(String $recipientEmail, String $title, String $message)
{
    $senderEmail = 'From: '.searchDatasFile('email')[1]; // storage path of the uploader file (see globalFunctions.php file) 
    mail($recipientEmail, $title, $message, $senderEmail);
}

/**
 * Method sendEmailHtml
 *
 * @param String $name $name
 * @param String $email $email
 * @param String $message
 * @param String $emailFrom
 * @param String $emailTo
 *
 * @return void
 */
function sendEmailHtml(String $name, String $email, String $message, String $emailFrom, String $emailTo)
{
    // Subject
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

    // To send an HTML mail, the Content-type header must be defined 
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
    $headers[] = 'From: '.$emailFrom;
    $to  = $emailTo;
    
    // Sending 
    mail($to, $subject, $message, implode("\r\n", $headers));
}


// file management 
    /**
     * Method searchDatasFile seeks to recover (in the form of an array) the data of a line in a file if it exists   
     *
     * @param String $typeDatasSought
     *
     * @return array
     */
    function searchDatasFile(String $typeDatasSought)
    {
        $file = fopen(CONFIGFILE, 'r');
        $datasFind = [];
        while(!feof($file)){
            $line = fgets($file);
            $datas = explode('|', $line);
            if ($datas[0] == $typeDatasSought) {
                $datasFind = $datas;
                break;	//to exit the foreach loop 
            }
        }
        fclose($file);
        
        return $datasFind;
    }

    /**
     * Method validateData search in a data table ($ datas) if a data exists (dataSought) 
     *
     * @param array $datas
     * @param String $dataSought
     *
     * @return bool
     */
    function validateData(array $datas, String $dataSought)
    {
        $find = false;
        foreach ($datas as $data){    
            if ($data == $dataSought) {
                $find = true;
                break;	//to exit the foreach loop 
            }
        }      
        return $find;
    }

    //searches if one of the words found in an array ($ datas) does indeed exist in a character string    
    /**
     * Method validateWordInString
     *
     * @param array $data
     * @param String $stringCaractere
     *
     * @return void
     */
    function validateWordInString(array $datas, String $stringCaractere){  
        $find = false;
        foreach($datas as $data){
            if (strpos($stringCaractere, $data)) {
                $find = true;
                break;
            }
        }
        return $find;
    }

//flash message    
    /**
     * Method setFlashErrors
     *
     * @param array $errors
     * @param $type='danger'
     *
     * @return void
     */
    function setFlashErrors(array $errors, $type='danger'){
        $_SESSION['flash'] = array(
            'errors'=> $errors,
            'type' => $type
        );
            
    }
    
    /**
     * Method getFalshErrors
     *
     * @return void
     */
    function getFalshErrors(){
        if (isset($_SESSION['flash'])) {
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