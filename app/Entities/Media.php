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
     * type
     * @var string $type type of the media
     */
    private $type;

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
     * Get $type type of the media
     *
     * @return  string
     */ 
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set $type type of the media
     *
     * @param  string  $type type of the media
     *
     * @return  self
     */ 
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

}