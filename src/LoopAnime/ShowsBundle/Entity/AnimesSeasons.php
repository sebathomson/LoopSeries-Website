<?php

namespace LoopAnime\ShowsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Animes_Seasons
 *
 * @ORM\Table("animes_seasons")
 * @ORM\Entity(repositoryClass="LoopAnime\ShowsBundle\Entity\AnimesSeasonsRepository")
 */
class AnimesSeasons
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_season", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="LoopAnime\ShowsBundle\Entity\Animes", inversedBy="animesSeasons")
     * @ORM\JoinColumn(name="id_anime", referencedColumnName="id_anime")
     */
    private $idAnime;

    /**
     * @var integer
     *
     * @ORM\Column(name="season", type="integer")
     */
    private $season;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_episodes", type="integer")
     */
    private $numberEpisodes;

    /**
     * @var string
     *
     * @ORM\Column(name="season_title", type="string", length=255)
     */
    private $seasonTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="poster", type="string", length=255)
     */
    private $poster;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated", type="datetime")
     */
    private $lastUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_time", type="datetime")
     */
    private $createTime;

    /**
     * @ORM\OneToMany(targetEntity="LoopAnime\ShowsBundle\Entity\AnimesEpisodes", mappedBy="idSeason")
     * @ORM\JoinColumn(name="id_season", referencedColumnName="id_season")
     */
    protected $animesEpisodes;


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
     * @param integer $idAnime
     * @return AnimesSeasons
     */
    public function setIdAnime($idAnime)
    {
        $this->idAnime = $idAnime;

        return $this;
    }

    /**
     * Get idAnime
     *
     * @return integer 
     */
    public function getIdAnime()
    {
        return $this->idAnime;
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
}
