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
    private $datechange;
        
    /**
     * user_id
     *
     * @var integer $user_id  id of the user who created the post
     */

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    /**
     * Method hydrate
     *
     * @param array $data content of the post
     *
     * @return void
     */
    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     * @param integer $id id of the post
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     * @param string $title title of the post
     * 
     * @return  self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of introduction
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Set the value of introduction
     * @param string $introduction post introduction
     * 
     * @return  self
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;
        return $this;
    }
    /**
     * Get the value of content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     * @param string $content content of the post
     *
     * @return  self
     */
    public function setContent($content)
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
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get the value of datechange
     */
    public function getDatechange()
    {
        return $this->datechange;
    }

    /**
     * Set the value of datechange
     * @param datetime $datechange post modification date
     *
     * @return  self
     */
    public function setDatechange($datechange)
    {
        $this->datechange = $datechange;

        return $this;
    }

    /**
     * Get the value of user_id
     */
    public function getUser_id()
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     * @param integer $user_id id of the user who created the post
     *
     * @return  self
     */
    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }
}