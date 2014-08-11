<?php

namespace LoopAnime\ShowsBundle\Services;

use LoopAnime\CrawlersBundle\Services\hosters\Anime44;
use LoopAnime\CrawlersBundle\Services\hosters\Anitube;
use LoopAnime\CrawlersBundle\Services\hosters\Hosters;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;

class VideoService
{

    private $hq;
    private $lq;
    private $dq;

    public function getDirectVideoLink(AnimesLinks $link)
    {
        $hoster = ucfirst(strtolower($link->getHoster()));
        /** @var Hosters $hoster */
        // TODO i dont know why i cant use a factory like this should be like this!
        //$hoster = new $hoster();

        switch($hoster) {
            case "Anime44":
                $hoster = new Anime44();
                break;
            case "Anitube":
                $hoster = new Anitube();
                break;
            default:
                throw new \Exception("I dont have the hoster $hoster case in the switch");
                break;
        }

        if($link = $hoster->getEpisodeDirectLink($link->getLink())) {
            return $link;
        } else {
            return false;
        }
    }

    public function getHQVideoLink()
    {
        return $this->hq;
    }

    public function hasHQVideoLink()
    {
        if(!empty($this->hq)) {
            return true;
        } else {
            return false;
        }
    }

    public function getLQVideoLink()
    {
        return $this->lq;
    }

    public function getDQVideoLink()
    {
        return $this->dq;
    }

}