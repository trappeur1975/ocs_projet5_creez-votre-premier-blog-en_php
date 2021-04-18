<?php

use App\Entities\Auth;
use App\Entities\Form;
use App\Entities\Post;
use App\Entities\User;
use App\Entities\Media;
use App\Entities\MediaType;
use App\Entities\UserType;
use App\Models\PostManager;
use App\Models\UserManager;
use App\Models\MediaManager;
use App\Models\UserTypeManager;
use App\Models\MediaTypeManager;

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
                        $_SESSION['connection'] = 'administrateur'. // --------------POUR LE TESTE APRES IL FAUTDRA FAIRE UN TRAITEMENT POUR RECUPERER LE TYPE DE L'USER QUI TENTE DE SE CONNECTER
                        header('Location: /backend/adminPosts'); //ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route
                        exit();
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
     * function use for road road http://localhost:8000/backend/editPost/1 ou http://localhost:8000/backend/editPost/2 ou ....
     * will display the view backEditPostView.php  
     */
    function editPost($id)
    {
        Auth::check();

        $postManager = new PostManager();
        $post = $postManager->getPost($id);
        if ( $post->getDateChange() === null){
            $post->setDateChange(new Datetime()); //to assign today's date (in datetime) by default when to edit the post
        }
 
        // pour afficher le contenu du select des users ------------
        $userManager = new UserManager();
        $user = $userManager->getUser($post->getUser_id());   // sera utiliser dans "$formPost = new Form($post);" ci dessous qui permettra de creer les champs propre au $post (via l entité "Form.php")
        $listSelectUsers = $userManager->listSelect(); //sera utiliser dans "backView > post > _form.php"

        // pour afficher le contenu du select des medias liers a l user qui est lier au post que l on souhaite editer
        $mediaManager = new MediaManager();             
        $media = $mediaManager->getListMediasForUser($post->getUser_id())[0]; // on recuperer le premier media de l user du post qui sera utiliser dans "$formMedia = new Form($media);" ci dessous qui permettra de creer les champs propre au $media (via l entité "Form.php")
        $mediaUpload = new Media(); //pour avoir dans le champ input "texte alternatif du media uploader" (creer apres) un champs vide

        //utiliser dans "backviews > post > _form.php" 
        $listSelectMediasForUser =  $mediaManager->listSelect($post->getUser_id()); // on affiche la liste des media de l'user auteur du post      
        $listSelectMediasForPost =  $mediaManager->getIdOftListMediasActifForPost($post->getId());// on recupere la liste des media pour ce $post

        // pour afficher le contenu du select des mediaType ------------
        // $mediaTypeManager = new MediaTypeManager();
        // $mediaType = new MediaType();
        // $listMediaTypes = $mediaTypeManager->listMediaType();

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
                            foreach($listSelectMediasForPost as $value){                           
                                $mediaManager->updateStatutActifMedia($value, 0); 
                            }
                        }

                        if($userOrigine == $newUser){ //on enregistre la nouvelle liste de media pour le post definit dans le select des medias uniquement si le user n a pas changer
                        // if(!is_null($_POST['path']) and ($userOrigine == $newUser)){ //on enregistre la nouvelle liste de media pour le post definit dans le select des medias uniquement si le user n a pas changer

                            // ajout du media si un upload a ete fait lors de l edit du post
                            if(isset($_FILES['mediaUpload']) AND $_FILES['mediaUpload']['error']== 0){
                                $name = 'media-'.pathinfo($_FILES['mediaUpload']['name'])['filename'].'-';
                                $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique
                                
                                $extension_upload = pathinfo($_FILES['mediaUpload']['name'])['extension']; //pour recuperer l'extension du fichier uploader
                                $pathFile = './media/'.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader

                                $mediaUpload
                                    ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                                    ->setAlt($_POST['alt'])
                                    ->setStatutActif(1)
                                    // ->setMediaType_id($_POST['mediaType'])
                                    ->setMediaType_id(1)
                                    ->setPost_id($post->getId())
                                    ->setUser_id($_POST['user'])
                                    ;
                                
                                $mediaManager->addMedia($mediaUpload, $_FILES['mediaUpload'], './media/', 'image', 400000, $newNameUploaderFile); //adding the media to the database and recovery via the id function of the last media in the database
                            }          
                            
                            // on met tout les medias du post en statutActif = false
                            foreach($listSelectMediasForPost as $value){                           
                                $mediaManager->updateStatutActifMedia($value, 0); 
                            }
                            // on met tout les medias dont leurs id sont dans "$_POST['path']" en statutActif = true 
                            // et on modifie leurs post_id pour bien attribuer au media selectionner dans le select le id du post
                            foreach($_POST['path'] as $value){
                                $mediaManager->updateStatutActifMedia($value, 1);
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

        // display of the form before saving changes 
        $formPost = new Form($post, true);    //pour pouvoir creer le formulaire de post (grace aux fonction qui creer les champs)  
        $formUser = new Form($user, true);    //pour creer le champs select des users qui sera integrer dans "backView > post > _form.php"     
        $formMediasSelect = new Form($media);  //pour creer le champs select des media qui sera integrer dans "backView > post > _form.php"
        $formMediaUpload = new Form($mediaUpload);  //pour creer le champs input "texte alternatif du media uploader" qui sera integrer dans "backView > post > _form.php"
        // $formMediaType = new Form($mediaType);

        require('../app/Views/backViews/post/backEditPostView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/createPost
     * will display the view backCreatePostView.php  
     */
    function createPost()
    {
        Auth::check();
        
        $post = new Post();
        $post->setDateCreate(new Datetime()); //to assign today's date (in datetime) by default to the post we create 
        $post->setDatechange(NULL); // ------POUR LE TESTE J ASSIGNE LA DATECHANGE A "NULL" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------
        
        // pour afficher le contenu du select des users ------------
        $userManager = new UserManager();
        $user = new User();
        $listSelectUsers = $userManager->listSelect();

        // pour afficher le champ d'upload de media ------------
        $mediaManager = new MediaManager();
        $mediaUpload = new Media(); //pour avoir dans le champ input "texte alternatif du media uploader" (creer apres) un champs vide

        // pour afficher le contenu du select des mediatype ------------
        // $mediaTypeManager = new MediaTypeManager();
        // $mediaType = new MediaType();
        // $listMediaTypes = $mediaTypeManager->listMediaType();

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
                   
                    // enregistrement en bdd du media et du fichier upload sur le server dans le dossier media

                    if(isset($_FILES['mediaUpload']) AND $_FILES['mediaUpload']['error']== 0){
                        $name = 'media-'.pathinfo($_FILES['mediaUpload']['name'])['filename'].'-';
                        $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + nom du fichier uploader(sans son extension + identifiant unique (via uniqid) pour avoir un identifiant unique
                        
                        $extension_upload = pathinfo($_FILES['mediaUpload']['name'])['extension']; //pour recuperer l'extension du fichier uploader
                        $pathFile = './media/'.basename($newNameUploaderFile.'.'.$extension_upload); //chemin de stockage  avec nouveau nom du media uploader

                        $mediaUpload
                            ->setPath($pathFile)    // ->setPath('./media/media-19.jpg')
                            ->setAlt($_POST['alt'])
                            ->setStatutActif(1)
                            // ->setMediaType_id($_POST['mediaType'])
                            ->setMediaType_id(1)
                            ->setPost_id($lastRecordingPost)
                            ->setUser_id($_POST['user'])
                            ;
                        
                        $mediaManager->addMedia($mediaUpload, $_FILES['mediaUpload'], './media/', 'image', 400000, $newNameUploaderFile); //adding the media to the database and recovery via the id function of the last media in the database
                    }

                    header('Location: /backend/editPost/'.$lastRecordingPost.'?created=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/createPost?created=false');
                }
        }

        $formPost = new Form($post);
        $formUser = new Form($user);
        $formMediaUpload = new Form($mediaUpload); //nommer "$formMediaUpload" au lieu de "$formMedia" par rapport a l editPost() et son utilisation dans "_form.php" du dossier "backendViews > post"
        // $formMediaType = new Form($mediaType);

        require('../app/Views/backViews/post/backCreatePostView.php');
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
        // dd($listMediasDelete);

        // on supprime les medias lier au post (si il y en a) dans la base de donnee et sur le serveur dans le dossier media
        if($listMediasDelete !== []){
            foreach($listMediasDelete as $media){
                unlink($media->getPath());
                $mediaManager->deleteMedia($media->getId());
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
     * function use for road road http://localhost:8000/backend/editUser/1 ou http://localhost:8000/backend/editUser/2 ou ....
     * will display the view backEditUserView.php  
     */
    function editUser($id)
    {
        Auth::check();

        $userManager = new UserManager();
        $user = $userManager->getUser($id);

        $userTypeManager = new UserTypeManager();
        $userType = $userTypeManager->getUserType($user->getUserType_id()); // sera utiliser dans "$formUserType = new Form($userType);" qui creer les champs propres au userType (via l entité "Form.php") qui seront eux meme integrer pour les integrer (en totalite ou en partie) dans "$formUser = new Form($user);" ci dessous qui permettra de creer les champs propre au $user (via l entité "Form.php")
        $listSelectUserTypes = $userTypeManager->listSelect(); //pour afficher le contenu du select des usertypes, sera utiliser dans "backView > user > _form.php"

   
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a post) has been made
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

                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        ->setSlogan($_POST['slogan'])
                        ->setLogin($_POST['login'])
                        ->setPassword($_POST['password'])
                        ->setValidate($dateValidate)
                        // ->setValidate($_POST['validate']);
                        ->setUserType_id($_POST['userType_id'][0]); //car on cette donnee est issu d'un select multiple

                    $userManager->updateUser($user);

                    header('Location: /backend/editUser/'.$user->getId().'?success=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/editUser/'.$user->getId().'?success=false');
                }
        }

        $formUser = new Form($user);
        $formUserType = new Form($userType);

        // require('../app/Views/backViews/post/backEditUserView.php');
        require('../app/Views/backViews/user/backEditUserView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/createUser
     * will display the view backCreateUserView.php  
     */
    function createUser()
    {
        Auth::check();

        $user = new user();
        $userType = new UserType();

        $userTypeManager = new UserTypeManager();
        $listSelectUserTypes = $userTypeManager->listSelect(); //pour afficher le contenu du select des usertypes, sera utiliser dans "backView > user > _form.php"

        $user->setValidate(new Datetime()); //to assign today's date (in datetime) by default to the user we create 
        
        // $user->setDatechange(NULL); // ------POUR LE TESTE J ASSIGNE LA DATECHANGE A "NULL" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------
        // $post->setUser_id(2); // ------POUR LE TESTE J ASSIGNE L UTILISATEUR "2" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a post) has been made
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

                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        ->setUserType_id($_POST['userType_id'][0]) //car on cette donnee est issu d'un select multiple
                        // ->setLogo($_POST['logo'])
                        ->setSlogan($_POST['slogan'])
                        // ->setSocialNetworks($_POST['socialNetworks'])
                        ->setLogin($_POST['login'])
                        ->setPassword($_POST['password'])
                        ->setValidate($dateCreate);
                        // ->setValidate(new Datetime()); //to assign today's date (in datetime) by default to the user we create
                        // ->setValidate(DateTime::createFromFormat('Y-m-d H:i:s',new Datetime())); //to assign today's date (in datetime) by default to the user we create 
                    
                    $userManager = new UserManager();
                    $lastRecording = $userManager->addUser($user);// add the post to the database and get the last id of the posts in the database via the return of the function
                    header('Location: /backend/editUser/'.$lastRecording.'?created=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/createUser?created=false');
                }
        }
        
        $formUser = new Form($user);
        $formUserType = new Form($userType);

        require('../app/Views/backViews/user/backCreateUserView.php');
    }

    /**
     * function use for road road  http://localhost:8000/backend/deleteUser/1 ou http://localhost:8000/backend/deleteUser/2 ou ....
     * will display the view backDeleteUserView.php  
     */
    function deleteUser($id)
    {
        Auth::check();
        
        $userManager = new UserManager();
        $user = $userManager->deleteUser($id);
        require('../app/Views/backViews/user/backDeleteUserView.php');
    }

// MEDIA
    /**
     * function use for road http://localhost:8000/backend/adminMedias
     * will display the view backAdminMediasView.php  
     */
    function adminMedias()
    {
        Auth::check();
        
        $mediaManager = new MediaManager();
        $listMedias = $mediaManager->getListMedias();
        require('../app/Views/backViews/media/backAdminMediasView.php ');
    }

    /**
     * function use for road road http://localhost:8000/backend/editMedia/1 ou http://localhost:8000/backend/editMedia/2 ou ....
     * will display the view backEditMediaView.php  
     */
    function editMedia($id)
    {
        Auth::check();

        $mediaManager = new MediaManager();
        $media = $mediaManager->getMedia($id);

        // pour afficher le contenu du select des type de media------------
        $mediaTypeManager = new MediaTypeManager();
        $list = $mediaTypeManager->list();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a post) has been made
            //for data validation
                $errors = [];

                // if(empty($_POST['title'])){
                //     $errors['title'][] = 'Le champs titre ne peut être vide';
                // }
                // if(mb_strlen($_POST['title'])<=3){
                //     $errors['title'][] = 'Le champs titre doit contenir plus de 3 caractere';
                // }

                if(empty($errors)){
                          
                    $media
                        ->setTitle($_POST['title'])
                        ->setIntroduction($_POST['introduction'])
                        ->setContent($_POST['content'])
                        ->setDateCreate($dateCreate)
                        ->setDateChange($dateChange)
                        ->setUser_id($_POST['user_id']);
    
                    $mediaManager->updateMedia($media);
                    header('Location: /backend/editMedia/'.$media->getId().'?success=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/editMedia/'.$media->getId().'?success=false');
                }
        }

        $form = new Form($media);

        require('../app/Views/backViews/media/backEditMediaView.php');


    }

    /**
     * function use for road http://localhost:8000/backend/createMedia
     * will display the view backCreateMediaView.php  
     */
    function createMedia()
    {
        Auth::check();

        $media = new Media();
        // $media->setUser_id(2); // ------POUR LE TESTE J ASSIGNE L UTILISATEUR "2" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a post) has been made
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
                    //modification pour gerer l enregistrement dans la base de donnee via le MediaManager

                    $media
                        ->setTitle($_POST['title'])
                        ->setIntroduction($_POST['introduction'])
                        ->setContent($_POST['content'])
                        ->setDateCreate($dateCreate)
                        ->setDateChange($dateChange)
                        ->setUser_id($_POST['user_id']);

                    $mediaManager = new MediaManager();
                    $lastRecording = $mediaManager->addMedia($media);// add the media to the database and get the last id of the medias in the database via the return of the function
                    header('Location: /backend/editPost/'.$lastRecording.'?created=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/createMedia?created=false');
                }
        }
        
        $form = new Form($media);

        require('../app/Views/backViews/media/backCreateMediaView.php');
    }

    /**
     * function use for road http://localhost:8000/backend/deleteMedia/1 ou http://localhost:8000/backend/deleteMedia/2 ou ....
     * will display the view backDeleteMediaView.php  
     */
    function deleteMedia($id)
    {
        Auth::check();
        
        $postManager = new PostManager();
        $post = $postManager->deletePost($id);
        require('../app/Views/backViews/media/backDeleteMediaView.php');
    }

// MEDIATYPE
    /**
     * function use for road http://localhost:8000/backend/adminMediaTypes
     * will display the view backAdminMediaTypesView.php  
     */
    function adminMediaTypes()
    {
        Auth::check();
        
        $mediaTypeManager = new MediaTypeManager();
        $listMediaTypes = $mediaTypeManager->getListMediatypes();
        require('../app/Views/backViews/mediaType/backAdminMediaTypesView.php ');
    }

    /**
     * function use for road road http://localhost:8000/backend/editMediaType/1 ou http://localhost:8000/backend/editMediaType/2 ou ....
     * will display the view backEditMediaTypeView.php  
     */
    function editMediaType($id)
    {
        Auth::check();

        $mediaTypeManager = new MediaTypeManager();
        $mediaType = $mediaTypeManager->getMediaType($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a post) has been made
            //for data validation
                $errors = [];

                // if(empty($_POST['title'])){
                //     $errors['title'][] = 'Le champs titre ne peut être vide';
                // }
                // if(mb_strlen($_POST['title'])<=3){
                //     $errors['title'][] = 'Le champs titre doit contenir plus de 3 caractere';
                // }

                if(empty($errors)){
                    $mediaType->setType($_POST['type']);
                    $mediaTypeManager->updateMediaType($mediaType);
                    header('Location: /backend/editMediaType/'.$mediaType->getId().'?success=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/editMediaType/'.$mediaType->getId().'?success=false');
                }
        }

        $form = new Form($mediaType);

        require('../app/Views/backViews/mediaType/backEditMediaTypeView.php');


    }

    /**
     * function use for road http://localhost:8000/backend/createMediaType
     * will display the view backCreateMediaTypeView.php  
     */
    function createMediaType()
    {
        Auth::check();

        $mediaType = new MediaType();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a post) has been made
            //for data validation
                $errors = [];
            
                // if(empty($_POST['title'])){
                //     $errors['title'][] = 'Le champs titre ne peut être vide';
                // }
                // if(mb_strlen($_POST['title'])<=3){
                //     $errors['title'][] = 'Le champs titre doit contenir plus de 3 caractere';
                // }
                
                if(empty($errors)){
                    $mediaType->setType($_POST['type']);
                    $mediaTypeManager = new MediaTypeManager();
                    $lastRecording = $mediaTypeManager->addMediaType($mediaType);// add the mediaType to the database and get the last id of the mediaTypes in the database via the return of the function
                    header('Location: /backend/editMediaType/'.$lastRecording.'?created=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/createMediaType?created=false');
                }
        }
        $form = new Form($mediaType);

        require('../app/Views/backViews/mediaType/backCreateMediaTypeView.php');
    }

    /**
     * function use for road road  http://localhost:8000/backend/deleteMediaType/1 ou http://localhost:8000/backend/deleteMediaType/2 ou ....
     * will display the view backDeleteMediaTypeView.php  
     */
    function deleteMediaType($id)
    {
        Auth::check();
        
        $mediaTypeManager = new MediaTypeManager();
        $mediaType = $mediaTypeManager->deleteMediaType($id);
        require('../app/Views/backViews/mediaType/backDeleteMediaTypeView.php');
    }