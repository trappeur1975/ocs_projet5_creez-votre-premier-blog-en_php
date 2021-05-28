<?php
namespace App\Entities;

/**
 * SocialNetwork
 * generate a SocialNetwork
 */
class SocialNetwork
{    
    /**
     * id
     *
     * @var integer $id id of the SocialNetwork
     */
    private $id;    
    
    /**
     * url
     *
     * @var string $url url of the SocialNetwork
     */
    private $url;
    
    /**
     * user_id
     * @var int $user_id id of the user to which this socialNetwork belongs
     */
    private $user_id;

    /**
     * Get $id id of the SocialNetwork
     *
     * @return integer
     */ 
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get $url url of the SocialNetwork
     *
     * @return string
     */ 
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set $url url of the SocialNetwork
     *
     * @param  string  $url url of the SocialNetwork
     *
     * @return self
     */ 
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get $user_id id of the user to which this socialNetwork belongs
     *
     * @return int
     */ 
    public function getUser_id(): ?int
    {
        return $this->user_id;
    }

    /**
     * Set $user_id id of the user to which this socialNetwork belongs
     *
     * @param  int  $User_id id of the user to which this socialNetwork belongs
     *
     * @return self
     */ 
    public function setUser_id(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}