<?php

namespace LoopAnime\AppBundle\Crawler\Service;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\AppBundle\Crawler\Guesser\GuesserInterface;
use LoopAnime\AppBundle\Crawler\Hoster\HosterInterface;
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

    /**
     * @param AnimesEpisodes $animeEpisodes
     * @param $hoster
     * @return
     * @throws \Exception
     */
    public function crawlEpisode(AnimesEpisodes $animeEpisodes, $hoster)
    {
        if (!$hoster instanceof HosterInterface) {
            $hoster = $this->hosters[$hoster];
        }
        $strategy = $this->strategies[$hoster->getStrategy()];
        /** @var GuesserInterface $guesser */
        $guesser = $strategy->execute($animeEpisodes, $hoster);

        if (!$guesser->isExactMatch()) {
            throw new \Exception('Not the best match - ' . $guesser->getLog());
        }

        return $hoster->getEpisodeMirrors($guesser->getUri());
    }

    public function addHoster(HosterInterface $hoster)
    {
        $this->hosters[$hoster->getName()] = $hoster;
    }

    public function addStrategy(StrategyInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    public function getHoster($hoster)
    {
        return $this->hosters[$hoster];
    }

    public function getStrategy($strategy)
    {
        return $this->strategies[$strategy];
    }
}
