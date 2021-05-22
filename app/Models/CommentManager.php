<?php //va interroger la base de donnée pour recuperer des infos concernant la table comment
namespace App\Models;

use PDO;
use App\Entities\Comment;
use Exception;
use \DateTime;

class CommentManager extends Manager
{
    const ADMINISTRATEUR = 2;
    
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

        $listCommentsForPost = $query ->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $listCommentsForPost;
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
        $UserIdComment = $comment->getUser_id();
        $usermanager = new userManager();
        $userComment = $usermanager->getUser($UserIdComment);
        
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

        if($userComment->getUserType_id() != self::ADMINISTRATEUR){
            sendEmail($userComment->getEmail(), 'commentaire sur BlogNico en attente', 'Votre commentaire sur le BlogNico a bien ete enregistre et est en attente de validation de la part de l\'administrateur du site');
        }
       
    }

    /**
     * Method deleteComment delete a comment 
     *
     * @param int $idcomment id of comment to delete 
     *
     * @return void
     */
    public function deleteComment(int $idComment)
    {   
        $comment = $this->getComment($idComment);

        $UserIdComment = $comment->getUser_id();
        $usermanager = new userManager();
        $userComment = $usermanager->getUser($UserIdComment);

        $db = $this->dbConnect();
        $query = $db->prepare('DELETE FROM comment WHERE id = :idComment');
        $result = $query->execute(['idComment' => $idComment]);

        if($result === false){
            throw new Exception('impossible de supprimer le commentaire :'.$idComment);
        }else {
            if($userComment->getUserType_id() != self::ADMINISTRATEUR){
                sendEmail($userComment->getEmail(), 'commentaire #'.$idComment.' sur BlogNico SUPPRIMER', 'Votre commentaire #'.$idComment.' sur le BlogNico a ete supprime');
            }
            
            return $comment;
        }
    }

    /**
     * Method updateComment update the content of a comment 
     *
     * @param Comment $comment comment to update 
     *
     * @return void
     */
    public function updateComment(Comment $comment): void
    {
        $db = $this->dbConnect();
        $query = $db->prepare('UPDATE comment SET comment = :comment, 
                                                dateCompletion = :dateCompletion,
                                                validate = :validate,
                                                user_id = :userId,
                                                post_id = :postId
                            WHERE id = :id');
        $result = $query->execute([
            'comment' => $comment->getComment(),
            'dateCompletion' => $comment->getDateCompletion(),
            // 'dateCompletion' => $comment->getDateCompletion()->format('Y-m-d H:i:s'),
            'validate' => $comment->getValidate(),
            // 'validate' => $comment->getValidate()->format('Y-m-d H:i:s'),
            'userId' => $comment->getUser_id(),
            'postId' => $comment->getPost_id(),
            'id' => $comment->getId()
        ]);
        
        if($result === false){
            throw new Exception('impossible de modifier le commentaire'.$comment->getId());
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
       
        $UserIdComment = $comment->getUser_id();
        $usermanager = new userManager();
        $userComment = $usermanager->getUser($UserIdComment);

        $dateTime = new Datetime();
        $validate = $dateTime->format('Y-m-d H:i:s');
  
        $db = $this->dbConnect();
        $query = $db->prepare('UPDATE comment SET validate = :validate WHERE id = :idComment');
        $result = $query->execute([
            'validate' => $validate,
            'idComment' => $idComment
            ]);
      
        if($result === true){
            if($userComment->getUserType_id() != self::ADMINISTRATEUR){
                sendEmail($userComment->getEmail(), 'votre commentaire #'.$idComment.' sur BlogNico VALIDER', 'Votre commentaire # '.$idComment.' sur le BlogNico a ete VALIDER part de l\'administrateur du site'); 
            }
            return $comment;
        }else {
            throw new Exception('impossible de valider le commentaire :'.$idComment);
        }
    }

    /**
     * Method ListCommentsWaiteValidate which returns the list of comments awaiting validation 
     *
     * @return Comment[] 
     */
    public function listCommentsWaiteValidate(): array
    {
        $db = $this->dbConnect();    
          
        $query = $db->query('SELECT * FROM comment where validate IS NULL');
        $listCommentsWaiteValidate = $query->fetchAll(PDO::FETCH_CLASS, Comment::class);
        
        return $listCommentsWaiteValidate;
    }

    /**
     * Method listCommentsNotNullForPost return the list of comments validate (validate = not null) 
     *
     * @return Comment[] 
     */
    public function listCommentsNotNullForPost(int $idPost): array
    {
        $db = $this->dbConnect();

        $query = $db->prepare('SELECT * FROM comment WHERE post_id = :id AND validate IS NOT NULL');
        $query->execute(['id' => $idPost]);

        $listCommentsForPost = $query ->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $listCommentsForPost;
    }

    /**
     * Method listCommentsForUser method that returns the list of comment linked to a user 
     *
     * @param int $iduser the id of the user whose comments we want to collect 
     *
     * @return Comment[]
     */

    public function listCommentsForUser(int $idUser): array
    {
        $db = $this->dbConnect();

        $query = $db->prepare('SELECT * FROM comment WHERE user_id = :idUser');
        $query->execute(['idUser' => $idUser]);

        $listCommentsForUser = $query ->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $listCommentsForUser;
    }

    /**
     * Method listCommentsForUserForPost method that returns the list of comment linked to a user and linked to a post
     *
     * @param int $iduser the id of the user whose comments we want to collect 
     * @param int $idPost the id of the post whose comments we want to collect 
     * 
     * @return Comment[]
     */

    public function listCommentsForUserForPost(int $idUser, int $idPost): array
    {
        $db = $this->dbConnect();

        $query = $db->prepare('SELECT * FROM comment WHERE user_id = :idUser and post_id = :idPost');
        $query->execute([
            'idUser' => $idUser,
            'idPost' => $idPost
            ]);

        $listCommentsForUserForPost = $query ->fetchAll(PDO::FETCH_CLASS, Comment::class);

        return $listCommentsForUserForPost;
    }

}