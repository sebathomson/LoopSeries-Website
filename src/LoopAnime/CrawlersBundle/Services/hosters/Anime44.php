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
        return false;
    }

    public function isPaginated()
    {
        return true;
    }

    public function getNextPage($link)
    {
        $this->page++;
        if($this->page === 50) {
            throw new \Exception("Looping till the page 50, stoping here as i could be looping forever");
        }
        if(strpos($link,"/page/") === false) {
            $link = $link . '/page/' . $this->page;
        }
        $link = preg_replace('/page\/\d+/','page/'.$this->page,$link);

        $webpageContent = file_get_contents($link);
        if($this->lastPageContent === $webpageContent)
            return false;

        $this->lastPageContent = $webpageContent;
        return $link;
    }

    public function getPageParameter()
    {
        return false;
    }
}