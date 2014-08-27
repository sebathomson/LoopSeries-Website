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
        $hoster = "LoopAnime\\CrawlersBundle\\Services\\hosters\\".explode("-",ucfirst(strtolower($link->getHoster())))[0];
        /** @var Hosters $hoster */
        $hoster = new $hoster();

        if($link = $hoster->getEpisodeDirectLink($link->getLink())) {
            return urldecode($link);
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