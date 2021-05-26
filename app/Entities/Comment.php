<?php
namespace App\Entities;

/**
 * comment
 * generate a comment
 */
class comment
{
    
    /**
     * id
     *
     * @var integer $id of the comment
     */
    private $id;
    
    /**
     * comment
     *
     * @var string $comment the comment
     */
    private $comment;
    
    /**
     * dateCompletion
     *
     * @var dateTime $dateCompletion comment creation date 
     */
    private $dateCompletion;
    
    /**
     * validate
     *
     * @var DateTime $validate comment validation date 
     */
    private $validate;
    
    /**
     * user_id
     *
     * @var integer $user_id commenter id of the comment 
     */
    private $user_id;
    
    /**
     * poste_id
     *
     * @var integer $poste_id id of the post on which the comment was posted 
     */
    private $post_id;
    

    /**
     * Get id of the comment
     *
     * @return  integer
     */ 
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get $comment the comment
     *
     * @return  string
     */ 
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set $comment the comment
     *
     * @param string  $comment the comment
     *
     * @return self
     */ 
    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get $dateCompletion comment creation date
     *
     * @return  dateTime //voir si cele faite de typer le return en datetime ne pose pas de probleme
     */ 
    public function getDateCompletion()
    {
        return $this->dateCompletion;
    }

    /**
     * Set $dateCompletion comment creation date
     *
     * @param  dateTime  $dateCompletion comment creation date
     *
     * @return  self
     */ 
    public function setDateCompletion($dateCompletion): self
    // public function setDateCompletion(\DateTime $dateCompletion): self
    {
        $this->dateCompletion = $dateCompletion;

        return $this;
    }

    /**
     * Get $validate comment validation date
     *
     * @return  DateTime //voir si cele faite de typer le return en datetime ne pose pas de probleme
     */ 
    public function getValidate()
    {
        return $this->validate;
    }

    /**
     * Set $validate comment validation date
     *
     * @param  DateTime  $validate comment validation date
     *
     * @return  self
     */ 
    public function setValidate($validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * Get $user_id commenter id of the comment
     *
     * @return  integer
     */ 
    public function getUser_id(): ?int
    {
        return $this->user_id;
    }

    /**
     * Set $user_id commenter id of the comment
     *
     * @param  integer $user_id commenter id of the comment
     *
     * @return  self
     */ 
    public function setUser_id($user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get $poste_id id of the post on which the comment was posted
     *
     * @return  integer
     */ 
    public function getPost_id(): ?int
    {
        return $this->post_id;
    }

    /**
     * Set $poste_id id of the post on which the comment was posted
     *
     * @param  integer $poste_id id of the post on which the comment was posted
     *
     * @return  self
     */ 
    public function setPost_id($post_id): self
    {
        $this->post_id = $post_id;

        return $this;
    }
}