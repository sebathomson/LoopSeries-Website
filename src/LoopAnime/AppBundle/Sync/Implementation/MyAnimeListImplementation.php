<?php

namespace LoopAnime\AppBundle\Sync\Implementation;

use LoopAnime\AppBundle\Sync\Implementation\Exception\ApiFaultException;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodes;
use LoopAnime\ShowsBundle\Entity\AnimesRepository;

class MyAnimeListImplementation extends BaseImplementation {

    const SYNC_URL = "http://myanimelist.net/";

    protected function callCurl($url, array $POST = null)
    {
        $ch = curl_init(self::SYNC_URL . $url);
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
            throw new ApiFaultException('MyAnimeList',$result);
        return $result;
    }

    public function markAsSeenEpisode(AnimesEpisodes $episode)
    {
        $season = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\AnimesSeasons')->find($episode->getIdSeason());
        $animeAPI = $this->em->getRepository('LoopAnime\ShowsAPIBundle\Entity\AnimesAPI')->findOneBy(['idAnime' => $season->getIdAnime()]);
        $anime = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\Animes')->findOneBy(['id' => $season->getIdAnime()]);

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
        $this->callCurl($this->getMarkEpisodeSeenApiUrl(),$POST);
        return true;
    }

    public function importSeenEpisodes()
    {
        /** @var AnimesRepository $animesRepo */
        $animesRepo = $this->em->getRepository('LoopAnime\ShowsBundle\Entity\Animes');
        $return = simplexml_load_string($this->callCurl($this->getImportApiUrl()));

        foreach($return->anime as $anime) {
            /** @var Animes $animeObj */
            $animeObj = $animesRepo->findOneBy(['title' => $anime->series_title]);
            if($animeObj !== null) {
                $myWatchedEpisodes = $anime->my_watched_episodes;
                $this->em->getRepository('LoopAnime\ShowsBundle\Entity\Views')->setEpisodesAsSeenBulk($myWatchedEpisodes, $this->user, $animeObj);
            }
        }
        return true;
    }

    protected function getMarkEpisodeSeenApiUrl()
    {
        return "show/episode/seen/" . $this->apiKey;
    }

    protected function getUserApiUrl()
    {
        return "api/account/verify_credentials.json";
    }

    protected function getImportApiUrl()
    {
        return "malappinfo.php?u=".$this->user->getMALUsername()."&status=all&type=anime";
    }
}