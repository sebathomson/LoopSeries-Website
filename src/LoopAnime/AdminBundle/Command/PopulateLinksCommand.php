<?php
/**
 * Created by PhpStorm.
 * User: luislopes
 * Date: 20/09/2014
 * Time: 20:54
 */

namespace LoopAnime\AdminBundle\Command;


use LoopAnime\CrawlersBundle\Services\crawlers\CrawlerService;
use LoopAnime\CrawlersBundle\Services\hosters\Anime44;
use LoopAnime\CrawlersBundle\Services\hosters\Anitube;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesLinks;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateLinksCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('admin:import:populate-links')
            ->setDescription('Populates links collection for the animes\' episodes')
            ->addArgument('hoster',null,InputArgument::REQUIRED,'Hoster to look on. [anime44, anitube] ',null)
            ->addArgument('anime',null,InputArgument::REQUIRED,'Anime id to look for', null)
            ->addOption('all',null,InputOption::VALUE_NONE,'Look for all episodes, even the ones already populated.',false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hoster = strtolower($input->getArgument('hoster'));
        $anime  = $input->getArgument('anime');
        $all    = $input->getOption('all');

        $doctrine = $this->getContainer()->get('doctrine');
        /** @var Animes $animeObj */
        $animeObj = $doctrine->getRepository('LoopAnime\ShowsBundle\Entity\Animes')->find($anime);
        /** @var AnimesEpisodesRepository $aEpisodesRepo */
        $aEpisodesRepo = $doctrine->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');
        /** @var AnimesEpisodes[] $episodes */
        $episodes = $aEpisodesRepo->getEpisodes2Update($anime, $hoster, $all);

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

        $crawler = new CrawlerService($animeObj, $hoster, $doctrine);

        foreach ($episodes as $episode) {
            $bestMatchs = $crawler->crawlEpisode($episode);
            var_dump($bestMatchs);
            if (($bestMatchs['percentage'] == "100") and count($bestMatchs['mirrors']) > 0) {
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
            }
            ob_flush();
        }
    }

} 