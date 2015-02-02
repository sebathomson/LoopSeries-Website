<?php

namespace LoopAnime\AppBundle\Sync\Exception;

use LoopAnime\AppBundle\Sync\Handler\AbstractHandler;

class HandlerWithDuplicatedName extends \Exception
{

    public function __construct(AbstractHandler $handler)
    {
        parent::__construct(sprintf('Handler %s seems to have its name %s duplicated!', get_class($handler), $handler->getName()));
    }

}
