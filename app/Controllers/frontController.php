<?php
use App\Models\PostManager;

/**
 * function use for route http: // localhost: 8000 / listposts
 * will display the view listPostsView.php  
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
    require('../app/Views/frontViews/listPostsView.php');
}

function post($id)
{
    $postManager = new PostManager(); // Création de l'objet manger de post
    $post = $postManager->getPost($id);

    require('../app/Views/frontViews/postView.php'); //BON CHEMIN QUAND INDEX.PHP EST dans le dossier "public"
    //require('app/views/frontViews/postView.php'); // BON CHEMIN QUAND INDEX.PHP EST A LA RACINE DU PROJET
}
// URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=post&id=1
// function post()
// {
//     $postManager = new PostManager(); // Création de l'objet manger de post
//     $post = $postManager->getPost($_GET["id"]);

//     require('../app/Views/frontViews/postView.php');
// }

