<?php

use App\Entities\Auth;
use App\Entities\Form;
use App\Entities\Post;
use App\Entities\User;
use App\Entities\Media;
use App\Entities\UserType;
use App\Models\PostManager;
use App\Models\UserManager;
use App\Models\MediaManager;
use App\Models\CommentManager;
use App\Entities\SocialNetwork;
use App\Models\UserTypeManager;
use App\Models\SocialNetworkManager;

// CONNECTION / DECONNECTION AU SITE
    /**
     * function use for road http://localhost:8000/backend/connection
     * will display the view backConnectionView.php  
     */
    function connection()
    {
        $user = new User();
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a connection attempt ) has been made
            
            if(!empty($_POST['login']) && !empty($_POST['password'])){
                
                $userManager = new UserManager();

                try {
                    $userRegister = $userManager->findByUserLogin($_POST['login']);

                    if($userRegister->getPassword() ===  $_POST['password']){
                        session_start();
                       
                        $_SESSION['connection'] = $userRegister->getId(); //creation de la session qui enregistre le id de user qi vient de se connecter
                        
                        $userLogged = $userRegister;    //pour avoir l'user logger si par la suite dans le code on ne fait pas appel a la function "Auth::check(['administrateur'])" ou "Auth::sessionStart()"

                        if($userManager->getUserSatus($_SESSION['connection'])['status'] === 'administrateur'){
                            header('Location: /backend/adminPosts');    //si user est administrateur il va sur le bachend admin
                            return http_response_code(302);
                        }else if($userManager->getUserSatus($_SESSION['connection'])['status'] === 'abonner' and !is_null($userRegister->getValidate())){  //si le user qui se connect est de type "abonner" et que sont compte a était valider par l administrateur du site (=> validate ! null)
                            header('Location: /userFrontDashboard/'.$userRegister->getId());    //si user est abonner il va sur son dashboard
                            // header('Location: /');    //si user est abonner il va sur le front page home
                            return http_response_code(302);
                        }else {
                            // $error = 'votre status ne vous autorise pas a acceder au contenu du site reserver a un certain statut ';
                            header('Location: /?unauthorizedAccess=true');
                        }
                    } else { 
                        $error = 'mot de passe incorrect';
                    }
                } catch (Exception $e){
                    $error = $e->getMessage();
                }
            } else {
                $error = 'remplissez tout les champs du formulaire s\'il vous plait !!!';
            }
        }

        $form = new Form($user);

        require('../app/Views/backViews/backConnectionView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/disconnection
     * will display the view backDisconnectionView.php 
     * we display the view backConnectionView in redirection  
     */
    function disconnection()
    {
        session_start();
        session_destroy();
        header('Location: /backend/connection');
        return http_response_code(302);
    }
// POST
    /**
     * function use for road http://localhost:8000/backend/adminPosts
     * will display the view backAdminPostsView.php  
     */
    function adminPosts()
    {
        $userLogged = Auth::check(['administrateur']);
        
        $postManager = new PostManager();
        $listPosts = $postManager->getListPosts();
        
        require('../app/Views/backViews/post/backAdminPostsView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/createPost
     * will display the view backCreatePostView.php  
     */
    function createPost()
    {
        $userLogged = Auth::check(['administrateur']);
        
        // post
        $post = new Post();
        $post->setDateCreate(new Datetime()); //to assign today's date (in datetime) by default to the post we create
        $formPost = new Form($post);

        // users
        $userManager = new UserManager();
        $user = new User();

        $listUsers = $userManager->getListUsers();
        $listUsersSelect = $userManager->listUsersFormSelect($listUsers);
        
        $formUser = new Form($user);

        // media (image et video)
        $mediaManager = new MediaManager();
       
        $mediaUploadImage = new Media(); //pour avoir dans le champ input "texte alternatif du media uploader" (creer apres) un champs vide
        $formMediaUploadImage = new Form($mediaUploadImage); //nommer "$formMediaUploadImage" au lieu de "$formMedia" par rapport a l editPost() et son utilisation dans "_form.php" du dossier "backendViews > post"
        
        $mediaUploadVideo = new Media();
        $formMediaUploadVideo = new Form($mediaUploadVideo);

        // traitement server et affichage des retours d'infos 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a post) has been made
            
            //for data validation
            $errors = [];

            //test de validation des champs du formulaire
                if(empty($_POST['title']) OR mb_strlen($_POST['title'])<=3){
                    $errors[] = 'Le champ title ne peut être vide et doit contenir plus de 3 caracteres';
                }
                if(empty($_POST['introduction']) OR mb_strlen($_POST['introduction'])<=3){
                    $errors[] = 'Le champ introduction ne peut être vide et doit contenir plus de 3 caracteres';
                }
                if(empty($_POST['content']) OR mb_strlen($_POST['content'])<=3){
                    $errors[] = 'Le champ content ne peut être vide et doit contenir plus de 3 caracteres';
                }

            if(empty($errors)){
                                    
                //ISSSUE  gestion des date en datetime dans entité post // base de donnee en string pour la create ou l edit d un post (=>voir methode setDateCreate($dateCreate) de la class Post)
                //modification pour gerer l enregistrement dans la base de donnee via le Postmanager
                    $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['dateCreate']); // pour que la date String soit en Datetime
                            
                // enregistrement en bdd du post
                $post
                    ->setTitle($_POST['title'])
                    ->setIntroduction($_POST['introduction'])
                    ->setContent($_POST['content'])
                    ->setDateCreate($dateCreate)
                    ->setUser_id($_POST['user'])
                    ;

                $postManager = new PostManager();

                try{
                    $lastRecordingPost = $postManager->addPost($post);// add the post to the database and get the last id of the posts in the database via the return of the function
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                } 

                //media IMAGE
                if(isset($_FILES['mediaUploadImage']) AND $_FILES['mediaUploadImage']['error']== 0){
                                                
                    // variables infos
                    $idMediaType = 1;   //image
                    
                    $file = $_FILES['mediaUploadImage']; //fichier uploader
                    $storagePath = searchDatasFile('imageStoragePath')[1]; //chemin de stockage du fichier uploader (voir fichier globalFunctions.php)
                    $name = 'mediaImage-'.pathinfo($file['name'])['filename'].'-'; 
                    $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique
                    
                    $extension_upload = pathinfo($file['name'])['extension']; //pour recuperer l'extension du fichier uploader
                    $pathFile =  $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader

                    // enregistrement en bdd du media IMAGE et du fichier uploader sur le server dans le dossier media
                    $mediaUploadImage
                        ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                        ->setAlt($_POST['altFileMediaImage'])
                        ->setStatutActif(1) //actif
                        ->setMediaType_id($idMediaType)
                        ->setPost_id($lastRecordingPost)
                        ->setUser_id($_POST['user'])
                        ;
                    
                    try{
                        $mediaManager->addMediaImage($mediaUploadImage, CONFIGFILE, $file); //adding the media to the database and recovery via the id function of the last media in the database
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                        
                    }
                }
                
                //media VIDEO
                if (!empty($_POST['mediaUploadVideo'])){
                    // enregistrement en bdd du media VIDEO
                    $mediaUploadVideo
                        ->setPath($_POST['mediaUploadVideo'])
                        ->setAlt($_POST['altFileMediaVideo'])
                        ->setStatutActif(1) //actif
                        ->setMediaType_id(3)    //video
                        ->setPost_id($lastRecordingPost)
                        ->setUser_id($_POST['user'])
                        ;
                    try{
                        $mediaManager->addMediaVideo($mediaUploadVideo);
                    } catch (Exception $e) {
                        // setFlashMessage($e->getMessage());
                        $errors[] = $e->getMessage();
                    }
                }
                
                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
                
                header('Location: /backend/editPost/'.$lastRecordingPost.'?created=true');
                return http_response_code(302);

            }else{
                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
                
                header('Location: /backend/createPost?created=false');
                return http_response_code(302);
            }
        }

        require('../app/Views/backViews/post/backCreatePostView.php');
    }

    /**
     * function use for road road http://localhost:8000/backend/editPost/1 ou http://localhost:8000/backend/editPost/2 ou ....
     * will display the view backEditPostView.php  
     */
    function editPost($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];

        // post
        $postManager = new PostManager();
        try{
            $post = $postManager->getPost($id);
        } catch (Exception $e) {    //dans le cas ou l'on demande une ressource qui n'existe pas (ici un id de post qui n'existe pas)
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }

        if ( $post->getDateChange() === null){
            $post->setDateChange(new Datetime()); //to assign today's date (in datetime) by default when to edit the post
        }
        $formPost = new Form($post, true);    //pour pouvoir creer le formulaire de post (grace aux fonction qui creer les champs)

        // users
        $userManager = new UserManager();
        $user = $userManager->getUser($post->getUser_id());   // sera utiliser dans "$formPost = new Form($post);" ci dessous qui permettra de creer les champs propre au $post (via l entité "Form.php")
        $listUsers = $userManager->getListUsers();
        $listUsersSelect = $userManager->listUsersFormSelect($listUsers);//sera utiliser dans "backView > post > _form.php"
    

        $formUser = new Form($user, true);    //pour creer le champs select des users qui sera integrer dans "backView > post > _form.php"     

        // media (image et video)
        $mediaManager = new MediaManager();             
        $listMediasForUser = $mediaManager->getListMediasForUser($post->getUser_id());
        $listIdsMediaType = [1,3];  //image et video
        $listMediasForUserForType = $mediaManager->getListMediasForUserForType($listMediasForUser, $listIdsMediaType);

        if(!empty($listMediasForUserForType)){
            $media = $listMediasForUserForType[0]; // on recuperer le premier media de l user du post qui sera utiliser dans "$formMedia = new Form($media);" ci dessous qui permettra de creer les champs propre au $media (via l entité "Form.php")
            $formMediasImageSelect = new Form($media);  //pour creer le champs select des media qui sera integrer dans "backView > post > _form.php"
        }

        //utiliser dans "backviews > post > _form.php" 
        $listMediasForUserSelect =  $mediaManager->listMediasFormSelect($listMediasForUserForType); // on affiche la liste des media de l'user auteur du post (uniquement les image et les video)     
        $listMediasForPostSelect =  $mediaManager->getIdOftListMediasActifForPost($post->getId());// on recupere la liste des media pour ce $post

        $mediaUploadImage = new Media();
        $formMediaUploadImage = new Form($mediaUploadImage);  //pour creer le champs input "texte alternatif du media uploader" qui sera integrer dans "backView > post > _form.php"

        $mediaUploadVideo = new Media();
        $formMediaUploadVideo = new Form($mediaUploadVideo);
       
        // traitement server et affichage des retours d'infos 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a post) has been made

            //test de validation des champs du formulaire
            if(empty($_POST['title']) OR mb_strlen($_POST['title'])<=3){
                $errors[] = 'Le champ title ne peut être vide et doit contenir plus de 3 caracteres';
            }
            if(empty($_POST['introduction']) OR mb_strlen($_POST['introduction'])<=3){
                $errors[] = 'Le champ introduction ne peut être vide et doit contenir plus de 3 caracteres';
            }
            if(empty($_POST['content']) OR mb_strlen($_POST['content'])<=3){
                $errors[] = 'Le champ content ne peut être vide et doit contenir plus de 3 caracteres';
            }

            if(empty($errors)){
                                
                //ISSUE  gestion des date en datetime dans entité post // base de donnee en string pour la create ou l edit d un post (=>voir methode setDateCreate($dateCreate) de la class Post)
                //modification pour gerer l enregistrement dans la base de donnee via le Postmanager
                $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['dateCreate']); // pour que la date String soit en Datetime
                
                $dateChange = $_POST['dateChange'];
                if($_POST['dateChange'] === ''){
                    $dateChange=NULL;
                }
        
                // enregistrement des modifications (via le select des users) infos sur le post
                    $post
                        ->setTitle($_POST['title'])
                        ->setIntroduction($_POST['introduction'])
                        ->setContent($_POST['content'])
                        ->setDateCreate($dateCreate)
                        ->setDateChange($dateChange)
                        ->setUser_id($_POST['user'])
                        ;
                    
                    try{                    
                        $postManager->updatePost($post);
                    } catch (Exception $e) {
                        // setFlashMessage($e->getMessage());
                        $errors[] = $e->getMessage();
                    }

                // -------- enregistrement des modifications (via le select des medias et upload de media) des infos sur les media lié au post edité
                    // cela nous servira par la suite a savoir si le user a l origine du post a ete modifier
                    $userOrigine = $user;
                    $newUser = $userManager->getUser($post->getUser_id());

                    // si l utilisateur a ete modifier on desactive les medias lier a ce post
                    if ($userOrigine != $newUser){
                        foreach($listMediasForPostSelect as $value){                           
                            $statutActif = 0; //false
                            $mediaManager->updateStatutActifMedia($value, $statutActif); 
                        }
                    }

                    if($userOrigine == $newUser){ //on enregistre la nouvelle liste de media pour le post definit dans le select des medias uniquement si le user n a pas changer
                        // ajout du media si un upload image a ete fait lors de l edit du post
                        if(isset($_FILES['mediaUploadImage']) AND $_FILES['mediaUploadImage']['error']== 0){
                            // variables infos
                            $idMediaType = 1;   //image

                            $file = $_FILES['mediaUploadImage']; //fichier uploader
                            $storagePath = searchDatasFile('imageStoragePath')[1]; //chemin de stockage du fichier uploader (voir fichier globalFunctions.php)         
                            $name = 'mediaImage-'.pathinfo($file['name'])['filename'].'-';
                            $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique
                            
                            $extension_upload = pathinfo($file['name'])['extension']; //pour recuperer l'extension du fichier uploader   
                            $pathFile =  $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader

                            // enregistrement en bdd du media IMAGE et du fichier uploader sur le server dans le dossier media
                            
                            $mediaUploadImage
                                ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                                ->setAlt($_POST['altFileMediaImage'])
                                ->setStatutActif(1) //actif
                                ->setMediaType_id($idMediaType)
                                ->setPost_id($post->getId())
                                ->setUser_id($_POST['user'])
                                ;
                            
                            try{
                                $mediaManager->addMediaImage($mediaUploadImage, CONFIGFILE, $file); //adding the media to the database and recovery via the id function of the last media in the database
                            } catch (Exception $e) {
                                // setFlashMessage($e->getMessage());
                                $errors[] = $e->getMessage();
                            }
                        }          
                        
                        // ajout du media si un upload video a ete fait lors de l edit du post
                        if (!empty($_POST['mediaUploadVideo'])){
                            // enregistrement en bdd du media VIDEO
                            $mediaUploadVideo
                                ->setPath($_POST['mediaUploadVideo'])
                                ->setAlt($_POST['altFileMediaVideo'])
                                ->setStatutActif(1) //actif
                                ->setMediaType_id(3)    //video
                                ->setPost_id($post->getId())
                                ->setUser_id($_POST['user'])
                                ;
                            
                            try{
                                $mediaManager->addMediaVideo($mediaUploadVideo);
                            } catch (Exception $e) {
                                // setFlashMessage($e->getMessage());
                                $errors[] = $e->getMessage();
                            } 
                        }
                        
                        // on met tout les medias du post en statutActif = false
                        foreach($listMediasForPostSelect as $value){                           
                            $statutActif = 0; //false
                            $mediaManager->updateStatutActifMedia($value, $statutActif); 
                        }
                        
                        // on met tout les medias dont leurs id sont dans "$_POST['path']" en statutActif = true 
                        // et on modifie leurs post_id pour bien attribuer au media selectionner dans le select le id du post
                        foreach($_POST['path'] as $value){
                            $statutActif = 1; //true
                            $mediaManager->updateStatutActifMedia($value, $statutActif);
                            try{
                                $mediaManager->updatePostIdMedia($value, $post->getId());
                            } catch (Exception $e) {
                                // setFlashMessage($e->getMessage());
                                $errors[] = $e->getMessage();
                            }
                        }
                    }

                    // ATTENTION ON MODIFIE LE USERORIGINE pour que notre verification de changement de user du post soit toujours valable
                    $userOrigine = $newUser;
                
                // --------------FIN enregistrement des modifications (via le select des medias) des infos sur les media lié au post 
                
                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)

                header('Location: /backend/editPost/'.$post->getId().'?success=true');
                return http_response_code(302);

            }else{
                
                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)

                header('Location: /backend/editPost/'.$post->getId().'?success=false');
                return http_response_code(302);
            }
        }

        require('../app/Views/backViews/post/backEditPostView.php');
    }

    /**
     * function use for road road  http://localhost:8000/backend/deletePost/1 ou http://localhost:8000/backend/deletePost/2 ou ....
     * will display the view backDeletePostView.php  
     */
    function deletePost($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];
 
        // on supprime les commentaires lier au post (si il y en a)
        $commentManager = new CommentManager();
        $listCommentsDelete = $commentManager->getListCommentsForPost($id);
        
        if($listCommentsDelete !== []){
            foreach($listCommentsDelete as $comment){
                try{
                    $commentManager->deleteComment($comment->getId());    //suppression dans la base de donnée
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        // on supprime les medias lier au post (si il y en a)
        $mediaManager = new MediaManager();
        $listMediasDelete =  $mediaManager->getListMediasForPost($id);// on recupere la liste des media pour ce $post
        
        if($listMediasDelete !== []){
            foreach($listMediasDelete as $media){
                try{
                    unlink($media->getPath());  //suppression des media sur le serveur dans le dossier media
                    $mediaManager->deleteMedia($media->getId());    //suppression dans la base de donnée
                } catch (Exception $e) {
                    // setFlashMessage($e->getMessage());
                    $errors[] = $e->getMessage();
                } 
            }
        }
        
        // on supprime le post
        $postManager = new PostManager();
        try{
            $post = $postManager->deletePost($id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        } 

        setFlashErrors($errors);

        require('../app/Views/backViews/post/backDeletePostView.php');
    }

// USER
    /**
     * function use for road http://localhost:8000/backend/adminUsers
     * will display the view backAdminUsersView.php  
     */
    function adminUsers()
    {
        $userLogged = Auth::check(['administrateur']);
        
        $userManager = new UserManager();
        $listUsers = $userManager->getListUsers();
        require('../app/Views/backViews/user/backAdminUsersView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/adminUsersWaiteValidate
     * will display the view backAdminUsersWaiteValidateView.php  
     */
    function adminUsersWaiteValidate()
    {
        $userLogged = Auth::check(['administrateur']);
      
        $userManager = new UserManager();
        $listUsersWaiteValidate = $userManager->listUsersWaiteValidate();

        require('../app/Views/backViews/user/backAdminUsersWaiteValidateView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/createUser
     * will display the view backCreateUserView.php  
     */
    function createUser()
    {
        $userLogged = Auth::check(['administrateur']);
 
        // user
        $user = new User();
        
        $dateTime = new Datetime();
        $date = $dateTime->format('Y-m-d H:i:s'); 
        $user->setValidate($date);
        // $user->setValidate(new Datetime()); //to assign today's date (in datetime) by default to the user we create 
        
        $userManager = new UserManager();

        $formUser = new Form($user);

        // userType
        $userType = new UserType();
        $formUserType = new Form($userType);

        $userTypeManager = new UserTypeManager(); 
        $listUserTypes = $userTypeManager->getListUserTypes();
        $listUserTypesSelect = $userTypeManager-> listUserTypesFormSelect($listUserTypes); //pour afficher le contenu du select des usertypes, sera utiliser dans "backView > user > _form.php"

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

                    // enregistrement en bdd du user
                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        ->setSlogan($_POST['slogan'])
                        ->setLogin($_POST['login'])
                        ->setPassword($_POST['password'])
                        ->setUserType_id($_POST['userType_id'][0]); //car on cette donnee est issu d'un select multiple
                        // ->setValidate(new Datetime()); //to assign today's date (in datetime) by default to the user we create
                        // ->setValidate(DateTime::createFromFormat('Y-m-d H:i:s',new Datetime())); //to assign today's date (in datetime) by default to the user we create 
                    
                    try{
                        $lastRecordingUser = $userManager->addUser($user);// add the user to the database and get the last id of the users in the database via the return of the function
                    } catch (Exception $e) {
                        // setFlashMessage($e->getMessage());
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
                            // setFlashMessage($e->getMessage());
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
                            // setFlashMessage($e->getMessage());
                            $errors[] = $e->getMessage();
                            // setFlashMessage($e->getMessage(), 'warning');
                        }
                    }

                    setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)

                    header('Location: /backend/editUser/'.$lastRecordingUser.'?created=true');
                    return http_response_code(302);

                }else{
                    
                    setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
                    
                    header('Location: /backend/createUser?created=false');
                    return http_response_code(302);

                }
        }
        
        require('../app/Views/backViews/user/backCreateUserView.php');
    }

    /**
     * function use for road road http://localhost:8000/backend/editUser/1 ou http://localhost:8000/backend/editUser/2 ou ....
     * will display the view backEditUserView.php  
     */
    function editUser($id)
    {
        $userLogged = Auth::check(['administrateur']);
        
        $errors = [];

        // user
        $userManager = new UserManager();
        try{
            $user = $userManager->getUser($id);
        } catch (Exception $e) {    //dans le cas ou l'on demande une ressource qui n'existe pas (ici un id du user qui n'existe pas)
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }
        
        $formUser = new Form($user, true);

        // userType
        $userTypeManager = new UserTypeManager();
        $userType = $userTypeManager->getUserType($user->getUserType_id()); // sera utiliser dans "$formUserType = new Form($userType);" qui creer les champs propres au userType (via l entité "Form.php") qui seront eux meme integrer pour les integrer (en totalite ou en partie) dans "$formUser = new Form($user);" ci dessous qui permettra de creer les champs propre au $user (via l entité "Form.php")
        
        $listUserTypes = $userTypeManager->getListUserTypes();
        $listUserTypesSelect = $userTypeManager-> listUserTypesFormSelect($listUserTypes); //pour afficher le contenu du select des usertypes, sera utiliser dans "backView > user > _form.php"
        
        $formUserType = new Form($userType);

        // media (logo)
        $mediaManager = new MediaManager();
       
        $listMediasForUser = $mediaManager->getListMediasForUser($user->getId());
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

        $listSocialNetworksForUser = $socialNetworkManager->getListSocialNetworksForUser($user->getId());
        $listSocialNetworksForUserForSelect =  $socialNetworkManager->listSocialNetworksFormSelect($listSocialNetworksForUser); // on affiche la liste des social network de l'user 
        
        if(!empty($listSocialNetworksForUser)){
            $socialNetworkForSelect = $listSocialNetworksForUser[0];
            $formSocialNetworkSelect = new Form($socialNetworkForSelect);
        }
        
        // traitement server et affichage des retours d'infos 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a user) has been made

            //for data validation
            // $errors = [];

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

            if(empty($errors)){
            
                // enregistrement en bdd du user
                $user
                    ->setFirstName($_POST['firstName'])
                    ->setLastName($_POST['lastName'])
                    ->setEmail($_POST['email'])
                    ->setSlogan($_POST['slogan'])
                    ->setLogin($_POST['login'])
                    ->setPassword($_POST['password'])
                    ->setUserType_id($_POST['userType_id'][0]); //car cette donnee est issu d'un select multiple
                
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
                    $listMediasForUser = $mediaManager->getListMediasForUser($user->getId());
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
                        ->setUser_id($user->getId())
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
                            ->setUser_id($user->getId())
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

                header('Location: /backend/editUser/'.$user->getId().'?success=true');
                return http_response_code(302);

            }else{
                
                setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)

                header('Location: /backend/editUser/'.$user->getId().'?success=false');
                return http_response_code(302);
            }
        }

        require('../app/Views/backViews/user/backEditUserView.php');
    }

    /**
     * function use for road road  http://localhost:8000/backend/deleteUser/1 ou http://localhost:8000/backend/deleteUser/2 ou ....
     * will display the view backDeleteUserView.php  
     */
    function deleteUser($id)
    {
        
        $userLogged = Auth::check(['administrateur']);

        $errors = [];
  
        // suppression de la base de donnee de tout les commentaires de l'user    
        $commentManager = new CommentManager();
        $listCommentsDelete = $commentManager->listCommentsForUser($id);

        if($listCommentsDelete !== []){
            foreach($listCommentsDelete as $comment){
                try{
                    $commentManager->deleteComment($comment->getId());  //suppression dans la base de donnée
                } catch (Exception $e) {
                    // setFlashMessage($e->getMessage());
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
                    // setFlashMessage($e->getMessage());
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
                    // setFlashMessage($e->getMessage());
                    $errors[] = $e->getMessage();
                } 
            }
        }

        //supression de tout les post lier a l'user
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
                            // setFlashMessage($e->getMessage());
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
                            // setFlashMessage($e->getMessage());
                            $errors[] = $e->getMessage();
                        } 
                    }
                }

                // on supprime le post
                try{
                    $post = $postManager->deletePost($post->getId());
                } catch (Exception $e) {
                    // setFlashMessage($e->getMessage());
                    $errors[] = $e->getMessage();
                } 
            }
        }

        // suppression de l'user
        $userManager = new UserManager();

        try{
            $user = $userManager->deleteUser($id);
        } catch (Exception $e) {
            // setFlashMessage($e->getMessage());
            $errors[] = $e->getMessage();

        }

        setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)

        require('../app/Views/backViews/user/backDeleteUserView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/validateUser/1 ou http://localhost:8000/backend/validateUser/2 ou ....
     * will display the view backValidateUserView.php  
     */
    function validateUser($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];

        // on valide le commentaire
        $usertManager = new userManager();
        try{
            $usertManager->validateUser($id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();

            setFlashErrors($errors);
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }  
        
        require('../app/Views/backViews/user/backValidateUserView.php');
    }

// COMMENT
    /**
     * function use for road http://localhost:8000/backend/adminCommentsWaiteValidate
     * will display the view backAdminCommentsWaiteValidateView.php  
     */
    function adminCommentsWaiteValidate()
    {
        $userLogged = Auth::check(['administrateur']);

        $commentManager = new CommentManager();
        $listCommentsWaiteValidate = $commentManager->listCommentsWaiteValidate();

        require('../app/Views/backViews/comment/backAdminCommentsWaiteValidateView.php');
    }

    /**
     * function use for road road http://localhost:8000/backend/editCommentsPost/1 ou http://localhost:8000/backend/editCommentsPost/2 ou ....
     * will display the view backEditCommentsPostView.php
     * display all the comments of a post 
     */
    function editCommentsPost($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];
  
        $commentManager = new CommentManager();
        $listCommentsForPost = $commentManager->getListCommentsForPost($id);

        require('../app/Views/backViews/comment/backEditCommentsPostView.php');
    }

    /**
     * function use for road road  http://localhost:8000/backend/deleteComment/1 ou http://localhost:8000/backend/deleteComment/2 ou ....
     * will display the view backDeleteCommentView.php  
     */
    function deleteComment($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];
    
        // on supprime le commentaire
        $commentManager = new CommentManager();
        try{
            $comment = $commentManager->deleteComment($id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();

            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
            
        }

        setFlashErrors($errors);

        require('../app/Views/backViews/comment/backDeleteCommentView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/validateComment/1 ou http://localhost:8000/backend/validateComment/2 ou ....
     * will display the view backValidateCommentView.php  
     */
    function validateComment($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];
    
        // on valide le commentaire
        $commentManager = new CommentManager();
        try{
            $comment = $commentManager->validateComment($id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();

            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once('../app/Views/errors.php');
            return http_response_code(302);
        }  
        
        require('../app/Views/backViews/comment/backValidateCommentView.php');  
    }