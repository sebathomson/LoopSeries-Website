<?php

namespace LoopAnime\AdminBundle\Command;

use LoopAnime\AppBundle\Command\Anime\CreateLink;
use LoopAnime\CrawlersBundle\Enum\HostersEnum;
use LoopAnime\CrawlersBundle\Services\crawlers\CrawlerService;
use LoopAnime\CrawlersBundle\Services\hosters\Hosters;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatedAiredEpisodesCommand extends ContainerAwareCommand {

    /** @var OutputInterface */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('loopanime:admin:import:aired-episodes')
            ->setDescription('Updates Episodes that went for air today or near and didnt got updated yet.')
            ->addOption('date',null,InputOption::VALUE_REQUIRED,'Add a Specific date to look for [format: Y-m-d]')
            ->addOption('hoster',null,InputOption::VALUE_REQUIRED,'Hoster where to look for the new episodes')
            ->addOption('allEpisodes',null,InputOption::VALUE_NONE,'Grabs for all episodes including the ones with links already!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $date = date('Y-m-d');
        $hosters = HostersEnum::getAsArray();
        $all = $input->hasOption('allEpisodes');

        if($input->hasOption('date') && !empty($input->getOption('date'))) {
            $date = \DateTime::createFromFormat('Y-m-d', $input->getOption('date'));
        }
        if($input->hasOption('hoster') && !empty($input->getOption('hoster'))) {
            $hosters = [$input->getOption('hoster')];
        }

        foreach($hosters as $hoster) {
            $this->output->writeln('Updating the anime from the hoster ' . $hoster);
            $doctrine = $this->getContainer()->get('doctrine');
            /** @var AnimesEpisodesRepository $aEpisodesRepo */
            $aEpisodesRepo = $doctrine->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');

            $this->output->writeln(sprintf('<comment>Getting the episodes from the %s to the hoster %s</comment>', $date->format('Y-m-d'), $hoster));
            /** @var AnimesEpisodes[] $episodes */
            $episodes = $aEpisodesRepo->getEpisodesByAirDate($date, $hoster, null, $all);

            if(!$episodes) {
                $this->output->write('<info>There are no episodes for the date: '.$date->format('Y-m-d').', and hoster:' . $hoster . '</info>');
                return 0;
            }

            /** @var Hosters $hoster */
            $hoster = $this->getContainer()->get('loopanime.hosters.'.$hoster);
            $this->updateEpisodes($episodes, $hoster);
        }
    }

    /**
     * @param AnimesEpisodes[] $episodes
     * @param Hosters $hoster
     */
    private function updateEpisodes($episodes, Hosters $hoster)
    {
        /** @var CrawlerService $crawler */
        $crawler = $this->getContainer()->get('loopanime.crawler');
        $crawler->setConsoleOutput($this->output);

        $doctrine = $this->getContainer()->get('doctrine');
        /** @var AnimesRepository $animeRepo */
        $animeRepo = $doctrine->getRepository('LoopAnime\ShowsBundle\Entity\Animes');
        foreach ($episodes as $episode) {
            /** @var AnimesSeasons $season */
            $season = $episode->getSeason();
            /** @var Animes $animeObj */
            $animeObj = $animeRepo->find($season->getAnime());
            $this->output->writeln('crawling the episode ' . $episode->getSeason() .':'.$episode->getEpisode()." Absolute: " . $episode->getAbsoluteNumber() . ' title: ' . $episode->getEpisodeTitle());
            $bestMatchs = $crawler->crawlEpisode($animeObj, $hoster, $episode);

            if ((round($bestMatchs['percentage']) == 100) && !empty($bestMatchs['mirrors']) && count($bestMatchs['mirrors']) > 0) {
                $command = new CreateLink($episode, $hoster, $bestMatchs['mirrors'], $this->output);
                $this->getContainer()->get('command_bus')->handle($command);
                $this->output->writeln("<info>Episode was found with 100 accuracy! Gathered a total of ".count($bestMatchs['mirrors'])." Mirrors</info>");
            } else {
                $this->output->writeln("<comment>Episode was not found - The best accuracy was ".$bestMatchs['percentage']."</comment>");
                var_dump($bestMatchs);
            }
        }
    }

}
