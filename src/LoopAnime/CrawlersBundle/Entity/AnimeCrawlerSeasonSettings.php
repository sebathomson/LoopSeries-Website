<?php

namespace LoopAnime\CrawlersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LoopAnime\AppBundle\Entity\BaseEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("animes_crawlers_seasons")
 * @ORM\Entity()
 */
class AnimeCrawlerSeasonSettings extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="LoopAnime\CrawlersBundle\Entity\AnimesCrawlers", inversedBy="settings")
     * @ORM\JoinColumn(nullable=false)
     **/
    protected $crawler;

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
        return $this->reset ? true : false;
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
     * @return AnimesCrawlers
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * @param AnimesCrawlers $crawler
     */
    public function setCrawler(AnimesCrawlers $crawler)
    {
        $this->crawler = $crawler;
    }

    public function __toString()
    {
        return (string)$this->season;
    }

    public function toArray()
    {
        return [
            'season' => $this->getSeason(),
            'title' => $this->getAnimeTitle(),
            'episode' => $this->getEpisodeTitle(),
            'reset' => $this->getReset(),
            'handicap' => $this->getHandicap()
        ];
    }
}
