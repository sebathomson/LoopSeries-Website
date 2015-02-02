<?php

namespace LoopAnime\AppBundle\Parser;


class ParserEpisode implements ParserInterface {

    private $episodeNumber;
    private $episodeTitle;
    private $poster;
    private $rating;
    private $views;
    private $comments;
    private $airDate;
    private $ratingCount;
    private $summary;
    private $imdbId;

    /**
     * @return mixed
     */
    public function getEpisodeTitle()
    {
        return $this->episodeTitle;
    }

    /**
     * @param mixed $episodeTitle
     */
    public function setEpisodeTitle($episodeTitle)
    {
        $this->episodeTitle = $episodeTitle;
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
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param mixed $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getAirDate()
    {
        return $this->airDate;
    }

    /**
     * @param mixed $airDate
     */
    public function setAirDate($airDate)
    {
        $this->airDate = $airDate;
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
    public function getAbsoluteNumber()
    {
        return $this->absoluteNumber;
    }

    /**
     * @param mixed $absoluteNumber
     */
    public function setAbsoluteNumber($absoluteNumber)
    {
        $this->absoluteNumber = $absoluteNumber;
    }

    /**
     * @return mixed
     */
    public function getEpisodeNumber()
    {
        return $this->episodeNumber;
    }

    /**
     * @param mixed $episodeNumber
     */
    public function setEpisodeNumber($episodeNumber)
    {
        $this->episodeNumber = $episodeNumber;
    }
    private $absoluteNumber;


}
