<?php

namespace LoopAnime\AppBundle\Sync\Services;

use LoopAnime\AppBundle\Sync\Enum\SyncEnum;
use LoopAnime\AppBundle\Sync\Exception\HandlerNotFound;
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
            $handler = false;
            if(!empty($user->getTraktAccessToken())) {
                $handler = $this->getHandler(SyncEnum::SYNC_TRAKT);
            }
            if(!empty($user->getMALUsername())) {
                $handler = $this->getHandler(SyncEnum::SYNC_MAL);
            }
            if (!$handler) {
                return false;
            }
            $handler->markAsSeenEpisode($episode, $user);
        } catch(\Exception $e) {
            return false;
        }
        return true;
    }

    public function setHandler(AbstractHandler $handler)
    {
        if(empty($handler->getName())) {
            throw new HandlerWithoutName($handler);
        }
        if(isset($this->handler[$handler->getName()])) {
            throw new HandlerWithDuplicatedName($handler);
        }
        $this->handler[$handler->getName()] = $handler;
    }

    public function getHandler($handler)
    {
        if (!isset($this->handler[$handler])) {
            throw new HandlerNotFound($handler);
        }
        return $this->handler[$handler];
    }

    /**
     * @param Users $user
     * @param string $syncAdapter
     * @return bool
     */
    public function checkIfUserExists(Users $user, $syncAdapter)
    {
        $handler = $this->getHandler($syncAdapter);
        return $handler->checkIfUserExists($user);
    }

    /**
     * @param Users $user
     * @param $syncAdapter
     * @return bool
     * @throws HandlerNotFound
     */
    public function importSeenEpisodes(Users $user, $syncAdapter)
    {
        $handler = $this->getHandler($syncAdapter);
        return $handler->importSeenEpisodes($user);
    }

}
