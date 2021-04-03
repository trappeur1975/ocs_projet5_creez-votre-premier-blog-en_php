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
     * firstName
     *
     * @var string $firstName firstName of the user
     */
    private $firstName;
    
    /**
     * lastName
     *
     * @var string $lastName lastName of the user
     */
    private $lastName;
    
    /**
     * email
     *
     * @var string $email email of the user
     */
    private $email;

    /**
     * slogan
     *
     * @var string $slogan slogan of the user
     */
    private $slogan;

    /**
     * login
     * 
     * @var string $login login of the user
     */
    private $login;

    /**
     * password
     * 
     * @var string $password password of the user
     */
    private $password;
    
    /**
     * validate
     *
     * @var \datetime $validate validate of the user
     */
    private $validate;

    /**
     * userType_id
     *
     * @var int $userType_id userType_id of the user
     */
    private $userType_id;

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
     * Get $firstName firstName of the user
     *
     * @return  string
     */ 
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set $firstName firstName of the user
     *
     * @param  string  $firstName firstName of the user
     *
     * @return  self
     */ 
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get $lastName lastName of the user
     *
     * @return  string
     */ 
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Set $lastName lastName of the user
     *
     * @param  string  $lastName lastName of the user
     *
     * @return  self
     */ 
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get $email email of the user
     *
     * @return  string
     */ 
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set $email email of the user
     *
     * @param  string  $email email of the user
     *
     * @return  self
     */ 
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get $slogan slogan of the user
     *
     * @return  string
     */ 
    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    /**
     * Set $slogan slogan of the user
     *
     * @param  string  $slogan slogan of the user
     *
     * @return  self
     */ 
    public function setSlogan(string $slogan): self
    {
        $this->slogan = $slogan;

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
     * @param  string  $login login of the user
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
     * @param  string  $password password of the user
     *
     * @return  self
     */ 
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get $validate validate of the user
     *
     * @return  \Datetime
     */ 
    // public function getValidate(): ?\datetime
    public function getValidate()
    {
        return $this->validate;
    }

    /**
     * Set $validate validate of the user
     *
     * @param  \Datetime  $validate validate of the user
     *
     * @return  self
     */ 
    public function setValidate(\Datetime $validate): self
    // public function setValidate($validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * Get $userType_id userType_id of the user
     *
     * @return  int
     */ 
    public function getUserType_id()
    {
        return $this->userType_id;
    }

    /**
     * Set $userType_id userType_id of the user
     *
     * @param  int  $userType userType_id of the user
     *
     * @return  self
     */ 
    public function setUserType_id(int $userType_id): self
    {
        $this->userType_id = $userType_id;

        return $this;
    }
}