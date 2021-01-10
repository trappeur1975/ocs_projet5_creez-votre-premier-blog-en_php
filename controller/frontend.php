<?php

use \Ocs\Blog\Model\PostManager;

require_once('model/PostManager.php');

// URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=post&id=1
function post()
{
    $postManager = new PostManager(); // Création de l'objet manger de post
    $post = $postManager->getPost($_GET["id"]);

    require('./view/frontend/postView.php');
}

// A MODIFIER POUR GERER L HYDRATATION
// URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=listPosts
function ListPosts()
{
    $postManager = new PostManager(); // Création d'un objet
    $listPosts = $postManager->getListPosts(); // Appel d'une fonction de cet objet

    require('./view/frontend/listPostsView.php');
}
