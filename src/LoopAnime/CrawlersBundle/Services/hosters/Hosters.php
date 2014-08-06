<?php

namespace LoopAnime\CrawlersBundle\Services\hosters;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class Hosters extends Controller{

    private $neededLook4Anime;


    abstract public function isNeededLook4Anime();
    abstract public function getAnimesSearchLink();
    abstract public function getEpisodesSearchLink();

}