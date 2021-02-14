<?php
namespace App\Entities;

/**
 * User
 * generate a user
 */
class User
{    
    /**
     * id
     * @var integer $id id of the user
     */
    private $id; 
    
    /**
     * login
     * @var string $login login of the user
     */
    private $login;

    /**
     * password
     * @var string $password password of the user
     */
    private $password;

    /** 
     * Get $id id of the user
     *
     * @return  integer
     */ 
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set $id id of the user
     *
     * @param  integer  $id id of the user
     *
     * @return  self
     */ 
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get $login login of the user
     *
     * @return  string
     */ 
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * Set $login login of the user
     *
     * @param  string  $login  $login login of the user
     *
     * @return  self
     */ 
    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get $password password of the user
     *
     * @return  string
     */ 
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set $password password of the user
     *
     * @param  string  $password  $password password of the user
     *
     * @return  self
     */ 
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

 
}