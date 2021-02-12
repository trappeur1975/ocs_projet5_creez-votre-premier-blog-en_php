<?php

use App\Entities\Auth;
use App\Entities\Post;
use App\Models\PostManager;

/**
 * function use for road http://localhost:8000/backend
 * will display the view backendView.php  
 */
function backHome()
{
    // $postManager = new PostManager();
    // $listPosts = $postManager->getListPosts();
    require('../app/Views/backViews/backHomeView.php');
}

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

// -------------------------------

/**
 * function use for road road http://localhost:8000/backend/editPost/1 ou http://localhost:8000/backend/editPost/2 ou ....
 * will display the view backEditPostView.php  
 */
function editPost($id)
{
    $postManager = new PostManager();
    $post = $postManager->getPost($id);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification) has been made 
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

    require('../app/Views/backViews/post/backEditPostView.php');
}

/**
 * function use for road http://localhost:8000/backend/createPost
 * will display the view backCreatePostView.php  
 */
function createPost()
{
    $post = new Post();
    $post->setDateCreate(new Datetime()); //to assign today's date (in datetime) by default to the post we create 
    $post->setDatechange(NULL); // ------POUR LE TESTE J ASSIGNE LA DATECHANGE A "NULL" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------
    $post->setUser_id(2); // ------POUR LE TESTE J ASSIGNE L UTILISATEUR "2" VOIR APRES COMMENT FAIRE POUR GERER CELA --------------

    if ($_SERVER['REQUEST_METHOD'] === 'POST') { // if a submission of the form (=> a modification) has been made 
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
                // exit();
            }else{
                // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                header('Location: /backend/createPost?created=false');
            }
    }
    require('../app/Views/backViews/post/backCreatePostView.php');
}

