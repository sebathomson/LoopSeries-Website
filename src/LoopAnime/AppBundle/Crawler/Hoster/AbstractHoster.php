<?php
namespace LoopAnime\AppBundle\Crawler\Hoster;


use LoopAnime\AppBundle\Crawler\Exception\MalformedHosterException;

abstract class AbstractHoster implements HosterInterface
{

    protected $page;
    protected $lastPageContent;

    public function __constructor() {
        $this->page = 0;
    }

    public function recreatePageParameter($link)
    {
        $pageParameter = $this->getPageParameter();
        if (!$pageParameter) {
            throw new MalformedHosterException('Page Parameter is required on the hoster ' . get_class($this));
        }
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
