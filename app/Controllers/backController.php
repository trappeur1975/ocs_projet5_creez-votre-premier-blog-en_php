<?php

use App\Entities\Auth;
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
    require('../app/Views/backViews/backAdminPostsView.php');
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
    require('../app/Views/backViews/backDeletePostView.php');
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
                $post
                    ->setTitle($_POST['title'])
                    ->setIntroduction($_POST['introduction']);
                // dd($post);
                $postManager->updatePost($post);
                header('Location: /backend/editPost/'.$post->getId().'?success=true');
            }else{
                // ISSUE COMMENT TRANSMETTRE UN TABLEAU $errors=[]; DANS LA REDIRECTION CI DESSOUS POUR AFFICHER DANS LA VIEW LES DIFFERENTES ERREORS
                header('Location: /backend/editPost/'.$post->getId().'?success=false');
            }
    }

    require('../app/Views/backViews/backEditPostView.php');
}

/**
 * function use for road http://localhost:8000/backend/createPost
 * will display the view backCreatePostView.php  
 */
function createPost()
{
    // $postManager = new PostManager();
    // $listPosts = $postManager->getListPosts();
    require('../app/Views/backViews/backCreatePostView.php');
}

