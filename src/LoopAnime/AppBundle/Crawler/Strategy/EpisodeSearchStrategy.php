<?php

namespace LoopAnime\AppBundle\Crawler\Strategy;

use LoopAnime\AppBundle\Crawler\Enum\StrategyEnum;
use LoopAnime\AppBundle\Crawler\Guesser\UrlGuesser;
use LoopAnime\AppBundle\Crawler\Hoster\HosterInterface;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

class EpisodeSearchStrategy extends AbstractStrategy implements StrategyInterface
{
    /** @var  HosterInterface */
    protected $hosterInterface;
    /** @var  AnimesEpisodes */
    protected $episode;
    protected $uri;
    protected $episodeTitles;

    public function execute(AnimesEpisodes $episode, HosterInterface $hosterInterface, $uri = false)
    {
        $this->episode = $episode;
        $this->hosterInterface = $hosterInterface;
        $this->uri = $uri;
        $this->validate();
        $this->createEpisodeTitles();

        if ($uri) {
            $page = 0;
            while ($page < 50) {
                $page++;
                $guesser = $this->guess($uri, $page);
                if ($guesser->isExactMatch() && !empty($guesser->getUri())) {
                    return $guesser->getUri();
                }
            }
        } else {
            foreach ($this->episodeTitles as $episodeTitle) {
                $uri = $hosterInterface->search($episodeTitle);
                $page = 0;
                while ($page < 51) {
                    $guesser = $this->guess($uri, $page);
                    $page++;
                    if ($guesser->isExactMatch() && !empty($guesser->getUri())) {
                        return $guesser->getUri();
                    }
                }
            }
        }

        return false;
    }

    private function guess($uri, $page)
    {
        $link = $this->hosterInterface->getNextPage($uri, $page);
        $contents = file_get_contents($link);
        $guesser = new UrlGuesser($contents, $this->episodeTitles);
        $guesser->guess();
        return $guesser;
    }

    private function createEpisodeTitles()
    {
        $episodeTitles = [$this->episode->getSeason()->getAnime()->getTitle()];

        $crawlSettings = $this->getCrawlSettings($this->episode);
        $seasonSettings = $crawlSettings->getMinimalSeasonSettings($this->episode->getSeason()->getSeason());
        if ($seasonSettings) {
            $episodeTitles =
                array_unique(
                    array_filter(
                        array_merge(
                            $episodeTitles,
                            explode(",", $seasonSettings->getEpisodeTitle()),
                            explode(",", $seasonSettings->getAnimeTitle())
                        )
                    )
                );
        }

        // Adding the number of the episode to the title
        foreach ($episodeTitles as &$title) {
            $title = " " . $this->episode->getEpisode();
        }
        $this->episodeTitles = $episodeTitles;
    }

    private function validate()
    {
        if ($this->hosterInterface->getStrategy() === StrategyEnum::STRATEGY_ANIME_SEARCH && !$this->uri) {
            throw new \Exception("URI needs to be set when anime search strategy is used");
        }
    }

    public function getName()
    {
        return StrategyEnum::STRATEGY_EPISODE_SEARCH;
    }
}
