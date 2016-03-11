<?php

namespace LoopAnime\AppBundle\Crawler\Strategy;

use LoopAnime\AppBundle\Crawler\Enum\StrategyEnum;
use LoopAnime\AppBundle\Crawler\Guesser\UrlGuesser;
use LoopAnime\AppBundle\Crawler\Hoster\HosterInterface;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

class SerieSearchStrategy extends AbstractStrategy implements StrategyInterface
{
    /** @var AnimesEpisodes */
    protected $episode;
    /** @var  HosterInterface */
    protected $hoster;

    public function execute(AnimesEpisodes $episode, HosterInterface $hosterInterface, $uri = false)
    {
        $this->episode = $episode;
        $this->hoster = $hosterInterface;
        $this->validate();

        $guesser = $this->grabAnimeURI();
        if (empty($guesser) || !$guesser->isExactMatch()) {
            throw new \Exception("The serie was not found - there was no exact math. Log: " . !empty($guesser) ? $guesser->getLog() : '');
        }

        $uri = $guesser->getUri();
        if (empty($uri)) {
            throw new \Exception(sprintf("URI is empty! %s was not found on the hoster %s. Log: %s, URI: %s",
                implode(",", $this->createAnimeTitles()), $hosterInterface->getName(), $guesser->getLog(), $guesser->getUri()));
        }

        /** @var EpisodeSearchStrategy $episodeSearchStrategy */
        $episodeSearchStrategy = $this->crawlerService->getStrategy(StrategyEnum::STRATEGY_EPISODE_SEARCH);
        $episodeSearchStrategy->addPossibleTitles($this->createAnimeTitles());

        return $episodeSearchStrategy->execute($episode, $hosterInterface, $uri);
    }

    /**
     * @return UrlGuesser|null
     */
    private function grabAnimeURI() {
        $titles = $this->createAnimeTitles();
        $idAnime = $this->episode->getSeason()->getAnime()->getId();
        $page = 0;
        foreach ($titles as $title) {
            $continue = true;
            while ($continue) {
                $link = $this->hoster->getNextPage($this->hoster->search($title), $page);
                $contents = file_get_contents($link);
                $guesser = new UrlGuesser($contents, [$this->episode->getSeason()->getAnime()->getTitle(), $title], $this->hoster->getDomain());
                $guesser->guess();
                $page++;
                if ($guesser->isExactMatch() && !empty($guesser->getUri())) {
                    $this->cache->save('sss_' . $this->hoster->getName() . "_" . $idAnime, $guesser->getUri());
                    return $guesser;
                }
                if (!$this->hoster->isPaginated() || $page > 50) {
                    $continue = false;
                }
            }
        }

        return null;
    }

    /**
     * @return array
     */
    private function createAnimeTitles()
    {
        $titles = [$this->episode->getSeason()->getAnime()->getTitle()];
        $crawlSettings = $this->findCrawlerSettings($this->episode);
        if ($crawlSettings) {
            $seasonSettings = $crawlSettings->getMinimalSeasonSettings($this->episode->getSeason()->getSeason());
            $titles[] = $seasonSettings->getAnimeTitle();
        }

        return $titles;
    }

    /**
     * @return bool
     */
    private function validate()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return StrategyEnum::STRATEGY_SERIE_SEARCH;
    }
}
