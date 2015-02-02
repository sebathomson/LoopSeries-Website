<?php

namespace LoopAnime\ShowsBundle\Event;

use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;
use LoopAnime\UsersBundle\Entity\Users;
use Symfony\Component\EventDispatcher\Event;

class EpisodeSeenEvent extends Event
{
    protected $user;
    protected $episode;
    protected $link;
    protected $syncService;

    public function __construct(Users $user, AnimesEpisodes $episode, AnimesLinks $link)
    {
        $this->user = $user;
        $this->episode = $episode;
        $this->link = $link;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getEpisode()
    {
        return $this->episode;
    }

    public function getLink()
    {
        return $this->link;
    }

}
