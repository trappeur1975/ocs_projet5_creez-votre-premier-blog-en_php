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
     * userType
     *
     * @var string $userType userType of the user
     */
    private $userType;

    /**
     * slogan
     *
     * @var string $logo logo of the user
     */
    private $slogan;

    /**
     * mediasUser
     *
     * @var Array $mediasUser the media related to the user 
     */
    private $mediasUser;

    /**
     * socialNetworks
     *
     * @var string[] 
     */
    private $socialNetworks;

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
     * @var \Datetime $validate validate of the user
     */
    private $validate;

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
     * Get $userType userType of the user
     *
     * @return  string
     */ 
    public function getUserType(): ?string
    {
        return $this->userType;
    }

    /**
     * Set $userType userType of the user
     *
     * @param  string  $userType userType of the user
     *
     * @return  self
     */ 
    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get $logo logo of the user
     *
     * @return  string
     */ 
    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    /**
     * Set $logo logo of the user
     *
     * @param  string  $slogan logo of the user
     *
     * @return  self
     */ 
    public function setSlogan(string $slogan): self
    {
        $this->slogan = $slogan;

        return $this;
    }

    /**
     * Get $mediasUser the media related to the user
     *
     * @return  Array
     */ 
    public function getMediasUser() :?array
    {
        return $this->mediasUser;
    }

    /**
     * Set $mediasUser the media related to the user
     *
     * @param  Array  $mediasUser the media related to the user
     *
     * @return  self
     */ 
    public function setMediasUser(Array $mediasUser): self
    {
        $this->mediasUser = $mediasUser;

        return $this;
    }

    /**
     * Get $socialNetworks socialNetworks of the user
     *
     * @return  array
     */ 
    public function getSocialNetworks(): ?array
    {
        return $this->socialNetworks;
    }

    /**
     * Set $socialNetworks socialNetworks of the user
     *
     * @param  array $socialNetworks socialNetworks of the user
     *
     * @return  self
     */ 
    public function setSocialNetworks(array $socialNetworks)
    {
        $this->socialNetworks = $socialNetworks;

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
    public function getValidate(): ?\datetime
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
    {
        $this->validate = $validate;

        return $this;
    }

}