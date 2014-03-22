<?php

namespace LoopAnime\ShowsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * animes_episodes
 *
 * @ORM\Table("animes_episodes")
 * @ORM\Entity(repositoryClass="LoopAnime\Bundle\ShowsBundle\Entity\animes_episodesRepository")
 */
class animes_episodes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_episode", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_season", type="integer")
     * @ORM\ManyToOne(targetEntity="LoopAnime\ShowsBundle\Entity\Animes_Seasons", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $idSeason;

    /**
     * @var integer
     *
     * @ORM\Column(name="episode", type="integer")
     */
    private $episode;

    /**
     * @var string
     *
     * @ORM\Column(name="episode_title", type="string", length=50)
     */
    private $episodeTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="poster", type="string", length=255)
     */
    private $poster;

    /**
     * @var integer
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;

    /**
     * @var integer
     *
     * @ORM\Column(name="comments", type="integer")
     */
    private $comments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="air_date", type="datetime")
     */
    private $airDate;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text")
     */
    private $summary;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratingCount", type="integer")
     */
    private $ratingCount;

    /**
     * @var string
     *
     * @ORM\Column(name="imdb_id", type="string", length=10)
     */
    private $imdbId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratingUp", type="integer")
     */
    private $ratingUp;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratingDown", type="integer")
     */
    private $ratingDown;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="datetime")
     */
    private $lastUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="absolute_number", type="integer")
     */
    private $absoluteNumber;


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
     * Set idSeason
     *
     * @param integer $idSeason
     * @return animes_episodes
     */
    public function setIdSeason($idSeason)
    {
        $this->idSeason = $idSeason;

        return $this;
    }

    /**
     * Get idSeason
     *
     * @return integer 
     */
    public function getIdSeason()
    {
        return $this->idSeason;
    }

    /**
     * Set episode
     *
     * @param integer $episode
     * @return animes_episodes
     */
    public function setEpisode($episode)
    {
        $this->episode = $episode;

        return $this;
    }

    /**
     * Get episode
     *
     * @return integer 
     */
    public function getEpisode()
    {
        return $this->episode;
    }

    /**
     * Set episodeTitle
     *
     * @param string $episodeTitle
     * @return animes_episodes
     */
    public function setEpisodeTitle($episodeTitle)
    {
        $this->episodeTitle = $episodeTitle;

        return $this;
    }

    /**
     * Get episodeTitle
     *
     * @return string 
     */
    public function getEpisodeTitle()
    {
        return $this->episodeTitle;
    }

    /**
     * Set poster
     *
     * @param string $poster
     * @return animes_episodes
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * Get poster
     *
     * @return string 
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return animes_episodes
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return animes_episodes
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set comments
     *
     * @param integer $comments
     * @return animes_episodes
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return integer 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set airDate
     *
     * @param \DateTime $airDate
     * @return animes_episodes
     */
    public function setAirDate($airDate)
    {
        $this->airDate = $airDate;

        return $this;
    }

    /**
     * Get airDate
     *
     * @return \DateTime 
     */
    public function getAirDate()
    {
        return $this->airDate;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return animes_episodes
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set ratingCount
     *
     * @param integer $ratingCount
     * @return animes_episodes
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
     * Set imdbId
     *
     * @param string $imdbId
     * @return animes_episodes
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * Get imdbId
     *
     * @return string 
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }

    /**
     * Set ratingUp
     *
     * @param integer $ratingUp
     * @return animes_episodes
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
     * @param integer $ratingDown
     * @return animes_episodes
     */
    public function setRatingDown($ratingDown)
    {
        $this->ratingDown = $ratingDown;

        return $this;
    }

    /**
     * Get ratingDown
     *
     * @return integer 
     */
    public function getRatingDown()
    {
        return $this->ratingDown;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     * @return animes_episodes
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime 
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return animes_episodes
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
     * Set absoluteNumber
     *
     * @param integer $absoluteNumber
     * @return animes_episodes
     */
    public function setAbsoluteNumber($absoluteNumber)
    {
        $this->absoluteNumber = $absoluteNumber;

        return $this;
    }

    /**
     * Get absoluteNumber
     *
     * @return integer 
     */
    public function getAbsoluteNumber()
    {
        return $this->absoluteNumber;
    }
}
