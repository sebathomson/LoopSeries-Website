<?php

namespace LoopAnime\AppBundle\Queue\Worker\Serie;

use LoopAnime\AppBundle\Command\Anime\CreateLink;
use LoopAnime\AppBundle\Queue\Enum\QueueType;
use LoopAnime\AppBundle\Queue\Exception\SubjectNotFoundException;
use LoopAnime\AppBundle\Queue\Exception\WorkerDataMalformedException;
use LoopAnime\AppBundle\Queue\Worker\BaseWorker;
use LoopAnime\AppBundle\Queue\Worker\WorkerInterface;
use LoopAnime\CrawlersBundle\Enum\HostersEnum;
use LoopAnime\CrawlersBundle\Services\crawlers\CrawlerService;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;

class PopulateEpisodeWorker extends BaseWorker implements WorkerInterface
{

    public function runWorker()
    {
        $data = $this->getData();
        $idEpisode = $data['idEpisode'];

        /** @var AnimesEpisodes $episode */
        $episode = $this->em->getRepository('LoopAnimeShowsBundle:AnimesEpisodes')->find($idEpisode);
        if (!$episode) {
            throw new SubjectNotFoundException('Episode with the id' . $idEpisode . ' was not found!');
        }

        $season = $episode->getSeason();
        $anime = $season->getAnime();

        /** @var CrawlerService $crawler */
        $crawler = $this->getContainer()->get('loopanime.crawler');
        $crawler->setConsoleOutput($this->output);

        foreach (HostersEnum::getAsArray() as $hoster) {
            $this->log('['.$anime->getId().'] Crawling the episode ' . $season->getSeason() . "X" . $episode->getEpisode() . ' title: ' . $episode->getEpisodeTitle());
            $hoster = 'LoopAnime\\CrawlersBundle\\Services\\hosters\\' . ucfirst($hoster);
            $hoster = new $hoster();
            $bestMatchs = $crawler->crawlEpisode($anime, $hoster, $episode);

            if (($bestMatchs['percentage'] == "100") && !empty($bestMatchs['mirrors']) && count($bestMatchs['mirrors']) > 0) {
                $command = new CreateLink($episode, $hoster, $bestMatchs['mirrors'], $this->output);
                $this->getContainer()->get('command_bus')->handle($command);
                $this->log("Episode was found with 100 accuracy! Gathered a total of ".count($bestMatchs['mirrors'])." Mirrors", 'info');
            } else {
                $this->log("Episode was not found - The best accuracy was ".$bestMatchs['percentage'], 'comment');
                var_dump($bestMatchs);
            }
        }
        return true;
    }

    public function validate()
    {
        $data = $this->getData();
        $keys = [];
        if (empty($data['idEpisode'])) {
            $keys[] = 'idEpisode';
        }
        if (!empty($keys)) {
            throw new WorkerDataMalformedException($keys);
        }
        return true;
    }

    public function getQueueType()
    {
        return QueueType::POPULATE_EPISODE;
    }

}
