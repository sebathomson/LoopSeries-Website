<?php
namespace LoopAnime\AppBundle\Crawler\Hoster;


interface HosterInterface
{

    public function search($searchTerm);

    public function isPaginated();

    public function getPageParameter();

    public function getEpisodeMirros($link);

    public function getNextPage($link, $page);

    public function getSubtitles();

    public function getStrategy();

    public function getName();

}
