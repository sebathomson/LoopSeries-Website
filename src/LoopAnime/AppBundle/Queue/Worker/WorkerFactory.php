<?php

namespace LoopAnime\AppBundle\Queue\Worker;

use Doctrine\ORM\EntityManager;
use LoopAnime\AppBundle\Queue\Entity\Queue;
use LoopAnime\AppBundle\Queue\Exception\WorkerNotDefinedException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WorkerFactory
{

    /** @var WorkerInterface[] */
    private $workers;
    private $em;
    private $output;
    private $container;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function addWorker(WorkerInterface $workerInterface)
    {
        $this->workers[$workerInterface->getQueueType()] = $workerInterface;
    }

    public function getWorker($queueType)
    {
        if (!isset($this->workers[$queueType])) {
            throw new WorkerNotDefinedException('Worker ' . $queueType . ' was not injected or do not exist!');
        }
        return $this->workers[$queueType];
    }

    public function create(Queue $job)
    {
        $worker = $this->getWorker($job->getType());
        $worker->setEntityManager($this->em);
        $worker->setContainer($this->container);
        $worker->setJob($job);

        if ($this->output !== null) {
            $worker->setOutputInterface($this->output);
        }

        return $worker;
    }
}
