<?php
use App\Models\PostManager;

/**
 * function use for route http: // localhost: 8000 / listposts
 * will display the view listPostsView.php  
 */
function listPosts()
{
    $postManager = new PostManager();
    $listPosts = $postManager->getListPosts(); // $listPosts sera utiliser dans listPostsView.php dans la boucle foreach
    require('../app/Views/frontend/listPostsView.php');
}

function post($id)
{
    $postManager = new PostManager(); // Création de l'objet manger de post
    $post = $postManager->getPost($id);

    require('../app/Views/frontend/postView.php'); //BON CHEMIN QUAND INDEX.PHP EST dans le dossier "public"
    //require('app/views/frontend/postView.php'); // BON CHEMIN QUAND INDEX.PHP EST A LA RACINE DU PROJET
}
// URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=post&id=1
// function post()
// {
//     $postManager = new PostManager(); // Création de l'objet manger de post
//     $post = $postManager->getPost($_GET["id"]);

//     require('../app/Views/frontend/postView.php');
// }

