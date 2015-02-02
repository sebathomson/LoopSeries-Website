<?php

namespace LoopAnime\UsersBundle\Event;

use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class UserConnectEvent extends Event {

    protected $userManagerInterface;
    protected $userInterface;
    protected $responseInterface;

    public function __construct(UserManagerInterface $userManagerInterface, UserInterface $user, UserResponseInterface $response)
    {
        $this->userManagerInterface = $userManagerInterface;
        $this->userInterface = $user;
        $this->responseInterface = $response;
    }

    public function getUserManagerInterface()
    {
        return $this->userManagerInterface;
    }

    public function getUserInterface()
    {
        return $this->userInterface;
    }

    public function getResponseInterface()
    {
        return $this->responseInterface;
    }

}
