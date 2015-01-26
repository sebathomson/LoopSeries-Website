<?php

namespace LoopAnime\AppBundle\Sync\Implementation;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\AppBundle\Sync\Implementation\Exception\UserIsNotValidException;
use LoopAnime\UsersBundle\Entity\Users;
use Symfony\Component\Security\Core\SecurityContext;

abstract class BaseImplementation {

    protected $apiKey;
    protected $user;
    protected $em;

    public function __construct($apiKey, SecurityContext $context, ObjectManager $em)
    {
        $this->apiKey = $apiKey;
        $this->user = $context->getToken()->getUser();
        $this->em = $em;

        $this->preValidation();
    }

    private function preValidation()
    {
        if(null === $this->user) {
            throw new UserIsNotValidException();
        }
        return true;
    }

    public function checkIfUserExists(Users $user)
    {
        $this->user = $user;
        $url = $this->getUserApiUrl();
        $this->callCurl($url);
        return true;
    }

    abstract protected function callCurl($url, array $POST = null);
    abstract protected function getUserApiUrl();
    abstract protected function getImportApiUrl();
    abstract protected function getMarkEpisodeSeenApiUrl();

}
