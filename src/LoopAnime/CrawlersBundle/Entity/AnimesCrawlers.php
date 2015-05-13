<?php

namespace LoopAnime\CrawlersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Animes_Crawlers
 *
 * @ORM\Table("animes_crawlers")
 * @ORM\Entity(repositoryClass="LoopAnime\CrawlersBundle\Entity\AnimesCrawlersRepository")
 */
class AnimesCrawlers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_anime", type="integer")
     * @ORM\ManyToOne(targetEntity="LoopAnime\ShowsBundle\Entity\Animes", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $idAnime;

    /**
     * @var string
     *
     * @ORM\Column(name="hoster", type="string", length=255)
     */
    private $hoster;

    /**
     * @var string
     *
     * @ORM\Column(name="title_adapted", type="string", length=500, nullable=true)
     */
    private $titleAdapted;

    /**
     *
     * @ORM\Column(name="seasons_settings", type="array", length=500)
     */
    private $seasonsSettings;

    /**
     * @var string
     *
     * @ORM\Column(name="episode_adapted", type="string", length=500, nullable=true)
     */
    private $episodeAdapted;

    /**
     * @var string
     *
     * @ORM\Column(name="episode_clean", type="string", length=500, nullable=true)
     */
    private $episodeClean;


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
     * @return AnimesCrawlers
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
     * Set hoster
     *
     * @param string $hoster
     * @return AnimesCrawlers
     */
    public function setHoster($hoster)
    {
        $this->hoster = $hoster;

        return $this;
    }

    /**
     * Get hoster
     *
     * @return string 
     */
    public function getHoster()
    {
        return $this->hoster;
    }

    /**
     * Set titleAdapted
     *
     * @param string $titleAdapted
     * @return AnimesCrawlers
     */
    public function setTitleAdapted($titleAdapted)
    {
        $this->titleAdapted = $titleAdapted;

        return $this;
    }

    /**
     * Get titleAdapted
     *
     * @return string 
     */
    public function getTitleAdapted()
    {
        return $this->titleAdapted;
    }

    /**
     * Set episodeAdapted
     *
     * @param string $episodeAdapted
     * @return AnimesCrawlers
     */
    public function setEpisodeAdapted($episodeAdapted)
    {
        $this->episodeAdapted = $episodeAdapted;

        return $this;
    }

    /**
     * Get episodeAdapted
     *
     * @return string 
     */
    public function getEpisodeAdapted()
    {
        return $this->episodeAdapted;
    }

    /**
     * Set episodeClean
     *
     * @param string $episodeClean
     * @return AnimesCrawlers
     */
    public function setEpisodeClean($episodeClean)
    {
        $this->episodeClean = $episodeClean;

        return $this;
    }

    /**
     * Get episodeClean
     *
     * @return string 
     */
    public function getEpisodeClean()
    {
        return $this->episodeClean;
    }

    public function setSeasonsSettings($seasonsAsNew)
    {
        $this->seasonsSettings = $seasonsAsNew;
    }

    public function getSeasonsSettings()
    {
        return $this->seasonsSettings;
    }

    public function getMinimalSeasonSettings($season)
    {
        if (empty($this->seasonsSettings)) {
            return null;
        }
        $savedSeason = 0;
        foreach ($this->seasonsSettings as $key => $seasonSettings) {
            if ($seasonSettings['season'] == $season) {
                return $seasonSettings;
            } elseif ($seasonSettings['season'] <= $season && $seasonSettings['season'] > $savedSeason) {
                $savedSeason = $seasonSettings['season'];
            }
        }
        if ($savedSeason) {
            return $this->getMinimalSeasonSettings($savedSeason);
        }
        return null;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    public function getSeasonsSettingsAsString()
    {
        $seasonsSettings = $this->getSeasonsSettings();
        $str = [];
        if ($seasonsSettings) {
            foreach ($seasonsSettings as $seasonSetting) {
                $str[] = $seasonSetting['season'];
            }

            return implode(", ",$str);
        }

        return "n/a";
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
