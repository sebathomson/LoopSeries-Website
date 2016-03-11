<?php

namespace LoopAnime\AppBundle\Crawler\Service;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\AppBundle\Crawler\Exception\InvalidHosterException;
use LoopAnime\AppBundle\Crawler\Exception\InvalidStrategyException;
use LoopAnime\AppBundle\Crawler\Guesser\GuesserInterface;
use LoopAnime\AppBundle\Crawler\Hoster\HosterInterface;
use LoopAnime\AppBundle\Crawler\Strategy\StrategyInterface;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

class CrawlerService
{
    /** @var StrategyInterface[] */
    protected $strategies;
    /** @var  StrategyInterface */
    protected $strategy;
    /** @var HosterInterface[] */
    private $hosters;
    /** @var  GuesserInterface */
    private $guesser;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param AnimesEpisodes $animeEpisodes
     * @param HosterInterface|string $hoster
     * @return array|null
     * @throws \Exception
     */
    public function crawlEpisode(AnimesEpisodes $animeEpisodes, $hoster)
    {
        if (!$hoster instanceof HosterInterface) {
            $hoster = $this->getHoster($hoster);
        }
        $this->strategy = $this->getStrategy($hoster->getStrategy());
        /** @var GuesserInterface $guesser */
        $this->guesser = $this->strategy->execute($animeEpisodes, $hoster);
        if (!$this->guesser->isExactMatch()) {
            throw new \Exception('Not the best match - ' . $this->guesser->getLog());
        }

        return $hoster->getEpisodeMirrors($guesser->getUri());
    }

    /**
     * @return GuesserInterface|null
     */
    public function getLastGuesser()
    {
        if (empty($this->guesser)) {
            return null;
        }
        return $this->guesser;
    }

    /**
     * @return StrategyInterface|null
     */
    public function getLastStrategy()
    {
        return $this->strategy;
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
        if (!isset($this->hosters[$hoster])) {
            throw new InvalidHosterException($hoster);
        }
        return $this->hosters[$hoster];
    }

    public function getStrategy($strategy)
    {
        if (!isset($this->strategies[$strategy])) {
            throw new InvalidStrategyException($strategy);
        }
        return $this->strategies[$strategy];
    }

}
