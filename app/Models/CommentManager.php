<?php //va interroger la base de donnée pour recuperer des infos concernant la table comment
namespace App\Models;

use PDO;
use App\Entities\Comment;
use Exception;
use \DateTime;

class CommentManager extends Manager
{
    /**
     * Method getListCommentForPost method that returns the list of comment linked to a post 
     *
     * @param int $idPost the id of the post whose comments we want to collect 
     *
     * @return Comment[]
     */

    public function getListCommentsForPost(int $idPost): array
    {
        $db = $this->dbConnect();

        $query = $db->prepare('SELECT * FROM comment WHERE post_id = :id');
        $query->execute(['id' => $idPost]);

        $listCommentForPost = $query ->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $listCommentForPost;
    }

    /**
     * Method getComment which displays the content of a comment 
     *
     * @param integer $id id of the comment we want to display
     *
     * @return Comment the content of the comment
     */
    public function getComment(int $id)
    {
        $db = $this->dbConnect();
        $query = $db->prepare('SELECT * FROM comment WHERE id = :id');
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, Comment::class);
        $post = $query->fetch();
        if($post === false){
            throw new Exception('aucun comment ne correspond a cet ID');
        }
        return $post;
    }

    // ajoute le comment (en attribut de cette fonction) a la table comment en bdd
    public function addComment(Comment $comment)
    {   
        $db = $this->dbConnect();
        $query = $db->prepare('INSERT INTO comment SET comment = :comment, 
                                                    dateCompletion = :dateCompletion,
                                                    validate = :validate,
                                                    user_id = :user_id,
                                                    post_id = :post_id');
        $result = $query->execute([
            'comment' => $comment->getComment(),
            'dateCompletion' => $comment->getDateCompletion(),
            'validate' => $comment->getValidate(),
            'user_id' => $comment->getUser_id(),
            'post_id' => $comment->getPost_id()
            ]);
        
        if($result === false){
            throw new Exception('impossible de creer l enregistrement du nouveau commentaire');
        }      
    }

    /**
     * Method deleteComment delete a comment 
     *
     * @param int $idcomment id of comment to delete 
     *
     * @return void
     */
    public function deleteComment(int $idcomment)
    {
        $comment = $this->getComment($idcomment);

        $db = $this->dbConnect();
        $query = $db->prepare('DELETE FROM comment WHERE id = :idcomment');
        $result = $query->execute(['idcomment' => $idcomment]);

        if($result === true){
            return $comment;
        }else {
            throw new Exception('impossible de supprimer le commentaire :'.$idcomment);
        }
    }

    /**
     * validates the comment whose id is indicated in the function parameter 
     *
     * @param $idComment of the comment we want to validate 
     *
     */
    public function validateComment(int $idComment)
    {
        $comment = $this->getComment($idComment);
        
        $dateTime = new Datetime();
        $validate = $dateTime->format('Y-m-d H:i:s');
  
        $db = $this->dbConnect();
        $query = $db->prepare('UPDATE comment SET validate = :validate WHERE id = :idComment');
        $result = $query->execute([
            'validate' => $validate,
            'idComment' => $idComment
            ]);
      
        if($result === true){
            return $comment;
        }else {
            throw new Exception('impossible de valider le commentaire :'.$idComment);
        }
    }

    /**
     * Method ListCommentsWaiteValidate which returns the list of comments awaiting validation   (as an object of type user) 
     *
     * @return UComment[] 
     */
    public function listCommentsWaiteValidate()
    {
        $db = $this->dbConnect();    
        $query = $db->query('SELECT * FROM comment where validate IS NULL');
        $listCommentsWaiteValidate = $query->fetchAll(PDO::FETCH_CLASS, Comment::class);
        return $listCommentsWaiteValidate;
    }

}