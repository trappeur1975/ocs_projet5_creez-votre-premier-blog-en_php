<?php
use App\Models\PostManager;

/**
 * function use for road http://localhost:8000
 * will display the view frontHomeView.php  
 */
function frontHome()
{
    // $postManager = new PostManager();
    // $listPosts = $postManager->getListPosts();
    require('../app/Views/frontViews/frontHomeView.php');
}

/**
 * function use for road http://localhost:8000/listposts
 * will display the view frontListPostsView.php  
 */
function listPosts()
{
    $postManager = new PostManager();
    /**
     * will be used in "listPostsView.php" in the foreach loop 
     * 
     * @ Post[] 
     * */
    $listPosts = $postManager->getListPosts();
    require('../app/Views/frontViews/frontListPostsView.php');
}

/**
 * function use for road http://localhost:8000/post/1 ou http://localhost:8000/post/2 ou ....
 * will display the view frontPostView.php  
 */
function post($id)
{
    $postManager = new PostManager(); // Création de l'objet manger de post
    $post = $postManager->getPost($id);
    require('../app/Views/frontViews/frontPostView.php'); //BON CHEMIN QUAND INDEX.PHP EST dans le dossier "public"
}