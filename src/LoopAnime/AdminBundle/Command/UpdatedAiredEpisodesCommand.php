<?php
/**
 * Created by PhpStorm.
 * User: luislopes
 * Date: 20/09/2014
 * Time: 20:54
 */

namespace LoopAnime\AdminBundle\Command;

use LoopAnime\CrawlersBundle\Enum\HostersEnum;
use LoopAnime\CrawlersBundle\Services\crawlers\CrawlerService;
use LoopAnime\CrawlersBundle\Services\hosters\Hosters;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatedAiredEpisodesCommand extends ContainerAwareCommand {

    /** @var OutputInterface */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('loopanimeadmin:import:aired-episodes')
            ->setDescription('Updates Episodes that went for air today or near and didnt got updated yet.')
            ->addOption('date',null,InputOption::VALUE_REQUIRED,'Add a Specific date to look for [format: Y-m-d]')
            ->addOption('hoster',null,InputOption::VALUE_REQUIRED,'Hoster where to look for the new episodes')
            ->addOption('allEpisodes',null,InputOption::VALUE_NONE,'Grabs for all episodes including the ones with links already!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $date = date('Y-m-d');
        $hosters = [HostersEnum::HOSTER_ANIME44,HostersEnum::HOSTER_ANITUBE];
        $all = $input->hasOption('allEpisodes');

        if($input->hasOption('date') && !empty($input->getOption('date'))) {
            $date = $input->getOption('date');
        }
        if($input->hasOption('hoster') && !empty($input->getOption('hoster'))) {
            $hosters = [$input->getOption('hoster')];
        }

        foreach($hosters as $hoster) {
            $this->output->writeln('Updating the anime from the hoster ' . $hoster);
            $doctrine = $this->getContainer()->get('doctrine');
            /** @var AnimesEpisodesRepository $aEpisodesRepo */
            $aEpisodesRepo = $doctrine->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');
            /** @var AnimesEpisodes[] $episodes */
            $episodes = $aEpisodesRepo->getEpisodesByAirDate($hoster, $date, $all);
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
        $doctrine = $this->getContainer()->get('doctrine');
        /** @var AnimesRepository $animeRepo */
        $animeRepo = $doctrine->getRepository('LoopAnime\ShowsBundle\Entity\Animes');
        foreach ($episodes as $episode) {
            /** @var AnimesSeasons $season */
            $season = $episode->getSeason();
            /** @var Animes $animeObj */
            $animeObj = $animeRepo->find($season->getIdAnime());
            $season->getIdAnime();
            $bestMatchs = $crawler->crawlEpisode($animeObj, $hoster, $episode);
            if (($bestMatchs['percentage'] == "100") && count($bestMatchs['mirrors']) > 0) {
                foreach ($bestMatchs['mirrors'] as $mirror) {
                    $url = parse_url($mirror);
                    $link = New AnimesLinks();
                    $link->setIdEpisode($episode->getId());
                    $link->setHoster($hoster->getName());
                    $link->setLink($mirror);
                    $link->setStatus(1);
                    $link->setIdUser(0);
                    $sublang = $hoster->getSubtitles();
                    $link->setLang("JAP");
                    $link->setSubtitles((!empty($sublang) ? 1 : 0));
                    $link->setSubLang($sublang);
                    $link->setFileType("mp4");
                    $link->setCreateTime(new \DateTime("now"));
                    $link->setUsed(0);
                    $link->setUsedTimes(0);
                    $link->setReport(0);
                    $link->setQualityType('SQ');
                    $link->setFileServer($url['host']);
                    $link->setFileSize("0");
                    $doctrine->persist($link);
                    $doctrine->flush();
                }
                $this->output->writeln("<success>Episode was found with 100 accuracy! Gathered a total of ".count($bestMatchs['mirrors'])." Mirrors</success>");
            } else {
                $this->output->writeln("<warning>Episode was not found - The best accuracy was ".$bestMatchs['percentage']." with a total of follow mirrors: ".count($bestMatchs['mirrors'])."</warning>");
            }
        }
    }

}