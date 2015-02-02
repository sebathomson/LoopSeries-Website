<?php

namespace LoopAnime\AppBundle\Parser;


class ParserAnime implements ParserInterface {

    private $title;
    private $poster;
    private $themes;
    private $summary;
    private $runningTime;
    private $startTime;
    private $endTime;
    private $status;
    private $rating;
    private $imdbId;
    private $ratingCount;
    private $genres;
    /** @var ParserSeason[] */
    private $animeSeasons = [];
    private $animeKey;
    private $apiId;


    public function __construct($title = "", $poster = "", $themes = "", $summary = "", $runningTime = "", $startTime = "", $endTime = "", $status = "", $rating = "", $imdbId = "", $ratingCount = "", $genres = "", $animeKey = null, $apiId = null)
    {
        $this->title = $title;
        $this->poster = $poster;
        $this->themes = $themes;
        $this->summary = $summary;
        $this->runningTime = $runningTime;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->status = $status;
        $this->rating = $rating;
        $this->imdbId = $imdbId;
        $this->ratingCount = $ratingCount;
        $this->genres = $genres;
        $this->animeKey = $animeKey;
        $this->apiId = $apiId;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * @param mixed $themes
     */
    public function setThemes($themes)
    {
        $this->themes = $themes;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return mixed
     */
    public function getRunningTime()
    {
        return $this->runningTime;
    }

    /**
     * @param mixed $runningTime
     */
    public function setRunningTime($runningTime)
    {
        $this->runningTime = $runningTime;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }

    /**
     * @param mixed $imdbId
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;
    }


    /**
     * @return mixed
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param mixed $genres
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;
    }

    /**
     * @return mixed
     */
    public function getRatingCount()
    {
        return $this->ratingCount;
    }

    /**
     * @param mixed $ratingCount
     */
    public function setRatingCount($ratingCount)
    {
        $this->ratingCount = $ratingCount;
    }

    /**
     * @return mixed
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param mixed $poster
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;
    }

    public function setSeason(ParserSeason $parserSeason)
    {
        $this->animeSeasons[] = $parserSeason;
    }

    public function getSeasons()
    {
        return $this->animeSeasons;
    }

    public function getAnimeKey()
    {
        return $this->animeKey;
    }

    public function getApiId()
    {
        return $this->apiId;
    }

}

