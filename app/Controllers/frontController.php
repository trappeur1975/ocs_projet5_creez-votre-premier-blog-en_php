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
        $userManager = new UserManager(); // creation of the manager object of user 

        try{
            $user = $userManager->getUser(1); // user nicolas tchenio
        } catch (Exception $e) {    //in the event that we request a resource that does not exist (here a post id that does not exist) 
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        // media
        $mediaManager = new MediaManager();
        $listMediasForUser = $mediaManager->getListMediasForUser($user->getId());
        $logoUser = $mediaManager->getListMediasForUserForType($listMediasForUser, [2])[0];

        // server processing and display of feedbacks 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a user) has been made
            //validation test of the form fields    
                if(empty($_POST['name']) OR mb_strlen($_POST['name'])<=3){
                    $errors[] = 'Le champ name ne peut être vide et doit contenir plus de 3 caracteres';
                }
                if(empty($_POST['email']) OR strpos($_POST['email'], '@') === false){
                    $errors[] = 'Le champ email ne peut être vide ou l\'ecriture de votre adresse email est incorrect';
                }
                if(empty($_POST['message']) OR mb_strlen($_POST['message'])<=3){
                    $errors[] = 'Le champ lastName ne peut être vide et doit contenir plus de 3 caracteres';
                }
        
            if(empty($errors)){ // we send the email 
                $emailFrom = searchDatasFile('email')[2];
                $emailTo = searchDatasFile('email')[1];
                sendEmailHtml($_POST['name'], $_POST['email'], $_POST['message'], $emailFrom, $emailTo);
               
                header('Location: /?SendEmail=true');
                return http_response_code(302);
            }else{  
                setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
                
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
        $postManager = new PostManager(); // create the post eat object 

        try{
            $post = $postManager->getPost($id);
        } catch (Exception $e) {    // in the event that we request a resource that does not exist (here a post id that does not exist) 
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
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
    
        // to create a new comment for a post 
        $comment = new Comment();
        $formComment = new Form($comment);

        // server processing and display of feedbacks  
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a post) has been made
            
            //validation test of the form fields 
                if(empty($_POST['comment']) OR mb_strlen($_POST['comment'])<=3){
                    $errors[] = 'Le champ commentaire ne peut être vide et doit contenir plus de 3 caracteres';
                }
            // info recording 
            if(empty($errors)){
                Auth::check(['administrateur','abonner']);    

                $dateTime = new Datetime();
                $date = $dateTime->format('Y-m-d H:i:s');
                $validate = null;
                if($userManager->getUserSatus($_SESSION['connection'])['status'] === 'administrateur'){ //pour valider automatiquement le commentaire si le commentateur a un status "administrateur"
                    $validate = $date;
                }

                // bdd recording of how     
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

                setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 

                header('Location: /post/'.$id.'?createdComment=true');
                return http_response_code(302);
 
            }else{
                
                setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
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
        } catch (Exception $e) {    // in the event that we request a resource that does not exist (here an id of the user that does not exist) 
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        $originalPassword = $user->getPassword();

        if($user->getId() === $_SESSION['connection']){ // we check that the dashboard that the user wishes to view is indeed his own 

            // EDIT DU USER DASHBOARD
                // userDasboard 
                $formUser = new Form($user, true);
            
                // media (logo)
                $mediaManager = new MediaManager();
                    
                $listMediasForUser = $mediaManager->getListMediasForUser($id);
                $listIdsMediaType = [2];  //logo
                $listLogos = $mediaManager->getListMediasForUserForType($listMediasForUser, $listIdsMediaType); // to retrieve the user's logo 

                if(!empty($listLogos)){
                    $logoUser = $listLogos[0];
                    $formMediaLogoUser = new Form($logoUser);  // to have in the input field to upload a logo 
                }

                $mediaUploadLogo = new Media();
                $formMediaUploadLogo = new Form($mediaUploadLogo);  // to have in the input field to upload a logo 
                    
                // socialNetwork
                $socialNetworkManager = new SocialNetworkManager();
                $socialNetwork = new SocialNetwork();
                $formSocialNetwork = new Form($socialNetwork);

                $listSocialNetworksForUser = $socialNetworkManager->getListSocialNetworksForUser($id);
                $listSocialNetworksForUserForSelect =  $socialNetworkManager->listSocialNetworksFormSelect($listSocialNetworksForUser); // we display the list of social networks of the user  

                if(!empty($listSocialNetworksForUser)){
                    $socialNetworkForSelect = $listSocialNetworksForUser[0];
                    $formSocialNetworkSelect = new Form($socialNetworkForSelect);
                }
            
            // LES COMMENTAIRES DU USER
                $commentManager = new CommentManager();
                $listCommentsForUser = $commentManager->listCommentsForUser($id);


            // server processing and display of feedbacks 
            if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a user) has been made

                //validation test of the form fields 
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
                
                // info recording 
                if(empty($errors)){
                
                    // the password is re-hashed only if it has been modified by the user 
                    if($originalPassword !== $_POST['password']){
                        $hashPsswords = hash('md5', $_POST['password']);
                    } else {
                        $hashPsswords = $_POST['password'];
                    }
                    
                    // user database recording 
                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        ->setSlogan($_POST['slogan'])
                        ->setLogin($_POST['login'])
                        ->setPassword($hashPsswords);
                    
                    try{
                        $userManager->updateUser($user);
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }

                    // bdd recording of the media logo and the uploader file on the server in the media folder 
                    if(isset($_FILES['mediaUploadLogo']) AND $_FILES['mediaUploadLogo']['error']== 0){
                        
                        // info variables 
                        $idMediaType = 2;   //logo

                        $file = $_FILES['mediaUploadLogo']; //file uploader 
                        $storagePath = searchDatasFile('imageStoragePath')[1]; //storage path of the uploader file (see globalFunctions.php file)
                        $name = 'mediaLogo-'.pathinfo($file['name'])['filename'].'-'; 
                        $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + name of the uploader file (without its extension + unique identifier (via uniqid) to have a unique identifier 

                        $extension_upload = pathinfo($file['name'])['extension']; // to retrieve the extension of the uploader file   
                        $pathFile =  $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); // storage path with new name of the media uploader 
                    
                        // we delete in the database as well as on the server in the media folder the old logo of the user    
                        $listMediasForUser = $mediaManager->getListMediasForUser($id);
                        $listLogosDelete = $mediaManager->getListMediasForUserForType($listMediasForUser, $listIdsMediaType);   // we retrieve the list of user logos 
                    
                        if(!empty($listLogosDelete)){
                            foreach($listLogosDelete as $logo){
                                try{
                                    unlink($logo->getPath());  // delete media on the server in the media folder 
                                    $mediaManager->deleteMedia($logo->getId());    // deletion from the database 
                                } catch (Exception $e) {
                                    $errors[] = $e->getMessage();
                                }   
                            }
                        }

                        // bdd recording of the new LOGO and its uploader file on the server in the media folder 
                        $mediaUploadLogo
                            ->setPath($pathFile)
                            ->setAlt($_POST['altFileMediaLogo'])
                            ->setStatutActif(1)
                            ->setMediaType_id($idMediaType)
                            ->setUser_id($id)
                            ;
                        
                        try{
                            $mediaManager->addMediaImage($mediaUploadLogo, CONFIGFILE, $file); //adding the media to the database and recovery via the id function of the last media in the database
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        } 
                    }

                    // saving in socialNetwork database of changes made in editUser ()  
                        // deletion of the user's social network (s) 
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
                        
                        // adding a socialNetwork to the user 
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
                    
                    setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 

                    header('Location: /userFrontDashboard/'.$id.'?successEditUser=true');
                    return http_response_code(302);

                }else{
                    setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
                    
                    header('Location: /userFrontDashboard/'.$id.'?successEditUser=false');
                    return http_response_code(302);
                }
            } 
            require('../app/Views/frontViews/frontUserFrontDashboardView.php');
        }else {
            $errors[] = 'impossible d\'afficher ce dashboard, il ne vous appartient pas';
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
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
        // creation of a session to be able in particular to display flash messages 
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        // user
        $user = new User();
        $formUser = new Form($user);

        $userManager = new UserManager();
     
        // media (logo)
        $mediaManager = new MediaManager();
        $mediaUploadLogo = new Media(); //to have in the input field to upload a logo (by default all the variables of this Media entity are "null") 
        $formMediaUploadLogo = new Form($mediaUploadLogo);

        // socialNetwork
        $socialNetwork = new SocialNetwork();
        $socialNetworkManager = new SocialNetworkManager();
        $formSocialNetwork = new Form($socialNetwork);

        // server processing and display of feedbacks 
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
     * will display the view frontDeleteUserView.php
     */
    function deleteUserFront($id){

        $userLogged = Auth::check(['abonner']);

        $errors = [];

        // users
        $userManager = new UserManager();
        try{
            $user = $userManager->getUser($id); //user of dashboard
        } catch (Exception $e) {    // in the event that we request a resource that does not exist (here an id of the user that does not exist) 
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        if($user->getId() === $_SESSION['connection']){ // we check that the user we want to delete corresponds to the user connected to the site 
            
            // deletion of all user comments from the database 
            $commentManager = new CommentManager();
            $listCommentsDelete = $commentManager->listCommentsForUser($id);

            if($listCommentsDelete !== []){
                foreach($listCommentsDelete as $comment){
                    try{
                        $commentManager->deleteComment($comment->getId());    // deletion from the database
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }  
                }
            }

            // deletion of all user-related media (logos, deactivate image, ...) to delete them from the server (media folder) and from the database 
            $mediaManager = new MediaManager();
            $listMedias = $mediaManager->getListMediasForUser($id); // we retrieve the list of user logos 

            if(!empty($listMedias)){
                foreach($listMedias as $media){
                    try{
                        unlink($media->getPath());  // delete media on the server in the media folder 
                        $mediaManager->deleteMedia($media->getId());    // deletion from the database
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }   
                }
            }

            // deletion of the database of all the user's socialNetworks 
            $socialNetworkManager = new SocialNetworkManager();
            $listSocialNetworksForUserDelete = $socialNetworkManager->getListSocialNetworksForUser($id);
            
            if(!empty($listSocialNetworksForUserDelete)){
                foreach($listSocialNetworksForUserDelete as $socialnetwork){
                    try
                    {
                        $socialNetworkManager->deleteSocialNetwork($socialnetwork->getId());    // deletion from the database 
                    }
                        catch (Exception $e)
                    {
                        $errors[] = $e->getMessage();
                    } 
                }
            }

            // deletion of all posts linked to the user CAUTION FUNCTIONALITY NOT PROVIDED FOR BY THE SUBJECT BUT MADE POSSIBLE BY ME BECAUSE ADMINISTRATOR (in backend) CAN ATTRIBUTE A POST (with his media and comments) TO A USER 
            $postManager = new PostManager();
            $listPostsForUser = $postManager->getListPostsForUser($id);

            if(!empty($listPostsForUser)){
                foreach($listPostsForUser as $post){    // deletion of all posts (and their associated media) from the user 
                    
                    // we delete the media linked to the post (if there is any) 
                    $listMediasDelete =  $mediaManager->getListMediasForPost($post->getId());// we get the media list for this $ post 

                    if($listMediasDelete !== []){
                        foreach($listMediasDelete as $media){
                            try{
                                unlink($media->getPath());  // delete media on the server in the media folder 
                                $mediaManager->deleteMedia($media->getId());    // deletion from the database 
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }  
                        }
                    }

                    // we delete the comments linked to the post (if there are any) 
                    $listCommentsDelete =  $commentManager->getListCommentsForPost($post->getId());// we get the list of comments for this $ post 
                    
                    if($listCommentsDelete !== []){
                        foreach($listCommentsDelete as $comment){
                            try{
                                $commentManager->deleteComment($comment->getId());    // deletion from the database 
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }  
                        }
                    }

                    // we delete the post 
                    try{
                        $post = $postManager->deletePost($post->getId());
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }  
                }
            }

            // user removal 
            try{
                $user = $userManager->deleteUser($id);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
            // session_destroy();
            unset($_SESSION['connection']);
           
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 

            require('../app/Views/frontViews/frontDeleteUserView.php');
        }else {
            $errors[] = 'impossible de supprimer ce user, vous n\'en avait pas le droit';
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
            header('Location: /listposts');
            return http_response_code(302);
        }

    }

//COMMENT
    /**
     * function use for road http://localhost:8000/editCommentPostFront/1 ou http://localhost:8000/editCommentPostFront/2 ou ....
     * will display the view frontEditCommentPostView.php
     */
    function editCommentPostFront($id)
    {
        $userLogged = Auth::check(['administrateur','abonner']);

        $errors = [];

        // we edit the comment (through its form) 
        $commentManager = new CommentManager();
        try{
            $comment = $commentManager->getComment($id);
        } catch (Exception $e) {    //in the event that we request a resource that does not exist (here an id of how that does not exist) 
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        if($comment->getUser_id() === $_SESSION['connection']){ // we check that the comment that the user wishes to modify belongs to him 

            $formComment = new Form($comment, true);

            if($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a comment) has been made
                
                // validation test of the form fields 
                if(empty($_POST['comment']) OR mb_strlen($_POST['comment'])<=3){
                    $errors[] = 'Le champ commentaire ne peut être vide et doit contenir plus de 3 caracteres';
                }

                // info recording 
                if(empty($errors)){
                    // save comment changes 
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
     * will display the view frontDeleteCommentPostView.php
     */
    function deleteCommentPostFront($id)
    {
        $userLogged = Auth::check(['administrateur','abonner']);

        $errors = [];

        // we delete the comment 
        $commentManager = new CommentManager();
        try{
            $comment = $commentManager->getComment($id);
        } catch (Exception $e) {    // in the event that we request a resource that does not exist (here an id of how that does not exist) 
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }
        
        if($comment->getUser_id() === $_SESSION['connection']){ // we check that the comment that the user wishes to modify belongs to him 
            try{
                $comment = $commentManager->deleteComment($id);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }  

            require('../app/Views/frontViews/frontDeleteCommentPostView.php');
        }else {
            $errors[] = 'impossible de supprimer le commentaire :'.$comment->getId().' par le user :'.$_SESSION['connection'];
            setFlashErrors($errors);

            header('Location: /listposts');
            return http_response_code(302);
        }
    }