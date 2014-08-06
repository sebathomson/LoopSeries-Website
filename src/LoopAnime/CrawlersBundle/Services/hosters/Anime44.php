<?php

namespace LoopAnime\CrawlersBundle\Services\hosters;

class Anime44 extends Hosters {

    private $animeSearchLink = "http://www.anime44.com/anime/search?key={search_term}&search_submit=Go";

    public function isNeededLook4Anime()
    {
        return true;
    }

    public function getAnimesSearchLink()
    {
        return $this->animeSearchLink;
    }

    public function getEpisodesSearchLink()
    {
        // TODO: Implement getEpisodesSearchLink() method.
    }
}