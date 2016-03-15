<?php

namespace LoopAnime\AppBundle\Crawler\Strategy;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Persistence\ObjectRepository;
use LoopAnime\AppBundle\Crawler\Service\CrawlerService;
use LoopAnime\CrawlersBundle\Entity\AnimesCrawlers;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

abstract class AbstractStrategy implements StrategyInterface
{
    /** @var ApcuCache */
    protected $cache;
    /** @var  array|null */
    protected $titles;
    /** @var  array|null */
    protected $episodeTitles;
    /** @var ObjectRepository */
    private $crawlSettingsRepo;
    /** @var CrawlerService */
    protected $crawlerService;
    /** @var  AnimesCrawlers|null */
    protected $crawlerSettings;

    public function __construct(ObjectRepository $crawlSettingsRepo, ApcuCache $cache, CrawlerService $crawlerService)
    {
        $this->crawlSettingsRepo = $crawlSettingsRepo;
        $this->cache = $cache;
        $this->crawlerService = $crawlerService;
    }

    /**
     * @param AnimesEpisodes $episode
     * @return AnimesCrawlers
     */
    protected function findCrawlerSettings(AnimesEpisodes $episode)
    {
        $this->crawlerSettings = $this->crawlSettingsRepo->findOneBy(['anime' => $episode->getSeason()->getAnime()->getId()]);
        return $this->crawlerSettings;
    }

    /**
     * @return AnimesCrawlers|null
     */
    public function getCrawlerSettings() {
        return $this->crawlerSettings;
    }

    /**
     * @param $titles
     */
    protected function addPossibleTitles($titles) {
        $this->titles = $titles;
    }

    /**
     * @param $titles
     */
    protected function addPossibleEpisodeTitles($titles) {
        $this->episodeTitles = $titles;
    }

    /**
     * @return array|null
     */
    public function getPossibleTitles() {
        return $this->titles;
    }

    /**
     * @return array|null
     */
    public function getPossibleEpisodeTitles() {
        return $this->episodeTitles;
    }
}
