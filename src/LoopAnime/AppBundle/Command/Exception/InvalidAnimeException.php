<?php
namespace LoopAnime\AppBundle\Command\Exception;


class InvalidAnimeException extends \Exception
{

    public function __construct($message)
    {
        parent::__construct(sprintf('The anime is invalid. %s', $message));
    }

}
