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

class MAL
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
        $this->apiUrl = "http://myanimelist.net/";
        $this->em = $em;
    }

    private function callCurl($url, array $POST = null)
    {
        $ch = curl_init($this->apiUrl . $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user->getMALUsername().":".$this->user->getMALPassword());
        curl_setopt($ch, CURLOPT_USERAGENT, $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if(!empty($POST)) {
            $POST = json_encode($POST);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($POST))
            );
        }
        $result = curl_exec($ch);
        $resultStatus = curl_getinfo($ch);
        if(empty($resultStatus['http_code']) || $resultStatus['http_code'] != "200" || $result === "Invalid credentials")
            throw new \Exception("MAL Error: " . $result);
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
        $POST = [
            "username" => $this->user->getTraktUsername(),
            "password" => $this->user->getTraktPassword(),
            "tvdb_id"  => $animeAPI->getApiAnimeKey(),
            "title"    => $anime->getTitle(),
            "year"     => date("Y",strtotime($anime->getStartTime())),
            "episodes" => [
                "season" => $season->getSeason(),
                "episode" => $episode->getEpisode(),
                "last_played" => new \DateTime("now"),
            ]
        ];
        $return = $this->callCurl($url,$POST);
        return true;
    }

    public function importSeenEpisodes()
    {
        $animeRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\Animes');
        $seasonsRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons');
        /** @var UsersFavoritesRepository $usersFavRepo */
        $usersFavRepo = $this->em->getRepository('LoopAnime\UsersBundle\Entity\UsersFavorites');
        /** @var AnimesEpisodesRepository $episodeRepo */
        $episodeRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\AnimesEpisodes');
        /** @var ViewsRepository $viewsRepo */
        $viewsRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\Views');
        $return = $this->callCurl("malappinfo.php?u=".$this->user->getMALUsername()."&status=all&type=anime");
        $return = simplexml_load_string($return);
        foreach($return->anime as $anime) {
            /** @var Animes $animeObj */
            $animeObj = $animeRepo->findOneBy(['title' => $anime->series_title]);
            if($animeObj !== null) {
                $myWatchedEpisodes = $anime->my_watched_episodes;
                $viewsRepo->setEpisodesAsSeenBulk($myWatchedEpisodes, $this->user, $animeObj);
            }
        }
        return true;
    }

    public function checkIfUserExists(Users $user)
    {
        $this->user = $user;
        $url = "api/account/verify_credentials.json";
        $return = $this->callCurl($url);
        return true;
    }

}