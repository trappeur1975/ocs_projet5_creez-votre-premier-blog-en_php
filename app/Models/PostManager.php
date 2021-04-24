<?php //va interroger la base de donnée pour recuperer des infos concernant la table post
namespace App\Models;

use PDO;
use App\Entities\Post;
use Exception;

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
        $listPosts = $query ->fetchAll(PDO::FETCH_CLASS, Post::class);
        return $listPosts;
    }

    /**
     * Method getPost which displays the content of a post 
     *
     * @param integer $id id of the post we want to display
     *
     * @return Post the content of the post
     */
    public function getPost(int $id)
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM post WHERE id = :id');
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Post::class);
        $post = $query->fetch();
        if($post === false){
            throw new Exception('aucun post ne correspond a cet ID');
        }
        return $post;
    }

    // ajoute le post (en attribut de cette fonction) a la table post en bdd
    public function addPost(Post $post)
    {
        $db = $this->dbConnect();
        $query = $db->prepare('INSERT INTO post SET title = :title, 
                                                  introduction = :introduction,
                                                  content = :content,
                                                  dateCreate = :dateCreate,
                                                  dateChange = :dateChange,
                                                  user_id = :user_id');
        $result = $query->execute([
            'title' => $post->getTitle(),
            'introduction' => $post->getIntroduction(),
            'content' => $post->getContent(),
            'dateCreate' => $post->getDateCreate()->format('Y-m-d H:i:s'),
            'dateChange' => $post->getDateChange(),
            'user_id' => $post->getUser_id()
            ]);

        if($result === true){
            return $db->lastInsertId();
        } else {
            throw new Exception('impossible de de creer l enregistrement du nouveau post');
        }
    }
   
    /**
     * Method updatePost update the content of a post 
     *
     * @param Post $post post to update 
     * @param integer $idUser id of the post user
     *  
     * @return void
     */
    public function updatePost(Post $post): void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('UPDATE post SET title = :title, 
                                                introduction = :introduction,
                                                content = :content,
                                                dateCreate = :dateCreate,
                                                dateChange = :dateChange,
                                                user_id = :user_id
                            WHERE id = :id');
        $result = $query->execute([
            'title' => $post->getTitle(),
            'introduction' => $post->getIntroduction(),
            'content' => $post->getContent(),
            'dateCreate' => $post->getDateCreate()->format('Y-m-d H:i:s'),
            'dateChange' => $post->getDateChange(),
            // 'user_id' => $idUser,
            'user_id' => $post->getUser_id(),
            'id' => $post->getId()
        ]);
        
        if($result === false){
            throw new Exception('impossible de modifier le post'.$post->getId());
        }
    }

    /**
     * Method deletePost delete a post 
     *
     * @param int $id post id to delete 
     *
     * @return void
     */
    public function deletePost(int $id) : void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('DELETE FROM post WHERE id = :id');
        $result = $query->execute(['id' => $id]);
        if($result === false){
            throw new Exception('impossible de supprimer le post :'.$id.'peut être il n\'existe pas');
        }
    }

    // ------------------------------------------
    
    /**
     * Method getListPostsForUser method that returns the list of post linked to a user 
     *
     * @param int $idUser the id user whose post we want to retrieve 
     *
     * @return Post[]
     */

    public function getListPostsForUser(int $idUser): array
    {
        $db = $this->dbConnect();

        $query = $db->prepare('SELECT * FROM post WHERE user_id = :id');
        $query->execute(['id' => $idUser]);

        $listPostForUser = $query ->fetchAll(PDO::FETCH_CLASS, Post::class);

        return $listPostForUser;
    }

}