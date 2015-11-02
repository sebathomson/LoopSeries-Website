<?php
namespace LoopAnime\ApiBundle\Response;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Type;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes as Entity;

/**
 * Class AnimesEpisodesResponse
 * @package LoopAnime\ApiBundle\Response
 *
 * @ExclusionPolicy("NONE")
 */
class AnimesEpisodesResponse
{

    /**
     * @Type("integer")
     */
    private $id;

    /**
     * @Type("integer")
     */
    private $episode;

    /**
     * @Type("string")
     */
    private $episodeTitle;

    /**
     * @Type("string")
     */
    private $poster;

    /**
     * @Type("integer")
     */
    private $rating;

    /**
     * @Type("integer")
     */
    private $views;

    /**
     * @Type("integer")
     */
    private $comments;

    /**
     * @Type("datetime")
     */
    private $airDate;

    /**
     * @Type("string")
     */
    private $summary;

    /**
     * @Type("integer")
     */
    private $ratingCount;

    /**
     * @Type("string")
     */
    private $imdbId;

    /**
     * @Type("integer")
     */
    private $ratingUp;

    /**
     * @Type("integer")
     */
    private $ratingDown;

    /**
     * @Type("datetime")
     */
    private $lastUpdate;

    /**
     * @Type("datetime")
     */
    private $createTime;

    /**
     * @Type("integer")
     */
    private $absoluteNumber;

    public function __construct(Entity $anime)
    {
        $this->id = $anime->getId();
        $this->lastUpdate = $anime->getLastUpdate();
        $this->createTime = $anime->getCreateTime();
        $this->rating = $anime->getRating();
        $this->ratingCount = $anime->getRatingCount();
        $this->ratingDown = $anime->getRatingDown();
        $this->ratingUp = $anime->getRatingUp();
        $this->imdbId = $anime->getImdbId();
        $this->poster = $anime->getPoster();
        $this->season = $anime->getSeason()->getId();
        $this->episodeTitle = $anime->getEpisodeTitle();
        $this->episode = $anime->getEpisode();
        $this->summary = $anime->getSummary();
        $this->airDate = $anime->getAirDate();
        $this->absoluteNumber = $anime->getAbsoluteNumber();
        $this->views = $anime->getViews();
        $this->comments = $anime->getComments();
    }

}
