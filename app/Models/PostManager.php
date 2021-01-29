<?php //va interroger la base de donnée pour recuperer des infos concernant la table post
namespace App\Models;

use App\Entities\Post;

// POUR COMPLETER CES FONCTION S APPUYER SUR LA DOC PDF "PROGRAMMEZ EN ORIENTE OBJET" PAGE 46 ET 47

/**
 * PostManager
 * 
 * manage access to the post database table
 */
class PostManager extends Manager
{
    /**
     * Method getPost
     *
     * @param integer $id id of the post we want to display
     *
     * @return Post the content of the post
     */
    public function getPost($id)
    {
        $db = $this->dbConnect();
        $id = (int) $id;
        $req = $db->query('SELECT * FROM post WHERE id = ' . $id);
        $data = $req->fetch(\PDO::FETCH_ASSOC);
        return new Post($data);
    }

    // recupére toute (la liste) des posts present en base de donnee et la retourne
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
