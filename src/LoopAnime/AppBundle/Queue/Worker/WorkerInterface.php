<?php
namespace LoopAnime\AppBundle\Queue\Worker;


use Doctrine\ORM\EntityManager;
use LoopAnime\AppBundle\Queue\Entity\Queue;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

interface WorkerInterface
{

    public function setOutputInterface(OutputInterface $output);

    /**
     * @return boolean
     */
    public function runWorker();
    public function setEntityManager(EntityManager $em);
    public function setJob(Queue $job);
    public function validate();
    public function getQueueType();
    public function setContainer(ContainerInterface $containerInterface);

}
