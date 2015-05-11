<?php
namespace LoopAnime\AppBundle\Queue\Exception;


class WorkerDataMalformedException extends \Exception
{

    public function __construct(array $keys)
    {
        parent::__construct(sprintf('Data on the job is missing or incomplete. Expecting the follow keys: %s', implode(", ", $keys)));
    }

}
