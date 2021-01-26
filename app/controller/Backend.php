<?php
namespace App\Controller;

use App\Model\PostManager;

class Backend
{
    function post($id)
    {
        $postManager = new PostManager(); // Création de l'objet manger de post
        $post = $postManager->getPost($id);
    
        require('../app/view/frontend/postView.php');
    }
    
    
    // A MODIFIER POUR GERER L HYDRATATION
    // URL : http://localhost/ocs_projet5_creez-votre-premier-blog-en_php/?action=listPosts
    function listPosts()
    {
        $postManager = new PostManager(); // Création d'un objet
        $listPosts = $postManager->getListPosts(); // Appel d'une fonction de cet objet
    
        require('../app/view/frontend/listPostsView.php');
    }
    
}
