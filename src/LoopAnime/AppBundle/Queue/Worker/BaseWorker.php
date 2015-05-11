<?php
namespace LoopAnime\AppBundle\Queue\Worker;

use Doctrine\ORM\EntityManager;
use LoopAnime\AppBundle\Queue\Entity\Queue;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseWorker implements WorkerInterface, ContainerAwareInterface
{

    private $container;
    /** @var OutputInterface */
    protected $output;
    /** @var EntityManager */
    protected $em;
    /** @var Queue */
    protected $job;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setJob(Queue $job)
    {
        $this->job = $job;
    }

    public function setOutputInterface(OutputInterface $outputInterface)
    {
        $this->output = $outputInterface;
    }

    public function getData()
    {
        return $this->job->getData();
    }

    public function log($text, $type = null)
    {
        if (!$this->output || empty($text)) {
            return;
        }
        if ($type) {
            $text = sprintf('<%s>%s</%s>', $type, $text, $type);
        }
        $this->output->writeln($text);
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

}
