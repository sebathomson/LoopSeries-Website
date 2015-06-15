<?php
namespace LoopAnime\AppBundle\Crawler\Hoster;


interface HosterInterface
{

    public function isNeededLook4Anime();
    public function getAnimesSearchLink();
    public function getEpisodesSearchLink();
    public function isPaginated();
    public function getPageParameter();
    public function getEpisodeDirectLink($link);
    public function getSubtitles();
    public function getName();

}
