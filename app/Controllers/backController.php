<?php
// use App\Models\PostManager;

/**
 * function use for route http://localhost:8000/backend
 * will display the view backendView.php  
 */
function backend()
{
    // $postManager = new PostManager();
    // $listPosts = $postManager->getListPosts();
    require('../app/Views/backViews/backendView.php');
}