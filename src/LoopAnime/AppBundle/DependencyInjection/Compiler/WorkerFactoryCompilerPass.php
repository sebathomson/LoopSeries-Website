<?php

namespace LoopAnime\AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WorkerFactoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('queue.worker.factory')) {
            return;
        }

        $definition = $container->getDefinition('queue.worker.factory');
        $taggedServices = $container->findTaggedServiceIds('queue.worker');
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addWorker', array(new Reference($id)));
        }
    }
}