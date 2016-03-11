<?php

namespace LoopAnime\AppBundle\Crawler\Strategy;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Persistence\ObjectRepository;
use LoopAnime\AppBundle\Crawler\Service\CrawlerService;
use LoopAnime\CrawlersBundle\Entity\AnimesCrawlers;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

abstract class AbstractStrategy implements StrategyInterface
{
    /** @var ApcCache */
    protected $cache;
    /** @var ObjectRepository */
    private $crawlSettingsRepo;
    /** @var CrawlerService */
    protected $crawlerService;
    /** @var  AnimesCrawlers|null */
    protected $crawlerSettings;

    public function __construct(ObjectRepository $crawlSettingsRepo, ApcCache $cache, CrawlerService $crawlerService)
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

}
