<?php

namespace LoopAnime\CrawlersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Animes_Crawlers
 *
 * @ORM\Table("animes_crawlers_seasons")
 * @ORM\Entity()
 */
class AnimeCrawlerSeasonSettings
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
     * @ORM\OneToMany(targetEntity="LoopAnime\CrawlersBundle\Entity\AnimesCrawlers", mappedBy="seasonsSettings")
     */
    private $animeCrawler;

    /** @ORM\Column(name="season", type="integer", length=2) */
    private $season;

    /** @ORM\Column(name="episode_title", type="string", length=500, nullable=true) */
    private $episodeTitle;

    /** @ORM\Column(name="anime_title", type="string", length=500, nullable=true) */
    private $animeTitle;

    /** @ORM\Column(name="reset", type="boolean", nullable=true, nullable=true) */
    private $reset;

    /** @ORM\Column(name="handicap", type="integer", length=10, nullable=true) */
    private $handicap;

    /**
     * @return mixed
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param mixed $season
     */
    public function setSeason($season)
    {
        $this->season = $season;
    }

    /**
     * @return mixed
     */
    public function getEpisodeTitle()
    {
        return $this->episodeTitle;
    }

    /**
     * @param mixed $episodeTitle
     */
    public function setEpisodeTitle($episodeTitle)
    {
        $this->episodeTitle = $episodeTitle;
    }

    /**
     * @return mixed
     */
    public function getAnimeTitle()
    {
        return $this->animeTitle;
    }

    /**
     * @param mixed $animeTitle
     */
    public function setAnimeTitle($animeTitle)
    {
        $this->animeTitle = $animeTitle;
    }

    /**
     * @return mixed
     */
    public function getReset()
    {
        return $this->reset;
    }

    /**
     * @param mixed $reset
     */
    public function setReset($reset)
    {
        $this->reset = $reset;
    }

    /**
     * @return mixed
     */
    public function getHandicap()
    {
        return $this->handicap;
    }

    /**
     * @param mixed $handicap
     */
    public function setHandicap($handicap)
    {
        $this->handicap = $handicap;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function getAnimeCrawler()
    {
        return $this->animeCrawler;
    }

    /**
     * @param mixed $animeCrawler
     */
    public function setAnimeCrawler($animeCrawler)
    {
        $this->animeCrawler = $animeCrawler;
    }

}
