<?php
use App\Entities\Auth;
use App\Entities\Form;
use App\Entities\User;
use App\Entities\Media;
use App\Entities\Comment;
use App\Models\PostManager;
use App\Models\UserManager;
use App\Models\MediaManager;
use App\Models\CommentManager;
use App\Entities\SocialNetwork;
use App\Models\SocialNetworkManager;

// SITE
    /**
     * function use for road http://localhost:8000
     * will display the view frontHomeView.php  
     */
    function frontHome()
    {
        $userLogged = Auth::sessionStart();

        $errors = [];

        // user
        $userManager = new UserManager(); // Création de l'objet manager de user

        try{
            $user = $userManager->getUser(1); // user nicolas tchenio
        } catch (Exception $e) {    //dans le cas ou l'on demande une ressource qui n'existe pas (ici un id de post qui n'existe pas)
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        // media
        $mediaManager = new MediaManager();
        $listMediasForUser = $mediaManager->getListMediasForUser($user->getId());
        $logoUser = $mediaManager->getListMediasForUserForType($listMediasForUser, [2])[0];

        // traitement server et affichage des retours d'infos 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a user) has been made
            //test de validation des champs du formulaire
                
                if(empty($_POST['name']) OR mb_strlen($_POST['name'])<=3){
                    $errors[] = 'Le champ name ne peut être vide et doit contenir plus de 3 caracteres';
                }
                if(empty($_POST['email']) OR strpos($_POST['email'], '@') === false){
                    $errors[] = 'Le champ email ne peut être vide ou l\'ecriture de votre adresse email est incorrect';
                }
                if(empty($_POST['message']) OR mb_strlen($_POST['message'])<=3){
                    $errors[] = 'Le champ lastName ne peut être vide et doit contenir plus de 3 caracteres';
                }
        
            if(empty($errors)){ //on envoie le email
                // sendEmail('adminBlogNico@hotmail.com', 'BlogNico message de '.$_POST['name'], 'message de '.$_POST['name'].'\r\n adresse email '.$_POST['email'].'\r\n avec le message suivant :'.$_POST['message']);
                $emailFrom = searchDatasFile('email')[2];
                $emailTo = searchDatasFile('email')[1];
                sendEmailHtml($_POST['name'], $_POST['email'], $_POST['message'], $emailFrom, $emailTo);
               
                header('Location: /?SendEmail=true');
                return http_response_code(302);
            }else{  
                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
                
                header('Location: /?SendEmail=false');
                return http_response_code(302);
            }
            
        }
        
        require('../app/Views/frontViews/frontHomeView.php');

    }

    /**
     * function use for road http://localhost:8000/listposts
     * will display the view frontListPostsView.php  
     */
    function listPosts()
    {
        $userLogged = Auth::sessionStart();

        
        $postManager = new PostManager();
        
        $listPosts = $postManager->getListPosts();
        require('../app/Views/frontViews/frontListPostsView.php');
    }

    /**
     * function use for road http://localhost:8000/post/1 ou http://localhost:8000/post/2 ou ....
     * will display the view frontPostView.php  
     */
    function post($id)
    { 
        $userLogged = Auth::sessionStart();
      
        $errors = [];

        // post
        $postManager = new PostManager(); // Création de l'objet manger de post

        try{
            $post = $postManager->getPost($id);
        } catch (Exception $e) {    //dans le cas ou l'on demande une ressource qui n'existe pas (ici un id de post qui n'existe pas)
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        // user
        $userManager = new UserManager();
        $userPost = $userManager->getUser($post->getUser_id());
        
        // media
        $mediaManager= new MediaManager();
        $listMediasForPost = $mediaManager->getListMediasForPost($id);

        // comment
        $commentManager = new CommentManager();
        $listCommentsForPost = $commentManager->listCommentsNotNullForPost($id);
        // $listCommentsForPost = $commentManager->getListCommentsForPost($id);
    
        // pour creer un nouveau commentaire pour un post
        $comment = new Comment();
        $formComment = new Form($comment);

        // traitement server et affichage des retours d'infos 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a post) has been made
            
            //test de validation des champs du formulaire
                if(empty($_POST['comment']) OR mb_strlen($_POST['comment'])<=3){
                    $errors[] = 'Le champ commentaire ne peut être vide et doit contenir plus de 3 caracteres';
                }
            // enregistrement des infos
            if(empty($errors)){
                Auth::check(['administrateur','abonner']);    

                $dateTime = new Datetime();
                $date = $dateTime->format('Y-m-d H:i:s');
                $validate = null;
                if($userManager->getUserSatus($_SESSION['connection'])['status'] === 'administrateur'){ //pour valider automatiquement le commentaire si le commentateur a un status "administrateur"
                    $validate = $date;
                }

                // enregistrement en bdd du comment    
                $comment
                    ->setComment($_POST['comment'])
                    ->setDateCompletion($date)
                    ->setValidate($validate)
                    ->setUser_id($_SESSION['connection'])
                    ->setPost_id($post->getId())
                    ;
                
                try{
                    $commentManager->addComment($comment);// add the comment to the database and get the last id of the comments in the database via the return of the function
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)

                header('Location: /post/'.$id.'?createdComment=true');
                return http_response_code(302);
 
            }else{
                
                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
                header('Location: /post/'.$id.'?createdComment=false');
                return http_response_code(302);
            }
        }
        
        require('../app/Views/frontViews/frontPostView.php');
    }

// USER
    /**
     * function use for road  http://localhost:8000/userFrontDashboard/1 ou http://localhost:8000/userFrontDashboard/2 ou ....
     * will display the view frontUserFrontDashboardView.php  
     */
    function userFrontDashboard($id)
    {
        $userLogged = Auth::check(['abonner']);

        $errors = [];

        // users
        $userManager = new UserManager();
        try{
            $user = $userManager->getUser($id); //user of dashboard
        } catch (Exception $e) {    //dans le cas ou l'on demande une ressource qui n'existe pas (ici un id du user qui n'existe pas)
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        $originalPassword = $user->getPassword();

        if($user->getId() === $_SESSION['connection']){ //on verifier que le dashboard que le user souhaite visualiser est bien le sien

            // EDIT DU USER DASHBOARD
                // userDasboard 
                $formUser = new Form($user, true);
            
                // media (logo)
                $mediaManager = new MediaManager();
                    
                $listMediasForUser = $mediaManager->getListMediasForUser($id);
                $listIdsMediaType = [2];  //logo
                $listLogos = $mediaManager->getListMediasForUserForType($listMediasForUser, $listIdsMediaType); // pour recuperer le logo du user

                if(!empty($listLogos)){
                    $logoUser = $listLogos[0];
                    $formMediaLogoUser = new Form($logoUser);  //pour avoir dans le champ input pour uploader un logo
                }

                $mediaUploadLogo = new Media();
                $formMediaUploadLogo = new Form($mediaUploadLogo);  //pour avoir dans le champ input pour uploader un logo
                    
                // socialNetwork
                $socialNetworkManager = new SocialNetworkManager();
                $socialNetwork = new SocialNetwork();
                $formSocialNetwork = new Form($socialNetwork);

                $listSocialNetworksForUser = $socialNetworkManager->getListSocialNetworksForUser($id);
                $listSocialNetworksForUserForSelect =  $socialNetworkManager->listSocialNetworksFormSelect($listSocialNetworksForUser); // on affiche la liste des social network de l'user 

                if(!empty($listSocialNetworksForUser)){
                    $socialNetworkForSelect = $listSocialNetworksForUser[0];
                    $formSocialNetworkSelect = new Form($socialNetworkForSelect);
                }
            
            // LES COMMENTAIRES DU USER
                $commentManager = new CommentManager();
                $listCommentsForUser = $commentManager->listCommentsForUser($id);


            // traitement server et affichage des retours d'infos 
            if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a user) has been made

                //test de validation des champs du formulaire
                    if(empty($_POST['firstName']) OR mb_strlen($_POST['firstName'])<=3){
                        $errors[] = 'Le champ firstName ne peut être vide et doit contenir plus de 3 caracteres';
                    }
                    if(empty($_POST['lastName']) OR mb_strlen($_POST['lastName'])<=3){
                        $errors[] = 'Le champ lastName ne peut être vide et doit contenir plus de 3 caracteres';
                    }
    
                    if(empty($_POST['email']) OR strpos($_POST['email'], '@') === false){
                        $errors[] = 'Le champ email ne peut être vide ou l\'ecriture de votre adresse email est incorrect';
                    }
                    $idUserIidenticalData1 = $userManager->identicalDataSearch('email', $_POST['email']);
                    if(!is_null($idUserIidenticalData1) AND $idUserIidenticalData1 != $id) {
                        $errors[] = 'Votre email a été déjà utilisé, vous devez en indiquer un autre';
                    }
                
                    if(empty($_POST['login']) OR mb_strlen($_POST['login'])<=3){
                        $errors[] = 'Le champ login ne peut être vide et doit contenir plus de 3 caracteres';
                    }
                    
                    $idUserIidenticalData2 = $userManager->identicalDataSearch('login', $_POST['login']);
                    if(!is_null($idUserIidenticalData2) AND $idUserIidenticalData2 != $id) {
                        $errors[] = 'Votre login a été déjà utilisé, vous devez en indiquer un autre';
                    }
    
                    if(empty($_POST['password']) OR mb_strlen($_POST['password'])<=3){
                        $errors[] = 'Le champ password ne peut être vide et doit contenir plus de 3 caracteres';
                    }
                
                // enregistrement des infos
                if(empty($errors)){
                
                    // on re-hache le mot de passe que si celui-ci a ete modifier par le user
                    if($originalPassword !== $_POST['password']){
                        $hashPsswords = hash('md5', $_POST['password']);
                    } else {
                        $hashPsswords = $_POST['password'];
                    }
                    
                    // enregistrement en bdd du user
                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        ->setSlogan($_POST['slogan'])
                        ->setLogin($_POST['login'])
                        ->setPassword($hashPsswords);
                        // ->setPassword($_POST['password']);
                    
                    try{
                        $userManager->updateUser($user);
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    // enregistrement en bdd du media logo et du fichier uploader sur le server dans le dossier media
                    if(isset($_FILES['mediaUploadLogo']) AND $_FILES['mediaUploadLogo']['error']== 0){
                        
                        // variables infos
                        $idMediaType = 2;   //logo

                        $file = $_FILES['mediaUploadLogo']; //fichier uploader
                        $storagePath = searchDatasFile('imageStoragePath')[1]; //chemin de stockage du fichier uploader (voir fichier globalFunctions.php)         
                        $name = 'mediaLogo-'.pathinfo($file['name'])['filename'].'-'; 
                        $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique

                        $extension_upload = pathinfo($file['name'])['extension']; //pour recuperer l'extension du fichier uploader   
                        $pathFile =  $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader
                    
                        // on supprime en base de donnée ainsi que sur le server dans le dossier media l'ancien logo de l'user    
                        $listMediasForUser = $mediaManager->getListMediasForUser($id);
                        $listLogosDelete = $mediaManager->getListMediasForUserForType($listMediasForUser, $listIdsMediaType);   // on recuperer la liste des logos du user
                    
                        if(!empty($listLogosDelete)){
                            foreach($listLogosDelete as $logo){
                                try{
                                    unlink($logo->getPath());  //suppression des media sur le serveur dans le dossier media
                                    $mediaManager->deleteMedia($logo->getId());    //suppression dans la base de donnée
                                } catch (Exception $e) {
                                    $errors[] = $e->getMessage();
                                }   
                            }
                        }

                        // enregistrement en bdd du nouveau LOGO et et de son fichier uploader sur le server dans le dossier media
                        $mediaUploadLogo
                            ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                            ->setAlt($_POST['altFileMediaLogo'])
                            ->setStatutActif(1)
                            ->setMediaType_id($idMediaType)
                            ->setUser_id($id)
                            // ->setUser_id($user->getId())
                            ;
                        
                        try{
                            $mediaManager->addMediaImage($mediaUploadLogo, CONFIGFILE, $file); //adding the media to the database and recovery via the id function of the last media in the database
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        } 
                    }

                    // enregistrement en bdd socialNetwork des modifications qui ont etait apporté dans l'editUser()   
                        // supression du ou des socialNetwork de l'user
                        if(!empty($_POST['socialNetworksUser'])){ 
                            foreach($_POST['socialNetworksUser'] as $idSsocialNetwork){
                                try
                                {
                                    $socialNetworkManager->deleteSocialNetwork($idSsocialNetwork);
                                }
                                catch (Exception $e)
                                {
                                    $errors[] = $e->getMessage();
                                } 
                            }
                        }
                        
                        // ajout d'un socialNetwork a l'user
                        if(!empty($_POST['socialNetwork'])){
                            $socialNetwork
                                ->setUrl($_POST['socialNetwork'])
                                ->setUser_id($id)
                                ;
                            
                            try
                            {
                                $socialNetworkManager->addSocialNetwork($socialNetwork);
                            }
                            catch (Exception $e)
                            {
                                $errors[] = $e->getMessage();
                            }
                        }
                    
                    setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)

                    header('Location: /userFrontDashboard/'.$id.'?successEditUser=true');
                    return http_response_code(302);

                }else{
                    setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
                    
                    header('Location: /userFrontDashboard/'.$id.'?successEditUser=false');
                    return http_response_code(302);
                }
            } 
            require('../app/Views/frontViews/frontUserFrontDashboardView.php');
        }else {
            $errors[] = 'impossible d\'afficher ce dashboard, il ne vous appartient pas';
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            header('Location: /listposts');
            return http_response_code(302);
        }
    }

    /**
     * function use for road http://localhost:8000/createUserFront
     * will display the view createUserFront.php  
     */
    function createUserFront()
    {
        // creation d une session pour pouvoir notamment afficher les message flash
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        // user
        $user = new User();
        $formUser = new Form($user);

        $userManager = new UserManager();
     
        // media (logo)
        $mediaManager = new MediaManager();
        $mediaUploadLogo = new Media(); //pour avoir dans le champ input pour uploader un logo (par defaut toute les variables de cette entité Media sont a "null" )
        $formMediaUploadLogo = new Form($mediaUploadLogo);

        // socialNetwork
        $socialNetwork = new SocialNetwork();
        $socialNetworkManager = new SocialNetworkManager();
        $formSocialNetwork = new Form($socialNetwork);

        // traitement server et affichage des retours d'infos 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a user) has been made
            
            //for data validation
            $errors = [];
            
            //test de validation des champs du formulaire
                if(empty($_POST['firstName']) OR mb_strlen($_POST['firstName'])<=3){
                    $errors[] = 'Le champ firstName ne peut être vide et doit contenir plus de 3 caracteres';
                }
                if(empty($_POST['lastName']) OR mb_strlen($_POST['lastName'])<=3){
                    $errors[] = 'Le champ lastName ne peut être vide et doit contenir plus de 3 caracteres';
                }

                if(empty($_POST['email']) OR strpos($_POST['email'], '@') === false){
                    $errors[] = 'Le champ email ne peut être vide ou l\'ecriture de votre adresse email est incorrect';
                }
                $idUserIidenticalData1 = $userManager->identicalDataSearch('email', $_POST['email']);
                if(!is_null($idUserIidenticalData1)) {
                    $errors[] = 'Votre email a été déjà utilisé, vous devez en indiquer un autre';
                }
            
                if(empty($_POST['login']) OR mb_strlen($_POST['login'])<=3){
                    $errors[] = 'Le champ login ne peut être vide et doit contenir plus de 3 caracteres';
                }
                
                $idUserIidenticalData2 = $userManager->identicalDataSearch('login', $_POST['login']);
                if(!is_null($idUserIidenticalData2)) {
                    $errors[] = 'Votre login a été déjà utilisé, vous devez en indiquer un autre';
                }

                if(empty($_POST['password']) OR mb_strlen($_POST['password'])<=3){
                    $errors[] = 'Le champ password ne peut être vide et doit contenir plus de 3 caracteres';
                }
            
            if(empty($errors)){
                
                // on hache le mot de passe
                $hashPsswords = hash('md5', $_POST['password']);

                // enregistrement en bdd du user
                $user
                    ->setFirstName($_POST['firstName'])
                    ->setLastName($_POST['lastName'])
                    ->setEmail($_POST['email'])
                    ->setSlogan($_POST['slogan'])
                    ->setLogin($_POST['login'])
                    ->setPassword($hashPsswords)
                    // ->setPassword($_POST['password']) 
                    ->setUserType_id(1); //par défaut c est un user de type "abonner"
                
                try{
                    $lastRecordingUser = $userManager->addUser($user);// add the user to the database and get the last id of the users in the database via the return of the function
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

                // enregistrement en bdd du media logo et du fichier uploader sur le server dans le dossier media
                if(isset($_FILES['mediaUploadLogo']) AND $_FILES['mediaUploadLogo']['error']== 0){
                    
                    // variables infos
                    $idMediaType = 2;   //logo

                    $file = $_FILES['mediaUploadLogo']; //fichier uploader
                    $storagePath = searchDatasFile('imageStoragePath')[1]; //chemin de stockage du fichier uploader (voir fichier globalFunctions.php)         
                    $name = 'mediaLogo-'.pathinfo($file['name'])['filename'].'-'; 
                    $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique

                    $extension_upload = pathinfo($file['name'])['extension']; //pour recuperer l'extension du fichier uploader   
                    $pathFile =  $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader

                    // enregistrement en bdd du media LOGO
                    $mediaUploadLogo
                        ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                        ->setAlt($_POST['altFileMediaLogo'])
                        ->setStatutActif(1)
                        ->setMediaType_id($idMediaType)
                        ->setUser_id($lastRecordingUser)
                        ;
                        
                    try{
                        $mediaManager->addMediaImage($mediaUploadLogo, CONFIGFILE, $file); //adding the media to the database and recovery via the id function of the last media in the database
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    } 
                }
                
                // enregistrement en bdd du socialNetwork
                if(!empty($_POST['socialNetwork'])){
                    
                    $socialNetwork
                        ->setUrl($_POST['socialNetwork'])
                        ->setUser_id($lastRecordingUser)
                        ;

                    try
                    {
                        $socialNetworkManager->addSocialNetwork($socialNetwork);
                    }
                    catch (Exception $e)
                    {
                        $errors[] = $e->getMessage();
                    }
                }
                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
                
                sendEmail('adminComptePerso@hotmail.com', 'nouveau compte user creer', 'un nouveau compte user a ete enregiste sur votre blog et est en attente de validation de votre part');
                sendEmail($user->getEmail(), 'Votre compte sur BlogNico', 'Votre compte user a bien ete enregistre sur le BlogNico et est en attente de validation de la part de l\'administrateur du site');

                header('Location: /createUserFront?createdUser=true');
                return http_response_code(302);

            }else{  
                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
                
                header('Location: /createUserFront?createdUser=false');
                return http_response_code(302);
            }
        }
        
        require('../app/Views/frontViews/createUserFront.php');
    }

    /**
     * function use for road http://localhost:8000/deleteUserFront/1 ou http://localhost:8000/deleteUserFront/2 ou ....
     * 
     */
    function deleteUserFront($id){

        $userLogged = Auth::check(['abonner']);

        $errors = [];

        // users
        $userManager = new UserManager();
        try{
            $user = $userManager->getUser($id); //user of dashboard
        } catch (Exception $e) {    //dans le cas ou l'on demande une ressource qui n'existe pas (ici un id du user qui n'existe pas)
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        if($user->getId() === $_SESSION['connection']){ //on verifier que le user que l on souhaite supprimer correspond bien au user connecté sur le site
            
            // suppression de la base de donnee de tout les commentaires de l'user
            $commentManager = new CommentManager();
            $listCommentsDelete = $commentManager->listCommentsForUser($id);

            if($listCommentsDelete !== []){
                foreach($listCommentsDelete as $comment){
                    try{
                        $commentManager->deleteComment($comment->getId());    //suppression dans la base de donnée
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }  
                }
            }

            // suppression de tout les medias lié a l'user (les logos, image desactiver, ...) pour les supprimer du server (dossier media) et de la base de donnée
            $mediaManager = new MediaManager();
            $listMedias = $mediaManager->getListMediasForUser($id); // on recuperer la liste des logos du user

            if(!empty($listMedias)){
                foreach($listMedias as $media){
                    try{
                        unlink($media->getPath());  //suppression des media sur le serveur dans le dossier media
                        $mediaManager->deleteMedia($media->getId());    //suppression dans la base de donnée
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }   
                }
            }

            // suppression de la base de donnee de tout les socialNetworks de l'user
            $socialNetworkManager = new SocialNetworkManager();
            $listSocialNetworksForUserDelete = $socialNetworkManager->getListSocialNetworksForUser($id);
            
            if(!empty($listSocialNetworksForUserDelete)){
                foreach($listSocialNetworksForUserDelete as $socialnetwork){
                    try
                    {
                        $socialNetworkManager->deleteSocialNetwork($socialnetwork->getId());    //suppression dans la base de donnée
                    }
                        catch (Exception $e)
                    {
                        $errors[] = $e->getMessage();
                    } 
                }
            }

            //supression de tout les post lier a l'user ATTENTION FONCTIONNALITE NON PREVU PAR LE SUJET MAIS RENDU POSSIBLE PAR MOI CAR ADMINISTRATEUR (en backend) PEUT ATTRIBUER UN POST (avec ses medias et commentaire) A UN USER
            $postManager = new PostManager();
            $listPostsForUser = $postManager->getListPostsForUser($id);

            if(!empty($listPostsForUser)){
                foreach($listPostsForUser as $post){    // suppression de tout les post (et des medias que leurs sont associés) de l user
                    
                    // on supprime les medias lier au post (si il y en a)
                    $listMediasDelete =  $mediaManager->getListMediasForPost($post->getId());// on recupere la liste des media pour ce $post

                    if($listMediasDelete !== []){
                        foreach($listMediasDelete as $media){
                            try{
                                unlink($media->getPath());  //suppression des media sur le serveur dans le dossier media
                                $mediaManager->deleteMedia($media->getId());    //suppression dans la base de donnée
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }  
                        }
                    }

                    // on supprime les commentaires lier au post (si il y en a)
                    $listCommentsDelete =  $commentManager->getListCommentsForPost($post->getId());// on recupere la liste des commentaire pour ce $post
                    
                    if($listCommentsDelete !== []){
                        foreach($listCommentsDelete as $comment){
                            try{
                                $commentManager->deleteComment($comment->getId());    //suppression dans la base de donnée
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }  
                        }
                    }

                    // on supprime le post
                    try{
                        $post = $postManager->deletePost($post->getId());
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }  
                }
            }

            // suppression de l'user
            try{
                $user = $userManager->deleteUser($id);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            // session_destroy();
            unset($_SESSION['connection']);
           
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)

            require('../app/Views/frontViews/frontDeleteUserView.php');
        }else {
            $errors[] = 'impossible de supprimer ce user, vous n\'en avait pas le droit';
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            header('Location: /listposts');
            return http_response_code(302);
        }

    }

//COMMENT
    /**
     * function use for road http://localhost:8000/editCommentPostFront/1 ou http://localhost:8000/editCommentPostFront/2 ou ....
     * 
     */
    function editCommentPostFront($id)
    {
        $userLogged = Auth::check(['administrateur','abonner']);

        $errors = [];

        // on edit le commentaire (a travers son formulaire)
        $commentManager = new CommentManager();
        try{
            $comment = $commentManager->getComment($id);
        } catch (Exception $e) {    //dans le cas ou l'on demande une ressource qui n'existe pas (ici un id de comment qui n'existe pas)
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        if($comment->getUser_id() === $_SESSION['connection']){ //on verifier que le commentaire que le user souhaite modifier lui appartient bien

            $formComment = new Form($comment, true);

            if($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a comment) has been made
                
                //test de validation des champs du formulaire
                if(empty($_POST['comment']) OR mb_strlen($_POST['comment'])<=3){
                    $errors[] = 'Le champ commentaire ne peut être vide et doit contenir plus de 3 caracteres';
                }

                // enregistrement des infos
                if(empty($errors)){
                    // enregistrement des modifications du commentaire
                    if (!empty($_POST['comment'])){
                        $comment->setComment($_POST['comment']);
                        
                        try{
                            $commentManager->updateComment($comment);
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        } 
                    }
                    
                    setFlashErrors($errors);

                    header('Location: /post/'.$comment->getPost_id().'?successUploadComment=true');
                    return http_response_code(302);

                }else{
                    setFlashErrors($errors);

                    header('Location: /post/'.$comment->getPost_id().'?successUploadComment=false');
                    return http_response_code(302);
                }
            }
            require('../app/Views/frontViews/frontEditCommentPostView.php');
        }else {
            $errors[] = 'impossible de modifier le commentaire :'.$comment->getId().'par le user :'.$_SESSION['connection'];
            setFlashErrors($errors);

            header('Location: /listposts');
            return http_response_code(302);
        }

    }

    /**
     * function use for road http://localhost:8000/deleteCommentPostFront/1 ou http://localhost:8000/deleteCommentPostFront/2 ou ....
     * 
     */
    function deleteCommentPostFront($id)
    {
        $userLogged = Auth::check(['administrateur','abonner']);

        $errors = [];

        // on supprime le commentaire
        $commentManager = new CommentManager();
        try{
            $comment = $commentManager->getComment($id);
        } catch (Exception $e) {    //dans le cas ou l'on demande une ressource qui n'existe pas (ici un id de comment qui n'existe pas)
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }
        
        if($comment->getUser_id() === $_SESSION['connection']){ //on verifier que le commentaire que le user souhaite modifier lui appartient bien
            try{
                $comment = $commentManager->deleteComment($id);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }  

            require('../app/Views/frontViews/frontDeleteCommentPostView.php');
        }else {
            // throw new Exception('impossible de supprimmer le commentaire :'.$comment->getId().'par le user :'.$_SESSION['connection']);
            $errors[] = 'impossible de supprimer le commentaire :'.$comment->getId().' par le user :'.$_SESSION['connection'];
            setFlashErrors($errors);

            header('Location: /listposts');
            return http_response_code(302);
        }
    }