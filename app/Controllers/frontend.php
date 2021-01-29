<?php
use App\Models\PostManager;

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

// A MODIFIER POUR GERER L HYDRATATION
// URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=listPosts
function listPosts()
{
    $postManager = new PostManager(); // Création d'un objet
    $listPosts = $postManager->getListPosts(); // Appel d'une fonction de cet objet

    require('../app/Views/frontend/listPostsView.php');
}