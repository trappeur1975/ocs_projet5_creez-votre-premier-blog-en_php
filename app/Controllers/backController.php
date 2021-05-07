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
                       
                        $_SESSION['connection'] = $userRegister->getId(); //status du user qui se connecte
                                         
                        if($userManager->getUserSatus($_SESSION['connection'])['status'] === 'administrateur'){
                            header('Location: /backend/adminPosts');    //si user est administrateur il va sur le bachend admin
                            exit();
                        }else if($userManager->getUserSatus($_SESSION['connection'])['status'] === 'abonner'){
                            header('Location: /');    //si user est abonner il va sur le front page home
                            exit();
                        }else {
                            $error = 'votre status ne vous autorise pas a acceder au contenu du site reserver a un certain statut ';
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
        exit();
    }

// AUTRE
    /**
     * function use for road http://localhost:8000/backend
     * will display the view backHomeView.php 
     */
    function backHome()
    {
        Auth::check();

        require('../app/Views/backViews/backHomeView.php');
    }
    
// POST
    /**
     * function use for road http://localhost:8000/backend/adminPosts
     * will display the view backAdminPostsView.php  
     */
    function adminPosts()
    {
        Auth::check();
        
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
        Auth::check();
        
        // post
        $post = new Post();
        $post->setDateCreate(new Datetime()); //to assign today's date (in datetime) by default to the post we create 
        $post->setDatechange(NULL); // ------POUR LE TESTE J ASSIGNE LA DATECHANGE A "NULL" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------
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
            
                if(empty($_POST['title'])){
                    $errors['title'][] = 'Le champs titre ne peut être vide';
                }
                if(mb_strlen($_POST['title'])<=3){
                    $errors['title'][] = 'Le champs titre doit contenir plus de 3 caractere';
                }
                
                if(empty($errors)){
                                      
                    //ISSSUE  gestion des date en datetime dans entité post // base de donnee en string pour la create ou l edit d un post (=>voir methode setDateCreate($dateCreate) de la class Post)
                    //modification pour gerer l enregistrement dans la base de donnee via le Postmanager
                        $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['dateCreate']); // pour que la date String soit en Datetime
                        $dateChange = null;
                               
                    // enregistrement en bdd du post
                    $post
                        ->setTitle($_POST['title'])
                        ->setIntroduction($_POST['introduction'])
                        ->setContent($_POST['content'])
                        ->setDateCreate($dateCreate)
                        ->setDateChange($dateChange)
                        ->setUser_id($_POST['user'])
                        ;

                    $postManager = new PostManager();
                    $lastRecordingPost = $postManager->addPost($post);// add the post to the database and get the last id of the posts in the database via the return of the function
                   
                    //media IMAGE
                    if(isset($_FILES['mediaUploadImage']) AND $_FILES['mediaUploadImage']['error']== 0){
                        // variables infos
                        $file = $_FILES['mediaUploadImage']; //fichier uploader
                        $storagePath = './media/'; //chemin de stockage du fichier uploader
                        $fileType = 'image'; //type de fichier uploader
                        $maxFileSize = 500000; //taille maximum du fichier uploader autorise
                        
                        $name = 'mediaImage-'.pathinfo($_FILES['mediaUploadImage']['name'])['filename'].'-';
                        $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique
                        
                        $extension_upload = pathinfo($_FILES['mediaUploadImage']['name'])['extension']; //pour recuperer l'extension du fichier uploader
                        $pathFile = './media/'.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader
                        
                        // enregistrement en bdd du media IMAGE et du fichier uploader sur le server dans le dossier media
                        $mediaUploadImage
                            ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                            ->setAlt($_POST['altFileMediaImage'])
                            ->setStatutActif(1) //actif
                            ->setMediaType_id(1)    //image
                            ->setPost_id($lastRecordingPost)
                            ->setUser_id($_POST['user'])
                            ;
                        //try{
                        $mediaManager->addMediaImage($mediaUploadImage, $file, $storagePath, $fileType, $maxFileSize, $newNameUploaderFile); //adding the media to the database and recovery via the id function of the last media in the database
                    // } catch {
                    //     $errorMessage  que l on passe a ma vue
                    // }
                        
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
                        
                        $mediaManager->addMediaVideo($mediaUploadVideo);
                    }
                    // -------------fin enregistrement en bdd du media VIDEO --------------------
           
                    header('Location: /backend/editPost/'.$lastRecordingPost.'?created=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/createPost?created=false');
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
        Auth::check();

        // post
        $postManager = new PostManager();
        $post = $postManager->getPost($id);

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
            //for data validation
                $errors = [];

                if(empty($_POST['title'])){
                    $errors['title'][] = 'Le champs titre ne peut être vide';
                }
                if(mb_strlen($_POST['title'])<=3){
                    $errors['title'][] = 'Le champs titre doit contenir plus de 3 caractere';
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
                                            
                        $postManager->updatePost($post);

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
                                $file = $_FILES['mediaUploadImage']; //fichier uploader
                                $storagePath = './media/'; //chemin de stockage du fichier uploader
                                $fileType = 'image'; //type de fichier uploader
                                $maxFileSize = 500000; //taille maximum du fichier uploader autorise    
                                  
                                $name = 'mediaImage-'.pathinfo($_FILES['mediaUploadImage']['name'])['filename'].'-';
                                $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique
                                
                                $extension_upload = pathinfo($_FILES['mediaUploadImage']['name'])['extension']; //pour recuperer l'extension du fichier uploader
                                $pathFile = './media/'.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader

                                // enregistrement en bdd du media IMAGE et du fichier uploader sur le server dans le dossier media
                                $mediaUploadImage
                                    ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                                    ->setAlt($_POST['altFileMediaImage'])
                                    ->setStatutActif(1) //actif
                                    ->setMediaType_id(1)    //image
                                    ->setPost_id($post->getId())
                                    ->setUser_id($_POST['user'])
                                    ;
                                
                                $mediaManager->addMediaImage($mediaUploadImage, $file, $storagePath, $fileType, $maxFileSize, $newNameUploaderFile); //adding the media to the database and recovery via the id function of the last media in the database
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
                            
                                $mediaManager->addMediaVideo($mediaUploadVideo);
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
                                $mediaManager->updatePostIdMedia($value, $post->getId());
                            }
                        }

                        // ATTENTION ON MODIFIE LE USERORIGINE pour que notre verification de changement de user du post soit toujours valable
                        $userOrigine = $newUser;
                    
                    // --------------FIN enregistrement des modifications (via le select des medias) des infos sur les media lié au post 
                    
                    header('Location: /backend/editPost/'.$post->getId().'?success=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/editPost/'.$post->getId().'?success=false');
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
        Auth::check();

        $mediaManager = new MediaManager();
        $listMediasDelete =  $mediaManager->getListMediasForPost($id);// on recupere la liste des media pour ce $post

        // on supprime les medias lier au post (si il y en a)
        if($listMediasDelete !== []){
            foreach($listMediasDelete as $media){
                $mediaManager->deleteMedia($media->getId());    //suppression dans la base de donnée
                unlink($media->getPath());  //suppression des media sur le serveur dans le dossier media
            }
        }
        
        // on supprime le post
        $postManager = new PostManager();
        $post = $postManager->deletePost($id);

        require('../app/Views/backViews/post/backDeletePostView.php');
    }

// USER
    /**
     * function use for road http://localhost:8000/backend/adminUsers
     * will display the view backAdminUsersView.php  
     */
    function adminUsers()
    {
        Auth::check();
        
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
        Auth::check();
        
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
        Auth::check();
        
        // user
        $user = new User();
        $user->setValidate(new Datetime()); //to assign today's date (in datetime) by default to the user we create 
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
            
                // if(empty($_POST['title'])){
                //     $errors['title'][] = 'Le champs titre ne peut être vide';
                // }
                // if(mb_strlen($_POST['title'])<=3){
                //     $errors['title'][] = 'Le champs titre doit contenir plus de 3 caractere';
                // }
                
                if(empty($errors)){
                    
                    //ISSSUE  gestion des date en datetime dans entité post // base de donnee en string pour la create ou l edit d un post (=>voir methode setDateCreate($dateCreate) de la class Post)
                    $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['validate']); // pour que la date String soit en Datetime

                    // if($_POST['dateChange'] === ''){
                    //     $dateChange=NULL;
                    // }

                    // enregistrement en bdd du user
                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        // ->setLogo($_POST['logo'])
                        ->setSlogan($_POST['slogan'])
                        // ->setSocialNetworks($_POST['socialNetworks'])
                        ->setLogin($_POST['login'])
                        ->setPassword($_POST['password'])
                        ->setValidate($dateCreate)
                        ->setUserType_id($_POST['userType_id'][0]); //car on cette donnee est issu d'un select multiple
                        // ->setValidate(new Datetime()); //to assign today's date (in datetime) by default to the user we create
                        // ->setValidate(DateTime::createFromFormat('Y-m-d H:i:s',new Datetime())); //to assign today's date (in datetime) by default to the user we create 

                    $userManager = new UserManager();
                    $lastRecordingUser = $userManager->addUser($user);// add the post to the database and get the last id of the posts in the database via the return of the function
                    
                    // enregistrement en bdd du media logo et du fichier uploader sur le server dans le dossier media
                    if(isset($_FILES['mediaUploadLogo']) AND $_FILES['mediaUploadLogo']['error']== 0){
                        
                        // variables infos
                        $file = $_FILES['mediaUploadLogo']; //fichier uploader
                        $storagePath = './media/'; //chemin de stockage du fichier uploader
                        $fileType = 'image'; //type de fichier uploader
                        $maxFileSize = 500000; //taille maximum du fichier uploader autorise
                        
                        $name = 'mediaLogo-'.pathinfo($_FILES['mediaUploadLogo']['name'])['filename'].'-';
                        $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique
                        
                        $extension_upload = pathinfo($_FILES['mediaUploadLogo']['name'])['extension']; //pour recuperer l'extension du fichier uploader
                        $pathFile = './media/'.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader

                        // enregistrement en bdd du media LOGO
                        $mediaUploadLogo
                            ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                            ->setAlt($_POST['altFileMediaLogo'])
                            ->setStatutActif(1)
                            ->setMediaType_id(2)
                            ->setUser_id($lastRecordingUser)
                            ;
                        
                        $mediaManager->addMediaImage($mediaUploadLogo, $file, $storagePath, $fileType, $maxFileSize, $newNameUploaderFile); //adding the media to the database and recovery via the id function of the last media in the database
                    }
                    
                    // enregistrement en bdd du socialNetwork
                    if(!empty($_POST['socialNetwork'])){
                        
                        $socialNetwork
                            ->setUrl($_POST['socialNetwork'])
                            ->setUser_id($lastRecordingUser)
                            ;

                        $socialNetworkManager->addSocialNetwork($socialNetwork);
                    }

                    header('Location: /backend/editUser/'.$lastRecordingUser.'?created=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/createUser?created=false');
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
        Auth::check();
              
        // user
        $userManager = new UserManager();
        $user = $userManager->getUser($id);

        
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
                $errors = [];

                // if(empty($_POST['title'])){
                //     $errors['title'][] = 'Le champs titre ne peut être vide';
                // }
                // if(mb_strlen($_POST['title'])<=3){
                //     $errors['title'][] = 'Le champs titre doit contenir plus de 3 caractere';
                // }

                if(empty($errors)){
                
                    //ISSSUE  gestion des date en datetime dans entité post // base de donnee en string pour la create ou l edit d un post (=>voir methode setDateCreate($dateCreate) de la class Post)
                    //modification pour gerer l enregistrement dans la base de donnee via le Usermanager
                    $dateValidate = DateTime::createFromFormat('Y-m-d H:i:s', $_POST['validate']); // pour que la date String soit en Datetime

                    if($_POST['validate'] === ''){
                        $validate=NULL;
                    }

                    // enregistrement en bdd du user
                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        // ->setLogo($_POST['logo'])
                        ->setSlogan($_POST['slogan'])
                        // ->setSocialNetworks($_POST['socialNetworks'])
                        ->setLogin($_POST['login'])
                        ->setPassword($_POST['password'])
                        ->setValidate($dateValidate)
                        ->setUserType_id($_POST['userType_id'][0]); //car on cette donnee est issu d'un select multiple

                    $userManager->updateUser($user);

                    // enregistrement en bdd du media logo et du fichier uploader sur le server dans le dossier media
                    if(isset($_FILES['mediaUploadLogo']) AND $_FILES['mediaUploadLogo']['error']== 0){
                        
                        // variables infos
                        $idMediaType = 2;   //logo
                        
                        $file = $_FILES['mediaUploadLogo']; //fichier uploader
                        $storagePath = './media/'; //chemin de stockage du fichier uploader
                        $fileType = 'image'; //type de fichier uploader
                        $maxFileSize = 500000; //taille maximum du fichier uploader autorise
                        
                        $name = 'mediaLogo-'.pathinfo($_FILES['mediaUploadLogo']['name'])['filename'].'-';
                        $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique
                        
                        $extension_upload = pathinfo($_FILES['mediaUploadLogo']['name'])['extension']; //pour recuperer l'extension du fichier uploader
                        $pathFile = './media/'.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader
                       
                        // on supprime en base de donnée ainsi que sur le server dans le dossier media l'ancien logo de l'user    
                        $listMediasForUser = $mediaManager->getListMediasForUser($user->getId());
                        // $listIdsMediaType = [2];  //logo
                        $listLogosDelete = $mediaManager->getListMediasForUserForType($listMediasForUser, $listIdsMediaType);   // on recuperer la liste des logos du user
                       
                        if(!empty($listLogosDelete)){
                            foreach($listLogosDelete as $logo){
                                unlink($logo->getPath());  //suppression des media sur le serveur dans le dossier media
                                $mediaManager->deleteMedia($logo->getId());    //suppression dans la base de donnée  
                            }
                        }

                        // enregistrement en bdd du nouveau LOGO et et de son fichier uploader sur le server dans le dossier media
                        $mediaUploadLogo
                            ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                            ->setAlt($_POST['altFileMediaLogo'])
                            ->setStatutActif(1)
                            ->setMediaType_id(2)
                            ->setUser_id($user->getId())
                            ;
                        
                        $mediaManager->addMediaImage($mediaUploadLogo, $file, $storagePath, $fileType, $maxFileSize, $newNameUploaderFile); //adding the media to the database and recovery via the id function of the last media in the database
                    }

                    // enregistrement en bdd socialNetwork des modifications qui ont etait apporté dans l'editUser()   
                        // supression du ou des socialNetwork de l'user
                        if(!empty($_POST['socialNetworksUser'])){ 
                            foreach($_POST['socialNetworksUser'] as $idSsocialNetwork){
                                $socialNetworkManager->deleteSocialNetwork($idSsocialNetwork);
                            }
                        }
                        
                        // ajout d'un socialNetwork a l'user
                        if(!empty($_POST['socialNetwork'])){
                            $socialNetwork
                                ->setUrl($_POST['socialNetwork'])
                                ->setUser_id($user->getId())
                                ;
                            
                            $socialNetworkManager->addSocialNetwork($socialNetwork);
                        }

                    header('Location: /backend/editUser/'.$user->getId().'?success=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/editUser/'.$user->getId().'?success=false');
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
        Auth::check();
        
  
        $postManager = new PostManager();
        $listPostsForUser = $postManager->getListPostsForUser($id);

        $mediaManager = new MediaManager();

        // recuperation de tout les post (et des medias que leurs sont associés) de l user pour les supprimer
        if(!empty($listPostsForUser)){
            foreach($listPostsForUser as $post){    // suppression de tout les post (et des medias que leurs sont associés) de l user
                $listMediasDelete =  $mediaManager->getListMediasForPost($post->getId());// on recupere la liste des media pour ce $post

                // on supprime les medias lier au post (si il y en a)
                if($listMediasDelete !== []){
                    foreach($listMediasDelete as $media){
                        $mediaManager->deleteMedia($media->getId());    //suppression dans la base de donnée
                        unlink($media->getPath());  //suppression des media sur le serveur dans le dossier media
                    }
                }
                // on supprime le post
                $post = $postManager->deletePost($post->getId());
            }
        }

        // ----------------A FAIRE PLUS TARD => recuperation de tout les commentaires de l user pour les supprimer--------
        
        // recuperation des dernieres medias de user non encore supprimer(les logos, image desactiver, ...) pour les supprimer du server (daossier media) et de la base de donnée
        $listMedias = $mediaManager->getListMediasForUser($id); // on recuperer la liste des logos du user
        
        if(!empty($listMedias)){
            foreach($listMedias as $media){
                unlink($media->getPath());  //suppression des media sur le serveur dans le dossier media
                $mediaManager->deleteMedia($media->getId());    //suppression dans la base de donnée  
            }
        }

        // suppression de la base de donnee de tout les socialNetworks de l'user
        $socialNetworkManager = new SocialNetworkManager();
        $listSocialNetworksForUserDelete = $socialNetworkManager->getListSocialNetworksForUser($id);
        
        if(!empty($listSocialNetworksForUserDelete)){
            foreach($listSocialNetworksForUserDelete as $socialnetwork){
                $socialNetworkManager->deleteSocialNetwork($socialnetwork->getId());    //suppression dans la base de donnée  
            }
        }

        // suppression de l'user
        $userManager = new UserManager();
        $user = $userManager->deleteUser($id);

        require('../app/Views/backViews/user/backDeleteUserView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/validateUser/1 ou http://localhost:8000/backend/validateUser/2 ou ....
     * will display the view backValidateUserView.php  
     */
    function validateUser($id)
    {
        Auth::check();
    
        // on valide le commentaire
        $usertManager = new userManager();
        $usertManager->validateUser($id);

        require('../app/Views/backViews/user/backValidateUserView.php');
    }

// COMMENT
    /**
     * function use for road http://localhost:8000/backend/adminCommentsWaiteValidate
     * will display the view backAdminCommentsWaiteValidateView.php  
     */
    function adminCommentsWaiteValidate()
    {
        Auth::check();

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
        Auth::check();
  
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
        Auth::check();
    
        // on supprime le commentaire
        $commentManager = new CommentManager();
        $comment = $commentManager->deleteComment($id);

        require('../app/Views/backViews/comment/backDeleteCommentView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/validateComment/1 ou http://localhost:8000/backend/validateComment/2 ou ....
     * will display the view backValidateCommentView.php  
     */
    function validateComment($id)
    {
        Auth::check();
    
        // on valide le commentaire
        $commentManager = new CommentManager();
        $comment = $commentManager->validateComment($id);

        require('../app/Views/backViews/comment/backValidateCommentView.php');
    }