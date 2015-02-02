<?php

namespace LoopAnime\AppBundle\Command\Handler;

use Doctrine\ORM\EntityManager;
use LoopAnime\AppBundle\Command\CreateAnime;
use LoopAnime\AppBundle\Parser\ParserAnime;
use LoopAnime\AppBundle\Parser\ParserEpisode;
use LoopAnime\AppBundle\Parser\ParserSeason;
use LoopAnime\ShowsAPIBundle\Entity\AnimesAPI;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAnimeCommandHandler implements MessageHandler {

    private $em;
    /** @var OutputInterface */
    private $output;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Handles the given message.
     *
     * @param Message|CreateAnime $message
     * @return void
     */
    public function handle(Message $message)
    {
        $this->output = $message->output;
        /** @var ParserAnime $parserAnime */
        $parserAnime = $message->parserAnime;
        $animeObj = $this->insertAnime($parserAnime);
        foreach($parserAnime->getSeasons() as $season) {
            $seasonObj = $this->insertSeason($season, $animeObj);
            foreach($season->getEpisodes() as $episode) {
                $this->insertEpisode($episode, $seasonObj);
            }
        }
    }

    private function insertAnime(ParserAnime $parserAnime)
    {
        $anime = new Animes();

        $anime->setTitle($parserAnime->getTitle());
        $anime->setThemes($parserAnime->getThemes());
        $anime->setTypeSeries('anime');
        $anime->setEndTime($parserAnime->getEndTime());
        $anime->setStartTime($parserAnime->getStartTime());
        $anime->setStatus($parserAnime->getStatus());
        $anime->setGenres($parserAnime->getGenres());
        $anime->setPlotSummary($parserAnime->getSummary());
        $anime->setRunningTime($parserAnime->getRunningTime());
        $anime->setPoster($parserAnime->getPoster());
        $anime->setRating($parserAnime->getRating());
        $anime->setRatingCount($parserAnime->getRatingCount());
        $anime->setImdbId($parserAnime->getImdbId());
        $anime->setCreateTime(new \DateTime('now'));

        $this->em->persist($anime);
        $this->em->flush();

        $this->output->writeln('Anime inserted ' . $anime->getId() . ' title: ' . $anime->getTitle());

        $animeApi = new AnimesAPI();
        $animeApi->setApiAnimeKey($parserAnime->getAnimeKey());
        $animeApi->setIdAnime($anime->getId());
        $animeApi->setIdApi($parserAnime->getApiId());

        $this->em->persist($animeApi);
        $this->em->flush();

        return $anime;
    }

    private function insertSeason(ParserSeason $parserSeason, Animes $anime)
    {
        $season = new AnimesSeasons();

        $season->setCreateTime(new \DateTime('now'));
        $season->setAnime($anime);
        $season->setLastUpdate(new \DateTime('now'));
        $season->setNumberEpisodes($parserSeason->getTotalEpisodes());
        $season->setSeasonTitle($parserSeason->getTitle());
        $season->setSeason($parserSeason->getNumber());
        $season->setPoster($parserSeason->getPoster());

        $this->em->persist($season);
        $this->em->flush();
        $this->output->writeln('Season inserted ' . $season->getId() . ' season: ' . $season->getSeason());
        return $season;
    }

    private function insertEpisode(ParserEpisode $parserEpisode, AnimesSeasons $season)
    {
        $episode = new AnimesEpisodes();

        $episode->setPoster($parserEpisode->getPoster());
        $episode->setAbsoluteNumber($parserEpisode->getAbsoluteNumber());
        $episode->setAirDate(new \DateTime($parserEpisode->getAirDate()));
        $episode->setComments($parserEpisode->getComments());
        $episode->setSummary($parserEpisode->getSummary());
        $episode->setViews($parserEpisode->getViews());
        $episode->setRating(0);
        $episode->setRatingUp(0);
        $episode->setRatingDown(0);
        $episode->setRatingCount(0);
        $episode->setImdbId($parserEpisode->getImdbId());
        $episode->setEpisode($parserEpisode->getEpisodeNumber());
        $episode->setEpisodeTitle($parserEpisode->getEpisodeTitle());
        $episode->setSeason($season);
        $episode->setLastUpdate(new \DateTime('now'));
        $episode->setCreateTime(new \DateTime('now'));

        $this->em->persist($episode);
        $this->em->flush();
        $this->output->writeln('Inserted Episode ' . $episode->getId() . ' title: ' . $episode->getEpisodeTitle() . ' number: ' . $episode->getEpisode());
        return $episode;
    }
}
