<?php

namespace LoopAnime\AppBundle\Sync\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\AppBundle\Sync\Handler\Exception\UserIsNotValidException;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\UsersBundle\Entity\Users;
use Symfony\Component\Security\Core\SecurityContext;

abstract class AbstractHandler {

    protected $apiKey;
    protected $em;

    public function __construct($apiKey, ObjectManager $em)
    {
        $this->apiKey = $apiKey;
        $this->em = $em;
    }

    public function checkIfUserExists(Users $user)
    {
        $url = $this->getUserApiUrl();
        $this->callCurl($url, null, $user);
        return true;
    }

    abstract protected function callCurl($url, array $POST = null, Users $user);
    abstract public function markAsSeenEpisode(AnimesEpisodes $episode, Users $user);
    abstract public function importSeenEpisodes(Users $user);

    abstract protected function getUserApiUrl();
    abstract protected function getImportApiUrl();
    abstract protected function getMarkEpisodeSeenApiUrl();

    abstract public function getName();
}
