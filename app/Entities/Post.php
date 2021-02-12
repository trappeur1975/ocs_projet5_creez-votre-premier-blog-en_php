<?php
namespace App\Entities;

/**
 * Post
 * generate a post
 */
class Post
{

    /**
     * id
     * @var integer $id id of the post
     */
    private $id; 

    /**
     * title 
     *
     * @var string $title title of the post
     */
    private $title;
    
    /**
     * introduction
     *
     * @var string $introduction post introduction
     */
    private $introduction;
    
    /**
     * content
     *
     * @var string $content content of the post
     */
    private $content;
    
    /**
     * dateCreate
     *
     * @var datetime $dateCreate post creation date
     */
    private $dateCreate;
    
    /**
     * datechange
     *
     * @var datetime $datechange post modification date
     */
    private $dateChange;
        
    /**
     * user_id
     *
     * @var integer $user_id  id of the user who created the post
     */
    private $user_id;

    /**
     * Get the value of id
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     * @param integer $id id of the post
     *
     * @return  self
     */
    public function setId(int $id) : self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of title
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     * @param string $title title of the post
     * 
     * @return  self
     */
    public function setTitle(string $title) : self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of introduction
     */
    public function getIntroduction() : ?string
    {
        return $this->introduction;
    }

    /**
     * Set the value of introduction
     * @param string $introduction post introduction
     * 
     * @return  self
     */
    public function setIntroduction(string $introduction) : self
    {
        $this->introduction = $introduction;
        return $this;
    }
    /**
     * Get the value of content
     */
    public function getContent() : ?string
    {
        return $this->content;
    }

    /**
     * Set the value of content
     * @param string $content content of the post
     *
     * @return  self
     */
    public function setContent(string $content) : self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of dateCreate
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set the value of dateCreate
     * @param datetime $dateCreate post creation date
     *
     * @return  self
     */
    // public function setDateCreate(\Datetime $dateCreate) : self
    // {
    //     $this->dateCreate = $dateCreate;

    //     return $this;
    // }
    public function setDateCreate($dateCreate) : self //--------------POUR LE MOMENT L'ARGUMENT $dateCreate N EST PAS TYPER "\Datetime" (=>actuellement en string) CAR JE N AI PAS TROUVER DE MOYEN POUR GERER APRES SON ENREGISTREMENT DANS LA BASE DE DONNER VIA POSTMANAGER LORS DE LA CREATION DUN POST OU L EDIT DUN POST
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get the value of datechange
     */
    public function getDateChange()
    {
        return $this->dateChange;
    }

    /**
     * Set the value of datechange
     * @param datetime $datechange post modification date
     *
     * @return  self
     */
    public function setDateChange($datechange) : self
    {
        $this->dateChange = $datechange;

        return $this;
    }

    /**
     * Get the value of user_id
     */
    public function getUser_id() : ?int
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     * @param integer $user_id id of the user who created the post
     *
     * @return  self
     */
    public function setUser_id(int $user_id) : self
    {
        $this->user_id = $user_id;

        return $this;
    }
}