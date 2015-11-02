<?php
namespace LoopAnime\ApiBundle\Response;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use LoopAnime\ShowsBundle\Entity\Animes as Entity;

/**
 * Class AnimesResponse
 * @package LoopAnime\ApiBundle\Response
 *
 * @ExclusionPolicy("NONE")
 */
class AnimesResponse
{

    /**
     * @Type("integer")
     * @Groups({"list"})
     */
    private $id;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $title;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $poster;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $genres;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $themes;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $plotSummary;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $runningTime;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $startTime;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $endTime;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $status;

    /**
     * @Type("integer")
     * @Groups({"list"})
     */
    private $rating;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $imdbId;

    /**
     * @Type("integer")
     * @Groups({"list"})
     */
    private $ratingCount;

    /**
     * @Type("integer")
     * @Groups({"list"})
     */
    private $ratingUp;

    /**
     * @Type("integer")
     * @Groups({"list"})
     */
    private $ratingDown;

    /**
     * @Type("integer")
     * @Groups({"list"})
     */
    private $lastUpdated;

    /**
     * @Type("datetime")
     * @Groups({"list"})
     */
    private $lastUpdate;

    /**
     * @Type("datetime")
     * @Groups({"list"})
     */
    private $createTime;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $typeSeries;

    /**
     * @Type("string")
     * @Groups({"list"})
     */
    private $bigPoster;

    /**
     * @Type("array")
     * @Groups({"list"})
     * @SerializedName("animes_seasons")
     */
    private $animesSeasons;

    public function __construct(Entity $anime)
    {
        $this->id = $anime->getId();
        $this->lastUpdate = $anime->getLastUpdate();
        $this->lastUpdated = $anime->getLastUpdated();
        $this->createTime = $anime->getCreateTime();
        $this->rating = $anime->getRating();
        $this->ratingCount = $anime->getRatingCount();
        $this->ratingDown = $anime->getRatingDown();
        $this->ratingUp = $anime->getRatingUp();
        $this->imdbId = $anime->getImdbId();
        $this->title = $anime->getTitle();
        $this->poster = $anime->getPoster();
        $this->typeSeries = $anime->getTypeSeries();
        $this->bigPoster = $anime->getBigPoster();
        $this->status = $anime->getStatus();
        $this->genres = $anime->getGenres();
        $this->themes = $anime->getThemes();
        $this->plotSummary = $anime->getPlotSummary();
        $this->runningTime = $anime->getRunningTime();
        $this->startTime = $anime->getStartTime();
        $this->endTime = $anime->getEndTime();
        $this->animesSeasons = [];
        if ($anime->getAnimeSeasons()->count() > 0) {
            foreach ($anime->getAnimeSeasons() as $season) {
                $this->animesSeasons[] = $season->getId();
            }
        }
    }

}
