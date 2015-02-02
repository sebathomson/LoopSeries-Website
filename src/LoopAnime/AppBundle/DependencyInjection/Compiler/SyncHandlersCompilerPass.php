<?php

namespace LoopAnime\AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SyncHandlersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('syncservice')) {
            return;
        }

        $definition = $container->getDefinition(
            'sync.service'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'sync.handler'
        );
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addHandler',
                array(new Reference($id))
            );
        }
    }
}
