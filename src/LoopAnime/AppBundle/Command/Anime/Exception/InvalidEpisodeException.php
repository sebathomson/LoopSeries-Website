<?php
namespace LoopAnime\AppBundle\Command\Anime\Exception;


class InvalidEpisodeException extends \Exception
{

    public function __construct($message)
    {
        parent::__construct(sprintf('The anime is invalid. %s', $message));
    }

}
