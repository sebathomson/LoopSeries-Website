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
     * @ORM\Column(name="id_crawl", type="integer")
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
     * @ORM\Column(name="title_adapted", type="string", length=500)
     */
    private $titleAdapted;

    /**
     * @var string
     *
     * @ORM\Column(name="seasonsAsNew", type="array", length=500)
     */
    private $seasonsAsNew;

    /**
     * @var string
     *
     * @ORM\Column(name="episode_adapted", type="string", length=500)
     */
    private $episodeAdapted;

    /**
     * @var string
     *
     * @ORM\Column(name="episode_clean", type="string", length=500)
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

    public function setSeasonsAsNew($seasonsAsNew)
    {
        $this->seasonsAsNew = $seasonsAsNew;
        return $this;
    }

    public function getSeasonsAsNew()
    {
        return $this->seasonsAsNew;
    }
}
