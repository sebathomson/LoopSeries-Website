<?php

namespace LoopAnime\AppBundle\Crawler\Strategy;

use LoopAnime\AppBundle\Crawler\Hoster\HosterInterface;
use LoopAnime\CrawlersBundle\Entity\AnimesCrawlers;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

interface StrategyInterface
{

    /**
     * @return AnimesCrawlers|null
     */
    public function getCrawlerSettings();

    public function execute(AnimesEpisodes $episode, HosterInterface $hoster, $uri = false);

    public function getName();

}
