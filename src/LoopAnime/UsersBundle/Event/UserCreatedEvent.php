<?php

namespace LoopAnime\UsersBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use LoopAnime\UsersBundle\Entity\Users;

class UserCreatedEvent extends Event {

    protected $user;

    public function __construct(Users $user)
    {
        $this->user = $user;
    }

    public function getUser() {
        return $this->user;
    }

}
