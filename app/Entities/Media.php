<?php
namespace App\Entities;

/**
 * Media
 * generate a media
 */
class Media
{    
    /**
     * id
     * @var integer $id id of the media
     */
    private $id;
    
    /**
     * path
     * @var string $path path of the media
     */
    private $path;

    /**
     * alt
     * @var string $alt alt of the media
     */
    private $alt;

    /**
     * statutActif
     * @var bool $statutActif media status (active = boolean true or not active = boolean false) depending on whether the media is used or not in a post 
     */
    private $statutActif;
     
    /**
     * mediaType
     * @var string $mediaType type of the media
     */
    // private $mediaType;

    /**
     * mediaType_id
     * @var int $mediaType_id id of the media type to which this media belongs 
     */
    private $mediaType_id;

    /**
     * post_id
     * @var int $post_id id of the post to which this media belongs
     */
    private $post_id;

    /**
     * user_id
     * @var int $user_id id of the user to which this media belongs
     */
    private $user_id;



    /**
     * Get $id id of the media
     *
     * @return  integer
     */ 
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get $path path of the media
     *
     * @return  string
     */ 
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set $path path of the media
     *
     * @param  string  $path  $path of the media
     *
     * @return  self
     */ 
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get $alt alt of the media
     *
     * @return  string
     */ 
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * Set $alt alt of the media
     *
     * @param  string  $alt alt of the media
     *
     * @return  self
     */ 
    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get $statutActif media status (active = boolean true or not active = boolean false) depending on whether the media is used or not in a post
     *
     * @return  bool
     */ 
    public function getStatutActif(): ?bool
    {
        return $this->statutActif;
    }

    /**
     * Set $statutActif media status (active = boolean true or not active = boolean false) depending on whether the media is used or not in a post
     *
     * @param  bool  $statutActif  $statutActif media status (active = boolean true or not active = boolean false) depending on whether the media is used or not in a post
     *
     * @return  self
     */ 
    public function setStatutActif(bool $statutActif) :self
    {
        $this->statutActif = $statutActif;

        return $this;
    }

    /**
     * Get $mediaType type of the media
     *
     * @return  string
     */ 
    // public function getMediaType(): ?string
    // {
    //     return $this->type;
    // }

    /**
     * Set $mediaType type of the media
     *
     * @param  string  $mediaType type of the media
     *
     * @return  self
     */ 
    // public function setMediaType(string $type): self
    // {
    //     $this->type = $type;

    //     return $this;
    // }


    /**
     * Get $mediaType_id id of the media type to which this media belongs
     *
     * @return  int
     */ 
    public function getMediaType_id(): ?int
    {
        return $this->mediaType_id;
    }

    /**
     * Set $mediaType_id id of the media type to which this media belongs
     *
     * @param  int  $mediaType_id  $mediaType_id id of the media type to which this media belongs
     *
     * @return  self
     */ 
    public function setMediaType_id(int $mediaType_id): self
    {
        $this->mediaType_id = $mediaType_id;

        return $this;
    }

    /**
     * Get $post_id id of the post to which this media belongs
     *
     * @return  int
     */ 
    public function getPost_id(): ?int
    {
        return $this->post_id;
    }

    /**
     * Set $post_id id of the post to which this media belongs
     *
     * @param  int  $post_id  $post_id id of the post to which this media belongs
     *
     * @return  self
     */ 
    public function setPost_id(int $post_id): self
    {
        $this->post_id = $post_id;

        return $this;
    }

    /**
     * Get $user_id id of the user to which this media belongs
     *
     * @return  int
     */ 
    public function getUser_id(): ?int
    {
        return $this->user_id;
    }

    /**
     * Set $user_id id of the user to which this media belongs
     *
     * @param  int  $user_id  $user_id id of the user to which this media belongs
     *
     * @return  self
     */ 
    public function setUser_id(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}