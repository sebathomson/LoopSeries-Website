<?php

namespace LoopAnime\AppBundle\Crawler\Strategy;

use LoopAnime\AppBundle\Crawler\Enum\StrategyEnum;
use LoopAnime\AppBundle\Crawler\Guesser\UrlGuesser;
use LoopAnime\AppBundle\Crawler\Hoster\HosterInterface;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

class SerieSearchStrategy extends AbstractStrategy implements StrategyInterface
{

    public function execute(AnimesEpisodes $episode, HosterInterface $hosterInterface, $uri = false)
    {
        $crawlSettings = $this->getCrawlSettings($episode);
        $seasonSettings = $crawlSettings->getMinimalSeasonSettings($episode->getSeason()->getSeason());
        $title = $seasonSettings->getAnimeTitle();

        $idAnime = $episode->getSeason()->getAnime()->getId();
        if (!$this->cache->fetch('sss_' . $idAnime)) {
            $page = 0;
            $found = false;
            while (!$found) {
                $link = $hosterInterface->getNextPage($hosterInterface->search($title), $page);
                $contents = file_get_contents($link);
                $guesser = new UrlGuesser($contents, [$episode->getSeason()->getAnime()->getTitle(), $title]);
                $guesser->guess();
                $page++;
                if ($guesser->isExactMatch() && !empty($guesser->getUri())) {
                    $found = true;
                    $this->cache->save('sss_' . $idAnime, $guesser->getUri());
                }
            }
        }

        return $this->cache->fetch('sss_' . $idAnime);
    }

    public function getName()
    {
        return StrategyEnum::STRATEGY_ANIME_SEARCH;
    }

}
