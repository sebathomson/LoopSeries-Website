<?php

namespace LoopAnime\CrawlersBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\OneToOne(targetEntity="LoopAnime\ShowsBundle\Entity\Animes", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE", name="id_anime", referencedColumnName="id_anime")
     */
    protected $anime;

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
     * @ORM\OneToMany(targetEntity="LoopAnime\CrawlersBundle\Entity\AnimeCrawlerSeasonSettings", cascade={"persist", "remove"}, orphanRemoval=True, mappedBy="crawler")
     * @Assert\Valid
     */
    protected $settings;

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

    public function __construct()
    {
        $this->settings = new ArrayCollection();
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
     * @param integer $anime
     * @return AnimesCrawlers
     */
    public function setAnime($anime)
    {
        $this->anime = $anime;

        return $this;
    }

    /**
     * Get idAnime
     *
     * @return integer 
     */
    public function getAnime()
    {
        return $this->anime;
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

    /**
     * @param $season
     * @return AnimeCrawlerSeasonSettings|null
     */
    public function getMinimalSeasonSettings($season)
    {
        $savedSeason = $this->settings->first();
        if (empty($savedSeason)) {
            return null;
        }
        foreach ($this->settings as $seasonSettings) {
            if ($seasonSettings->getSeason() == $season) {
                return $seasonSettings;
            } elseif ($seasonSettings->getSeason() <= $season && $seasonSettings->getSeason() > $savedSeason->getSeason()) {
                $savedSeason = $seasonSettings;
            }
        }
        return $savedSeason;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param AnimeCrawlerSeasonSettings[] $settings
     */
    public function setSettings($settings)
    {
        $this->settings = new ArrayCollection();
        foreach ($settings as $setting) {
            $this->addSetting($setting);
        }
    }

    public function addSetting(AnimeCrawlerSeasonSettings $settings)
    {
        $settings->setCrawler($this);
        $this->settings->add($settings);

        return $this;
    }
}
