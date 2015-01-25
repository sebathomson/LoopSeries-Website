<?php

namespace LoopAnime\AppBundle\Parser;


class ParserSeason implements ParserInterface {

    private $number;
    /** @var ParserEpisode[] */
    private $animeEpisodes = [];
    private $poster;
    private $title;

    public function __construct($number, $poster = "", $title = "") {
        $this->number = $number;
        $this->poster = $poster;
        $this->title = $title;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getPoster()
    {
        return $this->poster;
    }

    public function setEpisode(ParserEpisode $parserEpisode)
    {
        $this->animeEpisodes[] = $parserEpisode;
    }

    public function getTotalEpisodes()
    {
        return count($this->animeEpisodes);
    }

    public function getEpisodes()
    {
        return $this->animeEpisodes;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
