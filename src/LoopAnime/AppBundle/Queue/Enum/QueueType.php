<?php

namespace LoopAnime\AppBundle\Queue\Enum;

use LoopAnime\AppBundle\Enum\BaseEnum;

class QueueType extends BaseEnum
{

    const SYNC_USER = 'sync.user';
    const ADD_SERIE = 'add.serie';
    const POPULATE_EPISODE = 'populate.episode';


}
