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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $post->setTitle($_POST['title']);
        $postManager->updatePost($post);
        // $success = true; // LE GERER EN ATTRIBUT DANS URL
        header('Location: /backend/editPost/'.$post->getId().'?success=true');
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

