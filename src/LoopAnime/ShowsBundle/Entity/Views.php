<?php

namespace LoopAnime\ShowsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Views
 *
 * @ORM\Table("views")
 * @ORM\Entity(repositoryClass="LoopAnime\ShowsBundle\Entity\ViewsRepository")
 */
class Views
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_view", type="integer")
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
     * @ORM\Column(name="view_time", type="datetime")
     */
    private $viewTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="completed", type="integer")
     */
    private $completed;

    /**
     * @var integer
     *
     * @ORM\Column(name="watched_time", type="integer")
     */
    private $watchedTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_link", type="integer")
     */
    private $idLink;

    /**
     * @ORM\ManyToOne(targetEntity="LoopAnime\ShowsBundle\Entity\AnimesEpisodes", inversedBy="episodeViews")
     * @ORM\JoinColumn(name="id_episode", referencedColumnName="id_episode")
     */
    protected $animeEpisodes;


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
     * @return Views
     */
    public function setIdEpisode($idEpisode)
    {
        $this->idEpisode = $idEpisode;

        return $this;
    }

    /**
     * @param AnimesEpisodes $episode
     * @return $this
     */
    public function setAnimeEpisodes(AnimesEpisodes $episode)
    {
        $this->animeEpisodes = $episode;
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
     * @return Views
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
     * Set viewTime
     *
     * @param \DateTime $viewTime
     * @return Views
     */
    public function setViewTime($viewTime)
    {
        $this->viewTime = $viewTime;

        return $this;
    }

    /**
     * Get viewTime
     *
     * @return \DateTime 
     */
    public function getViewTime()
    {
        return $this->viewTime;
    }

    /**
     * Set completed
     *
     * @param integer $completed
     * @return Views
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Get completed
     *
     * @return integer 
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Set watchedTime
     *
     * @param integer $watchedTime
     * @return Views
     */
    public function setWatchedTime($watchedTime)
    {
        $this->watchedTime = $watchedTime;

        return $this;
    }

    /**
     * Get watchedTime
     *
     * @return integer 
     */
    public function getWatchedTime()
    {
        return $this->watchedTime;
    }

    /**
     * Set idLink
     *
     * @param integer $idLink
     * @return Views
     */
    public function setIdLink($idLink)
    {
        $this->idLink = $idLink;

        return $this;
    }

    /**
     * Get idLink
     *
     * @return integer 
     */
    public function getIdLink()
    {
        return $this->idLink;
    }
}
