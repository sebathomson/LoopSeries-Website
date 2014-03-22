<?php

namespace LoopAnime\ShowsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Animes
 *
 * @ORM\Table("animes")
 * @ORM\Entity(repositoryClass="LoopAnime\Bundle\ShowsBundle\Entity\AnimesRepository")
 */
class Animes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_anime", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="poster", type="string", length=255, nullable=true)
     */
    private $poster;

    /**
     * @var string
     *
     * @ORM\Column(name="genres", type="string", length=255, nullable=true)
     */
    private $genres;

    /**
     * @var string
     *
     * @ORM\Column(name="themes", type="string", length=255, nullable=true)
     */
    private $themes;

    /**
     * @var string
     *
     * @ORM\Column(name="plotSummary", type="text", nullable=true)
     */
    private $plotSummary;

    /**
     * @var string
     *
     * @ORM\Column(name="runningTime", type="string", length=255, nullable=true)
     */
    private $runningTime;

    /**
     * @var string
     *
     * @ORM\Column(name="startTime", type="string", length=255, nullable=true)
     */
    private $startTime;

    /**
     * @var string
     *
     * @ORM\Column(name="endTime", type="string", length=255, nullable=true)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="rating", type="integer", nullable=true)
     */
    private $rating;

    /**
     * @var integer
     *
     * @ORM\Column(name="imdb_id", type="integer", nullable=true)
     */
    private $imdbId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratingCount", type="integer", nullable=true)
     */
    private $ratingCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratingUp", type="integer", nullable=true)
     */
    private $ratingUp;

    /**
     * @var integer
     *
     * @ORM\Column(name="ratingDown", type="integer", nullable=true)
     */
    private $ratingDown;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastUpdated", type="integer", nullable=true)
     */
    private $lastUpdated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    private $lastUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @var string
     *
     * @ORM\Column(name="type_series", type="string", length=255)
     */
    private $typeSeries;


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
     * Set title
     *
     * @param string $title
     * @return Animes
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set poster
     *
     * @param string $poster
     * @return Animes
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
     * Set genres
     *
     * @param string $genres
     * @return Animes
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;

        return $this;
    }

    /**
     * Get genres
     *
     * @return string 
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set themes
     *
     * @param string $themes
     * @return Animes
     */
    public function setThemes($themes)
    {
        $this->themes = $themes;

        return $this;
    }

    /**
     * Get themes
     *
     * @return string 
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * Set plotSummary
     *
     * @param string $plotSummary
     * @return Animes
     */
    public function setPlotSummary($plotSummary)
    {
        $this->plotSummary = $plotSummary;

        return $this;
    }

    /**
     * Get plotSummary
     *
     * @return string 
     */
    public function getPlotSummary()
    {
        return $this->plotSummary;
    }

    /**
     * Set runningTime
     *
     * @param string $runningTime
     * @return Animes
     */
    public function setRunningTime($runningTime)
    {
        $this->runningTime = $runningTime;

        return $this;
    }

    /**
     * Get runningTime
     *
     * @return string 
     */
    public function getRunningTime()
    {
        return $this->runningTime;
    }

    /**
     * Set startTime
     *
     * @param string $startTime
     * @return Animes
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return string 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param string $endTime
     * @return Animes
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return string 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Animes
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return Animes
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
     * Set imdbId
     *
     * @param integer $imdbId
     * @return Animes
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * Get imdbId
     *
     * @return integer 
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }

    /**
     * Set ratingCount
     *
     * @param integer $ratingCount
     * @return Animes
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
     * @return Animes
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
     * @return Animes
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
     * Set lastUpdated
     *
     * @param integer $lastUpdated
     * @return Animes
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * Get lastUpdated
     *
     * @return integer 
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     * @return Animes
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
     * @return Animes
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
     * Set typeSeries
     *
     * @param string $typeSeries
     * @return Animes
     */
    public function setTypeSeries($typeSeries)
    {
        $this->typeSeries = $typeSeries;

        return $this;
    }

    /**
     * Get typeSeries
     *
     * @return string 
     */
    public function getTypeSeries()
    {
        return $this->typeSeries;
    }
}
