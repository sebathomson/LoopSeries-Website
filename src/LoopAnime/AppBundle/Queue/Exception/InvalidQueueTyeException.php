<?php
namespace LoopAnime\AppBundle\Queue\Exception;


class InvalidQueueTyeException extends \Exception
{

    public function __construct($type)
    {
        parent::__construct(sprintf('Type %s is invalid, if its a new type please create the worker and set the enum', $type));
    }

}
