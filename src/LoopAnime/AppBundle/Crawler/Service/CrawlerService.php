<?php

namespace LoopAnime\AppBundle\Crawler\Service;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\AppBundle\Crawler\Hoster\HosterInterface;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

class CrawlerService
{

    private $hosters;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function addHoster(HosterInterface $hoster)
    {
        $this->hosters[$hoster->getName()] = $hoster;
    }

    public function crawlEpisode(AnimesEpisodes $animeEpisodes)
    {
        // TODO
    }
}
