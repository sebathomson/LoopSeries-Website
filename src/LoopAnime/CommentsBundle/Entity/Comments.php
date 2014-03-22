<?php

namespace LoopAnime\CommentsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="id_episode", type="integer")
     * @ORM\ManyToOne(targetEntity="LoopAnime\ShowsBundle\Entity\Animes_Episodes", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $idEpisode;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer")
     * @ORM\ManyToOne(targetEntity="LoopAnime\UsersBundle\Entity\Users", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $idUser;

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
     * @param integer $idEpisode
     * @return Comments
     */
    public function setIdEpisode($idEpisode)
    {
        $this->idEpisode = $idEpisode;

        return $this;
    }

    /**
     * Get idEpisode
     *
     * @return integer 
     */
    public function getIdEpisode()
    {
        return $this->idEpisode;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     * @return Comments
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

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
}
