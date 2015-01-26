<?php

namespace LoopAnime\ShowsAPIBundle\Services\SyncAPI;

use LoopAnime\AppBundle\Sync\Implementation\BaseImplementation;
use LoopAnime\AppBundle\Sync\Implementation\Exception\ApiFaultException;
use LoopAnime\ShowsAPIBundle\Entity\AnimesAPI;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use LoopAnime\ShowsBundle\Entity\ViewsRepository;
use LoopAnime\UsersBundle\Entity\UsersFavoritesRepository;

class TraktTvImplementation extends BaseImplementation {

    const SYNC_URL = "http://api.trakt.tv/";

    protected function callCurl($url, array $POST = null)
    {
        $ch = curl_init(self::SYNC_URL. $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user->getTraktUsername().":".$this->user->getTraktPassword());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(!empty($POST)) {
            $POST = json_encode($POST);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
        }
        $result = curl_exec($ch);
        $result = json_decode($result,true);
        if(!empty($result['status']) && $result['status'] === "failure")
            throw new ApiFaultException("Trakt",$result['error']);
        return $result;
    }

    public function markAsSeenEpisode(AnimesEpisodes $episode)
    {
        /** @var AnimesSeasons $season */
        $season = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons')->find($episode->getIdSeason());
        /** @var AnimesAPI $animeAPI */
        $animeAPI = $this->em->getRepository('LoopAnime\ShowsAPIBundle\Entity\AnimesAPI')->findOneBy(['idAnime' => $season->getIdAnime()]);
        /** @var Animes $anime */
        $anime = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\Animes')->findOneBy(['id' => $season->getIdAnime()]);

        $today = new \DateTime("now");
        $POST = [
            "username" => $this->user->getTraktUsername(),
            "password" => $this->user->getTraktPassword(),
            "tvdb_id"  => $animeAPI->getApiAnimeKey(),
            "title"    => $anime->getTitle(),
            "year"     => date("Y",strtotime($anime->getStartTime())),
            "episodes" => [[
                "season" => $season->getSeason(),
                "episode" => $episode->getEpisode(),
                "last_played" => $today->format('c'),
            ]]
        ];
        $this->callCurl($this->getMarkEpisodeSeenApiUrl(),$POST);
        return true;
    }

    public function importSeenEpisodes()
    {
        $animeAPIRepo = $this->em->getRepository('LoopAnime\ShowsAPIBundle\Entity\AnimesAPI');
        $seasonsRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');
        /** @var UsersFavoritesRepository $usersFavRepo */
        $usersFavRepo = $this->em->getRepository('LoopAnime\UsersBundle\Entity\UsersFavorites');
        /** @var AnimesEpisodesRepository $episodeRepo */
        $episodeRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');
        /** @var ViewsRepository $viewsRepo */
        $viewsRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\Views');
        $return = $this->callCurl($this->getImportApiUrl());
        foreach($return as $anime) {
            if(empty($anime['tvdb_id']))
                continue;
            /** @var AnimesAPI $animeObj */
            $animeObj = $animeAPIRepo->findOneBy(['apiAnimeKey' => $anime["tvdb_id"]]);
            if($animeObj !== null) {
                foreach($anime['seasons'] as $season) {
                    foreach($season['episodes'] as $episode) {
                        $seasonObj = $seasonsRepo->findOneBy(['idAnime' => $animeObj->getIdAnime(), 'season' => $season['season']]);
                        $episode = $episodeRepo->getEpisodesBySeason($seasonObj->getId(),true,$episode);
                        if(empty($episode))
                            continue;
                        $episode = $episode[0][0];
                        if(!$viewsRepo->isEpisodeSeen($this->user,$episode->getId()))
                            $viewsRepo->setEpisodeAsSeen($this->user, $episode->getId(), 0);
                    }
                }
                // Adds the anime to the favorite if its not
                if(!$usersFavRepo->isAnimeFavorite($this->user,$animeObj->getIdAnime()))
                    $usersFavRepo->setAnimeAsFavorite($this->user, $animeObj->getIdAnime());
            }
        }
        return true;
    }

    protected function getUserApiUrl()
    {
        return "user/profile.json/".$this->apiKey."/".$this->user->getTraktUsername();
    }

    protected function getImportApiUrl()
    {
        return "user/library/shows/watched.json/".$this->apiKey."/".$this->user->getTraktUsername();
    }

    protected function getMarkEpisodeSeenApiUrl()
    {
        return "show/episode/seen/" . $this->apiKey;
    }
}