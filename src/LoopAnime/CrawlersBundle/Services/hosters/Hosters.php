<?php

namespace LoopAnime\CrawlersBundle\Services\hosters;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class Hosters extends Controller
{

    protected $page;
    protected $lastPageContent;

    abstract public function isNeededLook4Anime();
    abstract public function getAnimesSearchLink();
    abstract public function getEpisodesSearchLink();
    abstract public function isPaginated();
    abstract public function getPageParameter();
    abstract public function getEpisodeDirectLink($link);
    abstract public function getSubtitles();
    abstract public function getName();

    public function __constructor() {
        $this->page = 0;
    }

    public function recreatePageParameter($link)
    {
        $pageParameter = $this->getPageParameter();
        $link = preg_replace("/^.+([&?]$pageParameter=.+)[\\b&]/","",$link);
        if(strpos(basename($link),"?") !== false) {
            return $link . "?$pageParameter=" . $this->page;
        } else {
            return $link . "&$pageParameter=" . $this->page;
        }
    }

    public function getNextPage($link)
    {
        $this->page++;
        if($this->page === 50) {
            throw new \Exception("Looping till the page 50, stoping here as i could be looping forever");
        }
        $link = $this->recreatePageParameter($link);
        $webpageContent = file_get_contents($link);
        if($this->lastPageContent === $webpageContent)
            return false;

        $this->lastPageContent = $webpageContent;
        return $link;
    }

    public function resetInstance()
    {
        $blankInstance = new static;
        $reflBlankInstance = new \ReflectionClass($blankInstance);
        foreach ($reflBlankInstance->getProperties() as $prop) {
            $prop->setAccessible(true);
            $this->{$prop->name} = $prop->getValue($blankInstance);
        }
        $this->page = 0;
    }
}
