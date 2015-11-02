<?php

namespace LoopAnime\ShowsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use LoopAnime\AppBundle\Entity\BaseEntity;

/**
 * Animes_Seasons
 *
 * @ORM\Table("animes_seasons")
 * @ORM\Entity(repositoryClass="LoopAnime\ShowsBundle\Entity\AnimesSeasonsRepository")
 * @ExclusionPolicy("ALL")
 */
class AnimesSeasons extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_season", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="LoopAnime\ShowsBundle\Entity\Animes", inversedBy="animesSeasons")
     * @ORM\JoinColumn(name="id_anime", referencedColumnName="id_anime")
     * @Expose
     */
    private $anime;

    /**
     * @var integer
     *
     * @ORM\Column(name="season", type="integer")
     * @Expose
     */
    private $season;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_episodes", type="integer")
     * @Expose
     */
    private $numberEpisodes;

    /**
     * @var string
     *
     * @ORM\Column(name="season_title", type="string", length=255)
     * @Expose
     */
    private $seasonTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="poster", type="string", length=255)
     * @Expose
     */
    private $poster;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated", type="datetime")
     * @Expose
     */
    private $lastUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     * @Expose
     */
    private $createTime;

    /**
     * @ORM\OneToMany(targetEntity="LoopAnime\ShowsBundle\Entity\AnimesEpisodes", mappedBy="season")
     * @ORM\JoinColumn(name="id_season", referencedColumnName="id_season")
     * @Expose
     */
    protected $animesEpisodes;


    public function __construct()
    {
        $this->lastUpdate = new \DateTime('now');
        $this->createTime = new \DateTime('now');
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
     * Set idAnime
     *
     * @param Animes $anime
     * @return AnimesSeasons
     */
    public function setAnime(Animes $anime)
    {
        $this->anime = $anime;

        return $this;
    }

    /**
     * Get idAnime
     *
     * @return Animes
     */
    public function getAnime()
    {
        return $this->anime;
    }

    /**
     * Set season
     *
     * @param integer $season
     * @return AnimesSeasons
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return integer 
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set numberEpisodes
     *
     * @param integer $numberEpisodes
     * @return AnimesSeasons
     */
    public function setNumberEpisodes($numberEpisodes)
    {
        $this->numberEpisodes = $numberEpisodes;

        return $this;
    }

    /**
     * Get numberEpisodes
     *
     * @return integer 
     */
    public function getNumberEpisodes()
    {
        return $this->numberEpisodes;
    }

    /**
     * Set seasonTitle
     *
     * @param string $seasonTitle
     * @return AnimesSeasons
     */
    public function setSeasonTitle($seasonTitle)
    {
        $this->seasonTitle = $seasonTitle;

        return $this;
    }

    /**
     * Get seasonTitle
     *
     * @return string 
     */
    public function getSeasonTitle()
    {
        return $this->seasonTitle;
    }

    /**
     * Set poster
     *
     * @param string $poster
     * @return AnimesSeasons
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
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     * @return AnimesSeasons
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
     * @return AnimesSeasons
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

    public function convert2Array() {
        return [
            "id" => $this->getId(),
            "createTime" => $this->getCreateTime(),
            "numberEpisodes" => $this->getNumberEpisodes(),
            "lastUpdate" => $this->getLastUpdate(),
            "season" => $this->getSeason(),
            "poster" => $this->getPoster(),
            "seasonTitle" => $this->getSeasonTitle()
        ];
    }

    public function __toString()
    {
        return (string)$this->getSeason();
    }
}
