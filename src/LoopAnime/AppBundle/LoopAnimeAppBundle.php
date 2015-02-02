<?php

namespace LoopAnime\AppBundle;

use LoopAnime\AppBundle\DependencyInjection\Compiler\SyncHandlersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LoopAnimeAppBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SyncHandlersCompilerPass());
    }
}
