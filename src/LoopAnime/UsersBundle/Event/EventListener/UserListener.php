<?php

namespace LoopAnime\UsersBundle\Event\EventListener;

use Doctrine\ORM\EntityManager;
use LoopAnime\UsersBundle\Entity\Invitation;
use LoopAnime\UsersBundle\Entity\UsersPreferences;
use LoopAnime\UsersBundle\Event\UserConnectEvent;
use LoopAnime\UsersBundle\Event\UserCreatedEvent;

class UserListener
{

    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function onUserCreate(UserCreatedEvent $event)
    {
        $user = $event->getUser();
        $userPreferences = new UsersPreferences($user);
        $this->em->persist($userPreferences);
        $this->em->flush();
    }

    public function onUserConnect(UserConnectEvent $event)
    {

    }

}
