<?php //va interroger la base de donnée pour recuperer des infos concernant la table post
namespace App\Models;

use PDO;
use App\Entities\Post;
use Exception;

// POUR COMPLETER CES FONCTION S APPUYER SUR LA DOC PDF "PROGRAMMEZ EN ORIENTE OBJET" PAGE 46 ET 47

/**
 * PostManager
 * 
 * manage access to the post database table
 */
class PostManager extends Manager
{
        
    /**
     * Method getListPosts which returns the list of Post (as an object of type Post) 
     *
     * @return Post[] 
     */
    public function getListPosts()
    {
        $db = $this->dbConnect();    
        $query = $db->query('SELECT * FROM post');
        $listPosts = $query ->fetchAll(PDO::FETCH_CLASS, Post::class); // methode grafikart
        return $listPosts;
    }

    /**
     * Method getPost
     *
     * @param integer $id id of the post we want to display
     *
     * @return Post the content of the post
     */
    public function getPost($id)
    {
        $id = (int) $id;
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM post WHERE id = :id');
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Post::class);
        $post = $query->fetch(); // methode grafikart
        if($post === false){
            throw new Exception('aucun post ne correspond a cet ID');
        }
        return $post;
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
