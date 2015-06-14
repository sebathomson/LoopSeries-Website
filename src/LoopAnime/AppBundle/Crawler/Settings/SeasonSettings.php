<?php

namespace LoopAnime\AppBundle\Crawler\Settings;

class SeasonSettings implements \Serializable
{

    private $season;
    private $episodeTitle;
    private $animeTitle;
    private $reset;
    private $handicap;

    public function __construct($data = [])
    {
        if (!empty($data['season'])) {
            $this->season = $data['season'];
        }
        if (!empty($data['title'])) {
            $this->animeTitle = $data['title'];
        }
        if (!empty($data['episode'])) {
            $this->episodeTitle = $data['episode'];
        }
        if (!empty($data['handicap'])) {
            $this->handicap = $data['handicap'];
        }
        if (!empty($data['reset'])) {
            $this->reset = $data['reset'];
        }
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param mixed $season
     */
    public function setSeason($season)
    {
        $this->season = $season;
    }

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
    public function getAnimeTitle()
    {
        return $this->animeTitle;
    }

    /**
     * @param mixed $animeTitle
     */
    public function setAnimeTitle($animeTitle)
    {
        $this->animeTitle = $animeTitle;
    }

    /**
     * @return mixed
     */
    public function getReset()
    {
        return $this->reset;
    }

    /**
     * @param mixed $reset
     */
    public function setReset($reset)
    {
        $this->reset = $reset;
    }

    /**
     * @return mixed
     */
    public function getHandicap()
    {
        return $this->handicap;
    }

    /**
     * @param mixed $handicap
     */
    public function setHandicap($handicap)
    {
        $this->handicap = $handicap;
    }

    public function toArray()
    {
        return [
            'season' => $this->season,
            'title' => $this->animeTitle,
            'episode' => $this->episodeTitle,
            'reset' => $this->reset,
            'handicap' => $this->handicap
        ];
    }

    public function serialize()
    {
        return serialize($this->toArray());
    }

    public function unserialize($serialized)
    {
        $this->data = unserialize($serialized);
        $this->__construct($this->data);
    }
}
