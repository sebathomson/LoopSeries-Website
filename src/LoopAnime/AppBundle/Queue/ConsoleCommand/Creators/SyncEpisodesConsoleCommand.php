<?php

namespace LoopAnime\AppBundle\Queue\ConsoleCommand\Creators;

use Doctrine\ORM\EntityManager;
use LoopAnime\AppBundle\Queue\Entity\QueueRepository;
use LoopAnime\AppBundle\Queue\Enum\QueueType;
use LoopAnime\AppBundle\Queue\Services\QueueService;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncEpisodesConsoleCommand extends ContainerAwareCommand
{

    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var \DateTime */
    private $startTime;
    /** @var EntityManager */
    private $em;
    /** @var QueueRepository */
    private $queueRepo;

    public function configure()
    {
        $this
            ->setName('la:queue:create:sync')
            ->setDescription('Create a queue entry for sync users episodes account')
        ;
    }

    public function execute(InputInterface $inputInterface, OutputInterface $outputInterface)
    {
        $this->input = $inputInterface;
        $this->output = $outputInterface;
        $this->em = $this->getContainer()->get('doctrine');

        /** @var UsersRepository $usersRepo */
        $usersRepo = $this->em->getRepository('LoopAnimeUsersBundle:Users');

        /** @var QueueService $queueService */
        $queueService = $this->getContainer()->get('queue.service');

        $this->output->writeln('Grabing users with Sync options enabled..');
        /** @var Users[] $users */
        $users = $usersRepo->getUsersForSync();
        $this->output->writeln('Found ' . count($users) . ' users.. Creating the queues');
        foreach ($users as $user) {
            $this->output->writeln('Adding the user ' . $user->getId() . ' to the queue');
            $data = ['userId' => $user->getId()];
            $queueService->createJob(QueueType::SYNC_USER, $data);
        }
    }

}
