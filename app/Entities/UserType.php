<?php
namespace App\Entities;


/**
 * UserType
 * generate a userType
 */
class UserType
{      
    /**
     * id
     * @var integer $id id of the userType
     */
    private $id; 
        
    /**
     * status
     *
     * @var string $status status of the userType
     */
    private $status;

    /** 
     * Get $id id of the userType
     *
     * @return  integer
     */ 
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get $status status of the userType
     * 
     * @return  string
     */ 
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set $status status of the userType
     *
     * @param  string  $status status of the userType
     *
     * @return  self
     */ 
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}