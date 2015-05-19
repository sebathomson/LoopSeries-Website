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
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class PopulateLinksCommand extends ContainerAwareCommand {

    /** @var OutputInterface */
    private $output;
    /** @var EntityManager */
    private $doctrine;
    private $importLogHandler;

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
        $this->initLogger();
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

        /** @var CrawlerService $crawlerService */
        $crawlerService = $this->getContainer()->get('loopanime.crawler');
        $crawlerService->setConsoleOutput($output);

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
                try {
                    $bestMatch = $crawlerService->crawlEpisode($anime, $hoster, $episode);
                    if (($bestMatch['percentage'] == "100") && !empty($bestMatch['mirrors']) && count($bestMatch['mirrors']) > 0) {
                        $command = new CreateLink($episode, $hoster, $bestMatch['mirrors'], $this->output);
                        $this->getContainer()->get('command_bus')->handle($command);
                        $output->writeln("<info>Episode was found with 100 accuracy! Gathered a total of ".count($bestMatch['mirrors'])." Mirrors</info>");
                    } else {
                        $this->logCrawling($episode, $crawlerService, $bestMatch);
                        $output->writeln("<comment>Episode was not found - The best accuracy was ".$bestMatch['percentage']."</comment>");
                        var_dump($bestMatch);
                    }
                } catch(\Exception $e) {
                    $output->writeln("<comment>Crawler throwed an expcetion: ".$e->getMessage()."</comment>");
                    $this->logCrawling($episode, $crawlerService, ['uri' => '', 'log' => $e->getMessage(), 'percentage' => 0]);
                }
            }
        }

        fclose($this->importLogHandler);
    }

    private function initLogger()
    {
        $this->importLogHandler = fopen('/var/log/import.log','w+');
        fputcsv($this->importLogHandler, [
            'serie_id',
            'episode_id',
            'episode_title',
            'epsiode_season',
            'episode_number',
            'episode_absolute_number',
            'possible_titles',
            'possible_episode_titles',
            'rule_crawler_season',
            'rule_crawler_title',
            'rule_crawler_episode',
            'rule_crawler_reset',
            'rule_crawler_reset',
            'best_uri',
            'best_log',
            'best_percentage'
        ]);
    }

    private function logCrawling(AnimesEpisodes $episode, CrawlerService $crawler, $bestMatch)
    {
        fputcsv($this->importLogHandler, [
            $episode->getSeason()->getAnime()->getId(),
            $episode->getId(),
            $episode->getEpisodeTitle(),
            $episode->getSeason()->getSeason(),
            $episode->getEpisode(),
            $episode->getAbsoluteNumber(),
            implode(", ", $crawler->getPossibleTitlesMatchs()),
            implode(", ", $crawler->getPossibleEpisodesTitlesMatchs()),
            $crawler->getSeasonSettingsUsed() ? $crawler->getSeasonSettingsUsed()->getSeason() : 'n.a',
            $crawler->getSeasonSettingsUsed() ? $crawler->getSeasonSettingsUsed()->getAnimeTitle() : 'n.a',
            $crawler->getSeasonSettingsUsed() ? $crawler->getSeasonSettingsUsed()->getEpisodeTitle() : 'n.a',
            $crawler->getSeasonSettingsUsed() ? $crawler->getSeasonSettingsUsed()->getReset() ? 'yes' : 'no' : 'n.a',
            $crawler->getSeasonSettingsUsed() ? $crawler->getSeasonSettingsUsed()->getHandicap() : 'n.a',
            $bestMatch['uri'],
            $bestMatch['log'],
            $bestMatch['percentage']
        ]);
    }

}
