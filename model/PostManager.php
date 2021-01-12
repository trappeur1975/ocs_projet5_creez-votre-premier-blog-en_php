<?php //va interroger la base de donnée pour recuperer des infos concernant la table post

namespace Ocs\Blog\Model;

use Ocs\Blog\Model\Manager;
use Ocs\Blog\entity\Post;

require_once("model/Manager.php");
require_once("entity/Post.php");

// POUR COMPLETER CES FONCTION S APPUYER SUR LA DOC PDF "PROGRAMMEZ EN ORIENTE OBJET" PAGE 46 ET 47
class PostManager extends Manager
{

    // recupére le post (dont id est en attribut de cette fonction) dans la table post en bdd est retourne son objet Post
    public function getPost($id)
    {
        $db = $this->dbConnect();
        $id = (int) $id;
        $req = $db->query('SELECT * FROM post WHERE id = '.$id);
        $data = $req->fetch(\PDO::FETCH_ASSOC);
        return new Post($data);
    }

    // recupére toute (la liste) des posts present en base de donnee et la retourne
    // MODIFIER CETTE FONCTION POUR QUEL FONCTION AVEC HYDRATATION
    public function getListPosts()
    {
        $db = $this->dbConnect();

        // On effectue la requete
        $req = $db->query('SELECT * FROM post');

        // on return la requete
        return $req;
    }

    // ajoute le post (en attribut de cette fonction) a la table post en bdd
    public function addPost(Post $post)
    {
    }

    // supprime le post (en attribut de cette fonction) a la table post en bdd
    public function deletePost(Post $post)
    {
    }

    // mise a jour du post (en attribut de cette fonction) dans la table post en bdd
    public function updatePost(Post $post)
    {
    }
}