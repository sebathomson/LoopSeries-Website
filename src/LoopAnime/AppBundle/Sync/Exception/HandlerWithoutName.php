<?php

namespace LoopAnime\AppBundle\Sync\Exception;

use LoopAnime\AppBundle\Sync\Handler\AbstractHandler;

class HandlerWithoutName extends \Exception
{

    public function __construct(AbstractHandler $handler)
    {
        parent::__construct(sprintf('Handler %s doesnt have a Name!', get_class($handler)));
    }

}
