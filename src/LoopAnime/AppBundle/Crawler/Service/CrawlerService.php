<?php

namespace LoopAnime\AppBundle\Crawler\Service;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\AppBundle\Crawler\Enum\StrategyEnum;
use LoopAnime\AppBundle\Crawler\Hoster\HosterInterface;
use LoopAnime\AppBundle\Crawler\Strategy\SerieSearchStrategy;
use LoopAnime\AppBundle\Crawler\Strategy\StrategyInterface;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

class CrawlerService
{
    /** @var StrategyInterface[] */
    protected $strategies;
    /** @var HosterInterface[] */
    private $hosters;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function addHoster(HosterInterface $hoster)
    {
        $this->hosters[$hoster->getName()] = $hoster;
    }

    public function addStrategies(StrategyInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param AnimesEpisodes $animeEpisodes
     * @param $hoster
     */
    public function crawlEpisode(AnimesEpisodes $animeEpisodes, $hoster)
    {
        $hoster = $this->hosters[$hoster];
        $strategy = $this->strategies[$hoster->getStrategy()];

        $uri = false;
        if ($strategy instanceof SerieSearchStrategy) {
            $uri = $strategy->execute($animeEpisodes, $hoster);
            $strategy = $this->strategies[StrategyEnum::STRATEGY_EPISODE_SEARCH];
        }
        $episodeUri = $strategy->execute($animeEpisodes, $hoster, $uri);

        return $hoster->getEpisodeMirros($episodeUri);
    }
}
