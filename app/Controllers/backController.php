<?php

use App\Entities\Auth;
use App\Entities\Form;
use App\Entities\MediaType;
use App\Entities\Post;
use App\Entities\User;
use App\Models\PostManager;
use App\Models\UserManager;
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
            
            if(!empty($_POST['login']) && !empty($_POST['login'])){
                
                $userManager = new UserManager();

                try {
                    $userRegister = $userManager->findByUserLogin($_POST['login']);
                    if($userRegister->getPassword() ===  $_POST['password']){
                        session_start();
                        $_SESSION['connection'] = 'administrateur'. // --------------POUR LE TESTE APRES IL FAUTDRA FAIRE UN TRAITEMENT POUR RECUPERER LE TYPE DE L'USER QUI TENTE DE SE CONNECTER
                        header('Location: /backend/adminPosts'); //ISSUE faudra changer cela (ce qu il y a en php) avec l utilisation des nom de route
                        // echo 'code correct';
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
        // Auth::check();
        
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
                
                    //ISSSUE  gestion des date en datetime dans entité post // base de donnee en string pour la create ou l edit d un post (=>voir methode setDateCreate($dateCreate) de la class Post)
                    //modification pour gerer l enregistrement dans la base de donnee via le Postmanager
                    $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['dateCreate']); // pour que la date String soit en Datetime
                    $dateChange = $_POST['dateChange'];

                    if($_POST['dateChange'] === ''){
                        $dateChange=NULL;
                    }
                
                    $post
                        ->setTitle($_POST['title'])
                        ->setIntroduction($_POST['introduction'])
                        ->setContent($_POST['content'])
                        ->setDateCreate($dateCreate)
                        ->setDateChange($dateChange)
                        ->setUser_id($_POST['user_id']);
    
                    $postManager->updatePost($post);
                    header('Location: /backend/editPost/'.$post->getId().'?success=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/editPost/'.$post->getId().'?success=false');
                }
        }

        $form = new Form($post);

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
        $post->setUser_id(2); // ------POUR LE TESTE J ASSIGNE L UTILISATEUR "2" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------

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
                    $dateChange = $_POST['dateChange'];

                    if($_POST['dateChange'] === ''){
                        $dateChange=NULL;
                    }

                    $post
                        ->setTitle($_POST['title'])
                        ->setIntroduction($_POST['introduction'])
                        ->setContent($_POST['content'])
                        ->setDateCreate($dateCreate)
                        ->setDateChange($dateChange)
                        ->setUser_id($_POST['user_id']);

                    $postManager = new PostManager();
                    $lastRecording = $postManager->addPost($post);// add the post to the database and get the last id of the posts in the database via the return of the function
                    header('Location: /backend/editPost/'.$lastRecording.'?created=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/createPost?created=false');
                }
        }
        $form = new Form($post);

        require('../app/Views/backViews/post/backCreatePostView.php');
    }

    /**
     * function use for road road  http://localhost:8000/backend/deletePost/1 ou http://localhost:8000/backend/deletePost/2 ou ....
     * will display the view backDeletePostView.php  
     */
    function deletePost($id)
    {
        Auth::check();
        
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
                    // $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['dateCreate']); // pour que la date String soit en Datetime
                    $validate = $_POST['validate'];

                    if($_POST['validate'] === ''){
                        $validate=NULL;
                    }
                
                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        ->setPicture($_POST['picture'])
                        ->setLogo($_POST['logo'])
                        ->setSlogan($_POST['slogan'])
                        ->setSocialNetworks($_POST['socialNetworks'])
                        ->setLogin($_POST['login'])
                        ->setPassword($_POST['password'])
                        ->setValidate($validate);
    
                    // $post
                    //     ->setTitle($_POST['title'])
                    //     ->setIntroduction($_POST['introduction'])
                    //     ->setContent($_POST['content'])
                    //     ->setDateCreate($dateCreate)
                    //     ->setDateChange($dateChange)
                    //     ->setUser_id($_POST['user_id']);
                    
                    $userManager->updateUser($user);
                    header('Location: /backend/editUser/'.$user->getId().'?success=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/editUser/'.$user->getId().'?success=false');
                }
        }

        $form = new Form($user);

        require('../app/Views/backViews/post/backEditUserView.php');


    }

    /**
     * function use for road http://localhost:8000/backend/createUser
     * will display the view backCreateUserView.php  
     */
    function createUser()
    {
        Auth::check();

        $user = new user();
        // $user->setValidate(new Datetime()); //to assign today's date (in datetime) by default to the user we create 
        
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
                    //modification pour gerer l enregistrement dans la base de donnee via le Postmanager
                    // $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['dateCreate']); // pour que la date String soit en Datetime
                    // $dateChange = $_POST['dateChange'];

                    // if($_POST['dateChange'] === ''){
                    //     $dateChange=NULL;
                    // }

                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        // ->setPicture($_POST['picture'])
                        // ->setLogo($_POST['logo'])
                        ->setSlogan($_POST['slogan'])
                        // ->setSocialNetworks($_POST['socialNetworks'])
                        ->setLogin($_POST['login'])
                        ->setPassword($_POST['password'])
                        ->setValidate(new Datetime()); //to assign today's date (in datetime) by default to the user we create 

                    $userManager = new UserManager();
                    $lastRecording = $userManager->addUser($user);// add the post to the database and get the last id of the posts in the database via the return of the function
                    header('Location: /backend/editUser/'.$lastRecording.'?created=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/createUser?created=false');
                }
        }
        
        $form = new Form($user);

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

        $postManager = new PostManager();
        $post = $postManager->getPost($id);
        
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
                
                    //ISSSUE  gestion des date en datetime dans entité post // base de donnee en string pour la create ou l edit d un post (=>voir methode setDateCreate($dateCreate) de la class Post)
                    //modification pour gerer l enregistrement dans la base de donnee via le Postmanager
                    $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['dateCreate']); // pour que la date String soit en Datetime
                    $dateChange = $_POST['dateChange'];

                    if($_POST['dateChange'] === ''){
                        $dateChange=NULL;
                    }
                
                    $post
                        ->setTitle($_POST['title'])
                        ->setIntroduction($_POST['introduction'])
                        ->setContent($_POST['content'])
                        ->setDateCreate($dateCreate)
                        ->setDateChange($dateChange)
                        ->setUser_id($_POST['user_id']);
    
                    $postManager->updatePost($post);
                    header('Location: /backend/editPost/'.$post->getId().'?success=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/editPost/'.$post->getId().'?success=false');
                }
        }

        $form = new Form($post);

        require('../app/Views/backViews/post/backEditPostView.php');


    }

    /**
     * function use for road http://localhost:8000/backend/createMedia
     * will display the view backCreateMediaView.php  
     */
    function createMedia()
    {
        Auth::check();

        $post = new Post();
        $post->setDateCreate(new Datetime()); //to assign today's date (in datetime) by default to the post we create 
        $post->setDatechange(NULL); // ------POUR LE TESTE J ASSIGNE LA DATECHANGE A "NULL" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------
        $post->setUser_id(2); // ------POUR LE TESTE J ASSIGNE L UTILISATEUR "2" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------

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
                    $dateChange = $_POST['dateChange'];

                    if($_POST['dateChange'] === ''){
                        $dateChange=NULL;
                    }

                    $post
                        ->setTitle($_POST['title'])
                        ->setIntroduction($_POST['introduction'])
                        ->setContent($_POST['content'])
                        ->setDateCreate($dateCreate)
                        ->setDateChange($dateChange)
                        ->setUser_id($_POST['user_id']);

                    $postManager = new PostManager();
                    $lastRecording = $postManager->addPost($post);// add the post to the database and get the last id of the posts in the database via the return of the function
                    header('Location: /backend/editPost/'.$lastRecording.'?created=true');
                }else{
                    // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                    header('Location: /backend/createPost?created=false');
                }
        }
        
        $form = new Form($post);

        require('../app/Views/backViews/post/backCreatePostView.php');
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
        require('../app/Views/backViews/post/backDeletePostView.php');
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