<?php

namespace LoopAnime\AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class QueueServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('queue.service')) {
            return;
        }

        $definition = $container->getDefinition('queue.service');
        $taggedServices = $container->findTaggedServiceIds('queue.worker');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addWorker', array(new Reference($id)));
        }
    }
}