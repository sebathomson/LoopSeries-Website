<?php

namespace LoopAnime\CommentsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LoopAnime\UsersBundle\Entity\Users;

/**
 * Comments
 *
 * @ORM\Table("comments")
 * @ORM\Entity(repositoryClass="LoopAnime\CommentsBundle\Entity\CommentsRepository")
 */
class Comments
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_comment", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="LoopAnime\ShowsBundle\Entity\AnimesEpisodes")
     * @ORM\JoinColumn(name="id_episode", referencedColumnName="id_episode")
     */
    private $episode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var string
     *
     * @ORM\Column(name="comment_title", type="string", length=255)
     */
    private $commentTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private $comment;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratingCount", type="integer")
     */
    private $ratingCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratingUp", type="integer")
     */
    private $ratingUp;

    /**
     * @var string
     *
     * @ORM\Column(name="ratingDown", type="integer")
     */
    private $ratingDown;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="LoopAnime\UsersBundle\Entity\Users")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     */
    protected $user;

    public function __construct()
    {
        $this->createTime = new \DateTime("now");
        $this->ratingUp = 0;
        $this->ratingDown = 0;
        $this->ratingCount = 0;
    }

    /**
     * @return int
     */
    public function getEpisode()
    {
        return $this->episode;
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
     * Set idEpisode
     *
     * @param integer $episode
     * @return Comments
     */
    public function setEpisode($episode)
    {
        $this->episode = $episode;

        return $this;
    }

    /**
     * Set idUser
     *
     * @param Users $user
     * @return Comments
     */
    public function setUser(Users $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer 
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Comments
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
     * Set commentTitle
     *
     * @param string $commentTitle
     * @return Comments
     */
    public function setCommentTitle($commentTitle)
    {
        $this->commentTitle = $commentTitle;

        return $this;
    }

    /**
     * Get commentTitle
     *
     * @return string 
     */
    public function getCommentTitle()
    {
        return $this->commentTitle;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Comments
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set ratingCount
     *
     * @param integer $ratingCount
     * @return Comments
     */
    public function setRatingCount($ratingCount)
    {
        $this->ratingCount = $ratingCount;

        return $this;
    }

    /**
     * Get ratingCount
     *
     * @return integer 
     */
    public function getRatingCount()
    {
        return $this->ratingCount;
    }

    /**
     * Set ratingUp
     *
     * @param integer $ratingUp
     * @return Comments
     */
    public function setRatingUp($ratingUp)
    {
        $this->ratingUp = $ratingUp;

        return $this;
    }

    /**
     * Get ratingUp
     *
     * @return integer 
     */
    public function getRatingUp()
    {
        return $this->ratingUp;
    }

    /**
     * Set ratingDown
     *
     * @param string $ratingDown
     * @return Comments
     */
    public function setRatingDown($ratingDown)
    {
        $this->ratingDown = $ratingDown;

        return $this;
    }

    /**
     * Get ratingDown
     *
     * @return string 
     */
    public function getRatingDown()
    {
        return $this->ratingDown;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    private function convert2Array()
    {
        return array(
            "id" => $this->getId(),
            "author" => $this->getOwner(),
            "ratingUp" => $this->getRatingDown(),
            "ratingDown" => $this->getRatingDown(),
            "ratingCount" => $this->getRatingCount(),
            "commentTitle" => $this->getCommentTitle(),
            "createTime" => $this->getCreateTime(),
            "comment" => $this->getComment(),
            "id_user" => $this->getIdUser(),
            "id_episode" => $this->getIdEpisode()
        );
    }

    public function getUser()
    {
        return $this->user;
    }
}
