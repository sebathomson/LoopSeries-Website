<?php

namespace LoopAnime\UsersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Invitation
{
    /** @ORM\Id @ORM\Column(type="string", length=6) */
    protected $code;

    /** @ORM\Column(type="string", length=256) */
    protected $email;

    /**
     * When sending invitation be sure to set this value to `true`
     *
     * It can prevent invitations from being sent twice
     *
     * @ORM\Column(type="boolean")
     */
    protected $sent = false;

    public function __construct()
    {
        $this->generateCode();
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isSent()
    {
        return $this->sent;
    }

    public function send()
    {
        $this->sent = true;
    }

    public function generateCode()
    {
        // generate identifier only once, here a 8 characters length code
        $this->code = substr(md5(uniqid(rand(), true)), 0, 8);
    }

    public function resetInvitation()
    {
        $this->generateCode();
        $this->sent = false;
    }
}