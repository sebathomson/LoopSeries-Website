<?php

namespace LoopAnime\AdminBundle\Command;

use Doctrine\ORM\EntityManager;
use LoopAnime\AppBundle\Command\Anime\CreateLink;
use LoopAnime\CrawlersBundle\Services\crawlers\CrawlerService;
use LoopAnime\CrawlersBundle\Services\hosters\Anime44;
use LoopAnime\CrawlersBundle\Services\hosters\Anitube;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateLinksCommand extends ContainerAwareCommand {

    /** @var OutputInterface */
    private $output;
    /** @var EntityManager */
    private $doctrine;

    protected function configure()
    {
        $this
            ->setName('loopanime:admin:import:populate-links')
            ->setDescription('Populates links collection for the animes\' episodes')
            ->addArgument('hoster',null,InputArgument::REQUIRED,'Hoster to look on. [anime44, anitube] ')
            ->addOption('anime',null,InputOption::VALUE_REQUIRED,'Anime id to look for', null)
            ->addOption('continuing',null,InputOption::VALUE_NONE,'Only Anime that is today continuing')
            ->addOption('todayAired',null,InputOption::VALUE_NONE,'Only for today aired Episodes')
            ->addOption('all',null,InputOption::VALUE_NONE,'Look for all episodes, even the ones already populated.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hoster = strtolower($input->getArgument('hoster'));
        $anime  = $input->getOption('anime');
        $all    = $input->getOption('all');
        $this->output = $output;

        $criteria = [];
        if($anime) {
            $criteria = ['id' => $anime];
            $this->output->writeln(sprintf('<question>Grabing the links for the Show with the ID: %s</question>',$anime));
        } else {
            $this->output->writeln('<question>Updating links for all Animes!</question>');
        }
        if($all) {
            $this->output->writeln('<question>Populate links for all episodes</question>');
        }

        // Instanciate the Hoster
        switch ($hoster) {
            case "anitube":
                $hoster = new Anitube();
                break;
            case "anime44":
                $hoster = new Anime44();
                break;
            default:
                throw new \Exception("I dont have the hoster $hoster");
                break;
        }

        /** @var CrawlerService $crawler */
        $crawler = $this->getContainer()->get('loopanime.crawler');
        $crawler->setConsoleOutput($output);

        $this->doctrine = $this->getContainer()->get('doctrine');
        /** @var AnimesRepository $animesRepo */
        $animesRepo = $this->doctrine->getRepository("LoopAnimeShowsBundle:Animes");
        /** @var Animes[] $animeObj */
        $animeObj = $animesRepo->findBy($criteria);

        foreach($animeObj as $anime) {
            /** @var AnimesEpisodesRepository $aEpisodesRepo */
            $aEpisodesRepo = $this->doctrine->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');
            /** @var AnimesEpisodes[] $episodes */
            $episodes = $aEpisodesRepo->getEpisodes2Update($anime->getId(), $hoster, $all);
            foreach ($episodes as $episode) {
                $this->output->writeln('['.$anime->getId().'] Crawling the episode ' . $episode->getSeason()->getSeason() . "X" . $episode->getEpisode() . ' title: ' . $episode->getEpisodeTitle());
                $bestMatchs = $crawler->crawlEpisode($anime, $hoster, $episode);

                if (($bestMatchs['percentage'] == "100") && !empty($bestMatchs['mirrors']) && count($bestMatchs['mirrors']) > 0) {
                    $command = new CreateLink($episode, $hoster, $bestMatchs['mirrors'], $this->output);
                    $this->getContainer()->get('command_bus')->handle($command);
                    $output->writeln("<info>Episode was found with 100 accuracy! Gathered a total of ".count($bestMatchs['mirrors'])." Mirrors</info>");
                } else {
                    $output->writeln("<comment>Episode was not found - The best accuracy was ".$bestMatchs['percentage']."</comment>");
                    var_dump($bestMatchs);
                }
            }
        }
    }

}
