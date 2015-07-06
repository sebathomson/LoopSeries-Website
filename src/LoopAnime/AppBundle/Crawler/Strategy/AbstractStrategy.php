<?php

namespace LoopAnime\AppBundle\Crawler\Strategy;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Persistence\ObjectRepository;
use LoopAnime\CrawlersBundle\Entity\AnimesCrawlers;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

abstract class AbstractStrategy implements StrategyInterface
{
    protected $cache;
    private $crawlSettingsRepo;

    public function __construct(ObjectRepository $crawlSettingsRepo, ApcCache $cache)
    {
        $this->crawlSettingsRepo = $crawlSettingsRepo;
        $this->cache = $cache;
    }

    /**
     * @param AnimesEpisodes $episode
     * @return AnimesCrawlers
     */
    protected function getCrawlSettings(AnimesEpisodes $episode)
    {
        return $this->crawlSettingsRepo->findOneBy(['anime' => $episode->getSeason()->getAnime()->getId()]);
    }

    protected function search($searchTerm)
    {

    }

}
