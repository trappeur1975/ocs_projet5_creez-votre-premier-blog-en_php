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
            
            if (!empty($_POST['login']) && !empty($_POST['password'])) {
                
                $userManager = new UserManager();

                try {
                    $userRegister = $userManager->findByUserLogin($_POST['login']);
                    
                    // we hashed the password 
                    $hashPasswords = hash('md5', $_POST['password']);

                    if ($userRegister->getPassword() === $hashPasswords) {

                        session_start();
                       
                        $_SESSION['connection'] = $userRegister->getId(); // creation of the session which records the id of user who has just connected 
                        
                        $userLogged = $userRegister;    // to have the user logger if later in the code we do not call the function "Auth :: check (['administrator'])" or "Auth :: sessionStart ()" 

                        if ($userManager->getUserSatus($_SESSION['connection'])['status'] === 'administrateur') {
                            header('Location: /backend/adminPosts');    // if user is administrator, he goes to the admin bachend 
                            return http_response_code(302);
                        }else if ($userManager->getUserSatus($_SESSION['connection'])['status'] === 'abonner' and !is_null($userRegister->getValidate())) {  // si le user qui se connect est de type "abonner" et que sont compte a était valider par l administrateur du site (=> validate ! null)
                            header('Location: /userFrontDashboard/'.$userRegister->getId());    // if user is a subscriber, he goes on his dashboard 
                            return http_response_code(302);
                        }else {
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

        require'../app/Views/backViews/backConnectionView.php';
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
     * Function use for road http://localhost:8000/backend/adminPosts
     * will display the view backAdminPostsView.php  
     */
    function adminPosts()
    {
        $userLogged = Auth::check(['administrateur']);
        
        $postManager = new PostManager();
        $listPosts = $postManager->getListPosts();
        
        require'../app/Views/backViews/post/backAdminPostsView.php';
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
       
        $mediaUploadImage = new Media(); // to have in the input field "alternative text of the media uploader" (create after) an empty field
        $formMediaUploadImage = new Form($mediaUploadImage); // use in "_form.php" of the "backendViews> post" folder
        
        $mediaUploadVideo = new Media();
        $formMediaUploadVideo = new Form($mediaUploadVideo);

        // traitement server et affichage des retours d'infos 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a creation of a post) has been made
            
            // for data validation
            $errors = [];

            // test de validation des champs du formulaire
                if (empty($_POST['title']) OR mb_strlen($_POST['title'])<=3) {
                    $errors[] = 'Le champ title ne peut être vide et doit contenir plus de 3 caracteres';
                }
                if (empty($_POST['introduction']) OR mb_strlen($_POST['introduction'])<=3) {
                    $errors[] = 'Le champ introduction ne peut être vide et doit contenir plus de 3 caracteres';
                }
                if (empty($_POST['content']) OR mb_strlen($_POST['content'])<=3) {
                    $errors[] = 'Le champ content ne peut être vide et doit contenir plus de 3 caracteres';
                }

            if (empty($errors)) {
            
                // modification to manage the record in the database via the Postmanager 
                $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['dateCreate']);// so that the date String is in Datetime 
                            
                // bdd recording of the post 
                $post
                    ->setTitle($_POST['title'])
                    ->setIntroduction($_POST['introduction'])
                    ->setContent($_POST['content'])
                    ->setDateCreate($dateCreate)
                    ->setDateChange($dateCreate)
                    ->setUser_id($_POST['user'])
                    ;

                $postManager = new PostManager();

                try{
                    $lastRecordingPost = $postManager->addPost($post);// add the post to the database and get the last id of the posts in the database via the return of the function
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                } 

                // media IMAGE
                if (isset($_FILES['mediaUploadImage']) AND $_FILES['mediaUploadImage']['error']== 0) {
                                                
                    // info variables 
                    $idMediaType = 1;   // image
                    
                    $file = $_FILES['mediaUploadImage']; // file uploader 
                    $storagePath = searchDatasFile('imageStoragePath')[1]; // storage path of the uploader file (see globalFunctions.php file) 
                    $name = 'mediaImage-'.pathinfo($file['name'])['filename'].'-'; 
                    $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + name of the uploader file (without its extension + unique identifier (via uniqid) to have a unique identifier
                    
                    $extension_upload = pathinfo($file['name'])['extension']; // to retrieve the extension of the uploader file 
                    $pathFile =  $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); //storage path with new name of the media uploader

                    // recording in bdd of the IMAGE media and of the uploader file on the server in the media folder 
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
                
                // media VIDEO
                if (!empty($_POST['mediaUploadVideo'])){
                    // VIDEO media bdd recording 
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
                        $errors[] = $e->getMessage();
                    }
                }
                
                setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file)
                
                header('Location: /backend/editPost/'.$lastRecordingPost.'?created=true');
                return http_response_code(302);

            }else{
                setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file)
                
                header('Location: /backend/createPost?created=false');
                return http_response_code(302);
            }
        }

        require'../app/Views/backViews/post/backCreatePostView.php';
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
        } catch (Exception $e) {    //in the event that we request a resource that does not exist (here a post id that does not exist) 
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
            require_once'../app/Views/errors.php';
            return http_response_code(302);
        }

        if ( $post->getDateChange() === null){
            $post->setDateChange(new Datetime()); //to assign today's date (in datetime) by default when to edit the post
        }
        $formPost = new Form($post, true);    //to be able to create the post form (thanks to the functions which create the fields) 

        // users
        $userManager = new UserManager();
        $user = $userManager->getUser($post->getUser_id());   // will be used in "$ formPost = new Form ($ post);" below which will allow you to create the fields specific to the $ post (via the "Form.php" entity) 
        $listUsers = $userManager->getListUsers();
        $listUsersSelect = $userManager->listUsersFormSelect($listUsers);   // will be used in "backView> post> _form.php" 
    

        $formUser = new Form($user, true);    // to create the select field of users which will be integrated in "backView> post> _form.php"      

        // media (image and video) 
        $mediaManager = new MediaManager();             
        $listMediasForUser = $mediaManager->getListMediasForUser($post->getUser_id());
        $listIdsMediaType = [1,3];  // image and video 
        $listMediasForUserForType = $mediaManager->getListMediasForUserForType($listMediasForUser, $listIdsMediaType);

        if (!empty($listMediasForUserForType)) {
            $media = $listMediasForUserForType[0]; // we retrieve the first user media from the post which will be used in "$ formMedia = new Form ($ media);" below which will allow you to create the fields specific to $ media (via the "Form.php" entity) 
            $formMediasImageSelect = new Form($media);  // to create the media select field which will be integrated in "backView> post> _form.php" 
        }

        //utiliser dans "backviews > post > _form.php" 
        $listMediasForUserSelect =  $mediaManager->listMediasFormSelect($listMediasForUserForType); // we display the media list of the user author of the post (only images and videos)
        $listMediasForPostSelect =  $mediaManager->getIdOftListMediasActifForPost($post->getId());// we get the media list for this $ post 

        $mediaUploadImage = new Media();
        $formMediaUploadImage = new Form($mediaUploadImage);  // to create the input field "alternative text of the media uploader" which will be integrated in "backView> post> _form.php" 

        $mediaUploadVideo = new Media();
        $formMediaUploadVideo = new Form($mediaUploadVideo);
       
        // server processing and display of feedbacks 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a post) has been made

            //validation test of the form fields 
            if (empty($_POST['title']) OR mb_strlen($_POST['title'])<=3) {
                $errors[] = 'Le champ title ne peut être vide et doit contenir plus de 3 caracteres';
            }
            if (empty($_POST['introduction']) OR mb_strlen($_POST['introduction'])<=3) {
                $errors[] = 'Le champ introduction ne peut être vide et doit contenir plus de 3 caracteres';
            }
            if (empty($_POST['content']) OR mb_strlen($_POST['content'])<=3) {
                $errors[] = 'Le champ content ne peut être vide et doit contenir plus de 3 caracteres';
            }

            if (empty($errors)) {
                                
                // modification to manage the record in the database via the Postmanager 
                $dateCreate = DateTime::createFromFormat('Y-m-d H:i:s',$_POST['dateCreate']); // so that the date String is in Datetime 
                
                $dateChange = $_POST['dateChange'];
                if ($_POST['dateChange'] === '') {
                    $dateChange=NULL;
                }
        
                // save changes (via user select) post info 
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
                        $errors[] = $e->getMessage();
                    }

                // -------- recording of changes (via media select and media upload) media info linked to the edited post 
                    // this will be used later to know if the user at the origin of the post has been modified 
                    $userOrigine = $user;
                    $newUser = $userManager->getUser($post->getUser_id());

                    // if the user has been modified, we deactivate the media linked to this post 
                    if ($userOrigine != $newUser){
                        foreach($listMediasForPostSelect as $value){                           
                            $statutActif = 0; //false
                            $mediaManager->updateStatutActifMedia($value, $statutActif); 
                        }
                    }

                    if ($userOrigine == $newUser) { // we save the new media list for the post defined in the media select only if the user has not changed 
                        // addition of the media if an image upload was made during the edit of the post 
                        if (isset($_FILES['mediaUploadImage']) AND $_FILES['mediaUploadImage']['error']== 0) {
                            // info variables 
                            $idMediaType = 1;   //image

                            $file = $_FILES['mediaUploadImage']; //file uploader 
                            $storagePath = searchDatasFile('imageStoragePath')[1]; //storage path of the uploader file (see globalFunctions.php file)
                            $name = 'mediaImage-'.pathinfo($file['name'])['filename'].'-';
                            $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + name of the uploader file (without its extension + unique identifier (via uniqid) to have a unique identifier
                            
                            $extension_upload = pathinfo($file['name'])['extension']; //to retrieve the extension of the uploader file    
                            $pathFile =  $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); //storage path with new name of the media uploader 

                            // recording in bdd of the IMAGE media and of the uploader file on the server in the media folder          
                            $mediaUploadImage
                                ->setPath($pathFile)
                                ->setAlt($_POST['altFileMediaImage'])
                                ->setStatutActif(1) //actif
                                ->setMediaType_id($idMediaType)
                                ->setPost_id($post->getId())
                                ->setUser_id($_POST['user'])
                                ;
                            
                            try{
                                $mediaManager->addMediaImage($mediaUploadImage, CONFIGFILE, $file); //adding the media to the database and recovery via the id function of the last media in the database
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }
                        }          
                        
                        // addition of the media if a video upload was made during the edit of the post
                        if (!empty($_POST['mediaUploadVideo'])){
                            // VIDEO media bdd recording 
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
                                $errors[] = $e->getMessage();
                            } 
                        }
                        
                        // we put all the post's media in Active status = false 
                        foreach($listMediasForPostSelect as $value){                           
                            $statutActif = 0; //false
                            $mediaManager->updateStatutActifMedia($value, $statutActif); 
                        }
                        
                        // we put all the media whose id are in "$ _POST ['path']" in statusActif = true 
                        // and we modify their post_id to properly attribute to the media selected in the select the post id 
                        foreach($_POST['path'] as $value){
                            $statutActif = 1; //true
                            $mediaManager->updateStatutActifMedia($value, $statutActif);
                            try{
                                $mediaManager->updatePostIdMedia($value, $post->getId());
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }
                        }
                    }

                    // ATTENTION WE MODIFY THE USERORIGIN so that our post user change check is still valid 
                    $userOrigine = $newUser;
                
                // --------------END recording of the modifications (via the media select) of the information on the media linked to the post 
                
                setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 

                header('Location: /backend/editPost/'.$post->getId().'?success=true');
                return http_response_code(302);

            }else{
                
                setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 

                header('Location: /backend/editPost/'.$post->getId().'?success=false');
                return http_response_code(302);
            }
        }

        require'../app/Views/backViews/post/backEditPostView.php';
    }

    /**
     * function use for road road  http://localhost:8000/backend/deletePost/1 ou http://localhost:8000/backend/deletePost/2 ou ....
     * will display the view backDeletePostView.php  
     */
    function deletePost($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];
 
        // we delete the comments linked to the post (if there are any) 
        $commentManager = new CommentManager();
        $listCommentsDelete = $commentManager->getListCommentsForPost($id);
        
        if ($listCommentsDelete !== []) {
            foreach($listCommentsDelete as $comment){
                try{
                    $commentManager->deleteComment($comment->getId());    //deletion from the database 
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        // we delete the media linked to the post (if there is any) 
        $mediaManager = new MediaManager();
        $listMediasDelete =  $mediaManager->getListMediasForPost($id);// we get the media list for this $ post 
        
        if ($listMediasDelete !== []) {
            foreach($listMediasDelete as $media){
                try{
                    unlink($media->getPath());  //delete media on the server in the media folder 
                    $mediaManager->deleteMedia($media->getId());    //deletion from the database 
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                } 
            }
        }
        
        // we delete the post 
        $postManager = new PostManager();
        try{
            $post = $postManager->deletePost($id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        } 

        setFlashErrors($errors);

        require'../app/Views/backViews/post/backDeletePostView.php';
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
        require'../app/Views/backViews/user/backAdminUsersView.php';
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

        require'../app/Views/backViews/user/backAdminUsersWaiteValidateView.php';
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
        
        $userManager = new UserManager();

        $formUser = new Form($user);

        // userType
        $userType = new UserType();
        $formUserType = new Form($userType);

        $userTypeManager = new UserTypeManager(); 
        $listUserTypes = $userTypeManager->getListUserTypes();
        $listUserTypesSelect = $userTypeManager-> listUserTypesFormSelect($listUserTypes); // to display the content of the select of the usertypes, will be used in "backView> user> _form.php" 

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
            
                //validation test of the form fields 
                    if (empty($_POST['firstName']) OR mb_strlen($_POST['firstName'])<=3) {
                        $errors[] = 'Le champ firstName ne peut être vide et doit contenir plus de 3 caracteres';
                    }
                    if (empty($_POST['lastName']) OR mb_strlen($_POST['lastName'])<=3) {
                        $errors[] = 'Le champ lastName ne peut être vide et doit contenir plus de 3 caracteres';
                    }

                    if (empty($_POST['email']) OR strpos($_POST['email'], '@') === false) {
                        $errors[] = 'Le champ email ne peut être vide ou l\'ecriture de votre adresse email est incorrect';
                    }
                    $idUserIidenticalData1 = $userManager->identicalDataSearch('email', $_POST['email']);
                    if (!is_null($idUserIidenticalData1)) {
                        $errors[] = 'Votre email a été déjà utilisé, vous devez en indiquer un autre';
                    }
                
                    if (empty($_POST['login']) OR mb_strlen($_POST['login'])<=3) {
                        $errors[] = 'Le champ login ne peut être vide et doit contenir plus de 3 caracteres';
                    }
                    
                    $idUserIidenticalData2 = $userManager->identicalDataSearch('login', $_POST['login']);
                    if (!is_null($idUserIidenticalData2)) {
                        $errors[] = 'Votre login a été déjà utilisé, vous devez en indiquer un autre';
                    }

                    if (empty($_POST['password']) OR mb_strlen($_POST['password'])<=3) {
                        $errors[] = 'Le champ password ne peut être vide et doit contenir plus de 3 caracteres';
                    }

                if (empty($errors)) {

                    // we hashed the password
                    $hashPsswords = hash('md5', $_POST['password']);

                    // user database recording 
                    $user
                        ->setFirstName($_POST['firstName'])
                        ->setLastName($_POST['lastName'])
                        ->setEmail($_POST['email'])
                        ->setSlogan($_POST['slogan'])
                        ->setLogin($_POST['login'])
                        ->setPassword($hashPsswords)
                        ->setUserType_id($_POST['userType_id'][0]); //because this data comes from a multiple select 
                    
                    try{
                        $lastRecordingUser = $userManager->addUser($user);// add the user to the database and get the last id of the users in the database via the return of the function
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                    
                    // bdd recording of the media logo and the uploader file on the server in the media folder 
                    if (isset($_FILES['mediaUploadLogo']) AND $_FILES['mediaUploadLogo']['error']== 0) {
                        
                        // info variables 
                        $idMediaType = 2;   // logo

                        $file = $_FILES['mediaUploadLogo']; // file uploader 
                        $storagePath = searchDatasFile('imageStoragePath')[1]; // storage path of the uploader file (see globalFunctions.php file)       
                        $name = 'mediaLogo-'.pathinfo($file['name'])['filename'].'-'; 
                        $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + name of the uploader file (without its extension + unique identifier (via uniqid) to have a unique identifier 

                        $extension_upload = pathinfo($file['name'])['extension']; // to retrieve the extension of the uploader file    
                        $pathFile =  $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); // storage path with new name of the media uploader 

                        // LOGO media bdd recording 
                        $mediaUploadLogo
                            ->setPath($pathFile)
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
                    
                    // socialNetwork bdd recording 
                    if (!empty($_POST['socialNetwork'])) {
                        
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

                    setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 

                    header('Location: /backend/editUser/'.$lastRecordingUser.'?created=true');
                    return http_response_code(302);

                }else{
                    
                    setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
                    
                    header('Location: /backend/createUser?created=false');
                    return http_response_code(302);

                }
        }
        
        require'../app/Views/backViews/user/backCreateUserView.php';
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
        } catch (Exception $e) {    // in the event that we request a resource that does not exist (here an id of the user that does not exist) 
            $errors[] = $e->getMessage();
            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
            require_once'../app/Views/errors.php';
            return http_response_code(302);
        }

        $originalPassword = $user->getPassword();
        
        $formUser = new Form($user, true);

        // userType
        $userTypeManager = new UserTypeManager();
        $userType = $userTypeManager->getUserType($user->getUserType_id()); // will be used in "$ formUserType = new Form ($ userType);" which creates the fields specific to the userType (via the "Form.php" entity) which will themselves be integrated to integrate them (in whole or in part) in "$ formUser = new Form ($ user);" below which will allow you to create the fields specific to the $ user (via the "Form.php" entity) 
        
        $listUserTypes = $userTypeManager->getListUserTypes();
        $listUserTypesSelect = $userTypeManager-> listUserTypesFormSelect($listUserTypes); // to display the content of the select of the usertypes, will be used in "backView> user> _form.php" 
        
        $formUserType = new Form($userType);

        // media (logo)
        $mediaManager = new MediaManager();
       
        $listMediasForUser = $mediaManager->getListMediasForUser($user->getId());
        $listIdsMediaType = [2];  //logo
        $listLogos = $mediaManager->getListMediasForUserForType($listMediasForUser, $listIdsMediaType); // to retrieve the user's logo 
        
        if (!empty($listLogos)) {
            $logoUser = $listLogos[0];
            $formMediaLogoUser = new Form($logoUser);  // to have in the input field to upload a logo 
        }

        $mediaUploadLogo = new Media();
        $formMediaUploadLogo = new Form($mediaUploadLogo);  //to have in the input field to upload a logo 
            
        // socialNetwork
        $socialNetworkManager = new SocialNetworkManager();
        $socialNetwork = new SocialNetwork();
        $formSocialNetwork = new Form($socialNetwork);

        $listSocialNetworksForUser = $socialNetworkManager->getListSocialNetworksForUser($user->getId());
        $listSocialNetworksForUserForSelect =  $socialNetworkManager->listSocialNetworksFormSelect($listSocialNetworksForUser); // we display the list of social networks of the user 
        
        if (!empty($listSocialNetworksForUser)) {
            $socialNetworkForSelect = $listSocialNetworksForUser[0];
            $formSocialNetworkSelect = new Form($socialNetworkForSelect);
        }
        
        // server processing and display of feedbacks 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification of a user) has been made

            //validation test of the form fields 
                if (empty($_POST['firstName']) OR mb_strlen($_POST['firstName'])<=3) {
                    $errors[] = 'Le champ firstName ne peut être vide et doit contenir plus de 3 caracteres';
                }
                if (empty($_POST['lastName']) OR mb_strlen($_POST['lastName'])<=3) {
                    $errors[] = 'Le champ lastName ne peut être vide et doit contenir plus de 3 caracteres';
                }

                if (empty($_POST['email']) OR strpos($_POST['email'], '@') === false) {
                    $errors[] = 'Le champ email ne peut être vide ou l\'ecriture de votre adresse email est incorrect';
                }
                $idUserIidenticalData1 = $userManager->identicalDataSearch('email', $_POST['email']);
                if (!is_null($idUserIidenticalData1) AND $idUserIidenticalData1 != $id) {
                    $errors[] = 'Votre email a été déjà utilisé, vous devez en indiquer un autre';
                }

                if (empty($_POST['login']) OR mb_strlen($_POST['login'])<=3) {
                    $errors[] = 'Le champ login ne peut être vide et doit contenir plus de 3 caracteres';
                }

                $idUserIidenticalData2 = $userManager->identicalDataSearch('login', $_POST['login']);
                if (!is_null($idUserIidenticalData2) AND $idUserIidenticalData2 != $id) {
                    $errors[] = 'Votre login a été déjà utilisé, vous devez en indiquer un autre';
                }

                if (empty($_POST['password']) OR mb_strlen($_POST['password'])<=3) {
                    $errors[] = 'Le champ password ne peut être vide et doit contenir plus de 3 caracteres';
                }

            if (empty($errors)) {
            
                // the password is re-hashed only if it has been modified by the user 
                if ($originalPassword !== $_POST['password']) {
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
                    ->setPassword($hashPsswords)
                    ->setUserType_id($_POST['userType_id'][0]); //car cette donnee est issu d'un select multiple
                
                try{
                    $userManager->updateUser($user);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

                // bdd recording of the media logo and the uploader file on the server in the media folder 
                if (isset($_FILES['mediaUploadLogo']) AND $_FILES['mediaUploadLogo']['error']== 0) {
                    
                    // info variables 
                    $idMediaType = 2;   // logo
                    
                    $file = $_FILES['mediaUploadLogo']; // fichier uploader
                    $storagePath = searchDatasFile('imageStoragePath')[1]; // storage path of the uploader file (see globalFunctions.php file) 
                    $name = 'mediaLogo-'.pathinfo($file['name'])['filename'].'-'; 
                    $newNameUploaderFile = uniqid($name , true);    // concatenation "media-" + name of the uploader file (without its extension + unique identifier (via uniqid) to have a unique identifier 

                    $extension_upload = pathinfo($file['name'])['extension']; //to retrieve the extension of the uploader file   
                    $pathFile =  $storagePath.basename($newNameUploaderFile.'.'.$extension_upload); // storage path with new name of the media uploader 
                    
                    // we delete in the database as well as on the server in the media folder the old logo of the user   
                    $listMediasForUser = $mediaManager->getListMediasForUser($user->getId());
                    $listLogosDelete = $mediaManager->getListMediasForUserForType($listMediasForUser, $listIdsMediaType);   // we retrieve the list of user logos 
                    
                    if (!empty($listLogosDelete)) {
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
                        ->setUser_id($user->getId())
                        ;
                    
                    try{
                        $mediaManager->addMediaImage($mediaUploadLogo, CONFIGFILE, $file); //adding the media to the database and recovery via the id function of the last media in the database
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    } 
                }

                // saving in socialNetwork database of changes made in editUser ()   
                    // deletion of the user's social network (s) 
                    if (!empty($_POST['socialNetworksUser'])) { 
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
                    if (!empty($_POST['socialNetwork'])) {
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
                
                setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 

                header('Location: /backend/editUser/'.$user->getId().'?success=true');
                return http_response_code(302);

            }else{
                
                setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 

                header('Location: /backend/editUser/'.$user->getId().'?success=false');
                return http_response_code(302);
            }
        }

        require'../app/Views/backViews/user/backEditUserView.php';
    }

    /**
     * function use for road road  http://localhost:8000/backend/deleteUser/1 ou http://localhost:8000/backend/deleteUser/2 ou ....
     * will display the view backDeleteUserView.php  
     */
    function deleteUser($id)
    {
        
        $userLogged = Auth::check(['administrateur']);

        $errors = [];
  
        // deletion of all user comments from the database  
        $commentManager = new CommentManager();
        $listCommentsDelete = $commentManager->listCommentsForUser($id);

        if ($listCommentsDelete !== []) {
            foreach($listCommentsDelete as $comment){
                try{
                    $commentManager->deleteComment($comment->getId());  //deletion from the database 
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }

        // deletion of all user-related media (logos, deactivate image, ...) to delete them from the server (media folder) and from the database 
        $mediaManager = new MediaManager();
        $listMedias = $mediaManager->getListMediasForUser($id); // we retrieve the list of user logos 

        if (!empty($listMedias)) {
            foreach($listMedias as $media){
                try{
                    unlink($media->getPath());  //delete media on the server in the media folder 
                    $mediaManager->deleteMedia($media->getId());    //deletion from the database 
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }   
            }
        }

        // deletion of the database of all the user's socialNetworks 
        $socialNetworkManager = new SocialNetworkManager();
        $listSocialNetworksForUserDelete = $socialNetworkManager->getListSocialNetworksForUser($id);
 
        if (!empty($listSocialNetworksForUserDelete)) {
            foreach($listSocialNetworksForUserDelete as $socialnetwork){
                try
                { 
                    $socialNetworkManager->deleteSocialNetwork($socialnetwork->getId());    //deletion from the database 
                }
                catch (Exception $e)
                {
                    $errors[] = $e->getMessage();
                } 
            }
        }

        //deletion of all post related to use 
        $postManager = new PostManager();
        $listPostsForUser = $postManager->getListPostsForUser($id);
        
        if (!empty($listPostsForUser)) {
            foreach($listPostsForUser as $post){    // deletion of all posts (and their associated media) from the user 
                
                // we delete the media linked to the post (if there is any) 
                $listMediasDelete =  $mediaManager->getListMediasForPost($post->getId());// on recupere la liste des media pour ce $post

                if ($listMediasDelete !== []) {
                    foreach($listMediasDelete as $media){
                        try{
                            unlink($media->getPath());  //delete media on the server in the media folder 
                            $mediaManager->deleteMedia($media->getId());    //deletion from the database 
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        } 
                    }
                }

                // we delete the comments linked to the post (if there are any) 
                $listCommentsDelete =  $commentManager->getListCommentsForPost($post->getId());// we get the list of comments for this $ post 
                
                if ($listCommentsDelete !== []) {
                    foreach($listCommentsDelete as $comment){
                        try{
                            $commentManager->deleteComment($comment->getId());    //deletion from the database 
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
        $userManager = new UserManager();

        try{
            $user = $userManager->deleteUser($id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();

        }

        setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 

        require'../app/Views/backViews/user/backDeleteUserView.php';
    }

    /**
     * function use for road http://localhost:8000/backend/validateUser/1 ou http://localhost:8000/backend/validateUser/2 ou ....
     * will display the view backValidateUserView.php  
     */
    function validateUser($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];

        // we validate the comment 
        $usertManager = new userManager();
        try{
            $usertManager->validateUser($id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();

            setFlashErrors($errors);
            require_once'../app/Views/errors.php';
            return http_response_code(302);
        }  
        
        require'../app/Views/backViews/user/backValidateUserView.php';
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

        require'../app/Views/backViews/comment/backAdminCommentsWaiteValidateView.php';
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

        require'../app/Views/backViews/comment/backEditCommentsPostView.php';
    }

    /**
     * function use for road road  http://localhost:8000/backend/deleteComment/1 ou http://localhost:8000/backend/deleteComment/2 ou ....
     * will display the view backDeleteCommentView.php  
     */
    function deleteComment($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];
    
        // we delete the comment 
        $commentManager = new CommentManager();
        try{
            $comment = $commentManager->deleteComment($id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();

            setFlashErrors($errors);    // pour gerer les erreurs en message flash (voir fichier globalFunctions.php)
            require_once'../app/Views/errors.php';
            return http_response_code(302);
            
        }

        setFlashErrors($errors);

        require'../app/Views/backViews/comment/backDeleteCommentView.php';
    }

    /**
     * function use for road http://localhost:8000/backend/validateComment/1 ou http://localhost:8000/backend/validateComment/2 ou ....
     * will display the view backValidateCommentView.php  
     */
    function validateComment($id)
    {
        $userLogged = Auth::check(['administrateur']);

        $errors = [];
    
        // we validate the comment 
        $commentManager = new CommentManager();
        try{
            $comment = $commentManager->validateComment($id);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();

            setFlashErrors($errors);    // to manage flash message errors (see globalFunctions.php file) 
            require_once'../app/Views/errors.php';
            return http_response_code(302);
        }  
        
        require'../app/Views/backViews/comment/backValidateCommentView.php';  
    }