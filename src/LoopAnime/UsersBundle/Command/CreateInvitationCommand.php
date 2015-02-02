<?php

namespace LoopAnime\UsersBundle\Command;

use Doctrine\ORM\EntityManager;
use LoopAnime\UsersBundle\Entity\Invitation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateInvitationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('loopanimeusersbundle:invitations:user:create')
            ->setDescription('Creates a new invitation code')
            ->addArgument('email',InputArgument::REQUIRED,'Email to send the code')
            ->setHelp(
                <<<EOT
                    The <info>%command.name%</info>command creates a new invitation code.

<info>php %command.full_name%</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $invitationRepo = $entityManager->getRepository('LoopAnimeUsersBundle:Invitation');
        $invitation = new Invitation();
        $invitation->setEmail($input->getArgument('email'));
        $entityManager->persist($invitation);
        $entityManager->flush();
    }
}
