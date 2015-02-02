<?php

namespace LoopAnime\AppBundle\Sync\Services;

use LoopAnime\AppBundle\Sync\Enum\SyncEnum;
use LoopAnime\AppBundle\Sync\Exception\HandlerWithDuplicatedName;
use LoopAnime\AppBundle\Sync\Exception\HandlerWithoutName;
use LoopAnime\AppBundle\Sync\Handler\AbstractHandler;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\UsersBundle\Entity\Users;

class SyncService
{

    /** @var AbstractHandler[] */
    private $handler;

    public function syncEpisodeSeen(AnimesEpisodes $episode, Users $user)
    {
        try {
            if(!empty($user->getTraktUsername())) {
                $this->handler[SyncEnum::SYNC_TRAKT]->markAsSeenEpisode($episode);
            }
            if(!empty($user->getMALUsername())) {
                $this->handler[SyncEnum::SYNC_MAL]->markAsSeenEpisode($episode);
            }
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }

    public function addHandler(AbstractHandler $handler)
    {
        if(empty($handler->getName())) {
            throw new HandlerWithoutName($handler);
        }
        if(isset($this->handler[$handler->getName()])) {
            throw new HandlerWithDuplicatedName($handler);
        }
        $this->handler[$handler->getName()] = $handler;
    }

    /**
     * @param Users $user
     * @param string $syncAdapter
     * @return bool
     */
    public function checkIfUserExists(Users $user, $syncAdapter)
    {
        return $this->handler[$syncAdapter]->checkIfUserExists($user);
    }

    /**
     * @param $syncAdapter
     * @return bool
     */
    public function importSeenEpisodes($syncAdapter)
    {
        return $this->handler[$syncAdapter]->importSeenEpisodes();
    }

}
