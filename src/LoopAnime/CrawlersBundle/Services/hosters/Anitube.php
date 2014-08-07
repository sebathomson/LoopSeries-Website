<?php

namespace LoopAnime\CrawlersBundle\Services\hosters;

class Anitube extends Hosters {

    public function isNeededLook4Anime()
    {
        return false;
    }

    public function getAnimesSearchLink()
    {
        return false;
    }

    public function getEpisodesSearchLink()
    {
        return "http://www.anitube.se/search/?search_id={search_term}";
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
        if(preg_match('/^.+search\/\?.+$/',$link)) {
            $link = str_replace("search/?","search/".$this->page."/?",$link);
        }
        $link = preg_replace('/search\/(\d)\/\?/','search/'.$this->page.'/?',$link);

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