<?php

namespace LoopAnime\UsersBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use LoopAnime\UsersBundle\Entity\UsersPreferences;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Users
 *
 * @ORM\Table("users")
 * @ORM\Entity(repositoryClass="LoopAnime\UsersBundle\Entity\UsersRepository")
 */
class Users extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *

     */
    protected $username;

    /**
     * @var string
     *

     */
    protected $password;

    /**
     * @var string
     *

     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255)
     */
    private $avatar;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;

    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="datetime")
     */
    private $birthdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var \DateTime
     *

     */
    protected $lastLogin;

    /**
     * @var integer
     *
     * @ORM\Column(name="newsletter", type="boolean")
     */
    private $newsletter;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=10)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=3)
     */
    private $country;

    /**
     * @var UsersPreferences
     *
     * ORM\OneToOne(targetEntity="LoopAnime\UsersBundle\Entity\UsersPreferences")
     * ORM\JoinColumn(name="id_user", referencedColumnName="id_user", nullable=true)
     */
    protected $preferences;

    public function __construct() {
        parent::__construct();
        $this->avatar = "bundles/loopanimegeneral/img/dafault_avatar.png";
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Users
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return Users
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     * @return Users
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime 
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Users
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set newsletter
     *
     * @param integer $newsletter
     * @return Users
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get newsletter
     *
     * @return integer 
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Users
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return Users
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string 
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Users
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getPreferences()
    {
        return $this->preferences;
    }


    public function setFacebookId($id)
    {
        $this->facebook_id = $id;

        return $this;
    }

    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    public function setFacebookAccessToken($token)
    {
        $this->facebook_access_token = $token;

        return $this;
    }

    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    public function setGoogleId($id)
    {
        $this->google_id = $id;

        return $this;
    }

    public function getGoogleId()
    {
        return $this->google_id;
    }

    public function setGoogleAccessToken($token)
    {
        $this->google_access_token = $token;

        return $this;
    }

    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    public function validate(ExecutionContextInterface $context) {
// TODO: Put here the validation against the same username
//        $context (in_array($this->getFirstName(), $fakeNames)) {
//            $context->buildViolation(
//                'firstName',
//                'This name sounds totally fake!',
//                array(),
//                null
//            );
//        }

    }
}
