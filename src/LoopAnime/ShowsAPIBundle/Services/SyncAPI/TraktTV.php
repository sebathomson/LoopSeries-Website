<?php

namespace LoopAnime\ShowsAPIBundle\Services\SyncAPI;

use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\ShowsAPIBundle\Entity\AnimesAPI;
use LoopAnime\ShowsAPIBundle\Entity\AnimesAPIRepository;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;
use LoopAnime\ShowsBundle\Entity\AnimesSeasons;
use LoopAnime\ShowsBundle\Entity\ViewsRepository;
use LoopAnime\UsersBundle\Entity\Users;
use LoopAnime\UsersBundle\Entity\UsersFavoritesRepository;
use Symfony\Component\Security\Core\SecurityContext;
use Wubs\Trakt\Trakt;

class TraktTV
{
    /** @var Users user */
    private $user;

    /**
     * @param $traktKey
     * @param SecurityContext $context
     * @param ObjectManager $em
     */
    public function __construct($traktKey, SecurityContext $context, ObjectManager $em) {
        $this->apiKey = $traktKey;
        /** @var Users user */
        $this->user = $context->getToken()->getUser();
        $this->apiUrl = "http://api.trakt.tv/";
        $this->em = $em;
    }

    private function callCurl($url, array $POST = null)
    {
        $ch = curl_init($this->apiUrl . $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user->getTraktUsername().":".$this->user->getTraktPassword());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(!empty($POST)) {
            $POST = json_encode($POST);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
            /*curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($POST))
            );*/
        }
        $result = curl_exec($ch);
        $result = json_decode($result,true);
        if(!empty($result['status']) && $result['status'] === "failure")
            throw new \Exception("Trakt TV Error: " . $result['error']);
        return $result;
    }

    public function markAsSeenEpisode(AnimesEpisodes $episode)
    {
        $animeAPIRepo = $this->em->getRepository('LoopAnime\ShowsAPIBundle\Entity\AnimesAPI');
        $animeRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\Animes');
        $seasonsRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');
        /** @var AnimesSeasons $season */
        $season = $seasonsRepo->find($episode->getIdSeason());
        /** @var AnimesAPI $animeAPI */
        $animeAPI = $animeAPIRepo->findOneBy(['idAnime' => $season->getIdAnime()]);
        /** @var Animes $anime */
        $anime = $animeRepo->findOneBy(['id' => $season->getIdAnime()]);
        $url = "show/episode/seen/" . $this->apiKey;
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
        $return = $this->callCurl($url,$POST);
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
        $return = $this->callCurl("user/library/shows/watched.json/".$this->apiKey."/".$this->user->getTraktUsername());
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

    public function checkIfUserExists(Users $user)
    {
        $this->user = $user;
        $url = "user/profile.json/".$this->apiKey."/".$user->getTraktUsername();
        $return = $this->callCurl($url);
        return true;
    }

}